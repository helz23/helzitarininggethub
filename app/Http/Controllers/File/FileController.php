<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadFileRequest;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;

class FileController extends Controller
{
    private FileService $oper;

    public function __construct()
    {
        $this->oper = new FileService();
    }

    public function uploadFileUser(UploadFileRequest $request): JsonResponse
    {
        $file = $this->oper->uploadFile($request);
        return response()->json([
            'status' => true,
            'data' => $file
        ]);
    }

    public function deleteFile($id): JsonResponse
    {
        $result = $this->oper->deleteFile($id);
        if (!$result)
            return response()->json([
                'status' => false,
                'message' => 'Can\'t delete file because it is in use'
            ], 400);
        return response()->json([
            'message' => 'File deleted successfully'
        ]);
    }
}
