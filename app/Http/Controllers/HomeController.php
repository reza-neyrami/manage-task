<?php

namespace App\Http\Controllers;

use App\Core\Services\Request;
use App\Model\User;

class HomeController extends Controller
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function index($id)
    {
        // return $this->request->only(['email', 'password']);
        // var_dump();
        $user = User::find(intval($id));
        return  json_encode($user, JSON_PRETTY_PRINT);

    }
}
