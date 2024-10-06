<?php

namespace App\Http\Controllers\Group;


use App\Http\Controllers\Controller;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ownerAddsUsersToGroupRequest;
use App\Http\Requests\OwnerDeletedUsersFromGroupRequest;
use Illuminate\Support\Facades\Validator;


class UserGroupController extends Controller
{

    public function ownerAddsUsersToGroup(ownerAddsUsersToGroupRequest $request)
    {
        $user = Auth::user();

        UserGroup::create([
            'user_id' => $request->user_id,
            'group_id' => $request->group_id,
        ]);
        return response()->json([
            'status' => true,
            'data' => ['add user to group'],
        ], 201);

    }

    public function ownerDeletedUsersFromGroup(OwnerDeletedUsersFromGroupRequest $request)
    {

        $latestRecords = DB::table('reports')
            ->join('files', 'reports.file_id', '=', 'files.id')
            ->join('file_groups', 'file_groups.file_id', '=', 'files.id')
            ->where('reports.user_id', $request->user_id)
            ->where('file_groups.group_id', $request->group_id)
            ->whereIn('reports.operation_type', [1, 2])
            ->select('files.id', DB::raw('MAX(reports.id) as report_id'), DB::raw('MAX(reports.created_at) as latest_created_at'))
            ->groupBy('files.id')
            ->latest('reports.created_at')
            ->get();
        $counter = 0;

        foreach ($latestRecords as $record) {
            $fileId = $record->id;
            $reportId = $record->report_id;
            $hasOperationType1 = DB::table('reports')
                ->where('id', $reportId)
                ->where('operation_type', 1)
                ->exists();

            if ($hasOperationType1) {
                $counter++;
            }
        }

        if ($counter > 0) {
            return response()->json([
                'status' => false,
                'data' => ['there is file booked up '],
            ]);
        } else {
            $check3 = DB::table('user_groups')
                ->where('user_groups.group_id', $request->group_id)
                ->where('user_groups.user_id', $request->user_id)
                ->delete();

            return response()->json([
                'status' => true,
                'data' => ['delete user fro group'],
            ], 201);
        }
    }

    public function displayUsers(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'group_id' => 'required|integer|exists:groups,id',

        ]);
        if ($validator->fails())
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation'),
                'data' => $validator->errors()
            ], 400);

        $usersNotInGroup = DB::table('users')
            ->whereNotIn('users.id', function ($query) use ($request) {
                $query->select('user_id')
                    ->from('user_groups')
                    ->where('group_id', $request->group_id);
            })
            ->get(['users.id', 'users.name']);

        return response()->json([
            'status' => true,
            'data' => $usersNotInGroup
        ], 200);
    }
}

//    public function add_admin_user_to_group(Request $request)
//    {
//        $admin = Auth::user();
//        $validator = Validator::make($request->all(), [
//            'user_id' => 'required|integer|exists:users,id',
//            'group_id'=>'required|integer|exists:groups,id',
//        ]);
//        if ($validator->fails())
//            return response()->json([
//                'status' => false,
//                'data' => $validator->errors()
//            ], 400);
//
//        $check=Admin::join('groups', 'groups.admin_id', '=', 'admins.id')
//            ->where('admins.id',$admin->id)
//            ->where('groups.id',$request->group_id)
//            ->first();
//        if($check!=null){
//            $add_admin_user_to_group = UserGroup::create([
//                'user_id' => $request->user_id,
//                'group_id' => $request->group_id,
//            ]);
//            return response()->json([
//                'status' => true,
//                'data' => ['add admin user user to group'],
//            ], 201);
//        }
//        else{
//            return response()->json([
//                'status' => false,
//                'data' => ['there is error'], ]);
//        }
//
//    }

//    public function delete_user_from_groupAdmin(Request $request)
//    {
//        $admin = Auth::user();
//        $validator = Validator::make($request->all(), [
//            'user_id' => 'required|integer|exists:users,id',
//            'group_id'=>'required|integer|exists:groups,id',
//        ]);
//        if ($validator->fails())
//            return response()->json([
//                'status' => false,
//                'data' => $validator->errors()
//            ], 400);
//
//        $check1=User::join('groups', 'groups.user_id', '=', 'users.id')
//            ->where('users.id',$admin->id)
//            ->where('groups.id',$request->group_id)
//            ->first();
//
//        $check2=User::join('files','files.user_id','=','users.id')
//            ->join('file_groups','file_groups.file_id','=','files.id')
//            ->where('users.id',$request->user_id)
//            ->where('files.status',0)
//            ->first();
//
//
//        if($check1!=null ) {
//            if ($check2 != null) {
//                return response()->json([
//                    'status' => false,
//                    'data' => ['there is file booked up '],]);
//            } else if($check2 == null){
//
//                $check3 =DB::table('user_groups')
//                    ->where('user_groups.group_id',$request->group_id)
//                    ->where('user_groups.user_id',$request->user_id)
//                    ->delete();
//
//                return response()->json([
//                    'status' => true,
//                    'data' => ['delete user fro group'],
//                ], 201);
//
//
//
//            }
//        }
//        else {
//
//            return response()->json([
//                'status' => false,
//                'data' => ['there is erroe'],
//            ], 201);
//
//        }
//
//    }
