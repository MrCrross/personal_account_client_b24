<?php

return [
    'webhook' => env('B24_WEBHOOK'),
    'createdByUser' => env('B24_CREATED_BY_USER', 1),
    'default' => [
        'auditors' => explode(',', env('B24_AUDITORS', '1,2,3')),
        'responsibleId' => env('B24_RESPONSIBLE_ID', 1),
    ],
    'domain' => env('B24_DOMAIN', 'https://bitrix24.ru'),
    'routes' => [
        'group' => [
            'get' => 'sonet_group.get',
        ],
        'contact' => [
            'get' => 'crm.contact.list',
            'getById' => 'crm.contact.get',
            'add' => 'crm.contact.add',
            'company' => [
                'get' => 'crm.contact.company.items.get',
                'add' => 'crm.contact.company.add',
            ],
        ],
        'tasks' => [
            'get' => 'tasks.task.list',
            'getById' => 'tasks.task.get',
            'create' => 'tasks.task.add',
            'files' => [
                'attach' => 'tasks.task.files.attach',
            ],
            'results' => [
                'get' => 'tasks.task.result.list',
            ],
            'comments' => [
                'add' => 'task.commentitem.add',
            ],
        ],
        'disk' => [
            'get' => 'disk.storage.getList',
            'upload' => 'disk.storage.uploadfile',
            'attachedObject' => 'disk.attachedObject.get',
            'file' => 'disk.file.get',
        ],
    ],
];
