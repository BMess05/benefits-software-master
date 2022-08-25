<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Advisor;
use PDF as TCPDF;
use App\Http\Requests\EmployeeBasicInfoRequest;
use App\Http\Requests\EmployeeAddRequest;
use App\Http\Requests\FltcipRequest;
use App\Http\Requests\HealthBenifitsRequest;
use App\Http\Requests\SocialSecurityRequest;
use App\Http\Requests\RetirementEligibilityRequest;
use App\Http\Requests\AddMilitaryServiceRequest;
use App\Http\Requests\NonDeductionServiceRequest;
use App\Http\Requests\RefundedServiceRequest;
use App\Http\Requests\ParttimeServiceRequest;
use App\Http\Requests\FegliRequest;
use App\Http\Requests\TspRequest;
use App\Http\Requests\SystemConfigRequest;
use App\Http\Requests\PayAndLeaveRequest;
use App\Http\Requests\AddDepartmentRequest;
use App\Http\Requests\DeductionRequest;
use App\Http\Requests\EmployeeFileRequest;
use App\Http\Requests\ChildRequest;
use App\Models\AppLookup;
use App\Models\EmployeeConfig;
use App\Models\Child;
use App\Http\Controllers\Admin\PDF;
use App\Http\Controllers\Admin\MYPDF;
use function GuzzleHttp\json_encode;
use Carbon\Carbon;
use View;
class EmployeeController extends Controller
{
    public function __construct(Request $request) {
        $has_notes = 0;
        $empId = $request->segment(count($request->segments()));
        if($empId != null) {
            $employee = resolve('employee')->getById($empId);
            if (!$employee) {
                $empId = $request->segment(count($request->segments()) - 1);
                $employee = resolve('employee')->getById($empId);
                if (!$employee) {
                    View::share ( 'has_notes', $has_notes );
                    return redirect()->back(); //->with(['status' => 'danger', 'message' => 'Invalid Employee Id.']);
                }
            }
            if(trim($employee->notes) != "") {
                $has_notes = 1;
            }
        }
        View::share ( 'has_notes', $has_notes );
    }
    public function listFiles($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee']);
        }
        $file_dir = config('constants.DOCUMENT_STORAGE_EMP_FILES_DIR') . $empId;
        $files = resolve('employee')->getEmployeeFiles($empId);
        // $path = \Storage::get($file_dir);
        // echo "<pre>"; print_r($path); exit;
        return view('admin.employee.employeeFilesListing', [
            'active_tab' => 'files',
            'empId' => $empId,
            'file_dir' => $file_dir,
            'files' => $files
        ]);
    }

    public function add_employeeFile($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with('error', 'Please select an Employee');
        }
        return view('admin.employee.add_employeeFile', [
            'active_tab' => 'files',
            'empId' => $empId
        ]);
    }

    public function saveEmployeeFile($empId = null, EmployeeFileRequest $request)
    {
        $data = $request->all();
        if (($empId != null) && !empty($data)) {
            $res = resolve('employee')->checkEmployeeExist($empId);
            if (!$res) {
                return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee']);
            }
            $image = $data["emp_file"];
            $originalName = $image->getClientOriginalName();
            $mimetype = $image->getClientMimeType();
            $fileSize = $image->getSize();

            $uniqueString = time() . '_' . uniqid(rand());
            $image_name = str_replace('-', '_', $empId . '_' . $uniqueString . '_' . $originalName);
            $image_name = str_replace(' ', '_', $image_name);
            $file_dir = config('constants.DOCUMENT_STORAGE_EMP_FILES_DIR') . $empId;
            // echo $file_dir; exit;
            if (!\Storage::exists($file_dir)) {
                \Storage::makeDirectory($file_dir, 0777);
            }
            $storagePath = \Storage::put($file_dir, $image);
            // echo $storagePath; exit;
            $file['EmployeeId'] = $empId;
            $file['StoredFileName'] = basename($storagePath);
            $file['OrigFileName'] = $originalName;
            $file['ContentType'] = $mimetype;
            $file['FileSize'] = $fileSize;
            // echo "<pre>"; print_r($file); exit;
            $result = resolve('employee')->saveEmpFile($file);
            if ($result) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Employee File added successfully'
                ]);
            }
        }

        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong, please try again'
        ]);
    }

    public function downloadFile($empId = null, $fileName = null)
    {
        if (($empId != null) && ($fileName != null)) {
            $file_dir = config('constants.DOCUMENT_STORAGE_EMP_FILES_DIR') . $empId;
            $path = storage_path('app' . $file_dir . '/' . $fileName);
            return response()->download($path);
        }
        return false;
    }

    public function basicInformation($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee']);
        }
        $employee = resolve('employee')->getById($empId);
        if (!$employee) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Employee Id.']); // Invalid employee ID
        }

        $employee = $employee->toArray();
        $data['systemTypes'] = resolve('applookup')->getByTypeName('SystemType')->toArray();
        $data['retirementTypes'] = resolve('applookup')->getByTypeName('RetirementType')->toArray();
        // dd($data['retirementTypes']);
        $data['employeeTypes'] = resolve('applookup')->getByTypeName('EmployeeType')->toArray();
        $data['otherEmployeeTypes'] = resolve('applookup')->getByTypeName('OtherEmployeeType')->toArray();
        $data['marital_statuses'] = resolve('applookup')->getByTypeName('MaritalStatusType')->toArray();
        $data['child'] = resolve('child')->getChildByParentId($empId);
        $data['advisors'] = Advisor::where('isActive', 1)->orderBy('workshop_code', 'ASC')->get();

        $months = config('constants.months');
        // echo "<pre>";
        // print_r($data['systemTypes']);
        // exit;

        return view('admin.employee.basic_information', [
            'active_tab' => 'basic_info',
            'months' => $months,
            'empId' => $empId,
            'employee' => $employee,
            'data' => $data
        ]);
    }

    public function updateBasicInfo($empId = null, EmployeeBasicInfoRequest $request)
    {
        if (($empId == null) || ($empId == '')) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']); // Invalid Employee Id
        }
        $data = $request->all();
        // echo "<pre>"; print_r($data); exit;
        unset($data['_token']);
        $result = resolve('employee')->updateBasicInfo($empId, $data);
        if ($result) {
            if ($data['next'] == 1) {
                return redirect()->route('retirementEligibility', $empId)->with(['status' => 'success', 'message' => 'Information updated successfully.']);
            }
            return redirect()->back()->with(['status' => 'success', 'message' => 'Information updated successfully']);
        }
        return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
    }

    public function retirement_eligibility($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee']);
        }
        $employee = resolve('employee')->getById($empId);
        if (!$employee) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Employee Id.']); // Invalid employee ID
        }

        $emp = $employee->toArray();
        $emp_conf = resolve('employee')->getEmployeeConf($empId);

        //echo "<pre>"; print_r($emp); exit;
        $leave_scd = isset($emp['eligibility']['LeaveSCD']) ? date('m/d/Y', strtotime($emp['eligibility']['LeaveSCD'])) : '';
        $eligibilitySCD = isset($emp['eligibility']['EligibilitySCD']) ? date('m/d/Y', strtotime($emp['eligibility']['EligibilitySCD'])) : '';
        $annuitySCD = isset($emp['eligibility']['AnnuitySCD']) ? date('m/d/Y', strtotime($emp['eligibility']['AnnuitySCD'])) : '';
        $minRetirementDate = isset($emp['eligibility']['MinRetirementDate']) ? date('m/d/Y', strtotime($emp['eligibility']['MinRetirementDate'])) : '';

        $minimumRetirementAge = resolve('employee')->getMinumumRetirementAge($empId);
        // echo "<pre>";
        // print_r($minimumRetirementAge);
        // exit;
        $service_at_planned_date = 'Not defined';
        $age_at_ret = 'Not defined';
        if (isset($emp['eligibility']['RetirementDate']) && isset($emp['eligibility']['LeaveSCD'])) {
            $leaveScd = new \DateTime($emp['eligibility']['LeaveSCD']);
            $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
            $bdayDate = new \DateTime($emp['eligibility']['DateOfBirth']);

            $bdayDate = $bdayDate->modify('- 1 day'); // The government considers someone to have turned their new age on the day before their actual birthday.
            // echo $bdayDate->format('Y-m-d');
            // die;
            $serviceObj = $retDate->diff($leaveScd);
            $retAgeObj = $retDate->diff($bdayDate);
            $service_at_planned_date = $serviceObj->y . ' years ' . $serviceObj->m . ' months';
            $age_at_ret = $retAgeObj->y . ' years ' . $retAgeObj->m . ' months';
        }

        $summary_arr = [
            'Report Date' => date('M, Y', strtotime($emp['ReportDate'])),
            'Leave SCD' => $leave_scd,
            'Eligibility SCD' => $eligibilitySCD,
            'Annuity SCD' => $annuitySCD,
            'Retirement Options' => $emp['retirementType'],
            'Earliest Eligible Date' => $minRetirementDate,
            'Service at Earliest Eligible Date' => '34 years, 4 months',
            'Age at Minimum Retirement Date' => $minimumRetirementAge['mra_year'] . ' years, ' . $minimumRetirementAge['mra_month'] . ' months',
            'Planned Retirement Date' => isset($emp['eligibility']['RetirementDate']) ? date('m/d/Y', strtotime($emp['eligibility']['RetirementDate'])) : '',
            'Service at Planned Date' => $service_at_planned_date,
            'Age at Planned Date' => $age_at_ret
        ];
        $data = resolve('employee')->getEligibilityById($empId);
        $activeMilitaryService = resolve('employee')->getMilitaryServicesByType($empId, 2);
        $reserveMilitaryService = resolve('employee')->getMilitaryServicesByType($empId, 3);
        $nonDeductionServices = resolve('employee')->getNonDeductionService($empId)->toArray();
        $refundedServices = resolve('employee')->getRefundedServices($empId)->toArray();

        return view('admin.employee.retirement_eligibility', [
            'active_tab' => 'retirement_eligibility',
            'summary_arr' => $summary_arr,
            'empId' => $empId,
            'data' => $data,
            'activeMilitaryService' => $activeMilitaryService,
            'reserveMilitaryService' => $reserveMilitaryService,
            'nonDeductionServices' => $nonDeductionServices,
            'refundedServices' => $refundedServices,
            'minimumRetirementAge' => $minimumRetirementAge
        ]);
    }

    public function retirementEligibilityUpdate($empId = null, RetirementEligibilityRequest $request)
    {
        if ($empId != null) {
            $data = $request->all();
            if (!empty($data)) {
                // echo "<pre>"; print_r($data); exit;
                unset($data['_token']);
                $result = resolve('employee')->retirementEligibilityUpdate($empId, $data);
                if ($result) {
                    if ($data['next'] == 1) {
                        return redirect()->route('partTimeService', $empId)->with(['status' => 'success', 'message' => 'Retirement eligibility details updated.']);
                    }
                    return redirect()->back()->with(['status' => 'success', 'message' => 'Retirement eligibility details updated.']);
                }
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function part_time_service($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with('error', 'Please select an Employee.');
        }
        $pTServices = resolve('employee')->getPartTimeServices($empId)->toArray();
        // echo "<pre>"; print_r($pTServices); exit;
        return view('admin.employee.parttime_service', [
            'active_tab' => 'parttime_service',
            'empId' => $empId,
            'pTServices' => $pTServices
        ]);
    }

    public function add_part_time_service($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with('error', 'Please select an Employee.');
        }
        return view('admin.employee.add_parttime_service', [
            'active_tab' => 'parttime_service',
            'empId' => $empId
        ]);
    }

    public function savePartTimeService($empId = null, ParttimeServiceRequest $request)
    {
        $data = $request->all();
        // echo "<pre>"; print_r($data); exit;
        unset($data['_token']);
        if (($empId != null) && !empty($data)) {
            $result = resolve('employee')->savePartTimeService($empId, $data);
            if ($result) {
                return redirect('/employee/parttime_service/' . $empId)->with([
                    'status' => 'success',
                    'message' => 'Part Time Service Added Successfully'
                ]);
            } else {
                return redirect()->back()->with([
                    'status' => 'danger',
                    'message' => 'Please add valid options.'
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function edit_part_time_service($empId = null, $serviceId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with('error', 'Please select an Employee.');
        }
        $service = resolve('employee')->getPartTimeService($serviceId)->toArray();
        // echo "<pre>";
        // print_r($service);
        // exit;
        return view('admin.employee.edit_parttime_service', [
            'active_tab' => 'parttime_service',
            'empId' => $empId,
            'service' => $service
        ]);
    }

    public function updatePartTimeService($serviceId = null, ParttimeServiceRequest $request)
    {
        $data = $request->all();
        // echo "<pre>"; print_r($data); exit;
        if (($serviceId != null) && !empty($data)) {
            unset($data['_token']);
            $result = resolve('employee')->updatePartTimeService($serviceId, $data);
            if ($result) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Part Time service updated successfully'
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function deletePartTimeService($serviceId = null)
    {
        $res = resolve('employee')->deletePartTimeService($serviceId);
        if ($res) {
            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Part Time service deleted successfully.'
            ]);
        } else {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Something went wrong, please try again.'
            ]);
        }
    }

    public function pay_and_leave($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Please select an Employee'
            ]);
        }
        $employee = resolve('employee')->getById($empId);
        if (!$employee) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Please select an valid Employee'
            ]);
        }
        $emp = $employee->toArray();
        // echo "<pre>"; print_r($emp); exit;
        if ($emp['eligibility'] == null || empty($emp['eligibility'])) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Please add Eligibility details'
            ]);
        }
        $emp_conf = resolve('employee')->getEmployeeConf($empId);

        $data['RetirementType'] = $emp['RetirementType'];
        $data['current_sal'] = round($emp['InitialSalary'], 2);
        $data['annualIncrease'] = round($emp_conf['SalaryIncreaseDefault'], 2);
        $data['high3_avg'] = round($emp['High3Average'], 2);
        $data['unused_sick_leave'] = round($emp['UnusedSickLeave'], 2);
        $data['unused_annual_leave'] = round($emp['UnusedAnnualLeave'], 2);
        if (!empty($emp['deduction'])) {
            foreach ($emp['deduction'] as $deduction) {
                if ($deduction['DeductionName'] == "CSRS/FERS Contribution") {
                    $data['csrs_fers_contri'] = round($deduction['DeductionAmount'], 2);
                } elseif ($deduction['DeductionName'] == "Social Security (OASDI)") {
                    $data['ss_oasdi'] = round($deduction['DeductionAmount'], 2);
                } elseif ($deduction['DeductionName'] == "Medicare") {
                    $data['medicare'] = round($deduction['DeductionAmount'], 2);
                } elseif ($deduction['DeductionName'] == "Tax (Federal)") {
                    $data['tax_fedral'] = round($deduction['DeductionAmount'], 2);
                } elseif ($deduction['DeductionName'] == "Tax (State)") {
                    $data['tax_state'] = round($deduction['DeductionAmount'], 2);
                } elseif ($deduction['DeductionName'] == "Flexible Spending Account") {
                    $data['flexible_spending_account'] = round($deduction['DeductionAmount'], 2);
                }
            }
        } else {
            $data['csrs_fers_contri'] = 0;
            $data['ss_oasdi'] = 0;
            $data['medicare'] = 0;
            $data['tax_fedral'] = 0;
            $data['tax_state'] = 0;
            $data['flexible_spending_account'] = 0;
        }

        $otherDeductions = resolve('employee')->getEmployeeOtherDeduction($empId);

        $projectedHigh3Average = resolve('employee')->calcProjectedHigh3Average($empId);
        // echo "<pre>";
        // print_r($projectedHigh3Average);
        // exit;
        return view('admin.employee.pay_and_leave', [
            'active_tab' => 'pay_and_leave',
            'empId' => $empId,
            'data' => $data,
            'otherDeductions' => $otherDeductions,
            'projectedHigh3Average' => $projectedHigh3Average
        ]);
    }

    public function payAndLeaveUpdate($empId = null, PayAndLeaveRequest $request)
    {
        $data = $request->all();
        // echo "<pre>"; print_r($data); exit;
        unset($data['_token']);
        $result = resolve('employee')->updatePayAndLeave($empId, $data);
        if ($result) {
            if ($data['next'] == 1) {
                return redirect()->route('socialSecurityEdit', $empId)->with([
                    'status' => 'success',
                    'message' => 'Pay and Leave updated successfully'
                ]);
            }
            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Pay and Leave updated successfully'
            ]);
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function tsp_edit($empId = null)
    {
        /**
         * Amount 1 = Traditional amount
         * Amount 2 = Roth Amount
         */
        if ($empId == null) {
            return redirect()->back()->with('error', 'Please select an employee.');
        }
        $emp = resolve('employee')->getById($empId);
        if ($emp->InitialSalary == 0) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Please update employee salary first.'
            ]);
        }
        // dd($emp->eligibility->DateOfBirth);
        $current_sal = $emp->InitialSalary;
        $emp_conf = resolve('employee')->getEmployeeConf($empId);
        $tsp_conf = resolve('employee')->getEmpTspConfigurations($empId);
        $tsp = resolve('employee')->getEmpTspDetails($empId);

        // dd($tsp);

        $tsp['new_balance'] = resolve('employee')->getTSPNewBalance_new($empId);
        $tsp['total_contribution_amount'] = 0;
        if (isset($tsp['ContributionRegular']) && isset($tsp['ContributionCatchUp'])) {
            $tsp['total_contribution_amount'] = $tsp['ContributionRegular'] + $tsp['ContributionCatchUp'];
        }

        if (isset($tsp['StatementDate']) && ($tsp['StatementDate'] != NULL) && ($tsp['payoff_date_general'] == NULL)) {
            if ($tsp['loan_balance_general'] != 0.00 && $tsp['loan_repayment_general'] != 0.00) {
                $payoff_date_general_years = (string) round(($tsp['loan_balance_general'] / $tsp['loan_repayment_general']) / 26, 1);
                $g_arr = explode('.', $payoff_date_general_years);
                $payoff_year = $g_arr[0] ?? 0;
                $payoff_month = $g_arr[1] ?? 0;
                // echo "<pre>"; print_r($g_arr); exit;
                $statement_date = new \DateTime($tsp['StatementDate']);
                $statement_date->modify('+' . $payoff_year . ' years');
                $statement_date->modify('+' . $payoff_month . ' months');
                $tsp['payoff_date_general'] = $statement_date->format('Y-m-t');
            }
        }
        // echo $tsp['payoff_date_general'] . "<br>";
        // die;
        if ((isset($tsp['StatementDate']) && ($tsp['StatementDate'] != "") && $tsp['payoff_date_residential'] == "")) {
            // echo "here1" . $tsp['loan_balance_residential'] . "<br>";
            if ($tsp['loan_balance_residential'] != 0 && $tsp['loan_repayment_residential'] != 0) {
                // echo "here2";
                $payoff_date_residential_years = round(($tsp['loan_balance_residential'] / $tsp['loan_repayment_residential']) / 26, 1);
                $r_arr = explode('.', $payoff_date_residential_years);
                $rpayoff_year = $r_arr[0] ?? 0;
                $rpayoff_month = $r_arr[1] ?? 0;
                $statement_date = new \DateTime($tsp['StatementDate']);
                $statement_date->modify('+' . $rpayoff_year . ' years');
                $statement_date->modify('+' . $rpayoff_month . ' months');
                $tsp['payoff_date_residential'] = $statement_date->format('Y-m-d');
            }
        }
        // echo "Here3: " . $tsp['payoff_date_residential'];
        // die;
        $currentYear = new \DateTime(date('Y') . '-12-31');
        $bDayObj = new \DateTime($emp->eligibility->DateOfBirth);
        $ageDiff = $bDayObj->diff($currentYear);
        $age_by_end_of_year = $ageDiff->y;

        return view('admin.employee.tsp_edit', [
            'active_tab' => 'tsp',
            'empId' => $empId,
            'emp_conf' => $emp_conf,
            'tsp_conf' => $tsp_conf,
            'current_sal' => $current_sal,
            'tsp' => $tsp,
            'age_by_end_of_year' => $age_by_end_of_year
        ]);
    }

    public function tsp_update($empId = null, TspRequest $request)
    {
        $data = $request->all();
        $emp = resolve('employee')->getById($empId);
        if (is_null($emp)) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Invalid Employee Id'
            ]);
        }
        $emp = $emp->toArray();
        if ($emp['InitialSalary'] == 0) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Please Update Employees Current Salary In Pay and Leaves section.'
            ]);
        }
        $st = date('Y-m-d', strtotime($data['statement_date']));
        $min_st = date('Y-m-d', strtotime('2020-01-01'));
        $y = date('Y') + 10;
        $max_st = date('Y-m-d', strtotime($y . '-12-31'));
        if (($st < $min_st) || ($st > $max_st)) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Invalid statement date.'
            ]);
        }
        $distri = $data['gfund_distri'] + $data['ffund_distri'] + $data['cfund_distri'] + $data['sfund_distri'] + $data['ifund_distri'] + $data['lincome_distri'] + $data['l2025_distri'] + $data['l2030_distri'] + $data['l2035_distri'] + $data['l2040_distri'] + $data['l2045_distri'] + $data['l2050_distri'] + $data['l2055_distri'] + $data['l2060_distri'] + $data['l2065_distri'];
        if ($distri != 0) {
            if ($distri > 100 || $distri < 100) {
                return redirect()->back()->with([
                    'status' => 'danger',
                    'message' => 'The total for distributed percentages should be equals to 100'
                ]);
            }
        }
        $currentYear = new \DateTime(date('Y') . '-12-31');
        $bDayObj = new \DateTime($emp['eligibility']['DateOfBirth']);
        $ageDiff = $bDayObj->diff($currentYear);
        $age_by_end_of_year = $ageDiff->y;

        $totalCont = $data['tsp_contribution_catchup'] + $data['regular_tsp_contribution'];
        if ($age_by_end_of_year >= 50) {
            $allowedTotalCont = resolve('employee')->getSystemConfigurations('total_allowed_contri_for_age_50_or_greater');
        } else {
            $allowedTotalCont = resolve('employee')->getSystemConfigurations('total_allowed_contri_for_age_less_than_50');
        }
        if ($totalCont > $allowedTotalCont) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Total of Traditional and Roth Contribution should not exceed ' . $allowedTotalCont
            ]);
        }

        // echo "<pre>"; print_r($data); exit;
        if (($empId != null) && (!empty($data))) {
            unset($data['_token']);
            $result = resolve('employee')->updateEmployeeTsp($empId, $data);
            if ($result) {
                if ($data['next'] == 1) {
                    return redirect()->route('calcAndDebug', [$empId, 1])->with([
                        'status' => 'success',
                        'message' => 'TSP details updated successfully.'
                    ]);
                }
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'TSP details updated successfully.'
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function fegli_edit($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee']);
        }
        $emp = resolve('employee')->getById($empId);
        if (!$emp) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.']);
        }
        $basicReductionOptions = resolve('employee')->getReductionOptions('basic');
        $optionAReductionOptions = resolve('employee')->getReductionOptions('optionA');
        $optionBReductionOptions = resolve('employee')->getReductionOptions('optionB');
        $optionCReductionOptions = resolve('employee')->getReductionOptions('optionC');
        $fegli = resolve('employee')->getFegliByEmpId($empId);
        // echo "<pre>";
        // print_r($fegli->toArray());
        // exit;
        if ($fegli) {
            $data = $fegli->toArray();

            if (!isset($data['employee']['eligibility']['DateOfBirth'])) {
                return redirect()->back()->with(['status' => 'danger', 'message' => 'Please update employees birth date.']);
            }
            if (round($data['SalaryForFEGLI'], 2) == 0) {
                $current_sal = $data['employee']['CurrentSalary'];
            } else {
                $current_sal = $data['SalaryForFEGLI'];
            }
            $data['employee']['retirementType'] = $emp->retirementType;
            $fegli_data = resolve('employee')->getFegliReport($data);
            // echo "<pre>";
            // print_r($fegli_data);
            // die;
        } else {

            $data = [];
            if (is_null($emp->eligibility)) {
                return redirect()->back()->with(['status' => 'danger', 'message' => 'Please update employee DOB.']);
            }
            $current_sal = 0.000000;
            $fegli_data = [];
        }

        $costForBasicPremium = $fegli_data['biweekly_arr']['basic'] ?? 0;
        $costForOptionAPremium = $fegli_data['biweekly_arr']['optionA'] ?? 0;
        $costForOptionBPremium = $fegli_data['biweekly_arr']['optionB'] ?? 0;
        $costForOptionCPremium = $fegli_data['biweekly_arr']['optionC'] ?? 0;

        $costForBasicPremium_old = $fegli_data['old_premium_costs']['basic'] ?? 0;
        $costForOptionAPremium_old = $fegli_data['old_premium_costs']['optionA'] ?? 0;
        $costForOptionBPremium_old = $fegli_data['old_premium_costs']['optionB'] ?? 0;
        $costForOptionCPremium_old = $fegli_data['old_premium_costs']['optionC'] ?? 0;

        // echo number_format($costForOptionBPremium, 2);
        // die;
        $summary_arr = app('Common')->getSummaryFEGLI();

        /* Calculating cost for Basic premium starts */
        $sal_remainder = $current_sal % 1000;

        if (isset($data['employee']['PostalEmployee']) && ($data['employee']['PostalEmployee'] == 1)) {
            $nowDateObj = new \DateTime();
            $retDateObj = new \DateTime($data['employee']['eligibility']['RetirementDate']);
            if ($nowDateObj < $retDateObj) {
                $costForBasicPremium = 0;
            }
        }
        $costForBasicPremium =  !empty($data) ? (($data['BasicInc'] == 1) ? number_format($costForBasicPremium, 2) : 0) : 0;

        $optionA = !empty($data) ? (($data['OptionAInc'] == 1) ? number_format($costForOptionAPremium, 2) : 0) : 0;

        $optionB = !empty($data) ? (($data['OptionBInc'] == 1) ? number_format($costForOptionBPremium, 2) : 0) : 0;

        $optionC = !empty($data) ? (($data['OptionCInc'] == 1) ? number_format($costForOptionCPremium, 2) : 0) : 0;

        $abcPlus = $optionA + $optionB + $optionC;

        // echo $costForOptionAPremium_old . " ---- " . $costForOptionBPremium_old . " ---- " . $costForOptionCPremium_old;
        // die;
        return view('admin.employee.fegli_edit', [
            'active_tab' => 'fegli',
            'summary_arr' => $summary_arr,
            'empId' => $empId,
            'data' => $data,
            'costForBasicPremium' => $costForBasicPremium,
            'costForOptionAPremium' => $optionA,
            'costForOptionBPremium' => $optionB,
            'costForOptionCPremium' => $optionC,
            'basicReductionOptions' => $basicReductionOptions,
            'optionAReductionOptions' => $optionAReductionOptions,
            'optionBReductionOptions' => $optionBReductionOptions,
            'optionCReductionOptions' => $optionCReductionOptions,
            'abcPlus' => $abcPlus,
            'costForBasicPremium_old' => number_format(round($costForBasicPremium_old, 2), 2),
            'costForOptionAPremium_old' => number_format(round($costForOptionAPremium_old, 2), 2),
            'costForOptionBPremium_old' => number_format(round($costForOptionBPremium_old, 2), 2),
            'costForOptionCPremium_old' => number_format(round($costForOptionCPremium_old, 2), 2),
            'costForBasicPremium_old_nf' => $costForBasicPremium_old,
            'costForOptionAPremium_old_nf' => $costForOptionAPremium_old, // (no format, do that can be added on templete)
            'costForOptionBPremium_old_nf' => $costForOptionBPremium_old, // (no format, do that can be added on templete)
            'costForOptionCPremium_old_nf' => $costForOptionCPremium_old // (no format, do that can be added on templete)
        ]);
    }


    public function fegli_update($empId = null, FegliRequest $request)
    {
        if ($empId != null) {
            $data = $request->all();
            // echo "<pre>"; print_r($data); exit;
            if (isset($data['include_optionA']) || isset($data['include_optionB']) || isset($data['include_optionC'])) {
                if (!isset($data['include_basic'])) {
                    return redirect()->back()->with([
                        'status' => 'danger',
                        'message' => 'You need to include Basic Amount for selecting Option A, Option B or Option C'
                    ]);
                }
            }
            $result = resolve('employee')->updateEmployeeFegli($empId, $data);
            if ($result['status']) {
                if ($data['next'] == 1) {
                    return redirect()->route('healthBenefits', $empId)->with([
                        'status' => 'success',
                        'message' => 'Employee Fegli updated successfully'
                    ]);
                }
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Employee Fegli updated successfully'
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => $result['message']
        ]);
    }

    public function add_dependent($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with('error', 'Please select an Employee');
        }
        return view('admin.employee.add_dependent', [
            'active_tab' => 'fegli',
            'empId' => $empId
        ]);
    }

    public function save_dependent(AddDepartmentRequest $request)
    {
        $data = $request->all();
        if ($data['dob'] == NULL && ($data['age'] == NULL || $data['age'] == 0)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please add DOB or age of dependent.']);
        }
        $result = resolve('employee')->addFegliDependent($data);
        if ($result) {
            return redirect('employee/fegli/edit/' . $data['empId'])->with(['status' => 'success', 'message' => 'Dependent created successfully']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function edit_dependent($id = null)
    {
        if ($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a valid dependent.']);
        }
        $dependent = resolve('employee')->getDependentInfo($id);
        if (empty($dependent)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid dependent.']);
        }
        return view('admin.employee.edit_dependent', [
            'active_tab' => 'fegli',
            'empId' => $dependent['EmployeeId'],
            'dependent' => $dependent
        ]);
    }

    public function update_dependent($id = null, AddDepartmentRequest $request)
    {
        if ($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a valid dependent.']);
        }
        $data = $request->all();
        if ($data['dob'] == NULL && ($data['age'] == NULL || $data['age'] == 0)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please add DOB or age of dependent.']);
        }
        $result = resolve('employee')->updateFegliDependent($id, $data);
        if ($result) {
            return redirect('employee/fegli/edit/' . $data['empId'])->with(['status' => 'success', 'message' => 'Dependent created successfully']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function delete_dependent($id = null)
    {
        if ($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select a valid dependent.']);
        }
        $dependent = resolve('employee')->getDependentInfo($id);
        if (empty($dependent)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid dependent.']);
        }
        $result = resolve('employee')->deleteDependent($id);
        if ($result) {
            return redirect('employee/fegli/edit/' . $dependent['EmployeeId'])->with(['status' => 'success', 'message' => 'Dependent deleted successfully']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function health_benefits_edit($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with('error', 'Please select an Employee');
        }
        $employee = resolve('employee')->getById($empId)->toArray();
        if (!$employee) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid employee ID']);
        }
        // echo "<pre>"; print_r($employee); exit;

        $data = [
            'HealthPremium' => $employee['HealthPremium'],
            'DentalPremium' => $employee['DentalPremium'],
            'VisionPremium' => $employee['VisionPremium'],
            'dental_and_vision' => $employee['dental_and_vision'],
            'DoesNotMeetFiveYear' => $employee['DoesNotMeetFiveYear']
        ];
        return view('admin.employee.health_benifits_edit', [
            'active_tab' => 'health_benifits',
            'empId' => $empId,
            'data' => $data
        ]);
    }

    public function healthBenefitsUpdate($empId = null, HealthBenifitsRequest $request)
    {
        if ($empId == null) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Invalid Employee detail'
            ]);
        }
        $data = $request->all();
        $result = resolve('employee')->updateHealthBenifits($empId, $data);
        if ($result) {
            if ($data['next'] == 1) {
                return redirect()->route('fltcipEdit', $empId)->with([
                    'status' => 'success',
                    'message' => 'Employee Health Benefits Updated successfully'
                ]);
            }
            return redirect()->back()->with([
                'status' => 'success',
                'message' => 'Employee Health Benefits Updated successfully'
            ]);
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong, please try again'
        ]);
    }

    public function fltcip_edit($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid employee ID']);
        }
        $employee = resolve('employee')->getById($empId);
        if (!$employee) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid employee ID']);
        }
        $fltcip = resolve('employee')->getFltcip($empId);
        // echo $fltcip; exit;
        return view('admin.employee.fltcip_edit', [
            'active_tab' => 'fltcip',
            'empId' => $empId,
            'fltcip' => $fltcip
        ]);
    }

    public function updateFltcip($empId = null, FltcipRequest $request)
    {
        if ($empId != null) {
            $data = $request->all();
            // dd($data);
            $result = resolve('employee')->updateFltcip($empId, $data);
            if ($result) {
                if ($data['next'] == 1) {
                    return redirect()->route('empTspEdit', [$empId])->with([
                        'status' => 'success',
                        'message' => 'FLTCIP Premium updated successfully'
                    ]);
                }
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'FLTCIP Premium updated successfully'
                ]);
            }
        }
    }

    public function social_security_edit($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee']);
        }
        $employee = resolve('employee')->getById($empId);
        if ($employee) {
            $employee = $employee->toArray();
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid employee ID.']);
        }
        if (!isset($employee['eligibility']['DateOfBirth'])) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please add a Date of birth before proceeding.']);
        }
        $emp_age = date('Y') - date('Y', strtotime($employee['eligibility']['DateOfBirth']));
        $retirementYear = date('Y', strtotime($employee['eligibility']['RetirementDate']));
        if ($retirementYear > date('Y')) {
            $yearsLeftInRet = $retirementYear - date('Y');
            $retirementAge = $emp_age + $yearsLeftInRet;
        } else {
            $yearsExceedInRet = date('Y') - $retirementYear;
            $retirementAge = $emp_age - $yearsExceedInRet;
        }

        if (!$employee) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid employee ID']);
        }
        if ($employee['SSStartAge_year'] == 0 || $employee['SSStartAge_year'] == NULL) {
            $byear = date('Y', strtotime($employee['eligibility']['DateOfBirth']));
            $ss_start_age = resolve('employee')->getFullRetirementAge($byear);
            $employee['SSStartAge_year'] = $ss_start_age['fra_year'];
            $employee['SSStartAge_month'] = $ss_start_age['fra_month'];
        }
        $data = [
            'SSMonthlyAt62' => $employee['SSMonthlyAt62'],
            'SSStartAge_year' => $employee['SSStartAge_year'],
            'SSStartAge_month' => $employee['SSStartAge_month'],
            'SSMonthlyAtStartAge' => $employee['SSMonthlyAtStartAge'],
            'SSAtAgeOfRetirement' => $employee['SSAtAgeOfRetirement'],
            'SSYearsEarning' => $employee['SSYearsEarning'],
            'retirementAge' => $retirementAge
        ];
        // echo "<pre>"; print_r($data); exit;

        return view('admin.employee.social_security_edit', [
            'active_tab' => 'social_security',
            'empId' => $empId,
            'data' => $data
        ]);
    }

    public function social_security_update($empId = null, SocialSecurityRequest $request)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee']);
        }
        $data = $request->all();
        unset($data['_token']);
        // echo "<pre>"; print_r($data); exit;
        $result = resolve('employee')->socialSecurityUpdate($empId, $data);
        if ($result) {
            if ($data['next'] == 1) {
                return redirect()->route('empFegliEdit', $empId)->with(['status' => 'success', 'message' => 'Social security updated successfully.']);
            }
            return redirect()->back()->with(['status' => 'success', 'message' => 'Social security updated successfully.']);
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong, please try again.'
        ]);
    }


    public function configuration_edit($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with('error', 'Please select an Employee');
        }
        $emp_conf = resolve('employee')->getEmployeeConf($empId);
        $tspConf = resolve('employee')->getEmpTspConfigurations($empId);
        // echo "<pre>";
        // print_r($emp_conf);
        // exit;
        return view('admin.employee.configuration_edit', [
            'active_tab' => 'config',
            'empId' => $empId,
            'emp_conf' => $emp_conf,
            'tspConf' => $tspConf
        ]);
    }

    public function saveEmployeeConf($empId, SystemConfigRequest $request)
    {
        if ($empId != null) {
            $data = $request->all();
            unset($data['_token']);
            // echo "<pre>"; print_r($data); exit;
            $result = resolve('employee')->saveEmployeeConf($empId, $data);
            if ($result) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'configurations updated successfully'
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function report_notes_edit($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with('error', 'Please select an Employee');
        }

        return view('admin.employee.report_notes_edit', [
            'active_tab' => 'report_notes',
            'empId' => $empId
        ]);
    }

    public function add_new_employee()
    {
        $advisors = resolve('advisor')->getActiveAdvisers(false, 50);
        return view('admin.employee.add_new_employee', [
            'advisors' => $advisors
        ]);
    }

    public function createPdfFEGLI($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee']);
        }
        $employee = resolve('employee')->getById($empId);
        if (!$employee) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid employee ID']);
        }
        $completed = resolve('employee')->updateDateCompleted($empId);
        $empConf = resolve('employee')->getEmployeeConf($empId);

        $fegli = resolve('employee')->getFegliByEmpId($empId);
        $fegli_1 = resolve('employee')->getById($empId);
        $arr = array($fegli_1->SystemTypeId, $fegli_1->RetirementTypeId, $fegli_1->EmployeeTypeId);
        $fegli_2 = resolve('employee')->getRetirementType($arr);

        if (!$fegli) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Please add FEGLI information for employee before creating report.'
            ]);
        }

        $pensionScenario1 = resolve('employee')->getFirstPension($empId, 1);
        // dd($pensionScenario1);

        $data = $fegli->toArray();
        $data['employee']['retirementType'] = $employee->retirementType;

        $result_fegli_arr = resolve('employee')->getFegliReport($data);
        $fegli_arr = $result_fegli_arr['fegli_arr'];
        $fegliBiWeeklyCost = $result_fegli_arr['biweekly_arr'];
        $fegliMonthlyCost = $result_fegli_arr['monthly_arr'];
        $current_fegli = $result_fegli_arr['current_fegli'];
        $is_married = $result_fegli_arr['is_married'];
        $has_dependents = $result_fegli_arr['has_dependents'];
        $fegli_last_salary = $result_fegli_arr['last_salary'];
        $retirementAge = $result_fegli_arr['retirementAge'];

        // $marital_status = resolve('applookup')->getById($data['employee']['MaritalStatusTypeId'])->AppLookupName;

        $dtnow = new \DateTime(date('Y-m-d H:i:s'));
        $dtbday = new \DateTime($data['employee']['eligibility']['DateOfBirth']);
        $interval = $dtnow->diff($dtbday);

        $emp_age_format = $interval->y . 'y ' . $interval->m . 'm';
        $emp_age = $interval->y;
        $emp_age_mnth = $interval->m;

        $retirement_year = date('Y', strtotime($data['employee']['eligibility']['RetirementDate']));

        if ($data['SalaryForFEGLI'] == 0) {
            $current_sal = $data['employee']['CurrentSalary'];
        } else {
            $current_sal = $data['SalaryForFEGLI'];
        }

        if ($retirement_year > date('Y')) {
            $yearsLeftInRet = $retirement_year - date('Y');
            // $retirementAge = $emp_age + $yearsLeftInRet;
            $yearsInRet = 0;
        } else {
            $yearsExceedInRet = date('Y') - $retirement_year;
            // $retirementAge = $emp_age - $yearsExceedInRet;
            $yearsInRet = $yearsExceedInRet;
        }

        $yearlyPensionGross = resolve('employee')->getFirstPension($empId, 1);
        $pdf_data = [
            'emp_age_format' => $emp_age_format,
            'emp_age' => $emp_age,
            'emp_age_mnth' => $emp_age_mnth,
            'retirementAge' => $retirementAge,
            'retirement_year' => $retirement_year,
            'annual_sal' => $current_sal,
            'employee' => $employee,
            'empConf' => $empConf,
            'yearlyPensionGross' => $yearlyPensionGross
        ];

        /* TSP Module starts */
        $tsp_configurations = resolve('employee')->getEmpTspConfigurations($empId);
        $tsp = resolve('employee')->getEmpTspDetails($empId);
        if (empty($tsp)) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Please Update TSP details before creating PDF report.'
            ]);
        }
        $tsp['new_balance'] = resolve('employee')->getTSPNewBalance_new($empId);
        // echo "<pre>";
        // print_r($tsp['new_balance']);
        // die;

        $projected_ending_balance_data = resolve('employee')->calcProjectedEndingBalanceTsp($empId);
        /* TSP Module ends */

        /* FLTCIP module calculations start */
        $fltcipBiWeekly =  resolve('employee')->getFltcip($empId);
        $retirementYear = $retirement_year;

        /* Health Benifits calculations start */
        $is_postal = $employee['PostalEmployee'];
        $healthBenifits_arr = resolve('employee')->getHealthBenefitPdf($empId);
        // dd($healthBenifits_arr);

        if (($emp_age >= $retirementAge)) {
            $healthBenifits['biWeekly'] = [
                'health' => $employee['HealthPremium'],
                'dental' => $employee['DentalPremium'],
                'vision' => $employee['VisionPremium'],
                'dental_and_vision' => $employee['dental_and_vision'],
                'total' => ($employee['HealthPremium'] + $employee['DentalPremium'] + $employee['VisionPremium'] + $employee['dental_and_vision'])
            ];

            $healthPY = $employee['HealthPremium'] * 26;
            $dentalPY = $employee['DentalPremium'] * 26;
            $visionPY = $employee['VisionPremium'] * 26;
            $dentalAndVisionPY = $employee['dental_and_vision'] * 26;
            $healthBenifits['yearly'] = [
                'health' => $healthPY,
                'dental' => $dentalPY,
                'vision' => $visionPY,
                'dental_and_vision' => $dentalAndVisionPY,
                'total' => ($healthPY + $dentalPY + $visionPY + $dentalAndVisionPY)
            ];

            $healthPM = $healthPY / 12;
            $dentalPM = $dentalPY / 12;
            $visionPM = $visionPY / 12;
            $dentalAndVisionPM = $dentalAndVisionPY / 12;

            $healthBenifits['monthly'] = [
                'health' => $healthPM,
                'dental' => $dentalPM,
                'vision' => $visionPM,
                'dental_and_vision' => $dentalAndVisionPM,
                'total' => ($healthPM + $dentalPM + $visionPM + $dentalAndVisionPM)
            ];
            if ($employee['DoesNotMeetFiveYear'] == 1) {
                $healthBenifits['biWeekly'] = [
                    'health' => 0,
                    'dental' => $employee['DentalPremium'],
                    'vision' => $employee['VisionPremium'],
                    'dental_and_vision' => $employee['dental_and_vision'],
                    'total' => (0 + $employee['DentalPremium'] + $employee['VisionPremium'] + $employee['dental_and_vision'])
                ];

                $healthPY = $employee['HealthPremium'] * 26;
                $dentalPY = $employee['DentalPremium'] * 26;
                $visionPY = $employee['VisionPremium'] * 26;
                $dentalAndVisionPY = $employee['dental_and_vision'] * 26;
                $healthBenifits['yearly'] = [
                    'health' => 0,
                    'dental' => $dentalPY,
                    'vision' => $visionPY,
                    'dental_and_vision' => $dentalAndVisionPY,
                    'total' => (0 + $dentalPY + $visionPY + $dentalAndVisionPY)
                ];

                $healthPM = $healthPY / 12;
                $dentalPM = $dentalPY / 12;
                $visionPM = $visionPY / 12;
                $dentalAndVisionPM = $dentalAndVisionPY / 12;

                $healthBenifits['monthly'] = [
                    'health' => 0,
                    'dental' => $dentalPM,
                    'vision' => $visionPM,
                    'dental_and_vision' => $dentalAndVisionPM,
                    'total' => (0 + $dentalPM + $visionPM + $dentalAndVisionPM)
                ];
            }
            if (($emp_age == $retirementAge) && ($is_postal == 1)) {
                $healthPre = $employee['HealthPremium'] + ($employee['HealthPremium'] * 15 / 100);
                $dentalPre = $employee['DentalPremium'] + ($employee['DentalPremium'] * 15 / 100);
                $visionPre = $employee['VisionPremium'] + ($employee['VisionPremium'] * 15 / 100);
                $dAndVPre = $employee['dental_and_vision'] + ($employee['dental_and_vision'] * 15 / 100);
                $healthBenifits['biWeekly'] = [
                    'health' => $healthPre,
                    'dental' => $dentalPre,
                    'vision' => $visionPre,
                    'dental_and_vision' => $dAndVPre,
                    'total' => ($healthPre + $dentalPre + $visionPre + $dAndVPre)
                ];

                $healthPY = $healthPre * 26;
                $dentalPY = $dentalPre * 26;
                $visionPY = $visionPre * 26;
                $dentalAndVisionPY = $dAndVPre * 26;
                $healthBenifits['yearly'] = [
                    'health' => $healthPY,
                    'dental' => $dentalPY,
                    'vision' => $visionPY,
                    'dental_and_vision' => $dentalAndVisionPY,
                    'total' => ($healthPY + $dentalPY + $visionPY + $dentalAndVisionPY)
                ];

                $healthPM = $healthPY / 12;
                $dentalPM = $dentalPY / 12;
                $visionPM = $visionPY / 12;
                $dentalAndVisionPM = $dentalAndVisionPY / 12;

                $healthBenifits['monthly'] = [
                    'health' => $healthPM,
                    'dental' => $dentalPM,
                    'vision' => $visionPM,
                    'dental_and_vision' => $dentalAndVisionPM,
                    'total' => ($healthPM + $dentalPM + $visionPM + $dentalAndVisionPM)
                ];
            }
            // dd($healthBenifits);
        } else {
            $healthBenifits['biWeekly'] = [
                'health' => $employee['HealthPremium'],
                'dental' => $employee['DentalPremium'],
                'vision' => $employee['VisionPremium'],
                'dental_and_vision' => $employee['dental_and_vision'],
                'total' => ($employee['HealthPremium'] + $employee['DentalPremium'] + $employee['VisionPremium'] + $employee['dental_and_vision'])
            ];

            $healthPY = $employee['HealthPremium'] * 26;
            $dentalPY = $employee['DentalPremium'] * 26;
            $visionPY = $employee['VisionPremium'] * 26;
            $dentalAndVisionPY = $employee['dental_and_vision'] * 26;
            $healthBenifits['yearly'] = [
                'health' => $healthPY,
                'dental' => $dentalPY,
                'vision' => $visionPY,
                'dental_and_vision' => $dentalAndVisionPY,
                'total' => ($healthPY + $dentalPY + $visionPY + $dentalAndVisionPY)
            ];

            $healthPM = $healthPY / 12;
            $dentalPM = $dentalPY / 12;
            $visionPM = $visionPY / 12;
            $dentalAndVisionPM = $dentalAndVisionPY / 12;

            $healthBenifits['monthly'] = [
                'health' => $healthPM,
                'dental' => $dentalPM,
                'vision' => $visionPM,
                'dental_and_vision' => $dentalAndVisionPM,
                'total' => ($healthPM + $dentalPM + $visionPM + $dentalAndVisionPM)
            ];
        }


        $hb_premium_inc = EmployeeConfig::where('EmployeeId', $empId)->where('ConfigType', 'FEHBAveragePremiumIncrease')->first();
        if (!$hb_premium_inc) {
            $hb_premium_inc = AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', 'FEHBAveragePremiumIncrease')->first()->AppLookupDescription;
        } else {
            $hb_premium_inc = $hb_premium_inc->ConfigValue;
        }

        /* Health Benifits calculations ends */

        /* FEHB module calculation starts */

        $is_postal = $employee->PostalEmployee;
        /* FLTCIP module calculations start */
        $fltcipBiWeekly = resolve('employee')->getFltcip($empId);
        $retirementYear = $retirement_year;
        $emp_age = date('Y') - date('Y', strtotime($data['employee']['eligibility']['DateOfBirth']));

        /* Social_security Calculations starts */
        // if ($employee->systemType == 'FERS' || $employee->systemType == 'Transfers') {
        $ss_arr = resolve('employee')->getSocialSecurityDetailPdf($empId);
        if ($ss_arr == false) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Please add Social security and retirement Eligibility information for employee before creating report.'
            ]);
        }
        /* Social security Calculations ends */

        /* Pension data for pdf starts */
        $pensionArr = resolve('employee')->getPensionDetailsPdf($empId);
        $SBP_details = resolve('employee')->getSBPDetailsPdf($empId);
        // echo "<pre>";
        // print_r($pensionArr['retDetails']);
        // exit;
        $pensionScenario1 = resolve('employee')->getFirstPension($empId, 1);
        $pensionScenario2 = resolve('employee')->getFirstPension($empId, 2);
        $pensionScenario3 = resolve('employee')->getFirstPension($empId, 3);
        $pensionScenario4 = resolve('employee')->getFirstPension($empId, 4);
        $pensionScenario5 = resolve('employee')->getFirstPension($empId, 5);

        $wep_penalty = 0;
        if (($employee->systemType == 'CSRS Offset') || ($employee->systemType == 'CSRS')) {
            $pia_formula_bend = EmployeeConfig::where('EmployeeId', $empId)->where('ConfigType', 'PIAFormula')->first();
            if (!$pia_formula_bend) {
                $pia_formula_bend = AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', 'PIAFormula')->first()->AppLookupDescription;
            } else {
                $pia_formula_bend = $pia_formula_bend->ConfigValue;
            }
            // get wep panelty
            $wep_penalty = resolve('employee')->getWepPenalty($employee, $pia_formula_bend);

            // calculate offset time for csrs offset system employees
            // scenario 1
            $bday = new \DateTime($employee->eligibility->DateOfBirth);
            $now = new \DateTime();
            $ageObj = $bday->diff($now);
            $current_age = $ageObj->y;

            $retDate = new \DateTime($employee->eligibility->RetirementDate);
            $retAgeObj = $bday->diff($retDate);
            $retAge = $retAgeObj->y;
            // echo "Scenario Pension: " . $pensionScenario1 . " ----- ";
            // die;
            if ($retAge >= 62) {
                $csrsOffsetPenalty1 = resolve('employee')->getCsrsOffsetPenalty($empId, 1);
                $pensionScenario1_mnth = ($pensionScenario1 / 12) - ($csrsOffsetPenalty1 + $wep_penalty);
                $pensionScenario1 = $pensionScenario1_mnth * 12;
            }
            // dd($pensionScenario1);

            if (($retAge + 1) >= 62) {
                $csrsOffsetPenalty2 = resolve('employee')->getCsrsOffsetPenalty($empId, 2);
                $pensionScenario2_mnth = ($pensionScenario2 / 12) - ($csrsOffsetPenalty2 + $wep_penalty);
                $pensionScenario2 = $pensionScenario2_mnth * 12;
            }
            if (($retAge + 2) >= 62) {
                $csrsOffsetPenalty3 = resolve('employee')->getCsrsOffsetPenalty($empId, 3);
                $pensionScenario3_mnth = ($pensionScenario3 / 12) - ($csrsOffsetPenalty3 + $wep_penalty);
                $pensionScenario3 = $pensionScenario3_mnth * 12;
            }
            if (($retAge + 3) >= 62) {
                $csrsOffsetPenalty4 = resolve('employee')->getCsrsOffsetPenalty($empId, 4);
                $pensionScenario4_mnth = ($pensionScenario4 / 12) - ($csrsOffsetPenalty4 + $wep_penalty);
                $pensionScenario4 = $pensionScenario4_mnth * 12;
            }
            // echo $csrsOffsetPenalty4 . " ---- " . $pensionScenario4;
            // die;
            if (($retAge + 4) >= 62) {
                $csrsOffsetPenalty5 = resolve('employee')->getCsrsOffsetPenalty($empId, 5);
                $pensionScenario5_mnth = ($pensionScenario5 / 12) - ($csrsOffsetPenalty5 + $wep_penalty);
                $pensionScenario5 = $pensionScenario5_mnth * 12;
            }
        }

        $scenaio1 = resolve('employee')->calcProjectedHigh3Average($empId, 1);
        $scenaio2 = resolve('employee')->calcProjectedHigh3Average($empId, 2);
        $scenaio3 = resolve('employee')->calcProjectedHigh3Average($empId, 3);
        $scenaio4 = resolve('employee')->calcProjectedHigh3Average($empId, 4);
        $scenaio5 = resolve('employee')->calcProjectedHigh3Average($empId, 5);

        $fegliAllScenarios = resolve('employee')->getFegliAllScenarios($data, $fegli_last_salary, $retirementAge);

        $high3AllScenarios = resolve('employee')->getHigh3AvgAllScenarios($empId);
        // dd($high3AllScenarios);
        if ($employee->systemType == 'FERS') {
            $earlyoutPenaltyScenario1_arr = resolve('employee')->getMRAPenalty($empId, 1);
            $earlyoutPenaltyScenario1 = $earlyoutPenaltyScenario1_arr['first_pension_mra10_penalty'];
            $earlyoutPenaltyScenario2_arr = resolve('employee')->getMRAPenalty($empId, 2);
            $earlyoutPenaltyScenario2 = $earlyoutPenaltyScenario2_arr['first_pension_mra10_penalty'];
            $earlyoutPenaltyScenario3_arr = resolve('employee')->getMRAPenalty($empId, 3);
            $earlyoutPenaltyScenario3 = $earlyoutPenaltyScenario3_arr['first_pension_mra10_penalty'];
            $earlyoutPenaltyScenario4_arr = resolve('employee')->getMRAPenalty($empId, 4);
            $earlyoutPenaltyScenario4 = $earlyoutPenaltyScenario4_arr['first_pension_mra10_penalty'];
            $earlyoutPenaltyScenario5_arr = resolve('employee')->getMRAPenalty($empId, 5);
            $earlyoutPenaltyScenario5 = $earlyoutPenaltyScenario5_arr['first_pension_mra10_penalty'];
        } else {
            $earlyoutPenaltyScenario1 = resolve('employee')->getEarlyOutPenalty($empId, 1);
            $earlyoutPenaltyScenario2 = resolve('employee')->getEarlyOutPenalty($empId, 2);
            $earlyoutPenaltyScenario3 = resolve('employee')->getEarlyOutPenalty($empId, 3);
            $earlyoutPenaltyScenario4 = resolve('employee')->getEarlyOutPenalty($empId, 4);
            $earlyoutPenaltyScenario5 = resolve('employee')->getEarlyOutPenalty($empId, 5);
        }

        $depositPenalty = resolve('employee')->nonDeductionPanelty($empId);
        $redepositPenalty = resolve('employee')->calcRefundedPanelty($empId);

        /* Pension data for pdf ends */

        /* Front pages data starts */
        if ($employee['advisor']['SuppressConfidential'] == 1) {
            $bday = '******';
        } else {
            $bday = date('m/d/Y', strtotime($pdf_data['employee']['eligibility']['DateOfBirth']));
        }
        $disclaimer = resolve('employee')->getDesclaimerById($employee['advisor']['DefaultDisclaimerId']);
        if (count($disclaimer) > 0) {
            $disclaimerText = $disclaimer['DisclaimerText'];
        } else {
            $disclaimerText = '';
        }
        // echo "<pre>";
        // print_r($tsp);
        // exit;
        /* Front pages data ends */

        $netPensionScenario1 = $pensionScenario1 - ($earlyoutPenaltyScenario1 + $depositPenalty + $redepositPenalty);
        $netPensionScenario2 = $pensionScenario2 - ($earlyoutPenaltyScenario2 + $depositPenalty + $redepositPenalty);
        $netPensionScenario3 = $pensionScenario3 - ($earlyoutPenaltyScenario3 + $depositPenalty + $redepositPenalty);
        $netPensionScenario4 = $pensionScenario4 - ($earlyoutPenaltyScenario4 + $depositPenalty + $redepositPenalty);
        $netPensionScenario5 = $pensionScenario5 - ($earlyoutPenaltyScenario5 + $depositPenalty + $redepositPenalty);

        $current_fehb = [
            'biWeekly' => [
                'health' => $pdf_data['employee']['HealthPremium'],
                'dental' => $pdf_data['employee']['DentalPremium'],
                'vision' => $pdf_data['employee']['VisionPremium'],
                'dental_and_vision' => $pdf_data['employee']['dental_and_vision'],
                'total' => $pdf_data['employee']['HealthPremium'] + $pdf_data['employee']['DentalPremium'] + $pdf_data['employee']['VisionPremium'] + $pdf_data['employee']['dental_and_vision']
            ],
            'monthly' => [
                'health' => (($pdf_data['employee']['HealthPremium'] * 26) / 12),
                'dental' => (($pdf_data['employee']['DentalPremium'] * 26) / 12),
                'vision' => (($pdf_data['employee']['VisionPremium'] * 26) / 12),
                'dental_and_vision' => (($pdf_data['employee']['dental_and_vision'] * 26) / 12),
                'total' => (($pdf_data['employee']['HealthPremium'] * 26) / 12) + (($pdf_data['employee']['DentalPremium'] * 26) / 12) + (($pdf_data['employee']['VisionPremium'] * 26) / 12) + (($pdf_data['employee']['dental_and_vision'] * 26) / 12)
            ],
            'yearly' => [
                'health' => ($pdf_data['employee']['HealthPremium'] * 26),
                'dental' => ($pdf_data['employee']['DentalPremium'] * 26),
                'vision' => ($pdf_data['employee']['VisionPremium'] * 26),
                'dental_and_vision' => ($pdf_data['employee']['dental_and_vision'] * 26),
                'total' => ($pdf_data['employee']['HealthPremium'] * 26) + ($pdf_data['employee']['DentalPremium'] * 26) + ($pdf_data['employee']['VisionPremium'] * 26) + ($pdf_data['employee']['dental_and_vision'] * 26)
            ]
        ];

        $bdayrt = new \DateTime($data['employee']['eligibility']['DateOfBirth']);
        $rtDate = new \DateTime($data['employee']['eligibility']['RetirementDate']);
        $rtinterval = $bdayrt->diff($rtDate);
        $retirementAge = $rtinterval->y;
        $retirementAge_month = $rtinterval->m;

        $rt_year = date('Y', strtotime($data['employee']['eligibility']['RetirementDate']));
        $bd_year = date('Y', strtotime($data['employee']['eligibility']['DateOfBirth']));
        $ageRetCalenderYear = $rt_year - $bd_year;
        if ((($retirementAge == 59) && ($retirementAge_month >= 6)) || ($retirementAge >= 60)) {
            $irs_penalty_text = "The IRS has strict rules on accessing funds like the TSP prior to age 59½. Since you will already be at least age 59½
            when you retire, you will not be subject to IRS early withdrawal penalty on money you withdraw from the TSP.";
        } else {
            if (($employee['EmployeeType'] == "Regular") || ($employee['EmployeeType'] == "eCBPO")) {
                if ($ageRetCalenderYear < 55) {
                    $irs_penalty_text = "The IRS has strict rules on accessing funds like the TSP prior to age 59½. Regular employees who retire/separate in the calendar year in which they turn age 55 (or after) will have penalty-free access to their TSP funds from the time they retire/separate all the way until reaching age 59½. Based on your circumstances, you WILL NOT MEET this requirement at your planned separation date because you are retiring too young. You will be subject to the IRS's 10% early withdrawal penalty for any money you receive from TSP between the time you separate and age 59½.";
                } else {
                    $irs_penalty_text = "The IRS has strict rules on accessing funds like the TSP prior to age 59½. Regular employees who retire/separate in the calendar year in which they turn age 55 (or after) will have penalty-free access to their TSP funds from the time they retire/separate all the way until reaching age 59½. Based on your circumstances, you WILL MEET this requirement at your planned separation date. If you are considering a rollover out of TSP, it may be wise to leave enough money in the TSP that you may need between the time you retire/separate and age 59½.";
                }
            } else { // special provision employees
                if ($ageRetCalenderYear < 50) {
                    $irs_penalty_text = "The IRS has strict rules on accessing funds like the TSP prior to age 59½. Special Provision employees who retire/separate in the calendar year in which they turn age 50 (or after) will have penalty-free access to their TSP funds from the time they retire/separate all the way until reaching age 59½. Based on your circumstances, you WILL NOT MEET this
                    requirement at your planned separation date because you are retiring too young. You will be subject to the IRS's 10% early withdrawal penalty for any money you receive from TSP between the time you separate and age 59½.";
                } else {
                    $irs_penalty_text = "The IRS has strict rules on accessing funds like the TSP prior to age 59½. Special Provision employees who retire/separate in the calendar year in which they turn age 50 (or after) will have penalty-free access to their TSP funds from the time they retire/separate all the way until reaching age 59½. Based on your circumstances, you WILL MEET this requirement at your planned separation date. If you are considering a rollover out of TSP, it may be wise to leave enough money in the TSP that you may need between the time you retire/separate and age 59½.";
                }
            }
        }

        $minimumRetirementAge = resolve('employee')->getMinumumRetirementAge($empId);

        // dd($pdf_data);
        $view = \View::make('admin.employee.report_pdf', [
            'active_tab' => 'fegli',
            'empId' => $empId,
            'pdf_data' => $pdf_data,
            'data' => $data,
            'biWeeklyCost' => $fegliBiWeeklyCost,
            'monthlyCost' => $fegliMonthlyCost,
            'emp_age' => $emp_age,
            'retirement_year' => $retirement_year,
            'fltcipBiWeekly' => $fltcipBiWeekly,
            'retirementAge' => $retirementAge,
            'yearsInRet' => $yearsInRet,
            'healthBenifits' => $healthBenifits,
            'hb_premium_inc' => $hb_premium_inc,
            'fegli_arr' => $fegli_arr,
            'current_fegli' => $current_fegli,
            'fegliAllScenarios' => $fegliAllScenarios,
            'ss_arr' => $ss_arr,
            'tsp_configurations' => $tsp_configurations,
            'is_postal' => $is_postal,
            'tsp' => $tsp,
            // 'projected_ending_balance' => $projected_ending_balance,
            // 'contri_percentage_in_salary' => $contri_percentage_in_salary,
            // 'emp_current_contri' => $emp_current_contri,
            'pension' => $pensionArr,
            'scenaio1' => $scenaio1,
            'scenaio2' => $scenaio2,
            'scenaio3' => $scenaio3,
            'scenaio4' => $scenaio4,
            'scenaio5' => $scenaio5,
            'pensionScenario1' => $pensionScenario1,
            'pensionScenario2' => $pensionScenario2,
            'pensionScenario3' => $pensionScenario3,
            'pensionScenario4' => $pensionScenario4,
            'pensionScenario5' => $pensionScenario5,
            'earlyoutPenaltyScenario1' => $earlyoutPenaltyScenario1,
            'earlyoutPenaltyScenario2' => $earlyoutPenaltyScenario2,
            'earlyoutPenaltyScenario3' => $earlyoutPenaltyScenario3,
            'earlyoutPenaltyScenario4' => $earlyoutPenaltyScenario4,
            'earlyoutPenaltyScenario5' => $earlyoutPenaltyScenario5,
            'disclaimerText' => $disclaimerText,
            'bday' => $bday,
            'depositPenalty' => round($depositPenalty),
            'redepositPenalty' => round($redepositPenalty),
            'SBP_details' => $SBP_details,
            'projected_ending_balance_data' => $projected_ending_balance_data,
            'is_married' => $is_married,
            'has_dependents' => $has_dependents,
            'healthBenifits_arr' => $healthBenifits_arr,
            'high3AllScenarios' => $high3AllScenarios,
            'netPensionScenario1' => $netPensionScenario1,
            'netPensionScenario2' => $netPensionScenario2,
            'netPensionScenario3' => $netPensionScenario3,
            'netPensionScenario4' => $netPensionScenario4,
            'netPensionScenario5' => $netPensionScenario5,
            'current_fehb' => $current_fehb,
            'irs_penalty_text' => $irs_penalty_text,
            'minimumRetirementAge' => $minimumRetirementAge,
            'wep_penalty' => $wep_penalty,
        ]);

        $html_content = $view->render();

        // echo $html_content;
        // die;

        $doc_name = "Report-" . str_replace(' ', '', $employee['EmployeeName']) . "-Advisor-" . str_replace(' ', '', $employee['advisor']['AdvisorName']) . '_' . date('Ymdhis') . '.pdf';

        $footer_html = '<table style="width: 100%; border-top: 1px solid #ddd; color: #ddd; font-weight: bold;">
        <tr>
            <td style="width: 35%; text-align: left;">Prepared especially for <br>' . $pdf_data['employee']['EmployeeName'] . '
            </td>
            <td style="width: 30%; text-align: center;">© ' . date('Y') . ' ProFeds LLC
            </td>
            <td style="width: 35%; text-align: right;">Prepared by ProFeds on behalf of <br>' . $pdf_data['employee']['advisor']['AdvisorName'] . '
            </td>
        </tr>
        </table>';

        // Custom Footer
        $mypdf = new MYPDF();
        $mypdf::setFooterCallback(function ($pdf) use ($footer_html) {
            if ($pdf->getPage() == 1) {
                // $this->Cell(0, 10, 'Cover Page Footer '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
            } else {
                $pdf->SetY(-10); // -15
                // Set font
                $pdf->SetFont('helvetica', 'I', 8);

                $pdf->writeHTML($footer_html, true, false, true, false, '');
            }

            // Position at 15 mm from bottom

        });
        $mypdf::SetAuthor('Profeds');
        $mypdf::SetTitle($doc_name);
        $mypdf::SetSubject('Report By Profeds');
        $mypdf::SetMargins(8, 9, 8);
        $mypdf::SetFontSubsetting(false);
        $mypdf::SetFontSize('10px');
        $mypdf::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $mypdf::AddPage('P', 'A4');
        $mypdf::writeHTML($html_content, true, false, true, false, '');
        $mypdf::lastPage();
        $mypdf::Output($doc_name, 'D');
    }

    public function saveEmployee(EmployeeAddRequest $request)
    {
        $data = $request->all();
        // echo "<pre>"; print_r($data); exit;
        unset($data['_token']);
        $result = resolve('employee')->addNewEmployee($data);
        if ($result) {
            return redirect()->route('basicInformation', $result)->with(['status' => 'success', 'message' => 'Employee added successfully']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function addActiveMilitaryService($empId = null)
    {
        return view('admin.employee.addMilitaryService', [
            'active_tab' => 'retirement_eligibility',
            'empId' => $empId,
            'military_service_type' => 'Active',
            'military_service_type_id' => 2
        ]);
    }

    public function addReserveMilitaryService($empId = null)
    {
        return view('admin.employee.addMilitaryService', [
            'active_tab' => 'retirement_eligibility',
            'empId' => $empId,
            'military_service_type' => 'Reserve',
            'military_service_type_id' => 3
        ]);
    }

    public function saveMilitaryService($empId = null, AddMilitaryServiceRequest $request)
    {
        $data = $request->all();
        $militaryServiceTypeids = resolve('applookup')->getByTypeName('MilitaryServiceType')->pluck('AppLookupId')->toArray();
        if (($empId != null) && !empty($data)) {
            if ($empId == $data['employeeId']) {
                if (in_array($data['military_service_type_id'], $militaryServiceTypeids)) {
                    unset($data['_token']);
                    // echo "<pre>"; print_r($data); exit;
                    $result = resolve('employee')->saveMilitaryService($data);
                    if ($result) {
                        return redirect('/employee/retirementEligibility/' . $empId)->with([
                            'status' => 'success',
                            'message' => 'Service saved successfully'
                        ]);
                    }
                }
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function addNonDeductionService($empId = null)
    {
        return view('admin.employee.addNonDeductionService', [
            'active_tab' => 'retirement_eligibility',
            'empId' => $empId
        ]);
    }

    public function saveNonDeductionService($empId = null, NonDeductionServiceRequest $request)
    {
        $data = $request->all();
        // echo "<pre>"; print_r($data); exit;
        if (($empId != null) && !empty($data) && ($empId == $data['employeeId'])) {
            $result = resolve('employee')->saveNonDeductionService($data);
            if ($result) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Non Deduction Service added successfully'
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function addRefundedService($empId = null)
    {
        return view('admin.employee.addRefundedService', [
            'active_tab' => 'retirement_eligibility',
            'empId' => $empId
        ]);
    }

    public function saveRefundedService($empId = null, RefundedServiceRequest $request)
    {
        $data = $request->all();
        // echo "<pre>"; print_r($data); exit;
        if (($empId != null) && !empty($data) && $empId == $data['employeeId']) {
            unset($data['_token']);
            $result = resolve('employee')->saveRefundedServices($data);
            if ($result) {
                return redirect('/employee/retirementEligibility/' . $empId)->with([
                    'status' => 'success',
                    'message' => 'Refunded service added successfully'
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. please try again.'
        ]);
    }

    public function updateRefundedServiceView($empId = null, $serviceId = null)
    {
        if (($empId != null) && ($serviceId != null)) {
            $refundedService = resolve('employee')->getRefundedServiceById($serviceId)->toArray();
            if (!empty($refundedService)) {
                return view('admin.employee.editRefundedService', [
                    'active_tab' => 'retirement_eligibility',
                    'empId' => $empId,
                    'refundedService' => $refundedService
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. please try again.'
        ]);
    }

    public function updateRefundedService($serviceId = null, RefundedServiceRequest $request)
    {
        $data = $request->all();
        // echo "<pre>"; print_r($data); exit;
        if (($serviceId != null) && !empty($data)) {
            $result = resolve('employee')->updateRefundedService($serviceId, $data);
            if ($result) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Refunded service updated successfully'
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function editMilitaryService($empId = null, $serviceId = null)
    {
        if (($empId != null) && ($serviceId != null)) {
            $militaryService = resolve('employee')->getMilitaryServiceById($serviceId)->toArray();
            if (!empty($militaryService)) {
                $sType = ($militaryService['MilitaryServiceTypeId'] == 2) ? 'Active' : 'Reserve';
                return view('admin.employee.editMilitaryService', [
                    'active_tab' => 'retirement_eligibility',
                    'empId' => $empId,
                    'military_service_type' => $sType,
                    'militaryService' => $militaryService
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function updateMilitaryService($serviceId = null, AddMilitaryServiceRequest $request)
    {
        $data = $request->all();
        if (($serviceId != null) && !empty($data)) {
            unset($data['_token']);
            // echo "<pre>"; print_r($data); exit;
            $result = resolve('employee')->updateMilitaryService($serviceId, $data);
            if ($result) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Military Service updated successfully'
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function editNonDeductionService($empId = null, $serviceId = null)
    {
        if (($empId != null) && ($serviceId != null)) {
            $nonDeductionService = resolve('employee')->getNonDeductionServiceById($serviceId)->toArray();
            if (!empty($nonDeductionService)) {
                return view('admin.employee.editNonDeductionService', [
                    'active_tab' => 'retirement_eligibility',
                    'empId' => $empId,
                    'nonDeductionService' => $nonDeductionService
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function updateNonDeductionService($serviceId = null, NonDeductionServiceRequest $request)
    {
        $data = $request->all();
        if (($serviceId != null) && !empty($data)) {
            $result = resolve('employee')->updateNonDeductionService($serviceId, $data);
            if ($result) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'NonDeduction Service updated successfully'
                ]);
            }
        }
        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function addDeduction($empId = null)
    {
        if ($empId != null) {
            return view('admin.employee.addDeduction', [
                'active_tab' => 'pay_and_leave',
                'empId' => $empId,
            ]);
        }
    }

    public function saveDeduction($empId = null, DeductionRequest $request)
    {
        $data = $request->all();
        if (($empId != null) && !empty($data)) {
            // echo "<pre>"; print_r($data); exit;
            unset($data['_token']);
            $result = resolve('employee')->saveDeduction($empId, $data);
            if ($result) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'New Deduction added successfully'
                ]);
            }
        }

        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function editDeduction($empId = null, $deductionId = null)
    {
        $deduction = resolve('employee')->getDeduction($deductionId)->toArray();
        // echo "<pre>"; print_r($deduction); exit;
        return view('admin.employee.editDeduction', [
            'active_tab' => 'pay_and_leave',
            'empId' => $empId,
            'deduction' => $deduction
        ]);
    }

    public function updateDeduction($deductionId = null, DeductionRequest $request)
    {
        $data = $request->all();
        if (($deductionId != null) && !empty($data)) {
            $result = resolve('employee')->updateDeduction($deductionId, $data);
            if ($result) {
                return redirect()->back()->with([
                    'status' => 'success',
                    'message' => 'Deduction updated successfully'
                ]);
            }
        }

        return redirect()->back()->with([
            'status' => 'danger',
            'message' => 'Something went wrong. Please try again.'
        ]);
    }

    public function calculate_and_debug($empId = null, $scenario = 1)
    {
        if (($empId == null) || ($scenario <= 0) || ($scenario > 5)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee']);
        }
        $completed = resolve('employee')->updateDateCompleted($empId);
        $sce = resolve('employee')->updateScenario($empId, $scenario);

        $scenarios          = resolve('employee')->getEmployeeScenarios($empId);
        $report_dates_arr   = resolve('employee')->calcDebugReportDates($empId, $scenario);
        // dd($report_dates_arr );
        $employee = resolve('employee')->getById($empId)->toArray();
        if (empty($employee['eligibility'])) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please update birthdate for employee.']);
        }
        $tsp = resolve('employee')->getEmpTspDetails($empId);
        if (empty($tsp)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please update TSP details for employee.']);
        }


        if (empty($scenarios)) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please update Retirement Eligibility section']);
            // $res = resolve('employee')->createEmployeeScenarios($empId);
            // $scenarios = resolve('employee')->getEmployeeScenarios($empId);
        }


        $report_dates       = $report_dates_arr['report_dates'] ?? [];
        $scenarioData1      = $report_dates_arr['scenarioData'] ?? [];
        $salReport          = $report_dates_arr['salReport'] ?? [];
        $annuity            = resolve('employee')->calcDebugAnnuityData($empId, $scenario);
        $scenarioData2      = $annuity['scenarioData'];
        $ssReport           = resolve('employee')->CalcAndDebugSS($empId);
        $fersSupplement     = resolve('employee')->CalcAndDebugFersSuppl($empId);
        $allIncomeAnnual    = resolve('employee')->CalcAndDebugAllIncomeAnnual($empId);
        $tsp_sal_increase   = resolve('employee')->calcAndDebugTspSalIncrease($empId);
        $tsp_Nosal_increase = resolve('employee')->calcAndDebugTspNoSalIncrease($empId);
        $pre_retirement     = resolve('employee')->pre_retirement($empId, "pre");
        $post_retirement    = resolve('employee')->pre_retirement($empId, "post");
        $fltcipBiWeekly     = resolve('employee')->getFltcip($empId);
        $fltcip             = resolve('employee')->getFltcip($empId);
        $empConf            = resolve('employee')->getEmployeeConf($empId);
        $empConf            = $empConf['FEHBAveragePremiumIncrease'];

        $annuityTable = resolve('employee')->calcAndDebugAnnuity($empId, $scenario);

        // print_r($tsp_Nosal_increase);die("--");
        // echo "<pre>"; print_r($annuity['scenarioData']); exit;

        $is_postal = $employee['PostalEmployee'];
        /* Health Benifits calculations start */
        $healthBenifits['biWeekly'] = [
            'health' => $employee['HealthPremium'],
            'dental' => $employee['DentalPremium'],
            'vision' => $employee['VisionPremium'],
            'dental_and_vision' => $employee['dental_and_vision'],
            'total' => ($employee['HealthPremium'] + $employee['DentalPremium'] + $employee['VisionPremium'] + $employee['dental_and_vision'])
        ];

        $healthPY = $employee['HealthPremium'] * 26;
        $dentalPY = $employee['DentalPremium'] * 26;
        $visionPY = $employee['VisionPremium'] * 26;
        $dentalAndVisionPY = $employee['dental_and_vision'] * 26;
        $healthBenifits['yearly'] = [
            'health' => $healthPY,
            'dental' => $dentalPY,
            'vision' => $visionPY,
            'dental_and_vision' => $dentalAndVisionPY,
            'total' => ($healthPY + $dentalPY + $visionPY + $dentalAndVisionPY)
        ];

        $healthPM = $healthPY / 12;
        $dentalPM = $dentalPY / 12;
        $visionPM = $visionPY / 12;
        $dentalAndVisionPM = $dentalAndVisionPY / 12;
        $healthBenifits['monthly'] = [
            'health' => $healthPM,
            'dental' => $dentalPM,
            'vision' => $visionPM,
            'dental_and_vision' => $dentalAndVisionPM,
            'total' => ($healthPM + $dentalPM + $visionPM + $dentalAndVisionPM)
        ];

        $hb_premium_inc = EmployeeConfig::where('EmployeeId', $empId)->where('ConfigType', 'FEHBAveragePremiumIncrease')->first();
        if (!$hb_premium_inc) {
            $hb_premium_inc = AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', 'FEHBAveragePremiumIncrease')->first()->AppLookupDescription;
        } else {
            $hb_premium_inc = $hb_premium_inc->ConfigValue;
        }
        /* Health Benifits calculations ends */

        $dateNow = new \DateTime();
        $bday = new \DateTime($employee['eligibility']['DateOfBirth']);
        $interval = $dateNow->diff($bday);
        $emp_age = $interval->y;

        $retDate = new \DateTime($employee['eligibility']['RetirementDate']);
        $retInterval = $retDate->diff($bday);
        $retirementAge = $retInterval->y;
        $yearsInRet = date('Y');

        /* $temVar = "";
        $i = 0;
        foreach ($hbenefits['hbdata']['hbYearlyCost'] as $key => $value) {

            if($i == 0){
                echo $i;
                $temVar = round($value);
                $i++;
                continue;
            }
            $difference = round($value) - $temVar;
            $temVar = round($value);
        } */
        $healthBenifits_arr = resolve('employee')->getHealthBenefitPdf($empId);

        return view('admin.employee.calculate_and_debug', [
            'active_tab' => 'calc_debug',
            'empId' => $empId,
            'scenarios' => $scenarios,
            'scenarioData1' => $scenarioData1,
            'report_dates' => $report_dates,
            'salReport' => $salReport,
            'scenarioData2' => $scenarioData2,
            'ssReport' => $ssReport,
            'fersSupplement' => $fersSupplement,
            'allIncomeAnnual' => $allIncomeAnnual,
            'tsp_sal_increase' => $tsp_sal_increase,
            'tsp_Nosal_increase' => $tsp_Nosal_increase,
            'pre_retirement' => $pre_retirement,
            'post_retirement' => $post_retirement,
            'fltcipBiWeekly' => $fltcipBiWeekly,
            'emp_age' => $emp_age,
            'retirementAge' => $retirementAge,
            'yearsInRet' => $yearsInRet,
            'is_postal' => $is_postal,
            'healthBenifits' => $healthBenifits,
            'hb_premium_inc' => $hb_premium_inc,
            'fltcip' => $fltcip,
            'FEHBAveragePremiumIncrease' => $empConf,
            'URLscenariono' => $scenario,
            'annuityTable' => $annuityTable,
            'healthBenifits_arr' => $healthBenifits_arr
        ]);
    }

    public function add_child($empId = null)
    {
        return view('admin.employee.add_child', [
            'active_tab' => 'basic_info',
            'empId' => $empId
        ]);
    }

    public function update_child($childid = null)
    {
        $childData = Child::where('childid', $childid)->first();
        return view('admin.employee.update_child', [
            'active_tab' => 'basic_info',
            'empId' => $childData->EmployeeId,
            'childData' => $childData
        ]);
    }

    public function save_child(ChildRequest $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $result = resolve('child')->addNewChild($data);

        if ($result) {
            return redirect('employee/basic_info/' . $data['empId'])->with(['status' => 'success', 'message' => 'Child created successfully.']);
        } else {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }

    public function update_child_data(ChildRequest $request)
    {
        $data = $request->all();
        unset($data['_token']);
        $result = Child::where('ChildId', $data['ChildId'])
            ->update(['ChildName' => $data['name'], 'ChildAge' => $data['age']]);

        if ($result) {
            return redirect('employee/basic_info/' . $data['empId'])->with(['status' => 'success', 'message' => 'Child updated successfully.']);
        } else {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Something went wrong. Please try again.'
            ]);
        }
    }

    public function earliestRetirement($empId = null, $dob = null)
    {
        $retEligibility = resolve('employee')->getMinumumRetirementAge($empId, 1, $dob);
        return json_encode($retEligibility);
    }

    public function fullRetirement($empId = null, $dob = null, $leaveSCD = null)
    {
        $retEligibility = resolve('employee')->getFullEligibilityRetirementAge($empId, $dob, $leaveSCD);
        return json_encode($retEligibility);
    }

    public function deleteCase($id = null)
    {
        if ($id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input.']);
        }
        $emp = resolve('employee')->getById($id);
        if (!$emp) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input.']);
        }
        $result = resolve('employee')->deleteCase($id);
        if ($result) {
            return redirect()->route('admin.cases')->with(['status' => 'success', 'message' => 'Case deleted successfully.']);
        } else {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Something went wrong. Please try again.']);
        }
    }

    public function caseHistory($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input.']);
        }
        $basic_tab_data = resolve('employee')->getEmployeeTabHistory($empId, 'basic_info');
        $retirement_tab_data = resolve('employee')->getEmployeeTabHistory($empId, 'retirement_eligibility');
        $partTime_tab_data = resolve('employee')->getEmployeeTabHistory($empId, 'part_time_service');
        $pay_and_leave_tab_data = resolve('employee')->getEmployeeTabHistory($empId, 'pay_and_leave');
        $tsp_tab_data = resolve('employee')->getEmployeeTabHistory($empId, 'tsp');
        $fegli_tab_data = resolve('employee')->getEmployeeTabHistory($empId, 'fegli');
        $health_benefits_tab = resolve('employee')->getEmployeeTabHistory($empId, 'health_benefits');
        $fltcip_tab_data = resolve('employee')->getEmployeeTabHistory($empId, 'fltcip');
        $social_security_tab_data = resolve('employee')->getEmployeeTabHistory($empId, 'social_security');
        $configuration_tab_data = resolve('employee')->getEmployeeTabHistory($empId, 'configuration');
        // echo "<pre>";
        // print_r($health_benefits_tab);
        // die;
        return view('admin.employee.case_history', [
            'empId' => $empId,
            'basic_tab_data' => $basic_tab_data,
            'retirement_tab_data' => $retirement_tab_data,
            'partTime_tab_data' => $partTime_tab_data,
            'pay_and_leave_tab_data' => $pay_and_leave_tab_data,
            'tsp_tab_data' => $tsp_tab_data,
            'fegli_tab_data' => $fegli_tab_data,
            'health_benefits_tab' => $health_benefits_tab,
            'fltcip_tab_data' => $fltcip_tab_data,
            'social_security_tab_data' => $social_security_tab_data,
            'configuration_tab_data' => $configuration_tab_data
        ]);
        // resolve('employee')->getSystemConfigurations($config)
    }

    public function makeDuplicate($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input.']);
        }

        $res = resolve('employee')->makeDuplicate($empId);
        if ($res) {
            $ret = ['status' => 'success', 'message' => 'Duplicated successfully.'];
        } else {
            $ret = ['status' => 'danger', 'message' => 'Could not duplicate.'];
        }
        return redirect()->back()->with($ret);
    }

    public function deleteMilitaryService($service_id = null)
    {
        if ($service_id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input.']);
        }
        $res = resolve('employee')->deleteMilitaryService($service_id);
        if ($res) {
            $ret = ['status' => 'success', 'message' => 'Deleted successfully.'];
        } else {
            $ret = ['status' => 'danger', 'message' => 'Could not delete.'];
        }
        return redirect()->back()->with($ret);
    }

    public function deleteNonDeductionService($service_id = null)
    {
        if ($service_id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input.']);
        }
        $res = resolve('employee')->deleteNonDeductionService($service_id);
        if ($res) {
            $ret = ['status' => 'success', 'message' => 'Deleted successfully.'];
        } else {
            $ret = ['status' => 'danger', 'message' => 'Could not delete.'];
        }
        return redirect()->back()->with($ret);
    }

    public function deleteRefundedService($service_id = null)
    {
        if ($service_id == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid Input.']);
        }
        $res = resolve('employee')->deleteRefundedService($service_id);
        if ($res) {
            $ret = ['status' => 'success', 'message' => 'Deleted successfully.'];
        } else {
            $ret = ['status' => 'danger', 'message' => 'Could not delete.'];
        }
        return redirect()->back()->with($ret);
    }

    public function addNotes($empId = null)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee']);
        }
        $emp = resolve('employee')->getById($empId);
        if (!$emp) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.']);
        }
        return view('admin.employee.add_notes', [
            'active_tab' => 'notes',
            'empId' => $empId,
            'notes' => $emp->notes
        ]);
    }

    public function saveNotes($empId = null, Request $request)
    {
        if ($empId == null) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Please select an Employee'])->withInput();
        }
        $emp = resolve('employee')->getById($empId);
        if (!$emp) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.'])->withInput();
        }
        $data = $request->all();

        if (strlen($data['notes']) > 9500) {
            return redirect()->back()->with(['status' => 'danger', 'message' => 'Length exceeds maximum limit.'])->withInput();
        }
        $notes = $data['notes'] ?? '';
        $res = resolve('employee')->updateNotes($empId, $notes);
        if ($res) {
            return redirect()->back()->with(['status' => 'success', 'message' => 'Notes updated successfully.']);
        }
        return redirect()->back()->with(['status' => 'danger', 'message' => 'Invalid input.'])->withInput();
    }
}
