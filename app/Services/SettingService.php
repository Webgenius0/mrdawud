<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\DB;

class SettingService extends Service
{
    public  function  create()
    {
        $data['countries'] = DB::table('countries')->get();
        $data['timeZones'] = DB::table('time_zones')->get();
        $data['currencies'] = DB::table('currency')->get();
        $data['setting'] = SystemSetting::first();

        return view('backend.layout.setting.general')->with($data);
    }

    public function adminSettingPage()
    {
        $data['setting'] = SystemSetting::first();
        
        return view('backend.layout.setting.adminsetting')->with($data);
    }
    public  function store($title, $name, $tag, $code, $phone, $email, $timeZone, $language, $country, $currency, $registration, $logo, $favicon)
    {
        try {
            DB::beginTransaction();

            $setting = SystemSetting::firstOrNew();
            if($logo != null){
                $filePath      = $this->fileUpload($logo, 'systems/logo');
                $setting->logo = $filePath;
            }

            if($favicon != null){
                $filePath         = $this->fileUpload($favicon, 'systems/favicon');
                $setting->favicon = $filePath;
            }
            $setting->system_title = $title;
            $setting->company_name = $name;
            $setting->tag_line     = $tag;
            $setting->phone_code   = $code;
            $setting->phone_number = $phone;
            $setting->whatsapp     = $phone;
            $setting->email        = $email;
            $setting->time_zone    = $timeZone;
            $setting->language     = $language;
            $setting->country      = $country;
            $setting->currency     = $currency;
            $setting->registration = $registration;

            $res = $setting->save();
            DB::commit();
            if($res){
                return redirect()->back()->with('message', 'updated successfully');
            }
        }catch (\Exception $e){
            DB::rollback();
            info($e);
        }
    }

    public function adminSettingUpdate($title, $shortName, $logo, $miniLogo, $favicon, $copyright)
    {
        try {
            DB::beginTransaction();

            $setting = SystemSetting::firstOrNew();
            
            $setting->admin_title       = Str::title($title);
            $setting->system_short_name = Str::title($shortName);
            
            if($logo != null){
                $path                = $this->fileUpload($logo, 'systems/logo/');
                $setting->admin_logo = $path;
            }

            if($miniLogo != null){
                $path                     = $this->fileUpload($logo, 'systems/minilogo/');
                $setting->admin_mini_logo = $path;
            }

            if($miniLogo != null){
                $path                   = $this->fileUpload($logo, 'systems/favicon/');
                $setting->admin_favicon = $favicon;
            }
            $setting->copyright_text = $copyright;

            $res = $setting->save();

            DB::commit();
            if($res){
                return redirect()->back()->with('message', 'Update information successfully');
            }
        } catch (\Exception $e) {
            DB::rollback();
            info($e);
        }
    }
}
