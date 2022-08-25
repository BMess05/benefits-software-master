<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddEmployeeType extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('AppLookup')->insert([
            'AppLookupTypeName' => 'EmployeeType',
            'AppLookupName' => 'eCBPO',
            'AppLookupDescription' => 'ENHANCED CUSTOMS AND BORDER PROTECTION OFFICERS (eCBPO)',
            'DisplayOrder' => 3,
            'IsActive' => 1
        ]);
    }
}
