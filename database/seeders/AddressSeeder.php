<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->each(function (User $user): void {
            $addresses = Address::factory()->count(fake()->numberBetween(1, 3))->create([
                'user_id' => $user->id,
            ]);
            $addresses->first()->update(['is_default' => true]);
        });
    }
}
