<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestController extends Controller
{
    function index() {
        return response()->json([
            'message' => "ok",
        ], Response::HTTP_OK);
    }
}
