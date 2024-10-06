<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkCheckInRequest;
use App\Http\Requests\CheckInRequest;
use App\Http\Requests\EditFileRequest;
use App\Services\FileService;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FileOperationsController extends Controller
{
    private ReportService $report;
    private FileService $fileService;


    public function __construct()
    {
        $this->report = new ReportService();
        $this->fileService = new FileService();

    }

    public function checkInUser(CheckInRequest $request): JsonResponse
    {
        $file = $this->fileService->checkIn($request->file_id, $request->version);
        if (!$file)
            return response()->json([
                'status' => false,
                'message' => 'Can\'t check-in the file'
            ], 400);
        $this->report->saveCheckInReportUser($request->file_id);
        return response()->json([
            'status' => true,
            'message' => 'check-in succeeded'
        ]);
    }

    //transaction
    public function checkOutUser($id): JsonResponse
    {
        DB::beginTransaction();
        $result = $this->fileService->checkOut($id);
        if (!$result) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'check-out failed'
            ], 400);
        } else {
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'check-out succeeded'
            ]);
        }
    }

    public function editFileUser(EditFileRequest $request): JsonResponse
    {
        $file = $this->fileService->editFile($request);
        return response()->json([
            'status' => true,
            'data' => $file
        ]);
    }

    public function bulkCheckIn(BulkCheckInRequest $request): JsonResponse
    {
        DB::beginTransaction();
        $files = $request->files_id;
        $result = $this->fileService->bulkCheckIn($files);
        if (!$result) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Can\'t check-in the file'
            ], 400);
        } else
            DB::commit();
        return response()->json([
            'status' => true,
            'message' => 'check-in succeeded',
            'files' => $files
        ]);
    }

}
