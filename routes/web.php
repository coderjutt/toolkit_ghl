<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\CompanysettingController;
use App\Http\Controllers\Admin\ContactbuttonController;
use App\Http\Controllers\Admin\CustommenuController;
use App\Http\Controllers\Admin\CustommenulinkController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DatauseroptionController;
use App\Http\Controllers\Admin\FolderController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\LocationcustomizerController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RenamemenuController;
use App\Http\Controllers\Admin\ScriptpermController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SubmenuController;
use App\Http\Controllers\Admin\TranslationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UseroptionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AutoAuthController;
use App\Http\Controllers\Admin\ContactsController;
use App\Http\Controllers\CRMController;
use App\Http\Controllers\Admin\CustomValueController;
use App\Http\Controllers\SubAccountController;
use App\Models\LocationCustomizer;
use App\Models\UserPermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
  // dd('hay');
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->role == 1 || $user->role == 2) {
            return redirect('/admin/dashboard');
        }
    }
    return view('auth.login');
});
// Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
// Route::middleware('guest')->group(function () {
//     
//     Route::post('/login', [LoginController::class, 'login']);
// //     Route::get('/', function () {
// //     return view('auth.login');
// // });
// });
// Utility Routes
Route::get('flowbite', fn() => view('welcome'));
Route::get('/cache', function () {
    \Artisan::call('config:clear');
    \Artisan::call('optimize:clear');
    \Artisan::call('route:clear');
    \Artisan::call('view:clear');
    \Artisan::call('cache:clear');
    return '<h3>Caches have been cleared successfully!</h3>';
});

// Auth-protected route for both roles
Route::group(['as' => 'admin.', 'prefix' => 'admin', 'middleware' => ['auth']], function () {

    Route::middleware('role_check:1,2')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');

        Route::prefix('user')->name('user.')->group(function () {
            Route::get('index', [UserController::class, 'index'])->name('index');
            Route::post('store/{id?}', [UserController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
            Route::get('/profile', [UserController::class, 'profile'])->name('profile');
            Route::post('/password-save', [UserController::class, 'changePassword'])->name('password.save');
            Route::post('/email-change', [UserController::class, 'changeEmail'])->name('email.save');
            Route::get('/status/{id?}', [UserController::class, 'status'])->name('status');
            Route::delete('delete/{id}', [UserController::class, 'destroy'])->name('destroy');
            Route::post('/profile-save', [UserController::class, 'general'])->name('profile.save');
            Route::post('/image', [UserController::class, 'imageUpload'])->name('imageUpload');
        });

        Route::prefix('setting')->name('setting.')->group(function () {
            Route::get('/index', [SettingController::class, 'index'])->name('index');
            Route::post('/save', [SettingController::class, 'save'])->name('save');
            Route::post('settings/logo', [SettingController::class, 'saveLogo'])->name('saveLogo');
        });
    });

    Route::middleware(['auth', 'super_admin'])->group(function () {
        Route::prefix('sub-account')->name('subaccount.')->group(function () {
            Route::get('/index', [SubAccountController::class, 'index'])->name('index');
            Route::post('/policies', [SubAccountController::class, 'policies'])->name('policies');
            Route::get('/user/search', [SubAccountController::class, 'searchUserByAjax'])->name('user.search');
        });

        Route::prefix('log')->name('logs.')->group(function () {
            Route::get('/index', [SettingController::class, 'log'])->name('index');
        });

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions/update', [PermissionController::class, 'update'])->name('permissions.update');
        Route::get('/permissions/get/{user?}', [PermissionController::class, 'fetch'])->name('permissions.get');
        Route::post('permission/modules', [PermissionController::class, 'storeModules'])->name('modules.store');
    });
    Route::middleware(['auth', 'admin'])->group(function () {
        // Announcement
        Route::get('/announcement/index', [AnnouncementController::class, 'index'])->name('announcement.index');
        Route::get('/announcement/create', [AnnouncementController::class, 'create'])->name('announcement.create');
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::delete('delete/{id}', [AnnouncementController::class, 'destroy'])->name('destroy.announcement');
        Route::put('announcements/{id}', [AnnouncementController::class, 'update'])->name('announcement.update');

        Route::post('announcements/save-settings', [AnnouncementController::class, 'save_settings'])->name('announcement.save_settings');
        Route::post('announcements/save-settingswithrow', [AnnouncementController::class, 'save_settingswithrow'])->name('announcement.save_settingswithrow');
        Route::put('', [AnnouncementController::class, 'emailsettingupdate'])->name('announcement_email.update');

        // Route::get('/ghl/sync-uses2/{locationId}', [AnnouncementController::class, 'syncUsers']);
        // Route::get('/ghl/get-uses2/{locationId}', [AnnouncementController::class, 'getUsers']);

        // custom_values addons
        Route::get('/custom-values', [CustomValueController::class, 'index'])->name('customvalue.index');
        Route::get('/custom-values/create', [CustomValueController::class, 'create'])->name('customvalue.create'); // :point_left: NEW
        Route::post('/custom-values', [CustomValueController::class, 'store'])->name('customvalue.store');
        Route::get('/custom-values/{id}/edit', [CustomValueController::class, 'edit'])->name('customvalue.edit'); // :point_left: NEW
        Route::put('/custom-values/{id}', [CustomValueController::class, 'update'])->name('customvalue.update');
        Route::delete('/custom-values/{id}', [CustomValueController::class, 'destroy'])->name('customvalue.destroy');
        // contact addons
        Route::get('/contacts', [ContactsController::class, 'index'])->name('contacts.index');
        Route::get('/contacts/create', [ContactsController::class, 'create'])->name('contacts.create');
        Route::post('contacts', [ContactsController::class, 'store'])->name('contacts.store');
        Route::get('/contacts/{id}/edit', [ContactsController::class, 'edit'])->name('contacts.edit');
        Route::put('/contacts/{id}', [ContactsController::class, 'update'])->name('contacts.update');
        Route::delete('/contacts/{id}', [ContactsController::class, 'destroy'])->name('contacts.destroy');

        // LocationCustomizer
        Route::post('/store', [LocationCustomizerController::class, 'store'])->name('location_customizer.store');
        Route::get('/locationcustomizer/index', [LocationcustomizerController::class, 'index'])->name('locationcustomizer.index');
        Route::post('/location_customizer/toggle', [LocationCustomizerController::class, 'toggleEnable'])
            ->name('location_customizer.toggle');
        // customcss 
        // Fetch CSS for modal
        Route::get('custom_css/{locationId}', [LocationcustomizerController::class, 'editCSS'])->name('custom_css.edit');
        // Update CSS
        Route::post('custom_css/{locationId}', [LocationcustomizerController::class, 'updateCSS'])->name('custom_css.update');

        Route::get('/translation/index', [TranslationController::class, 'index'])->name('translation.index');
        Route::get('/location/index', [LocationController::class, 'index'])->name('location.index');
        Route::get('/user/index', [UserController::class, 'index'])->name('user.index');
        Route::get('/folder/index', [FolderController::class, 'index'])->name('folder.index');
        Route::get('/datauseroption/index', [DatauseroptionController::class, 'index'])->name('datauseroption.index');
        Route::get('/submenu/index', [SubmenuController::class, 'index'])->name('submenu.index');
        Route::get('/useroption/index', [UseroptionController::class, 'index'])->name('useroption.index');
        Route::get('/contactbutton/index', [ContactbuttonController::class, 'index'])->name('contactbutton.index');
        Route::get('/custommenu/index', [CustommenuController::class, 'index'])->name('custommenu.index');
        Route::get('/custommenulink/index', [CustommenulinkController::class, 'index'])->name('custommenulink.index');
        Route::get('/companysetting/index', [CompanysettingController::class, 'index'])->name('companysetting.index');
        Route::get('/settings/index', [SettingsController::class, 'index'])->name('settings.index');
        Route::get('/scriptperm/index', [ScriptpermController::class, 'index'])->name('scriptperm.index');
        Route::get('/permission/index', [PermissionController::class, 'index'])->name('permission.index');
        Route::get('/menu/index', [MenuController::class, 'index'])->name('menu.index');
        //Rename Menu Routes
        Route::get('/renamemenu/index', [RenamemenuController::class, 'index'])->name('renamemenu.index');
        Route::post('/renamemenu/index', [RenamemenuController::class, 'store'])->name('renamemenu.store');

    });
});

