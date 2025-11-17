<?php

namespace Database\Seeders;

use App\Models\Todo;
use App\Priority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TodoSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar tarefas de exemplo com diferentes prioridades e status
        Todo::factory()
            ->count(5)
            ->urgent()
            ->pending()
            ->create();

        Todo::factory()
            ->count(10)
            ->medium()
            ->pending()
            ->create();

        Todo::factory()
            ->count(15)
            ->simple()
            ->pending()
            ->create();

        // Criar algumas tarefas concluídas
        Todo::factory()
            ->count(8)
            ->completed()
            ->create();

        // Criar algumas tarefas com datas específicas
        Todo::factory()
            ->count(5)
            ->pending()
            ->withDate(now()->addDays(rand(1, 30))->format('Y-m-d'))
            ->create();

        $this->command->info('Todos criados com sucesso!');
    }
}
