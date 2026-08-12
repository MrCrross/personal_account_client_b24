<?php

namespace App\Http\Modules\Bitrix\Services;

use App\Http\Modules\Bitrix\Repositories\BCrmContactRepository;
use App\Http\Wrappers\BWebHookConstructor;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Auth;

class BCrmContactService extends BWebHookConstructor
{
    /**
     * @param string $person
     * @param string $personPhone
     * @return int
     */
    public function searchContact(string $person, string $personPhone): int
    {
        return BCrmContactRepository::searchContact($person, $personPhone)?->id ?? 0;
    }

    /**
     * @param string $person
     * @param string $personPhone
     * @return int
     * @throws ConnectionException
     */
    public function getContactIdOrStore(string $person, string $personPhone): int
    {
        $contactId = $this->searchContact($person, $personPhone);
        if ($contactId === 0 && !empty(Auth::user()->b_company_id)) {
            $contactId = $this->createContact($person, $personPhone);
        }

        return $contactId;
    }

    /**
     * @param string $person
     * @param string $personPhone
     * @return int
     * @throws ConnectionException
     */
    private function createContact(string $person, string $personPhone): int
    {
        $this->setRoute('contact.add');
        $nameArr = explode(' ', $person);
        for ($i = 0; $i <= 2; $i++) {
            if (empty($nameArr[$i])) {
                $nameArr[$i] = '';
            }
        }
        $fields = [
            'NAME' => $nameArr[1],
            'LAST_NAME' => $nameArr[0],
            'SECOND_NAME' => $nameArr[2],
            'COMPANY_ID' => Auth::user()->b_company_id,
            'OPENED' => 'Y',
            'EXPORT' => 'Y',
            'ASSIGNED_BY_ID' => $this->createdByUser,
            'PHONE' => $personPhone,
        ];
        $this->setBodyData(['fields' => $fields]);
        $response = $this->execute();

        return $response['result'];
    }
}
