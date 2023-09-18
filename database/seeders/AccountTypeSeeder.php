<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $account_types = [
            [ 'name' => 'スーパーユーザー' ],
            [ 'name' => '塾スタッフ' ],
            [ 'name' => '先生' ],
            [ 'name' => '生徒' ],
        ];
        foreach ($account_types as $account_type) {
            DB::table('account_types')->insert([
                'name' => $account_type['name'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
        }
    }
}
