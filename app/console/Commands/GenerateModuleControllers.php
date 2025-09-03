<?php

namespace App\Console\Commands;

use App\Models\Modules;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateModuleControllers extends Command
{
    protected $signature = 'generate:module-controllers';
    protected $description = 'Generate empty controllers for each module';

    public function handle()
    {
        // $modules = [
        //     "Ungrouped", "Announcement", "Translation", "Location", "User", "Folder", "Datauseroption", "Submenu",
        //     "Locationcustomizer", "Useroption", "Contactbutton", "Custommenu", "Custommenulink", "Companysetting",
        //     "Settings", "Scriptperm", "Permission", "Menu", "Renamemenu" ,"CustomValue"
        // ];
       $modules=Modules::all();
        foreach ($modules as $module) {
            $controllerName = $module . 'Controller';
            $controllerPath = app_path("Http/Controllers/Admin/{$controllerName}.php");

            if (!File::exists($controllerPath)) {
                File::put($controllerPath, $this->stub($controllerName));
                $this->info("✅ Created: {$controllerName}");
            } else {
                $this->warn("⚠️ Already exists: {$controllerName}");
            }
        }
    }

    protected function stub($class)
    {
        return <<<PHP
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class {$class} extends Controller
{
    public function index()
    {
        return view('admin.modules.{$class}');
    }
}
PHP;
    }
}
