<?php

namespace App\Http\Controllers\Group;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddGroupUserRequest;
use App\Http\Requests\DeleteFromUserGroupRequest;
use App\Models\Group;
use App\Models\UserGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{

    public function addGroupUser(AddGroupUserRequest $request)
    {
        $group = Group::create([
            'name' => $request->name,
            'user_id' => Auth::id(),
        ]);
        UserGroup::create([
            'group_id' => $group->id,
            'user_id' => Auth::id(),
        ]);

        return response()->json([
            'status' => true,
            'data' => ['A group has been added '],
        ], 201);
    }

    public function deleteFromUserGroup(DeleteFromUserGroupRequest $request)
    {
        $check2 = $this->check2($request->group_id);
        if ($check2) {
            return response()->json([
                'status' => false,
                'data' => [' you can not delete the group
                    because there is a reserved file in it'],
            ], 400);
        } else {
            $delete_group = DB::table('groups')
                ->where('groups.id', $request->group_id)
                ->delete();
            return response()->json([
                'status' => true,
                'data' => ['deleted the group'],
            ], 201);
        }
    }

    public function displayUserGroups()
    {

        $displayUserGroups = UserGroup::
        join('groups', 'user_groups.group_id', '=', 'groups.id')
            ->join('users', 'user_groups.user_id', '=', 'users.id')
            ->where('user_groups.user_id', Auth::id())
            ->get(['groups.name as group_name',
                'groups.id as id']);

        return response()->json([
            'status' => true,
            'data' => $displayUserGroups,
        ], 201);
    }

    public function displayUsersInMyGroup(Request $request)
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

        $displayUsersInMyGroup = DB::table('users')
            ->join('user_groups', 'users.id', '=', 'user_groups.user_id')
            ->whereNot('user_groups.user_id', Auth::id())
            ->where('user_groups.group_id', $request->group_id)
            ->get(['users.id',
                'users.name',]);
        return response()->json([
            'status' => true,
            'data' => $displayUsersInMyGroup
        ], 200);

    }

    public function check2($id)
    {
        $check2 = DB::table('files')
            ->join('file_groups', 'file_groups.file_id', '=', 'files.id')
            ->where('file_groups.group_id', $id)
            ->where('files.status', 0)
            ->exists();
        return $check2;
    }


}

//    public function delete_groupAdmin(Request $request)
//    {
//        $admin = Auth::user();
//        $validator = Validator::make($request->all(), [
//
//            'name' => 'required|string',
//            'id' => 'required|integer|exists:groups,id',
//            'admin_id'=>'integer|exists:admins,id',
//        ]);
//        if ($validator->fails())
//            return response()->json([
//                'status' => false,
//                'data' => $validator->errors()
//            ], 400);
//
//        $check1=User::join('groups', 'groups.user_id', '=', 'users.id')
//            ->where('groups.user_id',$admin->id)
//            ->where('groups.id',$request->id)
//            ->first();
//
//
//        $check2=File::
//        join('file_groups','file_groups.file_id','=','files.id')
//            ->join('groups','groups.id','=','file_groups.group_id')
//            ->where('groups.id',$request->id)
//            ->where('files.status',0)
//            ->first();
//        if($check1!=null ){
//            if($check2!=null ){
//                return response()->json([
//                    'status' => false,
//                    'data' => [' you can not delete group'],
//                ], 201);
//            }
//            else{
//                $delete_group=DB::table('groups')
//                    ->where('groups.user_id',$admin->id)
//                    ->where('groups.id',$request->id)
//                    ->delete();
//
//                return response()->json([
//                    'status' => true,
//                    'data' => [' delete group'],
//                ], 201);
//            }
//        }
//        else{
//            return response()->json([
//                'status' => false,
//                'data' => [' you are not owner group'],
//            ], 201);
//        }
//
//
//
//
//    }

//    public function add_admin_group(Request $request)
//    {
//        $admin = Auth::user();
//        $validator = Validator::make($request->all(), [
//
//            'name' => 'required|string',
//            'admin_id' => 'required|integer|exists:admins,id',
//
//
//        ]);
//        if ($validator->fails())
//            return response()->json([
//                'status' => false,
//                'data' => $validator->errors()
//            ], 400);
//
//        $add_admin_group = Group::create([
//            'name' => $request->name,
//            'admin_id' => $admin->id,
//        ]);
//        return response()->json([
//            'status' => true,
//            'data' => ['add name group'],
//        ], 201);
//    }
