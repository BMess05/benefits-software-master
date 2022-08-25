<?php

use Illuminate\Database\Seeder;
use App\Models\Advisor;

class AdvisorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $default_disclaimer = resolve('advisor')->getDefaultDisclaimer();
        $dd_id = $default_disclaimer['DisclaimerId'];
        $file_path = public_path('csv/advisors_test.csv');
        try {
            $file = fopen($file_path, 'r');
        } catch (\Exception $e) {
            echo "File not found: " . $e->getMessage();
            die;
        }
        $count = 0;
        while (($line = fgetcsv($file)) !== FALSE) {
            if ($count == 0) {
                $count++;
                continue;
            }
            $advisor = new Advisor();
            $advisor->AdvisorName = $line[0] . " " . $line[1];
            $advisor->AdvisorAddress = $line[4];
            $advisor->DefaultDisclaimerId = $dd_id;
            $advisor->IsActive = 1;
            $advisor->SuppressConfidential = 0;
            $advisor->company_name = $line[2];
            $advisor->workshop_code = $line[3];
            if ($advisor->save()) {
                continue;
            }
        }
        fclose($file);
    }
}
