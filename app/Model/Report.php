<?php
namespace App\Model;

use App\Core\Interfaces\Model\Model;

class Report extends Model {
    public $timestamps = false;
    protected $table = 'reports';
    protected $fillable = ['taskId', 'filename'];
    protected $toArray = ['id','taskId', 'filename','created_at','updated_at'];


}