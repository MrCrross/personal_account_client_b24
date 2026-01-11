<?php

namespace App\Models\Bitrix24\Iblock;

use Illuminate\Database\Eloquent\Model;

class BIblockElementProperty extends Model
{
    protected $table = 'b_iblock_element_property';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'IBLOCK_ELEMENT_ID',
        'IBLOCK_PROPERTY_ID',
        'VALUE',
    ];
}
