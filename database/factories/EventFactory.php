<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titleEvent = $this->faker->sentence;
        $descriptionEvent = fake()->paragraphs(asText: true);
        $dateEvent = fake()->dateTimeBetween('now', '+10 year');


        return [
            'titleEvent' => $titleEvent,
            'slugEvent'  => Str::slug($titleEvent),
            'descriptionEvent' => $descriptionEvent,
            'dateEvent' => $dateEvent,


        ];

    }

}
