<?php
namespace App\Model;
use App\Core\Interfaces\Model\Model;
use Serializable ;

class User extends Model  

{
    public $timestamps = false;
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    protected $toArray = ['name', 'email'];

    

}