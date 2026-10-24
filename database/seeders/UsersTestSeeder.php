<?php

namespace Database\Seeders;

use App\Models\Bitrix24\BSonetGroup;
use App\Models\Bitrix24\Crm\BCrmCompany;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

/**
 * Тестовые пользователи для проверки пагинации, поиска и фильтров в «Администрировании».
 * Только для разработки — вызывается из DatabaseSeeder при APP_DEBUG.
 */
class UsersTestSeeder extends Seeder
{
    private const COUNT = 120;

    private const EMAIL_DOMAIN = 'seed.test';

    public function run(): void
    {
        // Повторный запуск не плодит дубликаты
        if (User::query()->where('email', 'like', '%@'.self::EMAIL_DOMAIN)->exists()) {
            return;
        }

        // Роль без прав — чтобы было что выбирать в фильтре по роли, не раздавая доступ в админку
        $clientRole = Role::firstOrCreate(
            ['name' => 'client', 'guard_name' => 'web'],
            ['title' => 'Клиент']
        );

        // Группы и компании берём из локальных зеркал Bitrix (их наполняет TaskTestSeeder),
        // чтобы в таблице отображались названия и работал фильтр по компании
        $groupIds = $this->pluckIds(new BSonetGroup);
        $companyIds = $this->pluckIds(new BCrmCompany);

        for ($i = 1; $i <= self::COUNT; $i++) {
            $fields = [
                'email' => "user{$i}@".self::EMAIL_DOMAIN,
                'created_at' => fake()->dateTimeBetween('-1 year'),
            ];
            if ($groupIds) {
                $fields['b_group_id'] = fake()->randomElement($groupIds);
            }
            if ($companyIds) {
                $fields['b_company_id'] = fake()->randomElement($companyIds);
            }

            $user = User::factory()->create($fields);

            // Примерно у двух третей пользователей есть роль
            if ($i % 3 !== 0) {
                $user->assignRole($clientRole);
            }
        }
    }

    /**
     * @return array<int, int>
     */
    private function pluckIds(BSonetGroup|BCrmCompany $model): array
    {
        if (! Schema::hasTable($model->getTable())) {
            return [];
        }

        return $model->newQuery()->pluck('ID')->all();
    }
}
