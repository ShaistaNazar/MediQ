<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\User;
use App\Media;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

/**
 * Class UserRepository
 * @package 
 * @version 
*/

/*
 * UserRepository exteds BaseRepository and provide repository layer for User.
*/
class UserRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return User::class;
    }

    public function getAllUsers()
    {
        return $this->model::all();
    }

    public function userProfile($id)
    {
        return $this->model::where('id',$id)->first();
    }

    public function getUser($id)
    {
        return $this->model::find($id);
    }

    public function getUserByPhone($phone)
    {
        return $this->model::wherePhone($phone)->first();
    }

    public function getUserByEmail($email)
    {
        return $this->model::where('phone',$email)->first();
    } 

    public function bannerMedia()
    {
        return Media::where('media_type','banner')->get();
    }
    
    public function updatePlayerId($userid,$playerId){
        $user=$this->model::find($userid);
        $user->player_id=$playerId;
        
        if($user->save())
        {
            return $user;
        }
        return false;
    }    
}
