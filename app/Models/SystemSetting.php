<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'system_title',
        'logo',
        'favicon',
        'company_name',
        'tag_line',
        'phone_code',
        'phone_number',
        'whatsapp',
        'email',
        'time_zone',
        'language',
        'country',
        'currency',
        'admin_title',
        'system_short_name',
        'admin_logo',
        'admin_mini_logo',
        'admin_favicon',
        'copyright_text',
    ];
}
