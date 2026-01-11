<?php

namespace App\Models\Bitrix24\Iblock;

use Illuminate\Database\Eloquent\Model;

class BIblock extends Model
{
    protected $table = 'b_iblock';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'NAME',
        'ACTIVE',
    ];
}
