<?php

namespace App\Http\Controllers;

use App\Model\User;

class HomeController extends Controller
{
    public function index($id)
    {
        $user = User::find(intval($id));
        // return  json_encode($user, JSON_PRETTY_PRINT);
        return $user;
    }
}
