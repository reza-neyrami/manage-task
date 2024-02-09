<?php
namespace App\Model;

use App\Core\Interfaces\Model\Model;

class Task extends Model {
    public $timestamps = false;
    protected $table = 'tasks';
    protected $fillable = ['name', 'description', 'startDate','endDate','status','userId','created_at','updated_at'];
    protected $toArray = ['name', 'description','startDate','endDate','status','userId','created_at','updated_at'];


}