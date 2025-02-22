<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $setting = Settings::select('id', 'key_name', 'value')->get()->groupBy('key_name')->map(function ($item, $key) {
            if ($key === 'slider_image') {
                return $item->map(fn($i) => ['id' => $i->id, 'value' => $i->value])->all();
            }
            return $item->count() > 1 
                ? $item->map(fn($i) => ['id' => $i->id, 'value' => $i->value])->all()  
                : $item->first()->value;
        })->toArray();
        $settings['slider_image'] = $settings['slider_image'] ?? [];
                
        return view('settings.index', compact('setting'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except('_token', 'slider_image');
        if($request->file('slider_image') != null){
            foreach ($request->file('slider_image') as $image) {
                $imageName = time() . '-' . $image->getClientOriginalName();
                $imagePath = 'uploads/slider_img/' . $imageName;
    
                // Move the image to public/slider_img
                $image->move(public_path('uploads/slider_img'), $imageName);
    
                // Save to database
                Settings::create([
                    'key_name' => 'slider_image',
                    'value' => $imagePath,
                    'title' => ucfirst(str_replace('_', ' ', 'slider_image')),
                    'module' => 'Dashboard',
                    'active' => 'Y',
                    'updated_at' => now(),
                ]);
            }
        }
        foreach ($data as $key => $value) {
            Settings::updateOrInsert(
                ['key_name' => $key],
                [
                    'value' => $value,
                    'title' => ucfirst(str_replace('_', ' ', $key)),
                    'module' => 'Booking',
                    'active' => 'Y',
                    'updated_at' => now(),
                ]
            );
        }

        return back()->with('success', 'Settings saved successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function show(Settings $settings)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function edit(Settings $settings)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Settings $settings)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Settings  $settings
     * @return \Illuminate\Http\Response
     */
    public function destroy(Settings $settings, Request $request)
    {
        $settings = Settings::where('id', $request->id)->first();
        if ($settings) {
            $settings->delete();
            return response()->json(['status' => 'success', 'message' => 'Slider image delete successfully !!']);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Slider image not found !!']);
        }
    }
}
