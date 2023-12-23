<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursesTaken extends Model
{
    protected $primaryKey = 'CourseID';
    public $timestamps = false;
    protected $table = 'courses_taken';
    
    use HasFactory;
}
