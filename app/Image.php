<?php

namespace App;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class Image extends Eloquent
{
    protected $collection="images";
    protected $fillable=  ['name', 'extension'];
}
