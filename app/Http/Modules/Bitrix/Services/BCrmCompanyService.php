<?php

namespace App\Http\Modules\Bitrix\Services;

use App\Http\Modules\Bitrix\Repositories\BCrmCompanyRepository;
use App\Http\Resources\Autocomplete\AutocompleteCollection;

class BCrmCompanyService
{
    public function autocomplete(string $term): AutocompleteCollection
    {
        return new AutocompleteCollection(BCrmCompanyRepository::autocomplete($term));
    }
}
