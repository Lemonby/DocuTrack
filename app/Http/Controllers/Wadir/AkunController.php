<?php

namespace App\Http\Controllers\Wadir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    public function index()
    {
        return view('wadir.akun.index');
    }
}
