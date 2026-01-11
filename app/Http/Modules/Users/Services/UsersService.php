<?php

namespace App\Http\Modules\Users\Services;

use App\Http\Modules\Bitrix\Repositories\BCrmCompanyRepository;
use App\Http\Modules\Roles\Repositories\RolesRepository;
use App\Http\Modules\Users\Repositories\UsersRepository;
use App\Http\Modules\Users\Resources\UserResource;
use App\Http\Modules\Users\Resources\UsersPaginateCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersService
{
    public function getList (
        Request $request
    ): UsersPaginateCollection {
        return new UsersPaginateCollection(UsersRepository::getList());
    }

    public function store(Request $request): UserResource
    {
        $fields = [
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'b_group_id' => $request->post('b_group_id'),
            'b_company_id' => $request->post('b_company_id'),
            'telegram_chat_id' => $request->post('telegram_chat_id'),
            'password' => Hash::make($request->post('password')),
        ];
        $user = UsersRepository::store($fields);

        if ($request->has('roles') && $request->filled('roles')) {
            $roles = RolesRepository::getByIds($request->post('roles'));
            $user->syncRoles($roles);
        }

        return new UserResource($user);
    }

    public function update(int $userId, Request $request): UserResource
    {
        $fields = [
            'name' => $request->post('name'),
            'email' => $request->post('email'),
            'b_group_id' => $request->post('b_group_id'),
            'telegram_chat_id' => $request->post('telegram_chat_id'),
        ];
        if ($request->has('password')) {
            $fields['password'] = Hash::make($request->post('password'));
        }

        UsersRepository::update($userId, $fields);
        $user = UsersRepository::getById($userId);

        if ($request->has('roles')) {
            if (is_array($request->post('roles'))) {
                $roles = RolesRepository::getByIds($request->post('roles'));
                $user->syncRoles($roles);
            } else {
                $user->syncRoles([]);
            }
        }

        return new UserResource($user);
    }
}
