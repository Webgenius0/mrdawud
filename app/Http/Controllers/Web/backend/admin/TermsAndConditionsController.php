<?php

namespace App\Http\Controllers\Web\backend\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TermsAndConditions;

class TermsAndConditionsController extends Controller
{
    public function index()
    {
        $termsAndConditions = TermsAndConditions::first();
        return view('backend.layout.termsAndConditions.index', compact('termsAndConditions'));
    }

    //updateOrCreateTermsAndConditions
    public function storeOrUpdate(Request $request)
    {
        $request->validate([
            'shord_description' => 'required|string',
            'terms' => 'required|string',
        ]);

        TermsAndConditions::updateOrCreate(
            ['id' => 1], // Ensuring there's a single record
            [
                'shord_description' => $request->input('shord_description'),
                'terms' => $request->input('terms'),
            ]
        );

        flash()->success('Terms and conditions updated successfully');
        return redirect()->back();
    }


}

