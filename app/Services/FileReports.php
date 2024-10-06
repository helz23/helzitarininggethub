<?php

namespace App\Services;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class FileReports
{
    public function saveAddFileReportUser($file)
    {
        Report::query()->create([
            'operation_type' => 0,
            'user_id' => Auth::id(),
            'admin_id',
            'file_id' => $file->id
        ]);
    }

    public function saveAddFileReportAdmin($file)
    {
        Report::query()->create([
            'operation_type' => 0,
            'admin_id' => Auth::id(),
            'file_id' => $file->id
        ]);
    }

    /*
    public function saveDeleteFileReport($file)
    {
        Report::query()->create([
            'operation_type' => 3,
            'user_id' => Auth::id(),
            'admin_id',
            'file_id' => $file->id
        ]);
    }
    */
}
