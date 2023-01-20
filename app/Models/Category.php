<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
    ];

    public function getMovieAttribute(){
        return $this->hasMany(Movie::class,'CategoryId','id')->get();
    }
}

