<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [ 'name' => '山田一郎' ],
            [ 'name' => '山田二郎' ],
            [ 'name' => '山田三郎' ],
            [ 'name' => '山田四郎' ],
            [ 'name' => '山田五郎' ],
        ];
        foreach ($students as $student) {
            DB::table('students')->insert([
                'name' => $student['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
