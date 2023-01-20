<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'userId',
        'movieId',
        'score',
    ];

    public function getMovieAttribute(){
        return $this->hasOne(Movie::class,'uniqueId','movieId')->firstOrFail();
    }
    public function getUserAttribute(){
        return $this->hasOne(User::class,'uniqueId','userId')->firstOrFail();
    }
}
