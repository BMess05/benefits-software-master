<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SystemConfigRequest;

class DashboardController extends Controller
{
    public function casesListing()
    {
        $per_page = config('constants.per_page');
        $paginate = true;
        $employees = resolve('employee')->getAllActive($paginate, $per_page);
        // echo "<pre>";
        // print_r($employees->toArray());
        // die;
        return view('admin.dashboard.cases', [
            'cases' => $employees
        ]);
    }

    public function advisors_listing()
    {
        $per_page = 30;
        // $per_page = config('constants.per_page');
        $paginate = false;
        $advisors = resolve('advisor')->getAll($paginate, $per_page);

        return view('admin.dashboard.advisors', [
            'advisors' => $advisors,
            'page_title' => 'Advisors'
        ]);
    }
    public function disclaimers_listing()
    {
        $disclaimers = resolve('advisor')->getAllDisclaimers();
        // echo "<pre>"; print_r($disclaimers); exit;
        return view('admin.dashboard.disclaimers', [
            'disclaimers' => $disclaimers
        ]);
    }
    public function configurations()
    {
        $configs = [
            'csrs_cola' => 'CSRSCola',
            'fers_cola' => 'FERSCola',
            'sal_increase_default' => 'SalaryIncreaseDefault',
            'sal_increase' => 'SalaryIncrease',
            'sal_increase_1' => 'SalaryIncrease1',
            'sal_increase_2' => 'SalaryIncrease2',
            'ss_cola' => 'SSCola',
            'pia_formula' => 'PIAFormula',
            'income_limit' => 'SSEarnedIncomeLimit',
            'avg_premium_inc' => 'FEHBAveragePremiumIncrease',
            'deferral_limit' => 'TSPDeferralLimit',
            'catchup_limit' => 'TSPCatchUpLimit',
            'gfund_return' => 'TSPGFundReturn',
            'ffund_return' => 'TSPFFundReturn',
            'cfund_return' => 'TSPCFundReturn',
            'sfund_return' => 'TSPSFundReturn',
            'ifund_return' => 'TSPIFundReturn',
            'daily_benefit_amount' => 'daily_benefit_amount',
            'benefit_period' => 'benefit_period',
            'waiting_period' => 'waiting_period',
            'inflation_protection' => 'inflation_protection',
            'total_allowed_contri_for_age_less_than_50' => 'total_allowed_contri_for_age_less_than_50',
            'total_allowed_contri_for_age_50_or_greater' => 'total_allowed_contri_for_age_50_or_greater',
            'year_of_contribution' => 'year_of_contribution',
            'WhileWorkingBasicCostPer1000AgeLessThan35' => 'WhileWorkingBasicCostPer1000AgeLessThan35',
            'WhileWorkingBasicCostPer1000Age35To39' => 'WhileWorkingBasicCostPer1000Age35To39',
            'WhileWorkingBasicCostPer1000Age40To44' => 'WhileWorkingBasicCostPer1000Age40To44',
            'WhileWorkingBasicCostPer1000Age45To49' => 'WhileWorkingBasicCostPer1000Age45To49',
            'WhileWorkingBasicCostPer1000Age50To54' => 'WhileWorkingBasicCostPer1000Age50To54',
            'WhileWorkingBasicCostPer1000Age55To59' => 'WhileWorkingBasicCostPer1000Age55To59',
            'WhileWorkingBasicCostPer1000Age60To64' => 'WhileWorkingBasicCostPer1000Age60To64',
            'WhileWorkingBasicCostPer1000Age65To69' => 'WhileWorkingBasicCostPer1000Age65To69',
            'WhileWorkingBasicCostPer1000Age70To74' => 'WhileWorkingBasicCostPer1000Age70To74',
            'WhileWorkingBasicCostPer1000Age75To79' => 'WhileWorkingBasicCostPer1000Age75To79',
            'WhileWorkingBasicCostPer1000Age80orGreater' => 'WhileWorkingBasicCostPer1000Age80orGreater',
            'WhileWorkingOptionACostPer1000AgeLessThan35' => 'WhileWorkingOptionACostPer1000AgeLessThan35',
            'WhileWorkingOptionACostPer1000Age35To39' => 'WhileWorkingOptionACostPer1000Age35To39',
            'WhileWorkingOptionACostPer1000Age40To44' => 'WhileWorkingOptionACostPer1000Age40To44',
            'WhileWorkingOptionACostPer1000Age45To49' => 'WhileWorkingOptionACostPer1000Age45To49',
            'WhileWorkingOptionACostPer1000Age50To54' => 'WhileWorkingOptionACostPer1000Age50To54',
            'WhileWorkingOptionACostPer1000Age55To59' => 'WhileWorkingOptionACostPer1000Age55To59',
            'WhileWorkingOptionACostPer1000Age60To64' => 'WhileWorkingOptionACostPer1000Age60To64',
            'WhileWorkingOptionACostPer1000Age65To69' => 'WhileWorkingOptionACostPer1000Age65To69',
            'WhileWorkingOptionACostPer1000Age70To74' => 'WhileWorkingOptionACostPer1000Age70To74',
            'WhileWorkingOptionACostPer1000Age75To79' => 'WhileWorkingOptionACostPer1000Age75To79',
            'WhileWorkingOptionACostPer1000Age80orGreater' => 'WhileWorkingOptionACostPer1000Age80orGreater',
            'WhileWorkingOptionBCostPer1000AgeLessThan35' => 'WhileWorkingOptionBCostPer1000AgeLessThan35',
            'WhileWorkingOptionBCostPer1000Age35To39' => 'WhileWorkingOptionBCostPer1000Age35To39',
            'WhileWorkingOptionBCostPer1000Age40To44' => 'WhileWorkingOptionBCostPer1000Age40To44',
            'WhileWorkingOptionBCostPer1000Age45To49' => 'WhileWorkingOptionBCostPer1000Age45To49',
            'WhileWorkingOptionBCostPer1000Age50To54' => 'WhileWorkingOptionBCostPer1000Age50To54',
            'WhileWorkingOptionBCostPer1000Age55To59' => 'WhileWorkingOptionBCostPer1000Age55To59',
            'WhileWorkingOptionBCostPer1000Age60To64' => 'WhileWorkingOptionBCostPer1000Age60To64',
            'WhileWorkingOptionBCostPer1000Age65To69' => 'WhileWorkingOptionBCostPer1000Age65To69',
            'WhileWorkingOptionBCostPer1000Age70To74' => 'WhileWorkingOptionBCostPer1000Age70To74',
            'WhileWorkingOptionBCostPer1000Age75To79' => 'WhileWorkingOptionBCostPer1000Age75To79',
            'WhileWorkingOptionBCostPer1000Age80orGreater' => 'WhileWorkingOptionBCostPer1000Age80orGreater',
            'WhileWorkingOptionCCostPer1000AgeLessThan35' => 'WhileWorkingOptionCCostPer1000AgeLessThan35',
            'WhileWorkingOptionCCostPer1000Age35To39' => 'WhileWorkingOptionCCostPer1000Age35To39',
            'WhileWorkingOptionCCostPer1000Age40To44' => 'WhileWorkingOptionCCostPer1000Age40To44',
            'WhileWorkingOptionCCostPer1000Age45To49' => 'WhileWorkingOptionCCostPer1000Age45To49',
            'WhileWorkingOptionCCostPer1000Age50To54' => 'WhileWorkingOptionCCostPer1000Age50To54',
            'WhileWorkingOptionCCostPer1000Age55To59' => 'WhileWorkingOptionCCostPer1000Age55To59',
            'WhileWorkingOptionCCostPer1000Age60To64' => 'WhileWorkingOptionCCostPer1000Age60To64',
            'WhileWorkingOptionCCostPer1000Age65To69' => 'WhileWorkingOptionCCostPer1000Age65To69',
            'WhileWorkingOptionCCostPer1000Age70To74' => 'WhileWorkingOptionCCostPer1000Age70To74',
            'WhileWorkingOptionCCostPer1000Age75To79' => 'WhileWorkingOptionCCostPer1000Age75To79',
            'WhileWorkingOptionCCostPer1000Age80orGreater' => 'WhileWorkingOptionCCostPer1000Age80orGreater',
            'InRetirementBasicCostPer1000Age50To54NoReduction' => 'InRetirementBasicCostPer1000Age50To54NoReduction',
            'InRetirementBasicCostPer1000Age50To54Reduction50' => 'InRetirementBasicCostPer1000Age50To54Reduction50',
            'InRetirementBasicCostPer1000Age50To54Reduction75' => 'InRetirementBasicCostPer1000Age50To54Reduction75',
            'InRetirementBasicCostPer1000Age55To59NoReduction' => 'InRetirementBasicCostPer1000Age55To59NoReduction',
            'InRetirementBasicCostPer1000Age55To59Reduction50' => 'InRetirementBasicCostPer1000Age55To59Reduction50',
            'InRetirementBasicCostPer1000Age55To59Reduction75' => 'InRetirementBasicCostPer1000Age55To59Reduction75',
            'InRetirementBasicCostPer1000Age60To64NoReduction' => 'InRetirementBasicCostPer1000Age60To64NoReduction',
            'InRetirementBasicCostPer1000Age60To64Reduction50' => 'InRetirementBasicCostPer1000Age60To64Reduction50',
            'InRetirementBasicCostPer1000Age60To64Reduction75' => 'InRetirementBasicCostPer1000Age60To64Reduction75',
            'InRetirementBasicCostPer1000Age65To69NoReduction' => 'InRetirementBasicCostPer1000Age65To69NoReduction',
            'InRetirementBasicCostPer1000Age65To69Reduction50' => 'InRetirementBasicCostPer1000Age65To69Reduction50',
            'InRetirementOptionACostPer1000Age50To54Reduction75' => 'InRetirementOptionACostPer1000Age50To54Reduction75',
            'InRetirementOptionACostPer1000Age55To59Reduction75' => 'InRetirementOptionACostPer1000Age55To59Reduction75',
            'InRetirementOptionACostPer1000Age60To64Reduction75' => 'InRetirementOptionACostPer1000Age60To64Reduction75',
            'InRetirementOptionBCostPer1000Age50To54FullReduction' => 'InRetirementOptionBCostPer1000Age50To54FullReduction',
            'InRetirementOptionBCostPer1000Age55To59FullReduction' => 'InRetirementOptionBCostPer1000Age55To59FullReduction',
            'InRetirementOptionBCostPer1000Age60To64FullReduction' => 'InRetirementOptionBCostPer1000Age60To64FullReduction',
            'InRetirementOptionBCostPer1000Age50To54NoReduction' => 'InRetirementOptionBCostPer1000Age50To54NoReduction',
            'InRetirementOptionBCostPer1000Age55To59NoReduction' => 'InRetirementOptionBCostPer1000Age55To59NoReduction',
            'InRetirementOptionBCostPer1000Age60To64NoReduction' => 'InRetirementOptionBCostPer1000Age60To64NoReduction',
            'InRetirementOptionBCostPer1000Age65To69NoReduction' => 'InRetirementOptionBCostPer1000Age65To69NoReduction',
            'InRetirementOptionBCostPer1000Age70To74NoReduction' => 'InRetirementOptionBCostPer1000Age70To74NoReduction',
            'InRetirementOptionBCostPer1000Age75To79NoReduction' => 'InRetirementOptionBCostPer1000Age75To79NoReduction',
            'InRetirementOptionBCostPer1000Age80NoReduction' => 'InRetirementOptionBCostPer1000Age80NoReduction',
            'InRetirementOptionCCostPer1000Age50To54FullReduction' => 'InRetirementOptionCCostPer1000Age50To54FullReduction',
            'InRetirementOptionCCostPer1000Age55To59FullReduction' => 'InRetirementOptionCCostPer1000Age55To59FullReduction',
            'InRetirementOptionCCostPer1000Age60To64FullReduction' => 'InRetirementOptionCCostPer1000Age60To64FullReduction',
            'InRetirementOptionCCostPer1000Age50To54NoReduction' => 'InRetirementOptionCCostPer1000Age50To54NoReduction',
            'InRetirementOptionCCostPer1000Age55To59NoReduction' => 'InRetirementOptionCCostPer1000Age55To59NoReduction',
            'InRetirementOptionCCostPer1000Age60To64NoReduction' => 'InRetirementOptionCCostPer1000Age60To64NoReduction',
            'InRetirementOptionCCostPer1000Age65To69NoReduction' => 'InRetirementOptionCCostPer1000Age65To69NoReduction',
            'InRetirementOptionCCostPer1000Age70To74NoReduction' => 'InRetirementOptionCCostPer1000Age70To74NoReduction',
            'InRetirementOptionCCostPer1000Age75To79NoReduction' => 'InRetirementOptionCCostPer1000Age75To79NoReduction',
            'InRetirementOptionCCostPer1000Age80NoReduction' => 'InRetirementOptionCCostPer1000Age80NoReduction',
            'InRetirementBasicCostPer1000Age70To74NoReduction' => 'InRetirementBasicCostPer1000Age70To74NoReduction',
            'InRetirementBasicCostPer1000Age70To74Reduction50' => 'InRetirementBasicCostPer1000Age70To74Reduction50',
            'InRetirementBasicCostPer1000Age75To79NoReduction' => 'InRetirementBasicCostPer1000Age75To79NoReduction',
            'InRetirementBasicCostPer1000Age75To79Reduction50' => 'InRetirementBasicCostPer1000Age75To79Reduction50',
            'InRetirementBasicCostPer1000Age80NoReduction' => 'InRetirementBasicCostPer1000Age80NoReduction',
            'InRetirementBasicCostPer1000Age80Reduction50' => 'InRetirementBasicCostPer1000Age80Reduction50',
        ];
        foreach ($configs as $key => $conf) {
            $data[$key] = resolve('employee')->getSystemConfigurations($conf);
        }

        return view('admin.dashboard.configuration', [
            'data' => $data
        ]);
    }

    public function saveConfigs(SystemConfigRequest $request)
    {
        $data = $request->all();

        $result = resolve('employee')->saveSystemConfig($data);
        if ($result) {
            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'System Configurations updated successfully'
            ]);
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function searchCases(Request $request)
    {
        $data = $request->all();
        $caseId = ($data['case_id']) ?? '';
        $empName = ($data['emp_name']) ?? '';
        $advisorName = ($data['advisor_name']) ?? '';
        $res = resolve('employee')->searchCases($caseId, $empName, $advisorName);

        $view = \View::make('admin.dashboard.case_search', [
            'cases' => $res
        ]);

        return $view;
    }
}
