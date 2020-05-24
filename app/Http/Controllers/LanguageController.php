<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    function index($locale) {
        session(['my_locale' => $locale]);
        return redirect('/');
    }
}
