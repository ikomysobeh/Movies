<?php
namespace App\Services;

use App\Models\Video;


class VideoService extends ServiceHelper{
    public function __construct()
    {
        $this->model=new Video();
        $this->attributes=[
            'uniqueId',
            'movieId',
            'path',
            'resolution',
            'extension',
        ];
    }


}



?>
