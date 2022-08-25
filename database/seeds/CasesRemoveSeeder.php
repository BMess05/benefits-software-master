<?php

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\FEGLIDependent;
use App\Models\Eligibility;
use App\Models\EmployeeConfig;
use App\Models\EmployeeFile;
use App\Models\Fegli;
use App\Models\MilitaryService;
use App\Models\NonDeductionService;
use App\Models\PartTimeService;
use App\Models\RefundedService;
use App\Models\ReportScenario;
use App\Models\Tsp;
use Illuminate\Support\Facades\DB;

class CasesRemoveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $res = Employee::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        FEGLIDependent::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        Eligibility::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        EmployeeConfig::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        EmployeeFile::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        Fegli::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        MilitaryService::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        NonDeductionService::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        PartTimeService::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        RefundedService::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        ReportScenario::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        Tsp::whereNotIn('EmployeeId', ['18617', '18618', '18619'])->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
