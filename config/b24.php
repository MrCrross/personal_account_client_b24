<?php

return [
    'webhook' => env('B24_WEBHOOK'),
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
        ],
    ],
];
