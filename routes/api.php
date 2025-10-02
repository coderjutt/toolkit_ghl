<?php

use App\Http\Controllers\Admin\ContactbuttonController;
use App\Http\Controllers\Admin\CustommenulinkController;
use App\Http\Controllers\Admin\LocationcustomizerController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\api\AnnouncementController;
use App\Http\Controllers\admin\ContactsController;
use App\Http\Controllers\Admin\CustomValueController;
use App\Models\CustomCss;
use App\Models\LocationCustomizer;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\RenamemenuController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
// Route::middleware('auth:sanctum')->get('/announcements', [AnnouncementController::class, 'getAnnouncements']);
Route::post('/webhook', [WebhookController::class, 'handle_webhook']);
Route::get('/renamed-menus', [RenamemenuController::class, 'renameGetApi']);
Route::get('scripted_permissions',[PermissionController::class,'fetchapi']);

Route::get('/announcements', [AnnouncementController::class, 'getAnnouncements']);
// Route::get('/announcements', [AnnouncementController::class, 'getAnnouncements']);
Route::post('/announcements/viewed', [AnnouncementController::class, 'markAsViewed']);
Route::post('/announcements/globel', [AnnouncementController::class, 'storeGlobalViewAnnouncements']);
Route::get('location-customizer', [LocationcustomizerController::class, 'getApiLocationCustomizer']);
// CustomCss
Route::post('custom_css/{email}/{locationId}/{ghl_location_id}', [LocationcustomizerController::class, 'updateCSSapi'])->name('custom_css.update');
Route::get('custom_css/{email}/{locationId}/{ghl_location_id}',[LocationcustomizerController::class, 'getCSS'])->name('custom_css.get');

Route::get('custommenulink',[CustommenulinkController::class,'getapi_Custommenulink']);

Route::get('/custom-values', [CustomValueController::class, 'apiIndex']); // no middleware
Route::get('/contacts', [ContactbuttonController::class, 'apiIndex']); // no middleware