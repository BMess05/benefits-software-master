<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class AddLookupValues extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('AppLookup')->insert([
            'AppLookupTypeName' => 'SystemType',
            'AppLookupName' => 'CSRS Offset',
            'AppLookupDescription' => '',
            'DisplayOrder' => 4,
            'IsActive' => 1
        ]);
    }
}
