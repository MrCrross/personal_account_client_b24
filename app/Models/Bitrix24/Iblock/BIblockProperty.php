<?php

namespace App\Models\Bitrix24\Iblock;

use Illuminate\Database\Eloquent\Model;

class BIblockProperty extends Model
{
    protected $table = 'b_iblock_property';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'IBLOCK_ID',
        'CODE',
        'NAME',
        'USER_TYPE',
        'ACTIVE',
    ];
}
