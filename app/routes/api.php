<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('guest')->post('/auth', [\App\Http\Controllers\UserController::class, 'auth']);//
Route::middleware('guest')->post('/reg', [\App\Http\Controllers\UserController::class, 'register']);//

Route::middleware('guest')->post('/create_role', [\App\Http\Controllers\RoleController::class, 'createRole']);//
Route::middleware('guest')->post('/delete_role', [\App\Http\Controllers\RoleController::class, 'deleteRole']);//
Route::middleware('guest')->post('/edit_role', [\App\Http\Controllers\RoleController::class, 'updateRole']);//
Route::middleware('guest')->get('/roles', [\App\Http\Controllers\RoleController::class, 'showRoles']);//
Route::middleware('guest')->post('/role', [\App\Http\Controllers\RoleController::class, 'getRoleByName']);//
Route::middleware('guest')->post('/role_by_id', [\App\Http\Controllers\RoleController::class, 'getRoleById']);//

Route::middleware('guest')->get('/users', [\App\Http\Controllers\UserController::class, 'showUsers']);//
Route::middleware('guest')->get('/user_by_email', [\App\Http\Controllers\UserController::class, 'getUserByEmail']);//
Route::middleware('guest')->post('/edit_user', [\App\Http\Controllers\UserController::class, 'updateUser']);//
Route::middleware('guest')->post('/create_user', [\App\Http\Controllers\UserController::class, 'createUser']);//
Route::middleware('guest')->post('/delete_user', [\App\Http\Controllers\UserController::class, 'deleteUser']);//

Route::middleware('auth:sanctum')->post('/create_folder', [\App\Http\Controllers\PathController::class, 'createFolder']);//
Route::middleware('auth:sanctum')->post('/delete_folder', [\App\Http\Controllers\PathController::class, 'deleteFolder']);//
Route::middleware('auth:sanctum')->get('/get_files', [\App\Http\Controllers\PathController::class, 'getFiles']);//
Route::middleware('auth:sanctum')->post('/create_file', [\App\Http\Controllers\PathController::class, 'createFile']);//
Route::middleware('auth:sanctum')->post('/delete_file', [\App\Http\Controllers\PathController::class, 'deleteFile']);//
Route::middleware('auth:sanctum')->post('/check_file', [\App\Http\Controllers\PathController::class, 'checkFile']);//
Route::middleware('auth:sanctum')->post('/check_folder', [\App\Http\Controllers\PathController::class, 'checkFolder']);//
Route::middleware('auth:sanctum')->get('/save_file', [\App\Http\Controllers\PathController::class, 'saveFile']);//
Route::middleware('auth:sanctum')->get('/get_role_path', [\App\Http\Controllers\PathController::class, 'getRolePath']);//
Route::middleware('auth:sanctum')->post('/set_role_to_path', [\App\Http\Controllers\PathController::class, 'setRoleToPath']);//
Route::middleware('auth:sanctum')->get('/file_info', [\App\Http\Controllers\PathController::class, 'fileInfo']);//
Route::middleware('auth:sanctum')->post('/rename', [\App\Http\Controllers\PathController::class, 'rename']);//
Route::middleware('auth:sanctum')->post('/move', [\App\Http\Controllers\PathController::class, 'move']);//
Route::middleware('auth:sanctum')->get('/save_zip', [\App\Http\Controllers\PathController::class, 'saveZip']);//
Route::middleware('auth:sanctum')->get('/get_files_by_role', [\App\Http\Controllers\PathController::class, 'getFilesByRole']);//

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {//
    return auth()->user();
});
