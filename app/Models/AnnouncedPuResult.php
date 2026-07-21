<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncedPuResult extends Model
{
    //
    protected $table = 'announced_pu_results';

    protected $primaryKey = 'result_id';

    public $timestamps = false;

    protected $guarded = [];

    public function pollingUnit()
    {
        return $this->belongsTo(
            PollingUnit::class,
            'polling_unit_uniqueid',
            'uniqueid'
        );
    }
}