// These routes need auth before super_admin
Route::middleware(['auth', 'super_admin'])->group(function () {
    Route::get('/loginwith/{id}', function ($id) {
        $user = \App\Models\User::findOrFail($id);
        $allowedModules = UserPermission::where('user_id', $user->id)
            ->pluck('module')
            ->unique()
            ->toArray();

        session()->forget('user_modules_' . $user->id);
        session()->put('user_modules_' . $user->id, $allowedModules);
        if ($user) {
            $currentUser = Auth::user();

            if (in_array($currentUser->role, [1, 2])) {
                if ($user->role == 2) {
                    session()->put('super_admin', $currentUser);
                } else {
                    session()->put('company_admin', $currentUser);
                }

                Auth::loginUsingId($user->id);
            }
        }
        return redirect()->intended('admin/dashboard');
    })->name('admin.user.autoLogin');
});





Route::get('/backtoadmin', function () {
    if (request()->has('admin') && session()->has('super_admin')) {
        Auth::login(session('super_admin'));
        session()->forget(['super_admin', 'company_admin']);
    } elseif (request()->has('company') && session()->has('company_admin')) {
        Auth::login(session('company_admin'));
        session()->forget('company_admin');
    } else {
        return redirect()->route('login')->withErrors('No admin session available.');
    }
    return redirect()->intended('/admin/dashboard');
})->name('backtoadmin');
// CRM & OAuth Routes
Route::prefix('authorization')->name('crm.')->group(function () {
    Route::middleware('auth')->group(function () {
        Route::get('/crm/fetch_detail', [CRMController::class, 'crmFetchDetail'])->name('fetchDetail');
        Route::get('/crm/fetchLocations', [CRMController::class, 'fetchLocations'])->name('fetchLocations');
        Route::get('/crm/fetchUser', [CRMController::class, 'fetchUsers'])->name('fetchUser');
        Route::get('/crm/syn/location/data', [CRMController::class, 'synLocationData'])->name('syn.location.data');
    });
    Route::get('/crm/oauth/callback', [CRMController::class, 'crmCallback'])->name('oauth_callback');
});

// Auth handling
Route::get('check/auth', [AutoAuthController::class, 'connect'])->name('auth.check');
Route::get('check/auth/error', [AutoAuthController::class, 'authError'])->name('error');
Route::get('checking/auth', [AutoAuthController::class, 'authChecking'])->name('admin.auth.checking');

route::get('contact/get', [ContactbuttonController::class, 'getContact']);
route::get('calender/get', [ContactbuttonController::class, 'getcalender']);
route::get('calender/get/{location_id}/{company_id}/{calendarId}', [ContactbuttonController::class, 'getcalender']);

Auth::routes();
