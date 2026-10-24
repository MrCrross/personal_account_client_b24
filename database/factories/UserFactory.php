<?php

namespace Database\Factories;

use App\Http\Modules\Settings\Enums\ProfileThemeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            // ID группы/компании в Битриксе — произвольные; нужные значения передавать явно
            'b_group_id' => fake()->numberBetween(1, 99999),
            'b_company_id' => fake()->numberBetween(1, 99999),
            'theme' => ProfileThemeEnum::Light,
            'remember_token' => Str::random(10),
        ];
    }
}
