<?php

namespace App\Http\Controllers;

use App\Model\User;

class HomeController extends Controller
{
    public function index($id)
    {
       return User::find(intval($id));
    }

    public function __toString()
    {
        return [];
    }
}
