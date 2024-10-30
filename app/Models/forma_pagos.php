<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class forma_pagos extends Model
{
    use HasFactory;

    protected $fillable = ['nombre','casa_central_id','comercio_id'];
}
