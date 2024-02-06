<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index($user)
    {
        return 'سلام دنیا' . $user;
    }
}
