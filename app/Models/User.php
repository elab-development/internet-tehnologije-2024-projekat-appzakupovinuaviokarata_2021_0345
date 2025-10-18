<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;


class User extends Authenticatable
{
    use HasApiTokens, Notifiable;    

    protected $fillable = ['name','email','password'];
    protected $hidden   = ['password','remember_token'];
}
