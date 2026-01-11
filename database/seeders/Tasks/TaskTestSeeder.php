<?php

namespace Database\Seeders\Tasks;

use App\Models\Bitrix24\BSonetGroup;
use App\Models\Bitrix24\BUser;
use App\Models\Bitrix24\Crm\BCrmCompany;
use App\Models\Bitrix24\Iblock\BIblock;
use App\Models\Bitrix24\Iblock\BIblockElement;
use App\Models\Bitrix24\Iblock\BIblockElementProperty;
use App\Models\Bitrix24\Iblock\BIblockProperty;
use App\Models\Bitrix24\Task\BTask;
use App\Models\Tasks\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TaskTestSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $b_user = [
            [
                'ID' => 1,
                'NAME' => 'Иван',
                'LAST_NAME' => 'Иванов'
            ],
            [
                'ID' => 2,
                'NAME' => 'Владимир',
                'LAST_NAME' => 'Путянов'
            ],
            [
                'ID' => 3,
                'NAME' => 'Самали',
                'LAST_NAME' => 'Калашников'
            ]
        ];

        $b_sonet_group = [
            [
                'ID' => 12,
                'NAME' => 'Техническая поддержка',
                'DATE_CREATE' => Carbon::yesterday()->toDateTimeString(),
                'ACTIVE' => 'Y',
            ]
        ];

        $b_crm_company = [
            [
                'ID' => 3298,
                'TITLE' => 'Флеш-формат ООО',
                'SEARCH_CONTENT' => '3298 Флеш-формат ООО Лидия Банщикова 83952567150 73952567150 3952567150 952567150 52567150 2567150 567150 67150 7150 150 Информационные технологии логин и пароль не выбрано Услуги',
                'DATE_CREATE' => Carbon::parse('11.02.2020 08:57:59')->toDateTimeString(),
                'OPENED' => 'Y'
            ],
            [
                'ID' => 3943,
                'TITLE' => 'ФФ(внутренний)',
                'SEARCH_CONTENT' => '3943 ФФ(внутренний) Роман Черепанов Информационные технологии НЕ УДАЛЯТЬ ВСЯ ИНФА ПО ФФ !!!!!!!!!!!!!!! логин и пароль не выбрано Услуги 2',
                'DATE_CREATE' => Carbon::parse('15.05.2023 16:20:05')->toDateTimeString(),
                'OPENED' => 'Y'
            ],
        ];

        $b_iblock = [
            [
                'ID' => 151,
                'NAME' => 'График отпусков',
                'ACTIVE' => 'Y'
            ]
        ];

        $b_iblock_property = [
            [
                'ID' => 909,
                'IBLOCK_ID' => 151,
                'NAME' => 'Сотрудник',
                'CODE' => 'USER',
                'USER_TYPE' => 'UserID',
                'ACTIVE' => 'Y'
            ],
            [
                'ID' => 910,
                'IBLOCK_ID' => 151,
                'NAME' => 'Начало отпуска',
                'CODE' => 'DATE_START',
                'USER_TYPE' => 'Date',
                'ACTIVE' => 'Y'
            ],
            [
                'ID' => 911,
                'IBLOCK_ID' => 151,
                'NAME' => 'Окончание отпуска',
                'CODE' => 'DATE_END',
                'USER_TYPE' => 'Date',
                'ACTIVE' => 'Y'
            ],
        ];

        $b_iblock_element = [
            [
                'ID' => 21116,
                'DATE_CREATE' => Carbon::parse('16.07.2025 09:41:34')->toDateTimeString(),
                'IBLOCK_ID' => 151,
                'ACTIVE' => 'Y'
            ],
            [
                'ID' => 21200,
                'DATE_CREATE' => Carbon::parse('01.09.2025 10:42:51')->toDateTimeString(),
                'IBLOCK_ID' => 151,
                'ACTIVE' => 'Y'
            ],
            [
                'ID' => 21204,
                'DATE_CREATE' => Carbon::parse('05.09.2025 15:45:19')->toDateTimeString(),
                'IBLOCK_ID' => 151,
                'ACTIVE' => 'Y'
            ],
        ];

        $b_iblock_element_property = [
            [
                'ID' => 49628,
                'IBLOCK_ELEMENT_ID' => 21116,
                'IBLOCK_PROPERTY_ID' => 909,
                'VALUE' => 198
            ],
            [
                'ID' => 49629,
                'IBLOCK_ELEMENT_ID' => 21116,
                'IBLOCK_PROPERTY_ID' => 910,
                'VALUE' => '2025-08-01'
            ],
            [
                'ID' => 49630,
                'IBLOCK_ELEMENT_ID' => 21116,
                'IBLOCK_PROPERTY_ID' => 911,
                'VALUE' => '2025-08-08'
            ],

            [
                'ID' => 49649,
                'IBLOCK_ELEMENT_ID' => 21200,
                'IBLOCK_PROPERTY_ID' => 909,
                'VALUE' => 635
            ],
            [
                'ID' => 49650,
                'IBLOCK_ELEMENT_ID' => 21200,
                'IBLOCK_PROPERTY_ID' => 910,
                'VALUE' => '2025-09-22'
            ],
            [
                'ID' => 49651,
                'IBLOCK_ELEMENT_ID' => 21200,
                'IBLOCK_PROPERTY_ID' => 911,
                'VALUE' => '2025-10-04'
            ],

            [
                'ID' => 49654,
                'IBLOCK_ELEMENT_ID' => 21204,
                'IBLOCK_PROPERTY_ID' => 909,
                'VALUE' => 658
            ],
            [
                'ID' => 49655,
                'IBLOCK_ELEMENT_ID' => 21204,
                'IBLOCK_PROPERTY_ID' => 910,
                'VALUE' => '2025-09-29'
            ],
            [
                'ID' => 49656,
                'IBLOCK_ELEMENT_ID' => 21204,
                'IBLOCK_PROPERTY_ID' => 911,
                'VALUE' => '2025-10-12'
            ],
        ];

        $b_tasks = [
            [
                'ID' => 9706,
                'TITLE' => 'Подобрать системный блок и выставить счёт от Флеш формат без ндс',
                'DESCRIPTION' => '',
                'DESCRIPTION_IN_BBCODE' => 1,
                'PRIORITY' => 1,
                'STATUS' => 5,
                'RESPONSIBLE_ID' => 1,
                'DATE_START' => '2019-09-12 11:43:39',
                'DURATION_PLAN' => null,
                'DURATION_FACT' => null,
                'DURATION_TYPE' => 'days',
                'TIME_ESTIMATE' => 0,
                'REPLICATE' => 0,
                'DEADLINE' => '2019-09-19 13:00:00',
                'START_DATE_PLAN' => null,
                'END_DATE_PLAN' => null,
                'CREATED_BY' => 1,
                'CREATED_DATE' => '2019-09-12 11:42:35',
                'CHANGED_BY' => 1,
                'CHANGED_DATE' => '2019-09-16 04:56:59',
                'STATUS_CHANGED_BY' => 1,
                'STATUS_CHANGED_DATE' => '2019-09-16 04:23:01',
                'CLOSED_BY' => 1,
                'CLOSED_DATE' => '2019-09-16 04:23:01',
                'ACTIVITY_DATE' => '2019-09-16 04:56:59',
                'GUID' => '{b2d5a039-a28c-4d13-a3c0-c29ac602a892}',
                'XML_ID' => null,
                'EXCHANGE_ID' => null,
                'EXCHANGE_MODIFIED' => null,
                'OUTLOOK_VERSION' => '9',
                'MARK' => null,
                'ALLOW_CHANGE_DEADLINE' => 0,
                'ALLOW_TIME_TRACKING' => 0,
                'TASK_CONTROL' => 0,
                'ADD_IN_REPORT' => 0,
                'GROUP_ID' => 12,
                'PARENT_ID' => null,
                'FORUM_TOPIC_ID' => 3476,
                'MULTITASK' => 0,
                'SITE_ID' => 's1',
                'DECLINE_REASON' => null,
                'FORKED_BY_TEMPLATE_ID' => null,
                'ZOMBIE' => 0,
                'DEADLINE_COUNTED' => 0,
                'MATCH_WORK_TIME' => 0,
                'STAGE_ID' => 122,
                'IS_REGULAR' => 0
            ],
            [
                'ID' => 10150,
                'TITLE' => 'Покупка автокада для перспективы плюс',
                'DESCRIPTION' => 'На данный момент есть проблемы с автокадом в виду отсутствия лицензии. Компания готова купить себе лицензии. Предварительно нужно уточнить информацию о том , какая конкретно она нужна , под какие цели. Так же необходима лицензия на спдс. ( плагин для автокада) Продажу и консультацию нужно осуществлять через Елену Курятову.',
                'DESCRIPTION_IN_BBCODE' => 1,
                'PRIORITY' => 1,
                'STATUS' => 5,
                'RESPONSIBLE_ID' => 2,
                'DATE_START' => '2019-10-03 05:01:12',
                'DURATION_PLAN' => 0,
                'DURATION_FACT' => null,
                'DURATION_TYPE' => 'days',
                'TIME_ESTIMATE' => 0,
                'REPLICATE' => 0,
                'DEADLINE' => '2019-10-17 13:00:00',
                'START_DATE_PLAN' => null,
                'END_DATE_PLAN' => null,
                'CREATED_BY' => 1,
                'CREATED_DATE' => '2019-10-03 04:41:38',
                'CHANGED_BY' => 1,
                'CHANGED_DATE' => '2019-11-01 04:16:48',
                'STATUS_CHANGED_BY' => 2,
                'STATUS_CHANGED_DATE' => '2019-11-01 04:16:47',
                'CLOSED_BY' => 2,
                'CLOSED_DATE' => '2019-11-01 04:16:47',
                'ACTIVITY_DATE' => '2019-11-01 04:16:48',
                'GUID' => '{74a0f9ac-cc23-4ed4-b2f4-0e376333b260}',
                'XML_ID' => null,
                'EXCHANGE_ID' => null,
                'EXCHANGE_MODIFIED' => null,
                'OUTLOOK_VERSION' => '8',
                'MARK' => null,
                'ALLOW_CHANGE_DEADLINE' => 0,
                'ALLOW_TIME_TRACKING' => 0,
                'TASK_CONTROL' => 0,
                'ADD_IN_REPORT' => 0,
                'GROUP_ID' => 12,
                'PARENT_ID' => null,
                'FORUM_TOPIC_ID' => 3600,
                'MULTITASK' => 0,
                'SITE_ID' => 's1',
                'DECLINE_REASON' => null,
                'FORKED_BY_TEMPLATE_ID' => null,
                'ZOMBIE' => 0,
                'DEADLINE_COUNTED' => 0,
                'MATCH_WORK_TIME' => 0,
                'STAGE_ID' => 0,
                'IS_REGULAR' => 0
            ],
            [
                'ID' => 11922,
                'TITLE' => 'забрать товар в СДЕК',
                'DESCRIPTION' => null,
                'DESCRIPTION_IN_BBCODE' => 1,
                'PRIORITY' => 1,
                'STATUS' => 5,
                'RESPONSIBLE_ID' => 3,
                'DATE_START' => null,
                'DURATION_PLAN' => null,
                'DURATION_FACT' => null,
                'DURATION_TYPE' => 'days',
                'TIME_ESTIMATE' => 0,
                'REPLICATE' => 0,
                'DEADLINE' => null,
                'START_DATE_PLAN' => null,
                'END_DATE_PLAN' => null,
                'CREATED_BY' => 3,
                'CREATED_DATE' => '2019-12-17 06:02:26',
                'CHANGED_BY' => 3,
                'CHANGED_DATE' => '2019-12-17 10:44:52',
                'STATUS_CHANGED_BY' => 3,
                'STATUS_CHANGED_DATE' => '2019-12-17 10:44:52',
                'CLOSED_BY' => 3,
                'CLOSED_DATE' => '2019-12-17 10:44:52',
                'ACTIVITY_DATE' => '2019-12-17 10:44:52',
                'GUID' => '{87b1d9d8-af9b-4580-9c7f-7176445edbf6}',
                'XML_ID' => null,
                'EXCHANGE_ID' => null,
                'EXCHANGE_MODIFIED' => null,
                'OUTLOOK_VERSION' => '2',
                'MARK' => null,
                'ALLOW_CHANGE_DEADLINE' => 0,
                'ALLOW_TIME_TRACKING' => 0,
                'TASK_CONTROL' => 0,
                'ADD_IN_REPORT' => 0,
                'GROUP_ID' => 12,
                'PARENT_ID' => null,
                'FORUM_TOPIC_ID' => null,
                'MULTITASK' => 0,
                'SITE_ID' => 's1',
                'DECLINE_REASON' => null,
                'FORKED_BY_TEMPLATE_ID' => null,
                'ZOMBIE' => 0,
                'DEADLINE_COUNTED' => 0,
                'MATCH_WORK_TIME' => 0,
                'STAGE_ID' => 120,
                'IS_REGULAR' => 0
            ],
            [
                'ID' => 11924,
                'TITLE' => 'Забрать товар в Киты',
                'DESCRIPTION' => null,
                'DESCRIPTION_IN_BBCODE' => 1,
                'PRIORITY' => 1,
                'STATUS' => 5,
                'RESPONSIBLE_ID' => 3,
                'DATE_START' => null,
                'DURATION_PLAN' => null,
                'DURATION_FACT' => null,
                'DURATION_TYPE' => 'days',
                'TIME_ESTIMATE' => 0,
                'REPLICATE' => 0,
                'DEADLINE' => null,
                'START_DATE_PLAN' => null,
                'END_DATE_PLAN' => null,
                'CREATED_BY' => 3,
                'CREATED_DATE' => '2019-12-17 06:02:38',
                'CHANGED_BY' => 3,
                'CHANGED_DATE' => '2019-12-17 10:44:56',
                'STATUS_CHANGED_BY' => 3,
                'STATUS_CHANGED_DATE' => '2019-12-17 10:44:56',
                'CLOSED_BY' => 3,
                'CLOSED_DATE' => '2019-12-17 10:44:56',
                'ACTIVITY_DATE' => '2019-12-17 10:44:56',
                'GUID' => '{0e71ea2b-9107-4da5-9b12-1f6e6b81ceb7}',
                'XML_ID' => null,
                'EXCHANGE_ID' => null,
                'EXCHANGE_MODIFIED' => null,
                'OUTLOOK_VERSION' => '2',
                'MARK' => null,
                'ALLOW_CHANGE_DEADLINE' => 0,
                'ALLOW_TIME_TRACKING' => 0,
                'TASK_CONTROL' => 0,
                'ADD_IN_REPORT' => 0,
                'GROUP_ID' => 12,
                'PARENT_ID' => null,
                'FORUM_TOPIC_ID' => null,
                'MULTITASK' => 0,
                'SITE_ID' => 's1',
                'DECLINE_REASON' => null,
                'FORKED_BY_TEMPLATE_ID' => null,
                'ZOMBIE' => 0,
                'DEADLINE_COUNTED' => 0,
                'MATCH_WORK_TIME' => 0,
                'STAGE_ID' => 120,
                'IS_REGULAR' => 0
            ],
            [
                'ID' => 11926,
                'TITLE' => 'Созвон с Олхой',
                'DESCRIPTION' => null,
                'DESCRIPTION_IN_BBCODE' => 1,
                'PRIORITY' => 1,
                'STATUS' => 5,
                'RESPONSIBLE_ID' => 3,
                'DATE_START' => null,
                'DURATION_PLAN' => null,
                'DURATION_FACT' => null,
                'DURATION_TYPE' => 'days',
                'TIME_ESTIMATE' => 0,
                'REPLICATE' => 0,
                'DEADLINE' => null,
                'START_DATE_PLAN' => null,
                'END_DATE_PLAN' => null,
                'CREATED_BY' => 3,
                'CREATED_DATE' => '2019-12-17 06:03:00',
                'CHANGED_BY' => 3,
                'CHANGED_DATE' => '2020-01-15 07:17:26',
                'STATUS_CHANGED_BY' => 3,
                'STATUS_CHANGED_DATE' => '2020-01-15 07:17:26',
                'CLOSED_BY' => 3,
                'CLOSED_DATE' => '2020-01-15 07:17:26',
                'ACTIVITY_DATE' => '2020-01-15 07:17:26',
                'GUID' => '{cf169bb2-f7f6-4f1c-b1d1-72637aa1d052}',
                'XML_ID' => null,
                'EXCHANGE_ID' => null,
                'EXCHANGE_MODIFIED' => null,
                'OUTLOOK_VERSION' => '2',
                'MARK' => null,
                'ALLOW_CHANGE_DEADLINE' => 0,
                'ALLOW_TIME_TRACKING' => 0,
                'TASK_CONTROL' => 0,
                'ADD_IN_REPORT' => 0,
                'GROUP_ID' => 12,
                'PARENT_ID' => null,
                'FORUM_TOPIC_ID' => null,
                'MULTITASK' => 0,
                'SITE_ID' => 's1',
                'DECLINE_REASON' => null,
                'FORKED_BY_TEMPLATE_ID' => null,
                'ZOMBIE' => 0,
                'DEADLINE_COUNTED' => 0,
                'MATCH_WORK_TIME' => 0,
                'STAGE_ID' => 120,
                'IS_REGULAR' => 0
            ],
            [
                'ID' => 11938,
                'TITLE' => 'забрать сетевой фильтр и установить в МК',
                'DESCRIPTION' => null,
                'DESCRIPTION_IN_BBCODE' => 1,
                'PRIORITY' => 1,
                'STATUS' => 5,
                'RESPONSIBLE_ID' => 3,
                'DATE_START' => null,
                'DURATION_PLAN' => null,
                'DURATION_FACT' => null,
                'DURATION_TYPE' => 'days',
                'TIME_ESTIMATE' => 0,
                'REPLICATE' => 0,
                'DEADLINE' => null,
                'START_DATE_PLAN' => null,
                'END_DATE_PLAN' => null,
                'CREATED_BY' => 3,
                'CREATED_DATE' => '2019-12-17 07:34:55',
                'CHANGED_BY' => 3,
                'CHANGED_DATE' => '2019-12-17 10:44:46',
                'STATUS_CHANGED_BY' => 3,
                'STATUS_CHANGED_DATE' => '2019-12-17 10:44:46',
                'CLOSED_BY' => 3,
                'CLOSED_DATE' => '2019-12-17 10:44:46',
                'ACTIVITY_DATE' => '2019-12-17 10:44:46',
                'GUID' => '{4b575db9-7b2f-44c1-bf34-5abec6a3a8bc}',
                'XML_ID' => null,
                'EXCHANGE_ID' => null,
                'EXCHANGE_MODIFIED' => null,
                'OUTLOOK_VERSION' => '2',
                'MARK' => null,
                'ALLOW_CHANGE_DEADLINE' => 0,
                'ALLOW_TIME_TRACKING' => 0,
                'TASK_CONTROL' => 0,
                'ADD_IN_REPORT' => 0,
                'GROUP_ID' => 12,
                'PARENT_ID' => null,
                'FORUM_TOPIC_ID' => null,
                'MULTITASK' => 0,
                'SITE_ID' => 's1',
                'DECLINE_REASON' => null,
                'FORKED_BY_TEMPLATE_ID' => null,
                'ZOMBIE' => 0,
                'DEADLINE_COUNTED' => 0,
                'MATCH_WORK_TIME' => 0,
                'STAGE_ID' => 120,
                'IS_REGULAR' => 0
            ],
            [
                'ID' => 11974,
                'TITLE' => 'коллибри поменять рег',
                'DESCRIPTION' => null,
                'DESCRIPTION_IN_BBCODE' => 1,
                'PRIORITY' => 1,
                'STATUS' => 5,
                'RESPONSIBLE_ID' => 3,
                'DATE_START' => null,
                'DURATION_PLAN' => null,
                'DURATION_FACT' => null,
                'DURATION_TYPE' => 'days',
                'TIME_ESTIMATE' => 0,
                'REPLICATE' => 0,
                'DEADLINE' => null,
                'START_DATE_PLAN' => null,
                'END_DATE_PLAN' => null,
                'CREATED_BY' => 3,
                'CREATED_DATE' => '2019-12-18 07:20:45',
                'CHANGED_BY' => 3,
                'CHANGED_DATE' => '2020-03-10 04:19:14',
                'STATUS_CHANGED_BY' => 3,
                'STATUS_CHANGED_DATE' => '2020-03-10 04:19:14',
                'CLOSED_BY' => 3,
                'CLOSED_DATE' => '2020-03-10 04:19:14',
                'ACTIVITY_DATE' => '2020-03-10 04:19:14',
                'GUID' => '{12e191e1-1e65-46ac-ac2d-dcd06b657224}',
                'XML_ID' => null,
                'EXCHANGE_ID' => null,
                'EXCHANGE_MODIFIED' => null,
                'OUTLOOK_VERSION' => '2',
                'MARK' => null,
                'ALLOW_CHANGE_DEADLINE' => 0,
                'ALLOW_TIME_TRACKING' => 0,
                'TASK_CONTROL' => 0,
                'ADD_IN_REPORT' => 0,
                'GROUP_ID' => 12,
                'PARENT_ID' => null,
                'FORUM_TOPIC_ID' => null,
                'MULTITASK' => 0,
                'SITE_ID' => 's1',
                'DECLINE_REASON' => null,
                'FORKED_BY_TEMPLATE_ID' => null,
                'ZOMBIE' => 0,
                'DEADLINE_COUNTED' => 0,
                'MATCH_WORK_TIME' => 0,
                'STAGE_ID' => 120,
                'IS_REGULAR' => 0
            ]
        ];

        $ffTasks = [
            [
                "person" => "Иван Иванович Иванов",
                "person_phone" => "89999999999",
                "b_task_id" => 9706,
                "user_creator_id" => 1,
                "problem_type_id" => 2,
            ],
            [
                "person" => "Иван Иванович Иванов",
                "person_phone" => "89999999999",
                "b_task_id" => 10150,
                "user_creator_id" => 1,
                "problem_type_id" => 3,
            ],
            [
                "person" => "Иван Иванович Иванов",
                "person_phone" => "89999999999",
                "b_task_id" => 11922,
                "user_creator_id" => 1,
                "problem_type_id" => 4,
            ],
        ];
        foreach ($ffTasks as $key => $value) {
            $ffTasks[$key]['created_at'] = Carbon::now()->subWeek()->addDays($key)->toDateTimeString();
        }

        if (!Schema::hasTable('b_user') && config('app.debug')) {
            DB::unprepared(file_get_contents(public_path('/sql/b_tables_create.sql')));
            BUser::insert($b_user);
            BSonetGroup::insert($b_sonet_group);
            BTask::insert($b_tasks);
            BCrmCompany::insert($b_crm_company);
            BIblock::insert($b_iblock);
            BIblockElement::insert($b_iblock_element);
            BIblockProperty::insert($b_iblock_property);
            BIblockElementProperty::insert($b_iblock_element_property);
        }

        Task::insert($ffTasks);
    }
}
