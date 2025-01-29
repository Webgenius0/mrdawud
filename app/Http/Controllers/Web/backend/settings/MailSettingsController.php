<?php

namespace App\Http\Controllers\Web\backend\settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
class MailSettingsController extends Controller
{
    //for mail settings
    public function index()
    {
        return view('backend.layout.setting.mail-settings');
    }

//for stripe settings
    public function stripeSettings()
    {

        return view('backend.layout.setting.stripe-settings');
    }
    public function mailSettingUpdate(Request $request)
    {

        $messages = [
            'mail_mailer.required' => 'The mailer is required.',
            'mail_mailer.string' => 'The mailer must be a string.',
            'mail_host.required' => 'The mail host is required.',
            'mail_host.string' => 'The mail host must be a string.',
            'mail_port.required' => 'The mail port is required.',
            'mail_port.string' => 'The mail port must be a string.',
            'mail_username.nullable' => 'The mail username must be a string.',
            'mail_password.nullable' => 'The mail password must be a string.',
            'mail_encryption.nullable' => 'The mail encryption must be a string.',
            'mail_from_address.required' => 'The mail from address is required.',
            'mail_from_address.string' => 'The mail from address must be a string.',
        ];

        $request->validate([
            'mail_mailer' => 'required|string',
            'mail_host' => 'required|string',
            'mail_port' => 'required|string',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string',
            'mail_from_address' => 'required|string',
        ], $messages);

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak = "\n";
            $envContent = preg_replace([
                '/MAIL_MAILER=(.*)\s/',
                '/MAIL_HOST=(.*)\s/',
                '/MAIL_PORT=(.*)\s/',
                '/MAIL_USERNAME=(.*)\s/',
                '/MAIL_PASSWORD=(.*)\s/',
                '/MAIL_ENCRYPTION=(.*)\s/',
                '/MAIL_FROM_ADDRESS=(.*)\s/',
            ], [
                'MAIL_MAILER=' . $request->mail_mailer . $lineBreak,
                'MAIL_HOST=' . $request->mail_host . $lineBreak,
                'MAIL_PORT=' . $request->mail_port . $lineBreak,
                'MAIL_USERNAME=' . $request->mail_username . $lineBreak,
                'MAIL_PASSWORD=' . $request->mail_password . $lineBreak,
                'MAIL_ENCRYPTION=' . $request->mail_encryption . $lineBreak,
                'MAIL_FROM_ADDRESS=' . '"' . $request->mail_from_address . '"' . $lineBreak,
            ], $envContent);

            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }
            return response()->json([
                'success' => true,
                'message' => 'Mail Setting Updated Successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Mail Setting Update Failed'
            ]);
        }
    }

    //stripe

    public function stripeSettingUpdate(Request $request)
    {
        $request->validate([
            'STRIPE_KEY' => 'nullable|string',
            'STRIPE_SECRET' => 'nullable|string',
            
        ]);

        try {
            $envContent = File::get(base_path('.env'));
            $lineBreak = "\n";
            $envContent = preg_replace([
                '/STRIPE_KEY=(.*)\s/',
                '/STRIPE_SECRET=(.*)\s/',
                
            ], [
                'STRIPE_KEY=' . $request->STRIPE_KEY . $lineBreak,
                'STRIPE_SECRET=' . $request->STRIPE_SECRET . $lineBreak,
                
            ], $envContent);

            if ($envContent !== null) {
                File::put(base_path('.env'), $envContent);
            }
            session()->flash('success', 'Stripe settings updated successfully.');
            return redirect()->back();
           // return redirect()->back()->with('t-success', 'Stripe Setting Update successfully.');
        } catch (\Throwable $th) {
            return redirect(route('dashboard'))->with('t-error', 'Stripe Setting Update Failed');
        }
    }
}
