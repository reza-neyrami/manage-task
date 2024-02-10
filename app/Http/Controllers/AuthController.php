
<?php

use App\Core\Repository\AuthRepository;
use App\Core\Services\JWTApi;
use App\Core\Services\Request;
use App\Core\Services\Response;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    private $authRepositroy;
    protected $request;

    public function __construct(AuthRepository $authRepositroy, Request $request)
    {
        $this->authRepositroy = $authRepositroy;
        $this->request = $request;
    }
    public function login()
    {
        $login =  $this->authRepositroy->login([
            'email' => $this->request->get('email'),
            'password' => $this->request->get('password')
        ]);
        if ($login['status'] == false) {
            return Response::json($login, 401);
        }

        $jwt_token = JWTApi::generate_jwt_token($login['user_id']);
        return Response::json([
            'access_token' => $jwt_token,
            "message" => $login,
        ], 200);
    }


    public function logout(){
        $logout =  $this->authRepositroy->logout();
        if ($logout['status'] == false) {
            return Response::json($logout, 401);
        }
        return Response::json([
            "message" => $logout,
        ], 200);
    }

    public function register(){
        $register =  $this->authRepositroy->register([
            'username' => $this->request->get('username'),
            'email' => $this->request->get('email'),
            'password' => $this->request->get('password'),
            'role' => $this->request->get('role') ?? 'programmer'
        ]);
        if ($register['status'] == false) {
            return Response::json($register, 401);
        }
        return Response::json([
            "message" => $register,
        ], 200);
    }
}
