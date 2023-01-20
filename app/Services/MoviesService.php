<?php
namespace App\Services;

use App\Models\Movie;


class MoviesService extends ServiceHelper{
    public function __construct()
    {
        $this->model=new Movie();
        $this->attributes=[
            'uniqueId',
            'title',
            'description',
            'CategoryId',

        ];
    }


}


?>
