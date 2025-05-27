<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlannedLoad;

class PlannedLoadSeeder extends Seeder
{
    public function run()
    {
        PlannedLoad::truncate();

        // Создадим 30 плановых нагрузок
        PlannedLoad::factory()->count(30)->create();
    }
}
