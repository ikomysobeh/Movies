<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;
    protected $primaryKey='uniqueId';
    public $incrementing=false;
    protected $fillable = [
        'uniqueId',
        'movieId',
        'path',
        'resolution',
        'extension',
    ];
    public function getMoveAttribute(){
        return $this->hasOne(Movie::class,'uniqueId','movieId')->firstOrFail();
    }
//    protected $appends = [
//
//    ];

}

