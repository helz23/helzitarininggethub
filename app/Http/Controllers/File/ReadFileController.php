<?php /** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;

class ReadFileController extends Controller
{
    private FileService $oper;

    public function __construct()
    {
        $this->oper = new FileService();
    }


    public function readFile($id): JsonResponse
    {
        $file = $this->oper->getFile($id);
        return response()->json([
            'message' => 'success',
            'file' => $file
        ]);
    }

    public function userFiles(): JsonResponse
    {
        $files = $this->oper->getUserFiles();
        return response()->json([
            'message' => 'success',
            'files' => $files
        ]);
    }

    public function groupFiles($id): JsonResponse
    {
        $files = $this->oper->getGroupFiles($id);
        return response()->json([
            'message' => 'success',
            'files' => $files
        ]);
    }

}
