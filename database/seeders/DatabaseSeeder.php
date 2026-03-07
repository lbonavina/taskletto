<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Criando tarefas de exemplo...');

        // Tarefas variadas
        Task::factory(10)->pending()->create();
        Task::factory(5)->completed()->create();
        Task::factory(3)->urgent()->create();
        Task::factory(4)->overdue()->create();

        $this->command->info('✅ 22 tarefas criadas com sucesso!');
    }
}
