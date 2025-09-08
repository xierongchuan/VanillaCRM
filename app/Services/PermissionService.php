<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use App\Models\Permission;

class PermissionService
{
    /**
     * Проверяет, имеет ли должность указанное разрешение
     *
     * @param Post $post
     * @param string $permissionValue
     * @return bool
     */
    public function postHasPermission(Post $post, string $permissionValue): bool
    {
        // Получаем разрешение по его значению
        $permission = Permission::where('value', $permissionValue)->first();

        if (!$permission) {
            return false;
        }

        // Декодируем JSON с разрешениями
        $permissionIds = (array) json_decode($post->permission, true);

        // Проверяем, содержится ли ID разрешения в массиве
        return in_array($permission->id, $permissionIds, true);
    }

    /**
     * Получает пользователей с указанной должностью
     *
     * @param string $permissionValue
     * @param int $departmentId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersWithPermission(string $permissionValue, int $departmentId)
    {
        // Получаем разрешение по его значению
        $permission = Permission::where('value', $permissionValue)->first();

        if (!$permission) {
            return collect(); // Возвращаем пустую коллекцию, если разрешение не найдено
        }

        // Получаем пользователей с активным статусом из указанного отдела,
        // у которых должность имеет указанное разрешение
        return \App\Models\User::where('dep_id', $departmentId)
            ->where('status', 'active')
            ->whereHas('post', function ($query) use ($permission) {
                $query->whereRaw("JSON_SEARCH(permission, 'one', ?) IS NOT NULL", [$permission->id]);
            })
            ->get();
    }
}
