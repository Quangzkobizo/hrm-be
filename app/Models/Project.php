<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'priority', 'client', 'price', 'start_date', 'end_date', 'progress', 'description'
    ];

    protected $casts = [
        'start_date' => 'date', // Chuyển đổi trường start_date sang kiểu dữ liệu date
        'end_date' => 'date',   // Chuyển đổi trường end_date sang kiểu dữ liệu date
    ];
}
