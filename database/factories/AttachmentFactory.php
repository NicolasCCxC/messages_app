<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Company;
use Faker\Provider\Uuid;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attachment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id' => Uuid::uuid(),
            'name' => $this->faker->name,
            'bucket_id' => Uuid::uuid(),
            'company_id' => Company::inRandomOrder()->first(),
            'preview_url' => $this->faker->url(),
            'supporting_document_preview_url' => null
        ];
    }
}
