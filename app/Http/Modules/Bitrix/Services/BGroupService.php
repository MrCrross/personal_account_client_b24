<?php

namespace App\Http\Modules\Bitrix\Services;

use App\Http\Modules\Bitrix\Repositories\BGroupRepository;
use App\Http\Resources\Autocomplete\AutocompleteCollection;

class BGroupService
{
    public function autocomplete(string $term): AutocompleteCollection
    {
        return new AutocompleteCollection(BGroupRepository::autocomplete($term));
    }
}
