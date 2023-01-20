<?php
namespace App\Services;

use App\Models\Rating;


class RatingService extends ServiceHelper{
    public function __construct()
    {
        $this->model=new Rating();
        $this->attributes=[
            'userId',
            'id',
            'movieId',
            'score',
        ];
    }


}



?>
