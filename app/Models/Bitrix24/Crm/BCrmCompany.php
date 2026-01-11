<?php

namespace App\Models\Bitrix24\Crm;

use Illuminate\Database\Eloquent\Model;

class BCrmCompany extends Model
{
    protected $table = 'b_crm_company';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'DATE_CREATE',
        'TITLE',
        'SEARCH_CONTENT',
        'OPENED',
    ];
}
