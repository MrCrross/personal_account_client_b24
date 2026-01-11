<?php

namespace App\Models\Bitrix24;

use Illuminate\Database\Eloquent\Model;

class BSonetGroup extends Model
{
    protected $table = 'b_sonet_group';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'NAME',
        'DATE_CREATE',
        'ACTIVE',
        'VISIBLE',
        'CLOSED',
    ];
}
