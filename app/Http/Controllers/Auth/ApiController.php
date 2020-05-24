<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthApiController extends Controller
{
    function index(Request $request) {
        return $request->user();
    }
}
