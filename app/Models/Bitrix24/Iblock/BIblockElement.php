<?php

namespace App\Models\Bitrix24\Iblock;

use Illuminate\Database\Eloquent\Model;

class BIblockElement extends Model
{
    protected $table = 'b_iblock_element';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'DATE_CREATE',
        'IBLOCK_ID',
        'ACTIVE',
    ];
}
