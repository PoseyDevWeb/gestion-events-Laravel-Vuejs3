<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'titleEvent',
        'slugEvent',
        'descriptionEvent',
        'dateEvent',

    ];


    // Scope pour filtrer les dates par une intervalle donnée
    public function scopeDateInterval($query, $dateArray)
    {
        return $query->whereIn(DB::raw('DATE(dateEvent)'), $dateArray);
    }
}
