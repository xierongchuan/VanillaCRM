<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permission;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'permission',
    ];

    /**
     * Проверяет, имеет ли должность указанное разрешение
     *
     * @param string $permissionValue
     * @return bool
     */
    public function hasPermission(string $permissionValue): bool
    {
        // Получаем разрешение по его значению
        $permission = Permission::where('value', $permissionValue)->first();

        if (!$permission) {
            return false;
        }

        // Декодируем JSON с разрешениями
        $permissionIds = (array) json_decode($this->permission, true);

        // Проверяем, содержится ли ID разрешения в массиве
        return in_array($permission->id, $permissionIds, true);
    }
}
