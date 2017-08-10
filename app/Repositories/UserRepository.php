<?php

namespace App\Repositories;



use App\Models\Role;
use App\Models\User;

class UserRepository
{
    public function moveUsers2Roles($userIds, $roleIds)
    {
        $users = User::findOrFail($userIds);
        $roleIds = Role::findOrFail($roleIds)->pluck('id');
        $users->each(
            function ($user) use ($roleIds) {
                $user->roles()->sync($roleIds);
            }
        );
    }
}
