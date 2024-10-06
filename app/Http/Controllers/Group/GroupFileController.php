<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddFileToGroupUserRequest;
use App\Http\Requests\DeleteFileFromGroupUserRequest;
use App\Models\Admin;
use App\Models\FileGroup;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class GroupFileController extends Controller
{
    public function addFileToGroupUser(AddFileToGroupUserRequest $request)
    {
        FileGroup::create([
            'file_id' => $request->file_id,
            'group_id' => $request->group_id,
        ]);
        return response()->json([
            'status' => true,
            'data' => ['add file for group'],
        ], 201);
    }

    public function deleteFileFromGroupUser(DeleteFileFromGroupUserRequest $request)
    {
        $check1 = $this->check1($request->file_id);
        if ($check1 != null) {
            $check2 = $this->check2($request->file_id);
            if ($check2) {
                return response()->json([
                    'status' => false,
                    'data' => [' you can not delete the file because there is a reserved file in it'],
                ], 400);
            } else {
                $there_is = DB::table('file_groups')
                    ->where('file_groups.file_id', $request->file_id)
                    ->where('file_groups.group_id', $request->group_id)
                    ->exists();
                if ($there_is) {
                    $there_is = DB::table('file_groups')
                        ->where('file_groups.file_id', $request->file_id)
                        ->where('file_groups.group_id', $request->group_id)
                        ->delete();
                    return response()->json([
                        'status' => true,
                        'data' => ['delete file fro group'],
                    ], 201);
                } else {
                    return response()->json([
                        'status' => true,
                        'data' => ['there is no record'],
                    ], 201);
                }

            }
        } else {
            return response()->json([
                'status' => false,
                'data' => ['you are not the owner of the file'],], 400);
        }


    }

    public function check1($file_id)
    {
        $check1 = DB::table('files')
            ->where('files.user_id', Auth::id())
            ->where('files.id', $file_id)
            ->exists();
        return $check1;
    }

    public function check2($file_id)
    {
        $check2 = DB::table('files')
            ->where('files.user_id', Auth::id())
            ->where('files.id', $file_id)
            ->where('files.status', 0)
            ->exists();
        return $check2;
    }


}

//    public function delete_file_to_group_admin(Request $request)
//    {
//        $user = Auth::user();
//        $validator = Validator::make($request->all(), [
//            'file_id' => 'required|integer|exists:files,id',
//            'group_id' => 'required|integer|exists:groups,id',
//        ]);
//        if ($validator->fails())
//            return response()->json([
//                'status' => false,
//                'data' => $validator->errors()
//            ], 400);
//
//        $check=Admin::join('files', 'files.admin_id', '=', 'admins.id')
//            ->where('files.id',$request-> file_id)
//            ->where('admins.id',$user->id)
//            ->first();
//        if($check!=null){
//            $delete_file_for_group = Admin::join('files',
//                'files.admin_id', '=', 'admins.id')
//                ->where('files.id',$request-> file_id)
//                ->where('admins.id',$user->id)->delete();
//            return response()->json([
//                'status' => true,
//                'data' => ['delete file fro group'],
//            ], 201);
//        }
//        else{
//            return response()->json([
//                'status' => false,
//                'data' => ['there is error'], ]);
//        }
//
//
//
//    }

//    public function add_file_to_group_admin(Request $request)
//    {
//        $user = Auth::user();
//        $validator = Validator::make($request->all(), [
//            'file_id' => 'required|integer|exists:files,id',
//            'group_id' => 'required|integer|exists:groups,id',
//        ]);
//        if ($validator->fails())
//            return response()->json([
//                'status' => false,
//                'data' => $validator->errors()
//            ], 400);
//
//        $check=Admin::join('files', 'files.admin_id', '=', 'admins.id')
//            ->where('files.id',$request-> file_id)
//            ->where('admins.id',$user->id)
//            ->first();
//        if($check!=null){
//            $add_file_for_group = FileGroup::create([
//                'file_id' => $request->file_id,
//                'group_id' => $request->group_id,
//            ]);
//            return response()->json([
//                'status' => true,
//                'data' => ['add file for group'],
//            ], 201);
//        }
//        else{
//            return response()->json([
//                'status' => false,
//                'data' => ['there is error'], ]);
//        }
//
//
//
//    }
