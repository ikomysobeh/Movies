<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Constraint\Count;
use SebastianBergmann\LinesOfCode\Counter;

class Movie extends Model
{
    use HasFactory;
    protected $fillable = [
        'uniqueId',
        'title',
        'description',
        'CategoryId',
    ];
    protected $appends=[
        'rating'

        ];
    public function getVideoAttribute(){
        return $this->hasMany(Video::class,'movieId','uniqueId')->get();
    }
    public function getRatingsAttribute(){
        return $this->hasMany(Rating::class,'movieId','uniqueId')->get();
    }
    public function getCategoryAttribute(){
        return $this->hasOne(Category::class,'id','CategoryId')->first();
    }
    protected function getRatingAttributes()
    {
        $rat=$this->getRatingsAttribute();
        $c= count($rat);
        $sum=0;
        foreach ($rat as $i){
            $sum+=$i->score;
        }
        return $sum/$c;

    }


    }
