<?php

namespace App\Http\Controllers\Web\backend;

use Illuminate\Http\Request;
use App\Services\SettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminSettingUpdateRequest;
use App\Http\Requests\GeneralSettingStoreRequest;

class SettingController extends Controller
{
    public $settingServiceObj;

    public  function  __construct()
    {
        $this->settingServiceObj = new SettingService();
    }

    public function  create()
    {
        return $this->settingServiceObj->create();
    }

    public function adminSetting()
    {
        return $this->settingServiceObj->adminSettingPage();
    }

    public function update(GeneralSettingStoreRequest $request)
    {
        $title        = $request->input('system_title');
        $name         = $request->input('company_name');
        $tag          = $request->input('tag_line');
        $code         = $request->input('phone_code');
        $phone        = $request->input('phone_number');
        $email        = $request->input('email');
        $timeZone     = $request->input('time_zone');
        $language     = $request->input('language');
        $country      = $request->input('country');
        $currency     = $request->input('currency');
        $registration = $request->input('registration');

        if($request->hasFile('logo')){
            $logo = $request->file('logo');
        }else{
            $logo = '';
        }
        if($request->hasFile('favicon')){
            $favicon  = $request->file('favicon');
        }else{
            $favicon = '';
        }

        return $this->settingServiceObj->store($title, $name, $tag, $code, $phone, $email, $timeZone, $language, $country, $currency, $registration, $logo, $favicon);
    }

    public function adminSettingUpdate(AdminSettingUpdateRequest $request)
    {
        $title     = $request->input('title');
        $shortName = $request->input('system_short_name');

        if($request->hasFile('logo')){
            $logo = $request->file('logo');
        }else{
            $logo = null;
        }

        if($request->hasFile('mini_logo')){
            $miniLogo = $request->file('mini_logo');
        }else{
            $miniLogo = null;
        }

        if($request->hasFile('favicon')){
            $favicon = $request->file('favicon');
        }else{
            $favicon = null;
        }
        
        $copyright = $request->input('copyright');
        
        return $this->settingServiceObj->adminSettingUpdate($title, $shortName, $logo, $miniLogo, $favicon, $copyright);
    }
}
