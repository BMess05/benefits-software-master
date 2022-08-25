<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddRetirementTypeDeferred extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('AppLookup')->insert([
            'AppLookupTypeName' => 'RetirementType',
            'AppLookupName' => 'Deferred',
            'AppLookupDescription' => 'New retirement type',
            'DisplayOrder' => 3,
            'IsActive' => 1
        ]);
    }
}
