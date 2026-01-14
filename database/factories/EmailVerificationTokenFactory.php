<?php

namespace Database\Factories;

use App\Models\EmailVerificationToken;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class EmailVerificationTokenFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EmailVerificationToken::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $code = (string) random_int(100000, 999999);

        return [
            'email' => $this->faker->email(),
            'token' => Hash::make($code),
            'created_at' => now()
        ];

    }
}
