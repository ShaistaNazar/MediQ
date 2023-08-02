<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\City;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

/**
 * Class UserRepository
 * @package 
 * @version 
*/

/*
 * CityRepository exteds BaseRepository and provide repository layer for City.
*/
class CityRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return City::class;
    }

    public function getAllCities()
    {
        return $this->model::all();
    }
}
