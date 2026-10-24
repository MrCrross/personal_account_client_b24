<?php

namespace App\Http\Modules\Users\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

class UsersRepository
{
    public static function getList(
        ?string $search,
        ?int $roleId,
        ?int $companyId,
        ?string $dateStart,
        ?string $dateEnd,
        int $perPage = 15
    ): Paginator {
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
            ->with(['bGroup', 'bCompany', 'roles'])
            ->when(! empty($search), function ($query) use ($search) {
                $like = '%'.addcslashes($search, '%_\\').'%';

                return $query->where(function ($q) use ($like) {
                    $q->where('id', 'LIKE', $like)
                        ->orWhere('name', 'LIKE', $like)
                        ->orWhere('email', 'LIKE', $like);
                });
            })
            ->when(! empty($roleId), function ($query) use ($roleId) {
                return $query->whereHas('roles', function ($q) use ($roleId) {
                    $q->whereKey($roleId);
                });
            })
            ->when(! empty($companyId), function ($query) use ($companyId) {
                return $query->where('b_company_id', '=', $companyId);
            })
            ->when(! empty($dateStart), function ($query) use ($dateStart) {
                return $query->whereDate('created_at', '>=', $dateStart);
            })
            ->when(! empty($dateEnd), function ($query) use ($dateEnd) {
                return $query->whereDate('created_at', '<=', $dateEnd);
            })
            ->orderBy('id')
            ->simplePaginate($perPage);
    }

    public static function getById(
        string $userId
    ): ?User {
        return User::query()
            ->where('id', '=', $userId)
            ->first();
    }

    public static function getByEmail(
        string $email
    ): ?User {
        return User::query()
            ->where('email', '=', $email)
            ->first();
    }

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

    public static function update(
        int $userId,
        array $fields
    ): void {
        User::query()
            ->where('id', '=', $userId)
            ->update($fields);
    }
}
