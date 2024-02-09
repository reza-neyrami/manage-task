<?php

namespace App\Http\Controllers;

use App\Core\Services\Auth;
use App\Core\Services\Request;
use App\Core\Services\Response;
use App\Model\User;
use Exception;

class HomeController extends Controller
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    public function index()
    {
        try {
            $auth = Auth::user();
            return $auth;
          } catch (Exception $e) {
            return Response::json(['message' => $e->getMessage()], 401);
          }

    }
}
