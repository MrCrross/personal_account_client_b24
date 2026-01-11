<?php

namespace App\Models\Bitrix24\Crm;

use Illuminate\Database\Eloquent\Model;

class BCrmContact extends Model
{
    protected $table = 'b_crm_contact';

    public $timestamps = false;

    protected $fillable = [
        'ID',
        'DATE_CREATE',
        'COMPANY_ID',
        'SEARCH_CONTENT',
        'OPENED',
    ];
}
