<?php

use Illuminate\Database\Seeder;
use App\Models\TspCalculation;

class TspcalculateTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tspCalculation = Array (Array(
                    'id' => '1',
                    'year' => '2020',
                    'g' => '68.3',
                    'f' => '6.06',
                    'c' => '13.27',
                    's' => '3.39',
                    'i' => '8.98',
                ),Array(
                    'id'=> '2',
                    'year' => '2030',
                    'g' => '32.9',
                    'f' => '6.98',
                    'c' => '30.5',
                    's' => '8.58',
                    'i' => '21.04',
                ),Array(
                    'id'=> '3',
                    'year' => '2040',
                    'g' => '20.62',
                    'f' => '7.63',
                    'c' => '35.87',
                    's' => '10.77',
                    'i' => '25.11',
                ),Array(
                    'id'=> '4',
                    'year' => '2050',
                    'g' => '10.52',
                    'f' => '7.73',
                    'c' => '40.09',
                    's' => '13.05',
                    'i' => '28.61',
                ),Array(
                    'id'=> '5',
                    'year' => 'L INCOME',
                    'g' => '72.84',
                    'f' => '5.91',
                    'c' => '11.05',
                    's' => '2.76',
                    'i' => '7.44',
                )
            );

        foreach ($tspCalculation as $key => $value) {

            $res=TspCalculation::insert([
                    'year' => $value['year'],
                    'g' => $value['g'],
                    'f' => $value['f'],
                    'c' => $value['c'],
                    's' => $value['s'],
                    'i' => $value['i'],
                ]);
        }
        //
    }
}
