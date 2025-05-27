<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActualLoad;



class ActualLoadSeeder extends Seeder
{
    public function run()
    {
        ActualLoad::truncate();

        // Создадим 30 фактических нагрузок
        ActualLoad::factory()->count(30)->create();
    }
}