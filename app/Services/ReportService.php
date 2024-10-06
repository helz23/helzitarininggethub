<?php /** @noinspection PhpPossiblePolymorphicInvocationInspection */

/** @noinspection PhpMissingReturnTypeInspection */

namespace App\Services;

use App\Models\Admin;
use App\Models\File;
use App\Models\Report;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ReportService
{
    public function getFileReports($id)
    {
        $reports = Report::query()->where('file_id', $id)
            ->orderByDesc('id')
            ->get();
        $totalReport = collect([]);
        foreach ($reports as $reportItem) {
            $report = $this->getItemReport($reportItem);
            $report = $this->getOperationUser($reportItem, $report);
            $totalReport->push($report);
        }
        return $totalReport;
    }

    public function getUserReports()
    {
        $reports = Report::query()->where('user_id', Auth::id())
            ->orderByDesc('id')
            ->get();
        $totalReport = collect([]);
        foreach ($reports as $reportItem) {
            $report = $this->getItemReport($reportItem);
            $report = $this->getOperationFile($reportItem, $report);
            $totalReport->push($report);
        }
        return $totalReport;
    }

    private function operationType($type): string
    {
        return match ($type) {
            0 => 'upload',
            1 => 'check-in',
            2 => 'check-out',
            default => 'edit',
        };
    }

    private function getItemReport($item): Collection
    {
        $report = collect();
        $report->put('id', $item->id);
        $report->put('operation', $this->operationType($item->operation_type));
        $report->put('date', $item->created_at);
        return $report;
    }

    private function getOperationUser($item, $report)
    {
        if (isset($item->user_id))
            $report->put('user', User::query()->find($item->user_id)->name);
        else
            $report->put('user', Admin::query()->find($item->user_id)->name);
        return $report;
    }

    private function getOperationFile($item, $report)
    {
        $file = File::query()->with('media')
            ->find($item->file_id);
//        $file=File::query()
//            ->with(['media' => function ($query) {
//                $query->select('file_name', 'original_url');
//            }])
////            ->withAggregate('media', 'name')
//            ->where('id', $item->file_id)
//            ->get()->first();
        $report->put('file', $file);
        /*
         * File::query()
            ->find($item->file_id)->withAggregate('media','0')
            ->get([
                'id as file_id',
                'status',
                'version'
            ])->first()

        [
                'id as file_id',
                'status',
                'version',
                'media'
            ]
         */
        return $report;
    }

    public function saveAddFileReportUser($file)
    {
        Report::query()->create([
            'operation_type' => 0,
            'user_id' => Auth::id(),
            'admin_id',
            'file_id' => $file->id
        ]);
    }

    function saveCheckInReportUser($id)
    {
        Report::query()->create([
            'operation_type' => 1,
            'user_id' => Auth::id(),
            'admin_id',
            'file_id' => $id
        ]);
    }

    function saveCheckOutReportUser($id)
    {
        Report::query()->create([
            'operation_type' => 2,
            'user_id' => Auth::id(),
            'admin_id',
            'file_id' => $id
        ]);
    }

    function saveEditFileReportUser($id)
    {
        Report::query()->create([
            'operation_type' => 4,
            'user_id' => Auth::id(),
            'admin_id',
            'file_id' => $id
        ]);
    }

}
