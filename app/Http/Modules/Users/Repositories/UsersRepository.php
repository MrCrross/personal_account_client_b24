<?php

namespace App\Http\Modules\Users\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

class UsersRepository
{
    /**
     * @return Paginator
     */
    public static function getList(): Paginator
    {
        return User::query()
            ->select(
                'id',
                'name',
                'email',
                'b_group_id',
                'b_company_id',
                'telegram_chat_id',
                'created_at',
            )
            ->with(['bGroup', 'bCompany','roles'])
            ->orderBy('id')
            ->simplePaginate();
    }

    /**
     * @param string $userId
     * @return User|null
     */
    public static function getById(
        string $userId
    ): ?User {
        return User::query()
            ->where('id', '=', $userId)
            ->first();
    }

    /**
     * @param string $email
     * @return User|null
     */
    public static function getByEmail(
        string $email
    ): ?User {
        return User::query()
            ->where('email', '=', $email)
            ->first();
    }

    /**
     * @param string $tgChatId
     * @return User|null
     */
    public static function getByTgChatId(
        string $tgChatId
    ): ?User {
        return User::query()
            ->where('telegram_chat_id', '=', $tgChatId)
            ->first();
    }

    public static function store(array $fields): User
    {
        return User::query()->create($fields);
    }

    /**
     * @param int $userId
     * @param array $fields
     * @return void
     */
    public static function update(
        int $userId,
        array $fields
    ): void {
        User::query()
            ->where('id', '=', $userId)
            ->update($fields);
    }
}
