<?php

namespace App\Http\Wrappers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

abstract class BWebHookConstructor
{
    public const DRIVER_WEBHOOK = 'webhook';

    public const DRIVER_DATABASE = 'database';

    private string $webhook;

    private string $link = '';

    private string $method = '';

    private array $bodyData = [];

    protected int $createdByUser;

    public function __construct()
    {
        $this->webhook = (string) config('b24.webhook');
        if (! Str::endsWith($this->webhook, '/')) {
            $this->webhook .= '/';
        }
        $this->createdByUser = config('b24.createdByUser');
    }

    /**
     * Путь до хука в конфиге
     */
    protected function setRoute(string $path): void
    {
        $this->method = config('b24.routes.'.$path);
        $this->link = $this->webhook.$this->method;
    }

    protected function addSelect(array $select): void
    {
        $this->bodyData['SELECT'] = $select;
    }

    protected function addOrder(array $order): void
    {
        $this->bodyData['ORDER'] = $order;
    }

    protected function addFilters(array $filters): void
    {
        $this->bodyData['FILTER'] = $filters;
    }

    protected function addFields(array $fields, bool $isLowerCase = true): void
    {
        $fieldsParamName = 'fields';
        if (! $isLowerCase) {
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

    protected function addFieldInBody(string $field, array|string|int $value): void
    {
        $this->bodyData[$field] = $value;
    }

    /**
     * @return array|mixed|void
     *
     * @throws ConnectionException
     */
    protected function execute(bool $isJson = false): mixed
    {
        $bodyData = $this->bodyData;
        $this->bodyData = [];
        // B24_DRIVER=database: без вебхука, запись/чтение локальных таблиц b_*
        if ($this->usesDatabaseDriver()) {
            return (new BLocalRest)->call($this->method, $bodyData);
        }
        if ($isJson) {
            $bodyData = json_encode($bodyData);
        }
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

    /**
     * Скачать файл по DOWNLOAD_URL, который вернул Битрикс-Диск.
     *
     * @throws ConnectionException
     */
    protected function download(string $url): Response
    {
        if ($this->usesDatabaseDriver()) {
            return (new BLocalRest)->download($url);
        }

        return Http::get($url);
    }

    private function usesDatabaseDriver(): bool
    {
        return config('b24.driver') === self::DRIVER_DATABASE;
    }
}
