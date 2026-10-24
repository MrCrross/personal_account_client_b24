<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Локальные заглушки нативных таблиц Bitrix24 (b_*), которые проект читает напрямую
 * через модели app/Models/Bitrix24/**. Нужны только для разработки без доступа к БД портала.
 *
 * На бою таблицы уже есть (их создаёт сам Bitrix) — поэтому каждая создаётся только при
 * отсутствии, а в production/testing миграция пропускается (shouldRun). Набор колонок — минимально
 * необходимый коду и TaskTestSeeder, а не полная схема Bitrix.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->createIfMissing('b_user', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('LOGIN', 50)->default('');
            $table->string('NAME', 50)->nullable();
            $table->string('LAST_NAME', 50)->nullable();
            $table->string('SECOND_NAME', 50)->nullable();
            $table->string('EMAIL')->nullable();
            $table->char('ACTIVE', 1)->default('Y');
            $table->dateTime('DATE_REGISTER')->nullable();
        });

        $this->createIfMissing('b_sonet_group', function (Blueprint $table) {
            $table->increments('ID');
            $table->char('SITE_ID', 2)->default('s1');
            $table->string('NAME');
            $table->text('DESCRIPTION')->nullable();
            $table->dateTime('DATE_CREATE')->nullable();
            $table->char('ACTIVE', 1)->default('Y');
            $table->char('VISIBLE', 1)->default('Y');
            $table->char('OPENED', 1)->default('N');
            $table->char('CLOSED', 1)->default('N');
            $table->unsignedInteger('OWNER_ID')->nullable();
        });

        $this->createIfMissing('b_crm_company', function (Blueprint $table) {
            $table->increments('ID');
            $table->dateTime('DATE_CREATE')->nullable();
            $table->string('TITLE')->nullable();
            $table->mediumText('SEARCH_CONTENT')->nullable();
            $table->char('OPENED', 1)->default('N');
            $table->unsignedInteger('ASSIGNED_BY_ID')->nullable();
        });

        $this->createIfMissing('b_crm_contact', function (Blueprint $table) {
            $table->increments('ID');
            $table->dateTime('DATE_CREATE')->nullable();
            $table->string('NAME', 50)->nullable();
            $table->string('LAST_NAME', 50)->nullable();
            $table->string('SECOND_NAME', 50)->nullable();
            $table->unsignedInteger('COMPANY_ID')->nullable();
            $table->mediumText('SEARCH_CONTENT')->nullable();
            $table->char('OPENED', 1)->default('N');
            $table->unsignedInteger('ASSIGNED_BY_ID')->nullable();
        });

        $this->createIfMissing('b_iblock', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('IBLOCK_TYPE_ID', 50)->nullable();
            $table->string('CODE', 50)->nullable();
            $table->string('NAME');
            $table->char('ACTIVE', 1)->default('Y');
        });

        $this->createIfMissing('b_iblock_property', function (Blueprint $table) {
            $table->increments('ID');
            $table->unsignedInteger('IBLOCK_ID')->index();
            $table->string('NAME');
            $table->string('CODE', 50)->nullable();
            $table->char('PROPERTY_TYPE', 1)->default('S');
            $table->string('USER_TYPE')->nullable();
            $table->char('ACTIVE', 1)->default('Y');
        });

        $this->createIfMissing('b_iblock_element', function (Blueprint $table) {
            $table->increments('ID');
            $table->unsignedInteger('IBLOCK_ID')->index();
            $table->string('NAME')->default('');
            $table->char('ACTIVE', 1)->default('Y');
            $table->dateTime('DATE_CREATE')->nullable();
        });

        $this->createIfMissing('b_iblock_element_property', function (Blueprint $table) {
            $table->increments('ID');
            $table->unsignedInteger('IBLOCK_ELEMENT_ID')->index();
            $table->unsignedInteger('IBLOCK_PROPERTY_ID')->index();
            $table->text('VALUE');
        });

        $this->createIfMissing('b_tasks', function (Blueprint $table) {
            $table->increments('ID');
            $table->string('TITLE')->nullable();
            $table->mediumText('DESCRIPTION')->nullable();
            $table->char('DESCRIPTION_IN_BBCODE', 1)->default('Y');
            $table->char('PRIORITY', 1)->default('1');
            $table->char('STATUS', 1)->nullable();
            $table->unsignedInteger('RESPONSIBLE_ID')->nullable();
            $table->dateTime('DATE_START')->nullable();
            $table->integer('DURATION_PLAN')->nullable();
            $table->integer('DURATION_FACT')->nullable();
            $table->string('DURATION_TYPE', 5)->default('days');
            $table->integer('TIME_ESTIMATE')->default(0);
            $table->char('REPLICATE', 1)->default('N');
            $table->dateTime('DEADLINE')->nullable();
            $table->dateTime('START_DATE_PLAN')->nullable();
            $table->dateTime('END_DATE_PLAN')->nullable();
            $table->unsignedInteger('CREATED_BY')->nullable();
            $table->dateTime('CREATED_DATE')->nullable();
            $table->unsignedInteger('CHANGED_BY')->nullable();
            $table->dateTime('CHANGED_DATE')->nullable();
            $table->unsignedInteger('STATUS_CHANGED_BY')->nullable();
            $table->dateTime('STATUS_CHANGED_DATE')->nullable();
            $table->unsignedInteger('CLOSED_BY')->nullable();
            $table->dateTime('CLOSED_DATE')->nullable();
            $table->dateTime('ACTIVITY_DATE')->nullable();
            $table->string('GUID', 50)->nullable();
            $table->string('XML_ID', 200)->nullable();
            $table->string('EXCHANGE_ID', 196)->nullable();
            $table->string('EXCHANGE_MODIFIED', 196)->nullable();
            $table->integer('OUTLOOK_VERSION')->default(1);
            $table->char('MARK', 1)->nullable();
            $table->char('ALLOW_CHANGE_DEADLINE', 1)->default('N');
            $table->char('ALLOW_TIME_TRACKING', 1)->default('N');
            $table->char('TASK_CONTROL', 1)->default('N');
            $table->char('ADD_IN_REPORT', 1)->default('N');
            $table->unsignedInteger('GROUP_ID')->nullable()->index();
            $table->unsignedInteger('PARENT_ID')->nullable();
            $table->unsignedInteger('FORUM_TOPIC_ID')->nullable();
            $table->char('MULTITASK', 1)->default('N');
            $table->char('SITE_ID', 2)->default('s1');
            $table->text('DECLINE_REASON')->nullable();
            $table->unsignedInteger('FORKED_BY_TEMPLATE_ID')->nullable();
            $table->char('ZOMBIE', 1)->default('N');
            $table->integer('DEADLINE_COUNTED')->default(0);
            $table->char('MATCH_WORK_TIME', 1)->default('N');
            $table->unsignedInteger('STAGE_ID')->default(0);
            $table->char('IS_REGULAR', 1)->default('N');
        });

        $this->createIfMissing('b_tasks_result', function (Blueprint $table) {
            $table->increments('ID');
            $table->unsignedInteger('TASK_ID')->index();
            $table->unsignedInteger('COMMENT_ID')->nullable();
            $table->unsignedInteger('CREATED_BY')->nullable();
            $table->dateTime('CREATED_AT')->nullable();
            $table->dateTime('UPDATED_AT')->nullable();
            $table->mediumText('TEXT')->nullable();
            $table->integer('STATUS')->default(0);
        });

        $this->createIfMissing('b_disk_attached_object', function (Blueprint $table) {
            $table->increments('ID');
            $table->unsignedInteger('OBJECT_ID');
            $table->string('MODULE_ID', 32)->default('tasks');
            $table->string('ENTITY_TYPE', 100);
            $table->unsignedInteger('ENTITY_ID');
            $table->dateTime('CREATE_TIME')->nullable();
            $table->unsignedInteger('CREATED_BY')->nullable();
            $table->index(['ENTITY_ID', 'ENTITY_TYPE']);
        });

        // Ниже — таблицы, которые код не читает, а только пишет в них через REST.
        // Нужны драйверу B24_DRIVER=database (App\Http\Wrappers\BLocalRest).

        $this->createIfMissing('b_disk_object', function (Blueprint $table) {
            // ID файлов стартуют с миллиона, чтобы не пересекаться с ID b_disk_attached_object:
            // BDiskService::getAttachedObject пробует ID сначала как attached object, потом как файл
            $table->increments('ID')->startingValue(1000000);
            $table->unsignedInteger('STORAGE_ID')->nullable();
            $table->string('NAME');
            $table->unsignedTinyInteger('TYPE')->default(3);
            $table->unsignedBigInteger('SIZE')->default(0);
            $table->dateTime('CREATE_TIME')->nullable();
            $table->unsignedInteger('CREATED_BY')->nullable();
        });

        $this->createIfMissing('b_forum_message', function (Blueprint $table) {
            $table->increments('ID');
            // Связь с задачей в формате Bitrix: TASK_<ID задачи>
            $table->string('XML_ID')->nullable()->index();
            $table->unsignedInteger('AUTHOR_ID')->nullable();
            $table->mediumText('POST_MESSAGE');
            $table->dateTime('POST_DATE')->nullable();
        });
    }

    public function down(): void
    {
        // Ничего не удаляем: если локальная БД — копия боевой, b_* в ней настоящие таблицы
        // портала. up() идемпотентен, поэтому повторный migrate после отката безопасен.
    }

    public function shouldRun(): bool
    {
        return ! app()->environment(['production', 'testing']);
    }

    private function createIfMissing(string $table, Closure $callback): void
    {
        if (! Schema::hasTable($table)) {
            Schema::create($table, $callback);
        }
    }
};
