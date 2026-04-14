<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TravelOrderModel extends Model
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'travel_order';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    public $fillable = [
        'user_id',
        'destination',
        'departure_date',
        'return_date',
        'status'
    ];
}
