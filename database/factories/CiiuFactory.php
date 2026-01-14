<?php

namespace Database\Factories;

use App\Models\Ciiu;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Company;

class CiiuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ciiu::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid(),
            'company_id' => Company::COMPANY_CCXC,
            'code' => '0111',
            'name' => 'Cultivo de cereales (excepto arroz), legumbres y semillas oleaginosas.',
            'ciiu_id' => 2,
            'is_main' => true
        ];
    }
}
