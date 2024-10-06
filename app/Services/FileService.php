<?php /** @noinspection PhpPossiblePolymorphicInvocationInspection */
/** @noinspection PhpUndefinedFieldInspection */
/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Services;

use App\Models\Admin;
use App\Models\File;
use App\Models\FileGroup;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FileService
{
    private ReportService $report;

    public function __construct()
    {
        $this->report = new ReportService();
    }

    public function uploadFile($request)
    {
        $file = File::query()->create([
            'user_id' => Auth::id(),
            'status' => 1,
            'version' => 1
        ]);
        $this->saveFile($request, $file);
        $this->report->saveAddFileReportUser($file);
        return $file;
    }

    public function deleteFile($id)
    {
        $file = File::query()->where('status', 1)->find($id);
        if (!$file)
            return false;
        $file->clearMediaCollection();
        $file->delete();
        return true;
    }

    public function getFile($id)
    {
        $file = File::query()->with('media')->findOrFail($id);
        if (!($file->status))
            $file['blocking_user'] = $this->getBlockingUser($file->id);
        else
            $file['blocking_user'] = '';
        return $file;
    }

    public function getUserFiles()
    {
        return File::where('user_id', Auth::id())->with('media')->get();

    }

    public function getGroupFiles($id)
    {
        $filesIds = FileGroup::query()->where('group_id', $id)
            ->get(['file_id']);
        $files = File::query()->whereIn('id', $filesIds)->with('media')
            ->get();
        foreach ($files as $file) {
            if (!($file->status)) {
                $file['blocking_user_id'] = $this->getBlockingUser($file->id)->id;
                $file['blocking_user_name'] = $this->getBlockingUser($file->id)->name;
            } else {
                $file['blocking_user_id'] = null;
                $file['blocking_user_name'] = null;
            }
        }
        return $files;
    }

    public function checkIn($file_id, $version): int
    {
        return File::query()->where('id', $file_id)
            ->where('status', 1)
            ->where('version', $version)
            ->update(['status' => 0]);
    }

    public function checkOut($id)
    {
        $file = File::query()->find($id);
        $version = $file->increment('version');
        $status = $file->update(['status' => 1]);
        $this->report->saveCheckOutReportUser($id);
        if (!$version || !$status)
            return false;
        else
            return true;

    }

    public function editFile($request)
    {
        $file = File::query()->find($request->file_id);
        $this->saveFileEdit($request, $file);
        $this->report->saveEditFileReportUser($file->id);
        return $file;
    }

    public function bulkCheckIn($files): bool
    {
        foreach ($files as $file) {
            $result = File::query()->where('id', $file['file_id'])
                ->where('status', 1)
                ->where('version', $file['version'])
                ->update(['status' => 0]);
            $this->report->saveCheckInReportUser($file['file_id']);
            if (!$result)
                return false;
        }
        return true;
    }

    public function saveFile($request, $file)
    {
        $file->addMedia($request->file)->toMediaCollection();
        $file->save();
    }

    public function saveFileEdit($request, $file)
    {
        $file->clearMediaCollection();
        $file->addMedia($request->file)->toMediaCollection();
        $file->save();
    }

    public function getBlockingUser($id)
    {
        $report = Report::query()->where('file_id', $id)
            ->where('operation_type', 1)
            ->orderByDesc('id')
            ->first();
        if (isset($report->user_id))
            return User::query()->find($report->user_id);
        else
            return Admin::query()->find($report->admin_id);
    }

}
