<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Trabalho',
                'Pessoal',
                'Estudos',
                'Saúde',
                'Finanças',
                'Projetos',
                'Ideias',
                'Compras',
            ]),
            'color' => $this->faker->hexColor(),
            'icon' => $this->faker->randomElement(['tag', 'folder', 'star', 'heart', 'bolt', 'book', 'briefcase', 'home']),
            'description' => $this->faker->optional(0.5)->sentence(),
        ];
    }
}
