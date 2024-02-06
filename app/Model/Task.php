<?php
namespace App\Model;
class User extends Model {
    public $timestamps = false;
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
}