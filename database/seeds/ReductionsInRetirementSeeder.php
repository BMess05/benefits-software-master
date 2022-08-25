<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReductionsInRetirementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rows = [
            [
                'AppLookupTypeName' => 'ReductionAfterRetirement',
                'AppLookupName' => 'BasicReduction',
                'AppLookupDescription' => '0',
                'DisplayOrder' => 1,
                'IsActive' => 1
            ],
            [
                'AppLookupTypeName' => 'ReductionAfterRetirement',
                'AppLookupName' => 'BasicReduction',
                'AppLookupDescription' => '50',
                'DisplayOrder' => 2,
                'IsActive' => 1
            ],
            [
                'AppLookupTypeName' => 'ReductionAfterRetirement',
                'AppLookupName' => 'BasicReduction',
                'AppLookupDescription' => '75',
                'DisplayOrder' => 3,
                'IsActive' => 1
            ],
            [
                'AppLookupTypeName' => 'ReductionAfterRetirement',
                'AppLookupName' => 'OptionAReduction',
                'AppLookupDescription' => '75',
                'DisplayOrder' => 1,
                'IsActive' => 1
            ],
            [
                'AppLookupTypeName' => 'ReductionAfterRetirement',
                'AppLookupName' => 'OptionBReduction',
                'AppLookupDescription' => '0',
                'DisplayOrder' => 1,
                'IsActive' => 1
            ],
            [
                'AppLookupTypeName' => 'ReductionAfterRetirement',
                'AppLookupName' => 'OptionBReduction',
                'AppLookupDescription' => '100',
                'DisplayOrder' => 2,
                'IsActive' => 1
            ],
            [
                'AppLookupTypeName' => 'ReductionAfterRetirement',
                'AppLookupName' => 'OptionCReduction',
                'AppLookupDescription' => '0',
                'DisplayOrder' => 1,
                'IsActive' => 1
            ],
            [
                'AppLookupTypeName' => 'ReductionAfterRetirement',
                'AppLookupName' => 'OptionCReduction',
                'AppLookupDescription' => '100',
                'DisplayOrder' => 2,
                'IsActive' => 1
            ]
        ];
        foreach($rows as $row) {
            DB::table('AppLookup')->insert($row);
        }
        
    }
}
