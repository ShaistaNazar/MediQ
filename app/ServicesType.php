<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * ServicesType exteds Model to provide model for ServicesType.
*/
class ServicesType extends Model
{
    public function services()
    {
        return $this->beongsToMany(Services::class);
    }

    public function cities()
    {
        return $this->beongsToMany(City::class);
    }
}
