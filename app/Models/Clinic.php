<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    protected $fillable = [
        'name','cep','address','city','state','latitude','longitude','phone','active'
    ];
}
