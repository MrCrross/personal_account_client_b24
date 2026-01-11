<?php

namespace App\Http\Wrappers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract class BWebHookConstructor
{
    private string $webhook;
    private string $link = '';
    private array $bodyData = [];
    protected const CREATED_BY_USER = 312; // БОТ ФЛЕШ-ФОРМАТ
    public function __construct()
    {
        $this->webhook = config('b24.webhook');
        if (!Str::endsWith($this->webhook, '/')) {
            $this->webhook .= '/';
        }
    }

    /**
     * Путь до хука в конфиге
     * @param string $path
     * @return void
     */
    protected function setRoute(string $path): void
    {
        $this->link = $this->webhook . config('b24.routes.' . $path);
    }

    protected function addSelect(array $select): void
    {
        $this->bodyData['SELECT'] = $select;
    }

    protected function addOrder(array $order): void
    {
        $this->bodyData['ORDER'] = $order;
    }

    /**
     * @param array $filters
     * @return void
     */
    protected function addFilters(array $filters): void
    {
        $this->bodyData['FILTER'] = $filters;
    }

    protected function addFields(array $fields, bool $isLowerCase = true): void
    {
        $fieldsParamName = 'fields';
        if (!$isLowerCase) {
            $fieldsParamName = 'FIELDS';
        }
        $this->bodyData[$fieldsParamName] = $fields;
    }

    protected function addId(int $id): void
    {
        $this->bodyData['ID'] = $id;
    }

    protected function setBodyData(array $bodyData): void
    {
        $this->bodyData = $bodyData;
    }

    /**
     * @param string $field
     * @param array|string|int $value
     * @return void
     */
    protected function addFieldInBody(string $field, array|string|int $value): void
    {
        $this->bodyData[$field] = $value;
    }

    /**
     * @return array|mixed|void
     * @throws ConnectionException
     */
    protected function execute(bool $isJson = false): mixed
    {
        $bodyData = $this->bodyData;
        if ($isJson) {
            $bodyData = json_encode($bodyData);
        }
        $this->bodyData = [];
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])
            ->post($this->link, $bodyData);

        if ($response->successful()) {
            return $response->json();
        }

        return $response;
    }
}
