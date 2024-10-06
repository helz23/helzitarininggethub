<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\File\FileController;
use App\Http\Controllers\File\FileOperationsController;
use App\Http\Controllers\File\ReadFileController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Group\UserGroupController;
use App\Http\Controllers\Group\GroupFileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'requestresponseLog'], function () {
    Route::group([
        'controller' => RegisterController::class
    ], function () {
        Route::post('register', 'register');
        Route::post('user_login', 'userLogin');
    });

    Route::group([
        'middleware' => 'jwt.auth',
        'prefix' => 'user'
    ], function () {

        /*
         * HELEZ
         */

        //GroupFileController
        Route::post('addFileToGroupUser',
            [GroupFileController::class,
                'addFileToGroupUser'])->middleware('CheckFileOwner');

        Route::post('deleteFileFromGroupUser',
            [GroupFileController::class,

                'deleteFileFromGroupUser']);

//GroupController

        Route::post('deleteFromUserGroup',
            [GroupController::class,
                'deleteFromUserGroup'])->middleware('CheckGroupOwner');

        Route::get('displayUserGroups',
            [GroupController::class,

                'displayUserGroups']);

        Route::post('addGroupUser',
            [GroupController::class,

                'addGroupUser']);

        Route::post('displayUsersInMyGroup',
            [GroupController::class,
                'displayUsersInMyGroup'])->middleware('CheckGroupOwner');

//userGroupController

        Route::post('ownerAddsUsersToGroup',
            [UserGroupController::class,

                'ownerAddsUsersToGroup'])->middleware('CheckGroupOwner');

        Route::post('ownerDeletedUsersFromGroup',
            [UserGroupController::class,

                'ownerDeletedUsersFromGroup'])->middleware('CheckGroupOwner');


        /*
         * HIBA
         */
        Route::group([
            'controller' => FileController::class
        ], function () {
            Route::post('add_file', 'uploadFileUser');
            Route::delete('delete_file/{id}', 'deleteFile');
        });
        Route::get('my_files', [ReadFileController::class, 'userFiles']);
        Route::get('read_file/{id}', [ReadFileController::class, 'readFile']);
        Route::get('group_files/{id}', [ReadFileController::class, 'groupFiles']);

        Route::group([
            'controller' => FileOperationsController::class
        ], function () {
            Route::post('check_in', 'checkInUser');
            Route::post('bulk_check_in', 'bulkCheckIn');
            Route::post('check_out/{id}', 'checkOutUser');
            Route::post('edit_file', 'editFileUser');
        });

        Route::get('file_report/{id}', [ReportController::class, 'fileReports']);
        Route::get('user_report', [ReportController::class, 'userReports']);

    });

    Route::post('displayUsers', [UserGroupController::class, 'displayUsers']);
});

