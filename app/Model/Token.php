<?php
namespace App\Model;

use App\Core\Interfaces\Model\Model;

class Token extends Model {
    public $timestamps = false;
    protected $table = 'tokens';
    protected $fillable = ['userId', 'token', 'expiry', 'created_at', 'updated_at'];
    protected $toArray = ['userId', 'token', 'expiry'];


}