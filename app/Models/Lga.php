<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lga extends Model
{
    //
    protected $table = 'lga';

    protected $primaryKey = 'uniqueid';

    public $timestamps = false;

    protected $guarded = [];

    public function wards()
    {
        return $this->hasMany(
            Ward::class,
            'lga_id',
            'lga_id'
        );
    }

    public function pollingUnits()
    {
        return $this->hasMany(
            PollingUnit::class,
            'lga_id',
            'lga_id'
        );
    }
}
