<?php

use Illuminate\Database\Seeder;
use App\Models\HistoryColumn;

class HistoryColumnsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'column_name' => 'EmployeeName',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ], [
                'column_name' => 'AdvisorId',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ], [
                'column_name' => 'DateReceived',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ], [
                'column_name' => 'DueDate',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ], [
                'column_name' => 'DateCompleted',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ],
            [
                'column_name' => 'ReportDate',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ],
            [
                'column_name' => 'EmployeeAddress',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ],
            [
                'column_name' => 'SystemTypeId',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ],
            [
                'column_name' => 'RetirementTypeId',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ],
            [
                'column_name' => 'EmployeeTypeId',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ],
            [
                'column_name' => 'PostalEmployee',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ],
            [
                'column_name' => 'MaritalStatusTypeId',
                'table_name' => 'Employee',
                'tab_name' => 'basic_info'
            ],
            [
                'column_name' => 'DateOfBirth',
                'table_name' => 'Eligibility',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'LeaveSCD',
                'table_name' => 'Eligibility',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'RetirementDate',
                'table_name' => 'Eligibility',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'FromDate',
                'table_name' => 'MilitaryService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'ToDate',
                'table_name' => 'MilitaryService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'IsRetired',
                'table_name' => 'MilitaryService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'DepositOwed',
                'table_name' => 'MilitaryService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'AmountOwed',
                'table_name' => 'MilitaryService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'FromDate',
                'table_name' => 'NonDeductionService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'ToDate',
                'table_name' => 'NonDeductionService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'DepositOwed',
                'table_name' => 'NonDeductionService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'AmountOwed',
                'table_name' => 'NonDeductionService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'FromDate',
                'table_name' => 'RefundedService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'ToDate',
                'table_name' => 'RefundedService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'Withdrawal',
                'table_name' => 'RefundedService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'Redeposit',
                'table_name' => 'RefundedService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'AmountOwed',
                'table_name' => 'RefundedService',
                'tab_name' => 'retirement_eligibility'
            ],
            [
                'column_name' => 'FromDate',
                'table_name' => 'PartTimeService',
                'tab_name' => 'part_time_service'
            ],
            [
                'column_name' => 'ToDate',
                'table_name' => 'PartTimeService',
                'tab_name' => 'part_time_service'
            ],
            [
                'column_name' => 'HoursWeek',
                'table_name' => 'PartTimeService',
                'tab_name' => 'part_time_service'
            ],
            [
                'column_name' => 'percentage',
                'table_name' => 'PartTimeService',
                'tab_name' => 'part_time_service'
            ],
            [
                'column_name' => 'CurrentSalary',
                'table_name' => 'Employee',
                'tab_name' => 'pay_and_leave'
            ],
            [
                'column_name' => 'SalaryIncreaseDefault',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'pay_and_leave'
            ],
            [
                'column_name' => 'High3Average',
                'table_name' => 'Employee',
                'tab_name' => 'pay_and_leave'
            ],
            [
                'column_name' => 'UnusedSickLeave',
                'table_name' => 'Employee',
                'tab_name' => 'pay_and_leave'
            ],
            [
                'column_name' => 'UnusedAnnualLeave',
                'table_name' => 'Employee',
                'tab_name' => 'pay_and_leave'
            ],
            [
                'column_name' => 'CurrentSalary',
                'table_name' => 'Employee',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'SalaryIncreaseDefault',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'StatementDate',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'ContributionRegular',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'ContributionCatchUp',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'loan_balance_general',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'loan_repayment_general',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'payoff_date_general',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'loan_balance_residential',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'loan_repayment_residential',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'payoff_date_residential',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'GFund',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'FFund',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'CFund',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'SFund',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'IFund',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2025',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2030',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2035',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2040',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2045',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2050',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2055',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2060',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2065',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'LIncome',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'GFundDist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'FFundDist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'CFundDist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'SFundDist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'IFundDist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2025Dist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2030Dist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2035Dist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2040Dist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2045Dist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2050Dist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2055Dist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2060Dist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'L2065Dist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'LIncomeDist',
                'table_name' => 'TSP',
                'tab_name' => 'tsp'
            ],
            [
                'column_name' => 'SalaryForFEGLI',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'DoesNotMeetFiveYear',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'BasicInc',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'BasicAmount',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'basicReductionAfterRetirement',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'OptionAInc',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'OptionAAmount',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'optionAReductionAfterRetirement',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'OptionBInc',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'OptionBMultiplier',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'OptionBAmount',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'optionBReductionAfterRetirement',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'OptionCInc',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'OptionCMultiplier',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'OptionCAmount',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'optionCReductionAfterRetirement',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'TotalAmount',
                'table_name' => 'FEGLI',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'DateOfBirth',
                'table_name' => 'FEGLIDependent',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'age',
                'table_name' => 'FEGLIDependent',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'CoverAfter22',
                'table_name' => 'FEGLIDependent',
                'tab_name' => 'fegli'
            ],
            [
                'column_name' => 'HealthPremium',
                'table_name' => 'Employee',
                'tab_name' => 'health_benefits'
            ],
            [
                'column_name' => 'DentalPremium',
                'table_name' => 'Employee',
                'tab_name' => 'health_benefits'
            ],
            [
                'column_name' => 'VisionPremium',
                'table_name' => 'Employee',
                'tab_name' => 'health_benefits'
            ],
            [
                'column_name' => 'dental_and_vision',
                'table_name' => 'Employee',
                'tab_name' => 'health_benefits'
            ],
            [
                'column_name' => 'DoesNotMeetFiveYear',
                'table_name' => 'Employee',
                'tab_name' => 'health_benefits'
            ],
            [
                'column_name' => 'FLTCIPPremium',
                'table_name' => 'Employee',
                'tab_name' => 'fltcip'
            ],
            [
                'column_name' => 'SSMonthlyAt62',
                'table_name' => 'Employee',
                'tab_name' => 'social_security'
            ],
            [
                'column_name' => 'SSStartAge_year',
                'table_name' => 'Employee',
                'tab_name' => 'social_security'
            ],
            [
                'column_name' => 'SSStartAge_month',
                'table_name' => 'Employee',
                'tab_name' => 'social_security'
            ],
            [
                'column_name' => 'SSMonthlyAtStartAge',
                'table_name' => 'Employee',
                'tab_name' => 'social_security'
            ],
            [
                'column_name' => 'SSYearsEarning',
                'table_name' => 'Employee',
                'tab_name' => 'social_security'
            ],
            [
                'column_name' => 'CSRSCola',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'FERSCola',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'SalaryIncreaseDefault',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'SSCola',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'PIAFormula',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'SSEarnedIncomeLimit',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'FEHBAveragePremiumIncrease',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'TSPDeferralLimit',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'TSPCatchUpLimit',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'TSPGFundReturn',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'TSPFFundReturn',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'TSPCFundReturn',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'TSPSFundReturn',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ],
            [
                'column_name' => 'TSPIFundReturn',
                'table_name' => 'EmployeeConfig',
                'tab_name' => 'configuration'
            ]
        ];
        foreach ($data as $row) {
            $res = HistoryColumn::create($row);
        }
    }
}
