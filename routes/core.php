<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Core API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resources([
    'permissions' => 'olckerstech\core\src\Controllers\PermissionsController',
    'users' => 'olckerstech\core\src\Controllers\UserController',
    'tenants' => 'olckerstech\core\src\Controllers\TenantController',
    'trashed_permissions' => 'olckerstech\core\src\Controllers\PermissionsTrashedController',
    'trashed_users' => 'olckerstech\core\src\Controllers\UserTrashedController',
    'trashed_tenants' => 'olckerstech\core\src\Controllers\TenantTrashedController',
]);
