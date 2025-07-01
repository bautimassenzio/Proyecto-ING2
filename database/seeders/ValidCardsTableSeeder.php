<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValidCardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         DB::table('valid_cards')->insert([
            [
                'tipo' => 'Mastercard',
                'card_number' => '5031 7557 3453 0604',
                'expiry_month' => '11',
                'expiry_year' => '30',
                'cvv' => '123',
                'cardholder_name' => 'nombre1',
            ],
            [
                'tipo' => 'Visa',
                'card_number' => '4509 9535 6623 3704',
                'expiry_month' => '11',
                'expiry_year' => '30',
                'cvv' => '123',
                'cardholder_name' => 'nombre2',
            ],
        ]);
    }
}
