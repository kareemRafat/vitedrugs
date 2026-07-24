<?php

namespace App\Http\Controllers;

class ContactController extends Controller
{
    public function create()
    {
        return view('app.pages.contact');
    }
}
