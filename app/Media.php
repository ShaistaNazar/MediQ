<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * Media exteds Model to provide model for Media.
*/
class Media extends Model
{
    protected $table='media';
    protected $fillable=['id','media_type', 'file'];
}
