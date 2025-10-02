<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Yajra\DataTables\DataTables;
class SettingController extends Controller
{
    public function index()
    {
         $user_id =login_id();
        $settings = Setting::pluck('value','key')->toArray();

        $policies = Setting::where('user_id', $user_id)
            ->whereIn('key', ['policies', 'premium'])
            ->pluck('value', 'key')
            ->toArray();

        return view('admin.settings.index',compact('settings' ,'policies'));
    }

    public function save(Request $request)
    { 
        //  dd($request->all());
        foreach($request->setting ?? [] as $key=>$value){

            save_settings($key,$value);
        }
        return back();
    }

    public function saveLogo(Request $request)
    {
       // dd($request->all());
        $user = auth()->user();

        // Validate the image
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $settings = Setting::where('key', 'logo')->first();
            // Delete old image if it exists
            if ($settings && $settings->value) {
                $oldImagePath = public_path('logo/' . $settings->value);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }
            // Get the uploaded file
            $image = $request->file('image');

            // Generate a unique file name
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            // Move image to the public path
            $image->move(public_path('logo'), $imageName);
            // Save the new image path in the database
            if ($settings) {
                $settings->update([
                    'value' => $imageName, // Store only the filename
                    'user_id' => $user->id
                ]);
            } else {
                Setting::create([
                    'key' => 'logo',
                    'value' => $imageName,
                    'user_id' => $user->id
                ]);
            }
              return response()->json([
            'message' => 'Logo uploaded successfully.',
            'filename' => $imageName,
            'url' => asset('logo/' . $imageName)
        ]);
        }
       return response()->json(['error' => 'No image uploaded.'], 400);
    }

     public function log(Request $request) {
        if ($request->ajax()) {
            try {
                $data = Log::select(['id', 'location_id','message'])->orderBy('id', 'desc');
                return DataTables::of($data)->make(true);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => 'Something went wrong: ' . $e->getMessage()
                ], 500);
            }
        }
        return view('admin.log.index');
    }
    public function checkPassword(Request $request)
{
    $request->validate([
        'password' => 'required|string',
    ]);

    $user = auth()->user();
    if (Hash::check($request->password, $user->password)) {
        return response()->json(['status' => 'success']);
    }

    return response()->json(['status' => 'error']);
}

}
