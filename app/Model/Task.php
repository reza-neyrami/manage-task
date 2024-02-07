<?php
namespace App\Model;

use App\Core\Interfaces\Model\Model;

class User extends Model {
    public $timestamps = false;
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];


}