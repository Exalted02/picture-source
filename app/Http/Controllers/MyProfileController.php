<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyProfileController extends Controller
{
    public function index()
    {
      $data[] = '';
      return view('my-profile.index', $data);
    }
}
