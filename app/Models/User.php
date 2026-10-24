<?php

namespace App\Models;

use App\Http\Modules\Settings\Enums\ProfileThemeEnum;
use App\Models\Bitrix24\BSonetGroup;
use App\Models\Bitrix24\Crm\BCrmCompany;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasRoles;
    use Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'b_group_id',
        'b_company_id',
        'password',
        'telegram_chat_id',
        'theme',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $table = $this->getTable();
        $prefix = config('database.prefix', '');

        // Если префикс еще не добавлен - добавляем
        if ($prefix && ! str_starts_with($table, $prefix)) {
            $this->table = $prefix.$table;
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'theme' => ProfileThemeEnum::class,
        ];
    }

    public function getJWTIdentifier(): int
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function getPermissionsNamesViaRoles(): array
    {
        $permissionsNames = [];
        $roles = Role::with(['users', 'permissions'])
            ->whereHas('users', function ($query) {
                $query->where('id', '=', $this->getKey());
            })
            ->get();
        foreach ($roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissionsNames[] = $permission->name;
            }
        }

        return $permissionsNames;
    }

    public function bGroup(): BelongsTo
    {
        return $this->belongsTo(
            BSonetGroup::class,
            'b_group_id',
            'ID'
        );
    }

    public function bCompany(): BelongsTo
    {
        return $this->belongsTo(
            BCrmCompany::class,
            'b_company_id',
            'ID'
        );
    }
}
