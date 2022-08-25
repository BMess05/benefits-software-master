<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\Advisor;
use App\Models\Fegli;
use App\Models\Eligibility;
use App\Models\MilitaryService;
use App\Models\NonDeductionService;
use App\Models\RefundedService;
use App\Models\PartTimeService;
use App\Models\EmployeeConfig;
use App\Models\AppLookup;
use App\Models\Tsp;
use App\Models\Deduction;
use App\Models\Disclaimer;
use App\Models\EmployeeFile;
use App\Models\ReportScenario;
use App\Models\FEGLIDependent;
use App\Models\TspCalculation;
use App\Models\LfundsDist;
use Auth;
use App\Models\HistoryColumn;
use App\Models\HistoryChange;
use Illuminate\Support\Facades\DB;

class EmployeeRepository
{
    public function getAll()
    {
        return Employee::all();
    }

    public function getAllActive($paginate = false, $per_page = null)
    {
        $listing = Employee::with(['advisor', 'eligibility'])->orderBy('EmployeeId', 'desc')->where('IsActive', 1);
        if ($paginate) {
            return $listing->paginate($per_page);
        }
        return $listing->get();
    }

    public function getById($id = null)
    {
        // echo "<pre>"; print_r($empConf); exit;SalaryIncrease
        $emp = Employee::with(['advisor', 'eligibility', 'deduction', 'employeeConfig', 'militaryService', 'nonDeductionService', 'refundedService'])->where('EmployeeId', $id)->first();
        if ($emp != '') {
            $empType = AppLookup::where('AppLookupId', $emp->EmployeeTypeId)->first();
            if ($empType) {
                $empType = $empType->AppLookupName;
            } else {
                $empType = '';
            }
            $systemType = AppLookup::where('AppLookupId', $emp->SystemTypeId)->first();
            if ($systemType) {
                $systemType = $systemType->AppLookupName;
            } else {
                $systemType = '';
            }
            $retirementType = AppLookup::where('AppLookupId', $emp->RetirementTypeId)->first();
            if ($retirementType) {
                $retirementType = $retirementType->AppLookupName;
            } else {
                $retirementType = '';
            }
            if (($empType == "Regular") || ($empType == "eCBPO")) {
                $otherEmpType = '';
            } else {
                $otherEmpType = AppLookup::where('AppLookupId', $emp->OtherEmployeeTypeId)->first();
                if ($otherEmpType) {
                    $otherEmpType = $otherEmpType->AppLookupName;
                } else {
                    $otherEmpType = '';
                }
            }

            if (!empty($emp->militaryService)) {
                foreach ($emp->militaryService as $service) {
                    $service->militaryServiceType = AppLookup::where('AppLookupId', $service->MilitaryServiceTypeId)->first()->AppLookupName;
                }
            }

            $emp->empType = $empType;
            $emp->systemType = $systemType;
            $emp->retirementType = $retirementType;
            $emp->otherEmpType = $otherEmpType;

            $emp->InitialSalary = $emp->CurrentSalary;

            $reportDate = new \DateTime($emp->ReportDate);
            $now = new \DateTime();

            if (is_null($emp->eligibility)) {
                $interval = $reportDate->diff($now);
            } else {
                $retDate = new \DateTime($emp->eligibility->RetirementDate);
                if ($retDate < $reportDate) {
                    $interval = $reportDate->diff($reportDate);
                } else {
                    $interval = $reportDate->diff($now);
                }
            }

            if ($interval->y > 0) {
                $currentSal = $emp->CurrentSalary;
                $empConf = $this->getEmployeeConf($id);
                $salIncPer = $empConf['SalaryIncrease'];

                for ($i = 0; $i <= $interval->y; $i++) {
                    $currentSal = $currentSal + ($currentSal * ($salIncPer / 100));
                }
                $emp->CurrentSalary = $currentSal;
            }

            return $emp;
        } else {
            $emp = "";
            return $emp;
        }
    }

    public function getFegliByEmpId($emp_id = null)
    {
        $listing = Fegli::with(['employee' => function ($query) {
            $query->with('advisor')->select('EmployeeId', 'EmployeeName', 'CurrentSalary', 'MaritalStatusTypeId', 'SpouseName', 'PostalEmployee', 'ReportDate');
        }, 'employee.eligibility' => function ($query) {
            $query->select('EmployeeId', 'DateOfBirth', 'RetirementDate');
        }, 'fegliDependent'])->where('EmployeeId', $emp_id);

        return $listing->first();
    }

    public function addNewEmployee($data = [])
    {
        if (!empty($data)) {
            $emp = new Employee();
            $emp->EmployeeName = $data['name'];
            $emp->AdvisorId = $data['advisor'];
            $emp->DateReceived = date('Y-m-d', strtotime($data['date_received']));
            $emp->DueDate = date('Y-m-d', strtotime($data['due_date']));
            $emp->ReportDate = date('Y-m-d');
            $emp->created_by = Auth::user()->id;
            if ($emp->save()) {
                return $emp->EmployeeId;
            }
        }
        return false;
    }

    public function getFltcip($empId = null)
    {
        if ($empId != null) {
            return Employee::where('EmployeeId', $empId)->select('FLTCIPPremium')->first()->FLTCIPPremium;
        }
        return false;
    }

    public function updateFltcip($empId = null, $data = [])
    {
        $emp = Employee::find($empId);
        $history_fields['Employee'] = [];
        if ($emp->FLTCIPPremium != $data['fltcip_premium']) {
            $history_fields['Employee'][] = [
                'column_name' => 'FLTCIPPremium',
                'old_value' => $emp->FLTCIPPremium,
                'new_value' => $data['fltcip_premium']
            ];
        }
        $emp->FLTCIPPremium = $data['fltcip_premium'];
        if ($emp->save()) {
            $row_id = 0;
            $res = $this->updateHistory($empId, $history_fields, $row_id, 'fltcip');
            return true;
        }
        return false;
    }

    public function updateHealthBenifits($empId, $data = [])
    {
        $emp = Employee::find($empId);
        if (!$emp) {
            return false;
        }
        $history_fields['Employee'] = [];
        if ($emp->HealthPremium != $data['health_premium']) {
            $history_fields['Employee'][] = [
                'column_name' => 'HealthPremium',
                'old_value' => $emp->HealthPremium,
                'new_value' => $data['health_premium']
            ];
        }
        if ($emp->DentalPremium != $data['dental_premium']) {
            $history_fields['Employee'][] = [
                'column_name' => 'DentalPremium',
                'old_value' => $emp->DentalPremium,
                'new_value' => $data['dental_premium']
            ];
        }
        if ($emp->VisionPremium != $data['vision_premium']) {
            $history_fields['Employee'][] = [
                'column_name' => 'VisionPremium',
                'old_value' => $emp->VisionPremium,
                'new_value' => $data['vision_premium']
            ];
        }
        if ($emp->dental_and_vision != $data['dental_and_vision']) {
            $history_fields['Employee'][] = [
                'column_name' => 'dental_and_vision',
                'old_value' => $emp->dental_and_vision,
                'new_value' => $data['dental_and_vision']
            ];
        }
        $doesNotMeetFiveYear = isset($data['does_not_meet_five_year']) ? 1 : 0;
        if ($emp->DoesNotMeetFiveYear != $doesNotMeetFiveYear) {
            $history_fields['Employee'][] = [
                'column_name' => 'DoesNotMeetFiveYear',
                'old_value' => $emp->DoesNotMeetFiveYear,
                'new_value' => $doesNotMeetFiveYear
            ];
        }
        $emp->HealthPremium = $data['health_premium'];
        $emp->DentalPremium = $data['dental_premium'];
        $emp->VisionPremium = $data['vision_premium'];
        $emp->dental_and_vision = $data['dental_and_vision'];
        $emp->DoesNotMeetFiveYear = $doesNotMeetFiveYear;
        if ($emp->save()) {
            $row_id = 0;
            $res = $this->updateHistory($empId, $history_fields, $row_id, 'health_benefits');
            return true;
        }
        return false;
    }

    public function socialSecurityUpdate($empId = null, $data = [])
    {
        $emp = Employee::find($empId);
        // echo "<pre>"; print_r($data); exit;
        $history_fields['Employee'] = [];
        if ($emp->SSMonthlyAt62 != $data['monthly_social_security']) {
            $history_fields['Employee'][] = [
                'column_name' => 'SSMonthlyAt62',
                'old_value' => $emp->SSMonthlyAt62,
                'new_value' => $data['monthly_social_security']
            ];
        }
        if ($emp->SSStartAge_year != $data['social_security_start_age_year']) {
            $history_fields['Employee'][] = [
                'column_name' => 'SSStartAge_year',
                'old_value' => $emp->SSStartAge_year,
                'new_value' => $data['social_security_start_age_year']
            ];
        }
        if ($emp->SSStartAge_month != $data['social_security_start_age_month']) {
            $history_fields['Employee'][] = [
                'column_name' => 'SSStartAge_month',
                'old_value' => $emp->SSStartAge_month,
                'new_value' => $data['social_security_start_age_month']
            ];
        }
        if ($emp->SSMonthlyAtStartAge != $data['monthly_social_security_at_start_age']) {
            $history_fields['Employee'][] = [
                'column_name' => 'SSMonthlyAtStartAge',
                'old_value' => $emp->SSMonthlyAtStartAge,
                'new_value' => $data['monthly_social_security_at_start_age']
            ];
        }
        if ($emp->SSAtAgeOfRetirement != $data['social_security_at_age_of_retirement']) {
            $history_fields['Employee'][] = [
                'column_name' => 'SSAtAgeOfRetirement',
                'old_value' => $emp->SSAtAgeOfRetirement,
                'new_value' => $data['social_security_at_age_of_retirement']
            ];
        }
        if ($emp->SSYearsEarning != $data['ss_substantial_earning_years']) {
            $history_fields['Employee'][] = [
                'column_name' => 'SSYearsEarning',
                'old_value' => $emp->SSYearsEarning,
                'new_value' => $data['ss_substantial_earning_years']
            ];
        }

        $emp->SSMonthlyAt62 = $data['monthly_social_security'];
        $emp->SSStartAge_year = $data['social_security_start_age_year'];
        $emp->SSStartAge_month = $data['social_security_start_age_month'];
        $emp->SSMonthlyAtStartAge = $data['monthly_social_security_at_start_age'];
        $emp->SSAtAgeOfRetirement = $data['social_security_at_age_of_retirement'] ?? 0;
        $emp->SSYearsEarning = $data['ss_substantial_earning_years'];
        if ($emp->save()) {
            $row_id = 0;
            $res = $this->updateHistory($empId, $history_fields, $row_id, 'social_security');
            return true;
        }
        return false;
    }

    public function updateBasicInfo($empId = null, $data = [])
    {
        if ($empId == null || empty($data)) {
            return false;
        }
        $emp = Employee::find($empId);
        if (!$emp) {
            return false;
        }
        $history_fields['Employee'] = [];
        $change = [];
        if ($emp->EmployeeName != $data['name']) {
            $history_fields['Employee'][] = [
                'column_name' => 'EmployeeName',
                'old_value' => $emp->EmployeeName,
                'new_value' => $data['name']
            ];
        }
        if ($emp->AdvisorId != $data['advisor']) {
            $history_fields['Employee'][] = [
                'column_name' => 'AdvisorId',
                'old_value' => $emp->AdvisorId,
                'new_value' => $data['advisor']
            ];
        }
        if ($emp->DateReceived != date('Y-m-d', strtotime($data['date_received']))) {
            $history_fields['Employee'][] = [
                'column_name' => 'DateReceived',
                'old_value' => $emp->DateReceived,
                'new_value' => date('Y-m-d', strtotime($data['date_received']))
            ];
        }
        if ($emp->DueDate != date('Y-m-d H:i:s', strtotime($data['due_date']))) {
            $history_fields['Employee'][] = [
                'column_name' => 'DueDate',
                'old_value' => $emp->DueDate,
                'new_value' => date('Y-m-d', strtotime($data['due_date']))
            ];
        }
        if ($emp->DateCompleted != date('Y-m-d', strtotime($data['date_completed']))) {
            $history_fields['Employee'][] = [
                'column_name' => 'DateCompleted',
                'old_value' => $emp->DateCompleted,
                'new_value' => date('Y-m-d', strtotime($data['date_completed']))
            ];
        }
        if ($emp->ReportDate != date('Y-m-d H:i:s', strtotime($data['report_year'] . '-' . $data['report_month'] . '-01'))) {
            $history_fields['Employee'][] = [
                'column_name' => 'ReportDate',
                'old_value' => $emp->ReportDate,
                'new_value' => date('Y-m-d', strtotime($data['report_year'] . '-' . $data['report_month'] . '-01'))
            ];
        }
        if ($emp->EmployeeAddress != $data['address']) {
            $history_fields['Employee'][] = [
                'column_name' => 'EmployeeAddress',
                'old_value' => $emp->EmployeeAddress,
                'new_value' => $data['address']
            ];
        }
        if ($emp->SystemTypeId != $data['system']) {
            $history_fields['Employee'][] = [
                'column_name' => 'SystemTypeId',
                'old_value' => $emp->SystemTypeId,
                'new_value' => $data['system']
            ];
        }
        if ($emp->RetirementTypeId != $data['retirement_type']) {
            $history_fields['Employee'][] = [
                'column_name' => 'RetirementTypeId',
                'old_value' => $emp->RetirementTypeId,
                'new_value' => $data['retirement_type']
            ];
        }
        if (isset($data['csrs_offset_date'])) {
            if ($emp->CSRSOffsetDate != date('Y-m-d H:i:s', strtotime($data['csrs_offset_date']))) {
                $history_fields['Employee'][] = [
                    'column_name' => 'CSRSOffsetDate',
                    'old_value' => $emp->CSRSOffsetDate,
                    'new_value' => date('Y-m-d H:i:s', strtotime($data['csrs_offset_date']))
                ];
            }
        }
        if (isset($data['fers_transfer_date'])) {
            if ($emp->FERSTransferDate != date('Y-m-d H:i:s', strtotime($data['fers_transfer_date']))) {
                $history_fields['Employee'][] = [
                    'column_name' => 'FERSTransferDate',
                    'old_value' => $emp->FERSTransferDate,
                    'new_value' => date('Y-m-d H:i:s', strtotime($data['fers_transfer_date']))
                ];
            }
        }
        if (isset($data['postal_employee'])) {
            if ($emp->PostalEmployee != $data['postal_employee']) {
                $history_fields['Employee'][] = [
                    'column_name' => 'FERSTransferDate',
                    'old_value' => $emp->FERSTransferDate,
                    'new_value' => date('Y-m-d H:i:s', strtotime($data['fers_transfer_date']))
                ];
            }
        }
        if ($emp->SpecialProvisionsDate != date('Y-m-d H:i:s', strtotime($data['special_provisions_date']))) {
            $history_fields['Employee'][] = [
                'column_name' => 'SpecialProvisionsDate',
                'old_value' => $emp->SpecialProvisionsDate,
                'new_value' => date('Y-m-d H:i:s', strtotime($data['special_provisions_date']))
            ];
        }
        if ($emp->EmployeeTypeId != $data['employee_type']) {
            $history_fields['Employee'][] = [
                'column_name' => 'EmployeeTypeId',
                'old_value' => $emp->EmployeeTypeId,
                'new_value' => $data['employee_type']
            ];
        }
        if (isset($data['other_employee_type'])) {
            if ($emp->OtherEmployeeTypeId != $data['other_employee_type']) {
                $history_fields['Employee'][] = [
                    'column_name' => 'OtherEmployeeTypeId',
                    'old_value' => $emp->OtherEmployeeTypeId,
                    'new_value' => $data['other_employee_type']
                ];
            }
        }
        if ($emp->MaritalStatusTypeId != $data['marital_status']) {
            $history_fields['Employee'][] = [
                'column_name' => 'MaritalStatusTypeId',
                'old_value' => $emp->MaritalStatusTypeId,
                'new_value' => $data['marital_status']
            ];
        }
        if ($emp->updated_by != Auth::user()->id) {
            $history_fields['Employee'][] = [
                'column_name' => 'updated_by',
                'old_value' => $emp->updated_by,
                'new_value' => Auth::user()->id
            ];
        }
        // dd($history_fields);

        $emp->EmployeeName = $data['name'];
        $emp->AdvisorId = $data['advisor'];
        $emp->DateReceived = date('Y-m-d H:i:s', strtotime($data['date_received']));
        $emp->DueDate = date('Y-m-d H:i:s', strtotime($data['due_date']));
        $emp->DateCompleted = ($data['date_completed'] != '') ? date('Y-m-d', strtotime($data['date_completed'])) : null;
        $emp->ReportDate = date('Y-m-d H:i:s', strtotime($data['report_year'] . '-' . $data['report_month'] . '-01'));
        $emp->EmployeeAddress = isset($data['address']) ? $data['address'] : '';
        $emp->SystemTypeId = $data['system'];
        $emp->RetirementTypeId = $data['retirement_type'];
        if (isset($data['csrs_offset_date'])) {
            $emp->CSRSOffsetDate = ($data['csrs_offset_date'] != '') ? date('Y-m-d H:i:s', strtotime($data['csrs_offset_date'])) : null;
        }
        if (isset($data['fers_transfer_date'])) {
            $emp->FERSTransferDate = ($data['fers_transfer_date'] != '') ? date('Y-m-d H:i:s', strtotime($data['fers_transfer_date'])) : null;
        }
        if (isset($data['postal_employee']) && ($data['postal_employee'] == 1)) {
            $emp->PostalEmployee = 1;
        } else {
            $emp->PostalEmployee = 0;
        }

        $emp->SpecialProvisionsDate = ($data['special_provisions_date'] != '') ? date('Y-m-d H:i:s', strtotime($data['special_provisions_date'])) : null;
        $emp->EmployeeTypeId = $data['employee_type'];
        $emp->OtherEmployeeTypeId = isset($data['other_employee_type']) ? $data['other_employee_type'] : null;
        $emp->MaritalStatusTypeId = $data['marital_status'];
        // $emp->SpouseName = isset($data['spouse_name']) ? $data['spouse_name'] : '';
        $emp->updated_by = Auth::user()->id;
        // $res = $this->updateHistory($empId, $history_fields, $row_id); // for testing
        if ($emp->save()) {
            $row_id = 0;
            $res = $this->updateHistory($empId, $history_fields, $row_id, 'basic_info');
            return true;
        }
        return false;
    }

    public function getEligibilityById($empId = null)
    {
        if ($empId != null) {
            $result = Eligibility::where('EmployeeId', $empId)->first();
            if ($result) {
                return $result->toArray();
            }
        }
        return [];
    }

    public function retirementEligibilityUpdate($empId = null, $data = [])
    {
        if (($empId != null) && !empty($data)) {
            $history_fields['Eligibility'] = [];
            $elig = Eligibility::find($empId);
            if (!$elig) {
                $elig = new Eligibility();
                $elig->EmployeeId = $empId;
            } else {
                if ($elig->DateOfBirth != date('Y-m-d H:i:s', strtotime($data['birthdate']))) {
                    $history_fields['Eligibility'][] = [
                        'column_name' => 'DateOfBirth',
                        'old_value' => $elig->DateOfBirth,
                        'new_value' => date('Y-m-d H:i:s', strtotime($data['birthdate']))
                    ];
                }
                if ($elig->LeaveSCD != date('Y-m-d H:i:s', strtotime($data['leave_scd']))) {
                    $history_fields['Eligibility'][] = [
                        'column_name' => 'LeaveSCD',
                        'old_value' => $elig->LeaveSCD,
                        'new_value' => date('Y-m-d H:i:s', strtotime($data['leave_scd']))
                    ];
                }
                if ($elig->RetirementDate != date('Y-m-d H:i:s', strtotime($data['retirement_date']))) {
                    $history_fields['Eligibility'][] = [
                        'column_name' => 'RetirementDate',
                        'old_value' => $elig->RetirementDate,
                        'new_value' => date('Y-m-d H:i:s', strtotime($data['retirement_date']))
                    ];
                }
            }
            // echo "<pre>"; print_r($elig); exit;
            $elig->DateOfBirth = date('Y-m-d H:i:s', strtotime($data['birthdate']));
            $elig->LeaveSCD = date('Y-m-d H:i:s', strtotime($data['leave_scd']));
            $elig->RetirementDate = date('Y-m-d H:i:s', strtotime($data['retirement_date']));
            if ($elig->save()) {
                $row_id = 0;
                $res = $this->updateHistory($empId, $history_fields, $row_id, 'retirement_eligibility');
                $scenarios = ReportScenario::where('EmployeeId', $empId)->get();
                if ($scenarios->count() == 0) {
                    $adder  = 0;
                    $workingDate = $elig->RetirementDate;

                    while ($adder < 5) :
                        $reportScenario = new ReportScenario();
                        $reportScenario->EmployeeId = $empId;
                        $reportScenario->ScenarioNo = $adder + 1;
                        $reportScenario->RetirementDate = $workingDate;
                        $reportScenario->IsSelected = ($adder == 0) ? 1 : 0;
                        $reportScenario->save();
                        $adder += 1;
                        $workingDate_obj = new \DateTime($workingDate);
                        $workingDate_new = $workingDate_obj->modify('+1 year');
                        $workingDate = $workingDate_new->format('Y-m-d H:i:s');
                    endwhile;
                }
                return true;
            }
        }
        return false;
    }

    public function saveMilitaryService($data = [])
    {
        if (!empty($data)) {
            $milService = new MilitaryService();
            $milService->EmployeeId = $data['employeeId'];
            $milService->MilitaryServiceTypeId = $data['military_service_type_id'];
            $milService->FromDate = $data['from_date'];
            $milService->ToDate = $data['to_date'];
            $milService->IsRetired = isset($data['is_retired']) ? $data['is_retired'] : 0;
            $milService->DepositOwed = isset($data['deposit_owned']) ? $data['deposit_owned'] : 0;
            $milService->AmountOwed = !empty($data['amount_owned']) ? $data['amount_owned'] : 0.00;
            if ($milService->save()) {
                return true;
            }
        }
        return false;
    }

    public function getMilitaryServicesByType($empId = null, $type = null)
    {
        if ($empId != null && $type != null) {
            $result = MilitaryService::where('EmployeeId', $empId)->where('MilitaryServiceTypeId', $type)->get();
            if (is_null($result)) {
                $result = [];
            } else {
                $result = $result->toArray();
            }
            return $result;
        }
        return false;
    }

    public function getNonDeductionService($empId = null)
    {
        if ($empId != null) {
            $result = NonDeductionService::where('EmployeeId', $empId)->get();
            return $result;
        }
        return false;
    }

    public function saveNonDeductionService($data = [])
    {
        $ndService = new NonDeductionService();
        $ndService->EmployeeId = $data['employeeId'];
        $ndService->FromDate = date('Y-m-d H:i:s', strtotime($data['from_date']));
        $ndService->ToDate = date('Y-m-d H:i:s', strtotime($data['to_date']));
        $ndService->DepositOwed = isset($data['deposit_owned']) ? $data['deposit_owned'] : 0;
        $ndService->AmountOwed = $data['amount_owned'];
        if ($ndService->save()) {
            return true;
        }
        return false;
    }

    public function saveRefundedServices($data = [])
    {
        $rService = new RefundedService();
        $rService->EmployeeId = $data['employeeId'];
        $rService->FromDate = date('Y-m-d H:i:s', strtotime($data['from_date']));
        $rService->ToDate = date('Y-m-d H:i:s', strtotime($data['to_date']));
        // $rService->Withdrawal = isset($data['withdrawal']) ? 1 : 0;
        $rService->Redeposit = isset($data['redeposit']) ? $data['redeposit'] : 0;
        $rService->AmountOwed = !empty($data['amount_owned']) ? $data['amount_owned'] : 0.00;
        if ($rService->save()) {
            return true;
        }
        return false;
    }

    public function getRefundedServices($empId = null)
    {
        return RefundedService::where('EmployeeId', $empId)->get();
    }

    public function getRefundedServiceById($serviceId = null)
    {
        if ($serviceId != null) {
            return RefundedService::where('RefundedServiceId', $serviceId)->first();
        }
    }

    public function updateRefundedService($serviceId = null,  $data = [])
    {
        if (($serviceId != null) && !empty($data)) {
            $rService = RefundedService::find($serviceId);
            $history_fields['RefundedService'] = [];
            if ($rService->FromDate != date('Y-m-d H:i:s', strtotime($data['from_date']))) {
                $history_fields['RefundedService'][] = [
                    'column_name' => 'FromDate',
                    'old_value' => $rService->FromDate,
                    'new_value' => date('Y-m-d H:i:s', strtotime($data['from_date']))
                ];
            }
            if ($rService->ToDate != date('Y-m-d H:i:s', strtotime($data['to_date']))) {
                $history_fields['RefundedService'][] = [
                    'column_name' => 'ToDate',
                    'old_value' => $rService->ToDate,
                    'new_value' => date('Y-m-d H:i:s', strtotime($data['to_date']))
                ];
            }
            $withdrawal = $data['withdrawal'] ?? 0;
            if ($rService->Withdrawal != $withdrawal) {
                $history_fields['RefundedService'][] = [
                    'column_name' => 'Withdrawal',
                    'old_value' => $rService->Withdrawal,
                    'new_value' => $withdrawal
                ];
            }
            $redeposit = $data['redeposit'] ?? 0;
            if ($rService->Redeposit != $redeposit) {
                $history_fields['RefundedService'][] = [
                    'column_name' => 'Redeposit',
                    'old_value' => $rService->Redeposit,
                    'new_value' => $redeposit
                ];
            }
            if ($rService->AmountOwed != $data['amount_owned']) {
                $history_fields['RefundedService'][] = [
                    'column_name' => 'AmountOwed',
                    'old_value' => $rService->AmountOwed,
                    'new_value' => $data['amount_owned']
                ];
            }
            $rService->FromDate = date('Y-m-d H:i:s', strtotime($data['from_date']));
            $rService->ToDate = date('Y-m-d H:i:s', strtotime($data['to_date']));
            $rService->Withdrawal = isset($data['withdrawal']) ? $data['withdrawal'] : 0;
            $rService->Redeposit = isset($data['redeposit']) ? $data['redeposit'] : 0;
            $rService->AmountOwed = !empty($data['amount_owned']) ? $data['amount_owned'] : 0.00;
            if ($rService->save()) {
                $row_id = $rService->RefundedServiceId;
                $res = $this->updateHistory($rService->EmployeeId, $history_fields, $row_id, 'retirement_eligibility');
                return true;
            }
        }
        return false;
    }

    public function getMilitaryServiceById($serviceId = null)
    {
        if ($serviceId != null) {
            return MilitaryService::where('MilitaryServiceId', $serviceId)->first();
        }
    }

    public function updateMilitaryService($serviceId = null, $data = [])
    {
        if (($serviceId != null) && !empty($data)) {
            // echo "<pre>";
            // print_r($data);
            // exit;
            $mService = MilitaryService::find($serviceId);
            $history_fields['MilitaryService'] = [];
            if ($mService->FromDate != date('Y-m-d H:i:s', strtotime($data['from_date']))) {
                $history_fields['MilitaryService'][] = [
                    'column_name' => 'FromDate',
                    'old_value' => $mService->FromDate,
                    'new_value' => date('Y-m-d H:i:s', strtotime($data['from_date']))
                ];
            }
            if ($mService->ToDate != date('Y-m-d H:i:s', strtotime($data['to_date']))) {
                $history_fields['MilitaryService'][] = [
                    'column_name' => 'ToDate',
                    'old_value' => $mService->ToDate,
                    'new_value' => date('Y-m-d H:i:s', strtotime($data['to_date']))
                ];
            }
            $is_retired = $data['is_retired'] ?? 0;
            if ($mService->IsRetired != $is_retired) {
                $history_fields['MilitaryService'][] = [
                    'column_name' => 'IsRetired',
                    'old_value' => $mService->IsRetired,
                    'new_value' => $is_retired
                ];
            }
            $deposit_owned = $data['deposit_owned'] ?? 0;
            if ($mService->DepositOwed != $deposit_owned) {
                $history_fields['MilitaryService'][] = [
                    'column_name' => 'DepositOwed',
                    'old_value' => $mService->DepositOwed,
                    'new_value' => $deposit_owned
                ];
            }
            if ($mService->AmountOwed != $data['amount_owned']) {
                $history_fields['MilitaryService'][] = [
                    'column_name' => 'AmountOwed',
                    'old_value' => $mService->AmountOwed,
                    'new_value' => $data['amount_owned']
                ];
            }
            $mService->FromDate = date('Y-m-d H:i:s', strtotime($data['from_date']));
            $mService->ToDate = date('Y-m-d H:i:s', strtotime($data['to_date']));
            // $mService->IsRetired = $data['is_retired'] ?? 0;
            $mService->DepositOwed = $data['deposit_owned'] ?? 0;
            $mService->AmountOwed = $data['amount_owned'];
            if ($mService->save()) {
                $row_id = $mService->MilitaryServiceId;
                $res = $this->updateHistory($mService->EmployeeId, $history_fields, $row_id, 'retirement_eligibility');
                return true;
            }
        }
        return false;
    }

    public function getNonDeductionServiceById($serviceId = null)
    {
        if ($serviceId != null) {
            return NonDeductionService::where('NonDeductionServiceId', $serviceId)->first();
        }
        return false;
    }

    public function updateNonDeductionService($serviceId = null, $data = [])
    {
        if (($serviceId != null) && !empty($data)) {
            $ndService = NonDeductionService::find($serviceId);
            $history_fields['NonDeductionService'] = [];
            if ($ndService->FromDate != date('Y-m-d H:i:s', strtotime($data['from_date']))) {
                $history_fields['NonDeductionService'][] = [
                    'column_name' => 'FromDate',
                    'old_value' => $ndService->FromDate,
                    'new_value' => date('Y-m-d H:i:s', strtotime($data['from_date']))
                ];
            }
            if ($ndService->ToDate != date('Y-m-d H:i:s', strtotime($data['to_date']))) {
                $history_fields['NonDeductionService'][] = [
                    'column_name' => 'ToDate',
                    'old_value' => $ndService->ToDate,
                    'new_value' => date('Y-m-d H:i:s', strtotime($data['to_date']))
                ];
            }
            // if ($ndService->DepositOwed != $data['deposit_owned']) {
            //     $history_fields['NonDeductionService'][] = [
            //         'column_name' => 'DepositOwed',
            //         'old_value' => $ndService->DepositOwed,
            //         'new_value' => $data['deposit_owned']
            //     ];
            // }
            if ($ndService->AmountOwed != $data['amount_owned']) {
                $history_fields['NonDeductionService'][] = [
                    'column_name' => 'AmountOwed',
                    'old_value' => $ndService->AmountOwed,
                    'new_value' => $data['amount_owned']
                ];
            }
            $ndService->FromDate = date('Y-m-d H:i:s', strtotime($data['from_date']));
            $ndService->ToDate = date('Y-m-d H:i:s', strtotime($data['to_date']));
            $ndService->DepositOwed = isset($data['deposit_owned']) ? $data['deposit_owned'] : 0;
            $ndService->AmountOwed = $data['amount_owned'];
            if ($ndService->save()) {
                $row_id = $ndService->NonDeductionServiceId;
                $res = $this->updateHistory($ndService->EmployeeId, $history_fields, $row_id, 'retirement_eligibility');
                return true;
            }
        }
        return false;
    }

    public function getPartTimeServices($empId = null)
    {
        return PartTimeService::where('EmployeeId', $empId)->get();
    }

    public function savePartTimeService($empId = null, $data = [])
    {
        if (($empId != null) && !empty($data)) {
            $result = false;
            // echo "<pre>"; print_r($data); exit;
            if (isset($data['percentage']) && $data['percentage'] != NULL) {
                $ptObj = new PartTimeService();
                $ptObj->EmployeeId = $empId;
                $ptObj->percentage = $data['percentage'];
                $ptObj->FromDate = NULL;
                $ptObj->ToDate = NULL;
                $ptObj->HoursWeek = 0;
                if ($ptObj->save()) {
                    $result = true;
                }
            } else {
                for ($i = 0; $i < count($data['from_date']); $i++) {
                    if ($data['from_date'][$i] == "" || $data['to_date'][$i] == "" || $data['hours_weekly'][$i] == "") {
                        return false;
                    }
                    $ptObj = new PartTimeService();
                    $ptObj->EmployeeId = $empId;
                    $ptObj->FromDate = date('Y-m-d H:i:s', strtotime($data['from_date'][$i]));
                    $ptObj->ToDate = date('Y-m-d H:i:s', strtotime($data['to_date'][$i]));
                    $ptObj->HoursWeek = $data['hours_weekly'][$i];
                    $ptObj->percentage = 0;
                    if ($ptObj->save()) {
                        $result = true;
                    }
                }
            }
        }
        if ($result) {
            return true;
        }
        return false;
    }

    public function getPartTimeService($serviceId = null)
    {
        if ($serviceId != null) {
            return PartTimeService::where('PartTimeServiceId', $serviceId)->first();
        }
    }

    public function updatePartTimeService($serviceId = null, $data = [])
    {
        if (($serviceId != null) && !empty($data)) {
            $ptObj = PartTimeService::find($serviceId);
            if ($ptObj) {
                // $ptObj->FromDate = $data['from_date'];
                // $ptObj->ToDate = $data['to_date'];
                // $ptObj->HoursWeek = $data['hours_weekly'];
                $history_fields['PartTimeService'] = [];
                if ($ptObj->percentage != $data['percentage']) {
                    $history_fields['PartTimeService'][] = [
                        'column_name' => 'percentage',
                        'old_value' => $ptObj->percentage,
                        'new_value' => $data['percentage']
                    ];
                }
                $from_date = ($data['from_date'] != null) ? date('Y-m-d H:i:s', strtotime($data['from_date'])) : "";
                if ($ptObj->FromDate != $from_date) {
                    $history_fields['PartTimeService'][] = [
                        'column_name' => 'FromDate',
                        'old_value' => $ptObj->FromDate,
                        'new_value' => $from_date
                    ];
                }
                $to_date = ($data['from_date'] != null) ? date('Y-m-d H:i:s', strtotime($data['to_date'])) : "";
                if ($ptObj->ToDate != $to_date) {
                    $history_fields['PartTimeService'][] = [
                        'column_name' => 'ToDate',
                        'old_value' => $ptObj->ToDate,
                        'new_value' => $to_date
                    ];
                }
                if ($ptObj->HoursWeek != $data['hours_weekly']) {
                    $history_fields['PartTimeService'][] = [
                        'column_name' => 'HoursWeek',
                        'old_value' => $ptObj->HoursWeek,
                        'new_value' => $data['hours_weekly']
                    ];
                }

                if (isset($data['percentage']) && $data['percentage']  != '' && $data['percentage']  != 0) {
                    $ptObj->percentage = $data['percentage'];
                    $ptObj->FromDate = NULL;
                    $ptObj->ToDate = NULL;
                    $ptObj->HoursWeek = 0;
                } else {
                    if ($data['from_date'] == "" || $data['to_date'] == "" || $data['hours_weekly'] == "") {
                        return false;
                    }
                    $ptObj->FromDate = date('Y-m-d H:i:s', strtotime($data['from_date']));
                    $ptObj->ToDate = date('Y-m-d H:i:s', strtotime($data['to_date']));
                    $ptObj->HoursWeek = $data['hours_weekly'];
                    $ptObj->percentage = 0;
                }
                if ($ptObj->save()) {
                    $row_id = $ptObj->PartTimeServiceId;
                    $res = $this->updateHistory($ptObj->EmployeeId, $history_fields, $row_id, 'part_time_service');
                    return true;
                }
            }
        }
        return false;
    }

    public function deletePartTimeService($serviceId)
    {
        return PartTimeService::where('PartTimeServiceId', $serviceId)->delete();
    }

    public function getEmployeeConfig($empId = null, $configType = null)
    {
        if (($configType != null) && ($empId != null)) {
            return EmployeeConfig::where('EmployeeId', $empId)->where('ConfigType', $configType)->first();
        }
        return false;
    }

    public function getSystemConfiuration($appLookupTypeName = null, $appLookupName = null)
    {
        if ($appLookupTypeName != null && $appLookupName != null) {
            return AppLookup::where('AppLookupTypeName', $appLookupTypeName)->where('AppLookupName', $appLookupName)->first();
        }
        return false;
    }

    public function getRetirementType($a)
    {
        //pr($a);
        //if($appLookupTypeID != null) {
        return AppLookup::whereIn('AppLookupId', $a)->get()->toArray();
        //}
        return false;
    }

    public function getReductionOptions($type = null)
    {
        // ['basic', 'optionA', 'optionB', 'optionC']
        if ($type == 'basic') {
            $appLookupName = 'BasicReduction';
        } elseif ($type == 'optionA') {
            $appLookupName = 'OptionAReduction';
        } elseif ($type == 'optionB') {
            $appLookupName = 'OptionBReduction';
        } elseif ($type == 'optionC') {
            $appLookupName = 'OptionCReduction';
        } else {
            $appLookupName = 'BasicReduction';
        }
        $result = AppLookup::where('AppLookupTypeName', 'ReductionAfterRetirement')->where('AppLookupName', $appLookupName)->get();
        return $result;
    }

    public function updateEmployeeFegli($empId = null, $data = [])
    {
        if (!empty($data)) {
            // echo $data['basic_amount']; exit;
            $employee = Employee::find($empId);
            if (!$employee) {
                return [
                    'status' => false,
                    'message' => 'Invalid Employee Id'
                ];
            }
            $emp = $employee->toArray();
            // dd($emp);
            $basicAmount = isset($data['include_basic']) ? $data['basic_amount'] ?? 0.00 : 0.00;
            $optionAAmount = isset($data['include_optionA']) ? $data['optionA_amount'] ?? 0.00 : 0.00;
            $optionBAmount = isset($data['include_optionB']) ? $data['optionB_amount'] ?? 0.00 : 0.00;
            $optionCAmount = 0.00;
            $total = 0;


            // echo "basicAmount: " . $basicAmount . "<br>";
            // echo "optionAAmount: " . $optionAAmount . "<br>";
            // echo "optionBAmount: " . $optionBAmount . "<br>";
            // echo "optionCAmount: " . $optionCAmount . "<br>";
            // echo $total; die;
            $history_fields['FEGLI'] = [];
            $fegliObj = Fegli::find($empId);

            if (!($fegliObj)) {
                $fegliObj = new Fegli();
            } else {
                if ($emp['MaritalStatusType'] == "Married") {
                    $optionCAmount = isset($data['include_optionC']) ? $data['optionC_amount'] ?? 0.00 : 0.00;
                } else {
                    $valid_fegli_dependents = [];
                    if ($fegliObj->fegliDependent->count() > 0) {
                        foreach ($fegliObj->fegliDependent as $child) {
                            // echo $child->age . "<br>";
                            if (isset($child->age) && ($child->age > 0)) {
                                $child_age = $child->age;
                            } else {
                                $child_age = date('Y') - date('Y', strtotime($child['DateOfBirth']));
                            }
                            if ($child_age < 22) {
                                array_push($valid_fegli_dependents, $child_age);
                            } else {
                                if ($child->CoverAfter22 == 1) {
                                    array_push($valid_fegli_dependents, $child_age);
                                }
                            }
                        }
                    }
                    if (isset($data['include_optionC'])) {
                        if (count($valid_fegli_dependents) > 0) {
                            $optionCAmount = isset($data['include_optionC']) ? $data['optionC_amount'] ?? 0.00 : 0.00;
                        } else {
                            $fegliObj->OptionCInc = 0;
                            $fegliObj->OptionCAmount = 0;
                            $fegliObj->save();
                            return [
                                'status' => false,
                                'message' => 'Employee who is unmarried and have no dependents cannot select optionC.'
                            ];
                        }
                    } else {
                        $optionCAmount = 0.00;
                    }
                }

                $total = $basicAmount + $optionAAmount + $optionBAmount + $optionCAmount;

                $basic_inc = isset($data['include_basic']) ? 1 : 0;
                if ($fegliObj->BasicInc != $basic_inc) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'BasicInc',
                        'old_value' => $fegliObj->BasicInc,
                        'new_value' => $basic_inc
                    ];
                }
                if ($fegliObj->BasicAmount != $basicAmount) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'BasicAmount',
                        'old_value' => $fegliObj->BasicAmount,
                        'new_value' => $basicAmount
                    ];
                }
                if ($fegliObj->basicReductionAfterRetirement != $data['basicReductionAfterRetirement']) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'basicReductionAfterRetirement',
                        'old_value' => $fegliObj->basicReductionAfterRetirement,
                        'new_value' => $data['basicReductionAfterRetirement']
                    ];
                }
                $optionA_inc = isset($data['include_optionA']) ? 1 : 0;
                if ($fegliObj->OptionAInc != $optionA_inc) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'OptionAInc',
                        'old_value' => $fegliObj->OptionAInc,
                        'new_value' => $optionA_inc
                    ];
                }
                if ($fegliObj->OptionAAmount != $optionAAmount) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'OptionAAmount',
                        'old_value' => $fegliObj->OptionAAmount,
                        'new_value' => $optionAAmount
                    ];
                }
                if ($fegliObj->optionAReductionAfterRetirement != $data['optionAReductionAfterRetirement']) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'optionAReductionAfterRetirement',
                        'old_value' => $fegliObj->optionAReductionAfterRetirement,
                        'new_value' => $data['optionAReductionAfterRetirement']
                    ];
                }
                $optionB_inc = isset($data['include_optionB']) ? 1 : 0;
                if ($fegliObj->OptionBInc != $optionB_inc) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'OptionBInc',
                        'old_value' => $fegliObj->OptionBInc,
                        'new_value' => $optionB_inc
                    ];
                }
                if ($fegliObj->OptionBMultiplier != $data['b_multiplier']) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'OptionBMultiplier',
                        'old_value' => $fegliObj->OptionBMultiplier,
                        'new_value' => $data['b_multiplier']
                    ];
                }
                if ($fegliObj->OptionBAmount != $optionBAmount) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'OptionBAmount',
                        'old_value' => $fegliObj->OptionBAmount,
                        'new_value' => $optionBAmount
                    ];
                }
                if ($fegliObj->optionBReductionAfterRetirement != $data['optionBReductionAfterRetirement']) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'optionBReductionAfterRetirement',
                        'old_value' => $fegliObj->optionBReductionAfterRetirement,
                        'new_value' => $data['optionBReductionAfterRetirement']
                    ];
                }
                $optionC_inc = isset($data['include_optionC']) ? 1 : 0;
                if ($fegliObj->OptionCInc != $optionC_inc) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'OptionCInc',
                        'old_value' => $fegliObj->OptionCInc,
                        'new_value' => $optionC_inc
                    ];
                }
                if ($fegliObj->OptionCMultiplier != $data['c_multiplier']) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'OptionCMultiplier',
                        'old_value' => $fegliObj->OptionCMultiplier,
                        'new_value' => $data['c_multiplier']
                    ];
                }
                if ($fegliObj->OptionCAmount != $optionCAmount) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'OptionCAmount',
                        'old_value' => $fegliObj->OptionCAmount,
                        'new_value' => $optionCAmount
                    ];
                }
                if ($fegliObj->optionCReductionAfterRetirement != $data['optionCReductionAfterRetirement']) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'optionCReductionAfterRetirement',
                        'old_value' => $fegliObj->optionCReductionAfterRetirement,
                        'new_value' => $data['optionCReductionAfterRetirement']
                    ];
                }
                if ($fegliObj->TotalAmount != $total) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'TotalAmount',
                        'old_value' => $fegliObj->TotalAmount,
                        'new_value' => $total
                    ];
                }
                if ($fegliObj->SalaryForFEGLI != $data['salary_override']) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'SalaryForFEGLI',
                        'old_value' => $fegliObj->SalaryForFEGLI,
                        'new_value' => $data['salary_override']
                    ];
                }
                $doesNotMeetFiveYear = isset($data['does_meet_five_year']) ? 1 : 0;
                if ($fegliObj->DoesNotMeetFiveYear = $doesNotMeetFiveYear) {
                    $history_fields['FEGLI'][] = [
                        'column_name' => 'DoesNotMeetFiveYear',
                        'old_value' => $fegliObj->DoesNotMeetFiveYear,
                        'new_value' => $doesNotMeetFiveYear
                    ];
                }
            }

            // echo $empId . "<br><pre>"; print_r($data); exit;
            $fegliObj->EmployeeId = $empId;
            $fegliObj->BasicInc = isset($data['include_basic']) ? 1 : 0;
            $fegliObj->BasicAmount = $basicAmount;
            $fegliObj->basicReductionAfterRetirement = isset($data['basicReductionAfterRetirement']) ? $data['basicReductionAfterRetirement'] : 0;
            $fegliObj->OptionAInc = isset($data['include_optionA']) ? 1 : 0;
            $fegliObj->OptionAAmount = $optionAAmount;
            $fegliObj->optionAReductionAfterRetirement = isset($data['optionAReductionAfterRetirement']) ? $data['optionAReductionAfterRetirement'] : 0;
            $fegliObj->OptionBInc = isset($data['include_optionB']) ? 1 : 0;
            $fegliObj->OptionBMultiplier = $data['b_multiplier'] ?? 0;
            $fegliObj->OptionBAmount = $optionBAmount;
            $fegliObj->optionBReductionAfterRetirement = isset($data['optionBReductionAfterRetirement']) ? $data['optionBReductionAfterRetirement'] : 0;
            $fegliObj->OptionCInc = isset($data['include_optionC']) ? 1 : 0;
            $fegliObj->OptionCMultiplier = $data['c_multiplier'] ?? 0;
            $fegliObj->OptionCAmount = $optionCAmount;
            $fegliObj->optionCReductionAfterRetirement = isset($data['optionCReductionAfterRetirement']) ? $data['optionCReductionAfterRetirement'] : 0;
            $fegliObj->TotalAmount = $total;
            $fegliObj->SalaryForFEGLI = isset($data['salary_override']) ? $data['salary_override'] : 0.00;
            $fegliObj->DoesNotMeetFiveYear = isset($data['does_meet_five_year']) ? 1 : 0;
            if ($fegliObj->save()) {
                $row_id = 0;
                $res = $this->updateHistory($empId, $history_fields, $row_id, 'fegli');
                return [
                    'status' => true,
                    'message' => 'FEGLI updated successfully'
                ];
            }
        }
        return [
            'status' => false,
            'message' => 'Empty Request Data'
        ];
    }

    public function getFegliReport($data)
    {
        $fegli_arr = array();
        $biweekly_arr = array();
        $monthly_arr = array();
        $current_fegli['basic'] = 0;
        $current_fegli['optionA'] = 0;
        $current_fegli['optionB'] = 0;
        $current_fegli['optionC'] = 0;
        $current_fegli['optionC_spouse'] = 0;
        $current_fegli['optionC_dependent'] = 0;
        // dd($data);
        if ($data['employee']['MaritalStatusType'] == "Married") {
            $is_married = 1;
        } else {
            $is_married = 0;
        }
        if (!empty($data)) {
            $empId = $data['EmployeeId'];
            if ($data['SalaryForFEGLI'] == 0) {
                $current_sal = $data['employee']['CurrentSalary'];
            } else {
                $current_sal = $data['SalaryForFEGLI'];
            }

            $reportYear = date('Y', strtotime($data['employee']['ReportDate']));
            if ($reportYear < date('Y')) {
                $current_sal = $this->getCurrentSalary($data['employee'], $current_sal);
            }
            /* Employee annual increse in salary */

            $empAnualIncrease = resolve('employee')->getEmployeeConfig($empId, 'SalaryIncreaseDefault');

            if (!$empAnualIncrease) {
                $empAnualIncrease = resolve('employee')->getSystemConfiuration('EmployeeConfig', 'SalaryIncreaseDefault')->AppLookupDescription;
            } else {
                $empAnualIncrease = $empAnualIncrease->ConfigValue;
            }

            /* Employee age */
            $dtnow = new \DateTime();
            $dtbday = new \DateTime($data['employee']['eligibility']['DateOfBirth']);
            $interval = $dtnow->diff($dtbday);
            $emp_age = $interval->y;
            $emp_age_month = $interval->m;

            /* Employee Retirement age */
            $retirement_year = date('Y', strtotime($data['employee']['eligibility']['RetirementDate']));
            $dtRet = new \DateTime($data['employee']['eligibility']['RetirementDate']);
            $retInterval = $dtRet->diff($dtbday);
            $retirementAge = $retInterval->y;
            $retirementAge_month = $retInterval->m;

            $rtnow = new \DateTime();
            $rtDate = new \DateTime($data['employee']['eligibility']['RetirementDate']);
            /* Years in retirement */
            if ($retirement_year > date('Y')) {
                $yearsInRet = 0;
            } elseif ($retirement_year == date('Y')) {
                $yearsInRet = '*';
            } else {
                $yearsInRet = date('Y') - $retirement_year;
            }

            /* calculate salary for premium */
            $fegli_empage = $emp_age;
            $salForPremium = app('Common')->calcSalForPremium($current_sal);

            $newSal = $current_sal;
            /* ****loop starts here**** */

            $runningTotalCost = 0;
            if ($data['OptionAInc'] == 1) {
                $optionACoverage = 10000;
                $reducedCoverageOptionA = 10000;
            } else {
                $optionACoverage = 0;
                $reducedCoverageOptionA = 0;
            }
            $current_fegli['basic'] = 0;
            $current_fegli['optionA'] = 0;
            $current_fegli['optionB'] = 0;
            $current_fegli['optionC'] = 0;
            $last_salary = $newSal;

            $fegli_dependents = [];
            if (count($data['fegli_dependent']) > 0) {
                foreach ($data['fegli_dependent'] as $child) {
                    $child_row = [];
                    if (isset($child['age']) && ($child['age'] > 0)) {
                        $child_age = $child['age'];
                    } else {
                        $child_age = date('Y') - date('Y', strtotime($child['DateOfBirth']));
                    }
                    $child_row['CoverAfter22'] = $child['CoverAfter22'];
                    $child_row['child_age'] = $child_age;
                    array_push($fegli_dependents, $child_row);
                }
            }
            $retMonth = (int) date('m', strtotime($data['employee']['eligibility']['RetirementDate']));
            $bdayMonth = (int) date('m', strtotime($data['employee']['eligibility']['DateOfBirth']));

            $bdayYearObj = new \DateTime($data['employee']['eligibility']['DateOfBirth']);
            // if ($emp_age == 65) {
            //     $last_age = 89;
            // } else {
            //     $last_age = 90;
            // }
            if (($emp_age <= 65) && ($emp_age > 62)) {
                $bdayYearObj->modify('+ 87 years');
            } else {
                $bdayYearObj->modify('+ 90 years');
            }
            $last_year = $bdayYearObj->format('Y');

            // $retirement_year
            $year_at_age65 = $dtbday = new \DateTime($data['employee']['eligibility']['DateOfBirth']);
            $year_at_age65 = $year_at_age65->modify('+ 65 years');
            $year_at_age65 = $year_at_age65->format('Y');
            // dd($data);
            for ($i = date('Y'); $i <= $last_year; $i++) {
                // // // for ($i = $emp_age; $i <= $last_age; $i++) {
                $premium_costs = app('Common')->getPremiumCostMultiplierFEGLI($fegli_empage);
                $premium_costs_old = app('Common')->getPremiumCostMultiplierFEGLI_old($fegli_empage);

                $premium_costs_biweekly = app('Common')->getPremiumCostMultiplierFEGLIBiWeekly($fegli_empage);

                $premium_costs_biweekly_old = app('Common')->getPremiumCostMultiplierFEGLIBiWeekly_old($fegli_empage);

                if ($fegli_empage == $retirementAge) {
                    $yearsInRet = '*';
                }
                if ($fegli_empage > $retirementAge) {
                    $yearsInRet++;
                }

                $fegli_row['years_in_retirement'] = $yearsInRet;
                if ($fegli_empage == $retirementAge) {
                    $yearsInRet = 0;
                    $dtbday_obj = new \DateTime($data['employee']['eligibility']['DateOfBirth']);
                    $bday_year = date('Y', strtotime($data['employee']['eligibility']['DateOfBirth']));
                }

                if ($i > $retirement_year) {
                    $newSal = 0;
                }

                $fegli_row['age'] = $fegli_empage;

                $retirement_row_second = []; // to have clear idea when salary changed during retirement period
                if ($newSal > 0) {
                    if ($i <= $retirement_year && $i > date('Y')) {
                        if ($i == $retirement_year) {
                            if (($retMonth == 1) && ($dtRet->format('d') < 14)) {
                                $newSal = 0;
                            } else {
                                $newSal = $newSal + ($newSal * ($empAnualIncrease / 100));
                                $salForPremium = app('Common')->calcSalForPremium($newSal);
                                $last_salary = $newSal;
                            }
                        } elseif ($i < $retirement_year) {
                            $newSal = $newSal + ($newSal * ($empAnualIncrease / 100));
                            $salForPremium = app('Common')->calcSalForPremium($newSal);
                        } else {
                            $newSal = 0;
                            $salForPremium = 0;
                        }
                    }
                }

                $fegli_row['annual_sal'] = $newSal;
                if ($i <= $retirement_year) {
                    if ($i == $retirement_year) {
                        if (($retMonth == 1) && ($dtRet->format('d') < 14)) {
                            //
                        } else {
                            $last_salary = $newSal;
                        }
                    } else {
                        $last_salary = $newSal;
                    }
                }

                /* BASIC Coverage and premium Cost */
                $basicCoverage = 0;
                if ((($data['employee']['retirementType'] == "Deferred") || ($data['DoesNotMeetFiveYear'] == 1)) && ($i > $retirement_year)) {
                    // $current_fegli['basic'] = 0;
                    // $current_fegli['optionA'] = 0;
                    // $current_fegli['optionB'] = 0;
                    // $current_fegli['optionC_spouse'] = 0;
                    // $current_fegli['optionC_dependent'] = 0;

                    $biWeeklyCost['basic'] = 0;
                    $biWeeklyCost['optionA'] = 0;
                    $biWeeklyCost['optionB'] = 0;
                    $biWeeklyCost['optionC'] = 0;

                    $monthlyCost['basic'] = 0;
                    $monthlyCost['optionA'] = 0;
                    $monthlyCost['optionB'] = 0;
                    $monthlyCost['optionC'] = 0;
                    $biWeeklyCost['total'] = 0;
                    $monthlyCost['total'] = 0;
                    $fegli_row['total_coverage'] = 0;
                    $fegli_row['totalBiWeekly'] = 0;
                    $fegli_row['totalMonthly'] = 0;
                    $fegli_row['totalYearly'] = 0;
                    $fegli_row['runningTotalCost'] = 0;
                    $fegli_row['costs'] = 0;

                    if ($i == date('Y')) {
                        $basicCoverage = round($data['BasicAmount']);
                        $optionACoverage = round($data['OptionAAmount']);
                        $optionBCoverage = round($data['OptionBAmount']);
                        $optionCCoverage = round($data['OptionCAmount']);
                    } else {
                        $basicCoverage = 0;
                        $optionACoverage = 0;
                        $optionBCoverage = 0;
                        $optionCCoverage = 0;
                    }
                    $fegli_row['basic_coverage'] = 0;
                    $cost['basic']['monthly'] = 0;
                    $cost['basic']['BiWeekly'] = 0;
                    $cost['basic']['yearly'] = 0;
                    $oldCostBiWeekly['basic'] = 0;


                    $fegli_row['optionA_coverage'] = 0;
                    $cost['optionA']['monthly'] = 0;
                    $cost['optionA']['BiWeekly'] = 0;
                    $cost['optionA']['yearly'] = 0;
                    $oldCostBiWeekly['optionA'] = 0;

                    $fegli_row['optionB_coverage'] = 0;
                    $cost['optionB']['monthly'] = 0;
                    $cost['optionB']['BiWeekly'] = 0;
                    $cost['optionB']['yearly'] = 0;
                    $oldCostBiWeekly['optionB'] = 0;

                    $fegli_row['optionC_coverage'] = 0;
                    $cost['optionC']['monthly'] = 0;
                    $cost['optionC']['BiWeekly'] = 0;
                    $cost['optionC']['yearly'] = 0;
                    $oldCostBiWeekly['optionC'] = 0;
                } else {
                    if ($data['BasicInc'] == 1) {
                        if ($i <= $retirement_year) {
                            $basicPremiumMultiplier = app('Common')->basicCoverageMultiplier($fegli_empage);
                            $basicCoverage = app('Common')->calcBasicCoverageFegli($data['BasicInc'], $salForPremium, $basicPremiumMultiplier);
                            if (isset($data['employee']['PostalEmployee']) && ($data['employee']['PostalEmployee'] == 1)) {
                                $monthlyCostForBasicPremium = 0;
                                $oldMonthlyCostForBasicPremium = 0;
                                $yearlyCostForBasicPremium = 0;
                                $oldYearlyCostForBasicPremium = 0;
                                $BiWeeklycostForBasicPremium = 0;
                                $oldBiWeeklyCostForBasicPremium = 0;
                            } else {
                                $monthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium_conf($data['BasicInc'], $salForPremium, $fegli_empage);

                                $yearlyCostForBasicPremium = $monthlyCostForBasicPremium * 12;
                                $BiWeeklycostForBasicPremium = $yearlyCostForBasicPremium / 26; // BiWeekly cost

                                $oldMonthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium_old($data['BasicInc'], $salForPremium);
                                $oldYearlyCostForBasicPremium = $oldMonthlyCostForBasicPremium * 12;
                                $oldBiWeeklyCostForBasicPremium = $oldYearlyCostForBasicPremium / 26;
                            }
                            if (($i == date('Y')) || ($i == $retirement_year)) {
                                $month_now = (int) date('m');
                                if ($rtnow < $rtDate) {
                                    if (isset($data['employee']['PostalEmployee']) && ($data['employee']['PostalEmployee'] == 1)) {
                                        $monthlyCostForBasicPremium = 0;
                                        $yearlyCostForBasicPremium = 0;
                                        $BiWeeklycostForBasicPremium = 0;
                                        $oldBiWeeklyCostForBasicPremium = 0;
                                    }
                                } else {
                                    if ($data['basicReductionAfterRetirement'] == 0) {
                                        $monthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremiumZeroReduction_conf($fegli_empage, $basicCoverage); // BiWeekly cost
                                        $oldMonthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremiumZeroReduction_old($fegli_empage, $basicCoverage);
                                    } elseif ($data['basicReductionAfterRetirement'] == 50) {
                                        $monthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium50Reduction_conf($fegli_empage, $basicCoverage);
                                        $oldMonthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium50Reduction_old($fegli_empage, $basicCoverage);
                                    } else {
                                        $monthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium75Reduction_conf($fegli_empage, $basicCoverage);
                                        $oldMonthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium75Reduction_old($fegli_empage, $basicCoverage);
                                    }
                                    $yearlyCostForBasicPremium = $monthlyCostForBasicPremium * 12;
                                    $BiWeeklycostForBasicPremium = $yearlyCostForBasicPremium / 26;

                                    $oldYearlyCostForBasicPremium = $oldMonthlyCostForBasicPremium * 12;
                                    $oldBiWeeklyCostForBasicPremium = $oldYearlyCostForBasicPremium / 26;
                                }
                            }
                        } else {

                            if ($data['basicReductionAfterRetirement'] == 0) {
                                $basicCoverage = $salForPremium + 2000;
                                $monthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremiumZeroReduction_conf($fegli_empage, $basicCoverage);
                                $oldMonthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremiumZeroReduction_old($fegli_empage, $basicCoverage);
                            } elseif ($data['basicReductionAfterRetirement'] == 50) {

                                $basicPremiumMultiplier = app('Common')->basicCoverageMultiplier($fegli_empage);
                                $maxCoverage = $salForPremium + 2000;
                                $reducedCoverageBasic = $maxCoverage;
                                if ($fegli_empage < 65) {
                                    $basicCoverage = $salForPremium + 2000;
                                    $oldMonthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium50Reduction_old($fegli_empage, $basicCoverage);
                                } else {
                                    // here comes if age is 65 or more after retirement
                                    $minCoverage = (50 / 100) * $maxCoverage;
                                    $reductionAmount = $maxCoverage / 100;
                                    if ($fegli_empage == 65) {
                                        $reducedCoverageBasic = $maxCoverage;
                                    } else {
                                        if ($fegli_empage == $emp_age) {
                                            $exceedYears = $fegli_empage - 65;
                                            $reducedCoverageBasic = $maxCoverage;
                                            for ($ag = 65; $ag <= 65 + $exceedYears; $ag++) {
                                                for ($mnt = $dtnow->format('m'); $mnt <= 12; $mnt++) {
                                                    if ($reducedCoverageBasic > $minCoverage) {
                                                        $reducedCoverageBasic = $reducedCoverageBasic - $reductionAmount;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    for ($mnt = $dtnow->format('m'); $mnt <= 12; $mnt++) {
                                        if ($reducedCoverageBasic > $minCoverage) {
                                            $reducedCoverageBasic = $reducedCoverageBasic - $reductionAmount;
                                        }
                                        if ($reducedCoverageBasic == $minCoverage) {
                                            $lastAgeOfReduction = $i;
                                            $lastMonthOfReduction = $mnt;
                                        } else {
                                            $lastAgeOfReduction = 0;
                                            $lastMonthOfReduction = 0;
                                        }
                                    }
                                    $basicCoverage = $reducedCoverageBasic;
                                    $monthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium50Reduction_conf($i, $maxCoverage);
                                    $oldMonthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium50Reduction_old($fegli_empage, $basicCoverage);
                                }
                            } else {
                                // ***** Basic 75 % reduction. default seclted *****
                                if ($fegli_empage < 65) {
                                    $basicCoverage = $salForPremium + 2000;
                                    $oldMonthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium75Reduction_old($fegli_empage, $basicCoverage);
                                } else {
                                    $maxCoverage = $salForPremium + 2000;
                                    $minCoverage = (25 / 100) * $maxCoverage;
                                    $reductionAmount = $maxCoverage * (2 / 100);
                                    $reducedCoverageBasic = $maxCoverage;

                                    if ($fegli_empage == 65) {
                                        $reducedCoverageBasic = $maxCoverage;
                                    } else {
                                        if ($i == date('Y')) {
                                            $exceedYears = $fegli_empage - 65;
                                            $reducedCoverageBasic = $maxCoverage;
                                            for ($ag = 65; $ag <= 65 + $exceedYears; $ag++) {
                                                for ($mnt = $dtnow->format('m'); $mnt <= 12; $mnt++) {
                                                    if ($reducedCoverageBasic > $minCoverage) {
                                                        $reducedCoverageBasic = $reducedCoverageBasic - $reductionAmount;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    for ($mnt = $dtnow->format('m'); $mnt <= 12; $mnt++) {
                                        if ($reducedCoverageBasic > $minCoverage) {
                                            $reducedCoverageBasic = $reducedCoverageBasic - $reductionAmount;
                                        } elseif ($reducedCoverageBasic <= $minCoverage) {
                                            $lastAgeOfReduction = $fegli_empage;
                                            $lastMonthOfReduction = $mnt;
                                            break;
                                        } else {
                                            $lastAgeOfReduction = 0;
                                            $lastMonthOfReduction = 0;
                                            break;
                                        }
                                    }
                                    $basicCoverage = $reducedCoverageBasic;
                                    $monthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium75Reduction_conf($fegli_empage, $maxCoverage);
                                    $oldMonthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremium75Reduction_old($fegli_empage, $basicCoverage);
                                }
                            }
                        }

                        $yearlyCostForBasicPremium = $monthlyCostForBasicPremium * 12;
                        $BiWeeklycostForBasicPremium = $yearlyCostForBasicPremium / 26; // BiWeekly cost

                        $oldYearlyCostForBasicPremium = $oldMonthlyCostForBasicPremium * 12;
                        $oldBiWeeklyCostForBasicPremium = $oldYearlyCostForBasicPremium / 26;


                        $fegli_row['basic_coverage'] = $basicCoverage;
                        $cost['basic']['monthly'] = $monthlyCostForBasicPremium;
                        $cost['basic']['BiWeekly'] = $BiWeeklycostForBasicPremium;
                        $cost['basic']['yearly'] = $yearlyCostForBasicPremium;
                        $oldCostBiWeekly['basic'] = $oldBiWeeklyCostForBasicPremium;
                    } else {
                        $basicCoverage = 0;
                        $fegli_row['basic_coverage'] = 0;
                        $cost['basic']['monthly'] = 0;
                        $cost['basic']['BiWeekly'] = 0;
                        $cost['basic']['yearly'] = 0;
                        $oldCostBiWeekly['basic'] = 0;
                    }

                    /* OptionA Coverage and premium Cost */
                    if ($data['OptionAInc'] == 1) {
                        // echo $optionACoverage . "<br>";

                        if ($i <= $retirement_year) {

                            if ($i >= $year_at_age65) {
                                $optionACoverage = 10000;
                                if ($i == $retirement_year) {
                                    if ($dtnow < $dtRet) {
                                        $monthlyCostForOptionAPremium =  10 * $premium_costs['premiumMultiple_A'];
                                        $oldMonthlyCostForOptionAPremium =  10 * $premium_costs_old['premiumMultiple_A'];
                                    } else {
                                        $monthlyCostForOptionAPremium =  0;
                                        $oldMonthlyCostForOptionAPremium =  0;
                                    }
                                } else {
                                    $monthlyCostForOptionAPremium =  10 * $premium_costs['premiumMultiple_A'];
                                    $oldMonthlyCostForOptionAPremium =  10 * $premium_costs_old['premiumMultiple_A'];
                                }
                            } else {
                                $optionACoverage = 10000;
                                $monthlyCostForOptionAPremium = 10 * $premium_costs['premiumMultiple_A'];
                                $oldMonthlyCostForOptionAPremium = 10 * $premium_costs_old['premiumMultiple_A'];
                            }
                        } else {
                            // ***** Option A 75% reduction by default *****
                            $maxCoverage = 10000;
                            $conf_name = app('Common')->getConfiguarationNameInRetirement($fegli_empage);
                            $premiumMultiple_A = resolve('employee')->getSystemConfigurations($conf_name['optionA']['reduction_75']);
                            // dd($conf_name['optionA']['reduction_75']);
                            if ($fegli_empage < 65) {
                                $optionACoverage = 10000;
                                $monthlyCostForOptionAPremium = app('Common')->monthlyCostForOptionAPremium75Reduction($premiumMultiple_A, $maxCoverage);

                                $oldMonthlyCostForOptionAPremium = app('Common')->monthlyCostForOptionAPremium75Reduction($premium_costs_old['premiumMultiple_A'], $maxCoverage);
                            } else {
                                // echo $i . " reducing coerage by 200 per month <br>";
                                // continue;
                                $minCoverage = ($maxCoverage * 25) / 100;
                                $reductionAmount = $maxCoverage * (2 / 100); // to initialize (error on live)
                                // echo $fegli_empage;
                                // die;
                                if ($fegli_empage == 65) {
                                    $reducedCoverageOptionA = $maxCoverage;
                                } else {

                                    if ($i == date('Y')) {
                                        $exceedYears = $emp_age - 65;
                                        $reducedCoverageOptionA = $maxCoverage;
                                        for ($ag = 65; $ag <= $fegli_empage; $ag++) {
                                            for ($mnt = $dtnow->format('m'); $mnt <= 12; $mnt++) {
                                                if ($reducedCoverageOptionA > $minCoverage) {
                                                    $reducedCoverageOptionA = $reducedCoverageOptionA - $reductionAmount;
                                                } else {
                                                    break;
                                                }
                                            }
                                        }
                                    }
                                }

                                // echo '.....<br>' . $minCoverage . "<br>";
                                for ($mnt = 1; $mnt <= 12; $mnt++) {
                                    if ($reducedCoverageOptionA > $minCoverage) {
                                        $reducedCoverageOptionA = $reducedCoverageOptionA - $reductionAmount;
                                        if ($reducedCoverageOptionA < $minCoverage) {
                                            $reducedCoverageOptionA = $minCoverage;
                                        }
                                        // echo "check 1 <br>" . $reducedCoverageOptionA . "<br>";
                                    } else {
                                        // echo $reducedCoverageOptionA . " check 2 <br>";
                                        $lastAgeOfReduction = $i;
                                        $lastMonthOfReduction = $mnt;
                                        break;
                                    }
                                    // echo $reducedCoverageOptionA . "-----" . $minCoverage . "<br>";
                                }

                                if ($i == date('Y')) {
                                    //
                                    if ($rtnow < $rtDate) {
                                        $monthlyCostForOptionAPremium = app('Common')->monthlyCostForOptionAPremium75Reduction($premiumMultiple_A, $maxCoverage);
                                    } else {
                                        $monthlyCostForOptionAPremium = 0;
                                    }
                                } else {
                                    $monthlyCostForOptionAPremium = app('Common')->monthlyCostForOptionAPremium75Reduction($premiumMultiple_A, $maxCoverage);
                                }
                                $optionACoverage = $reducedCoverageOptionA;


                                $oldMonthlyCostForOptionAPremium = app('Common')->monthlyCostForOptionAPremium75Reduction($premium_costs_old['premiumMultiple_A'], $maxCoverage);
                            }
                        }
                        $yearlyCostForOptionAPremium = $monthlyCostForOptionAPremium * 12;
                        $BiWeeklycostForOptionAPremium = $yearlyCostForOptionAPremium / 26; // BiWeekly cost

                        $oldYearlyCostForOptionAPremium = $oldMonthlyCostForOptionAPremium * 12;
                        $oldBiWeeklycostForOptionAPremium = $oldYearlyCostForOptionAPremium / 26;

                        // echo $BiWeeklycostForOptionAPremium;
                        // die;
                        $fegli_row['optionA_coverage'] = $optionACoverage;
                        $cost['optionA']['monthly'] = $monthlyCostForOptionAPremium;
                        $cost['optionA']['BiWeekly'] = $BiWeeklycostForOptionAPremium;
                        $cost['optionA']['yearly'] = $yearlyCostForOptionAPremium;
                        $oldCostBiWeekly['optionA'] = $oldBiWeeklycostForOptionAPremium;
                    } else {
                        $optionACoverage = 0;
                        $fegli_row['optionA_coverage'] = 0;
                        $cost['optionA']['monthly'] = 0;
                        $cost['optionA']['BiWeekly'] = 0;
                        $cost['optionA']['yearly'] = 0;
                        $oldCostBiWeekly['optionA'] = 0;
                    }


                    // OptionB Coverage and premium Cost
                    if ($data['OptionBInc'] == 1) {
                        if (($i <= $retirement_year)/*  && ($emp_age_month < $retirementAge_month) */) {
                            $optionBCoverage = $salForPremium * $data['OptionBMultiplier'];
                            $monthlyCostForOptionB = app('Common')->monthlyCostForOptionB($data['OptionBInc'], $optionBCoverage, $premium_costs['premiumMultiple_B']);
                            $BiWeeklycostForOptionB = app('Common')->biweeklyCostForOptionBNoReduction($premium_costs_biweekly['premiumMultiple_B'], $optionBCoverage);

                            $oldBiWeeklycostForOptionB = app('Common')->biweeklyCostForOptionBNoReduction($premium_costs_biweekly_old['premiumMultiple_B'], $optionBCoverage);
                        } else {
                            $conf_name = app('Common')->getConfiguarationNameInRetirement($fegli_empage);
                            if ($data['optionBReductionAfterRetirement'] == 100) {
                                $premiumMultiple_B = resolve('employee')->getSystemConfigurations($conf_name['optionB']['full_reduction']);
                                $maxCoverage = $salForPremium * $data['OptionBMultiplier'];
                                // Option B full reduction
                                if ($fegli_empage < 65) {
                                    $optionBCoverage = $salForPremium * $data['OptionBMultiplier'];
                                    $monthlyCostForOptionB = app('Common')->monthlyCostForOptionBPremiumFullReduction($premiumMultiple_B, $optionBCoverage);
                                    $BiWeeklycostForOptionB = app('Common')->biweeklyCostForOptionBPremiumFullReduction($premium_costs_biweekly['premiumMultiple_B'], $optionBCoverage);

                                    $oldBiWeeklycostForOptionB = app('Common')->biweeklyCostForOptionBPremiumFullReduction($premium_costs_biweekly_old['premiumMultiple_B'], $optionBCoverage);
                                } else {
                                    // option B full reduction.. age >= 65
                                    $minCoverage = 0.00;
                                    $reductionAmount = $maxCoverage * (2 / 100);
                                    $reducedCoverageOptionB = $maxCoverage;
                                    if ($emp_age == 65) {
                                        $reducedCoverageOptionB = $maxCoverage;
                                    } else {
                                        if ($i == date('Y')) {
                                            $exceedYears = $emp_age - 65;
                                            $reducedCoverageOptionB = $maxCoverage;
                                            for ($ag = 65; $ag <= 65 + $exceedYears; $ag++) {
                                                for ($mnt = $dtnow->format('m'); $mnt <= 12; $mnt++) {
                                                    if ($reducedCoverageOptionB > $minCoverage) {
                                                        $reducedCoverageOptionB = $reducedCoverageOptionB - $reductionAmount;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    for ($mnt = $dtnow->format('m'); $mnt <= 12; $mnt++) {
                                        if ($reducedCoverageOptionB > $minCoverage) {
                                            $reducedCoverageOptionB = $reducedCoverageOptionB - $reductionAmount;
                                        } elseif ($reducedCoverageOptionB <= $minCoverage) {
                                            $reducedCoverageOptionB = 0.00;
                                            $lastAgeOfReduction = $fegli_empage;
                                            $lastMonthOfReduction = $mnt;
                                            break;
                                        }
                                        $lastAgeOfReduction = 0;
                                        $lastMonthOfReduction = 0;
                                    }
                                    $optionBCoverage = $reducedCoverageOptionB;
                                    $monthlyCostForOptionB = app('Common')->monthlyCostForOptionBPremiumFullReduction($premiumMultiple_B, $optionBCoverage);
                                    $BiWeeklycostForOptionB = app('Common')->biweeklyCostForOptionBPremiumFullReduction($premium_costs_biweekly['premiumMultiple_B'], $optionBCoverage);

                                    $oldBiWeeklycostForOptionB = app('Common')->biweeklyCostForOptionBPremiumFullReduction($premium_costs_biweekly_old['premiumMultiple_B'], $optionBCoverage);
                                }
                            } else {
                                // option B no reduction
                                $optionBCoverage = $salForPremium * $data['OptionBMultiplier'];

                                $monthlyCostForOptionB = app('Common')->monthlyCostForOptionBNoReduction($fegli_empage, $optionBCoverage);
                                $BiWeeklycostForOptionB = app('Common')->biweeklyCostForOptionBNoReduction($premium_costs_biweekly['premiumMultiple_B'], $optionBCoverage);

                                $oldBiWeeklycostForOptionB = app('Common')->biweeklyCostForOptionBNoReduction($premium_costs_biweekly_old['premiumMultiple_B'], $optionBCoverage);
                            }
                        }
                        $fegli_row['optionB_coverage'] = $optionBCoverage;
                        $yearlyCostForOptionB = $monthlyCostForOptionB * 12;

                        $cost['optionB']['monthly'] = $monthlyCostForOptionB;
                        $cost['optionB']['BiWeekly'] = $BiWeeklycostForOptionB;
                        $cost['optionB']['yearly'] = $yearlyCostForOptionB;
                        $oldCostBiWeekly['optionB'] = $oldBiWeeklycostForOptionB;
                    } else {
                        $optionBCoverage = 0;
                        $fegli_row['optionB_coverage'] = 0;
                        $cost['optionB']['monthly'] = 0;
                        $cost['optionB']['BiWeekly'] = 0;
                        $cost['optionB']['yearly'] = 0;
                        $oldCostBiWeekly['optionB'] = 0;
                    }
                    // echo "<pre>"; print_r($data); die;
                    if ($data['OptionCInc'] == 1) {
                        if ($i > date('Y')) {
                            if (count($fegli_dependents) > 0) {
                                foreach ($fegli_dependents as $k => $dep) {
                                    $fegli_dependents[$k]['child_age'] = ++$dep['child_age'];
                                }
                            }
                        }
                        if (($i <= $retirement_year)) { //  && ($emp_age_month < $retirementAge_month)
                            $optionCCoverage = app('Common')->calcOptionCCoverageFegli($data['OptionCInc'], $data['employee']['MaritalStatusTypeId'], $data['OptionCMultiplier'], $fegli_dependents);

                            $monthlyCostForOptionC = app('Common')->monthlyCostForOptionC($data['OptionCInc'], $premium_costs['premiumMultiple_C'], $data['OptionCMultiplier']);
                            // echo $monthlyCostForOptionC;
                            // die;
                        } else {
                            $conf_name = app('Common')->getConfiguarationNameInRetirement($fegli_empage);
                            $maxCoverage = app('Common')->calcOptionCCoverageFegli($data['OptionCInc'], $data['employee']['MaritalStatusTypeId'], $data['OptionCMultiplier'], $fegli_dependents);
                            if ($data['optionCReductionAfterRetirement'] == 100) {
                                // Option C Full reduction
                                $premiumMultiple_C = resolve('employee')->getSystemConfigurations($conf_name['optionC']['full_reduction']);
                                if ($fegli_empage < 65) {
                                    $OptionCCoverage = $maxCoverage;

                                    $monthlyCostForOptionC = app('Common')->monthlyCostForOptionC(1, $premiumMultiple_C, $data['OptionCMultiplier']); // app('Common')->monthlyCostForOptionCFullReduction($premiumMultiple_C, $data['OptionCMultiplier']);
                                    // option B full reduction.. age >= 65
                                    $minCoverage = 0.00;
                                    $reductionAmount = $maxCoverage * (2 / 100);
                                    $reducedCoverageOptionC = $maxCoverage;

                                    if ($fegli_empage == 65) {
                                        $reducedCoverageOptionC = $maxCoverage;
                                    } else {
                                        if ($i == date('Y')) {
                                            $exceedYears = $emp_age - 65;
                                            $reducedCoverageOptionC = $maxCoverage;
                                            for ($ag = 65; $ag <= 65 + $exceedYears; $ag++) {
                                                for ($mnt = $dtnow->format('m'); $mnt <= 12; $mnt++) {
                                                    if ($reducedCoverageOptionC > $minCoverage) {
                                                        $reducedCoverageOptionC = $reducedCoverageOptionC - $reductionAmount;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    for ($mnt = $dtnow->format('m'); $mnt <= 12; $mnt++) {
                                        if ($reducedCoverageOptionC > $minCoverage) {
                                            $reducedCoverageOptionC = $reducedCoverageOptionC - $reductionAmount;
                                        } elseif ($reducedCoverageOptionC <= $minCoverage) {
                                            $reducedCoverageOptionC = 0.00;
                                            $lastAgeOfReduction = $i;
                                            $lastMonthOfReduction = $mnt;
                                            break;
                                        } else {
                                            $lastAgeOfReduction = 0;
                                            $lastMonthOfReduction = 0;
                                            break;
                                        }
                                    }
                                    $optionCCoverage = $reducedCoverageOptionC;
                                    $monthlyCostForOptionC = app('Common')->monthlyCostForOptionC(1, $premiumMultiple_C, $data['OptionCMultiplier']); // app('Common')->monthlyCostForOptionCFullReduction($premiumMultiple_C, $data['OptionCMultiplier']);
                                }
                            } else {
                                // Option C No Reduction after retirement
                                $premiumMultiple_C = resolve('employee')->getSystemConfigurations($conf_name['optionC']['no_reduction']);
                                $optionCCoverage = $maxCoverage;
                                $monthlyCostForOptionC = app('Common')->monthlyCostForOptionCNoReduction($premiumMultiple_C, $data['OptionCMultiplier']);
                            }
                        }
                        $yearlyCostForOptionC =  $monthlyCostForOptionC * 12;
                        $BiWeeklycostForOptionC = $yearlyCostForOptionC / 26;


                        $oldBiWeeklycostForOptionC = app('Common')->monthlyCostForOptionCNoReduction($premium_costs_biweekly_old['premiumMultiple_C'], $data['OptionCMultiplier']);

                        $fegli_row['optionC_coverage'] = $optionCCoverage;
                        $cost['optionC']['monthly'] = $monthlyCostForOptionC;
                        $cost['optionC']['BiWeekly'] = $BiWeeklycostForOptionC;
                        $cost['optionC']['yearly'] = $yearlyCostForOptionC;
                        $oldCostBiWeekly['optionC'] = $oldBiWeeklycostForOptionC;
                    } else {
                        $optionCCoverage = 0;
                        $fegli_row['optionC_coverage'] = 0;
                        $cost['optionC']['monthly'] = 0;
                        $cost['optionC']['BiWeekly'] = 0;
                        $cost['optionC']['yearly'] = 0;
                        $oldCostBiWeekly['optionC'] = 0;
                    }

                    $fegli_row['total_coverage'] = $basicCoverage + $optionACoverage + $optionBCoverage + $optionCCoverage;

                    $fegli_row['totalBiWeekly'] = $cost['basic']['BiWeekly'] + $cost['optionA']['BiWeekly'] + $cost['optionB']['BiWeekly'] + $cost['optionC']['BiWeekly'];

                    $fegli_row['totalMonthly'] = $cost['basic']['monthly'] + $cost['optionA']['monthly'] + $cost['optionB']['monthly'] + $cost['optionC']['monthly'];

                    $fegli_row['totalYearly'] = $cost['basic']['yearly'] + $cost['optionA']['yearly'] + $cost['optionB']['yearly'] + $cost['optionC']['yearly'];

                    $runningTotalCost = $runningTotalCost + $fegli_row['totalYearly'];

                    $fegli_row['runningTotalCost'] = $runningTotalCost;

                    $fegli_row['costs'] = $cost;
                }

                array_push($fegli_arr, $fegli_row);

                if ($i == date('Y')) {
                    $biweekly_arr = [
                        'basic' => $cost['basic']['BiWeekly'],
                        'optionA' => $cost['optionA']['BiWeekly'],
                        'optionB' => $cost['optionB']['BiWeekly'],
                        'optionC' => $cost['optionC']['BiWeekly'],
                        'total' => $fegli_row['totalBiWeekly']
                    ];
                    $monthly_arr = [
                        'basic' => $cost['basic']['monthly'],
                        'optionA' => $cost['optionA']['monthly'],
                        'optionB' => $cost['optionB']['monthly'],
                        'optionC' => $cost['optionC']['monthly'],
                        'total' => $fegli_row['totalMonthly']
                    ];
                    $current_fegli['basic'] = $basicCoverage;
                    $current_fegli['optionA'] = $optionACoverage;
                    $current_fegli['optionB'] = $optionBCoverage;
                    $current_fegli['optionC'] = $optionCCoverage;
                    $current_fegli['optionC_spouse'] = (($data['OptionCInc'] == 1) && ($is_married == 1)) ? 5000 * $data['OptionCMultiplier'] : 0;
                    $current_fegli['optionC_dependent'] = ($data['OptionCInc'] == 1) && (count($data['fegli_dependent']) > 0) ? (2500 * $data['OptionCMultiplier'] * count($data['fegli_dependent'])) : 0;
                    $old_premium_costs = $oldCostBiWeekly;
                }

                $fegli_empage++;
            }
        }
        // echo "<pre>";
        // print_r($current_fegli);
        // exit;
        if (count($data['fegli_dependent']) > 0) {
            $has_dependents = 1;
        } else {
            $has_dependents = 0;
        }

        $result_data =  [
            'fegli_arr' => $fegli_arr,
            'biweekly_arr' => $biweekly_arr,
            'monthly_arr' => $monthly_arr,
            'current_fegli' => $current_fegli,
            'is_married' => $is_married,
            'has_dependents' => $has_dependents,
            'last_salary' => $last_salary,
            'retirementAge' => $retirementAge,
            'old_premium_costs' => $old_premium_costs
        ];
        return $result_data;
    }

    public function getFegliAllScenarios($data = [], $last_salary = 0, $retirementAge = 0)
    {
        if (!empty($data)) {
            /* calculate salary for premium */
            $fegli_empage = $retirementAge - 1;
            $salForPremium = app('Common')->calcSalForPremium($last_salary);
            /* BASIC Coverage No Reduction, same throughout life , Cost will change so calculated in loop */
            $basicPremiumMultiplier = app('Common')->basicCoverageMultiplier($fegli_empage);
            // echo $basicPremiumMultiplier;
            // die;

            $maxCoverage = $basicCoverageNoReduction = $basicCoverage50Reduction = $basicCoverage75Reduction = app('Common')->calcBasicCoverageFegli($data['BasicInc'], $salForPremium, $basicPremiumMultiplier);

            $bdaydate = new \DateTime($data['employee']['eligibility']['DateOfBirth']);
            $now = new \DateTime();
            $age_diff = $now->diff($bdaydate);
            $emp_age = $age_diff->y;

            /** Option A coverage */
            if ($data['OptionAInc'] == 1) {
                $optionACoverage = 10000;
                $reducedCoverageOptionA = 10000;

                $maxCoverageOptionA = 10000;
                $minCoverageOptionA = ($maxCoverageOptionA * 25) / 100;
                $reductionAmountOptionA = $maxCoverageOptionA * (2 / 100);
            } else {
                $optionACoverage = 0;
                $reducedCoverageOptionA = 0;

                $maxCoverageOptionA = 0;
                $minCoverageOptionA = 0;
                $reductionAmountOptionA = 0;
            }
            /** Option B coverage */
            if ($data['OptionBInc'] == 1) {
                $optionBCoverage = $maxCoverageOptionB = $reducedCoverageOptionB = $salForPremium * $data['OptionBMultiplier'];
            } else {
                $optionBCoverage = $maxCoverageOptionB = $reducedCoverageOptionB = 0;
            }

            /** Option c coverage */
            $fegli_dependents = [];
            if (count($data['fegli_dependent']) > 0) {
                foreach ($data['fegli_dependent'] as $child) {
                    $child_row = [];
                    if (isset($child['age']) && ($child['age'] > 0)) {
                        $child_age = $child['age'];
                    } else {
                        $child_age = date('Y') - date('Y', strtotime($child['DateOfBirth']));
                    }
                    $child_row['CoverAfter22'] = $child['CoverAfter22'];
                    $child_row['child_age'] = $child_age;
                    array_push($fegli_dependents, $child_row);
                }
            }

            if (($emp_age < $retirementAge) && (count($fegli_dependents) > 0)) {
                for ($i = $emp_age; $i < $retirementAge; $i++) {
                    foreach ($fegli_dependents as $k => $dep) {
                        $fegli_dependents[$k]['child_age'] = $dep['child_age'] + 1;
                    }
                }
            }

            // dd($fegli_dependents);
            if ($data['OptionCInc'] == 1) {
                $maxCoverageOptionC = $reducedCoverageOptionC = app('Common')->calcOptionCCoverageFegli($data['OptionCInc'], $data['employee']['MaritalStatusTypeId'], $data['OptionCMultiplier'], $fegli_dependents);
            } else {
                $maxCoverageOptionC = $reducedCoverageOptionC = 0;
            }


            /* ****loop data starts here**** */

            $fegli_all_cases = [];
            if ($retirementAge == 65) {
                $crows = 89;
            } else {
                $crows = 90;
            }

            for ($i = $retirementAge; $i <= $crows; $i++) {
                $premium_costs = app('Common')->getPremiumCostMultiplierFEGLI($i);
                $fegli_row = [];
                $fegli_row['age'] = $i;
                if(($data['DoesNotMeetFiveYear'] == 1) && ($i >= $retirementAge)) {
                    $fegli_row['basicCoverageNoReduction'] = 0;
                    $fegli_row['basicCoverage50Reduction'] = 0;
                    $fegli_row['basicCoverage75Reduction'] = 0;
                    $fegli_row['basicCostNoReduction'] = 0;
                    $fegli_row['basicCost50Reduction'] = 0;
                    $fegli_row['basicCost75Reduction'] = 0;

                    $fegli_row['optionACoverage'] = 0;
                    $fegli_row['OptionACost'] = 0;

                    $fegli_row['optionBCoverageNoReduction'] = 0;
                    $fegli_row['OptionBCostNoReduction'] = 0;
                    $fegli_row['optionBCoverageFullReduction'] = 0;
                    $fegli_row['OptionBCostFullReduction'] = 0;

                    $fegli_row['optionCCoverageNoReduction'] = 0;
                    $fegli_row['optionCCostNoReduction'] = 0;
                    $fegli_row['optionCCoverageFullReduction'] = 0;
                    $fegli_row['optionCCostFullReduction'] = 0;
                }   else {

                    $fegli_row['basicCoverageNoReduction'] = $basicCoverageNoReduction;
                    $fegli_row['basicCoverage50Reduction'] = $basicCoverage50Reduction;
                    $fegli_row['basicCoverage75Reduction'] = $basicCoverage75Reduction;
                    
                    /* BASIC Coverage and premium Cost */

                    // if (isset($data['employee']['PostalEmployee']) && ($data['employee']['PostalEmployee'] == 1)) {
                    //     $fegli_row['basicCostNoReduction'] = 0;
                    // } else {
                    if ($data['BasicInc'] == 1) {
                        $monthlyCostForBasicPremium = app('Common')->monthlyCostForBasicPremiumZeroReduction_conf($i, $basicCoverageNoReduction);
                    } else {
                        $monthlyCostForBasicPremium = 0;
                    }
                    $fegli_row['basicCostNoReduction'] = $monthlyCostForBasicPremium;
                    // }

                    // Basic reduction 50% and 75%
                    if ($i >= 65) {
                        $basicCoverage50Reduction_pre = $basicCoverage50Reduction;
                        $basicCoverage75Reduction_pre = $basicCoverage75Reduction;

                        $basicCoverage50Reduction = app('Common')->calcBasicCoverage50Reduction($data['BasicInc'], $basicCoverageNoReduction, $basicCoverage50Reduction_pre);
                        $basicCoverage75Reduction = app('Common')->calcBasicCoverage75Reduction($data['BasicInc'], $basicCoverageNoReduction, $basicCoverage75Reduction_pre);
                        $fegli_row['basicCoverage50Reduction'] = $basicCoverage50Reduction;
                        $fegli_row['basicCoverage75Reduction'] = $basicCoverage75Reduction;
                    }

                    if ($data['BasicInc'] == 1) {
                        $monthlyCostForBasicPremium50 = app('Common')->monthlyCostForBasicPremium50Reduction_conf($i, $basicCoverageNoReduction);
                        $monthlyCostForBasicPremium75 = app('Common')->monthlyCostForBasicPremium75Reduction_conf($i, $basicCoverageNoReduction);
                    } else {
                        $monthlyCostForBasicPremium50 = 0;
                        $monthlyCostForBasicPremium75 = 0;
                    }
                    $fegli_row['basicCost50Reduction'] = $monthlyCostForBasicPremium50;
                    $fegli_row['basicCost75Reduction'] = $monthlyCostForBasicPremium75;

                    /* OptionA Coverage and premium Cost */

                    // echo $optionACoverage . "<br>";
                    if ($data['OptionAInc'] == 1) {
                        if ($i == $retirementAge) {
                            $fegli_row['optionACoverage'] = 10000;
                            if ($i < 65) {
                                $fegli_row['OptionACost'] =  10 * $premium_costs['premiumMultiple_A'];
                            } else {
                                $fegli_row['OptionACost'] =  0;
                            }
                        } else {
                            // ***** Option A 75% reduction by default *****
                            $conf_name = app('Common')->getConfiguarationNameInRetirement($i);
                            $premiumMultiple_A = resolve('employee')->getSystemConfigurations($conf_name['optionA']);
                            if ($i < 65) {
                                $fegli_row['optionACoverage'] = 10000;
                                $fegli_row['OptionACost'] = 10 * $premiumMultiple_A;
                            } else {
                                if ($i == 65) {
                                    $reducedCoverageOptionA = $maxCoverageOptionA;
                                }

                                for ($mnt = 1; $mnt <= 12; $mnt++) {
                                    if ($reducedCoverageOptionA > $minCoverageOptionA) {
                                        $reducedCoverageOptionA = $reducedCoverageOptionA - $reductionAmountOptionA;
                                        if ($reducedCoverageOptionA < $minCoverageOptionA) {
                                            $reducedCoverageOptionA = $minCoverageOptionA;
                                        }
                                    } else {
                                        $lastAgeOfReduction = $i;
                                        $lastMonthOfReduction = $mnt;
                                        break;
                                    }
                                }
                                $fegli_row['optionACoverage'] = $reducedCoverageOptionA;
                                if ($i >= $retirementAge) {
                                    if ($i < 65) {
                                        $fegli_row['OptionACost'] =  10 * $premium_costs['premiumMultiple_A'];
                                    } else {
                                        $fegli_row['OptionACost'] =  0;
                                    }
                                } else {
                                    $fegli_row['OptionACost'] = app('Common')->monthlyCostForOptionAPremium75Reduction($premiumMultiple_A, $maxCoverageOptionA);
                                }
                            }
                        }
                    } else {
                        $fegli_row['optionACoverage'] = 0;
                        $fegli_row['OptionACost'] = 0;
                    }

                    // OptionB Coverage and premium Cost
                    if ($data['OptionBInc'] == 1) {
                        if ($i <= $retirementAge) {
                            $fegli_row['optionBCoverageNoReduction'] = $fegli_row['optionBCoverageFullReduction'] = $optionBCoverage;

                            $fegli_row['OptionBCostNoReduction'] = $fegli_row['OptionBCostFullReduction'] = app('Common')->monthlyCostForOptionB($data['OptionBInc'], $optionBCoverage, $premium_costs['premiumMultiple_B']);
                        } else {

                            $fegli_row['optionBCoverageNoReduction'] = $optionBCoverage;

                            $fegli_row['OptionBCostNoReduction'] = app('Common')->monthlyCostForOptionB($data['OptionBInc'], $optionBCoverage, $premium_costs['premiumMultiple_B']);

                            // Option B full reduction
                            if ($i < 65) {
                                $fegli_row['optionBCoverageFullReduction'] = $optionBCoverage;
                                $fegli_row['OptionBCostFullReduction'] = app('Common')->monthlyCostForOptionBPremiumFullReduction($premium_costs['premiumMultiple_B'], $fegli_row['optionBCoverageFullReduction']);
                            } else {
                                // option B full reduction.. age >= 65

                                $minCoverage = 0.00;
                                $reductionAmount = ($maxCoverageOptionB * (2 / 100)) * 12;
                                if ($reducedCoverageOptionB > $minCoverage) {
                                    $reducedCoverageOptionB = $reducedCoverageOptionB - $reductionAmount;
                                    if ($reducedCoverageOptionB <= $minCoverage) {
                                        $reducedCoverageOptionB = 0;
                                    }
                                } elseif ($reducedCoverageOptionB <= $minCoverage) {
                                    $reducedCoverageOptionB = 0;
                                }

                                $fegli_row['optionBCoverageFullReduction'] = $reducedCoverageOptionB;
                                $fegli_row['OptionBCostFullReduction'] = 0;
                            }
                        }
                    } else {
                        $fegli_row['optionBCoverageNoReduction'] = 0;

                        $fegli_row['OptionBCostNoReduction'] = 0;
                        $fegli_row['optionBCoverageFullReduction'] = 0;
                        $fegli_row['OptionBCostFullReduction'] = 0;
                    }


                    /** Option C starts */

                    if ($data['OptionCInc'] == 1) {
                        if ($i == $retirementAge) {
                            $fegli_row['optionCCoverageNoReduction'] = $fegli_row['optionCCoverageFullReduction'] = app('Common')->calcOptionCCoverageFegli($data['OptionCInc'], $data['employee']['MaritalStatusTypeId'], $data['OptionCMultiplier'], $fegli_dependents); // $maxCoverageOptionC;

                            $fegli_row['optionCCostNoReduction'] = app('Common')->monthlyCostForOptionC($data['OptionCInc'], $premium_costs['premiumMultiple_C'], $data['OptionCMultiplier']);
                            if ($i >= 65) {
                                $fegli_row['optionCCostFullReduction'] = 0;
                            } else {
                                $fegli_row['optionCCostFullReduction'] = app('Common')->monthlyCostForOptionC($data['OptionCInc'], $premium_costs['premiumMultiple_C'], $data['OptionCMultiplier']);
                            }
                        } else {
                            foreach ($fegli_dependents as $k => $dep) {
                                $fegli_dependents[$k]['child_age'] = $dep['child_age'] + 1;
                            }
                            $fegli_row['optionCCoverageNoReduction'] = app('Common')->calcOptionCCoverageFegli($data['OptionCInc'], $data['employee']['MaritalStatusTypeId'], $data['OptionCMultiplier'], $fegli_dependents);

                            $fegli_row['optionCCostNoReduction'] = app('Common')->monthlyCostForOptionC(1, $premium_costs['premiumMultiple_C'], $data['OptionCMultiplier']); // app('Common')->monthlyCostForOptionCNoReduction($i, $data['OptionCMultiplier']);

                            if ($i < 65) {
                                $fegli_row['optionCCoverageFullReduction'] = app('Common')->calcOptionCCoverageFegli($data['OptionCInc'], $data['employee']['MaritalStatusTypeId'], $data['OptionCMultiplier'], $fegli_dependents);

                                $fegli_row['optionCCostFullReduction'] = app('Common')->monthlyCostForOptionC(1, $premium_costs['premiumMultiple_C'], $data['OptionCMultiplier']); // app('Common')->monthlyCostForOptionCFullReduction($i, $data['OptionCMultiplier']);
                            } else {
                                // option B full reduction.. age >= 65
                                $minCoverage = 0.00;
                                $reductionAmount = ($maxCoverageOptionC * (2 / 100)) * 12;

                                if ($i == 65) {
                                    $reducedCoverageOptionC = $maxCoverageOptionC;
                                }

                                if ($reducedCoverageOptionC > $minCoverage) {
                                    $reducedCoverageOptionC = $reducedCoverageOptionC - $reductionAmount;
                                    if ($reducedCoverageOptionC <= $minCoverage) {
                                        $reducedCoverageOptionC = 0;
                                    }
                                } elseif ($reducedCoverageOptionC <= $minCoverage) {
                                    $reducedCoverageOptionC = 0.00;
                                }

                                $fegli_row['optionCCoverageFullReduction'] = $reducedCoverageOptionC;
                                $fegli_row['optionCCostFullReduction'] = 0; // app('Common')->monthlyCostForOptionCFullReduction($i, $data['OptionCMultiplier']);
                            }
                        }
                    } else {
                        $fegli_row['optionCCoverageNoReduction'] = 0;
                        $fegli_row['optionCCostNoReduction'] = 0;
                        $fegli_row['optionCCoverageFullReduction'] = 0;
                        $fegli_row['optionCCostFullReduction'] = 0;
                    }

                }

                array_push($fegli_all_cases, $fegli_row);
            }
        }
        // echo "<pre>";print_r($fegli_all_cases); die;
        return $fegli_all_cases;
    }

    public function getSocialSecurityDetailPdf($empId = null)
    {
        $emp = $this->getById($empId);
        // $emp = Employee::find($empId);
        // echo "<pre>";
        // print_r($emp->toArray());
        // exit;
        if (is_null($emp->eligibility)) {
            return [];
        }
        $ssStartAge_year = $emp->SSStartAge_year;

        $dtnow = new \DateTime();
        $dtbday = new \DateTime($emp->eligibility->DateOfBirth);
        $rtdate = new \DateTime($emp->eligibility->RetirementDate); // retirement date object
        $interval = $dtnow->diff($dtbday);
        $rt_interval = $rtdate->diff($dtbday);
        $retirementAge = $rt_interval->y;
        $emp_age = $interval->y;
        $emp_age_month = $interval->m;
        // echo $emp_age; exit;
        $retirement_year = date('Y', strtotime($emp->eligibility->RetirementDate));
        if ($dtnow < $rtdate) {
            $yearsInRet = 0;
        } else {
            $yearsInRet_interval = $rtdate->diff($dtnow);
            $yearsInRet = $yearsInRet_interval->y;
        }

        $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
        $bday = $bday->modify('- 1 day'); // The government considers someone to have turned their new age on the day before their actual birthday.
        $minRetAge = $this->getMinumumRetirementAge($empId, 1);
        $mraObj = new \DateTime($minRetAge['minRetirementDate']);
        $mra_interval = $mraObj->diff($bday);
        $mra_y = $mra_interval->y;
        $mra_m = $mra_interval->m;



        $ss_cola = EmployeeConfig::where('EmployeeId', $empId)->where('ConfigType', 'SSCola')->first(); // empConfigure SS Cola
        // dd($ss_cola->toArray());Y
        $pia_formula_bend = EmployeeConfig::where('EmployeeId', $empId)->where('ConfigType', 'PIAFormula')->first();
        $ssEarnedIncomeLimit = EmployeeConfig::where('EmployeeId', $empId)->where('ConfigType', 'SSEarnedIncomeLimit')->first();
        if (!$ss_cola) {
            $ss_cola = AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', 'SSCola')->first()->AppLookupDescription;
        } else {
            $ss_cola = $ss_cola->ConfigValue;
        }

        if (!$pia_formula_bend) {
            $pia_formula_bend = AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', 'PIAFormula')->first()->AppLookupDescription;
        } else {
            $pia_formula_bend = $pia_formula_bend->ConfigValue;
        }

        if (!$ssEarnedIncomeLimit) {
            $ssEarnedIncomeLimit = AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', 'SSEarnedIncomeLimit')->first()->AppLookupDescription;
        } else {
            $ssEarnedIncomeLimit = $ssEarnedIncomeLimit->ConfigValue;
        }
        // echo $ss_cola;
        // exit;
        // dd($emp->toArray());
        $ssAtStartAge = $emp->SSMonthlyAtStartAge;
        $wep_penalty = $this->getWepPenalty($emp, $pia_formula_bend);
        // dd($wep_penalty);
        $ssAtStartAge = $ssAtStartAge - $wep_penalty;
        $current_ss_benefits = $ssAtStartAge;
        $ss_arr = [];
        $varInc = 0;
        /* SRS Calculation */
        $retEligibility = $this->getEarliestRetirement($empId);
        $duration = round($retEligibility['serviceDurationForSRS']);

        $serviceDuration = $retEligibility['serviceDurationForPension'];

        $mrAgeArr = $this->getMinumumRetirementAge($empId, 1);
        $minRetDate = new \DateTime($mrAgeArr['minRetirementDate']);

        $mra10 = $this->getMRAPenalty($empId, 1);
        $mra10_penalty = $mra10['first_pension_mra10_penalty'];

        if ($mra10_penalty > 0) {
            $res = 0;
        } else {
            if (($emp_age >= $retirementAge) && ($emp_age <= 61)) {
                $SSMonthlyAt62 = $emp['SSMonthlyAt62'];
                $res = round($SSMonthlyAt62, 2) * $duration / 40;
            } else {
                $res = 0;
            }
        }

        // dd($mra10_penalty);

        $ss_row['fers_srs'] = $res;
        $configInc  = $this->getEmployeeConf($empId);
        $ss_row['configInc'] = $configInc['FERSCola'];

        $firstPension = 0;
        if ($emp_age >= $retirementAge) {
            $firstPension = $this->getFirstPension($empId);
        }
        /* SRS Calculation */
        $mraPenalty_arr = $this->getMRAPenalty($empId, 1);
        $mraPenalty = $mraPenalty_arr['first_pension_mra10_penalty'];
        $earlyOutPenalty = $this->getEarlyOutPenalty($empId, 1);

        $nonDeductionPanelty = $this->nonDeductionPanelty($empId);
        $refundedPanelty = $this->calcRefundedPanelty($empId);
        $all_penelties = $earlyOutPenalty + $nonDeductionPanelty + $refundedPanelty;

        $firstPension = $firstPension - $all_penelties;
        $csrsOffsetPenalty = $this->getCsrsOffsetPenalty($empId);
        if ((($emp['systemType'] == 'CSRS') || ($emp['systemType'] == 'CSRS Offset')) && ($retirementAge >= 62)) {
            $firstPension = ($firstPension / 12) - $csrsOffsetPenalty;
            $firstPension = $firstPension * 12;
        }


        if (($emp['systemType'] == 'FERS') || ($emp['systemType'] == 'Transfers')) {
            $cola = $configInc['FERSCola'];
        } else {
            $cola = $configInc['CSRSCola'];
        }

        for ($i = $retirementAge; $i <= 90; $i++) {
            if ($i == $retirementAge) {
                $firstPension = $this->getFirstPension($empId);
                $csrsOffsetPenalty = $this->getCsrsOffsetPenalty($empId);
                if ((($emp['systemType'] == 'CSRS') || ($emp['systemType'] == 'CSRS Offset')) && ($retirementAge >= 62)) {
                    $firstPension = ($firstPension / 12) - $csrsOffsetPenalty;
                    $firstPension = $firstPension * 12;
                }
                $yearsInRet = 0;
            } else {
                $yearsInRet++;
            }

            if (($emp['systemType'] == 'FERS') || ($emp['systemType'] == 'Transfers')) {
                if ($emp['empType'] == "Other") {
                    if ($i > $retirementAge) {
                        $firstPension = $firstPension + ($firstPension * ($configInc['FERSCola'] / 100));
                    }
                } else {
                    if (($i >= 62) && ($i > $retirementAge)) {
                        $firstPension = $firstPension + ($firstPension * ($configInc['FERSCola'] / 100));
                    }
                }
            } else { // CSRS Apply COLA immediatly after retirement
                if ($i > $retirementAge) {
                    $firstPension = $firstPension + ($firstPension * ($configInc['CSRSCola'] / 100));
                }
            }


            if ($emp['RetirementType'] == "Deferred") {
                $firstPension = $this->getFirstPension($empId);
                if ($serviceDuration >= 30) {
                    if (($i >= $mra_y) && ($i <= 62)) {
                        //
                    } elseif ($i > $mra_y) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100));
                    } else {
                        $firstPension = 0;
                    }
                } elseif ($serviceDuration >= 20) {
                    if (($i >= 60) && ($i <= 62)) {
                        //
                    } elseif ($i >= 62) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100));
                    } else {
                        $firstPension = 0;
                    }
                } elseif (($serviceDuration >= 5) || ($serviceDuration <= 19)) {
                    if ($i == 62) {
                        $monthlygrossPension = $firstPension / 12;
                    } elseif ($i > 62) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                        $monthlygrossPension = $firstPension / 12;
                    } else {
                        $firstPension = 0;
                    }
                }
            }

            if ($mra10_penalty > 0) {
                $res = 0;
            } else {

                if (($i >= $retirementAge) && ($i <= 61)) {
                    $SSMonthlyAt62 = $emp['SSMonthlyAt62'];
                    $res = round($SSMonthlyAt62, 2) * $duration / 40;
                    // echo $SSMonthlyAt62 . "*" . $duration . "/ 40";
                    // echo $res;
                    // die;
                } else {
                    $res = 0;
                }

                if (($emp['systemType'] == 'FERS') || ($emp['systemType'] == 'Transfers')) {
                    if ($emp['retirementType'] == 'Early-Out') {
                        if ($i < $mrAgeArr['mra_year']) {
                            $res = 0;
                        }
                    }
                }
                if ($emp['retirementType'] == 'Deferred') {
                    $res = 0;
                }
            }
            $netPension = $firstPension - ($mraPenalty + $earlyOutPenalty);
            $ss_row['years_in_ret'] = $yearsInRet;

            $ss_row['age'] = $i;

            $ss_row['monthly_pension'] = round($netPension / 12, 2);

            if (($i >= $retirementAge) && ($i < 62)) {
                // if ($emp['EmployeeType'] == 'Other') {
                $ss_row['fers_srs'] = $res;
                // } else {

                // if ($i > $emp_age) {
                //     $ss_row['fers_srs'] = round($ss_row['fers_srs'] + ($ss_row['fers_srs'] * $ss_cola) / 100, 2);
                // }
                // }
            } else {
                $ss_row['fers_srs'] = 0;
            }

            if (($i == $ssStartAge_year)) { //  || ($i == $retirementAge)
                $ss_row['ss_benefits'] = $ssAtStartAge;
            } elseif ($i < $ssStartAge_year) {
                $ss_row['ss_benefits'] = 0;
            } else {
                $current_ss_benefits = $current_ss_benefits + ($current_ss_benefits * ($ss_cola / 100));
                $ss_row['ss_benefits'] = $current_ss_benefits;
            }
            $ss_row['total'] = $ss_row['monthly_pension'] + $ss_row['fers_srs'] + $ss_row['ss_benefits'];
            array_push($ss_arr, $ss_row);
        }

        // echo "<pre>"; print_r($ss_arr); exit;
        $data = [
            'ss_details' => $ss_arr,
            'ss_cola' => $ss_cola,
            'ssEarnedIncomeLimit' => $ssEarnedIncomeLimit
        ];
        //die("----");
        return $data;
    }

    public function getWepPenalty($emp, $pia_formula_bend)
    {
        if (($emp->SystemType == "CSRS") || ($emp->SystemType == "CSRS Offset") || ($emp->SystemType == "Transfers")) {
            // step 1
            $halfSSBenefits = $emp->SSMonthlyAtStartAge / 2;
            // Step 2
            $substantialYearPercentage = 0;
            if (round($emp->SSYearsEarning) > 0) {

                if (($emp->SSYearsEarning <= 20) && ($emp->SSYearsEarning > 0)) {
                    $substantialYearPercentage = 40;
                } elseif ($emp->SSYearsEarning == 21) {
                    $substantialYearPercentage = 45;
                } elseif ($emp->SSYearsEarning == 22) {
                    $substantialYearPercentage = 50;
                } elseif ($emp->SSYearsEarning == 23) {
                    $substantialYearPercentage = 55;
                } elseif ($emp->SSYearsEarning == 24) {
                    $substantialYearPercentage = 60;
                } elseif ($emp->SSYearsEarning == 25) {
                    $substantialYearPercentage = 65;
                } elseif ($emp->SSYearsEarning == 26) {
                    $substantialYearPercentage = 70;
                } elseif ($emp->SSYearsEarning == 27) {
                    $substantialYearPercentage = 75;
                } elseif ($emp->SSYearsEarning == 28) {
                    $substantialYearPercentage = 80;
                } elseif ($emp->SSYearsEarning == 29) {
                    $substantialYearPercentage = 85;
                } elseif ($emp->SSYearsEarning >= 30) {
                    $substantialYearPercentage = 90;
                }
            }

            $wepPenalty = ($pia_formula_bend * 90 / 100) - ($pia_formula_bend * $substantialYearPercentage / 100);
            // Step 3
            if ($halfSSBenefits < $wepPenalty) {
                return $halfSSBenefits;
            } else {
                return $wepPenalty;
            }
        } else {
            return 0;
        }
    }

    public function getSystemConfigurations($config = null)
    {
        if ($config != null) {
            return AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', $config)->first()->AppLookupDescription ?? 0;
        }
        return false;
    }

    public function saveSystemConfig($data = [])
    {
        if (!empty($data)) {
            $values = [
                'CSRSCola' => $data['csrs_cola'],
                'FERSCola' => $data['fers_cola'],
                'SalaryIncreaseDefault' => $data['sal_increase_default'],
                'SalaryIncrease' => $data['sal_increase'],
                'SalaryIncrease1' => $data['sal_increase_1'],
                'SalaryIncrease2' => $data['sal_increase_2'],
                'SSCola' => $data['ss_cola'],
                'PIAFormula' => $data['pia_formula_fb'],
                'SSEarnedIncomeLimit' => $data['income_limit'],
                'FEHBAveragePremiumIncrease' => $data['avg_prem_increase'],
                'TSPDeferralLimit' => $data['deff_limit'],
                'TSPCatchUpLimit' => $data['catchup_limit'],
                'TSPGFundReturn' => $data['gfund'],
                'TSPFFundReturn' => $data['ffund'],
                'TSPCFundReturn' => $data['cfund'],
                'TSPSFundReturn' => $data['sfund'],
                'TSPIFundReturn' => $data['ifund'],
                'daily_benefit_amount' => $data['daily_benefit_amount'],
                'benefit_period' => $data['benefit_period'],
                'waiting_period' => $data['waiting_period'],
                'inflation_protection' => $data['inflation_protection'],
                'total_allowed_contri_for_age_less_than_50' => $data['total_allowed_contri_for_age_less_than_50'],
                'total_allowed_contri_for_age_50_or_greater' => $data['total_allowed_contri_for_age_50_or_greater'],
                'year_of_contribution' => $data['year_of_contribution'],
                'WhileWorkingBasicCostPer1000AgeLessThan35' => $data['WhileWorkingBasicCostPer1000AgeLessThan35'],
                'WhileWorkingBasicCostPer1000Age35To39' => $data['WhileWorkingBasicCostPer1000Age35To39'],
                'WhileWorkingBasicCostPer1000Age40To44' => $data['WhileWorkingBasicCostPer1000Age40To44'],
                'WhileWorkingBasicCostPer1000Age45To49' => $data['WhileWorkingBasicCostPer1000Age45To49'],
                'WhileWorkingBasicCostPer1000Age50To54' => $data['WhileWorkingBasicCostPer1000Age50To54'],
                'WhileWorkingBasicCostPer1000Age55To59' => $data['WhileWorkingBasicCostPer1000Age55To59'],
                'WhileWorkingBasicCostPer1000Age60To64' => $data['WhileWorkingBasicCostPer1000Age60To64'],
                'WhileWorkingBasicCostPer1000Age65To69' => $data['WhileWorkingBasicCostPer1000Age65To69'],
                'WhileWorkingBasicCostPer1000Age70To74' => $data['WhileWorkingBasicCostPer1000Age70To74'],
                'WhileWorkingBasicCostPer1000Age75To79' => $data['WhileWorkingBasicCostPer1000Age75To79'],
                'WhileWorkingBasicCostPer1000Age80orGreater' => $data['WhileWorkingBasicCostPer1000Age80orGreater'],
                'WhileWorkingOptionACostPer1000AgeLessThan35' => $data['WhileWorkingOptionACostPer1000AgeLessThan35'],
                'WhileWorkingOptionACostPer1000Age35To39' => $data['WhileWorkingOptionACostPer1000Age35To39'],
                'WhileWorkingOptionACostPer1000Age40To44' => $data['WhileWorkingOptionACostPer1000Age40To44'],
                'WhileWorkingOptionACostPer1000Age45To49' => $data['WhileWorkingOptionACostPer1000Age45To49'],
                'WhileWorkingOptionACostPer1000Age50To54' => $data['WhileWorkingOptionACostPer1000Age50To54'],
                'WhileWorkingOptionACostPer1000Age55To59' => $data['WhileWorkingOptionACostPer1000Age55To59'],
                'WhileWorkingOptionACostPer1000Age60To64' => $data['WhileWorkingOptionACostPer1000Age60To64'],
                'WhileWorkingOptionACostPer1000Age65To69' => $data['WhileWorkingOptionACostPer1000Age65To69'],
                'WhileWorkingOptionACostPer1000Age70To74' => $data['WhileWorkingOptionACostPer1000Age70To74'],
                'WhileWorkingOptionACostPer1000Age75To79' => $data['WhileWorkingOptionACostPer1000Age75To79'],
                'WhileWorkingOptionACostPer1000Age80orGreater' => $data['WhileWorkingOptionACostPer1000Age80orGreater'],
                'WhileWorkingOptionBCostPer1000AgeLessThan35' => $data['WhileWorkingOptionBCostPer1000AgeLessThan35'],
                'WhileWorkingOptionBCostPer1000Age35To39' => $data['WhileWorkingOptionBCostPer1000Age35To39'],
                'WhileWorkingOptionBCostPer1000Age40To44' => $data['WhileWorkingOptionBCostPer1000Age40To44'],
                'WhileWorkingOptionBCostPer1000Age45To49' => $data['WhileWorkingOptionBCostPer1000Age45To49'],
                'WhileWorkingOptionBCostPer1000Age50To54' => $data['WhileWorkingOptionBCostPer1000Age50To54'],
                'WhileWorkingOptionBCostPer1000Age55To59' => $data['WhileWorkingOptionBCostPer1000Age55To59'],
                'WhileWorkingOptionBCostPer1000Age60To64' => $data['WhileWorkingOptionBCostPer1000Age60To64'],
                'WhileWorkingOptionBCostPer1000Age65To69' => $data['WhileWorkingOptionBCostPer1000Age65To69'],
                'WhileWorkingOptionBCostPer1000Age70To74' => $data['WhileWorkingOptionBCostPer1000Age70To74'],
                'WhileWorkingOptionBCostPer1000Age75To79' => $data['WhileWorkingOptionBCostPer1000Age75To79'],
                'WhileWorkingOptionBCostPer1000Age80orGreater' => $data['WhileWorkingOptionBCostPer1000Age80orGreater'],
                'WhileWorkingOptionCCostPer1000AgeLessThan35' => $data['WhileWorkingOptionCCostPer1000AgeLessThan35'],
                'WhileWorkingOptionCCostPer1000Age35To39' => $data['WhileWorkingOptionCCostPer1000Age35To39'],
                'WhileWorkingOptionCCostPer1000Age40To44' => $data['WhileWorkingOptionCCostPer1000Age40To44'],
                'WhileWorkingOptionCCostPer1000Age45To49' => $data['WhileWorkingOptionCCostPer1000Age45To49'],
                'WhileWorkingOptionCCostPer1000Age50To54' => $data['WhileWorkingOptionCCostPer1000Age50To54'],
                'WhileWorkingOptionCCostPer1000Age55To59' => $data['WhileWorkingOptionCCostPer1000Age55To59'],
                'WhileWorkingOptionCCostPer1000Age60To64' => $data['WhileWorkingOptionCCostPer1000Age60To64'],
                'WhileWorkingOptionCCostPer1000Age65To69' => $data['WhileWorkingOptionCCostPer1000Age65To69'],
                'WhileWorkingOptionCCostPer1000Age70To74' => $data['WhileWorkingOptionCCostPer1000Age70To74'],
                'WhileWorkingOptionCCostPer1000Age75To79' => $data['WhileWorkingOptionCCostPer1000Age75To79'],
                'WhileWorkingOptionCCostPer1000Age80orGreater' => $data['WhileWorkingOptionCCostPer1000Age80orGreater'],
                'InRetirementBasicCostPer1000Age50To54NoReduction' => $data['InRetirementBasicCostPer1000Age50To54NoReduction'],
                'InRetirementBasicCostPer1000Age50To54Reduction50' => $data['InRetirementBasicCostPer1000Age50To54Reduction50'],
                'InRetirementBasicCostPer1000Age50To54Reduction75' => $data['InRetirementBasicCostPer1000Age50To54Reduction75'],
                'InRetirementBasicCostPer1000Age55To59NoReduction' => $data['InRetirementBasicCostPer1000Age55To59NoReduction'],
                'InRetirementBasicCostPer1000Age55To59Reduction50' => $data['InRetirementBasicCostPer1000Age55To59Reduction50'],
                'InRetirementBasicCostPer1000Age55To59Reduction75' => $data['InRetirementBasicCostPer1000Age55To59Reduction75'],
                'InRetirementBasicCostPer1000Age60To64NoReduction' => $data['InRetirementBasicCostPer1000Age60To64NoReduction'],
                'InRetirementBasicCostPer1000Age60To64Reduction50' => $data['InRetirementBasicCostPer1000Age60To64Reduction50'],
                'InRetirementBasicCostPer1000Age60To64Reduction75' => $data['InRetirementBasicCostPer1000Age60To64Reduction75'],
                'InRetirementBasicCostPer1000Age65To69NoReduction' => $data['InRetirementBasicCostPer1000Age65To69NoReduction'],
                'InRetirementBasicCostPer1000Age65To69Reduction50' => $data['InRetirementBasicCostPer1000Age65To69Reduction50'],
                'InRetirementOptionACostPer1000Age50To54Reduction75' => $data['InRetirementOptionACostPer1000Age50To54Reduction75'],
                'InRetirementOptionACostPer1000Age55To59Reduction75' => $data['InRetirementOptionACostPer1000Age55To59Reduction75'],
                'InRetirementOptionACostPer1000Age60To64Reduction75' => $data['InRetirementOptionACostPer1000Age60To64Reduction75'],
                'InRetirementOptionBCostPer1000Age50To54FullReduction' => $data['InRetirementOptionBCostPer1000Age50To54FullReduction'],
                'InRetirementOptionBCostPer1000Age55To59FullReduction' => $data['InRetirementOptionBCostPer1000Age55To59FullReduction'],
                'InRetirementOptionBCostPer1000Age60To64FullReduction' => $data['InRetirementOptionBCostPer1000Age60To64FullReduction'],
                'InRetirementOptionBCostPer1000Age50To54NoReduction' => $data['InRetirementOptionBCostPer1000Age50To54NoReduction'],
                'InRetirementOptionBCostPer1000Age55To59NoReduction' => $data['InRetirementOptionBCostPer1000Age55To59NoReduction'],
                'InRetirementOptionBCostPer1000Age60To64NoReduction' => $data['InRetirementOptionBCostPer1000Age60To64NoReduction'],
                'InRetirementOptionBCostPer1000Age65To69NoReduction' => $data['InRetirementOptionBCostPer1000Age65To69NoReduction'],
                'InRetirementOptionBCostPer1000Age70To74NoReduction' => $data['InRetirementOptionBCostPer1000Age70To74NoReduction'],
                'InRetirementOptionBCostPer1000Age75To79NoReduction' => $data['InRetirementOptionBCostPer1000Age75To79NoReduction'],
                'InRetirementOptionBCostPer1000Age80NoReduction' => $data['InRetirementOptionBCostPer1000Age80NoReduction'],
                'InRetirementOptionCCostPer1000Age50To54FullReduction' => $data['InRetirementOptionCCostPer1000Age50To54FullReduction'],
                'InRetirementOptionCCostPer1000Age55To59FullReduction' => $data['InRetirementOptionCCostPer1000Age55To59FullReduction'],
                'InRetirementOptionCCostPer1000Age60To64FullReduction' => $data['InRetirementOptionCCostPer1000Age60To64FullReduction'],
                'InRetirementOptionCCostPer1000Age50To54NoReduction' => $data['InRetirementOptionCCostPer1000Age50To54NoReduction'],
                'InRetirementOptionCCostPer1000Age55To59NoReduction' => $data['InRetirementOptionCCostPer1000Age55To59NoReduction'],
                'InRetirementOptionCCostPer1000Age60To64NoReduction' => $data['InRetirementOptionCCostPer1000Age60To64NoReduction'],
                'InRetirementOptionCCostPer1000Age65To69NoReduction' => $data['InRetirementOptionCCostPer1000Age65To69NoReduction'],
                'InRetirementOptionCCostPer1000Age70To74NoReduction' => $data['InRetirementOptionCCostPer1000Age70To74NoReduction'],
                'InRetirementOptionCCostPer1000Age75To79NoReduction' => $data['InRetirementOptionCCostPer1000Age75To79NoReduction'],
                'InRetirementOptionCCostPer1000Age80NoReduction' => $data['InRetirementOptionCCostPer1000Age80NoReduction'],
                'InRetirementBasicCostPer1000Age70To74NoReduction' => $data['InRetirementBasicCostPer1000Age70To74NoReduction'],
                'InRetirementBasicCostPer1000Age70To74Reduction50' => $data['InRetirementBasicCostPer1000Age70To74Reduction50'],
                'InRetirementBasicCostPer1000Age75To79NoReduction' => $data['InRetirementBasicCostPer1000Age75To79NoReduction'],
                'InRetirementBasicCostPer1000Age75To79Reduction50' => $data['InRetirementBasicCostPer1000Age75To79Reduction50'],
                'InRetirementBasicCostPer1000Age80NoReduction' => $data['InRetirementBasicCostPer1000Age80NoReduction'],
                'InRetirementBasicCostPer1000Age80Reduction50' => $data['InRetirementBasicCostPer1000Age80Reduction50'],
            ];
            foreach ($values as $key => $val) {
                $res = AppLookup::updateORCreate([
                    'AppLookupTypeName' => 'EmployeeConfig',
                    'AppLookupName' => $key
                ], [
                    'AppLookupDescription' => $val,
                ]);
            }

            // $tsp = $data['tsp'];

            // foreach ($tsp as $key1 => $val1) {
            //     $res2 = TspCalculation::where('year', $val1['year'])
            //         ->update(['g' => $val1['g'], 'f' => $val1['f'], 'c' => $val1['c'], 's' => $val1['s'], 'i' => $val1['i']]);
            // }

            if ($res) {
                return true;
            }
            return false;
        }
    }

    public function saveEmployeeConf($empId = null, $data = [])
    {
        if (!empty($data) && ($empId != null)) {
            $emp = Employee::where('EmployeeId', $empId)->first();
            if ($emp) {
                $conf_names = [
                    'CSRSCola' => $data['csrs_cola'],
                    'FERSCola' => $data['fers_cola'],
                    'SalaryIncreaseDefault' => $data['sal_increase'],
                    'SSCola' => $data['ss_cola'],
                    'PIAFormula' => $data['pia_formula_fb'],
                    'SSEarnedIncomeLimit' => $data['income_limit'],
                    'FEHBAveragePremiumIncrease' => $data['avg_prem_increase'],
                    'TSPDeferralLimit' => $data['deff_limit'],
                    'TSPCatchUpLimit' => $data['catchup_limit'],
                    'TSPGFundReturn' => $data['gfund'],
                    'TSPFFundReturn' => $data['ffund'],
                    'TSPCFundReturn' => $data['cfund'],
                    'TSPSFundReturn' => $data['sfund'],
                    'TSPIFundReturn' => $data['ifund'],
                ];

                $history_fields['EmployeeConfig'] = [];
                foreach ($conf_names as $key => $val) {
                    $conf = EmployeeConfig::where([
                        'EmployeeId' => $empId,
                        'ConfigType' => $key
                    ])->first();
                    if ($conf) {
                        $old_val = $conf->ConfigValue;
                        if ($old_val != $val) {
                            $history_fields['EmployeeConfig'][] = [
                                'column_name' => $key,
                                'old_value' => $old_val,
                                'new_value' => $val
                            ];
                        }
                    }
                    $res = EmployeeConfig::updateORCreate([
                        'EmployeeId' => $empId,
                        'ConfigType' => $key
                    ], [
                        'ConfigValue' => $val,
                    ]);
                }
                $row_id = 0;
                $res = $this->updateHistory($empId, $history_fields, $row_id, 'configuration');

                return true;
            }
        }
        return false;
    }

    public function getEmployeeConf($empId = null)
    {
        $conf_names = [
            'CSRSCola', 'FERSCola', 'SalaryIncreaseDefault', 'SalaryIncrease', 'SalaryIncrease1', 'SalaryIncrease2', 'SalaryIncrease2', 'SSCola', 'PIAFormula', 'SSEarnedIncomeLimit', 'FEHBAveragePremiumIncrease', 'daily_benefit_amount', 'benefit_period', 'waiting_period', 'inflation_protection'
        ];
        $conf = [];
        foreach ($conf_names as $nm) {

            $conf_obj = AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', $nm)->first();
            if ($conf_obj) {
                $conf[$nm] = $conf_obj->AppLookupDescription;
            } else {
                $conf[$nm] = '';
            }

            $empConf = EmployeeConfig::where('EmployeeId', $empId)->where('ConfigType', $nm)->first();
            if ($empConf) {
                // if ($empConf->ConfigValue > 0) { // 0 is valid valur for salaryIncreaseDefault
                $conf[$nm] = $empConf->ConfigValue;
                // }
            }
        }
        return $conf;
    }

    public function getEmpTspConfigurations($empID = null)
    {
        if ($empID != null) {
            $confs = [
                'deff_limit' => 'TSPDeferralLimit',
                'catchup_limit' => 'TSPCatchUpLimit',
                'gfund' => 'TSPGFundReturn',
                'ffund' => 'TSPFFundReturn',
                'cfund' => 'TSPCFundReturn',
                'sfund' => 'TSPSFundReturn',
                'ifund' => 'TSPIFundReturn',
                'year_of_contribution' => 'year_of_contribution'
            ];
            foreach ($confs as $key => $val) {
                $empConf = EmployeeConfig::where('EmployeeId', $empID)->where('ConfigType', $val)->first();
                if ($empConf) {
                    $data[$key] = $empConf->ConfigValue;
                } else {
                    $data[$key] = AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', $val)->first()->AppLookupDescription;
                }
            }
            return $data;
        }
    }

    public function getEmpTspDetails($empId = null)
    {
        $emp = $this->getById($empId);
        $tsp = Tsp::find($empId);
        if (!is_null($tsp)) {
            if (round($emp->CurrentSalary) == 0) {
                return [];
            }
            $tsp = $tsp->toArray();

            $tsp['current_salary'] = $emp->CurrentSalary;
            $tsp['regularContriPercentage'] = (($tsp['ContributionRegular'] * 26) / $emp->CurrentSalary) * 100;
            $tsp['catchupContriPercentage'] = (($tsp['ContributionCatchUp'] * 26) / $emp->CurrentSalary) * 100;
            $tsp['totalCurrentBalance'] = $tsp['GFund'] + $tsp['FFund'] + $tsp['CFund'] + $tsp['SFund'] + $tsp['IFund'] + $tsp['LIncome'] + $tsp['L2025'] + $tsp['L2030'] + $tsp['L2035'] + $tsp['L2040'] + $tsp['L2045'] + $tsp['L2050'] + $tsp['L2055'] + $tsp['L2060'] + $tsp['L2065'];
            $tsp['totalContributions'] = $tsp['ContributionRegular'] + $tsp['ContributionCatchUp'];
            $tsp['contriSalPercentage'] = ($tsp['totalContributions'] / $emp->CurrentSalary) * 100;
        } else {
            $tsp = [];
        }
        return $tsp;
    }

    public function updateEmployeeTsp($empId = null, $data = [])
    {
        if (($empId != null) && !empty($data)) {
            $emp = resolve('employee')->getById($empId);
            $current_sal = $emp->CurrentSalary;
            if ($current_sal != $data['current_salary']) {
                $eObj = Employee::find($empId);
                if ($eObj->CurrentSalary != $data['current_salary']) {
                    $history_fields['Employee'][] = [
                        'column_name' => 'CurrentSalary',
                        'old_value' => $eObj->CurrentSalary,
                        'new_value' => $data['current_salary']
                    ];
                    $row_id = 0;
                    $res = $this->updateHistory($empId, $history_fields, $row_id, 'tsp');
                }
                $eObj->CurrentSalary = $data['current_salary'];
                $sal_res = $eObj->save();
                if (!$sal_res) {
                    return false;
                }
            }

            $sal_inc_conf = EmployeeConfig::where([
                'EmployeeId' => $empId,
                'ConfigType' => 'SalaryIncreaseDefault'
            ])->first();
            $history_fields['EmployeeConfig'] = [];
            if ($sal_inc_conf) {
                if ($sal_inc_conf->ConfigValue != $data['annual_increase']) {
                    $history_fields['EmployeeConfig'][] = [
                        'column_name' => 'CurrentSalary',
                        'old_value' => $current_sal,
                        'new_value' => $data['current_salary']
                    ];
                }
            }
            $sal_ires = EmployeeConfig::updateORCreate([
                'EmployeeId' => $empId,
                'ConfigType' => 'SalaryIncreaseDefault'
            ], [
                'ConfigValue' => $data['annual_increase'],
            ]);
            if (!$sal_ires) {
                return false;
            }
            $row_id = 0;
            $res = $this->updateHistory($empId, $history_fields, $row_id, 'tsp');
            $history_fields['TSP'] = [];
            $tsp = Tsp::find($empId);
            if (!$tsp) {
                $tsp = new Tsp();
            } else {
                if ($tsp->StatementDate != date('Y-m-d H:i:s', strtotime($data['statement_date']))) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'StatementDate',
                        'old_value' => $tsp->StatementDate,
                        'new_value' => date('Y-m-d H:i:s', strtotime($data['statement_date']))
                    ];
                }
                if ($tsp->GFund != $data['gfund']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'GFund',
                        'old_value' => $tsp->GFund,
                        'new_value' => $data['gfund']
                    ];
                }
                if ($tsp->FFund != $data['ffund']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'FFund',
                        'old_value' => $tsp->FFund,
                        'new_value' => $data['ffund']
                    ];
                }
                if ($tsp->CFund != $data['cfund']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'CFund',
                        'old_value' => $tsp->CFund,
                        'new_value' => $data['cfund']
                    ];
                }
                if ($tsp->SFund != $data['sfund']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'SFund',
                        'old_value' => $tsp->SFund,
                        'new_value' => $data['sfund']
                    ];
                }
                if ($tsp->IFund != $data['ifund']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'IFund',
                        'old_value' => $tsp->IFund,
                        'new_value' => $data['ifund']
                    ];
                }
                if ($tsp->L2025 != $data['l2025']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2025',
                        'old_value' => $tsp->L2025,
                        'new_value' => $data['l2025']
                    ];
                }
                if ($tsp->L2030 != $data['l2030']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2030',
                        'old_value' => $tsp->L2030,
                        'new_value' => $data['l2030']
                    ];
                }
                if ($tsp->L2035 != $data['l2035']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2035',
                        'old_value' => $tsp->L2035,
                        'new_value' => $data['l2035']
                    ];
                }
                if ($tsp->L2040 != $data['l2040']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2040',
                        'old_value' => $tsp->L2040,
                        'new_value' => $data['l2040']
                    ];
                }
                if ($tsp->L2045 != $data['l2045']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2045',
                        'old_value' => $tsp->L2045,
                        'new_value' => $data['l2045']
                    ];
                }
                if ($tsp->L2050 != $data['l2050']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2050',
                        'old_value' => $tsp->L2050,
                        'new_value' => $data['l2050']
                    ];
                }
                if ($tsp->L2055 != $data['l2055']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2055',
                        'old_value' => $tsp->L2055,
                        'new_value' => $data['l2055']
                    ];
                }
                if ($tsp->L2060 != $data['l2060']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2060',
                        'old_value' => $tsp->L2060,
                        'new_value' => $data['l2060']
                    ];
                }
                if ($tsp->L2065 != $data['l2065']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2065',
                        'old_value' => $tsp->L2065,
                        'new_value' => $data['l2065']
                    ];
                }
                if ($tsp->LIncome != $data['lincome']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'LIncome',
                        'old_value' => $tsp->LIncome,
                        'new_value' => $data['lincome']
                    ];
                }
                if ($tsp->GFundDist != $data['gfund_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'GFundDist',
                        'old_value' => $tsp->GFundDist,
                        'new_value' => $data['gfund_distri']
                    ];
                }
                if ($tsp->FFundDist != $data['ffund_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'FFundDist',
                        'old_value' => $tsp->FFundDist,
                        'new_value' => $data['ffund_distri']
                    ];
                }
                if ($tsp->CFundDist != $data['cfund_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'CFundDist',
                        'old_value' => $tsp->CFundDist,
                        'new_value' => $data['cfund_distri']
                    ];
                }
                if ($tsp->SFundDist != $data['sfund_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'SFundDist',
                        'old_value' => $tsp->SFundDist,
                        'new_value' => $data['sfund_distri']
                    ];
                }
                if ($tsp->IFundDist != $data['ifund_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'IFundDist',
                        'old_value' => $tsp->IFundDist,
                        'new_value' => $data['ifund_distri']
                    ];
                }
                if ($tsp->L2025Dist != $data['l2025_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2025Dist',
                        'old_value' => $tsp->L2025Dist,
                        'new_value' => $data['l2025_distri']
                    ];
                }
                if ($tsp->L2030Dist != $data['l2030_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2030Dist',
                        'old_value' => $tsp->L2030Dist,
                        'new_value' => $data['l2030_distri']
                    ];
                }
                if ($tsp->L2035Dist != $data['l2035_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2035Dist',
                        'old_value' => $tsp->L2035Dist,
                        'new_value' => $data['l2035_distri']
                    ];
                }
                if ($tsp->L2040Dist != $data['l2040_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2040Dist',
                        'old_value' => $tsp->L2040Dist,
                        'new_value' => $data['l2040_distri']
                    ];
                }
                if ($tsp->L2045Dist != $data['l2045_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2045Dist',
                        'old_value' => $tsp->L2045Dist,
                        'new_value' => $data['l2045_distri']
                    ];
                }
                if ($tsp->L2050Dist != $data['l2050_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2050Dist',
                        'old_value' => $tsp->L2050Dist,
                        'new_value' => $data['l2050_distri']
                    ];
                }
                if ($tsp->L2055Dist != $data['l2055_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2055Dist',
                        'old_value' => $tsp->L2055Dist,
                        'new_value' => $data['l2055_distri']
                    ];
                }
                if ($tsp->L2060Dist != $data['l2060_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2060Dist',
                        'old_value' => $tsp->L2060Dist,
                        'new_value' => $data['l2060_distri']
                    ];
                }
                if ($tsp->L2065Dist != $data['l2065_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'L2065Dist',
                        'old_value' => $tsp->L2065Dist,
                        'new_value' => $data['l2065_distri']
                    ];
                }
                if ($tsp->LIncomeDist != $data['lincome_distri']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'LIncomeDist',
                        'old_value' => $tsp->LIncomeDist,
                        'new_value' => $data['lincome_distri']
                    ];
                }
                if ($tsp->ContributionRegular != $data['regular_tsp_contribution']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'ContributionRegular',
                        'old_value' => $tsp->ContributionRegular,
                        'new_value' => $data['regular_tsp_contribution']
                    ];
                }
                if ($tsp->ContributionCatchUp != $data['tsp_contribution_catchup']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'ContributionCatchUp',
                        'old_value' => $tsp->ContributionCatchUp,
                        'new_value' => $data['tsp_contribution_catchup']
                    ];
                }
                if ($tsp->loan_balance_general != $data['loan_balance_general']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'loan_balance_general',
                        'old_value' => $tsp->loan_balance_general,
                        'new_value' => $data['loan_balance_general']
                    ];
                }
                if ($tsp->loan_repayment_general != $data['loan_repayment_general']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'loan_repayment_general',
                        'old_value' => $tsp->loan_repayment_general,
                        'new_value' => $data['loan_repayment_general']
                    ];
                }
                if ($tsp->payoff_date_general != date('Y-m-d H:i:s', strtotime($data['payoff_date_general']))) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'payoff_date_general',
                        'old_value' => $tsp->payoff_date_general,
                        'new_value' => date('Y-m-d H:i:s', strtotime($data['payoff_date_general']))
                    ];
                }
                if ($tsp->loan_balance_residential != $data['loan_balance_residential']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'loan_balance_residential',
                        'old_value' => $tsp->loan_balance_residential,
                        'new_value' => $data['loan_balance_residential']
                    ];
                }
                if ($tsp->loan_repayment_residential != $data['loan_repayment_residential']) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'loan_repayment_residential',
                        'old_value' => $tsp->loan_repayment_residential,
                        'new_value' => $data['loan_repayment_residential']
                    ];
                }
                if ($tsp->payoff_date_residential != date('Y-m-d H:i:s', strtotime($data['payoff_date_residential']))) {
                    $history_fields['TSP'][] = [
                        'column_name' => 'payoff_date_residential',
                        'old_value' => $tsp->payoff_date_residential,
                        'new_value' => date('Y-m-d H:i:s', strtotime($data['payoff_date_residential']))
                    ];
                }
            }


            $tsp->StatementDate = ($data['statement_date'] != null) ? date('Y-m-d H:i:s', strtotime($data['statement_date'])) : NULL;
            $tsp->GFund = $data['gfund'];
            $tsp->FFund = $data['ffund'];
            $tsp->CFund = $data['cfund'];
            $tsp->SFund = $data['sfund'];
            $tsp->IFund = $data['ifund'];
            $tsp->L2025 = $data['l2025'];
            $tsp->L2030 = $data['l2030'];
            $tsp->L2035 = $data['l2035'];
            $tsp->L2040 = $data['l2040'];
            $tsp->L2045 = $data['l2045'];
            $tsp->L2050 = $data['l2050'];
            $tsp->L2055 = $data['l2055'];
            $tsp->L2060 = $data['l2060'];
            $tsp->L2065 = $data['l2065'];
            $tsp->LIncome = $data['lincome'];
            $tsp->GFundDist = $data['gfund_distri'];
            $tsp->FFundDist = $data['ffund_distri'];
            $tsp->CFundDist = $data['cfund_distri'];
            $tsp->SFundDist = $data['sfund_distri'];
            $tsp->IFundDist = $data['ifund_distri'];
            $tsp->L2025Dist = $data['l2025_distri'];
            $tsp->L2030Dist = $data['l2030_distri'];
            $tsp->L2035Dist = $data['l2035_distri'];
            $tsp->L2040Dist = $data['l2040_distri'];
            $tsp->L2045Dist = $data['l2045_distri'];
            $tsp->L2050Dist = $data['l2050_distri'];
            $tsp->L2055Dist = $data['l2055_distri'];
            $tsp->L2060Dist = $data['l2060_distri'];
            $tsp->L2065Dist = $data['l2065_distri'];
            $tsp->LIncomeDist = $data['lincome_distri'];
            $tsp->ContributionRegular = $data['regular_tsp_contribution'];
            $tsp->ContributionCatchUp = $data['tsp_contribution_catchup'];
            // $tsp->EndingBalance = $data[''];
            // $tsp->LoanRepayment = $data['loan_repayment'];

            $tsp->loan_balance_general = $data['loan_balance_general'];
            $tsp->loan_repayment_general = $data['loan_repayment_general'];


            $tsp->loan_balance_residential = $data['loan_balance_residential'];
            $tsp->loan_repayment_residential = $data['loan_repayment_residential'];


            $tsp->EmployeeId = $empId;

            if (isset($data['statement_date']) && ($data['statement_date'] != NULL) && ($data['payoff_date_general'] == NULL)) {
                if ($data['loan_balance_general'] != 0.00 && $data['loan_repayment_general'] != 0.00) {
                    $payoff_date_general_years = (string) round(($data['loan_balance_general'] / $data['loan_repayment_general']) / 26, 1);
                    $g_arr = explode('.', $payoff_date_general_years);
                    $payoff_year = $g_arr[0] ?? 0;
                    $payoff_month = $g_arr[1] ?? 0;
                    // echo "<pre>"; print_r($g_arr); exit;
                    $statement_date = new \DateTime($data['statement_date']);
                    $statement_date->modify('+' . $payoff_year . ' years');
                    $statement_date->modify('+' . $payoff_month . ' months');
                    $data['payoff_date_general'] = $statement_date->format('Y-m-t H:i:s');
                }
            }
            // echo $tsp['payoff_date_general'] . "<br>";
            // die;
            if ((isset($data['statement_date']) && ($data['statement_date'] != "") && $data['payoff_date_residential'] == "")) {
                // echo "here1" . $tsp['loan_balance_residential'] . "<br>";
                if ($data['loan_balance_residential'] != 0 && $data['loan_repayment_residential'] != 0) {
                    // echo "here2";
                    $payoff_date_residential_years = round(($data['loan_balance_residential'] / $data['loan_repayment_residential']) / 26, 1);
                    $r_arr = explode('.', $payoff_date_residential_years);
                    $rpayoff_year = $r_arr[0] ?? 0;
                    $rpayoff_month = $r_arr[1] ?? 0;
                    $statement_date = new \DateTime($data['statement_date']);
                    $statement_date->modify('+' . $rpayoff_year . ' years');
                    $statement_date->modify('+' . $rpayoff_month . ' months');
                    $data['payoff_date_residential'] = $statement_date->format('Y-m-d');
                }
            }

            $tsp->payoff_date_general = ($data['payoff_date_general'] != null) ? date('Y-m-d H:i:s', strtotime($data['payoff_date_general'])) : NULL;
            $tsp->payoff_date_residential = ($data['payoff_date_residential'] != null) ? date('Y-m-d H:i:s', strtotime($data['payoff_date_residential'])) : NULL;


            if ($tsp->save()) {
                $row_id = 0;
                $res = $this->updateHistory($empId, $history_fields, $row_id, 'tsp');
                return true;
            }
        }
        return false;
    }

    public function getTspCalculation($year, $tspfund)
    {
        $query = TspCalculation::where('year', 2020)->get()->toArray();
        $res = array();
        foreach ($query as $key => $value) {
            unset($value['id']);
            unset($value['year']);
            $res['g'] = ($tspfund * $value['g'] / 100);
            $res['f'] = ($tspfund * $value['f'] / 100);
            $res['s'] = ($tspfund * $value['s'] / 100);
            $res['c'] = ($tspfund * $value['c'] / 100);
            $res['i'] = ($tspfund * $value['i'] / 100);
        }

        return $res;
    }

    public function getEmployeeOtherDeduction($empId = null)
    {
        $result = Deduction::where('EmployeeId', $empId)->where('IsOther', 1)->get();
        if ($result) {
            return $result->toArray();
        }
        return false;
    }

    public function calcProjectedEndingBalanceTsp($empId = null)
    {
        $tsp_contri_info = [];
        if ($empId != null) {
            $emp = $this->getById($empId);
            if ($emp) {
                $tsp = $this->getEmpTspDetails($empId);
                if ($tsp) {
                    $employeeAdditionalContri = $tsp['ContributionRegular'] + $tsp['ContributionCatchUp'];

                    $salPercentageOfContri = (($employeeAdditionalContri * 26) / $emp['CurrentSalary']) * 100;

                    $agencyContribution1 = ($emp['CurrentSalary'] / 100);
                    $sal_pp = $emp['CurrentSalary'] / 26;
                    $agencyContriPerc = 0;
                    if (($salPercentageOfContri >= 1) && ($salPercentageOfContri < 2)) {
                        $agencyContriPerc = 1;
                    } elseif (($salPercentageOfContri >= 2) && ($salPercentageOfContri < 3)) {
                        $agencyContriPerc = 2;
                    } elseif (($salPercentageOfContri >= 3) && ($salPercentageOfContri < 4)) {
                        $agencyContriPerc = 3;
                    } elseif (($salPercentageOfContri >= 4) && ($salPercentageOfContri < 5)) {
                        $agencyContriPerc = 3.5;
                    } elseif ($salPercentageOfContri >= 5) {
                        $agencyContriPerc = 4;
                    }
                    $agencyContribution2 = $emp['CurrentSalary'] * ($agencyContriPerc / 100);
                    $max_agency_contribution = $emp['CurrentSalary'] * (4 / 100);
                    if ($salPercentageOfContri < 5) {
                        $annualMatch = $this->tspAnnualMatch($sal_pp, $salPercentageOfContri);
                        $full5Per = ($sal_pp * 5 / 100) * 26;
                        $missingOutAmount = $full5Per - $annualMatch;
                    } else {
                        if ($salPercentageOfContri < 4) {
                            $missing_out_percentage = (4 - $salPercentageOfContri);
                        } else {
                            $missing_out_percentage = 0;
                        }
                        $missingOutAmount = $emp['CurrentSalary'] * ($missing_out_percentage / 100);
                    }

                    $tsp_contri_info['agency_percentage'] = $agencyContriPerc;
                    $tsp_contri_info['agency_contribution'] = $agencyContribution2;
                    $tsp_contri_info['employee_contribution'] = $employeeAdditionalContri;
                    $tsp_contri_info['sal_percentage_in_contri'] = $salPercentageOfContri;
                    $tsp_contri_info['max_agency_contribution'] = $max_agency_contribution;
                    $tsp_contri_info['missing_out_amount'] = number_format(round($missingOutAmount)); // round(($max_agency_contribution - $agencyContribution2));
                    $tsp_contri_info['agency_contribution'] = $agencyContribution1 + $agencyContribution2;
                    $tsp_contri_info['emp_min_contri_for_full_agency_contri'] = number_format(round(($emp['CurrentSalary'] * 5 / 100) / 26));
                }
            }
        }
        return $tsp_contri_info;
    }

    public function tspAnnualMatch($salary_pp, $sal_contri)
    {
        $gov_auto_contri = $salary_pp / 100;

        if ($sal_contri <= 3) {
            $total_gov_sal = $salary_pp * $sal_contri / 100;
        } else {
            $gov_contri_3per = $salary_pp * 3 / 100;

            $remaining_gov_contri = ($salary_pp * ($sal_contri - 3) / 100) / 2;

            $total_gov_sal = $gov_contri_3per + $remaining_gov_contri;
        }

        return ($gov_auto_contri + $total_gov_sal) * 26;
    }

    public function updatePayAndLeave($empId = null, $data = [])
    {
        if (($empId != null) && !empty($data)) {
            if ($data['current_salary'] == 0) {
                return false;
            }
            $eObj = Employee::find($empId);
            $history_fields['Employee'] = [];
            if ($eObj->CurrentSalary != $data['current_salary']) {
                $history_fields['Employee'][] = [
                    'column_name' => 'CurrentSalary',
                    'old_value' => $eObj->CurrentSalary,
                    'new_value' => $data['current_salary']
                ];
            }

            if ($eObj->High3Average != $data['high3_avg']) {
                $history_fields['Employee'][] = [
                    'column_name' => 'High3Average',
                    'old_value' => $eObj->High3Average,
                    'new_value' => $data['high3_avg']
                ];
            }
            if ($eObj->UnusedSickLeave != $data['unusual_sick_leave']) {
                $history_fields['Employee'][] = [
                    'column_name' => 'UnusedSickLeave',
                    'old_value' => $eObj->UnusedSickLeave,
                    'new_value' => $data['unusual_sick_leave']
                ];
            }
            if ($eObj->UnusedAnnualLeave != $data['unusual_annual_leave']) {
                $history_fields['Employee'][] = [
                    'column_name' => 'UnusedAnnualLeave',
                    'old_value' => $eObj->UnusedAnnualLeave,
                    'new_value' => $data['unusual_annual_leave']
                ];
            }

            $eObj->CurrentSalary = $data['current_salary'];
            $eObj->High3Average = $data['high3_avg'];
            $eObj->UnusedSickLeave = $data['unusual_sick_leave'] ?? 0;
            $eObj->UnusedAnnualLeave = $data['unusual_annual_leave'] ?? 0;
            $emp_res = $eObj->save();
            if (!$emp_res) {
                return false;
            }

            $row_id = 0;
            $res = $this->updateHistory($eObj->EmployeeId, $history_fields, $row_id, 'pay_and_leave');

            if ($eObj->High3Average == 0) {
                $scenarios = ReportScenario::where('EmployeeId', $empId)->get();
                // dd($scenarios->toArray());
                $high3Average_arr = $this->calcProjectedHigh3Average($empId, 1);
                $high3 = $high3Average_arr['projectedHigh3Avg'];
                foreach ($scenarios as $scenario) {
                    if ($scenario->ScenarioNo > 1) {
                        $high3 = $high3 + ($high3 * $data['annual_increase'] / 100);
                    }
                    ReportScenario::where(['EmployeeId' => $empId, 'ScenarioNo' => $scenario->ScenarioNo])->update(['High3Average' => $high3]);
                }
            }
            $salary_incease = EmployeeConfig::where([
                'EmployeeId' => $empId,
                'ConfigType' => 'SalaryIncreaseDefault'
            ])->first();
            if ($salary_incease) {
                if ($salary_incease->ConfigValue != $data['annual_increase']) {
                    $history_fields['EmployeeConfig'][] = [
                        'column_name' => 'SalaryIncreaseDefault',
                        'old_value' => $salary_incease->ConfigValue,
                        'new_value' => $data['annual_increase']
                    ];
                    $row_id = 0;
                    $res = $this->updateHistory($eObj->EmployeeId, $history_fields, $row_id, 'pay_and_leave');
                }
            }
            $sal_ires = EmployeeConfig::updateORCreate([
                'EmployeeId' => $empId,
                'ConfigType' => 'SalaryIncreaseDefault'
            ], [
                'ConfigValue' => $data['annual_increase'],
            ]);
            if (!$sal_ires) {
                // echo $empId . "not done";
                // die;
                return false;
            } else {
                // echo $empId . "done: " . $data['annual_increase'];
                // die;
                return true;
            }
        }
        return false;
    }

    public function saveDeduction($empId = null, $data = [])
    {
        if (($empId != null) && !empty($data)) {
            $obj = new Deduction();
            $obj->EmployeeId = $empId;
            $obj->DeductionName = $data['deduction_name'];
            $obj->DeductionAmount = $data['amount'];
            $obj->IsOther = 1;
            if ($obj->save()) {
                return true;
            }
        }
        return false;
    }

    public function getDeduction($did = null)
    {
        if ($did != null) {
            return Deduction::where('DeductionId', $did)->first();
        }
        return false;
    }
    public function updateDeduction($deductionId = null, $data = [])
    {
        if (($deductionId != null) && !empty($data)) {
            $obj = Deduction::find($deductionId);
            $obj->DeductionName = $data['deduction_name'];
            $obj->DeductionAmount = $data['amount'];
            if ($obj->save()) {
                return true;
            }
        }
        return false;
    }

    public function saveEmpFile($file = [])
    {
        if (!empty($file)) {
            $obj = new EmployeeFile();
            $obj->EmployeeId = $file['EmployeeId'];
            $obj->StoredFileName = $file['StoredFileName'];
            $obj->OrigFileName = $file['OrigFileName'];
            $obj->ContentType = $file['ContentType'];
            $obj->FileSize = $file['FileSize'];
            if ($obj->save()) {
                return true;
            }
        }
        return false;
    }

    public function checkEmployeeExist($empId = null)
    {
        if ($empId != null) {
            $emp = $this->getById($empId);
            if ($emp) {
                return true;
            }
        }
        return false;
    }

    public function getEmployeeFiles($empId = null)
    {
        if ($empId != null) {
            $files = EmployeeFile::where('EmployeeId', $empId)->get();
            if ($files) {
                return $files->toArray();
            }
        }
        return [];
    }

    public function getEmployeeScenarios($empId = null)
    {
        $result = ReportScenario::where('EmployeeId', $empId)->get();
        if ($result) {
            return $result->toArray();
        }
        return [];
    }

    public function getScanarioByNumber($empId = null, $scanario = 1)
    {
        $result = ReportScenario::where('EmployeeId', $empId)->where('ScenarioNo', $scanario)->first();
        if ($result) {
            return $result->toArray();
        }
        return [];
    }


    function dateDiffInDays($date1, $date2)
    {
        // Calulating the difference in timestamps
        $diff = strtotime($date2) - strtotime($date1);

        // 1 day = 24 hours
        // 24 * 60 * 60 = 86400 seconds
        return abs(round($diff / 86400));
    }



    public function calcDebugReportDates($empId = null, $scenario = 1)
    {
        $report_arr = [];
        $sal_arr = [];
        $finalDates['report_dates'] = [];
        $finalDates['scenarioData'] = [];
        if (($empId != null) && ($scenario != null)) {
            $employee = resolve('employee')->getById($empId)->toArray();
            // echo "<pre>"; print_r($employee); exit;
            // if(!isset($employee['eligibility']['MinRetirementDate'])) {
            //     return [];
            // }
            $currentScenario = $this->getScanarioByNumber($empId, $scenario);
            // dd($currentScenario);
            $data['scenario_no'] = (isset($currentScenario['ScenarioNo']) ? $currentScenario['ScenarioNo'] : "");
            $data['report_date'] = date('m/d/Y', strtotime($employee['ReportDate']));
            $data['retirement_date'] = (isset($currentScenario['RetirementDate']) ? date('m/d/Y', strtotime($currentScenario['RetirementDate'])) : "");
            $data['earliest_eligible_retirement_date'] = date('m/d/Y', strtotime($employee['eligibility']['MinRetirementDate']));
            $data['scd'] = date('m/d/Y', strtotime($employee['eligibility']['LeaveSCD']));
            $data['elibility_scd'] = date('m/d/Y', strtotime($employee['eligibility']['EligibilitySCD']));
            $data['annuity_scd'] = date('m/d/Y', strtotime($employee['eligibility']['AnnuitySCD']));
            $data['dob'] = date('m/d/Y', strtotime($employee['eligibility']['DateOfBirth']));

            $dateNow = new \DateTime();
            $bday = new \DateTime($employee['eligibility']['DateOfBirth']);
            $interval = $dateNow->diff($bday);
            $emp_age = $interval->y;

            $scd = $employee['eligibility']['LeaveSCD'];
            $scdDate = new \DateTime();
            $nowDate = new \DateTime();
            $serviceDuration = $scdDate->diff($nowDate);
            $yearInService = $serviceDuration->y;

            /* ------- Service year code by P ------- */

            $now_Date = new \DateTime($data['scd']);
            $serYrdate = $now_Date->format('d-m-Y');
            $d1 = new \DateTime(date("d-m-Y"));
            $d2 = new \DateTime($serYrdate);

            $diffYear = $d2->diff($d1);

            /* ------- Service year code by P ------- */

            $reportYear = date('Y');

            $retDate = new \DateTime(isset($currentScenario['RetirementDate']) ? $currentScenario['RetirementDate'] : "");
            $retInterval = $retDate->diff($bday);
            $retirementAge = $retInterval->y;
            $reportEmpAge = $emp_age;
            for ($i = $emp_age; $i <= 90; $i++) {
                $report['report_year'] = $reportYear;
                $reportYear++;
                $report['age'] = $reportEmpAge++;
                //$report['years_in_service'] = $yearInService;
                $report['years_in_service'] = $diffYear->y;
                $diffYear->y++;
                $report['is_retired'] = ($i >= $retirementAge) ? 'true' : 'false';
                $report['retirementAge'] = $retirementAge;
                array_push($report_arr, $report);
            }
            $finalDates['report_dates'] = $report_arr;
            $finalDates['scenarioData'] = $data;

            $curretSalYear = date('Y');
            $salReportAge = $emp_age;
            $empConf = $this->getEmployeeConf($empId);
            //echo "<pre>"; print_r($empConf); exit;
            $empAnualIncrease = $empConf['SalaryIncreaseDefault'];

            $SalReportcurrentSal = $employee['CurrentSalary'];
            for ($j = $emp_age; $j < $retirementAge; $j++) {
                $salReport['year'] = $curretSalYear++;
                $salReport['age'] = $salReportAge++;
                $salReport['yearly'] = $SalReportcurrentSal;
                $salReport['monthly'] = ($SalReportcurrentSal / 12);
                array_push($sal_arr, $salReport);
                $SalReportcurrentSal = $SalReportcurrentSal + ($SalReportcurrentSal * ($empAnualIncrease / 100));
            }
            $finalDates['salReport'] = $sal_arr;
        }
        return $finalDates;
    }

    public function calcDebugAnnuityData($empId = null, $scenario = 1)
    {
        $annuity_arr = [];
        if ($empId != null) {
            $employee = $this->getById($empId)->toArray();
            $currentScenario = $this->getScanarioByNumber($empId, $scenario);
            // echo "<pre>";
            // print_r($currentScenario);
            // die;
            $empConf = $this->getEmployeeConf($empId);
            // echo "<pre>"; print_r($empConf); exit;
            $data['leave_scd'] = isset($employee['eligibility']['LeaveSCD']) ? $employee['eligibility']['LeaveSCD'] : "";
            $data['annuity_scd'] = isset($employee['eligibility']['AnnuitySCD']) ? $employee['eligibility']['AnnuitySCD'] : "";
            $data['high3_avg'] = isset($currentScenario['High3Average']) ? $currentScenario['High3Average'] : "";
            $data['annuity_before_deduction'] = isset($currentScenario['AnnuityBeforeDeduction']) ? $currentScenario['AnnuityBeforeDeduction'] : 0.00;
            $data['survivory_annuity'] = isset($currentScenario['SurvivorAnnuity']) ? $currentScenario['SurvivorAnnuity'] : 0.00;
            $data['survivory_annuity_cost'] = isset($currentScenario['SurvivorAnnuityCost']) ? $currentScenario['SurvivorAnnuityCost'] : "";
            $data['part_time_multiplier'] = isset($currentScenario['PartTimeMultiplier']) ? $currentScenario['PartTimeMultiplier'] : 1.00;
            $data['mra10_multiplier'] = isset($currentScenario['MRA10Multiplier']) ? $currentScenario['MRA10Multiplier'] : 1.00;
            $data['total_fers_months_service'] = isset($currentScenario['FERSServiceAtRetirement']) ? $currentScenario['FERSServiceAtRetirement'] : "";
            $data['total_csrs_months_service'] = isset($currentScenario['CSRSServiceAtRetirement']) ? $currentScenario['CSRSServiceAtRetirement'] : "";
            $data['deposit_penalty'] = isset($employee['eligibility']['DepositPenalty']) ? $employee['eligibility']['DepositPenalty'] : "";
            $data['refund_penalty'] = isset($employee['eligibility']['RefundPenalty']) ? $employee['eligibility']['RefundPenalty'] : "";
            $data['first_year_annuity'] = isset($employee['eligibility']['AnnuitySCD']) ? $employee['eligibility']['AnnuitySCD'] : "";
            $data['annuity_cola'] = isset($empConf['FERSCola']) ? $empConf['FERSCola'] : "";
            $data['unused_sick_leave'] = isset($employee['UnusedSickLeave']) ? $employee['UnusedSickLeave'] : "";
            $data['unused_annual_leave'] = isset($employee['UnusedAnnualLeave']) ? $employee['UnusedAnnualLeave'] : "";
            // echo "<pre>"; print_r($empConf); exit;
            $annuity_arr['scenarioData'] = $data;
            $annuity_arr['annuityReport'] = [];
        }
        return $annuity_arr;
    }

    public function CalcAndDebugSS($empId = null)
    {
        $data = [];
        if ($empId != null) {
            $employee = $this->getById($empId)->toArray();
            $empConf = $this->getEmployeeConf($empId);
            // echo "<pre>"; print_r($employee); exit;
            $data['ss_cola'] = $empConf['SSCola'];
            $data['ss_at_62'] = $employee['SSMonthlyAt62'];
            $data['ss_start_age'] = $employee['SSStartAge_year'];
            $data['ss_at_start_age'] = $employee['SSMonthlyAtStartAge'];
            $dateNow = new \DateTime();
            $bday = new \DateTime($employee['eligibility']['DateOfBirth']);
            $interval = $dateNow->diff($bday);
            $data['emp_age'] = $interval->y;
        }
        return $data;
    }

    public function CalcAndDebugFersSuppl($empId = null)
    {
        $data = [];
        if ($empId != null) {
            $employee = $this->getById($empId)->toArray();
            $empConf = $this->getEmployeeConf($empId);

            $dateNow = new \DateTime();
            $bday = new \DateTime($employee['eligibility']['DateOfBirth']);
            $interval = $dateNow->diff($bday);
            $emp_age = $interval->y;

            $retDate = new \DateTime($employee['eligibility']['RetirementDate']);
            $retInterval = $retDate->diff($bday);
            $retirementAge = $retInterval->y;
            // echo $retirementAge; exit;
            $serviceStartDate = new \DateTime($employee['eligibility']['LeaveSCD']);
            $serviceInterval = $serviceStartDate->diff($retDate);
            $serviceDuration = $serviceInterval->y;

            $ss_at_age62 = $employee['SSMonthlyAt62'];

            $monthlySRSBenifits = $ss_at_age62 * ($serviceDuration / 40);

            $data['emp_age'] = $emp_age;
            $data['retirement_age'] = $retirementAge;
            $data['monthlySRS'] = $monthlySRSBenifits;
        }
        // echo "<pre>"; print_r($data); exit;
        return $data;
    }

    public function CalcAndDebugAllIncomeAnnual($empId = null)
    {
        $result = [];
        if ($empId != null) {
            $employee = $this->getById($empId)->toArray();
            $empConf = $this->getEmployeeConf($empId);

            $dateNow = new \DateTime();
            $bday = new \DateTime($employee['eligibility']['DateOfBirth']);
            $interval = $dateNow->diff($bday);
            $emp_age = $interval->y;
            $emp_age_month = $interval->m;

            $retDate = new \DateTime($employee['eligibility']['RetirementDate']);
            $retInterval = $retDate->diff($bday);
            $retirementAge = $retInterval->y;
            $retirement_month = $retInterval->m;

            $year = date('Y');
            $empAnnualIncrease = $empConf['SalaryIncrease'];
            $SalReportcurrentSal = $employee['CurrentSalary'];

            $serviceStartDate = new \DateTime($employee['eligibility']['LeaveSCD']);
            $serviceInterval = $serviceStartDate->diff($retDate);
            $serviceDuration = $serviceInterval->y;

            $ss_at_age62 = $employee['SSMonthlyAt62'];

            $monthlySRSBenifits = $ss_at_age62 * ($serviceDuration / 40);

            $ss_start_age = $employee['SSStartAge_year'];
            $ss_at_start_age = $employee['SSMonthlyAtStartAge'];
            $ss_cola = $empConf['SSCola'];
            $count = 0;
            for ($i = $emp_age; $i <= 90; $i++) {
                $data['year'] = $year++;
                $data['age'] = $i;
                $data['salary'] = $SalReportcurrentSal;

                $SalReportcurrentSal = $SalReportcurrentSal + ($SalReportcurrentSal * ($empAnnualIncrease / 100));
                if ($i == ($retirementAge - 1)) {
                    $monthlySal = $SalReportcurrentSal / 12;
                    $SalReportcurrentSal = $monthlySal * ($retirement_month);
                } elseif ($i >= $retirementAge) {
                    $SalReportcurrentSal = 0;
                }

                $data['annuity'] = 0;
                $data['supplement'] = (($i > $retirementAge) && ($i < 62)) ? ($monthlySRSBenifits * 12) : 0;

                $monthly_ss = 0;
                if ($i == $ss_start_age) {
                    $monthly_ss = $ss_at_start_age;
                } elseif (($i == $emp_age) && ($i > $ss_start_age)) {
                    $monthly_ss = $ss_at_start_age;
                    for ($k = $emp_age + 1; $k <= $i; $k++) {
                        $monthly_ss = ($monthly_ss * ($ss_cola / 100)) + $monthly_ss;
                    }
                } elseif ($i > $ss_start_age) {
                    $monthly_ss = ($monthly_ss * ($ss_cola / 100)) + $monthly_ss;
                } else {
                    $monthly_ss = 0;
                }
                $data['ss'] = $monthly_ss * 12;

                $total = $data['ss'] + $data['supplement'] + $data['annuity'] + $data['salary'];
                $data['total'] = $total;
                if ($i == $emp_age) {
                    $data['change'] = 0;
                } elseif ($i > $emp_age) {
                    $data['change'] = $total - $result[$count - 1]['total'];
                }
                array_push($result, $data);
                $count++;
            }
        }
        // echo "<pre>"; print_r($result); exit;
        return $result;
    }

    public function calcAndDebugTspSalIncrease($empId = null)
    {
        $tsp_arr = [];
        if ($empId != null) {
            $employee = $this->getById($empId)->toArray();
            $empConf = $this->getEmployeeConf($empId);
            $tsp_conf = $this->getEmpTspConfigurations($empId);
            $tsp = $this->getEmpTspDetails($empId);
            // echo "<pre>";
            // print_r($tsp);
            // exit;

            $dateNow = new \DateTime();
            $bday = new \DateTime($employee['eligibility']['DateOfBirth']);
            $interval = $dateNow->diff($bday);
            $emp_age = $interval->y;

            $retDate = new \DateTime($employee['eligibility']['RetirementDate']);
            $retInterval = $retDate->diff($bday);
            $retirementAge = $retInterval->y;
            $retirement_month = date('m', strtotime($employee['eligibility']['RetirementDate']));
            // echo $retirement_month; exit;
            $serviceStartDate = new \DateTime($employee['eligibility']['LeaveSCD']);
            $serviceInterval = $serviceStartDate->diff($retDate);
            $serviceDuration = $serviceInterval->y;

            $currentYear = date('Y');

            $data['starting_balance'] = isset($tsp['totalCurrentBalance']) ? $tsp['totalCurrentBalance'] : "";
            $data['yearlyContribution'] = isset($tsp['ContributionRegular']) ? ($tsp['ContributionRegular'] + $tsp['ContributionCatchUp']) * 26 : "";
            $data['loanRepayment'] = isset($tsp['LoanRepayment']) ? $tsp['LoanRepayment'] : "";
            $data['payoff_date'] = isset($tsp['PayoffDate']) ? $tsp['PayoffDate'] : "";

            $gfund      = isset($tsp['GFund']) ? $tsp['GFund'] : 0;
            $ffund      = isset($tsp['FFund']) ? $tsp['FFund'] : 0;
            $cfund      = isset($tsp['CFund']) ? $tsp['CFund'] : 0;
            $sfund      = isset($tsp['SFund']) ? $tsp['SFund'] : 0;
            $ifund      = isset($tsp['IFund']) ? $tsp['IFund'] : 0;
            $lIncome    = isset($tsp['LIncome']) ? $tsp['LIncome'] : 0;
            $l2025      = isset($tsp['L2025']) ? $tsp['L2025'] : 0;
            $l2030      = isset($tsp['L2030']) ? $tsp['L2030'] : 0;
            $l2035      = isset($tsp['L2035']) ? $tsp['L2035'] : 0;
            $l2040      = isset($tsp['L2040']) ? $tsp['L2040'] : 0;
            $l2045      = isset($tsp['L2045']) ? $tsp['L2045'] : 0;
            $l2050      = isset($tsp['L2050']) ? $tsp['L2050'] : 0;
            $l2055      = isset($tsp['L2055']) ? $tsp['L2055'] : 0;
            $l2060      = isset($tsp['L2060']) ? $tsp['L2060'] : 0;
            $l2065      = isset($tsp['L2065']) ? $tsp['L2065'] : 0;

            $balance = $gfund + $ffund + $cfund + $sfund + $ifund + $lIncome + $l2025 + $l2030 + $l2035 + $l2040 + $l2045 + $l2050 + $l2055 + $l2060 + $l2065;
            $salary = isset($tsp['current_salary']) ? $tsp['current_salary'] : 0;
            $percentageYear = isset($tsp['GFundDist']) ? $tsp['GFundDist'] : (isset($tsp['FFundDist']) ? $tsp['FFundDist']  : (isset($tsp['CFundDist']) ? $tsp['CFundDist']  : (isset($tsp['SFundDist']) ? $tsp['SFundDist']  : (isset($tsp['IFundDist']) ? $tsp['IFundDist'] : 0))));
            $empRegularContri = isset($tsp['ContributionRegular']) ? $tsp['ContributionRegular'] * 26  : 0;
            $regularContriPercentage = isset($tsp['regularContriPercentage']) ? $tsp['regularContriPercentage'] : 0;
            $catchUpContri = isset($tsp['ContributionCatchUp']) ? $tsp['ContributionCatchUp'] * 26 : 0;
            $onePercentSal = isset($tsp['current_salary']) ? ($tsp['current_salary'] / 100) : 0;
            $matchPercent = 0;
            $loan_rePay_gen = isset($tsp['loan_repayment_general']) ? $tsp['loan_repayment_general'] : 0;
            $loan_rePay_res = isset($tsp['loan_repayment_residential']) ? $tsp['loan_repayment_residential'] : 0;
            $rePay = $loan_rePay_gen + $loan_rePay_res;

            $employee_Contri = $empRegularContri + $catchUpContri + $rePay;

            $payOffYear = isset($tsp['PayoffDate']) ? date('Y', strtotime($tsp['PayoffDate'])) : "";
            $payOffMonth = isset($tsp['PayoffDate']) ? date('m', strtotime($tsp['PayoffDate'])) : "";


            $agency_contibution_arr = $this->getAgencyContribution($tsp);
            $agency_percentage = $agency_contibution_arr['agency_percentage'];
            $agency_contribution = $agency_contibution_arr['agency_contribution'];
            $employee_contribution = $agency_contibution_arr['employee_contribution'];


            $total = $empRegularContri + $catchUpContri + $onePercentSal + $agency_contribution + $rePay;

            for ($i = $emp_age; $i <= $retirementAge; $i++) {
                if ($i > $emp_age) {
                    $gfund = $tsp['GFund'] + ($tsp['GFund'] * ($tsp_conf['gfund'] / 100));
                    $ffund = $tsp['FFund'] + ($tsp['FFund'] * ($tsp_conf['ffund'] / 100));
                    $cfund = $tsp['CFund'] + ($tsp['CFund'] * ($tsp_conf['cfund'] / 100));
                    $sfund = $tsp['SFund'] + ($tsp['SFund'] * ($tsp_conf['sfund'] / 100));
                    $ifund = $tsp['IFund'] + ($tsp['IFund'] * ($tsp_conf['ifund'] / 100));
                    $balance = $gfund + $ffund + $cfund + $sfund + $ifund + $tsp['LIncome'] + $tsp['L2025'] + $tsp['L2030'] + $tsp['L2035'] + $tsp['L2040'] + $tsp['L2045'] + $tsp['L2050'] + $tsp['L2055'] + $tsp['L2060'] + $tsp['L2065'];
                    $salary = $salary + ($salary * ($empConf['SalaryIncrease'] / 100));
                    $empRegularContri = $empRegularContri + ($empRegularContri * ($empConf['SalaryIncrease'] / 100));
                    $regularContriPercentage = ($empRegularContri / $salary) * 100;
                    $catchUpContri = $catchUpContri + ($catchUpContri * ($empConf['SalaryIncrease'] / 100));
                    $onePercentSal = ($salary / 100);
                }

                if ($i == $retirementAge) {
                    $percentageYear = ($retirement_month / 12) * 100;
                    $salary = $salary * ($percentageYear / 100);
                    $onePercentSal = ($salary / 100);
                }
                if ($currentYear > $payOffYear) {
                    $rePay = 0;
                }
                $total = $empRegularContri + $catchUpContri + $onePercentSal + $matchPercent + $rePay;
                $tsp_sal_inc['year'] = $currentYear++;
                $tsp_sal_inc['cont'] = 0;
                $tsp_sal_inc['gfund'] = $gfund;
                $tsp_sal_inc['ffund'] = $ffund;
                $tsp_sal_inc['cfund'] = $cfund;
                $tsp_sal_inc['sfund'] = $sfund;
                $tsp_sal_inc['ifund'] = $ifund;
                $tsp_sal_inc['lIncome'] = $lIncome;
                $tsp_sal_inc['l2025'] = $l2025;
                $tsp_sal_inc['l2030'] = $l2030;
                $tsp_sal_inc['l2035'] = $l2035;
                $tsp_sal_inc['l2040'] = $l2040;
                $tsp_sal_inc['l2045'] = $l2045;
                $tsp_sal_inc['l2050'] = $l2050;
                $tsp_sal_inc['l2055'] = $l2055;
                $tsp_sal_inc['l2060'] = $l2060;
                $tsp_sal_inc['l2065'] = $l2065;

                $tsp_sal_inc['balance'] = $balance;
                $tsp_sal_inc['salary'] = $salary;
                $tsp_sal_inc['percentageOfYear'] = $percentageYear;
                $tsp_sal_inc['empRegularContri'] = $empRegularContri;
                $tsp_sal_inc['regularContriPercentage'] = $regularContriPercentage;
                $tsp_sal_inc['catchUpContri'] = $catchUpContri;
                $tsp_sal_inc['onePercentSal'] = $onePercentSal;
                $tsp_sal_inc['matchPercent'] = $matchPercent;
                $tsp_sal_inc['repay'] = $rePay;
                $tsp_sal_inc['total'] = $total;

                array_push($tsp_arr, $tsp_sal_inc);
            }
        }
        $data['tsp_arr'] = $tsp_arr;
        // echo "<pre>"; print_r($data); exit;
        return $data;
    }

    public function getAgencyContribution($tsp)
    {
        $empRegularContri = isset($tsp['ContributionRegular']) ? $tsp['ContributionRegular'] * 26  : 0;

        $catchUpContri = isset($tsp['ContributionCatchUp']) ? $tsp['ContributionCatchUp'] * 26 : 0;
        $agency_contri_1 = isset($tsp['current_salary']) ? ($tsp['current_salary'] / 100) : 0;

        $employee_Contri = $empRegularContri + $catchUpContri;
        // calculate percentage of salary in employee contri
        $sal_percentage_in_contri = ($employee_Contri / $tsp['current_salary']) * 100;

        if (round($sal_percentage_in_contri) == 0) {
            $agency_percentage = 0;
        } elseif (round($sal_percentage_in_contri) == 1) {
            $agency_percentage = 1;
        } elseif (round($sal_percentage_in_contri) == 2) {
            $agency_percentage = 2;
        } elseif (round($sal_percentage_in_contri) == 3) {
            $agency_percentage = 3;
        } elseif (round($sal_percentage_in_contri) == 4) {
            $agency_percentage = 3.5;
        } elseif (round($sal_percentage_in_contri) >= 5) {
            $agency_percentage = 4;
        }
        $agency_contribution = ($agency_percentage / 100) * $tsp['current_salary'];
        $max_agency_contribution = (5 / 100) * $tsp['current_salary'];
        $result = [
            'agency_percentage' => $agency_percentage,
            'agency_contribution' => $agency_contribution,
            'employee_contribution' => $employee_Contri,
            'sal_percentage_in_contri' => $sal_percentage_in_contri,
            'max_agency_contribution' => $max_agency_contribution,
            'agency_contribution_1' => $agency_contri_1
        ];
        return $result;
    }

    public function pre_retirement($empId = null, $param)
    {
        $fegli_arr = array();
        if ($empId != null) {
            $data = "";
            $employee = $this->getById($empId)->toArray();
            $fegli = resolve('employee')->getFegliByEmpId($empId);

            $dateNow = new \DateTime();
            $bday = new \DateTime($employee['eligibility']['DateOfBirth']);
            $interval = $dateNow->diff($bday);
            $emp_age = $interval->y;

            $retDate = new \DateTime($employee['eligibility']['RetirementDate']);
            $retInterval = $retDate->diff($bday);
            $retirementAge = $retInterval->y;

            if ($fegli) {
                $data = $fegli->toArray();
            }
            $data['employee']['retirementType'] = $employee['retirementType'];
            $setData = $this->getFegliReport($data);

            foreach ($setData['fegli_arr'] as $value) {
                if ($value['age'] <= $retirementAge && $param == "pre") {
                    $fegli_arr[] = $value;
                } elseif ($value['age'] >= $retirementAge && $param == "post") {
                    $fegli_arr[] = $value;
                }
            }
        }
        return $fegli_arr;
    }

    public function calcAndDebugTspNoSalIncrease($empId = null)
    {
        $tsp_arr = [];
        if ($empId != null) {
            $employee = $this->getById($empId)->toArray();
            $empConf = $this->getEmployeeConf($empId);
            $tsp_conf = $this->getEmpTspConfigurations($empId);
            $tsp = $this->getEmpTspDetails($empId);

            if (count($tsp) > 0) {
                /* echo "<pre>"; print_r($tsp); exit; */

                $dateNow = new \DateTime();
                $bday = new \DateTime($employee['eligibility']['DateOfBirth']);
                $interval = $dateNow->diff($bday);
                $emp_age = $interval->y;

                $retDate = new \DateTime($employee['eligibility']['RetirementDate']);
                $retInterval = $retDate->diff($bday);
                $retirementAge = $retInterval->y;
                $retirement_month = date('m', strtotime($employee['eligibility']['RetirementDate']));
                //echo $retirement_month; exit;
                $serviceStartDate = new \DateTime($employee['eligibility']['LeaveSCD']);
                $serviceInterval = $serviceStartDate->diff($retDate);
                $serviceDuration = $serviceInterval->y;

                $currentYear = date('Y');

                $data['starting_balance'] = $tsp['totalCurrentBalance'];
                $data['yearlyContribution'] = ($tsp['ContributionRegular'] + $tsp['ContributionCatchUp']) * 26;
                $data['loanRepayment'] = $tsp['LoanRepayment'];
                $data['payoff_date'] = $tsp['PayoffDate'];

                $gfund = $tsp['GFund'];
                $ffund = $tsp['FFund'];
                $cfund = $tsp['CFund'];
                $sfund = $tsp['SFund'];
                $ifund = $tsp['IFund'];
                $lIncome = $tsp['LIncome'];
                $l2025 = $tsp['L2025'];
                $l2030 = $tsp['L2030'];
                $l2035 = $tsp['L2035'];
                $l2040 = $tsp['L2040'];
                $l2045 = $tsp['L2045'];
                $l2050 = $tsp['L2050'];
                $l2055 = $tsp['L2055'];
                $l2060 = $tsp['L2060'];
                $l2065 = $tsp['L2065'];
                $balance = $tsp['GFund'] + $tsp['FFund'] + $tsp['CFund'] + $tsp['SFund'] + $tsp['IFund'] + $tsp['LIncome'] + $tsp['L2025'] + $tsp['L2030'] + $tsp['L2035'] + $tsp['L2040'] + $tsp['L2045'] + $tsp['L2050'] + $tsp['L2055'] + $tsp['L2060'] + $tsp['L2065'];
                $salary = $tsp['current_salary'];
                $percentageYear = $tsp['GFundDist'] + $tsp['FFundDist'] + $tsp['CFundDist'] + $tsp['SFundDist'] + $tsp['IFundDist'];
                $empRegularContri = $tsp['ContributionRegular'] * 26;
                $regularContriPercentage = $tsp['regularContriPercentage'];
                $catchUpContri = $tsp['ContributionCatchUp'] * 26;
                $onePercentSal = ($tsp['current_salary'] / 100);
                $matchPercent = 0;
                $rePay = $tsp['LoanRepayment'];
                $payOffYear = date('Y', strtotime($tsp['PayoffDate']));
                $payOffMonth = date('m', strtotime($tsp['PayoffDate']));
                $total = $empRegularContri + $catchUpContri + $onePercentSal + $matchPercent + $rePay;

                for ($i = $emp_age; $i <= $retirementAge; $i++) {
                    if ($i > $emp_age) {
                        $gfund = $tsp['GFund'] + ($tsp['GFund'] * ($tsp_conf['gfund'] / 100));
                        $ffund = $tsp['FFund'] + ($tsp['FFund'] * ($tsp_conf['ffund'] / 100));
                        $cfund = $tsp['CFund'] + ($tsp['CFund'] * ($tsp_conf['cfund'] / 100));
                        $sfund = $tsp['SFund'] + ($tsp['SFund'] * ($tsp_conf['sfund'] / 100));
                        $ifund = $tsp['IFund'] + ($tsp['IFund'] * ($tsp_conf['ifund'] / 100));
                        $balance = $gfund + $ffund + $cfund + $sfund + $ifund + $tsp['LIncome'] + $tsp['L2025'] + $tsp['L2030'] + $tsp['L2035'] + $tsp['L2040'] + $tsp['L2045'] + $tsp['L2050'] + $tsp['L2055'] + $tsp['L2060'] + $tsp['L2065'];
                        $salary = $salary;
                        $empRegularContri = $empRegularContri;
                        $regularContriPercentage = ($empRegularContri / $salary) * 100;
                        $catchUpContri = $catchUpContri + ($catchUpContri * ($empConf['SalaryIncrease'] / 100));
                        $onePercentSal = ($salary / 100);
                    }
                    if ($i == $retirementAge) {
                        $percentageYear = ($retirement_month / 12) * 100;
                        $salary = $salary * ($percentageYear / 100);
                        $onePercentSal = ($salary / 100);
                    }
                    if ($currentYear > $payOffYear) {
                        $rePay = 0;
                    }
                    $total = $empRegularContri + $catchUpContri + $onePercentSal + $matchPercent + $rePay;
                    $tsp_sal_inc['year'] = $currentYear++;
                    $tsp_sal_inc['cont'] = 0;
                    $tsp_sal_inc['gfund'] = $gfund;
                    $tsp_sal_inc['ffund'] = $ffund;
                    $tsp_sal_inc['cfund'] = $cfund;
                    $tsp_sal_inc['sfund'] = $sfund;
                    $tsp_sal_inc['ifund'] = $ifund;
                    $tsp_sal_inc['lIncome'] = $lIncome;
                    $tsp_sal_inc['l2025'] = $tsp['L2025'];
                    $tsp_sal_inc['l2030'] = $tsp['L2030'];
                    $tsp_sal_inc['l2035'] = $tsp['L2035'];
                    $tsp_sal_inc['l2040'] = $tsp['L2040'];
                    $tsp_sal_inc['l2045'] = $tsp['L2045'];
                    $tsp_sal_inc['l2050'] = $tsp['L2050'];
                    $tsp_sal_inc['l2055'] = $tsp['L2055'];
                    $tsp_sal_inc['l2060'] = $tsp['L2060'];
                    $tsp_sal_inc['l2065'] = $tsp['L2065'];
                    $tsp_sal_inc['balance'] = $balance;
                    $tsp_sal_inc['salary'] = $salary;
                    $tsp_sal_inc['percentageOfYear'] = $percentageYear;
                    $tsp_sal_inc['empRegularContri'] = $empRegularContri;
                    $tsp_sal_inc['regularContriPercentage'] = $regularContriPercentage;
                    $tsp_sal_inc['catchUpContri'] = $catchUpContri;
                    $tsp_sal_inc['onePercentSal'] = $onePercentSal;
                    $tsp_sal_inc['matchPercent'] = $matchPercent;
                    $tsp_sal_inc['repay'] = $rePay;
                    $tsp_sal_inc['total'] = $total;

                    array_push($tsp_arr, $tsp_sal_inc);
                }
            }
        }
        $data['tsp_arr'] = $tsp_arr;
        //echo "<pre>"; print_r($data); exit;
        return $data;
    }

    public function calcProjectedHigh3Average($empId = null, $scenario = 1)
    {
        $data = [];
        if ($empId != null) {
            $employee = $this->getById($empId);
            if (is_null($employee)) {
                return [];
            }
            $employee = $employee->toArray();
            $empConf = $this->getEmployeeConf($empId);
            // echo "<pre>"; print_r($empConf); exit;

            if ($empConf['SalaryIncreaseDefault'] == "") {
                $empConf['SalaryIncreaseDefault'] = 1.41;
            }
            if ($empConf['SalaryIncrease1'] == "") {
                $empConf['SalaryIncrease1'] = 1;
            }
            if ($empConf['SalaryIncrease2'] == "") {
                $empConf['SalaryIncrease2'] = 2.6;
            }

            $current_sal = $employee['CurrentSalary'];

            $reportYear = date('Y', strtotime($employee['ReportDate']));

            if ($reportYear < date('Y')) {
                $current_sal = $this->getCurrentSalary($employee, $current_sal);
            }


            $bday = new \DateTime($employee['eligibility']['DateOfBirth']);
            $retDate = new \DateTime($employee['eligibility']['RetirementDate']);
            $today = new \DateTime();
            $retInterval = $retDate->diff($bday);
            $retAgeY = $retInterval->y;
            $retAgeM = $retInterval->m;

            $empAge = $bday->diff($today);
            $emp_age = $empAge->y;
            $retirement_year = date('Y', strtotime($employee['eligibility']['RetirementDate']));
            $retirement_month = date('m', strtotime($employee['eligibility']['RetirementDate']));
            $retirement_date = date('d', strtotime($employee['eligibility']['RetirementDate']));
            // $joining_month = date('m', strtotime($employee['eligibility']['LeaveSCD']));
            $current_year = date('Y');

            // echo $salIncrease; die;
            // if ($scenario > 1) {
            //     $lyear = $retirement_year = $retirement_year + ($scenario - 1);
            //     for ($z = 2; $z <= $scenario; $z++) {
            //         if ($lyear > date('Y')) {
            //             $salIncrease = $empConf['SalaryIncreaseDefault'];
            //         } elseif ($lyear == date('Y')) {
            //             $salIncrease = $empConf['SalaryIncrease'];
            //         } elseif ($lyear == (date('Y') - 1)) {
            //             $salIncrease = $empConf['SalaryIncrease1'];
            //         } else {
            //             $salIncrease = $empConf['SalaryIncrease2'];
            //         }
            //         $current_sal = $current_sal + ($current_sal * ($salIncrease / 100));
            //         $lyear++;
            //     }
            // }

            if ($retirement_year < $current_year) {
                // formula to calculate previous year calculation
                $data['lastSalary'] = $current_sal;

                $incSalaryArr[$current_year] = (int) $sal1 = (100 * $current_sal) / (100 + $empConf['SalaryIncrease']);
                $incSalaryArr[$current_year - 1] = (int) $sal2 = (100 * $sal1) / (100 + $empConf['SalaryIncrease1']);
                $incSalaryArr[$current_year - 2] = (int) $sal3 = (100 * $sal2) / (100 + $empConf['SalaryIncrease2']);
                if ($retirement_month < 12) {
                    $ret_diff = 12 - $retirement_month;
                    $working_months = 12 - $ret_diff;
                    $work_percentage_sal1 = ($working_months / 12) * 100;
                    $sal1 = $sal1 * ($work_percentage_sal1 / 100);
                    $sal4 = (100 * $sal3) / (100 + $empConf['SalaryIncrease2']);
                    $sal4 = $sal4 * (100 - $work_percentage_sal1) / 100;
                } else {
                    $sal4 = 0;
                }
                // if ret mnth is <= 9 count 2018
                $data['projectedHigh3Avg'] = ($sal1 + $sal2 + $sal3 + $sal4) / 3;

                $data['projectedIncreases'] = $incSalaryArr;
            } elseif ($retirement_year === $current_year) {
                // echo "<pre>"; print_r($empConf); die;
                $incSalaryArr[$current_year] = (int) $sal1 = $current_sal;
                $incSalaryArr[$current_year - 1] = (int) $sal2 = (100 * $sal1) / (100 + $empConf['SalaryIncrease']);
                $incSalaryArr[$current_year - 2] = (int) $sal3 = (100 * $sal2) / (100 + $empConf['SalaryIncrease1']);
                if ($retirement_month < 12) {
                    $ret_diff = 12 - $retirement_month;
                    $working_months = 12 - $ret_diff;
                    $work_percentage_sal1 = ($working_months / 12) * 100;
                    // echo $work_percentage_sal1; die;
                    $sal1 = $sal1 * ($work_percentage_sal1 / 100);
                    $sal4 = (100 * $sal3) / (100 + $empConf['SalaryIncrease2']);
                    // echo $sal4; die;
                    $sal4 = $sal4 * (100 - $work_percentage_sal1) / 100;
                } else {
                    $sal4 = 0;
                }
                // if ret mnth is <= 9 count 2018
                $data['projectedHigh3Avg'] = ($sal1 + $sal2 + $sal3 + $sal4) / 3;
                $data['lastSalary'] = $current_sal;
                // echo $data['projectedHigh3Avg'];
                // die;
                $data['projectedIncreases'] = $incSalaryArr;
            } else {
                if ($retirement_year == ($current_year + 1)) {

                    if (($retirement_month == 1) && ($retirement_date < 14)) {
                        // no increase in salary
                        $incSalaryArr[$current_year + 1] = $data['lastSalary'] = (int) $sal1 = $current_sal;
                        $incSalaryArr[$current_year] = (int) $sal2 = (100 * $current_sal) / (100 + $empConf['SalaryIncrease']);
                        $incSalaryArr[$current_year - 1] = (int) $sal3 = (100 * $sal2) / (100 + $empConf['SalaryIncrease']);
                    } else {
                        // increase salary
                        $incSalaryArr[$current_year + 1] = $data['lastSalary'] = (int) $sal1 = round($current_sal + ($current_sal * $empConf['SalaryIncreaseDefault'] / 100));
                        $incSalaryArr[$current_year] = (int) $sal2 = $current_sal;
                        $incSalaryArr[$current_year - 1] = (int) $sal3 = (100 * $sal2) / (100 + $empConf['SalaryIncrease']);
                    }

                    if ($retirement_month < 12) {
                        $ret_diff = 12 - $retirement_month;
                        $working_months = 12 - $ret_diff;
                        $work_percentage_sal1 = ($working_months / 12) * 100;
                        $sal1 = $sal1 * ($work_percentage_sal1 / 100);
                        $sal4 = (100 * $sal3) / (100 + $empConf['SalaryIncrease1']);
                        $sal4 = $sal4 * (100 - $work_percentage_sal1) / 100;
                    } else {
                        $sal4 = 0;
                    }
                    $data['projectedHigh3Avg'] = ($sal1 + $sal2 + $sal3 + $sal4) / 3;

                    $data['projectedIncreases'] = $incSalaryArr;
                } elseif ($retirement_year == (date('Y') + 2)) {
                    $incSalaryArr[date('Y')] = (int) $sal3 = $current_sal;
                    $incSalaryArr[date('Y') + 1] = (int) $sal2 = $current_sal + ($current_sal * $empConf['SalaryIncreaseDefault'] / 100);
                    $incSalaryArr[date('Y') + 2] = $data['lastSalary'] = (int) $sal1 = round($sal2 + ($sal2 * $empConf['SalaryIncreaseDefault'] / 100));

                    if ($retirement_month < 12) {
                        $ret_diff = 12 - $retirement_month;
                        $working_months = 12 - $ret_diff;
                        $work_percentage_sal1 = ($working_months / 12) * 100;
                        $sal1 = $sal1 * ($work_percentage_sal1 / 100);
                        $sal4 = (100 * $sal3) / (100 + $empConf['SalaryIncrease']);
                        $sal4 = $sal4 * (100 - $work_percentage_sal1) / 100;
                    } else {
                        $sal4 = 0;
                    }
                    $data['projectedHigh3Avg'] = ($sal1 + $sal2 + $sal3 + $sal4) / 3;

                    $data['projectedIncreases'] = $incSalaryArr;
                } else {
                    $sal4 = $current_sal;
                    $now = $current_year;
                    // retirement_year
                    // echo $current_year;
                    // die;
                    for ($i = $current_year + 1; $i <= ($retirement_year - 3); $i++) {
                        $sal4 = $sal4 + ($sal4 * $empConf['SalaryIncreaseDefault'] / 100);
                    }

                    $incSalaryArr[$retirement_year - 2] = (int) $sal3 = $sal4 + ($sal4 * $empConf['SalaryIncreaseDefault'] / 100);
                    $incSalaryArr[$retirement_year - 1] = (int) $sal2 = $sal3 + ($sal3 * $empConf['SalaryIncreaseDefault'] / 100);
                    $incSalaryArr[$retirement_year] = (int) $sal1 = round($sal2 + ($sal2 * $empConf['SalaryIncreaseDefault'] / 100));
                    $data['lastSalary'] = $sal1;

                    if ($retirement_month < 12) {
                        $ret_diff = 12 - $retirement_month;
                        $working_months = 12 - $ret_diff;
                        $work_percentage_sal1 = ($working_months / 12) * 100;
                        $sal1 = $sal1 * ($work_percentage_sal1 / 100);
                        $sal4 = $sal4 * (100 - $work_percentage_sal1) / 100;
                    } else {
                        $sal4 = 0;
                    }
                    // echo (int) $sal1 . " -- " . (int) $sal2 . " -- " . (int) $sal3 . " -- " . (int) $sal4;
                    // die;
                    $data['projectedHigh3Avg'] = ($sal1 + $sal2 + $sal3 + $sal4) / 3;

                    $data['projectedIncreases'] = $incSalaryArr;
                }

                // sort incremented array
                ksort($incSalaryArr);
            }
            // Annual leave payout calculation is this:
            // Final salary / 2,087 hours  X  # of hours of Annual Leave
            // $140,000 salary  /  2,087 hours  X  230 hours of annual leave  =  $15,429 payout
            $data['annual_leaves_payout'] = ($data['lastSalary'] / 2087) * $employee['UnusedAnnualLeave'];
        }
        // dd($incSalaryArr);
        if ($scenario > 1) {
            $high3Avg = $data['projectedHigh3Avg'] ?? 0;
            if ($scenario > 1) {
                for ($sc = 2; $sc <= $scenario; $sc++) {
                    $high3Avg = $high3Avg + ($high3Avg * $empConf['SalaryIncreaseDefault'] / 100);
                }
            }
            $data['projectedHigh3Avg'] = $high3Avg;
        }

        if (isset($employee['High3Average']) && ($employee['High3Average'] > 0)) {
            $high3Avg = $employee['High3Average'];
            if ($scenario > 1) {
                for ($sc = 2; $sc <= $scenario; $sc++) {
                    $high3Avg = $high3Avg + ($high3Avg * $empConf['SalaryIncreaseDefault'] / 100);
                }
            }
            $data['projectedHigh3Avg'] = $high3Avg;
        }
        return $data;
    }

    public function addFegliDependent($data = [])
    {
        if (!empty($data)) {
            $FEGLIDependentObj = new FEGLIDependent();
            $FEGLIDependentObj->EmployeeId = $data['empId'];
            if ($data['dob'] != NULL) {
                $FEGLIDependentObj->DateOfBirth = $data['dob'];
            }
            if ($data['age'] != NULL) {
                $FEGLIDependentObj->age = $data['age'];
            }
            $FEGLIDependentObj->CoverAfter22 = (isset($data['cover_after_22']) ? $data['cover_after_22'] : 0);

            if ($FEGLIDependentObj->save()) {
                return $FEGLIDependentObj->FEGLIDependentId;
            } else {
                return false;
            }
        }
        return false;
    }

    public function getDependentInfo($id)
    {
        $res = FEGLIDependent::find($id);
        if ($res) {
            $dependent = $res->toArray();
        } else {
            $dependent = [];
        }
        return $dependent;
    }

    public function updateFegliDependent($id, $data)
    {
        if (!empty($data)) {
            $FEGLIDependentObj = FEGLIDependent::find($id);
            if ($data['dob'] != NULL) {
                $FEGLIDependentObj->DateOfBirth = $data['dob'];
            }
            if ($data['age'] != NULL) {
                $FEGLIDependentObj->age = $data['age'];
            }
            $FEGLIDependentObj->CoverAfter22 = (isset($data['cover_after_22']) ? $data['cover_after_22'] : 0);

            if ($FEGLIDependentObj->save()) {
                return $FEGLIDependentObj->FEGLIDependentId;
            } else {
                return false;
            }
        }
        return false;
    }

    public function deleteDependent($id)
    {
        $res = FEGLIDependent::where('FEGLIDependentId', $id)->delete();
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /* ** Eligibility function below return **
    * Earliest Eligibility date
    * Vaid Service time for eligibility
    * Vaid Service time for retirement
    * Invalid Service time for eligibility
    * Invalid Service Duration for retirement
    * Individual valid and Invalid service durations for Other Services
    *
    *
    *
    ** Military Service to be added or not with different conditions
    * Military Deposit MADE:
    * time counts for eligibility to retire
    * time counts in the pension calculation
    * time does NOT count in the SRS calculation
    * Military Deposit NOT MADE:
    * time does NOT count for eligibility to retire
    * time does NOT count in the pension calculation
    * time does NOT count in the SRS calculation
    */
    public function getEarliestRetirement($empId = null, $scenario = 1)
    {
        $emp = $this->getById($empId)->toArray();
        $empConf = $this->getEmployeeConf($empId);
        // echo "<pre>"; print_r($emp); exit;
        $dateNow = new \DateTime(date('Y-m-d H:i:s'));
        $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
        $bday->modify("- 1 day");
        $interval = $dateNow->diff($bday);
        $ageMPerc = ($interval->m / 12);
        $ageDPerc = ($interval->d / 365);
        $emp_age = $interval->y; // + $ageMPerc + $ageDPerc;
        // echo $emp_age; exit;
        $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
        $retInterval = $retDate->diff($bday);
        $rAgeMonthToYear = ($retInterval->m / 12);
        $rAgeDaysToYear = ($retInterval->d / 365);
        $retirementAge = $retInterval->y + ($rAgeMonthToYear + $rAgeDaysToYear);

        $serviceStartDate = new \DateTime($emp['eligibility']['LeaveSCD']);
        $currentServiceInterval = $serviceStartDate->diff($dateNow); // for earliest retirementDate
        $currentServiceYears = $currentServiceInterval->y;
        $currentServicemonths = ($currentServiceInterval->m / 12);
        $currentServicedays = ($currentServiceInterval->d / 365);

        $currentServiceDuration = $currentServiceYears + $currentServicemonths; // + $currentServicedays;
        /* ********** */
        $totalServiceInterval = $serviceStartDate->diff($retDate); // for pension
        $totalServiceYears = $totalServiceInterval->y;
        $totalServicemonths = ($totalServiceInterval->m / 12);
        $totalServicedays = ($totalServiceInterval->d / 365);

        // echo $totalServiceInterval->y . " Y " . $totalServiceInterval->m . " M";
        // die;
        $totalServiceDuration = $totalServiceYears + $totalServicemonths + $totalServicedays;
        // echo $totalServiceDuration;
        // die;
        /* Total services percentage with part time services starts */
        // Q1: How many hours should this employee have worked had they been full-time for their entire career?
        // 1. years in part time service

        $fullTime_hrs = ($totalServiceYears * 2087) + ($totalServicemonths * 2087); // + ($totalServicedays * 2087);

        $partTimeServiceDuration = $this->getPartTimeServiceDuration($empId); // getPartTimeServiceDuration_ByDates
        // dd($partTimeServiceDuration);
        /* Total services percentage with part time services ends */
        $csrsOffsetDate = $emp['CSRSOffsetDate'];
        $fersTransferDate = $emp['FERSTransferDate'];

        /* Caculate military service hours, non deduction service hours and Refunded service hours that should be substracted from service duration before calculationg pension */

        $militaryServiceDurationSRS = 0;
        $mTotalValidService = 0;
        $mTotalInvalidService = 0;
        if (count($emp['military_service']) > 0) {
            // for military services conditions for eligibility and pension service duration are same.
            $mTotalSubtractDurationEligibility = 0;
            $mTotalSubtractDurationPension = 0;
            if ($emp['systemType'] == 'FERS') {
                $mbeforeDate = new \DateTime(date('Y-m-d H:i:s', strtotime('1-1-1957')));
            } else {
                $mbeforeDate = new \DateTime(date('Y-m-d H:i:s', strtotime('10-1-1957')));
            }
            foreach ($emp['military_service'] as $mservice) {
                $militaryServiceStart = new \DateTime($mservice['FromDate']);
                $militaryServiceEnd = new \DateTime($mservice['ToDate']);
                if ($militaryServiceStart < $mbeforeDate) { // before given date
                    if ($militaryServiceEnd < $mbeforeDate) {
                        $validMServiceInterval = $militaryServiceStart->diff($militaryServiceEnd);
                        $invalidMServiceInterval = $militaryServiceStart->diff($militaryServiceStart);
                    } else {
                        if ($mservice['DepositOwed'] == 0) {
                            $validMServiceInterval = $militaryServiceStart->diff($mbeforeDate);
                            $invalidMServiceInterval = $mbeforeDate->diff($militaryServiceEnd);
                        } else {
                            $validMServiceInterval = $militaryServiceStart->diff($militaryServiceEnd);
                            $invalidMServiceInterval = $militaryServiceStart->diff($militaryServiceStart);
                        }
                    }
                    $mDurationSRS = 0;
                } else { // On or after given date
                    if ($mservice['DepositOwed'] == 0) { // not paid

                        $validMServiceInterval = $militaryServiceStart->diff($militaryServiceStart);
                        $invalidMServiceInterval = $militaryServiceStart->diff($militaryServiceEnd);

                        $mServiceInterval = $militaryServiceEnd->diff($militaryServiceStart);
                        $mDurationSRS = $mServiceInterval->y;
                        $mDurationSRSmonths = $mServiceInterval->m / 12;
                        $mDurationSRS = round($mDurationSRS + $mDurationSRSmonths);
                    } else { // paid
                        $validMServiceInterval = $militaryServiceStart->diff($militaryServiceEnd);
                        $invalidMServiceInterval = $militaryServiceStart->diff($militaryServiceStart);



                        $mServiceInterval = $militaryServiceEnd->diff($militaryServiceStart);
                        $mDurationSRS = $mServiceInterval->y;
                        $mDurationSRSmonths = $mServiceInterval->m / 12;
                        $mDurationSRS = round($mDurationSRS + $mDurationSRSmonths);

                        // $substractDurationEligibility = 0;
                        // $substractDurationPension = 0;
                        // $mDurationSRS = 0;
                    }
                }

                $mValidMonthsPerc = ($validMServiceInterval->m / 12);
                $mTotalValidService = $mTotalValidService + $validMServiceInterval->y + $mValidMonthsPerc;

                $mInvalidMonthsPerc = ($invalidMServiceInterval->m / 12);
                $mTotalInvalidService = $mTotalInvalidService + $invalidMServiceInterval->y + $mInvalidMonthsPerc;

                /* Calculate military service duration for substract */
                $militaryServiceEndSRS = new \DateTime($mservice['ToDate']);
                $mServiceIntervalSRS = $militaryServiceEndSRS->diff($militaryServiceStart);

                $militaryServiceDurationSRS = $militaryServiceDurationSRS + $mDurationSRS;
            }
        } // else part not required as values defined before if

        $ndTotalValidServiceE = 0;
        $ndTotalInvalidServiceE = 0;
        $ndTotalValidServiceP = 0;
        $ndTotalInvalidServiceP = 0;
        if (count($emp['non_deduction_service']) > 0) {
            if ($emp['systemType'] == 'FERS') {
                $ndBeforeDate = new \DateTime(date('Y-m-d H:i:s', strtotime('1-1-1989')));
                // echo "<pre>"; print_r($emp['non_deduction_service']); exit;
                foreach ($emp['non_deduction_service'] as $ndService) {
                    $ndServiceStart = new \DateTime($ndService['FromDate']);
                    $endNdService = new \DateTime($ndService['ToDate']);

                    if ($ndServiceStart < $ndBeforeDate) {
                        if ($ndService['DepositOwed'] == 1) { // not paid
                            $validNDServiceIntervalP = $validNDServiceIntervalE = $ndServiceStart->diff($ndServiceStart);
                            $inValidNDServiceIntervalP = $inValidNDServiceIntervalE = $ndServiceStart->diff($endNdService);
                        } else { // paid
                            if ($endNdService < $ndBeforeDate) {
                                $validNDServiceIntervalP = $validNDServiceIntervalE = $ndServiceStart->diff($endNdService);
                                $inValidNDServiceIntervalP = $inValidNDServiceIntervalE = $ndServiceStart->diff($ndServiceStart);
                            } else {
                                $validNDServiceIntervalP = $validNDServiceIntervalE = $ndServiceStart->diff($ndBeforeDate);
                                $inValidNDServiceIntervalP = $inValidNDServiceIntervalE = $endNdService->diff($ndBeforeDate);
                            }
                        }
                    } else {
                        $validNDServiceIntervalP = $validNDServiceIntervalE = $ndServiceStart->diff($ndServiceStart);
                        $inValidNDServiceIntervalP = $inValidNDServiceIntervalE = $ndServiceStart->diff($endNdService);
                    }

                    $ndValidMonthPercE = ($validNDServiceIntervalE->m / 12);
                    $ndTotalValidServiceE = $ndTotalValidServiceE + $validNDServiceIntervalE->y + $ndValidMonthPercE;

                    $ndinValidMonthPercE = ($inValidNDServiceIntervalE->m / 12);
                    $ndTotalInvalidServiceE = $ndTotalInvalidServiceE + $inValidNDServiceIntervalE->y + $ndinValidMonthPercE;

                    $ndValidMonthPercP = ($validNDServiceIntervalP->m / 12);
                    $ndTotalValidServiceP = $ndTotalValidServiceP + $validNDServiceIntervalP->y + $ndValidMonthPercP;

                    $ndinValidMonthPercP = ($inValidNDServiceIntervalP->m / 12);
                    $ndTotalInvalidServiceP = $ndTotalInvalidServiceP + $inValidNDServiceIntervalP->y + $ndinValidMonthPercP;


                    // $ndTotalSubtractDurationEligibility = $ndTotalSubtractDurationEligibility + $ndSubstractionTime;
                    // $ndTotalSubtractDurationPension = $ndTotalSubtractDurationEligibility;
                }
            } else { // CSRS Nondeduction Services
                $ndBeforeDate = new \DateTime(date('Y-m-d H:i:s', strtotime('10-1-1982')));
                foreach ($emp['non_deduction_service'] as $ndService) {
                    $ndServiceStart = new \DateTime($ndService['FromDate']);
                    $endNdService = new \DateTime($ndService['ToDate']);

                    $ndSubstractionTime = 0;
                    $validNDServiceIntervalP = $validNDServiceIntervalE = $ndServiceStart->diff($endNdService);
                    $inValidNDServiceIntervalP = $inValidNDServiceIntervalE = $ndServiceStart->diff($ndServiceStart);

                    if ($ndServiceStart >= $ndBeforeDate) {
                        if ($endNdService > $ndBeforeDate) {
                            if ($ndService['DepositOwed'] == 1) { // not Paid
                                $validNDServiceIntervalE = $ndServiceStart->diff($endNdService);
                                $inValidNDServiceIntervalE = $ndServiceStart->diff($ndServiceStart);
                                $validNDServiceIntervalP = $ndServiceStart->diff($ndBeforeDate);
                                $inValidNDServiceIntervalP = $ndBeforeDate->diff($endNdService);
                            }
                        }
                    }

                    $ndValidMonthPercE = ($validNDServiceIntervalE->m / 12);
                    $ndTotalValidServiceE = $ndTotalValidServiceE + $validNDServiceIntervalE->y + $ndValidMonthPercE;

                    $ndinValidMonthPercE = ($inValidNDServiceIntervalE->m / 12);
                    $ndTotalInvalidServiceE = $ndTotalInvalidServiceE + $inValidNDServiceIntervalE->y + $ndinValidMonthPercE;

                    $ndValidMonthPercP = ($validNDServiceIntervalP->m / 12);
                    $ndTotalValidServiceP = $ndTotalValidServiceP + $validNDServiceIntervalP->y + $ndValidMonthPercP;

                    $ndinValidMonthPercP = ($inValidNDServiceIntervalP->m / 12);
                    $ndTotalInvalidServiceP = $ndTotalInvalidServiceP + $inValidNDServiceIntervalP->y + $ndinValidMonthPercP;

                    // $ndTotalSubtractDurationEligibility = 0;
                    // $ndTotalSubtractDurationPension = $ndTotalSubtractDurationPension + $ndSubstractionTime;
                }
            }
        }

        // $rTotalSubtractDurationEligibility = 0;
        // $rTotalSubtractDurationPension = 0;

        $rTotalValidServiceE = 0;
        $rTotalInvalidServiceE = 0;
        $rTotalValidServiceP = 0;
        $rTotalInvalidServiceP = 0;

        if (count($emp['refunded_service']) > 0) {
            if ($emp['systemType'] == 'FERS') {
                // $retDate
                $rBeforeDate = new \DateTime(date('Y-m-d H:i:s', strtotime('10-28-2009')));
                if ($retDate >= $rBeforeDate) {
                    foreach ($emp['refunded_service'] as $rService) {
                        $rServiceStart = new \DateTime($rService['FromDate']);
                        $endRService = new \DateTime($rService['ToDate']);
                        // $rServiceDuration = $endRService->diff($rServiceStart);
                        // $rSubstractionTime = (float) $rServiceDuration->y . '.' . $rServiceDuration->m;
                        if ($rService['Redeposit'] == 0) { // paid
                            // $rSubstractionTime = 0;
                            $validRServiceIntervalP = $validRServiceIntervalE = $rServiceStart->diff($endRService);
                            $inValidRServiceIntervalP = $inValidRServiceIntervalE = $rServiceStart->diff($rServiceStart);
                        } else { // not paid
                            $validRServiceIntervalP = $rServiceStart->diff($rServiceStart);
                            $validRServiceIntervalE = $rServiceStart->diff($endRService);

                            $inValidRServiceIntervalP = $rServiceStart->diff($endRService);
                            $inValidRServiceIntervalE = $rServiceStart->diff($rServiceStart);
                        }

                        $rValidMonthPercE = ($validRServiceIntervalE->m / 12);
                        $rTotalValidServiceE = $rTotalValidServiceE + $validRServiceIntervalE->y + $rValidMonthPercE;

                        $rinValidMonthPercE = ($inValidRServiceIntervalE->m / 12);
                        $rTotalInvalidServiceE = $rTotalInvalidServiceE + $inValidRServiceIntervalE->y + $rinValidMonthPercE;

                        $rValidMonthPercP = ($validRServiceIntervalP->m / 12);
                        $rTotalValidServiceP = $rTotalValidServiceP + $validRServiceIntervalP->y + $rValidMonthPercP;

                        $rinValidMonthPercP = ($inValidRServiceIntervalP->m / 12);
                        $rTotalInvalidServiceP = $rTotalInvalidServiceP + $inValidRServiceIntervalP->y + $rinValidMonthPercP;

                        // $rTotalSubtractDurationEligibility = $rTotalSubtractDurationEligibility + 0;
                        // $rTotalSubtractDurationPension = $rTotalSubtractDurationPension + $rSubstractionTime;
                    }
                }
            } else { // CSRS Refunded services
                $rBeforeDate = new \DateTime(date('Y-m-d H:i:s', strtotime('3-1-1991')));
                foreach ($emp['refunded_service'] as $rService) {
                    $rServiceStart = new \DateTime($rService['FromDate']);
                    $endRService = new \DateTime($rService['ToDate']);

                    $validRServiceIntervalP =  $validRServiceIntervalE = $rServiceStart->diff($endRService);

                    $inValidRServiceIntervalP =  $inValidRServiceIntervalE = $rServiceStart->diff($rServiceStart);
                    if ($endRService >= $rBeforeDate) {
                        if ($rService['Redeposit'] == 1) { // Not Paid
                            $validRServiceIntervalP = $rServiceStart->diff($rBeforeDate);
                            $inValidRServiceIntervalP = $rBeforeDate->diff($endRService);
                        }
                    }

                    $rValidMonthPercE = ($validRServiceIntervalE->m / 12);
                    $rTotalValidServiceE = $rTotalValidServiceE + $validRServiceIntervalE->y + $rValidMonthPercE;

                    $rinValidMonthPercE = ($inValidRServiceIntervalE->m / 12);
                    $rTotalInvalidServiceE = $rTotalInvalidServiceE + $inValidRServiceIntervalE->y + $rinValidMonthPercE;

                    $rValidMonthPercP = ($validRServiceIntervalP->m / 12);
                    $rTotalValidServiceP = $rTotalValidServiceP + $validRServiceIntervalP->y + $rValidMonthPercP;

                    $rinValidMonthPercP = ($inValidRServiceIntervalP->m / 12);
                    $rTotalInvalidServiceP = $rTotalInvalidServiceP + $inValidRServiceIntervalP->y + $rinValidMonthPercP;
                }
            }
        }

        $totalSubstractionEligibility = $mTotalInvalidService + $ndTotalInvalidServiceE + $rTotalInvalidServiceE;

        $totalSubstractionPension = $mTotalInvalidService + $ndTotalInvalidServiceP + $rTotalInvalidServiceP;

        $totalValidDurationE = $mTotalValidService + $ndTotalValidServiceE + $rTotalValidServiceE;
        $totalValidDurationP = $mTotalValidService + $ndTotalValidServiceP + $rTotalValidServiceP;
        // echo $totalServiceDuration;
        // exit;

        if (($scenario > 1) && ($scenario < 5)) {
            $totalServiceDuration = $totalServiceDuration + ($scenario - 1);
        }
        $serviceDurationForEligibility = $currentServiceDuration - $totalSubstractionEligibility;
        $serviceDurationForPension = $totalServiceDuration - $totalSubstractionPension;

        // echo $totalServiceDuration . " ---- " . $totalSubstractionPension;
        // die;
        /** Not adding unused sick leave hours into service duration, as we are adding it while calculating pension */
        // if ($emp['UnusedSickLeave'] > 0) {
        //     $hrs = $emp['UnusedSickLeave'];
        //     $unusedSickLeaveYears = $hrs / 2087;
        // } else {
        //     $unusedSickLeaveYears = 0;
        // }
        if (($scenario > 1) && ($scenario < 5)) {
            $totalServiceDuration = $totalServiceDuration + ($scenario - 1);
        }

        // $serviceDurationForPension = $serviceDurationForPension + $unusedSickLeaveYears;
        $result['serviceDurationForEligibility'] = $serviceDurationForEligibility;
        $result['serviceDurationForPension'] = $serviceDurationForPension;
        $result['emp_age'] = $emp_age;
        $result['totalServiceDuration'] = $totalServiceDuration;

        /* Calculate Earliest Retirement Date */
        $invalidDuration = round($totalSubstractionEligibility);
        $penalty_message = "";
        if ($emp['systemType'] == 'FERS') {
            if ($emp['retirementType'] == 'Regular') { // FULL
                if (($emp['empType'] == 'Regular') || ($emp['empType'] == 'eCBPO')) {
                    if (($serviceDurationForEligibility == 20) && ($emp_age == 60)) {
                        $minRetirementDate = $bday->modify('+60 years')->format('Y-m-d H:i:s');
                    } elseif (($serviceDurationForEligibility == 5) && ($emp_age == 62)) {
                        $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                    } else { // if($serviceDurationForEligibility == 30) {
                        $minRetirementDate = $serviceStartDate->modify('+' . $invalidDuration . 'years');
                        $minRetirementDate = $serviceStartDate->modify('+30 years')->format('Y-m-d H:i:s');
                    }
                } else {
                    if (($emp['otherEmpType'] == 'Air Traffic Controller') || ($emp['otherEmpType'] == 'Law Enforcement') || ($emp['otherEmpType'] == 'Firefighter')) {
                        if (($serviceDurationForEligibility == 20) && ($emp_age == 50)) {
                            $minRetirementDate = $bday->modify('+50 years')->format('Y-m-d H:i:s');
                        } else {
                            $minRetirementDate = $serviceStartDate->modify('+' . $invalidDuration);
                            $minRetirementDate = $serviceStartDate->modify('+25 years')->format('Y-m-d H:i:s');
                        }
                    }
                }
            } elseif ($emp['retirementType'] == 'Early-Out') { // Early Out
                if (($serviceDurationForEligibility == 20) && ($emp_age == 50)) {
                    $minRetirementDate = $serviceStartDate->modify('+20 years')->format('Y-m-d H:i:s');
                } else { // if($serviceDurationForEligibility == 25) {
                    $minRetirementDate = $serviceStartDate->modify('+' . $invalidDuration . 'years');
                    $minRetirementDate = $serviceStartDate->modify('+25 years')->format('Y-m-d H:i:s');
                }
            } elseif ($emp['retirementType'] == 'MRA+10') { // MRA + 10
                if (($emp['empType'] == 'Regular') || ($emp['empType'] == 'eCBPO')) {
                    $minRetirementDate = $serviceStartDate->modify('+' . $invalidDuration . 'years');
                    $minRetirementDate = $serviceStartDate->modify('+10 years')->format('Y-m-d H:i:s');
                    $penalty_message = "Warning! MRA Penalty will apply, Pension will be penalized";
                } else {
                    $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                    $penalty_message = "MRA+10 retirement type is only for REGULAR employees of FERS system.";
                }
            } else { // Disability
                $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                $penalty_message = "!Disability Retirement type.";
            }
        } elseif ($emp['systemType'] == 'CSRS') {
            if ($emp['retirementType'] == 'Regular') { // FULL
                if (($emp['empType'] == 'Regular') || ($emp['empType'] == 'eCBPO')) {
                    if (($serviceDurationForEligibility == 5) && ($emp_age == 62)) {
                        $minRetirementDate = $serviceStartDate->modify('+5 years')->format('Y-m-d H:i:s');
                    } elseif (($serviceDurationForEligibility == 20) && ($emp_age == 60)) {
                        $minRetirementDate = $serviceStartDate->modify('+20 years')->format('Y-m-d H:i:s');
                    } elseif (($serviceDurationForEligibility == 30) && ($emp_age == 55)) {
                        $minRetirementDate = $serviceStartDate->modify('+30 years')->format('Y-m-d H:i:s');
                    } else {
                        $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                    }
                } else { // empType = Other
                    if (($emp['otherEmpType'] == 'Law Enforcement') || ($emp['otherEmpType'] == 'Firefighter')) {
                        if (($serviceDurationForEligibility == 20) && ($emp_age == 50)) {
                            $minRetirementDate = $bday->modify('+50 years')->format('Y-m-d H:i:s');
                        } else {
                            $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                        }
                    } else { // if(($emp['otherEmpType'] == 'Air Traffic Controller')) {
                        if (($serviceDurationForEligibility == 20) && ($emp_age == 50)) {
                            $minRetirementDate = $bday->modify('+50 years')->format('Y-m-d H:i:s');
                        } else { // if($serviceDurationForEligibility == 25) {
                            $minRetirementDate = $serviceStartDate->modify('+' . $invalidDuration . 'years');
                            $minRetirementDate = $serviceStartDate->modify('+25 years')->format('Y-m-d H:i:s');
                        }
                    }
                }
            } elseif ($emp['retirementType'] == 'Early-Out') { // Early Out
                if (($serviceDurationForEligibility == 20) && ($emp_age == 50)) {
                    $minRetirementDate = $serviceStartDate->modify('+20 years')->format('Y-m-d H:i:s');
                } elseif ($serviceDurationForEligibility == 25) {
                    $minRetirementDate = $serviceStartDate->modify('+25 years')->format('Y-m-d H:i:s');
                } else {
                    $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                }
            } else { // condition not given so self created else block
                $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
            }
        } else {
            if ($fersTransferDate != null) {
                if ($emp['retirementType'] == 'Regular') { // FULL
                    if (($emp['empType'] == 'Regular') || ($emp['empType'] == 'eCBPO')) {
                        if ($serviceDurationForEligibility >= 30) {
                            $minRetirementDate = $serviceStartDate->modify('+' . $invalidDuration . 'years');
                            $minRetirementDate = $serviceStartDate->modify('+30 years')->format('Y-m-d H:i:s');
                        } elseif ($serviceDurationForEligibility >= 20) {
                            $minRetirementDate = $bday->modify('+60 years')->format('Y-m-d H:i:s');
                        } else {
                            $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                        }
                    } else {
                        if (($emp['otherEmpType'] == 'Air Traffic Controller') || ($emp['otherEmpType'] == 'Law Enforcement') || ($emp['otherEmpType'] == 'Firefighter')) {
                            if ($serviceDurationForEligibility >= 25) {
                                $minRetirementDate = $serviceStartDate->modify('+' . $invalidDuration . 'years');
                                $minRetirementDate = $serviceStartDate->modify('+25 years')->format('Y-m-d H:i:s');
                            } else {
                                $minRetirementDate = $bday->modify('+50 years')->format('Y-m-d H:i:s');
                            }
                        }
                    }
                } elseif ($emp['retirementType'] == 'Early-Out') { // Early Out
                    if ($serviceDurationForEligibility >= 25) {
                        $minRetirementDate = $serviceStartDate->modify('+' . $invalidDuration . 'years');
                        $minRetirementDate = $serviceStartDate->modify('+25 years')->format('Y-m-d H:i:s');
                    } else {
                        $minRetirementDate = $bday->modify('+50 years')->format('Y-m-d H:i:s');
                    }
                } elseif ($emp['retirementType'] == 'Mandatory') { // MRA + 10
                    if (($emp['empType'] == 'Regular') || ($emp['empType'] == 'eCBPO')) {
                        $minRetirementDate = $serviceStartDate->modify('+' . $invalidDuration . 'years');
                        $minRetirementDate = $serviceStartDate->modify('+10 years')->format('Y-m-d H:i:s');
                    } else {
                        $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                    }
                } else { // Disability
                    $minRetirementDate = date('Y-m-d H:i:s');
                }
            } else {  //if($csrsOffsetDate != null) { // normal CSRS rules
                if ($emp['retirementType'] == 'Regular') { // FULL
                    if (($emp['empType'] == 'Regular') || ($emp['empType'] == 'eCBPO')) {
                        if ($serviceDurationForEligibility >= 30) {
                            $minRetirementDate = $bday->modify('+55 years')->format('Y-m-d H:i:s');
                        } elseif ($serviceDurationForEligibility >= 20) {
                            $minRetirementDate = $bday->modify('+60 years')->format('Y-m-d H:i:s');
                        } else {
                            $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                        }
                    } elseif (($emp['otherEmpType'] == 'Law Enforcement') || ($emp['otherEmpType'] == 'Firefighter')) {
                        if ($serviceDurationForEligibility >= 20) {
                            $minRetirementDate = $bday->modify('+50 years')->format('Y-m-d H:i:s');
                        } else {
                            $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                        }
                    } elseif ($emp['otherEmpType'] == 'Air Traffic Controller') {
                        if ($serviceDurationForEligibility >= 25) {
                            $minRetirementDate = date('Y-m-d H:i:s');
                        } elseif ($serviceDurationForEligibility >= 20) {
                            $minRetirementDate = $bday->modify('+50 years')->format('Y-m-d H:i:s');
                        } else {
                            $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                        }
                    }
                } elseif ($emp['retirementType'] == 'Early-Out') { // Early Out
                    if ($serviceDurationForEligibility >= 25) {
                        $minRetirementDate = date('Y-m-d H:i:s');
                    } elseif ($serviceDurationForEligibility >= 20) {
                        $minRetirementDate = $bday->modify('+50 years')->format('Y-m-d H:i:s');
                    } else {
                        $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                    }
                } else { // condition not given so self created else block
                    if ($serviceDurationForEligibility >= 30) {
                        $minRetirementDate = $bday->modify('+55 years')->format('Y-m-d H:i:s');
                    } elseif ($serviceDurationForEligibility >= 20) {
                        $minRetirementDate = $bday->modify('+60 years')->format('Y-m-d H:i:s');
                    } else {
                        $minRetirementDate = $bday->modify('+62 years')->format('Y-m-d H:i:s');
                    }
                }
            }
        }

        // echo $militaryServiceDurationSRS;
        // die;
        $result['serviceDurationForSRS'] = $totalServiceDuration - $militaryServiceDurationSRS;
        $result['minRetirementDate'] = date('Y-m-d', strtotime($minRetirementDate));
        $result['penalty_message'] = $penalty_message;

        $result['validInvalidServices'] = [
            'mTotalValidService' => $mTotalValidService,
            'mTotalInvalidService' => $mTotalInvalidService,
            'ndTotalValidServiceE' => $ndTotalValidServiceE,
            'ndTotalInvalidServiceE' => $ndTotalInvalidServiceE,
            'ndTotalValidServiceP' => $ndTotalValidServiceP,
            'ndTotalInvalidServiceP' => $ndTotalInvalidServiceP,
            'rTotalValidServiceE' => $rTotalValidServiceE,
            'rTotalInvalidServiceE' => $rTotalInvalidServiceE,
            'rTotalValidServiceP' => $rTotalValidServiceP,
            'rTotalInvalidServiceP' => $rTotalInvalidServiceP,
            'totalInvalidDurationE' => $totalSubstractionEligibility,
            'totalInvalidDurationP' => $totalSubstractionPension,
            'totalValidDurationE' => $totalValidDurationE,
            'totalValidDurationP' => $totalValidDurationP,
            'currentServiceDuration' => $currentServiceDuration,
            'totalServiceDuration' => $totalServiceDuration,
            'fullTime_hrs' => $fullTime_hrs,
            'partTimeServiceDuration' => $partTimeServiceDuration
        ];

        return $result;
    }

    /* ****************************New function for minimum retirement age*************************** */
    public function getMinumumRetirementAge($empId = null, $scenario = 1, $dob = null)
    {
        $emp = $this->getById($empId)->toArray();
        if (!isset($emp['eligibility']['DateOfBirth']) || ($emp['eligibility']['DateOfBirth'] == NULL)) {
            if ($dob != null) {
                $birthdate = date('Y-m-d H:i:s', strtotime($dob));
            } else {
                $birthdate = date('Y-m-d H:i:s');
            }
        } else {
            $birthdate = $emp['eligibility']['DateOfBirth'];
        }
        if (!isset($emp['eligibility']['DateOfBirth']) && ($dob == NULL)) {
            $data['minRetirementDate'] = '';
            $data['mra_str'] = '';
            $data['penalty_message'] = 'Please save Date of birth to calculate MRA';
            $data['success'] = false;
        }

        if (isset($birthdate)) {
            $byear = date('Y', strtotime($birthdate));
            $bday = new \DateTime($birthdate);
            if ($byear < 1948) {
                $minRetirementDate = $bday->modify('+55 years')->format('Y-m-d H:i:s');
            } elseif ($byear == 1948) {
                $minRetirementDate = $bday->modify('+55 years')->modify('+2 months')->format('Y-m-d H:i:s');
            } elseif ($byear == 1949) {
                $minRetirementDate = $bday->modify('+55 years')->modify('+4 months')->format('Y-m-d H:i:s');
            } elseif ($byear == 1950) {
                $minRetirementDate = $bday->modify('+55 years')->modify('+6 months')->format('Y-m-d H:i:s');
            } elseif ($byear == 1951) {
                $minRetirementDate = $bday->modify('+55 years')->modify('+8 months')->format('Y-m-d H:i:s');
            } elseif ($byear == 1952) {
                $minRetirementDate = $bday->modify('+55 years')->modify('+10 months')->format('Y-m-d H:i:s');
            } elseif ($byear >= 1953 && $byear <= 1964) {
                $minRetirementDate = $bday->modify('+56 years')->format('Y-m-d H:i:s');
            } elseif ($byear == 1965) {
                $minRetirementDate = $bday->modify('+56 years')->modify('+2 months')->format('Y-m-d H:i:s');
            } elseif ($byear == 1966) {
                $minRetirementDate = $bday->modify('+56 years')->modify('+4 months')->format('Y-m-d H:i:s');
            } elseif ($byear == 1967) {
                $minRetirementDate = $bday->modify('+56 years')->modify('+6 months')->format('Y-m-d H:i:s');
            } elseif ($byear == 1968) {
                $minRetirementDate = $bday->modify('+56 years')->modify('+8 months')->format('Y-m-d H:i:s');
            } elseif ($byear == 1969) {
                $minRetirementDate = $bday->modify('+56 years')->modify('+10 months')->format('Y-m-d H:i:s');
            } elseif ($byear >= 1970) {
                $minRetirementDate = $bday->modify('+57 years')->format('Y-m-d H:i:s');
            } else {
                $minRetirementDate = $bday->modify('+58 years')->format('Y-m-d H:i:s');
            }
            $min_r = new \DateTime($minRetirementDate);
            $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
            $now = new \DateTime();
            // $mra_interval = $min_r->diff($bday);
            if ($min_r < $now) {
                $minRetirementDate = date('Y-m-t');
                // $mra_interval = $now->diff($bday);
            } else {
                // $mra_interval = $min_r->diff($bday);
                $minRetirementDate = date('Y-m-d', strtotime($minRetirementDate));
            }
            $mra_interval = $min_r->diff($bday);
            $data['mra_str'] = '(MRA is age ' . $mra_interval->y . 'y ' . $mra_interval->m . 'm)';
            $data['mra_year'] = $mra_interval->y;
            $data['mra_month'] = $mra_interval->m;
            $data['success'] = true;
            $data['minRetirementDate'] = $minRetirementDate;
        } else {
            $data['minRetirementDate'] = '';
            $data['mra_str'] = '';
            $data['penalty_message'] = 'Please save Date of birth to calculate MRA';
            $data['mra_year'] = '';
            $data['mra_month'] = '';
            $data['success'] = false;
            $data['minRetirementDate'] = '';
        }

        return $data;
    }

    public function getFullEligibilityRetirementAge($empId = null, $dob = null, $leaveSCD = null)
    {
        $emp = $this->getById($empId)->toArray();

        if (!isset($emp['eligibility']['DateOfBirth']) || ($emp['eligibility']['DateOfBirth'] == NULL)) {
            if ($dob != null && $leaveSCD != null) {
                $birthdate = date('Y-m-d H:i:s', strtotime($dob));
                $joiningDate = date('Y-m-d H:i:s', strtotime($leaveSCD));
            }
        } else {
            $birthdate = $emp['eligibility']['DateOfBirth'];
            $joiningDate = $emp['eligibility']['LeaveSCD'];
        }
        if (!isset($emp['eligibility']['DateOfBirth']) && ($dob == null)) {
            $data['fullRetirementDate'] = '';
            $data['penalty_message'] = 'Please add Date of birth to calculate MRA.';
            $data['success'] = false;
        }

        if (!isset($emp['eligibility']['LeaveSCD']) && ($leaveSCD == null)) {
            $data['fullRetirementDate'] = '';
            $data['penalty_message'] = 'Please add leave SCD to calculate MRA.';
            $data['success'] = false;
        }

        if ($emp['EmployeeType'] == 'Other') {
            $start_service = new \DateTime($emp['eligibility']['LeaveSCD']);
            if ($emp['SpecialProvisionsDate'] != NULL) {
                unset($start_service);
                $start_service = new \DateTime($emp['SpecialProvisionsDate']);
            }
            $start_service_modify = $start_service;
            $service20Year = $start_service_modify->modify('+ 20 years');
            $bDay = new \DateTime($emp['eligibility']['DateOfBirth']);

            $ageAt20Service = $bDay->diff($service20Year);
            $ageAt20Service_year = $ageAt20Service->y;
            if ($ageAt20Service_year >= 50) {
                $fullRetirementDate = $service20Year->format('Y-m-t H:i:s');
            } else {
                $service25Year = $start_service->modify('+ 25 years');
                $fullRetirementDate = $service25Year->format('Y-m-t H:i:s');
            }
            $now = new \DateTime();
            $fullRetObj = new \DateTime($fullRetirementDate);
            if ($fullRetObj < $now) {
                $fullRetirementDate = date('Y-m-t');
            } else {
                $fullRetirementDate = date('Y-m-t', strtotime($fullRetirementDate));
            }
            $data = [
                'fullRetirementDate' => $fullRetirementDate,
                'success' => true
            ];
        } else {
            if (isset($birthdate) && (isset($joiningDate))) {
                $mra_Arr = $this->getMinumumRetirementAge($empId, 1, $dob);
                $mra = $mra_Arr['minRetirementDate'];
                $mra_age = $mra_Arr['mra_year'];
                $mraObj = new \DateTime($mra);
                $dojObj = new \DateTime($joiningDate);
                $mraServiceObj = $dojObj->diff($mraObj);
                $mraService = $mraServiceObj->y + ($mraServiceObj->m / 12) + ($mraServiceObj->d / 365);
                // echo $mra . " -- " . $mraService;
                // die;
                $mraService = round($mraService);
                if ($mraService >= 30) {
                    $fullRetirementDate = $mra;
                } else {
                    $dateAtAge60 = new \DateTime($birthdate);
                    $dateAtAge60->modify('+ 60 years');
                    $dojObj = new \DateTime($joiningDate);
                    $serviceObj = $dateAtAge60->diff($dojObj);
                    $serviceYears = $serviceObj->y;
                    $serviceMonths = $serviceObj->m;
                    if ($serviceYears >= 20) {
                        $fullRetirementDate = $dateAtAge60->format('Y-m-d H:i:s');
                    } else {
                        // turns 62 and service minimum 5 years
                        $dateAtAge62 = new \DateTime($birthdate);
                        $dateAtAge62->modify("+ 62 years");
                        $serviceAt62 = $dojObj->diff($dateAtAge62);
                        $fullRetirementDate = $dateAtAge62->format('Y-m-d H:i:s');
                    }

                    $fullDateObj = new \DateTime($fullRetirementDate);
                    $fullDuration = $dojObj->diff($fullDateObj);
                    if ($fullDuration->y > 30) {
                        $mra_less = 30 - $mraService;
                        // echo $mra_less;
                        // die;
                        $fullDuration = $mraObj->modify('+ ' . $mra_less . ' years');
                        $fullRetirementDate = $fullDuration->format('Y-m-d H:i:s');
                    }
                }

                $now = new \DateTime();
                $fullRetObj = new \DateTime($fullRetirementDate);
                if ($fullRetObj < $now) {
                    $fullRetirementDate = date('Y-M-t');
                } else {
                    $fullRetirementDate = date('Y-m-t', strtotime($fullRetirementDate));
                }
                $fra = [
                    'fullRetirementDate' => $fullRetirementDate,
                    'success' => true
                ];
                return $fra;
            } else {
                $data['fullRetirementDate'] = '';
                $data['penalty_message'] = 'Please save leave SCD to calculate Retirement Date';
                $data['success'] = false;
            }
        }

        return $data;
    }


    public function getFirstPension($empId = null, $scenario = 1)
    {
        // Calculate only first pension
        // without panelties
        $pension = 0;
        if ($empId != null) {
            $serviceDurationArr = $this->getEarliestRetirement($empId);
            // dd($serviceDurationArr['serviceDurationForPension']);
            $serviceDuration = $serviceDurationArr['serviceDurationForPension'];
            $emp = $this->getById($empId)->toArray();
            $empConf = $this->getEmployeeConf($empId);
            // dd($serviceDuration);
            // echo "<pre>";
            // print_r($emp);
            // exit;
            $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
            $emp_age = $serviceDurationArr['emp_age'];
            $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
            $retInterval = $retDate->diff($bday);
            $retirementAge = $retInterval->y;

            if ($scenario > 1) {
                $retirementAge = $retirementAge + ($scenario - 1);
                $serviceDuration = $serviceDuration + ($scenario - 1);
            }
            // dd($serviceDuration);

            $high3Arr = $this->calcProjectedHigh3Average($empId, $scenario);
            $high3Avg = $high3Arr['projectedHigh3Avg'];

            // echo $high3Avg;
            // die;
            $unusedSickLeaveHrs = $emp['UnusedSickLeave'];
            if ($unusedSickLeaveHrs > 0) {
                $unusedSickLeaveYears = ($unusedSickLeaveHrs / 2087);
            } else {
                $unusedSickLeaveYears = 0;
            }
            // dd($unusedSickLeaveYears);
            // echo $serviceDuration + $unusedSickLeaveYears;
            // die;

            $serviceDuration = round($serviceDuration + $unusedSickLeaveYears, 3);
            $fraction = $serviceDuration - floor($serviceDuration);

            // dd($fraction);
            // 0.083333 = 1 full month
            // 0.166667 = 2 full months
            // 0.250000 = 3 full months
            // 0.333333 = 4 full months
            // 0.416667 = 5 full months
            // 0.500000 = 6 full months
            // 0.583333 = 7 full months
            // 0.666667 = 8 full months
            // 0.750000 = 9 full months
            // 0.833333 = 10 full months
            // 0.916667 = 11 full months
            if ($fraction >= 0.0833 && $fraction < 0.166667) {

                $serviceDuration = floor($serviceDuration) + 0.0833;
            } elseif ($fraction >= 0.166667 && $fraction < 0.250000) {

                $serviceDuration = floor($serviceDuration) + 0.166667;
            } elseif ($fraction >= 0.250000 && $fraction < 0.333333) {

                $serviceDuration = floor($serviceDuration) + 0.250000;
            } elseif ($fraction >= 0.333333 && $fraction < 0.416667) {

                $serviceDuration = floor($serviceDuration) + 0.333333;
            } elseif ($fraction >= 0.416667 && $fraction < 0.500000) {

                $serviceDuration = floor($serviceDuration) + 0.416667;
            } elseif ($fraction >= 0.500000 && $fraction < 0.583333) {

                $serviceDuration = floor($serviceDuration) + 0.500000;
            } elseif ($fraction >= 0.583333 && $fraction < 0.666667) {

                $serviceDuration = floor($serviceDuration) + 0.583333;
            } elseif ($fraction >= 0.666667 && $fraction < 0.750000) {

                $serviceDuration = floor($serviceDuration) + 0.666667;
            } elseif ($fraction >= 0.750000 && $fraction < 0.833333) {

                $serviceDuration = floor($serviceDuration) + 0.750000;
            } elseif ($fraction >= 0.833333 && $fraction < 0.916667) {

                $serviceDuration = floor($serviceDuration) + 0.833333;
            } elseif ($fraction >= 0.916667) {
                $serviceDuration = floor($serviceDuration) + 0.916667;
            }

            if (($emp['systemType'] == 'FERS')) { //  || ($emp['systemType'] == 'Transfers')

                if ($emp['retirementType'] == 'Regular') { // FULL
                    if ($emp['empType'] == 'Regular') {
                        // echo $high3Avg . " ---- " . $serviceDuration . " --- " . $unusedSickLeaveYears;
                        // die;
                        if ($retirementAge >= 62) {
                            // echo $serviceDuration + $unusedSickLeaveYears;
                            // die;
                            if (($serviceDuration) >= 20) {
                                $pension = $high3Avg  * ($serviceDuration) * (1.1 / 100);
                            } else { // < 62
                                $pension = $high3Avg  * ($serviceDuration) * (1 / 100);
                            }

                            // $pension_sick = $high3Avg * $unusedSickLeaveYears * (1 / 100);
                            // $pension = $pension + $pension_sick;

                        } else { // < 62
                            // if ($serviceDuration > 20) {

                            $pension = $high3Avg  * ($serviceDuration) * (1 / 100);

                            // $pension_sick = $high3Avg * $unusedSickLeaveYears * (1 / 100);
                            // $pension = $pension + $pension_sick;

                            // } else {
                            //     $pension = 0;
                            // }
                        }
                    } elseif ($emp['empType'] == 'eCBPO') { // ENHANCED CUSTOMS AND BORDER PROTECTION OFFICERS (eCBPO)
                        $joinDate = new \DateTime($emp['eligibility']['LeaveSCD']);
                        $divDate = new \DateTime('06-07-2008 00:00:00');
                        $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
                        $firstPart = $joinDate->diff($divDate);
                        $secondPart = $divDate->diff($retDate);
                        $firstPart_year = $firstPart->y;
                        $firstPart_month = ($firstPart->m / 12);
                        $firstPart_day = ($firstPart->d / 365);
                        $secondPart_year = $secondPart->y;
                        $secondPart_month = ($secondPart->m / 12);
                        $secondPart_day = ($secondPart->d / 365);

                        $firstPart = $firstPart_year + $firstPart_month + $firstPart_day;
                        $secondPart = $secondPart_year + $secondPart_month + $secondPart_day; // + $unusedSickLeaveYears;

                        // echo $retirementAge;
                        // die;
                        if ($scenario > 1) { // Add years to LEO part of service
                            $secondPart = $secondPart + ($scenario - 1);
                        }

                        if ($retirementAge >= 62) {
                            $LEO_part_pension = $high3Avg * $secondPart * (1.7 / 100);
                            if (($serviceDuration) >= 20) {
                                $general_part_pension = $high3Avg  * ($firstPart) * (1.1 / 100);
                            } else { // < 62
                                $general_part_pension = $high3Avg  * $firstPart * (1 / 100);
                            }
                            $pension = $general_part_pension + $LEO_part_pension;
                        } else { // < 62
                            $LEO_part_pension = $high3Avg * $secondPart * (1.7 / 100);
                            $general_part_pension = $high3Avg  * $firstPart * (1 / 100);
                            $pension = $general_part_pension + $LEO_part_pension;
                        }
                    } else { // empType Other
                        if (($emp['otherEmpType'] == 'Air Traffic Controller') || ($emp['otherEmpType'] == 'Law Enforcement') || ($emp['otherEmpType'] == 'Firefighter')) {

                            $a = $high3Avg * 20 * (1.70 / 100);
                            $serviceDuration = round($serviceDuration, 2);
                            if ($serviceDuration > 20) {
                                $remaining_yrs_mos = $serviceDuration - 20;
                                $b = $high3Avg * $remaining_yrs_mos * (1.00 / 100);
                            } else {
                                $b = 0;
                            }
                            // $pension_for_sick_leaves = $high3Avg * $unusedSickLeaveYears * (1 / 100);

                            $pension = $a + $b; // + $pension_for_sick_leaves;
                        } else {
                            $pension = 0;
                        }
                    }
                }
                // elseif ($emp['retirementType'] == 'MRA+10') { // MRA+10
                //     if ($emp['empType'] == 'Regular') {
                //         $serviceDuration = $serviceDuration + $unusedSickLeaveYears;
                //         $pension = $high3Avg * ($serviceDuration / 100);
                //     } else {
                //         $pension = 0;
                //     }
                // }
                elseif ($emp['retirementType'] == 'Early-Out') { // Early Out
                    // echo $serviceDuration . "<br>";
                    // $serviceDuration = $serviceDuration; // + $unusedSickLeaveYears;
                    $serviceDuration = round($serviceDuration);
                    // echo $serviceDuration . "<br>";
                    // die;
                    if ($serviceDuration >= 25) {
                        $pension = $high3Avg * ($serviceDuration / 100);
                    } elseif (($serviceDuration >= 20) && ($retirementAge >= 50)) {
                        $pension = $high3Avg * ($serviceDuration * (1.10 / 100));
                    } else {
                        $pension = 0;
                    }
                } else { // Disability
                    $pension = 0;
                }
            } elseif (($emp['systemType'] == 'CSRS') || ($emp['systemType'] == 'CSRS Offset')) {
                if ($emp['retirementType'] == 'Regular') { // FULL
                    if ($emp['empType'] == 'Regular') {
                        // 41 years 11 months $maxServiceYearsAllowed as shared in example
                        $maxServiceYearsAllowed  =  41.91667;
                        if ($serviceDuration > $maxServiceYearsAllowed) {
                            $serviceDuration = $maxServiceYearsAllowed;
                        }
                        // dd($serviceDuration);
                        if ($serviceDuration > 5) {
                            $csrsA = $high3Avg * 5 * (1.50 / 100);
                            $remSer = ($serviceDuration - 5);
                            $csrsB = 0;
                            $csrsC = 0;
                            if ($remSer > 5) {
                                $csrsB = $high3Avg * 5 * (1.75 / 100);
                                $remSer = $remSer - 5;
                                // dd($remSer);
                                $csrsC = $high3Avg * $remSer * (2 / 100);
                                // dd($csrsC);
                            } else {
                                $csrsB = $high3Avg * $remSer * (1.75 / 100);
                            }
                            $pension_reg = $csrsA + $csrsB + $csrsC;
                        } else {
                            $pension_reg = $high3Avg * $serviceDuration * (1.50 / 100);
                        }
                        // pension for unused sick leave

                        // $pension_sick = $high3Avg * $unusedSickLeaveYears * 0.02;
                        $pension = $pension_reg; // + $pension_sick;
                        // dd($pension);
                    } else { // employeeType Other
                        // Firefighter, Law Enforcement
                        $a = $high3Avg * 20 * (2.50 / 100);
                        $b = 0;
                        if ($emp['otherEmpType'] == 'Air Traffic Controller') {
                            if ($serviceDuration > 20) {
                                $remSer = $serviceDuration - 20;
                                $b = $high3Avg * $serviceDuration * (1.75 / 100);
                            }
                        }
                        $pension = $a + $b;
                    }
                } elseif ($emp['retirementType'] == 'Early-Out') { // Early Out
                    // minimum 20 year of service
                    $a = $high3Avg * 5 * (1.50 / 100);
                    $b = $high3Avg * 5 * (1.75 / 100);
                    $remSer = $serviceDuration - 10;
                    $c = $high3Avg * $remSer * (2 / 100);
                    $pension = $a + $b + $c;
                } elseif ($emp['retirementType'] == 'Mandatory') {
                    $pension = 0;
                } else { // Disability
                    $pension = 0;
                }
            } else { // System type = Transfer or CSRS offset
                $pension = 0;
                if ($emp['FERSTransferDate'] != null) {
                    $fersTransferDate = new \DateTime($emp['FERSTransferDate']);
                    if ($emp['retirementType'] == 'Regular') { // FULL
                        if ($emp['empType'] == 'Regular') {
                            $joinDate = new \DateTime($emp['eligibility']['LeaveSCD']);
                            $csrsServiceInterval = $fersTransferDate->diff($joinDate);
                            $csrsServiceYears = $csrsServiceInterval->y;
                            $csrsServiceMonths = $csrsServiceInterval->m;
                            $csrsServicedays = $csrsServiceInterval->d;
                            $csrsMonthsPercentage = $csrsServiceMonths / 12;
                            $csrsDaysPercentage = $csrsServicedays / 365;
                            $csrsServiceDuration = $csrsServiceYears + $csrsMonthsPercentage + $csrsDaysPercentage;

                            $fersServiceInterval = $retDate->diff($fersTransferDate);
                            $fersServiceYears = $fersServiceInterval->y;
                            $fersServiceMonths = $fersServiceInterval->m;
                            $fersServicedays = $fersServiceInterval->d;
                            $fersMonthsPercentage = ($fersServiceMonths / 12);
                            $fersDaysPercentage = ($fersServicedays / 365);
                            $fersServiceDuration = $fersServiceYears + $fersMonthsPercentage + $fersDaysPercentage;

                            // $unusedSickLeaveHrs = $emp['UnusedSickLeave'];
                            // if ($unusedSickLeaveHrs > 0) {
                            //     $unusedSickLeaveYears = $unusedSickLeaveHrs / 2087;
                            // } else {
                            //     $unusedSickLeaveYears = 0;
                            // }

                            if ($csrsServiceDuration > 5) {
                                // echo 'csrsServ'.$csrsServiceDuration."<br>";
                                $csrsA = $high3Avg * 5 * (1.50 / 100);
                                $remSer = $csrsServiceDuration - 5;
                                // echo "remDura".$remSer."<br>";
                                $csrsB = 0;
                                $csrsC = 0;
                                if ($remSer > 5) {
                                    $csrsB = $high3Avg * 5 * (1.75 / 100);
                                    $remSer = $remSer - 5;
                                    $csrsC = $high3Avg * $remSer * (2 / 100);
                                } else {
                                    $csrsB = $high3Avg * $remSer * (1.75 / 100);
                                }
                                $csrsPension = $csrsA + $csrsB + $csrsC;
                            } else {
                                $csrsPension = $high3Avg * $csrsServiceDuration * (1.50 / 100);
                            }

                            if (round($retirementAge) >= 62) {
                                $fersPension = $high3Avg * $fersServiceDuration * (1.1 / 100);
                                // $sickLeavePension = $high3Avg * $unusedSickLeaveYears * (1.1 / 100);
                            } else {
                                $fersPension = $high3Avg * $fersServiceDuration * (1 / 100);
                                // $sickLeavePension = $high3Avg * $unusedSickLeaveYears * (1 / 100);
                            }

                            $pension = $csrsPension + $fersPension; // + $sickLeavePension;
                        }
                    }
                } else { // CSRSOffsetDate
                    $pension = 0;
                }
            }

            if ($emp['retirementType'] == 'Deferred') {
                $pension = $high3Avg * ($serviceDuration / 100);
            }
        }
        $partTimeWorkPercentage = $serviceDurationArr['validInvalidServices']['partTimeServiceDuration']['work_percentage'];
        // dd($partTimeWorkPercentage);
        $pension = ($pension * ($partTimeWorkPercentage / 100));
        // dd($pension);
        return $pension;
    }

    public function getMRAPenalty($empId = null, $scenario = 1)
    {
        $mraPanelty = 0;
        $result['first_pension_mra10_penalty'] = 0;
        $result['monthsUnder62'] = 0;
        $result['multiplier'] = 0;
        if ($empId != null) {
            $emp = $this->getById($empId)->toArray();
            if (!empty($emp) && $emp['systemType'] == 'FERS') {
                if ($emp['retirementType'] == 'Regular') {
                    if (($emp['empType'] == 'Regular') || ($emp['empType'] == 'eCBPO')) {
                        $bday = new \DateTime($emp['eligibility']['DateOfBirth']);

                        // $bday = $bday->modify('- 1 month'); // The government considers someone to have turned their new age on the day before their actual birthday.

                        $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
                        if ($scenario > 1) {
                            $retDate = $retDate->modify('+' . ($scenario - 1) . ' years');
                        }
                        $retInterval = $retDate->diff($bday);
                        $retirementAgeY = (int) $retInterval->y;
                        $retirementAgeM = (int) $retInterval->m;
                        $data = $this->getEarliestRetirement($empId, $scenario);



                        $serviceDuration = round($data['serviceDurationForPension']);



                        $mrAgeArr = $this->getMinumumRetirementAge($empId, $scenario);
                        // echo $mrAgeArr['minRetirementDate'];
                        // die;
                        $minRetDate = new \DateTime($mrAgeArr['minRetirementDate']);
                        $is_eligible_ret = 0;
                        $minRetDate = $minRetDate->modify('-1 day'); // The government considers someone to have turned their new age on the day before their actual birthday.  How can we reflect that in the calculations?

                        if ($retirementAgeY >= 62 && $serviceDuration >= 5) {
                            $is_eligible_ret = 1;
                        } elseif ($retirementAgeY >= 60 && $serviceDuration >= 20) {
                            $is_eligible_ret = 1;
                        } elseif ($retDate >= $minRetDate && $serviceDuration >= 30) {
                            $is_eligible_ret = 1;
                        }
                        // if ($scenario == 1) {  // debugging
                        //     echo $retirementAgeY . " ----- " . $serviceDuration . " -- " . $is_eligible_ret;
                        //     die;
                        // }


                        if ($is_eligible_ret == 0) {

                            // if (($serviceDuration >= 10) && ($serviceDuration < 30)) {

                            $monthsUnder62 = 0;
                            if ($retirementAgeY < 62) {
                                $age62 = $bday->modify('+ 62 years');
                                $ret_and_62_diff = $retDate->diff($age62);
                                $monthsUnder62 = ($ret_and_62_diff->y * 12) + $ret_and_62_diff->m;
                            }
                            $pension = $this->getFirstPension($empId, $scenario);
                            $mraPanelty = $monthsUnder62 * 0.00416667 * $pension;
                            $result['first_pension_mra10_penalty'] = $mraPanelty;
                            $result['monthsUnder62'] = $monthsUnder62;
                            $result['multiplier'] = 0.00416667;
                            // }
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function getEarlyOutPenalty($empId = null, $scenario = 1)
    {
        $earlyOutPenalty = 0;
        if ($empId != null) {
            $emp = $this->getById($empId)->toArray();
            if ($emp['systemType'] == 'CSRS') {
                if ($emp['retirementType'] == 'Early-Out') { // EARLY OUT
                    $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
                    $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
                    $retInterval = $retDate->diff($bday);
                    $retirementAgeY = $retInterval->y;
                    $retirementAgeM = $retInterval->m;
                    $restMonths = ($retirementAgeM > 0) ? (12 - $retirementAgeM) : 0;
                    $retirementAgeY = $retirementAgeY + ($scenario - 1);
                    $monthsUnder55 = 0;
                    if ($retirementAgeY < 55) {
                        $yearsRem = 55 - $retirementAgeY;
                        $monthsRem = $yearsRem * 12;
                        $monthsUnder55 = $monthsRem + $restMonths;
                    }
                    $pension = $this->getFirstPension($empId, $scenario);
                    $earlyOutPenalty = $monthsUnder55 * .00416667 * $pension;
                }
            } elseif ($emp['systemType'] == 'FERS') {
                $earlyOutPenalty = 0;
            }
        }
        return $earlyOutPenalty;
    }

    public function getCsrsOffsetPenalty($empId = null, $scenario = 1)
    {
        $penalty = 0;
        if ($empId != null) {
            $employee = $this->getById($empId);
            if (is_null($employee)) {
                return redirect()->back()->with([
                    'status' => 'danger',
                    'message' => 'Invalid Employee'
                ]);
            }
            $emp = $employee->toArray();
            // dd($emp);
            if (($emp['systemType'] == 'CSRS') || ($emp['systemType'] == 'CSRS Offset')) {
                $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
                $now = new \DateTime();
                $ageObj = $bday->diff($now);
                $current_age = $ageObj->y;
                $age = $current_age + ($scenario - 1);

                $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
                if ($scenario > 1) {
                    $years = $scenario - 1;
                    $retDate->modify('+' . $years . 'years');
                }
                $retAgeObj = $bday->diff($retDate);
                $retAge = $retAgeObj->y;
                $retAge = $retAge + ($scenario - 1);

                $csrsOffsetDate = new \DateTime($emp['CSRSOffsetDate']);
                $csrsOffsetInterval = $retDate->diff($csrsOffsetDate);
                $csrsOffsetDuration = $csrsOffsetInterval->y;
                $mnth = $csrsOffsetInterval->m / 12;
                $csrsOffsetDuration = round($csrsOffsetDuration + $mnth);
                // dd($csrsOffsetDuration);
                if ($emp['CSRSOffsetDate'] != null) {
                    if (($emp['systemType'] == 'CSRS Offset') && ($emp['SSAtAgeOfRetirement'] > 0)) {
                        $pia_formula_bend = EmployeeConfig::where('EmployeeId', $empId)->where('ConfigType', 'PIAFormula')->first();
                        if (!$pia_formula_bend) {
                            $pia_formula_bend = AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', 'PIAFormula')->first()->AppLookupDescription;
                        } else {
                            $pia_formula_bend = $pia_formula_bend->ConfigValue;
                        }
                        $wep_panelty = $this->getWepPenalty($employee, $pia_formula_bend);
                        $ss_amount = $emp['SSAtAgeOfRetirement'] - $wep_panelty;
                        $penalty = $ss_amount * ($csrsOffsetDuration / 40);
                        // dd($penalty);
                    } else {
                        $penalty = $emp['SSMonthlyAt62'] * ($csrsOffsetDuration / 40);
                    }
                }
            }
        }
        // dd($csrsOffsetDuration);
        return $penalty;
    }

    public function getPensionDetailsPdf($empId)
    {
        $retDetails = [];
        $firstPension = $this->getFirstPension($empId);
        // echo "<pre>";
        // print_r($firstPension / 12);
        // die;
        $mraPenalty_arr = $this->getMRAPenalty($empId);

        $mraPenalty = $mraPenalty_arr['first_pension_mra10_penalty'];
        $monthlyMraPenalty = $mraPenalty / 12;
        $earlyOutPenalty = $this->getEarlyOutPenalty($empId);
        $monthlyEarlyOutPenalty = $earlyOutPenalty / 12;
        // $netPension = $firstPension - ($mraPenalty + $earlyOutPenalty);

        // echo $mraPenalty . "-----" . $earlyOutPenalty; die;
        $nonDeductionPanelty = $this->nonDeductionPanelty($empId);
        $refundedPanelty = $this->calcRefundedPanelty($empId);
        // echo "<pre>"; print_r($nonDeductionPanelty); exit;

        $serviceDurationArr = $this->getEarliestRetirement($empId);
        $validInvalidServices = $serviceDurationArr['validInvalidServices'];
        unset($serviceDurationArr['validInvalidServices']);

        $serviceDuration = $serviceDurationArr['serviceDurationForPension'];

        $minRetirementDate = new \DateTime(date('Y-m-d H:i:s', strtotime($serviceDurationArr['minRetirementDate'])));

        $emp = $this->getById($empId)->toArray();
        $empConf = $this->getEmployeeConf($empId);

        $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
        $bday = $bday->modify('- 1 day'); // The government considers someone to have turned their new age on the day before their actual birthday.
        $minRetAge = $this->getMinumumRetirementAge($empId, 1);
        $mraObj = new \DateTime($minRetAge['minRetirementDate']);
        $mra_interval = $mraObj->diff($bday);
        $mra_y = $mra_interval->y;
        $mra_m = $mra_interval->m;

        // *******************************************************************************

        // echo "<pre>";
        // print_r($serviceDurationArr);
        // exit;
        if ($emp['systemType'] == 'FERS') {
            $cola = $empConf['FERSCola'];
            if (($emp['empType'] == 'Regular') || ($emp['empType'] == 'eCBPO')) {
                $eligibilityForFullRetirement[0]['age'] = 62;
                $eligibilityForFullRetirement[0]['service'] = 5;
                $eligibilityForFullRetirement[1]['age'] = 60;
                $eligibilityForFullRetirement[1]['service'] = 20;
                $eligibilityForFullRetirement[2]['age'] = 'MRA (55-57)';
                $eligibilityForFullRetirement[2]['service'] = 30;
            } else {
                $eligibilityForFullRetirement[0]['age'] = 50;
                $eligibilityForFullRetirement[0]['service'] = 20;
                $eligibilityForFullRetirement[1]['age'] = 'ANY';
                $eligibilityForFullRetirement[1]['service'] = 25;
            }
        } else {
            $cola = $empConf['CSRSCola'];
            if ($emp['empType'] == 'Regular') {
                $eligibilityForFullRetirement[0]['age'] = 62;
                $eligibilityForFullRetirement[0]['service'] = 5;
                $eligibilityForFullRetirement[1]['age'] = 60;
                $eligibilityForFullRetirement[1]['service'] = 20;
                $eligibilityForFullRetirement[2]['age'] = 55;
                $eligibilityForFullRetirement[2]['service'] = 30;
            } else {
                if (($emp['otherEmpType'] == 'Firefighter') || ($emp['otherEmpType'] == 'Law Enforcement')) {
                    $eligibilityForFullRetirement[0]['age'] = 50;
                    $eligibilityForFullRetirement[0]['service'] = 20;
                } else {
                    $eligibilityForFullRetirement[0]['age'] = 50;
                    $eligibilityForFullRetirement[0]['service'] = 20;
                    $eligibilityForFullRetirement[1]['age'] = 'ANY';
                    $eligibilityForFullRetirement[1]['service'] = 25;
                }
            }
        }
        // echo "<pre>";
        // print_r($emp);
        // exit;
        $emp_age = $serviceDurationArr['emp_age'];
        $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
        $retInterval = $retDate->diff($bday);
        $retirementAge = $retInterval->y;
        $retirementAgeM = $retInterval->m;

        // echo $retInterval->y . " ---- " . $retInterval->m;
        // die;
        $joining = new \DateTime($emp['eligibility']['LeaveSCD']);
        $service = $joining->diff($retDate);

        $now = new \DateTime();
        $creditableService = $joining->diff($now);

        $high3Arr = $this->calcProjectedHigh3Average($empId, 1);
        $high3Avg = $high3Arr['projectedHigh3Avg'];

        $csrsOffsetPenalty = $this->getCsrsOffsetPenalty($empId);

        // echo $csrsOffsetPenalty;
        // die;

        // echo  "In Pension detail: Your Federal Pension: " . $firstPension / 12 . " ------ ";
        $years_in_retirement = 0;
        if ((($emp['systemType'] == 'CSRS') || ($emp['systemType'] == 'CSRS Offset')) && ($retirementAge >= 62)) {
            $firstPension = $firstPension - ($csrsOffsetPenalty * 12);
        }
        // echo  $csrsOffsetPenalty . " ------ " . $firstPension / 12;
        // die;

        // echo $emp['empType'] . "------" . $emp['systemType'];
        // die;
        for ($i = $retirementAge; $i <= 90; $i++) {
            if ($emp['systemType'] == 'FERS') {
                if ($emp['empType'] == "Other") {
                    if ($i > $retirementAge) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                        // $netPension = $netPension + ($netPension * ($cola / 100));
                    }
                } else {
                    if (($i >= 62) && ($i > $retirementAge)) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100));
                        // $netPension = $netPension + ($netPension * ($cola / 100));
                    }
                }
                // if ($i <= 62) {
                // $monthlyMraPenalty = ($firstPension / 12) * 20 / 100;
                $mra10_penalty = $mraPenalty_arr['monthsUnder62'] * $mraPenalty_arr['multiplier'] * $firstPension;
                $monthlyMraPenalty = $mra10_penalty / 12;
                $row['monthlyEarlyOutPenalty'] = $monthlyMraPenalty;
                // } else {
                //     $row['monthlyEarlyOutPenalty'] = 0;
                // }
                $monthlygrossPension = $firstPension / 12;
            } else { // CSRS Apply COLA immediatly after retirement
                if ($i > $retirementAge) {
                    $firstPension = $firstPension + ($firstPension * ($cola / 100));
                    // $netPension = $netPension + ($netPension * ($cola / 100));
                }
                // if ($i <= 55) {
                $row['monthlyEarlyOutPenalty'] = $monthlyEarlyOutPenalty;

                if ($i == 62) {
                    $monthlygrossPension = ($firstPension / 12) - $csrsOffsetPenalty;
                    $firstPension = $monthlygrossPension * 12;
                } else {
                    $monthlygrossPension = $firstPension / 12;
                }
            }
            // dd($emp);
            if ($emp['RetirementType'] == "Deferred") {
                $monthlygrossPension = 0;
                if ($serviceDuration >= 30) {
                    if (($i >= $mra_y) && ($i <= 62)) {
                        $monthlygrossPension = $firstPension / 12;
                    } elseif ($i > $mra_y) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                        $monthlygrossPension = $firstPension / 12;
                    }
                } elseif ($serviceDuration >= 20) {
                    if (($i >= 60) && ($i <= 62)) {
                        $monthlygrossPension = $firstPension / 12;
                    } elseif ($i >= 62) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                        $monthlygrossPension = $firstPension / 12;
                    }
                } elseif (($serviceDuration >= 5) || ($serviceDuration <= 19)) {
                    if ($i == 62) {
                        $monthlygrossPension = $firstPension / 12;
                    } elseif ($i > 62) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                        $monthlygrossPension = $firstPension / 12;
                    }
                }
            }

            $monthlyNetPension = $monthlygrossPension - $row['monthlyEarlyOutPenalty'];
            $row['years_in_retirement'] = $years_in_retirement++;
            $row['age'] = $i;

            $row['monthly_pension_gross'] = $monthlygrossPension;

            $row['monthlyDepositPenalty'] = ($nonDeductionPanelty / 12);
            $row['monthlyRedepositPenalty'] = ($refundedPanelty / 12);
            $row['net_monthly_pension'] = $monthlyNetPension - (($nonDeductionPanelty / 12) + ($refundedPanelty / 12));

            array_push($retDetails, $row);
        }

        $pensionData['retDetails'] = $retDetails;
        $pensionData['cola'] = $cola;
        $pensionData['eligibilityForFullRetirement'] = $eligibilityForFullRetirement;
        $pensionData['systemType'] = $emp['systemType'];
        $pensionData['MRA'] = $mra_y;
        $pensionData['MRA_mnth'] = $mra_m;
        $pensionData['bdayYear'] = date('Y', strtotime($emp['eligibility']['DateOfBirth']));
        $pensionData['emp_age'] = $emp_age;
        $pensionData['retirement_date'] = date('m/d/Y', strtotime($emp['eligibility']['RetirementDate']));
        $pensionData['serviceYears'] = $service->y;
        $pensionData['serviceMonths'] = $service->m;
        $pensionData['retirementAgeY'] = $retirementAge;
        $pensionData['retirementAgeM'] = $retirementAgeM;
        $pensionData['validInvalidServices'] = $validInvalidServices;
        $pensionData['creditableServiceYears'] = $creditableService->y;
        $pensionData['creditableServiceMonths'] = $creditableService->m;

        return $pensionData;
    }

    public function searchCases($caseId = null, $empName = null, $advisorName = null)
    {
        $advisors = [];
        if ($advisorName) {
            $advisors = Advisor::where('AdvisorName', 'LIKE', "%$advisorName%")->pluck('AdvisorId')->toArray();
        }
        if (empty($advisors)) {
            if (($caseId == null) && ($empName == null)) {
                return [];
            }
        }
        // echo "<pre>"; print_r($advisors); exit;
        $listing = Employee::with('advisor')->when($caseId, function ($q) use ($caseId) {
            return $q->where('EmployeeId', $caseId);
        })
            ->when($empName, function ($q) use ($empName) {
                return $q->where('EmployeeName', 'LIKE', '%' . $empName . '%');
            })
            ->when(!!$advisors, function ($q) use ($advisors) {
                return $q->whereIn('AdvisorId', $advisors);
            })
            ->where('IsActive', 1)
            ->orderBy('EmployeeId', 'desc')->get();

        return $listing;
    }

    public function getDesclaimerById($disclaimerId = null)
    {
        $disclaimer = Disclaimer::where('DisclaimerId', $disclaimerId)->first();
        if (is_null($disclaimer)) {
            return [];
        } else {
            return $disclaimer->toArray();
        }
    }

    public function calcAndDebugAnnuity($empId = null, $scenario = 1)
    { // Calculation and debug page pension
        $retDetails = [];
        $firstPension = $this->getFirstPension($empId, $scenario);
        $mraPenalty_arr = $this->getMRAPenalty($empId, $scenario);
        $mraPenalty = $mraPenalty_arr['first_pension_mra10_penalty'];
        $earlyOutPenalty = $this->getEarlyOutPenalty($empId, $scenario); // yearly
        $nonDeductionPanelty = $this->nonDeductionPanelty($empId);
        $refundedPanelty = $this->calcRefundedPanelty($empId);
        // echo "<pre>"; print_r($refundedPanelty); exit;
        $netPension = $firstPension - ($mraPenalty + $earlyOutPenalty + $nonDeductionPanelty + $refundedPanelty);

        $emp = $this->getById($empId);
        if (is_null($emp)) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Invalid Employee'
            ]);
        }
        $emp = $emp->toArray();
        $empConf = $this->getEmployeeConf($empId);

        $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
        $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
        $retInterval = $retDate->diff($bday);
        $retirementAge = $retInterval->y + ($scenario - 1);

        $csrsOffsetPenalty = round($this->getCsrsOffsetPenalty($empId));
        // echo $netPension; exit;
        $reportDate = new \DateTime($emp['ReportDate']);
        $ageAtReportInterval = $bday->diff($reportDate);
        $ageAtReportDate = $ageAtReportInterval->y;

        $reportYear = date('Y', strtotime($emp['ReportDate']));
        $years_in_retirement = 0;

        if ($emp['systemType'] == 'FERS') {
            $cola = $empConf['FERSCola'];
        } else {
            $cola = $empConf['CSRSCola'];
        }
        if (($emp['systemType'] == 'CSRS') && ($retirementAge >= 62)) {
            $netPension = $netPension - $csrsOffsetPenalty;
        }
        if ($reportYear >= date('Y', strtotime($emp['eligibility']['RetirementDate']))) {
            $pensionY = $netPension;
        } else {
            $pensionY = 0;
        }
        for ($i = $ageAtReportDate; $i <= 90; $i++) {
            if ($i >= $retirementAge) {
                if ($emp['systemType'] == 'FERS') {
                    if (($i >= 62) && ($i > $retirementAge)) {
                        $netPension = $netPension + ($netPension * ($cola / 100));
                    }
                } else { // CSRS Apply COLA immediatly after retirement
                    if ($i == 62) {
                        $netPension = $netPension - $csrsOffsetPenalty;
                    }
                    if ($i > $retirementAge) {
                        $netPension = $netPension + ($netPension * ($cola / 100)); //
                    }
                }
                $pensionY = $netPension;
            }

            $row['reportYear'] = $reportYear++;
            $row['age'] = $i;
            $row['yearlyPension'] = $pensionY;
            $row['earlyOutPenalty'] = $earlyOutPenalty;
            $row['refundPenalty'] = 0;
            $row['nonDeductPenalty'] = 0;
            $row['annualAnnuityNoSurvival'] = 0;
            $row['annualWithSurvivor'] = 0;
            $row['annualSurvivorBenifits'] = 0;
            $row['annualDifference'] = 0;
            $row['monthlyPension'] = ($pensionY / 12);
            $row['monthlyWithSurvivor'] = 0;
            $row['monthlySurvivorBenifits'] = 0;
            $row['monthlyDifference'] = 0;
            $row['annualAccumulatedDifference'] = 0;

            array_push($retDetails, $row);
        }
        return $retDetails;
    }

    public function calcCatch62Panelty($empId = null)
    {
        /* Penalty applies if employee WILL have at least 40 Social Security credits by age 62 Prior to age 62, military time is INCLUDED in the pension calculation At age 62 and beyond, military time is REMOVED from the pension calculation */

        // $emp = $this->getById($empId);
        // if (is_null($emp)) {
        //     return redirect()->back()->with([
        //         'status' => 'danger',
        //         'message' => 'Invalid Employee'
        //     ]);
        // }
        // $emp = $emp->toArray();
        // $empConf = $this->getEmployeeConf($empId);


        // if (count($emp['military_service']) > 0) {
        //     if ($emp['systemType'] == 'FERS') {
        //         return 0;
        //     }
        //     $mbeforeDate = new \DateTime(date('Y-m-d H:i:s', strtotime('10-1-1957')));
        //     // compare this date with "when first hired under CSRS". Wait for video to explain this
        //     $totalCatch62Panelty = 0;
        //     foreach ($emp['military_service'] as $mservice) {
        //         $militaryServiceStart = new \DateTime($mservice['FromDate']);
        //         if ($militaryServiceStart < $mbeforeDate) { // before given date
        //             if ($mservice['DepositOwed'] == 1) {
        //                 $catchPanelty = 0;
        //             }
        //         }
        //     }
        // }
        return 0;
    }

    public function nonDeductionPanelty($empId = null)
    { // OR Deposit Penalty
        $emp = $this->getById($empId);
        if (is_null($emp)) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Invalid Employee'
            ]);
        }
        $emp = $emp->toArray();
        // echo "<pre>"; print_r($emp); exit;
        $totalNDPAnelty = 0;
        if (count($emp['non_deduction_service']) > 0) {
            if ($emp['systemType'] == 'CSRS') {
                $ndBeforeDate = new \DateTime(date('Y-m-d H:i:s', strtotime('10-1-1982')));
                foreach ($emp['non_deduction_service'] as $ndService) {

                    $ndServiceStart = new \DateTime($ndService['FromDate']);
                    $endNdService = new \DateTime($ndService['ToDate']);
                    if ($ndServiceStart < $ndBeforeDate) {
                        if ($ndService['DepositOwed'] == 0) { // not Paid 1
                            $NDpanelty = $ndService['AmountOwed'] * (10 / 100);
                            $totalNDPAnelty = $totalNDPAnelty + $NDpanelty;
                        }
                    }
                }
            }
        }
        return $totalNDPAnelty;
    }

    public function calcRefundedPanelty($empId = null)
    { // Acturial Reduction Penalty
        // OR Redeposit Penalty
        // Amount owed  / Present Value Factor  x  12 months = annual reduction to pension
        $emp = $this->getById($empId);
        if (is_null($emp)) {
            return redirect()->back()->with([
                'status' => 'danger',
                'message' => 'Invalid Employee'
            ]);
        }
        $emp = $emp->toArray();
        // echo "<pre>"; print_r($emp); exit;
        $totalRefundedPanelty = 0;
        if (count($emp['refunded_service']) > 0) {
            if ($emp['systemType'] == 'CSRS') {
                $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
                $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
                $retAgeInterval = $bday->diff($retDate);
                $retAge = $retAgeInterval->y;
                $pvf = config('constants.PVF')[$retAge] ?? 0;

                $rBeforeDate = new \DateTime(date('Y-m-d H:i:s', strtotime('3-1-1991')));
                foreach ($emp['refunded_service'] as $rService) {
                    $rServiceStart = new \DateTime($rService['FromDate']);
                    $endRService = new \DateTime($rService['ToDate']);
                    if ($endRService < $rBeforeDate) {
                        if ($rService['Redeposit'] == 1) { // Not Paid
                            $refundPenalty = ($rService['AmountOwed'] / $pvf) * 12;
                            $totalRefundedPanelty = $totalRefundedPanelty + $refundPenalty;
                        }
                    }
                }
            }
        }
        return $totalRefundedPanelty;
    }

    public function getEmpNameById($empId = null)
    {
        $name = Employee::where('EmployeeId', $empId)->select('EmployeeName')->first();
        if (is_null($name)) {
            return "";
        } else {
            return $name->EmployeeName;
        }
    }

    public function getPartTimeServiceDuration($empId = null)
    {
        $ptServices = PartTimeService::where('EmployeeId', $empId)->get()->toArray();
        if (is_null($ptServices)) {
            return [
                'work_percentage' => 100
            ];
        }
        $emp = $this->getById($empId);
        // dd($emp->toArray());
        $totalDuration = 0;
        $maxDuration = 0; // Q1
        $partTimePercentage = 0;
        $ptDurationYears = 0;
        foreach ($ptServices as $ser) {
            if ($ser['percentage'] == 0) {
                $from = new \DateTime($ser['FromDate']);
                $to = new \DateTime($ser['ToDate']);
                $durationInterval = $from->diff($to);
                $duration_y = $durationInterval->y;
                $duration_m = $durationInterval->m;
                $duration_d = $durationInterval->d;
                $duration = round(($duration_y) + ($duration_m / 12) + ($duration_d / 365));

                $ptDurationYears = $ptDurationYears + $duration;
                $durationHrs = $duration * 2087 * ($ser['HoursWeek'] / 40);
                $totalDuration = $totalDuration + $durationHrs;
            } else {
                $partTimePercentage = $ser['percentage'];
                break;
            }
        }

        if ($partTimePercentage > 0) {
            return [
                'work_percentage' => $partTimePercentage
            ];
        } else {
            if ($totalDuration > 0) {
                $serviceStart = new \DateTime($emp['eligibility']['LeaveSCD']);
                $serviceRetire = new \DateTime($emp['eligibility']['RetirementDate']);
                $fullDuration = $serviceRetire->diff($serviceStart);
                $full_y = $fullDuration->y;
                $full_m = $fullDuration->m;
                $full_d = $fullDuration->d;
                $fullDuration = ($full_y) + ($full_m / 12) + ($full_d / 365);
                $netFullWorkingYears = $fullDuration - $ptDurationYears;
                $netFullWorkingHrs = $netFullWorkingYears * 2087;

                $fullDurationHrs = ($fullDuration * 2087) - $emp['UnusedSickLeave']; //as need to divide working hrs with total hrs * 40 / 40);

                $workPercentage = (($totalDuration + $netFullWorkingHrs) / $fullDurationHrs) * 100;
                return [
                    'work_percentage' => $workPercentage
                ];
            } else {
                return [
                    'work_percentage' => 100
                ];
            }
        }
    }

    public function getPartTimeServiceDuration_ByDates($empId = null)
    {
        $ptServices = PartTimeService::where('EmployeeId', $empId)->get()->toArray();
        if (is_null($ptServices)) {
            return 0;
        }
        $emp = $this->getById($empId);
        $totalDuration = 0;
        $maxDuration = 0; // Q1
        foreach ($ptServices as $ser) {
            if ($ser['percentage'] == 0) {
                $hrsPerWeek = $ser['HoursWeek'];
                $from = new \DateTime($ser['FromDate']);
                $to = new \DateTime($ser['ToDate']);
                $durationInterval = $from->diff($to);
                $duration_y = $durationInterval->y;
                $duration_m = $durationInterval->m;
                $duration_d = $durationInterval->d;
                // $durationHours = $duration * $hrsPerWeek * 52; // 52 = 26 * 2
                $duration = ($duration_y * 2087) + (($duration_m / 12) * 2087) + (($duration_d / 365) * 2087);
                $durationHours = $duration * ($hrsPerWeek / 40);
                // $durationHours = $duration_y * 2087 * ($hrsPerWeek / 40);
                $totalDuration = $totalDuration + $durationHours;
                $maxDuration = $maxDuration + ($duration_y * 2087) + (($duration_m / 12) * 2087) + (($duration_d / 365) * 2087);
            }
        }
        return $data = [
            'totalDuration' => $totalDuration,
            'maxDuration' => $maxDuration
        ];
    }

    public function getEmpInfoById($empId = null)
    {
        $emp = Employee::with(['advisor', 'eligibility'])->where('EmployeeId', $empId)->first();
        if (is_null($emp)) {
            return [];
        } else {
            $SystemType = AppLookup::where('AppLookupId', $emp->SystemTypeId)->first();
            if ($SystemType) {
                $SystemType = $SystemType->AppLookupName;
            } else {
                $SystemType = '';
            }

            $adv_name = explode(' ', $emp->advisor->AdvisorName ?? '');
            $abbr = "";
            foreach ($adv_name as $name) {
                $name_arr = str_split($name);
                $abbr .= $name_arr[0];
            }

            $now = new \DateTime();
            $o_dob = new \DateTime($emp->eligibility->DateOfBirth ?? '');
            $o_ret = new \DateTime($emp->eligibility->RetirementDate ?? '');
            $interval_dob = $o_dob->diff($now);
            $interval_ret = $o_dob->diff($o_ret);
            $data['current_age'] = '(' . $interval_dob->y . 'y ' . $interval_dob->m . 'm)';
            $data['ret_age'] = '(' . $interval_ret->y . 'y ' . $interval_ret->m . 'm)';

            // eligibility.DateOfBirth', 'SystemTypeId', 'eligibility.RetirementDate', 'advisor.AdvisorName
            $data['dob'] = date('m/d/Y', strtotime($emp->eligibility->DateOfBirth ?? ''));
            $data['SystemType'] = $SystemType;
            $data['retirement_date'] = date('m/d/Y', strtotime($emp->eligibility->RetirementDate ?? ''));
            $data['advisor_name'] = $abbr;
            $data['workshop_code'] = $emp->eligibility->workshop_code ?? '';
            return $data;
        }
    }

    public function getFullRetirementAge($birth_year = null)
    {
        if ($birth_year == null) {
            return false;
        }
        $fra_year = 0;
        $fra_month = 0;
        if ($birth_year <= 1937) {
            $fra_year = 65;
            $fra_month = 0;
        } elseif ($birth_year == 1938) {
            $fra_year = 65;
            $fra_month = 2;
        } elseif ($birth_year == 1939) {
            $fra_year = 65;
            $fra_month = 4;
        } elseif ($birth_year == 1940) {
            $fra_year = 65;
            $fra_month = 6;
        } elseif ($birth_year == 1941) {
            $fra_year = 65;
            $fra_month = 8;
        } elseif ($birth_year == 1942) {
            $fra_year = 65;
            $fra_month = 10;
        } elseif ($birth_year >= 1943 && $birth_year <= 1954) {
            $fra_year = 66;
            $fra_month = 0;
        } elseif ($birth_year == 1955) {
            $fra_year = 66;
            $fra_month = 2;
        } elseif ($birth_year == 1956) {
            $fra_year = 66;
            $fra_month = 4;
        } elseif ($birth_year == 1957) {
            $fra_year = 66;
            $fra_month = 6;
        } elseif ($birth_year == 1958) {
            $fra_year = 66;
            $fra_month = 8;
        } elseif ($birth_year == 1959) {
            $fra_year = 66;
            $fra_month = 10;
        } elseif ($birth_year >= 1960) {
            $fra_year = 67;
            $fra_month = 0;
        }
        $fra = [
            'fra_year' => $fra_year,
            'fra_month' => $fra_month
        ];
        return $fra;
    }

    // public function createEmployeeScenarios($empId = null)
    // {
    //     // empId must be valid and eligibility should be set already. Not applying check here.
    //     $employee = $this->getById($empId)->toArray();
    //     // echo "<pre>"; print_r($employee); exit;
    //     // save 5 scenaios in loop
    //     // $te = [];
    //     for ($s = 1; $s <= 5; $s++) {
    //         $data = [];
    //         $data['EmployeeId'] = $employee['EmployeeId'];
    //         $data['ScenarioNo'] = $s;
    //         $rt_date = $employee['eligibility']['RetirementDate'];
    //         if ($s > 1) {
    //             $rt = new \DateTime($employee['eligibility']['RetirementDate']);
    //             $rt->modify('+1 year');
    //             $rt_date = $rt->format('Y-m-d H:i:s');
    //         }
    //         $data['RetirementDate'] = $rt_date;
    //         $high3 = $this->calcProjectedHigh3Average($empId, $s);
    //         $data['High3Average'] = $high3['projectedHigh3Avg'];
    //         $annuity_data = $this->calcDebugAnnuityData($empId, $s);
    //         $data['AnnuityBeforeDeduction'] = $annuity_data['scenarioData']['annuity_before_deduction'];  // MY_DOUBT

    //         $data['SurvivorAnnuity'] = 0.00; // MY_DOUBT
    //         $data['SurvivorAnnuityCost'] = 0.00;  // MY_DOUBT
    //         $data['PartTimeMultiplier'] = 1.00;  // MY_DOUBT
    //         $data['MRA10Multiplier'] = 1.00;  // MY_DOUBT
    //         $data['Annuity'] = 0.00;
    //         if ($s > 1) {
    //             $data['IsSelected'] = 0;
    //         } else {
    //             $data['IsSelected'] = 1;
    //         }
    //         // $data['CSRSServiceAtRetirement'] = "";
    //         // $data['FERSServiceAtRetirement'] = "";
    //         $result = ReportScenario::create($data);
    //         if ($result) {
    //             continue;
    //         } else {
    //             break;
    //         }
    //         // $te[] = $data;
    //     }


    //     if ($result) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    public function getDistributionForLfund($fund_type = null, $lfund_amount = 0.00, $statement_date = null, $tsp_conf = [])
    {
        $dt_Obj = new \DateTime($statement_date);
        $statement_date = $dt_Obj->format('Y-m-d');

        $lDists = LfundsDist::where('date', '<=', $statement_date)
            ->where('lfund_type', $fund_type)
            ->orderBy('date', 'DESC')
            ->first();
        if (!$lDists) {
            $lDists = LfundsDist::where('date', '<=', $statement_date)
                ->where('lfund_type', 'LIncome')
                ->orderBy('date', 'DESC')
                ->first();
        }
        if (!$lDists) {
            $lDists = LfundsDist::where('date', '>=', $statement_date)
                ->where('lfund_type', 'LIncome')
                ->orderBy('date', 'ASC')
                ->first();
        }
        $data = [];
        // echo "<pre>"; print_r($statement_date); exit;

        $data['gfund_dist'] = $lfund_amount * ($lDists['gfund'] / 100);
        $data['ffund_dist'] = $lfund_amount * ($lDists['ffund'] / 100);
        $data['cfund_dist'] = $lfund_amount * ($lDists['cfund'] / 100);
        $data['sfund_dist'] = $lfund_amount * ($lDists['sfund'] / 100);
        $data['ifund_dist'] = $lfund_amount * ($lDists['ifund'] / 100);

        // Appply rate of return
        $data['gfund_dist'] = ($data['gfund_dist'] * ($tsp_conf['gfund'] / 12)) + $data['gfund_dist'];
        $data['ffund_dist'] = ($data['ffund_dist'] * ($tsp_conf['ffund'] / 12)) + $data['ffund_dist'];
        $data['cfund_dist'] = ($data['cfund_dist'] * ($tsp_conf['cfund'] / 12)) + $data['cfund_dist'];
        $data['sfund_dist'] = ($data['sfund_dist'] * ($tsp_conf['sfund'] / 12)) + $data['sfund_dist'];
        $data['ifund_dist'] = ($data['ifund_dist'] * ($tsp_conf['ifund'] / 12)) + $data['ifund_dist'];
        $lfund = $data['gfund_dist'] + $data['ffund_dist'] + $data['cfund_dist'] + $data['sfund_dist'] + $data['ifund_dist'];


        return $lfund;
    }

    public function getDistributionForLfund_new($fund_type = null, $lfund_amount = 0.00, $statement_date = null, $tsp_conf = [], $newMoneyLFundPart, $months_in_ret_and_statement)
    {
        $dt_Obj = new \DateTime($statement_date);
        $statement_date = $dt_Obj->format('Y-m-d');

        $lDists = $this->getLFundsDistributions($fund_type, $statement_date);
        $data = [];

        $data['gfund_dist'] = $lfund_amount * ($lDists['gfund'] / 100);
        $data['ffund_dist'] = $lfund_amount * ($lDists['ffund'] / 100);
        $data['cfund_dist'] = $lfund_amount * ($lDists['cfund'] / 100);
        $data['sfund_dist'] = $lfund_amount * ($lDists['sfund'] / 100);
        $data['ifund_dist'] = $lfund_amount * ($lDists['ifund'] / 100);

        $newMoneyDist['gfund_dist'] = $newMoneyLFundPart * ($lDists['gfund'] / 100);
        $newMoneyDist['ffund_dist'] = $newMoneyLFundPart * ($lDists['ffund'] / 100);
        $newMoneyDist['cfund_dist'] = $newMoneyLFundPart * ($lDists['cfund'] / 100);
        $newMoneyDist['sfund_dist'] = $newMoneyLFundPart * ($lDists['sfund'] / 100);
        $newMoneyDist['ifund_dist'] = $newMoneyLFundPart * ($lDists['ifund'] / 100);
        // Appply rate of return
        // $totalFund = $fund_money;
        // echo $tsp['GFund'] . "--------------- " . $totalFund; die;
        $new_total = 0;
        for ($i = 0; $i < $months_in_ret_and_statement; $i++) {
            $data['gfund_dist'] = $data['gfund_dist'] + $newMoneyDist['gfund_dist'];
            $data['ffund_dist'] = $data['ffund_dist'] + $newMoneyDist['ffund_dist'];
            $data['cfund_dist'] = $data['cfund_dist'] + $newMoneyDist['cfund_dist'];
            $data['sfund_dist'] = $data['sfund_dist'] + $newMoneyDist['sfund_dist'];
            $data['ifund_dist'] = $data['ifund_dist'] + $newMoneyDist['ifund_dist'];

            $data['gfund_dist'] = ($data['gfund_dist'] * ($tsp_conf['gfund'] / 12) / 100) + $data['gfund_dist'];
            $data['ffund_dist'] = ($data['ffund_dist'] * ($tsp_conf['ffund'] / 12) / 100) + $data['ffund_dist'];
            $data['cfund_dist'] = ($data['cfund_dist'] * ($tsp_conf['cfund'] / 12) / 100) + $data['cfund_dist'];
            $data['sfund_dist'] = ($data['sfund_dist'] * ($tsp_conf['sfund'] / 12) / 100) + $data['sfund_dist'];
            $data['ifund_dist'] = ($data['ifund_dist'] * ($tsp_conf['ifund'] / 12) / 100) + $data['ifund_dist'];

            // check distribution each month to get diffrent distributions of LFunds
            $newLFund = $data['gfund_dist'] + $data['ffund_dist'] + $data['cfund_dist'] + $data['sfund_dist'] + $data['ifund_dist'];
            $dt_Obj->modify('+1 month');
            $new_statement_date = $dt_Obj->format('Y-m-d');
            $lDists = $this->getLFundsDistributions($fund_type, $new_statement_date);
            $data['gfund_dist'] = $newLFund * ($lDists['gfund'] / 100);
            $data['ffund_dist'] = $newLFund * ($lDists['ffund'] / 100);
            $data['cfund_dist'] = $newLFund * ($lDists['cfund'] / 100);
            $data['sfund_dist'] = $newLFund * ($lDists['sfund'] / 100);
            $data['ifund_dist'] = $newLFund * ($lDists['ifund'] / 100);
        }

        $lfund = $data['gfund_dist'] + $data['ffund_dist'] + $data['cfund_dist'] + $data['sfund_dist'] + $data['ifund_dist'];
        return $lfund;
    }

    public function getLFundsDistributions($fund_type = null, $statement_date = null)
    {
        $lDists = LfundsDist::where('date', '<=', $statement_date)
            ->where('lfund_type', $fund_type)
            ->orderBy('date', 'DESC')
            ->first();
        if (!$lDists) {
            $lDists = LfundsDist::where('date', '<=', $statement_date)
                ->where('lfund_type', 'LIncome')
                ->orderBy('date', 'DESC')
                ->first();
        }
        if (!$lDists) {
            $lDists = LfundsDist::where('date', '>=', $statement_date)
                ->where('lfund_type', 'LIncome')
                ->orderBy('date', 'ASC')
                ->first();
        }
        return $lDists;
    }

    /************************************************
     *
     * getTspNewBalance
     ***/

    public function getTSPNewBalance_new($empId)
    {
        $tsp = $this->getEmpTspDetails($empId);

        $data['GFund'] = $data['FFund'] = $data['CFund'] = $data['SFund'] = $data['IFund'] = $data['LIncome'] = $data['L2025'] = $data['L2030'] = $data['L2035'] = $data['L2040'] = $data['L2045'] = $data['L2050'] = $data['L2055'] = $data['L2060'] = $data['L2065'] = $data['new_balance'] = 0;
        if (!isset($tsp['StatementDate']) || $tsp['StatementDate'] == null) {
            return $data;
        }

        $stDate = new \DateTime($tsp['StatementDate']);
        $st_year = $stDate->format('Y');
        // year in statement date should not be less than 2021
        // if ($st_year < 2021) {
        //     return $data;
        // }

        $emp = $this->getById($empId);

        $retirementDate = $emp->eligibility->RetirementDate;
        $rtDate = new \DateTime($retirementDate);

        $st_rt_diff_obj = $rtDate->diff($stDate);
        $st_rt_diff = $st_rt_diff_obj->y;
        $months_in_ret_and_statement = ($st_rt_diff * 12) + $st_rt_diff_obj->m;

        if (isset($tsp['payoff_date_general']) && ($tsp['payoff_date_general'] != null)) {
            $payoff_date_general_obj = new \DateTime($tsp['payoff_date_general']);
            $st_payoff_gen_diff_obj = $payoff_date_general_obj->diff($stDate);
            $st_payoff_gen_diff = $st_payoff_gen_diff_obj->y;
            $months_in_st_payoff_gen = ($st_payoff_gen_diff * 12) + $st_payoff_gen_diff_obj->m;
        } else {
            $months_in_st_payoff_gen = 0;
        }

        if (isset($tsp['payoff_date_residential']) && ($tsp['payoff_date_residential'] != null)) {
            $payoff_date_residential_obj = new \DateTime($tsp['payoff_date_residential']);
            $st_payoff_res_diff_obj = $payoff_date_residential_obj->diff($stDate);
            $st_payoff_res_diff = $st_payoff_res_diff_obj->y;


            $months_in_st_payoff_res = $st_payoff_res_diff * 12 + $st_payoff_res_diff_obj->m;
        } else {
            $months_in_st_payoff_res = 0;
        }

        // Check if months between {statement date and retirementDate} are less or greater than months between {statementDate and payOffDate}

        if ($months_in_ret_and_statement < $months_in_st_payoff_gen) {
            $months_for_loan_gen = $months_in_ret_and_statement;
        } else {
            $months_for_loan_gen = $months_in_st_payoff_gen;
        }

        if ($months_in_ret_and_statement < $months_in_st_payoff_res) {
            $months_for_loan_res = $months_in_ret_and_statement;
        } else {
            $months_for_loan_res = $months_in_st_payoff_res;
        }

        $tsp_conf = $this->getEmpTspConfigurations($empId);

        $currentSalaryY = $emp->CurrentSalary;
        $currentSalaryPP = $currentSalaryY / 26;
        // Part 1
        $employee_contri_pp = $tsp['ContributionRegular'] + $tsp['ContributionCatchUp'];
        // Part 2
        $emp_contri_general_loan = ($tsp['loan_repayment_general'] * 26) / 12; // pp => monthly
        $emp_contri_residen_loan = ($tsp['loan_repayment_residential'] * 26) / 12; // pp => monthly

        $employee_contri_percentage = round(($employee_contri_pp / $currentSalaryPP) * 100);

        // Agency contri 1%
        if ($emp->systemType == "FERS") {
            $auto_agencyContri = $currentSalaryPP * (1 / 100);

            if ($employee_contri_percentage == 0) {
                $matching_agencyContri = 0;
            } elseif ($employee_contri_percentage == 1) {
                $matching_agencyContri = $currentSalaryPP * (1 / 100);
            } elseif ($employee_contri_percentage == 2) {
                $matching_agencyContri = $currentSalaryPP * (2 / 100);
            } elseif ($employee_contri_percentage == 3) {
                $matching_agencyContri = $currentSalaryPP * (3 / 100);
            } elseif ($employee_contri_percentage == 4) {
                $matching_agencyContri = $currentSalaryPP * (3.5 / 100);
            } else { // if($employee_contri_percentage >= 5) {
                $matching_agencyContri = $currentSalaryPP * (4 / 100);
            }
        } else {
            $auto_agencyContri = 0;
            $matching_agencyContri = 0;
        }

        $total_new_money_pp = $employee_contri_pp + $auto_agencyContri + $matching_agencyContri;
        $total_new_money_monthly = ($total_new_money_pp * 26) / 12;

        // if (round($tsp['GFundDist']) != 0) {
        // Part 1 Calculation GFund
        $newMoneyGfundPart = $total_new_money_monthly * ($tsp['GFundDist'] / 100);
        $data['GFund'] = $this->getEndingBalanceForFunds($tsp['GFund'], $newMoneyGfundPart, $tsp_conf['gfund'], $months_in_ret_and_statement);

        // Part 2 Calculation GFund
        $generalLoanGFundPart = $emp_contri_general_loan * ($tsp['GFundDist'] / 100);
        $residenLoanGFundPart = $emp_contri_residen_loan * ($tsp['GFundDist'] / 100);

        $generalLoanEndingBalanceGFund = $this->getEndingBalanceForLoans($generalLoanGFundPart, $tsp_conf['gfund'], $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceGFund = $this->getEndingBalanceForLoans($residenLoanGFundPart, $tsp_conf['gfund'], $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_residential'], $retirementDate);

        $data['GFund'] = $data['GFund'] + $generalLoanEndingBalanceGFund + $residenLoanEndingBalanceGFund;
        // }

        // if (round($tsp['FFundDist']) != 0) {
        // Part 1 Calculation
        $newMoneyFfundPart = $total_new_money_monthly * ($tsp['FFundDist'] / 100);
        $data['FFund'] = $this->getEndingBalanceForFunds($tsp['FFund'], $newMoneyFfundPart, $tsp_conf['ffund'], $months_in_ret_and_statement);

        // Part 2 Calculation
        $generalLoanFFundPart = $emp_contri_general_loan * ($tsp['FFundDist'] / 100);
        $residenLoanFFundPart = $emp_contri_residen_loan * ($tsp['FFundDist'] / 100);

        $generalLoanEndingBalanceFFund = $this->getEndingBalanceForLoans($generalLoanFFundPart, $tsp_conf['ffund'], $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceFFund = $this->getEndingBalanceForLoans($residenLoanFFundPart, $tsp_conf['ffund'], $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_residential'], $retirementDate);

        $data['FFund'] = $data['FFund'] + $generalLoanEndingBalanceFFund + $residenLoanEndingBalanceFFund;
        // }
        // if (round($tsp['CFundDist']) != 0) {
        // Part 1 Calculation
        $newMoneyCfundPart = $total_new_money_monthly * ($tsp['CFundDist'] / 100);
        $data['CFund'] = $this->getEndingBalanceForFunds($tsp['CFund'], $newMoneyCfundPart, $tsp_conf['cfund'], $months_in_ret_and_statement);

        // Part 2 Calculation
        $generalLoanCFundPart = $emp_contri_general_loan * ($tsp['CFundDist'] / 100);
        $residenLoanCFundPart = $emp_contri_residen_loan * ($tsp['CFundDist'] / 100);

        $generalLoanEndingBalanceCFund = $this->getEndingBalanceForLoans($generalLoanCFundPart, $tsp_conf['cfund'], $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceCFund = $this->getEndingBalanceForLoans($residenLoanCFundPart, $tsp_conf['cfund'], $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_residential'], $retirementDate);

        // echo $data['CFund'] . "<br>---<br>";
        $data['CFund'] = $data['CFund'] + $generalLoanEndingBalanceCFund + $residenLoanEndingBalanceCFund;
        // echo $data['CFund'];
        // die;
        // }

        // if (round($tsp['SFundDist']) != 0) {
        // Part 1 Calculation
        $newMoneySfundPart = $total_new_money_monthly * ($tsp['SFundDist'] / 100);
        $data['SFund'] = $this->getEndingBalanceForFunds($tsp['SFund'], $newMoneySfundPart, $tsp_conf['sfund'], $months_in_ret_and_statement);

        // Part 2 Calculation
        $generalLoanSFundPart = $emp_contri_general_loan * ($tsp['SFundDist'] / 100);
        $residenLoanSFundPart = $emp_contri_residen_loan * ($tsp['SFundDist'] / 100);

        $generalLoanEndingBalanceSFund = $this->getEndingBalanceForLoans($generalLoanSFundPart, $tsp_conf['sfund'], $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceSFund = $this->getEndingBalanceForLoans($residenLoanSFundPart, $tsp_conf['sfund'], $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_residential'], $retirementDate);

        $data['SFund'] = $data['SFund'] + $generalLoanEndingBalanceSFund + $residenLoanEndingBalanceSFund;
        // }

        // if (round($tsp['IFundDist']) != 0) {
        // Part 1 Calculation
        $newMoneyIfundPart = $total_new_money_monthly * ($tsp['IFundDist'] / 100);
        $data['IFund'] = $this->getEndingBalanceForFunds($tsp['IFund'], $newMoneyIfundPart, $tsp_conf['ifund'], $months_in_ret_and_statement);

        // Part 2 Calculation
        $generalLoanIFundPart = $emp_contri_general_loan * ($tsp['IFundDist'] / 100);
        $residenLoanIFundPart = $emp_contri_residen_loan * ($tsp['IFundDist'] / 100);

        $generalLoanEndingBalanceIFund = $this->getEndingBalanceForLoans($generalLoanIFundPart, $tsp_conf['ifund'], $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceIFund = $this->getEndingBalanceForLoans($residenLoanIFundPart, $tsp_conf['ifund'], $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_residential'], $retirementDate);

        $data['IFund'] = $data['IFund'] + $generalLoanEndingBalanceIFund + $residenLoanEndingBalanceIFund;
        // }

        // if (round($tsp['L2025Dist']) != 0) {
        // Part 1
        $newMoneyL2025fundPart = $total_new_money_monthly * ($tsp['L2025Dist'] / 100);

        /* ******************************** */
        $data['L2025'] = $this->getDistributionForLfund_new('L2025', $tsp['L2025'], $tsp['StatementDate'], $tsp_conf, $newMoneyL2025fundPart, $months_in_ret_and_statement);
        /* **********************

        ********************

        **********************

        dd($data['L2025']); */

        // Part 2 calculation for L2025
        $generalLoanL2025Part = $emp_contri_general_loan * ($tsp['L2025Dist'] / 100);
        $residenLoanL2025Part = $emp_contri_residen_loan * ($tsp['L2025Dist'] / 100);
        $generalLoanEndingBalanceL2025 = $this->getEndingBalanceForLoans_lfunds('L2025', $generalLoanL2025Part, $tsp_conf, $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);
        // echo $generalLoanEndingBalanceL2025;
        // die;
        $residenLoanEndingBalanceL2025 = $this->getEndingBalanceForLoans_lfunds('L2025', $residenLoanL2025Part, $tsp_conf, $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $data['L2025'] = $data['L2025'] + $generalLoanEndingBalanceL2025 + $residenLoanEndingBalanceL2025;

        // }
        // echo $total_new_money_monthly;
        // die;
        // if (round($tsp['L2030Dist']) != 0) {
        // Part 1 calculation for L2030
        $newMoneyL2030fundPart = $total_new_money_monthly * ($tsp['L2030Dist'] / 100);
        $data['L2030'] = $this->getDistributionForLfund_new('L2030', $tsp['L2030'], $tsp['StatementDate'], $tsp_conf, $newMoneyL2030fundPart, $months_in_ret_and_statement);

        // Part 2 calculation for L2030
        $generalLoanL2030Part = $emp_contri_general_loan * ($tsp['L2030Dist'] / 100);
        $residenLoanL2030Part = $emp_contri_residen_loan * ($tsp['L2030Dist'] / 100);

        $generalLoanEndingBalanceL2030 = $this->getEndingBalanceForLoans_lfunds('L2030', $generalLoanL2030Part, $tsp_conf, $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceL2030 = $this->getEndingBalanceForLoans_lfunds('L2030', $residenLoanL2030Part, $tsp_conf, $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $data['L2030'] = $data['L2030'] + $generalLoanEndingBalanceL2030 + $residenLoanEndingBalanceL2030;

        // if (round($tsp['L2035Dist']) != 0) {
        // Part 1 calculation for L2035
        $newMoneyL2035fundPart = $total_new_money_monthly * ($tsp['L2035Dist'] / 100);
        $data['L2035'] = $this->getDistributionForLfund_new('L2035', $tsp['L2035'], $tsp['StatementDate'], $tsp_conf, $newMoneyL2035fundPart, $months_in_ret_and_statement);

        // Part 2 calculation for L2035
        $generalLoanL2035Part = $emp_contri_general_loan * ($tsp['L2035Dist'] / 100);
        $residenLoanL2035Part = $emp_contri_residen_loan * ($tsp['L2035Dist'] / 100);

        $generalLoanEndingBalanceL2035 = $this->getEndingBalanceForLoans_lfunds('L2035', $generalLoanL2035Part, $tsp_conf, $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceL2035 = $this->getEndingBalanceForLoans_lfunds('L2035', $residenLoanL2035Part, $tsp_conf, $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $data['L2035'] = $data['L2035'] + $generalLoanEndingBalanceL2035 + $residenLoanEndingBalanceL2035;
        // }

        // if (round($tsp['L2040Dist']) != 0) {
        // Part 1 calculation for L2040
        $newMoneyL2040fundPart = $total_new_money_monthly * ($tsp['L2040Dist'] / 100);
        $data['L2040'] = $this->getDistributionForLfund_new('L2040', $tsp['L2040'], $tsp['StatementDate'], $tsp_conf, $newMoneyL2040fundPart, $months_in_ret_and_statement);

        // Part 2 calculation for L2040
        $generalLoanL2040Part = $emp_contri_general_loan * ($tsp['L2040Dist'] / 100);
        $residenLoanL2040Part = $emp_contri_residen_loan * ($tsp['L2040Dist'] / 100);

        $generalLoanEndingBalanceL2040 = $this->getEndingBalanceForLoans_lfunds('L2040', $generalLoanL2040Part, $tsp_conf, $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceL2040 = $this->getEndingBalanceForLoans_lfunds('L2040', $residenLoanL2040Part, $tsp_conf, $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $data['L2040'] = $data['L2040'] + $generalLoanEndingBalanceL2040 + $residenLoanEndingBalanceL2040;
        // }

        // if (round($tsp['L2045Dist']) != 0) {
        // Part 1 calculation for L2045
        $newMoneyL2045fundPart = $total_new_money_monthly * ($tsp['L2045Dist'] / 100);
        $data['L2045'] = $this->getDistributionForLfund_new('L2045', $tsp['L2045'], $tsp['StatementDate'], $tsp_conf, $newMoneyL2045fundPart, $months_in_ret_and_statement);

        // Part 2 calculation for L2045
        $generalLoanL2045Part = $emp_contri_general_loan * ($tsp['L2045Dist'] / 100);
        $residenLoanL2045Part = $emp_contri_residen_loan * ($tsp['L2045Dist'] / 100);

        $generalLoanEndingBalanceL2045 = $this->getEndingBalanceForLoans_lfunds('L2045', $generalLoanL2045Part, $tsp_conf, $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceL2045 = $this->getEndingBalanceForLoans_lfunds('L2045', $residenLoanL2045Part, $tsp_conf, $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $data['L2045'] = $data['L2045'] + $generalLoanEndingBalanceL2045 + $residenLoanEndingBalanceL2045;
        // }

        // if (round($tsp['L2050Dist']) != 0) {
        // Part 1 calculation for L2050
        $newMoneyL2050fundPart = $total_new_money_monthly * ($tsp['L2050Dist'] / 100);
        $data['L2050'] = $this->getDistributionForLfund_new('L2050', $tsp['L2050'], $tsp['StatementDate'], $tsp_conf, $newMoneyL2050fundPart, $months_in_ret_and_statement);

        // Part 2 calculation for L2050
        $generalLoanL2050Part = $emp_contri_general_loan * ($tsp['L2050Dist'] / 100);
        $residenLoanL2050Part = $emp_contri_residen_loan * ($tsp['L2050Dist'] / 100);

        $generalLoanEndingBalanceL2050 = $this->getEndingBalanceForLoans_lfunds('L2050', $generalLoanL2050Part, $tsp_conf, $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceL2050 = $this->getEndingBalanceForLoans_lfunds('L2050', $residenLoanL2050Part, $tsp_conf, $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $data['L2050'] = $data['L2050'] + $generalLoanEndingBalanceL2050 + $residenLoanEndingBalanceL2050;
        // }

        // if (round($tsp['L2055Dist']) != 0) {
        // Part 1 calculation for L2055
        $newMoneyL2055fundPart = $total_new_money_monthly * ($tsp['L2055Dist'] / 100);
        $data['L2055'] = $this->getDistributionForLfund_new('L2055', $tsp['L2055'], $tsp['StatementDate'], $tsp_conf, $newMoneyL2055fundPart, $months_in_ret_and_statement);

        // Part 2 calculation for L2055
        $generalLoanL2055Part = $emp_contri_general_loan * ($tsp['L2055Dist'] / 100);
        $residenLoanL2055Part = $emp_contri_residen_loan * ($tsp['L2055Dist'] / 100);

        $generalLoanEndingBalanceL2055 = $this->getEndingBalanceForLoans_lfunds('L2055', $generalLoanL2055Part, $tsp_conf, $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceL2055 = $this->getEndingBalanceForLoans_lfunds('L2055', $residenLoanL2055Part, $tsp_conf, $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $data['L2055'] = $data['L2055'] + $generalLoanEndingBalanceL2055 + $residenLoanEndingBalanceL2055;
        // }

        // if (round($tsp['L2060Dist']) != 0) {
        // Part 1 calculation for L2060
        $newMoneyL2060fundPart = $total_new_money_monthly * ($tsp['L2060Dist'] / 100);
        $data['L2060'] = $this->getDistributionForLfund_new('L2060', $tsp['L2060'], $tsp['StatementDate'], $tsp_conf, $newMoneyL2060fundPart, $months_in_ret_and_statement);

        // Part 2 calculation for L2060
        $generalLoanL2060Part = $emp_contri_general_loan * ($tsp['L2060Dist'] / 100);
        $residenLoanL2060Part = $emp_contri_residen_loan * ($tsp['L2060Dist'] / 100);

        $generalLoanEndingBalanceL2060 = $this->getEndingBalanceForLoans_lfunds('L2060', $generalLoanL2060Part, $tsp_conf, $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceL2060 = $this->getEndingBalanceForLoans_lfunds('L2060', $residenLoanL2060Part, $tsp_conf, $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $data['L2060'] = $data['L2060'] + $generalLoanEndingBalanceL2060 + $residenLoanEndingBalanceL2060;
        // }

        // if (round($tsp['L2065Dist']) != 0) {
        // Part 1 calculation for L2065
        $newMoneyL2065fundPart = $total_new_money_monthly * ($tsp['L2065Dist'] / 100);
        $data['L2065'] = $this->getDistributionForLfund_new('L2065', $tsp['L2065'], $tsp['StatementDate'], $tsp_conf, $newMoneyL2065fundPart, $months_in_ret_and_statement);

        // Part 2 calculation for L2065
        $generalLoanL2065Part = $emp_contri_general_loan * ($tsp['L2065Dist'] / 100);
        $residenLoanL2065Part = $emp_contri_residen_loan * ($tsp['L2065Dist'] / 100);

        $generalLoanEndingBalanceL2065 = $this->getEndingBalanceForLoans_lfunds('L2065', $generalLoanL2065Part, $tsp_conf, $months_for_loan_gen, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $residenLoanEndingBalanceL2065 = $this->getEndingBalanceForLoans_lfunds('L2065', $residenLoanL2065Part, $tsp_conf, $months_for_loan_res, $tsp['StatementDate'], $tsp['payoff_date_general'], $retirementDate);

        $data['L2065'] = $data['L2065'] + $generalLoanEndingBalanceL2065 + $residenLoanEndingBalanceL2065;
        // }

        // if (round($tsp['LIncomeDist']) != 0) {
        $newMoneyLIncomefundPart = $total_new_money_monthly * ($tsp['LIncomeDist'] / 100);
        $data['LIncome'] = $this->getDistributionForLfund_new('LIncome', $tsp['LIncome'], $tsp['StatementDate'], $tsp_conf, $newMoneyLIncomefundPart, $months_in_ret_and_statement);
        // }

        $new_balance = $data['GFund'] + $data['FFund'] + $data['CFund'] + $data['SFund'] + $data['IFund'] + $data['LIncome'] + $data['L2025'] + $data['L2030'] + $data['L2035'] + $data['L2040'] + $data['L2045'] + $data['L2050'] + $data['L2055'] + $data['L2060'] + $data['L2065'];

        $data['new_balance'] = $new_balance;
        return $data;
    }

    public function getEndingBalanceForFunds($fund_money, $newMoneyFundPart, $fund_rate, $months_in_ret_and_statement)
    {
        $monthly_rate = $fund_rate / 12;
        $totalFund = $fund_money;
        for ($i = 0; $i < $months_in_ret_and_statement; $i++) {
            $totalFund = $totalFund + $newMoneyFundPart;
            $totalFund = $totalFund + ($totalFund * ($monthly_rate / 100));
        }
        return $totalFund;
    }

    public function getEndingBalanceForLoans($loanGFundPart, $fund_rate, $months_in_ret_and_statement, $statementDate, $payoff_date_general, $retirementDate)
    {
        $startDate = new \DateTime($statementDate);
        $payoffDate = new \DateTime($payoff_date_general);
        $rtDate = new \DateTime($retirementDate);
        $monthly_rate = $fund_rate / 12;
        // $totalFund = 0;
        $totalFund = $loanGFundPart;

        for ($i = 0; $i < $months_in_ret_and_statement; $i++) {
            if ($startDate >= $rtDate) {
                break;
            }
            if ($startDate <= $payoffDate) {
                $totalFund = $totalFund + $loanGFundPart;
                $totalFund = $totalFund + ($totalFund * ($monthly_rate / 100));
            } else {
                $totalFund = $totalFund + ($totalFund * ($monthly_rate / 100));
            }
            $startDate->modify('+1 month');
        }
        return $totalFund;
    }


    public function getEndingBalanceForLoans_lfunds($fund_type, $newMoneyLFundPart, $tsp_conf, $months_for_loan, $statementDate, $payoff_date_general, $retirementDate)
    {
        $dt_Obj = new \DateTime($statementDate);
        $statementDate = $dt_Obj->format('Y-m-d');
        $rtDate = new \DateTime($retirementDate);
        $payoffDate = new \DateTime($payoff_date_general);
        $lDists = $this->getLFundsDistributions($fund_type, $statementDate);

        $data = [];

        $data['gfund_dist'] = 0;
        $data['ffund_dist'] = 0;
        $data['cfund_dist'] = 0;
        $data['sfund_dist'] = 0;
        $data['ifund_dist'] = 0;

        $newMoneyDist['gfund_dist'] = $newMoneyLFundPart * ($lDists['gfund'] / 100);
        $newMoneyDist['ffund_dist'] = $newMoneyLFundPart * ($lDists['ffund'] / 100);
        $newMoneyDist['cfund_dist'] = $newMoneyLFundPart * ($lDists['cfund'] / 100);
        $newMoneyDist['sfund_dist'] = $newMoneyLFundPart * ($lDists['sfund'] / 100);
        $newMoneyDist['ifund_dist'] = $newMoneyLFundPart * ($lDists['ifund'] / 100);
        // Appply rate of return
        // $totalFund = $fund_money;
        // echo $tsp['GFund'] . "--------------- " . $totalFund; die;

        for ($i = 0; $i < $months_for_loan; $i++) {
            if (($dt_Obj >= $rtDate) || ($dt_Obj >= $payoffDate)) {
                break;
            }
            $data['gfund_dist'] = $data['gfund_dist'] + $newMoneyDist['gfund_dist'];
            $data['ffund_dist'] = $data['ffund_dist'] + $newMoneyDist['ffund_dist'];
            $data['cfund_dist'] = $data['cfund_dist'] + $newMoneyDist['cfund_dist'];
            $data['sfund_dist'] = $data['sfund_dist'] + $newMoneyDist['sfund_dist'];
            $data['ifund_dist'] = $data['ifund_dist'] + $newMoneyDist['ifund_dist'];

            $data['gfund_dist'] = ($data['gfund_dist'] * ($tsp_conf['gfund'] / 12) / 100) + $data['gfund_dist'];
            $data['ffund_dist'] = ($data['ffund_dist'] * ($tsp_conf['ffund'] / 12) / 100) + $data['ffund_dist'];
            $data['cfund_dist'] = ($data['cfund_dist'] * ($tsp_conf['cfund'] / 12) / 100) + $data['cfund_dist'];
            $data['sfund_dist'] = ($data['sfund_dist'] * ($tsp_conf['sfund'] / 12) / 100) + $data['sfund_dist'];
            $data['ifund_dist'] = ($data['ifund_dist'] * ($tsp_conf['ifund'] / 12) / 100) + $data['ifund_dist'];

            $dt_Obj->modify('+1 month');
            $newLFund = $data['gfund_dist'] + $data['ffund_dist'] + $data['cfund_dist'] + $data['sfund_dist'] + $data['ifund_dist'];
            $new_statement_date = $dt_Obj->format('Y-m-d');
            $lDists = $this->getLFundsDistributions($fund_type, $new_statement_date);

            $data['gfund_dist'] = $newLFund * ($lDists['gfund'] / 100);
            $data['ffund_dist'] = $newLFund * ($lDists['ffund'] / 100);
            $data['cfund_dist'] = $newLFund * ($lDists['cfund'] / 100);
            $data['sfund_dist'] = $newLFund * ($lDists['sfund'] / 100);
            $data['ifund_dist'] = $newLFund * ($lDists['ifund'] / 100);
        }

        $lfund = $data['gfund_dist'] + $data['ffund_dist'] + $data['cfund_dist'] + $data['sfund_dist'] + $data['ifund_dist'];
        return $lfund;
    }

    public function updateScenario($empId = null, $scenario = 1)
    {
        $employee = Employee::with(['partTimeServices', 'advisor', 'eligibility', 'deduction', 'employeeConfig', 'militaryService', 'nonDeductionService', 'refundedService'])->where('EmployeeId', $empId)->first();
        // echo "<pre>"; print_r($employee->toArray()); exit;
        if (!$employee) {
            return [];
        } else {
            // return [];
            $reportScenario = ReportScenario::where(['EmployeeId' => $empId, 'ScenarioNo' => $scenario])->first();
            if (!$reportScenario) {
                return [];
            }

            $annuityCola = 1 + ($employee->AnnuityCola / 100);

            $partTimeHours = 0;
            $fullTimeHours = 0;
            $partTimeMultiplier = 1;
            $totalYears = 0;

            $marriedMultiplier = ($employee->MaritalStatusType = "Married") ? 1 : 0;
            if (!is_null($employee->part_time_services)) {
                foreach ($employee->part_time_services as $partTime) {
                    $totalYears = app('Common')->GetMonthDiffRoundToZero($partTime->FromDate, $partTime->ToDate) / 12.0;
                    $partTimeHours += ($totalYears * ($partTime->HoursWeek * 52));
                    $fullTimeHours += ($totalYears * 2087);
                }
            }

            $hoursMissed = $fullTimeHours - $partTimeHours;

            $unusedSickLeaveMonths = app('Common')->hoursToMonths($employee->UnusedSickLeave);
            $unusedAnnualLeaveMonths = app('Common')->hoursToMonths($employee->UnusedAnnualLeave);

            $penaltyFERSRefundMonth = 0;
            $penaltyFERSNonDeductionMonth = 0;
            $penaltyCSRSRefundMonth = 0;
            $penaltyCSRSNonDeductionMonth = 0;
            $penaltyCSRSRedepositPre1982 = 0;
            $amountOwedCSRSRefundPre1991 = 0;

            // Check refunded services for deduction
            if (!is_null($employee->refunded_service)) {
                foreach ($employee->refunded_service as $rservice) {
                    $deduct = false;

                    if ($employee->SystemType == "FERS") {

                        if ($rservice->Withdrawal == 1 && $rservice->Redeposit == 0) {
                            $deduct = true;
                        }
                    } elseif ($employee->SystemType == "Transfers") {

                        if ($rservice->Withdrawal == 1 && $rservice->Redeposit == 0 && $employee->FERSTransferDate > $rservice->ToDate) {
                            $deduct = true;
                        }
                    } elseif ($employee->SystemType == "CSRS") {

                        // '
                        // '   See document Redeposit Service for CSRS
                        // '

                        if (($rservice->Withdrawal == 1 && $rservice->Redeposit == 0) && (($employee->FERSTransferDate > $rservice->ToDate) || (is_null($employee->FERSTransferDate) && $rservice->ToDate >= "3/1/1991"))) {
                            $deduct = true;
                        } else {
                            if ($rservice->ToDate < "3/1/1991") {
                                $amountOwedCSRSRefundPre1991 += $rservice->AmountOwed;
                            }
                        }
                    }

                    if ($deduct == true) {

                        if ($employee->SystemType == "FERS") {
                            $penaltyFERSRefundMonth += app('Common')->GetMonthDiffRoundToZero($rservice->FromDate, $rservice->ToDate);
                        } elseif ($employee->SystemType == "CSRS") {
                            $penaltyCSRSRefundMonth += app('Common')->GetMonthDiffRoundToZero($rservice->FromDate, $rservice->ToDate);
                        } else {
                            if (is_null($employee->FERSTransferDate)) {
                                $employee->FERSTransferDate = $employee->eligibility->AnnuitySCD;
                            }

                            // '
                            // '   If FromDate >= emp.FERSTransferDate
                            // '       Apply all to FERS
                            // '   ElseIf ToDate < emp.FERSTransferDate
                            // '       Apply all to CSRS
                            // '   Else
                            // '       FERS = Diff emp.FERSTransferDate, ToDate
                            // '       CSRS = Diff FromDate, emp.FERSTransferDate
                            // '

                            if ($rservice->FromDate >= $employee->FERSTransferDate) {
                                $penaltyFERSRefundMonth += app('Common')->GetMonthDiffRoundToZero($rservice->FromDate, $rservice->ToDate);
                            } elseif ($rservice->ToDate < $employee->FERSTransferDate) {
                                $penaltyCSRSRefundMonth += app('Common')->GetMonthDiffRoundToZero($rservice->FromDate, $rservice->ToDate);
                            } else {
                                $penaltyFERSRefundMonth += app('Common')->GetMonthDiffRoundToZero($employee->FERSTransferDate, $rservice->ToDate);
                                $penaltyCSRSRefundMonth += app('Common')->GetMonthDiffRoundToZero($rservice->FromDate, $employee->FERSTransferDate);
                            }
                        }
                    }
                }
            }

            $monthsAgeAtRetirement = app('Common')->GetMonthDiffRoundToZero($employee->eligibility->DateOfBirth, $reportScenario->RetirementDate);
            $monthsServiceAtRetirement = app('Common')->GetMonthDiffRoundToZero($employee->eligibility->AnnuitySCD, $reportScenario->RetirementDate);

            $csrsMonthsServiceAtRetirement = 0;
            $fersMonthsServiceAtRetirement = 0;

            if ($employee->SystemType == "FERS") {
                $fersMonthsServiceAtRetirement = $monthsServiceAtRetirement;
            } elseif ($employee->SystemType == "CSRS") {
                $csrsMonthsServiceAtRetirement = $monthsServiceAtRetirement;
            } else {
                if (is_null($employee->FERSTransferDate)) {
                    $employee->FERSTransferDate = $employee->eligibility->AnnuitySCD;
                }
                $csrsMonthsServiceAtRetirement = app('Common')->GetMonthDiffRoundToZero($employee->eligibility->AnnuitySCD, $employee->FERSTransferDate);
                $fersMonthsServiceAtRetirement = app('Common')->GetMonthDiffRoundToZero($employee->FERSTransferDate, $reportScenario->RetirementDate);
            }
            // echo $fersMonthsServiceAtRetirement;
            // die;
            $annualCSRSAnnuity = 0;
            $annualFERSAnnuity = 0;
            $penaltyFERSRefundAmount = 0;
            $penaltyFERSNonDeductionAmount = 0;
            $penaltyCSRSRefundAmount = 0;
            $penaltyCSRSNonDeductionAmount = 0;
            $penaltyCSRSRefundPre1991 = 0;

            $mraMultiplier = 1;

            //  echo $fersMonthsServiceAtRetirement; exit;
            if ($fersMonthsServiceAtRetirement > 0) {

                $result = $this->GetFERSAnnuity($employee->EmployeeType, $employee->RetirementType, $fersMonthsServiceAtRetirement, $reportScenario->High3Average, $monthsServiceAtRetirement, $monthsAgeAtRetirement, $unusedSickLeaveMonths);
                // dd($result);
                $annualFERSAnnuity = $result['annualFERSAnnuity'];
                $mraMultiplier = $result['mraMultiplier'];

                if ($penaltyFERSRefundMonth > 0) {
                    $result = $this->GetFERSAnnuity($employee->EmployeeType, $employee->RetirementType, $fersMonthsServiceAtRetirement - $penaltyFERSRefundMonth, $reportScenario->High3Average, $monthsServiceAtRetirement - $penaltyFERSRefundMonth - $penaltyCSRSRefundMonth, $monthsAgeAtRetirement, $unusedSickLeaveMonths);
                    $penaltyFERSRefundAmount = $annualFERSAnnuity - $result['annualFERSAnnuity'];
                }

                if ($penaltyFERSNonDeductionMonth > 0) {
                    $result = $this->GetFERSAnnuity($employee->EmployeeType, $employee->RetirementType, $fersMonthsServiceAtRetirement - $penaltyFERSNonDeductionMonth, $reportScenario->High3Average, $monthsServiceAtRetirement - $penaltyFERSNonDeductionMonth - $penaltyCSRSNonDeductionMonth, $monthsAgeAtRetirement, $unusedSickLeaveMonths);
                    $penaltyFERSNonDeductionAmount = $annualFERSAnnuity - $result['annualFERSAnnuity'];
                }
            }

            if ($csrsMonthsServiceAtRetirement > 0) {

                $annualCSRSAnnuity = $this->GetCSRSAnnuity($employee->EmployeeType, $csrsMonthsServiceAtRetirement, $reportScenario->High3Average, $employee->CSRSOffsetDate, $employee->eligibility->RetirementDate, $employee->SSMonthlyAtStartAge);

                if ($penaltyCSRSRefundMonth > 0) {
                    $penaltyCSRSRefundAmount = $annualCSRSAnnuity - $this->GetCSRSAnnuity($employee->EmployeeType, $csrsMonthsServiceAtRetirement - $penaltyCSRSRefundMonth, $reportScenario->High3Average, $employee->CSRSOffsetDate, $employee->eligibility->RetirementDate, $employee->SSMonthlyAtStartAge);
                }

                if ($penaltyCSRSNonDeductionMonth > 0) {
                    $penaltyCSRSNonDeductionAmount = $annualCSRSAnnuity - $this->GetCSRSAnnuity($employee->EmployeeType, $csrsMonthsServiceAtRetirement - $penaltyCSRSNonDeductionMonth, $reportScenario->High3Average, $employee->CSRSOffsetDate, $employee->eligibility->RetirementDate, $employee->SSMonthlyAtStartAge);
                }

                if ($amountOwedCSRSRefundPre1991 > 0) {
                    $penaltyCSRSRefundPre1991 = $this->GetCSRSPre1991RefundPenalty(app('Common')->GetYearDiff($employee->eligibility->DateOfBirth, $reportScenario->RetirementDate), $amountOwedCSRSRefundPre1991);
                }
            } // ' FERS/CSRS

            $totalHours = app('Common')->GetMonthDiffRoundToZero($employee->eligibility->LeaveSCD, $reportScenario->RetirementDate) * 173;
            $partTimeMultiplier = 1;

            // '
            // '   hours should have worked = 50000 (This is all of the hours that should have been worked)
            // '   part time hours
            // '


            // '
            // '
            // '   If retired and 62 years of age or older, apply CSRS Offset Penalty
            // '

            if ($totalHours > 0) {
                $partTimeMultiplier = (($totalHours - $hoursMissed) / $totalHours);
            }

            $annualFERSAnnuity = $annualFERSAnnuity * $partTimeMultiplier;
            $annualCSRSAnnuity = $annualCSRSAnnuity * $partTimeMultiplier;

            $annualAnnuity = $annualFERSAnnuity + $annualCSRSAnnuity;


            // '
            // '   Log to ReportScenario

            $scenarioSave = ReportScenario::find($reportScenario->ReportScenarioId);

            $scenarioSave->MRA10Multiplier = $mraMultiplier;
            $scenarioSave->PartTimeMultiplier = $partTimeMultiplier;
            $scenarioSave->AnnuityBeforeDeduction = $annualAnnuity;
            $scenarioSave->Annuity = $scenarioSave->AnnuityBeforeDeduction * $scenarioSave->MRA10Multiplier;
            $scenarioSave->SurvivorAnnuity = ($annualFERSAnnuity * $scenarioSave->MRA10Multiplier * 0.5 * $marriedMultiplier) + ($annualCSRSAnnuity * $scenarioSave->MRA10Multiplier * 0.55 * $marriedMultiplier);
            $scenarioSave->SurvivorAnnuityCost = $annualFERSAnnuity * $scenarioSave->MRA10Multiplier * 0.1 * $marriedMultiplier;

            if ($annualCSRSAnnuity > 0 && $marriedMultiplier > 0) {

                $csrsFinalAnnuityAmount = $annualCSRSAnnuity * $scenarioSave->MRA10Multiplier;

                $csrsSurvivorBenefit = $annualCSRSAnnuity * $scenarioSave->MRA10Multiplier * 0.55;
                $csrsSurvivorBenefitCost = 0;

                if ($csrsFinalAnnuityAmount > 3600) {
                    $csrsSurvivorBenefitCost = (3600 * 0.025) + (($csrsFinalAnnuityAmount - 3600) * 0.1);
                } else {
                    $csrsSurvivorBenefitCost = $csrsFinalAnnuityAmount * 0.025;
                }

                $scenarioSave->SurvivorAnnuityCost += $csrsSurvivorBenefitCost;
            }

            $scenarioSave->FERSServiceAtRetirement = $fersMonthsServiceAtRetirement;
            $scenarioSave->CSRSServiceAtRetirement = $csrsMonthsServiceAtRetirement;

            $scenarioSave->save();
        }
        return $scenarioSave->toArray();
    }

    public function GetFERSAnnuity($employeeType = null, $retirementType = null, $fersMonthsServiceAtRetirement = 0, $high3Average = 0, $monthsServiceAtRetirement = 0, $monthsAgeAtRetirement = 0, $monthsUnusedSickLeave = 0)
    {
        $annualFERSAnnuity = 0;
        $mraMultiplier = 0;
        if (($employeeType == "Regular") || ($employeeType == "eCBPO")) {
            $first20 = 0;
            $rest = 0;
            // 48
            if ($fersMonthsServiceAtRetirement > (20 * 12)) {
                $first20 = 20;
                $rest = ($fersMonthsServiceAtRetirement - (20 * 12)) / 12;
            } else {
                $first20 = $fersMonthsServiceAtRetirement / 12;
            }
            // 4

            $annualFERSAnnuity = ($high3Average * 0.017 * $first20) + ($high3Average * 0.01 * $rest);
            $mraMultiplier = 1;
        } else {
            if ($monthsAgeAtRetirement >= (62 * 12) && (($monthsServiceAtRetirement - $monthsUnusedSickLeave) >= (20 * 12))) {
                $annualFERSAnnuity = $high3Average * 0.011 * ($fersMonthsServiceAtRetirement / 12);
            } else {
                $annualFERSAnnuity = $high3Average * 0.01 * ($fersMonthsServiceAtRetirement / 12);
            }

            if ($retirementType == "Regular") {
                /* '
                '   MRA+10 Penalty
                ' */
                if ($monthsServiceAtRetirement >= (10 * 12) && $monthsServiceAtRetirement < (30 * 12)) {

                    $monthsEarly = 0;
                    if ($monthsServiceAtRetirement >= (20 * 12) && $monthsAgeAtRetirement >= (60 * 12)) {
                        $monthsEarly = 0;
                    } elseif ($monthsAgeAtRetirement < (62 * 12)) {
                        $monthsEarly = (62 * 12) - $monthsAgeAtRetirement;
                    }

                    $mraMultiplier = 1 - ((($monthsEarly / 12) * 5) / 100);

                    if ($mraMultiplier < 0) {
                        $mraMultiplier = 0;
                    }
                }
            }
        } // ' Special/Regular
        return [
            'annualFERSAnnuity' => $annualFERSAnnuity,
            'mraMultiplier' => $mraMultiplier
        ];
    }


    public function GetCSRSAnnuity($employeeType, $csrsMonthsServiceAtRetirement, $high3Average, $csrsOffsetDate, $retirementDate, $ssMonthlyAtStartAge)
    {

        $annualCSRSAnnuity = 0;

        if (($employeeType == "Regular") || ($employeeType == "eCBPO")) {

            $first5 = 0;
            $next5 = 0;
            $rest = 0;

            if ($csrsMonthsServiceAtRetirement > 503) {
                $csrsMonthsServiceAtRetirement = 503;
            }

            if ($csrsMonthsServiceAtRetirement >= (10 * 12)) {
                $first5 = 5;
                $next5 = 5;
                $rest = ($csrsMonthsServiceAtRetirement - (10 * 12)) / 12;
            } elseif ($csrsMonthsServiceAtRetirement >= (5 * 12)) {
                $first5 = 5;
                $next5 = ($csrsMonthsServiceAtRetirement - (5 * 12)) / 12;
            } elseif ($csrsMonthsServiceAtRetirement >= (5 * 12)) {
                $first5 = $csrsMonthsServiceAtRetirement / 12;
            }

            $annualCSRSAnnuity = ($high3Average * 0.015 * $first5) + ($high3Average * 0.0175 * $next5) + ($high3Average * 0.02 * $rest);
        } else {

            $first20 = 0;
            $rest = 0;

            if ($csrsMonthsServiceAtRetirement > (20 * 12)) {
                $first20 = 20;
                $rest = ($csrsMonthsServiceAtRetirement - (20 * 12)) / 12;
            } else {
                $first20 = $csrsMonthsServiceAtRetirement / 12;
            }

            $annualCSRSAnnuity = ($high3Average * 0.025 * $first20) + ($high3Average * 0.02 * $rest);
        } // ' Special/Regular

        return $annualCSRSAnnuity;
    }

    public function GetCSRSPre1991RefundPenalty($age, $amount)
    {
        $penaltyMultiplier = 0;
        switch ($age) {
            case 40:
                $penaltyMultiplier = 277.6;
                break;
            case 41:
                $penaltyMultiplier = 274.7;
                break;
            case 42:
                $penaltyMultiplier = 272.1;
                break;
            case 43:
                $penaltyMultiplier = 269.1;
                break;
            case 44:
                $penaltyMultiplier = 265.0;
                break;
            case 45:
                $penaltyMultiplier = 260.0;
                break;
            case 46:
                $penaltyMultiplier = 255.1;
                break;
            case 47:
                $penaltyMultiplier = 250.8;
                break;
            case 48:
                $penaltyMultiplier = 245.9;
                break;
            case 49:
                $penaltyMultiplier = 240.3;
                break;
            case 50:
                $penaltyMultiplier = 234.8;
                break;
            case 51:
                $penaltyMultiplier = 230.2;
                break;
            case 52:
                $penaltyMultiplier = 225.9;
                break;
            case 53:
                $penaltyMultiplier = 221.4;
                break;
            case 54:
                $penaltyMultiplier = 216.8;
                break;
            case 55:
                $penaltyMultiplier = 211.9;
                break;
            case 56:
                $penaltyMultiplier = 207.2;
                break;
            case 57:
                $penaltyMultiplier = 202.3;
                break;
            case 58:
                $penaltyMultiplier = 197.6;
                break;
            case 59:
                $penaltyMultiplier = 193.1;
                break;
            case 60:
                $penaltyMultiplier = 188.7;
                break;
            case 61:
                $penaltyMultiplier = 183.7;
                break;
            case 62:
                $penaltyMultiplier = 178.3;
                break;
            case 63:
                $penaltyMultiplier = 173.2;
                break;
            case 64:
                $penaltyMultiplier = 168.2;
                break;
            case 65:
                $penaltyMultiplier = 163.0;
                break;
            case 66:
                $penaltyMultiplier = 157.9;
                break;
            case 67:
                $penaltyMultiplier = 153.1;
                break;
            case 68:
                $penaltyMultiplier = 148.0;
                break;
            case 69:
                $penaltyMultiplier = 142.8;
                break;
            case 70:
                $penaltyMultiplier = 138.0;
                break;
            case 71:
                $penaltyMultiplier = 133.1;
                break;
            case 72:
                $penaltyMultiplier = 128.0;
                break;
            case 73:
                $penaltyMultiplier = 123.1;
                break;
            case 74:
                $penaltyMultiplier = 118.4;
                break;
            case 75:
                $penaltyMultiplier = 113.5;
                break;
            case 76:
                $penaltyMultiplier = 108.2;
                break;
            case 77:
                $penaltyMultiplier = 103.2;
                break;
            case 78:
                $penaltyMultiplier = 98.2;
                break;
            case 79:
                $penaltyMultiplier = 93.1;
                break;
            case 80:
                $penaltyMultiplier = 88.4;
                break;
            case 81:
                $penaltyMultiplier = 83.6;
                break;
            case 82:
                $penaltyMultiplier = 78.4;
                break;
            case 83:
                $penaltyMultiplier = 73.7;
                break;
            case 84:
                $penaltyMultiplier = 69.5;
                break;
            case 85:
                $penaltyMultiplier = 65.8;
                break;
            case 86:
                $penaltyMultiplier = 62.0;
                break;
            case 87:
                $penaltyMultiplier = 57.9;
                break;
            case 88:
                $penaltyMultiplier = 54.0;
                break;
            case 89:
                $penaltyMultiplier = 50.7;
                break;
            case 90:
                $penaltyMultiplier = 47.2;
                break;
            default:
                $penaltyMultiplier = 277.6;
                break;
        }
        return ($amount / $penaltyMultiplier) * 12;
    }

    public function getSBPDetailsPdf($empId) // only FERS system employees will call this function.
    {
        $SBPDetails = [];
        $firstPension = $this->getFirstPension($empId);

        $emp = $this->getById($empId)->toArray();
        // echo "<pre>";
        // print_r($emp);
        // die;
        if ($emp['SystemType'] == 'CSRS' || $emp['SystemType'] == 'CSRS Offset') {
            $SBPDetails = $this->getSBPDetailsCSRSPdf($empId);
        } else {

            $serviceDurationArr = $this->getEarliestRetirement($empId);
            $serviceDuration = $serviceDurationArr['serviceDurationForPension'];

            $bday1 = new \DateTime($emp['eligibility']['DateOfBirth']);
            $bday1 = $bday1->modify('- 1 month'); // The government considers someone to have turned their new age on the day before their actual birthday.
            $minRetAge = $this->getMinumumRetirementAge($empId, 1);
            $mraObj = new \DateTime($minRetAge['minRetirementDate']);
            $mra_interval = $mraObj->diff($bday1);
            $mra_y = $mra_interval->y;
            $mra_m = $mra_interval->m;

            $mraPenalty_arr = $this->getMRAPenalty($empId);
            $mraPenalty = $mraPenalty_arr['first_pension_mra10_penalty'];
            $monthlyMraPenalty = $mraPenalty / 12;

            $nonDeductionPanelty = $this->nonDeductionPanelty($empId);
            $refundedPanelty = $this->calcRefundedPanelty($empId);

            $empConf = $this->getEmployeeConf($empId);

            $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
            $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
            $retInterval = $retDate->diff($bday);
            $retirementAge = $retInterval->y;
            $cola = $empConf['FERSCola'];
            $years_in_retirement = 0;

            $cumulative_survivor_benefit_cost_50 = 0;
            $cumulative_survivor_benefit_cost_25 = 0;

            for ($i = $retirementAge; $i <= 90; $i++) {

                if ($emp['RetirementType'] == "Deferred") {
                    $monthlygrossPension = 0;
                    if ($serviceDuration >= 30) {
                        if ($i == $mra_y) {
                            $monthlygrossPension = $firstPension / 12;
                        } elseif ($i > $mra_y) {
                            $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                            $monthlygrossPension = $firstPension / 12;
                        }
                    } elseif ($serviceDuration >= 20) {
                        if ($i == 60) {
                            $monthlygrossPension = $firstPension / 12;
                        } elseif ($i > 60) {
                            $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                            $monthlygrossPension = $firstPension / 12;
                        }
                    } elseif (($serviceDuration >= 5) || ($serviceDuration <= 19)) {
                        if ($i == 62) {
                            $monthlygrossPension = $firstPension / 12;
                        } elseif ($i > 62) {
                            $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                            $monthlygrossPension = $firstPension / 12;
                        }
                    }
                } else {
                    if ($emp['empType'] == "Other") {
                        if ($i > $retirementAge) {
                            $firstPension = $firstPension + ($firstPension * ($cola / 100));
                        }
                    } else {
                        if (($i > $retirementAge) && ($i >= 62)) {
                            $firstPension = $firstPension + ($firstPension * ($cola / 100));
                        }
                    }

                    $monthlygrossPension = $firstPension / 12;
                }

                if ($monthlygrossPension > 0) {
                    $monthlyNetPension = $monthlygrossPension - ($monthlyMraPenalty + ($nonDeductionPanelty / 12) + ($refundedPanelty / 12));
                } else {
                    $monthlyNetPension = 0;
                }

                if ($emp['MaritalStatusType'] == "Married") {
                    $monthly_survivor_benefits_50 = $monthlyNetPension * (50 / 100);
                    $monthly_survivor_benefit_cost_50 = $monthlyNetPension * (10 / 100);
                    $annual_survivor_benefit_cost_50 = $monthly_survivor_benefit_cost_50 * 12;
                    $cumulative_survivor_benefit_cost_50 = $cumulative_survivor_benefit_cost_50 + $annual_survivor_benefit_cost_50;

                    $monthly_survivor_benefits_25 = $monthlyNetPension * (25 / 100);
                    $monthly_survivor_benefit_cost_25 = $monthlyNetPension * (5 / 100);
                    $annual_survivor_benefit_cost_25 = $monthly_survivor_benefit_cost_25 * 12;
                    $cumulative_survivor_benefit_cost_25 = $cumulative_survivor_benefit_cost_25 + $annual_survivor_benefit_cost_25;
                } else {
                    $monthly_survivor_benefits_50 = 0;
                    $monthly_survivor_benefit_cost_50 = 0;
                    $annual_survivor_benefit_cost_50 = 0;
                    $cumulative_survivor_benefit_cost_50 = 0;

                    $monthly_survivor_benefits_25 = 0;
                    $monthly_survivor_benefit_cost_25 = 0;
                    $annual_survivor_benefit_cost_25 = 0;
                    $cumulative_survivor_benefit_cost_25 = 0;
                }

                $row['years_in_retirement'] = $years_in_retirement++;
                $row['age'] = $i;

                $row['monthly_pension_gross'] = $monthlyNetPension;
                $row['monthly_survivor_benefits_50'] = $monthly_survivor_benefits_50;
                $row['monthly_survivor_benefit_cost_50'] = $monthly_survivor_benefit_cost_50;
                $row['annual_survivor_benefit_cost_50'] = $annual_survivor_benefit_cost_50;
                $row['cumulative_survivor_benefit_cost_50'] = $cumulative_survivor_benefit_cost_50;

                $row['monthly_survivor_benefits_25'] = $monthly_survivor_benefits_25;
                $row['monthly_survivor_benefit_cost_25'] = $monthly_survivor_benefit_cost_25;
                $row['annual_survivor_benefit_cost_25'] = $annual_survivor_benefit_cost_25;
                $row['cumulative_survivor_benefit_cost_25'] = $cumulative_survivor_benefit_cost_25;

                array_push($SBPDetails, $row);
            }
        }

        return $SBPDetails;
    }

    public function getSBPDetailsCSRSPdf($empId) // only FERS system employees will call this function.
    {
        $SBPDetails = [];
        $firstPension = $this->getFirstPension($empId);
        $emp = $this->getById($empId)->toArray();
        $empConf = $this->getEmployeeConf($empId);


        $serviceDurationArr = $this->getEarliestRetirement($empId);
        $serviceDuration = $serviceDurationArr['serviceDurationForPension'];

        $bday1 = new \DateTime($emp['eligibility']['DateOfBirth']);
        $bday1 = $bday1->modify('- 1 month'); // The government considers someone to have turned their new age on the day before their actual birthday.
        $minRetAge = $this->getMinumumRetirementAge($empId, 1);
        $mraObj = new \DateTime($minRetAge['minRetirementDate']);
        $mra_interval = $mraObj->diff($bday1);
        $mra_y = $mra_interval->y;
        $mra_m = $mra_interval->m;


        $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
        $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
        $retInterval = $retDate->diff($bday);
        $retirementAge = $retInterval->y;
        $cola = $empConf['CSRSCola'];
        $years_in_retirement = 0;
        $earlyOutPenalty = $this->getEarlyOutPenalty($empId);
        $monthlyEarlyOutPenalty = $earlyOutPenalty / 12;


        $nonDeductionPanelty = $this->nonDeductionPanelty($empId);
        $refundedPanelty = $this->calcRefundedPanelty($empId);

        $csrsOffsetPenalty = $this->getCsrsOffsetPenalty($empId);
        // dd($firstPension);
        if ($retirementAge >= 62) {
            $firstPension = ($firstPension / 12) - $csrsOffsetPenalty;
            $firstPension = $firstPension * 12;
        }
        // dd($firstPension);
        $cumulative_survivor_benefit_cost_55 = 0;
        $cumulative_survivor_benefit_cost_partial = 0;

        $years_in_retirement = 0;
        for ($i = $retirementAge; $i <= 90; $i++) {
            if ($i > $retirementAge) {
                $firstPension = $firstPension + ($firstPension * ($cola / 100));
            }
            // if ($i == 62) {
            //     $netPension = $firstPension - ($csrsOffsetPenalty * 12);
            // } else {
            //     $netPension = $firstPension;
            // }


            if ($emp['RetirementType'] == "Deferred") {
                $monthlygrossPension = 0;
                if ($serviceDuration >= 30) {
                    if ($i == $mra_y) {
                        $monthlygrossPension = $firstPension / 12;
                    } elseif ($i > $mra_y) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                        $monthlygrossPension = $firstPension / 12;
                    }
                } elseif ($serviceDuration >= 20) {
                    if ($i == 60) {
                        $monthlygrossPension = $firstPension / 12;
                    } elseif ($i > 60) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                        $monthlygrossPension = $firstPension / 12;
                    }
                } elseif (($serviceDuration >= 5) || ($serviceDuration <= 19)) {
                    if ($i == 62) {
                        $monthlygrossPension = $firstPension / 12;
                    } elseif ($i > 62) {
                        $firstPension = $firstPension + ($firstPension * ($cola / 100)); //
                        $monthlygrossPension = $firstPension / 12;
                    }
                }
            } else {
                $netPension = $firstPension;
                $monthlygrossPension = $netPension / 12;
            }


            if (($monthlygrossPension > 0) && ($emp['MaritalStatusType'] == "Married")) {
                $monthlyNetPension = $monthlygrossPension - ($monthlyEarlyOutPenalty + ($nonDeductionPanelty / 12) + ($refundedPanelty / 12));
                $monthly_survivor_benefits_55 = $monthlyNetPension * (55 / 100);

                if ($monthlyNetPension <= 300) {
                    $monthly_survivor_benefit_cost_55 = 7.50;
                } else {
                    $costFirstPart = 7.50;
                    $costSecondPart = ($monthlyNetPension - 300) * (10 / 100);
                    $monthly_survivor_benefit_cost_55 = $costFirstPart + $costSecondPart;
                }

                $annual_survivor_benefit_cost_55 = $monthly_survivor_benefit_cost_55 * 12;
                $cumulative_survivor_benefit_cost_55 = $cumulative_survivor_benefit_cost_55 + $annual_survivor_benefit_cost_55;

                $monthly_survivor_benefits_partial = 165;
                $monthly_survivor_benefit_cost_partial = 8;
                $annual_survivor_benefit_cost_partial = 90;
                $cumulative_survivor_benefit_cost_partial = $cumulative_survivor_benefit_cost_partial + $annual_survivor_benefit_cost_partial;
            } else {
                $monthlyNetPension = $monthlygrossPension - ($monthlyEarlyOutPenalty + ($nonDeductionPanelty / 12) + ($refundedPanelty / 12));

                $monthly_survivor_benefits_55 = 0;

                $monthly_survivor_benefit_cost_55 = 0;

                $annual_survivor_benefit_cost_55 = 0;
                $cumulative_survivor_benefit_cost_55 = 0;

                $monthly_survivor_benefits_partial = 0;
                $monthly_survivor_benefit_cost_partial = 0;
                $annual_survivor_benefit_cost_partial = 0;
                $cumulative_survivor_benefit_cost_partial = 0;
            }

            $row['years_in_retirement'] = $years_in_retirement++;
            $row['age'] = $i;

            $row['monthly_pension_gross'] = $monthlyNetPension;
            $row['monthly_survivor_benefits_55'] = $monthly_survivor_benefits_55;
            $row['monthly_survivor_benefit_cost_55'] = $monthly_survivor_benefit_cost_55;
            $row['annual_survivor_benefit_cost_55'] = $annual_survivor_benefit_cost_55;
            $row['cumulative_survivor_benefit_cost_55'] = $cumulative_survivor_benefit_cost_55;

            $row['monthly_survivor_benefits_partial'] = $monthly_survivor_benefits_partial;
            $row['monthly_survivor_benefit_cost_partial'] = $monthly_survivor_benefit_cost_partial;
            $row['annual_survivor_benefit_cost_partial'] = $annual_survivor_benefit_cost_partial;
            $row['cumulative_survivor_benefit_cost_partial'] = $cumulative_survivor_benefit_cost_partial;

            array_push($SBPDetails, $row);
        }

        return $SBPDetails;
    }

    public function getSBPDetailsCSRSCalculateAndDebug($empId) // only FERS system employees will call this function.
    {
        $SBPDetails = [];
        $firstPension = $this->getFirstPension($empId);

        $emp = $this->getById($empId)->toArray();
        $empConf = $this->getEmployeeConf($empId);

        $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
        $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
        $retInterval = $retDate->diff($bday);
        $retirementAge = $retInterval->y;
        $cola = $empConf['CSRSCola'];
        $years_in_retirement = 0;


        $cumulative_survivor_benefit_cost_55 = 0;
        $cumulative_survivor_benefit_cost_partial = 0;
        for ($i = $retirementAge; $i <= 90; $i++) {

            if ($i > $retirementAge) {
                $firstPension = $firstPension + ($firstPension * ($cola / 100));
            }

            $monthlygrossPension = $firstPension / 12;
            $monthly_survivor_benefits_55 = $monthlygrossPension * (55 / 100);

            if ($monthlygrossPension <= 300) {
                $monthly_survivor_benefit_cost_55 = 7.50;
            } else {
                $costFirstPart = 7.50;
                $costSecondPart = ($monthlygrossPension - 300) * (10 / 100);
                $monthly_survivor_benefit_cost_55 = $costFirstPart + $costSecondPart;
            }

            $annual_survivor_benefit_cost_55 = $monthly_survivor_benefit_cost_55 * 12;
            $cumulative_survivor_benefit_cost_55 = $cumulative_survivor_benefit_cost_55 + $annual_survivor_benefit_cost_55;

            $monthly_survivor_benefits_partial = 165;
            $monthly_survivor_benefit_cost_partial = 8;
            $annual_survivor_benefit_cost_partial = 90;
            $cumulative_survivor_benefit_cost_partial = $cumulative_survivor_benefit_cost_partial + $annual_survivor_benefit_cost_partial;

            $row['years_in_retirement'] = $years_in_retirement++;
            $row['age'] = $i;

            $row['monthly_pension_gross'] = $monthlygrossPension;
            $row['monthly_survivor_benefits_55'] = $monthly_survivor_benefits_55;
            $row['monthly_survivor_benefit_cost_55'] = $monthly_survivor_benefit_cost_55;
            $row['annual_survivor_benefit_cost_55'] = $annual_survivor_benefit_cost_55;
            $row['cumulative_survivor_benefit_cost_55'] = $cumulative_survivor_benefit_cost_55;

            $row['monthly_survivor_benefits_partial'] = $monthly_survivor_benefits_partial;
            $row['monthly_survivor_benefit_cost_partial'] = $monthly_survivor_benefit_cost_partial;
            $row['annual_survivor_benefit_cost_partial'] = $annual_survivor_benefit_cost_partial;
            $row['cumulative_survivor_benefit_cost_partial'] = $cumulative_survivor_benefit_cost_partial;

            array_push($SBPDetails, $row);
        }

        return $SBPDetails;
    }

    public function getHealthBenefitPdf($empId = null)
    {
        $emp = $this->getById($empId)->toArray();
        $bday = new \DateTime($emp['eligibility']['DateOfBirth']);
        $retDate = new \DateTime($emp['eligibility']['RetirementDate']);
        $retInterval = $retDate->diff($bday);
        $retirementAge = $retInterval->y;

        $rowYear = date('Y');
        $retYear = date('Y', strtotime($emp['eligibility']['RetirementDate']));

        $today = new \DateTime();
        $ageInterval = $bday->diff($today);
        $emp_age = $ageInterval->y;
        // echo "<pre>"; print_r($emp['DoesNotMeetFiveYear']); exit;
        $years_in_retirement = 0;
        $hb_premium_inc = EmployeeConfig::where('EmployeeId', $empId)->where('ConfigType', 'FEHBAveragePremiumIncrease')->first();

        if (!$hb_premium_inc) {
            $hb_premium_inc = AppLookup::where('AppLookupTypeName', 'EmployeeConfig')->where('AppLookupName', 'FEHBAveragePremiumIncrease')->first()->AppLookupDescription;
        } else {
            $hb_premium_inc = $hb_premium_inc->ConfigValue;
        }

        $is_postal = $emp['PostalEmployee'];
        /** */
        $biWeeklyHealthPremium = $emp['HealthPremium'];
        $biWeeklyDentalPremium = $emp['DentalPremium'];
        $biWeeklyVisionPremium = $emp['VisionPremium'];
        $biWeeklyDentalAndVision = $emp['dental_and_vision'];

        $yearlyHealthPremium = $emp['HealthPremium'] * 26;
        $yearlyDentalPremium = $emp['DentalPremium'] * 26;
        $yearlyVisionPremium = $emp['VisionPremium'] * 26;
        $yearlyDentalAndVision = $emp['dental_and_vision'] * 26;

        $monthlyHealthPremium = $yearlyHealthPremium / 12;
        $monthlyDentalPremium = $yearlyDentalPremium / 12;
        $monthlyVisionPremium = $yearlyVisionPremium / 12;
        $monthlyDentalAndVision = $yearlyDentalAndVision / 12;

        $biWeeklyTotalPremium = $biWeeklyHealthPremium + $biWeeklyDentalPremium + $biWeeklyVisionPremium + $biWeeklyDentalAndVision;
        $yearlyTotalPremium = $yearlyHealthPremium + $yearlyDentalPremium + $yearlyVisionPremium + $yearlyDentalAndVision;
        $monthlyTotalPremium = $monthlyHealthPremium + $monthlyDentalPremium + $monthlyVisionPremium + $monthlyDentalAndVision;
        // echo $emp_age . " -- " . $retirementAge . " -- " . $is_postal;
        // die;
        if ($emp_age >= $retirementAge) {
            // Govt removed Postal employee rule, they ar now treated as regular employees.
            // if ($is_postal == 1) {
            //     $biWeeklyHealthPremium = $biWeeklyHealthPremium + ($biWeeklyHealthPremium * (15 / 100));
            //     $biWeeklyDentalPremium = $biWeeklyDentalPremium + ($biWeeklyDentalPremium * (15 / 100));
            //     $biWeeklyVisionPremium = $biWeeklyVisionPremium + ($biWeeklyVisionPremium * (15 / 100));
            //     $biWeeklyDentalAndVision = $biWeeklyDentalAndVision + ($biWeeklyDentalAndVision * (15 / 100));

            //     $yearlyHealthPremium = $yearlyHealthPremium + ($yearlyHealthPremium * (15 / 100));
            //     $yearlyDentalPremium = $yearlyDentalPremium + ($yearlyDentalPremium * (15 / 100));
            //     $yearlyVisionPremium = $yearlyVisionPremium + ($yearlyVisionPremium * (15 / 100));
            //     $yearlyDentalAndVision = $yearlyDentalAndVision + ($yearlyDentalAndVision * (15 / 100));

            //     $monthlyHealthPremium = $monthlyHealthPremium + ($monthlyHealthPremium * (15 / 100));
            //     $monthlyDentalPremium = $monthlyDentalPremium + ($monthlyDentalPremium * (15 / 100));
            //     $monthlyVisionPremium = $monthlyVisionPremium + ($monthlyVisionPremium * (15 / 100));
            //     $monthlyDentalAndVision = $monthlyDentalAndVision + ($monthlyDentalAndVision * (15 / 100));

            //     $biWeeklyTotalPremium = $biWeeklyTotalPremium + ($biWeeklyTotalPremium * (15 / 100));
            //     $yearlyTotalPremium = $yearlyTotalPremium + ($yearlyTotalPremium * (15 / 100));
            //     $monthlyTotalPremium = $monthlyTotalPremium + ($monthlyTotalPremium * (15 / 100));

            //     // echo $biWeeklyTotalPremium . " -- " . $yearlyTotalPremium . " -- " . $monthlyTotalPremium;
            //     // die;
            // }
            if ($emp['DoesNotMeetFiveYear'] == 1) {
                $biWeeklyHealthPremium = 0;

                $yearlyHealthPremium = 0;

                $monthlyHealthPremium = 0;

                $biWeeklyTotalPremium = $biWeeklyHealthPremium + $biWeeklyDentalPremium + $biWeeklyVisionPremium + $biWeeklyDentalAndVision;
                $yearlyTotalPremium = $yearlyHealthPremium + $yearlyDentalPremium + $yearlyVisionPremium + $yearlyDentalAndVision;
                $monthlyTotalPremium = $monthlyHealthPremium + $monthlyDentalPremium + $monthlyVisionPremium + $monthlyDentalAndVision;
            }
            $years_in_retirement = $emp_age - $retirementAge;
        }

        $accum = 0;
        $data = [];
        $count = 0;
        $age = $emp_age;
        // echo $emp_age . " --- " . $retirementAge;
        // die;

        for ($i = $emp_age; $i <= 90; $i++) {
            $rowYear++;
            if (($emp['retirementType'] == "Deferred") && ($i > $retirementAge)) {
                $row['age'] = $i;
                $age = $age + 1;
                $row['years_in_retirement'] = $years_in_retirement;
                // if ($rowYear >= $retYear) {
                //     $years_in_retirement = $years_in_retirement + 1;
                // }
                if ($i > $retirementAge) {
                    $years_in_retirement = $years_in_retirement + 1;
                }

                if ($i > $emp_age) {
                    $count = $count + 1;
                }
                $row['biWeeklyHealthPremium'] = 0;
                $row['biWeeklyDentalPremium'] = 0;
                $row['biWeeklyVisionPremium'] = 0;
                $row['biWeeklyDentalAndVision'] = 0;
                $row['yearlyHealthPremium'] = 0;
                $row['yearlyDentalPremium'] = 0;
                $row['yearlyVisionPremium'] = 0;
                $row['yearlyDentalAndVision'] = 0;
                $row['monthlyHealthPremium'] = 0;
                $row['monthlyDentalPremium'] = 0;
                $row['monthlyVisionPremium'] = 0;
                $row['monthlyDentalAndVision'] = 0;
                $row['biWeeklyTotalPremium'] = 0;
                $row['yearlyTotalPremium'] = 0;
                $row['monthlyTotalPremium'] = 0;
                $row['accum'] = 0;
                $row['change'] = 0;
            } else {
                if (($emp_age >= $retirementAge) && ($i == $emp_age) && ($is_postal == 1)) {
                    // already increased total by 15%
                } else {
                    // if ($emp_age < $retirementAge && ($i == $retirementAge) && ($is_postal == 1)) {
                    //     $biWeeklyHealthPremium = $biWeeklyHealthPremium + ($biWeeklyHealthPremium * (15 / 100));
                    //     $biWeeklyDentalPremium = $biWeeklyDentalPremium + ($biWeeklyDentalPremium * (15 / 100));
                    //     $biWeeklyVisionPremium = $biWeeklyVisionPremium + ($biWeeklyVisionPremium * (15 / 100));
                    //     $biWeeklyDentalAndVision = $biWeeklyDentalAndVision + ($biWeeklyDentalAndVision * (15 / 100));
                    //     $yearlyHealthPremium = ($biWeeklyHealthPremium * 26) + (($biWeeklyHealthPremium * 26) * (15 / 100));
                    //     $yearlyDentalPremium = ($biWeeklyDentalPremium * 26) + (($biWeeklyDentalPremium * 26) * (15 / 100));
                    //     $yearlyVisionPremium = ($biWeeklyVisionPremium * 26) + (($biWeeklyVisionPremium * 26) * (15 / 100));
                    //     $yearlyDentalAndVision = ($biWeeklyDentalAndVision * 26) + (($biWeeklyDentalAndVision * 26) * (15 / 100));

                    //     $monthlyHealthPremium = $yearlyHealthPremium / 12;
                    //     $monthlyDentalPremium = $yearlyDentalPremium / 12;
                    //     $monthlyVisionPremium = $yearlyVisionPremium / 12;
                    //     $monthlyDentalAndVision = $yearlyDentalAndVision / 12;

                    //     $biWeeklyTotalPremium = $biWeeklyHealthPremium + $biWeeklyDentalPremium + $biWeeklyVisionPremium + $biWeeklyDentalAndVision;
                    //     $yearlyTotalPremium = $yearlyHealthPremium + $yearlyDentalPremium + $yearlyVisionPremium + $yearlyDentalAndVision;
                    //     $monthlyTotalPremium = $monthlyHealthPremium + $monthlyDentalPremium + $monthlyVisionPremium + $monthlyDentalAndVision;
                    // } else {
                    // if ($i > $emp_age) {
                    $age = $age + 1;
                    $biWeeklyHealthPremium = $biWeeklyHealthPremium + ($hb_premium_inc / 100) * $biWeeklyHealthPremium;
                    $biWeeklyDentalPremium = $biWeeklyDentalPremium + ($hb_premium_inc / 100) * $biWeeklyDentalPremium;
                    $biWeeklyVisionPremium = $biWeeklyVisionPremium + ($hb_premium_inc / 100) * $biWeeklyVisionPremium;
                    $biWeeklyDentalAndVision = $biWeeklyDentalAndVision + ($hb_premium_inc / 100) * $biWeeklyDentalAndVision;
                    $yearlyHealthPremium = $yearlyHealthPremium + ($hb_premium_inc / 100) * $yearlyHealthPremium;
                    $yearlyDentalPremium = $yearlyDentalPremium + ($hb_premium_inc / 100) * $yearlyDentalPremium;
                    $yearlyVisionPremium = $yearlyVisionPremium + ($hb_premium_inc / 100) * $yearlyVisionPremium;
                    $yearlyDentalAndVision = $yearlyDentalAndVision + ($hb_premium_inc / 100) * $yearlyDentalAndVision;
                    $monthlyHealthPremium = $monthlyHealthPremium + ($hb_premium_inc / 100) * $monthlyHealthPremium;
                    $monthlyDentalPremium = $monthlyDentalPremium + ($hb_premium_inc / 100) * $monthlyDentalPremium;
                    $monthlyVisionPremium = $monthlyVisionPremium + ($hb_premium_inc / 100) * $monthlyVisionPremium;
                    $monthlyDentalAndVision = $monthlyDentalAndVision + ($hb_premium_inc / 100) * $monthlyDentalAndVision;

                    // $biWeeklyTotalPremium = $biWeeklyHealthPremium + $biWeeklyDentalPremium + $biWeeklyVisionPremium + $biWeeklyDentalAndVision;
                    // $yearlyTotalPremium = $yearlyHealthPremium + $yearlyDentalPremium + $yearlyVisionPremium + $yearlyDentalAndVision;
                    // $monthlyTotalPremium = $monthlyHealthPremium + $monthlyDentalPremium + $monthlyVisionPremium + $monthlyDentalAndVision;

                    $biWeeklyTotalPremium = $biWeeklyTotalPremium + (($hb_premium_inc / 100) * $biWeeklyTotalPremium);
                    $yearlyTotalPremium = $yearlyTotalPremium + (($hb_premium_inc / 100) * $yearlyTotalPremium);
                    $monthlyTotalPremium = $monthlyTotalPremium + (($hb_premium_inc / 100) * $monthlyTotalPremium);
                    // }
                }


                if ($i >= $retirementAge) {

                    if ($emp['DoesNotMeetFiveYear'] == 1) {
                        $biWeeklyHealthPremium = 0;
                        $yearlyHealthPremium = 0;
                        $monthlyHealthPremium = 0;

                        $biWeeklyTotalPremium = $biWeeklyHealthPremium + $biWeeklyDentalPremium + $biWeeklyVisionPremium + $biWeeklyDentalAndVision;
                        $yearlyTotalPremium = $yearlyHealthPremium + $yearlyDentalPremium + $yearlyVisionPremium + $yearlyDentalAndVision;
                        $monthlyTotalPremium = $monthlyHealthPremium + $monthlyDentalPremium + $monthlyVisionPremium + $monthlyDentalAndVision;
                    }
                }
                if ($i > $retirementAge) {
                    $years_in_retirement = $years_in_retirement + 1;
                }
                $row['years_in_retirement'] = $years_in_retirement;
                // if ($rowYear > $retYear) {
                //     $years_in_retirement = $years_in_retirement + 1;
                // }

                $row['age'] = $i;
                $row['biWeeklyHealthPremium'] = $biWeeklyHealthPremium;
                $row['biWeeklyDentalPremium'] = $biWeeklyDentalPremium;
                $row['biWeeklyVisionPremium'] = $biWeeklyVisionPremium;
                $row['biWeeklyDentalAndVision'] = $biWeeklyDentalAndVision;
                $row['yearlyHealthPremium'] = $yearlyHealthPremium;
                $row['yearlyDentalPremium'] = $yearlyDentalPremium;
                $row['yearlyVisionPremium'] = $yearlyVisionPremium;
                $row['yearlyDentalAndVision'] = $yearlyDentalAndVision;
                $row['monthlyHealthPremium'] = $monthlyHealthPremium;
                $row['monthlyDentalPremium'] = $monthlyDentalPremium;
                $row['monthlyVisionPremium'] = $monthlyVisionPremium;
                $row['monthlyDentalAndVision'] = $monthlyDentalAndVision;
                $row['biWeeklyTotalPremium'] = $biWeeklyTotalPremium;
                $row['yearlyTotalPremium'] = $yearlyTotalPremium;
                $row['monthlyTotalPremium'] = $monthlyTotalPremium;
                $row['accum'] = $accum = $accum + $yearlyTotalPremium;
                $change = 0;
                if ($i > $emp_age) {
                    $count = $count + 1;
                    $change = $accum - $data[$count - 1]['accum'];
                }
                $row['change'] = $change;
            }
            $data[$count] = $row;
        }
        return $data;
    }

    public function updateDateCompleted($empId = null)
    {
        $date = date("Y-m-d");
        $emp = Employee::find($empId);
        if (!$emp) {
            return false;
        }
        $emp->DateCompleted = $date;
        if ($emp->save()) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteCase($id = null)
    {
        $employee = Employee::find($id);
        if (!$employee) {
            return false;
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $res = Employee::where('EmployeeId', $id)->delete();
        $res1 = $this->deleteDependentsByEmpId($id);
        $res1 = $this->deleteEligibilityDetails($id);
        $res1 = $this->deleteEmployeeConfigs($id);
        $res1 = $this->deleteEmployeeFiles($id);
        $res1 = $this->deleteEmployeeFegli($id);
        $res1 = $this->deleteOtherServices($id);
        $res1 = $this->deleteEmpReportScenarios($id);
        $res1 = $this->deleteEmpTSP($id);
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteDependentsByEmpId($emp_id = null)
    {
        return FEGLIDependent::where('EmployeeId', $emp_id)->delete();
    }

    public function deleteEligibilityDetails($emp_id = null)
    {
        return Eligibility::where('EmployeeId', $emp_id)->delete();
    }

    public function deleteEmployeeConfigs($emp_id = null)
    {
        return EmployeeConfig::where('EmployeeId', $emp_id)->delete();
    }

    public function deleteEmployeeFiles($emp_id = null)
    {
        return EmployeeFile::where('EmployeeId', $emp_id)->delete();
    }

    public function deleteEmployeeFegli($emp_id = null)
    {
        return Fegli::where('EmployeeId', $emp_id)->delete();
    }

    public function deleteOtherServices($emp_id = null)
    {
        MilitaryService::where('EmployeeId', $emp_id)->delete();
        NonDeductionService::where('EmployeeId', $emp_id)->delete();
        PartTimeService::where('EmployeeId', $emp_id)->delete();
        RefundedService::where('EmployeeId', $emp_id)->delete();
        return true;
    }

    public function deleteEmpReportScenarios($emp_id = null)
    {
        ReportScenario::where('EmployeeId', $emp_id)->delete();
        return true;
    }

    public function deleteEmpTSP($emp_id = null)
    {
        Tsp::where('EmployeeId', $emp_id)->delete();
        return true;
    }

    public function updateHistory($empId = null, $history_fields = [], $row_id = 0, $tab_name)
    {
        if (empty($history_fields)) {
            return true;
        }
        // echo "<pre>";
        // print_r($history_fields);
        // die;
        foreach ($history_fields as $table_name => $values) {
            foreach ($values as $val) {
                $history_column = HistoryColumn::where([
                    'column_name' => $val['column_name'],
                    'table_name' => $table_name,
                    'tab_name' => $tab_name
                ])->first();
                if (!$history_column) {
                    continue;
                }
                $change = new HistoryChange();
                $change->emp_id = $empId;
                $change->column_id = $history_column->id;
                $change->row_id = $row_id;
                $change->old_val = $val['old_value'];
                $change->new_val = $val['new_value'];
                $change->updated_by = Auth::user()->id;
                if ($change->save()) {
                    continue;
                }
            }
        }
    }

    public function getEmployeeTabHistory($empId, $tab_name)
    {
        $history_columns = HistoryColumn::select(['id', 'column_name', 'table_name'])->where('tab_name', $tab_name)->get()->toArray();
        $change_history = [];
        $col = [];
        foreach ($history_columns as $column) {
            // $col = DB::select("select * from history_changes where 'column_id' = " . $column['id'] . " and 'emp_id' = " . $empId . ") group by 'row_id'");
            // print_r($col);
            // die;
            $col = HistoryChange::with(['updated_by_user'])->where([
                'column_id' => $column['id'],
                'emp_id' => $empId
            ])->get()->toArray();
            $col['column_name'] = $column['column_name'];
            $col['table_name'] = $column['table_name'];
            $change_history[] = $col;
        }
        // ******NOTE:::: add row_id condition for dependents and other services

        // echo "<pre>";
        // print_r($change_history);
        // die;

        if ($tab_name == "basic_info") {
            $basic_tab_config_columns = [
                'SystemTypeId', 'RetirementTypeId', 'EmployeeTypeId', 'MaritalStatusTypeId'
            ];
            $basic_tab_config_columns_name = [
                'SystemType', 'RetirementType', 'EmployeeType', 'MaritalStatusType'
            ];
            $position = array_search('abc', $basic_tab_config_columns);

            $final_history_arr = [];
            foreach ($change_history as $index => $column_data) {
                $column_name = $column_data['column_name'];
                $table_name = $column_data['table_name'];
                unset($column_data['column_name']);
                unset($column_data['table_name']);
                // echo $column_name . " :: " . count($column_data) . "<br>";
                // continue;
                $new_column_name = $column_name;
                foreach ($column_data as $k => $row) {
                    if ($column_name == "AdvisorId") {
                        $new_column_name = "Advisor";
                        $column_data[$k]['old_val_name'] = $this->getAdvisorName($row['old_val']);
                        $column_data[$k]['new_val_name'] = $this->getAdvisorName($row['new_val']);
                    } elseif (in_array($column_name, $basic_tab_config_columns)) {
                        $position = array_search($column_name, $basic_tab_config_columns);
                        if ($position) {
                            $new_column_name = $basic_tab_config_columns_name[$position];
                        }
                        $column_data[$k]['old_val_name'] = $this->getConfigurationName($row['old_val']);
                        $column_data[$k]['new_val_name'] = $this->getConfigurationName($row['new_val']);


                        // echo $column_name . ": " . $column_data[$k]['old_val_name'] . " :: " . $column_data[$k]['new_val_name'] . "<br>";
                    } else {
                        $column_data[$k]['old_val_name'] = $row['old_val'];
                        $column_data[$k]['new_val_name'] = $row['new_val'];
                    }
                }
                $column_data['column_name'] = $new_column_name;
                $final_history_arr[$table_name][] = $column_data;
            }
        } else { //if ($tab_name == "retirement_eligibility") {
            // retirement tab, other services, PartTime services
            foreach ($change_history as $index => $column_data) {
                $column_name = $column_data['column_name'];
                $table_name = $column_data['table_name'];
                unset($column_data['column_name']);
                unset($column_data['table_name']);
                $new_column_name = $column_name;
                foreach ($column_data as $k => $row) {
                    $column_data[$k]['old_val_name'] = $row['old_val'];
                    $column_data[$k]['new_val_name'] = $row['new_val'];
                }
                $column_data['column_name'] = $new_column_name;
                $final_history_arr[$table_name][] = $column_data;
            }
        }

        return $final_history_arr;
    }

    public function getConfigurationName($id = null)
    {
        if ($id == null) {
            return null;
        }
        $row = AppLookup::where('AppLookupId', $id)->first();
        if ($row) {
            $name = $row->AppLookupName;
        } else {
            $name = '';
        }
        return $name;
    }

    public function getAdvisorName($adv_id = null)
    {
        $advisor = Advisor::where('AdvisorId', $adv_id)->first();
        if ($advisor) {
            $advisor_name = $advisor->AdvisorName;
        } else {
            $advisor_name = '';
        }
        return $advisor_name;
    }

    public function getHigh3AvgAllScenarios($empId = null)
    {
        $high3_arr = [];
        $scenarios = ReportScenario::where('EmployeeId', $empId)->orderBy('ScenarioNo', 'ASC')->get();
        // if ($scenarios->count() > 0 && $scenarios[0]->High3Average > 0) {
        //     foreach ($scenarios as $sce) {
        //         $high3_arr[$sce->ScenarioNo] = $sce->High3Average;
        //     }
        // } else {
        $empConf = $this->getEmployeeConf($empId);
        if ($empConf['SalaryIncreaseDefault'] == "") {
            $empConf['SalaryIncreaseDefault'] = 1.41;
        }
        $res = $this->calcProjectedHigh3Average($empId, 1);
        $high3_arr[1] = $high3 = $res['projectedHigh3Avg'];
        for ($scenario = 2; $scenario <= 5; $scenario++) {
            $high3 = $high3 + ($high3 * $empConf['SalaryIncreaseDefault'] / 100);
            $high3_arr[$scenario] = $high3;
        }
        // }
        return $high3_arr;
    }

    public function makeDuplicate($empId = null)
    {
        DB::beginTransaction();
        try {
            $emp = Employee::find($empId);

            if (!$emp) {
                return false;
            }
            $new_emp = $emp->replicate();
            $new_emp->DateCompleted = null;
            $new_emp->created_by = Auth::user()->id;
            if ($new_emp->save()) {
                // $new_emp->EmployeeId;
                $empEligibility = Eligibility::find($empId);
                if ($empEligibility) {
                    $newEmpEligibility = $empEligibility->replicate();
                    $newEmpEligibility->EmployeeId = $new_emp->EmployeeId;
                    $newEmpEligibility->save();
                    // special services
                    $militartServices = MilitaryService::where('EmployeeId', $empId)->get();
                    if ($militartServices->count() > 0) {
                        foreach ($militartServices as $service) {
                            $newMilServ = $service->replicate();
                            $newMilServ->EmployeeId = $new_emp->EmployeeId;
                            $newMilServ->save();
                        }
                    }

                    $nonDeductionServices = NonDeductionService::where('EmployeeId', $empId)->get();
                    if ($nonDeductionServices->count() > 0) {
                        foreach ($nonDeductionServices as $ndser) {
                            $newNDSer = $ndser->replicate();
                            $newNDSer->EmployeeId = $new_emp->EmployeeId;
                            $newNDSer->save();
                        }
                    }

                    $refundedServices = RefundedService::where('EmployeeId', $empId)->get();
                    if ($refundedServices->count() > 0) {
                        foreach ($refundedServices as $rService) {
                            $newRService = $rService->replicate();
                            $newRService->EmployeeId = $new_emp->EmployeeId;
                            $newRService->save();
                        }
                    }
                }
                $partTimeJobs = PartTimeService::where('EmployeeId', $empId)->get();
                if ($partTimeJobs->count() > 0) {
                    foreach ($partTimeJobs as $job) {
                        $newJob = $job->replicate();
                        $newJob->EmployeeId = $new_emp->EmployeeId;
                        $newJob->save();
                    }
                }
                $tsp = Tsp::find($empId);
                if ($tsp) {
                    $new_tsp = $tsp->replicate();
                    $new_tsp->EmployeeId = $new_emp->EmployeeId;
                    $new_tsp->save();
                }
                $fegli = Fegli::find($empId);
                if ($fegli) {
                    $new_fegli = $fegli->replicate();
                    $new_fegli->EmployeeId = $new_emp->EmployeeId;
                    $new_fegli->save();
                }
                $dependents = FEGLIDependent::where('EmployeeId', $empId)->get();
                if ($dependents->count() > 0) {
                    foreach ($dependents as $dep) {
                        $new_dep = $dep->replicate();
                        $new_dep->EmployeeId = $new_emp->EmployeeId;
                        $new_dep->save();
                    }
                }
                $empConfigs = EmployeeConfig::where('EmployeeId', $empId)->get();
                if ($empConfigs->count() > 0) {
                    foreach ($empConfigs as $conf) {
                        $new_emp_conf = $conf->replicate();
                        $new_emp_conf->EmployeeId = $new_emp->EmployeeId;
                        $new_emp_conf->save();
                    }
                }
                DB::commit();
                return true;
            }
        } catch (\Exception $e) {
            DB::rollback();
        }
        return false;
    }

    public function deleteMilitaryService($service_id)
    {
        return MilitaryService::where('MilitaryServiceId', $service_id)->delete();
    }

    public function deleteNonDeductionService($service_id)
    {
        return NonDeductionService::where('NonDeductionServiceId', $service_id)->delete();
    }

    public function deleteRefundedService($service_id)
    {
        return RefundedService::where('RefundedServiceId', $service_id)->delete();
    }

    public function getCurrentSalary($employee, $current_sal)
    {
        $reportYear = date('Y', strtotime($employee['ReportDate']));

        if ($reportYear < date('Y')) {
            $configInc  = $this->getEmployeeConf($employee['EmployeeId']);
            for ($y = $reportYear + 1; $y <= date('Y'); $y++) {
                if ($y < date('Y')) {
                    $salIncreasePercentage = $configInc['SalaryIncreaseDefault'];
                } else {
                    $salIncreasePercentage = $configInc['SalaryIncrease'];
                }
                $current_sal = $current_sal + ($current_sal * ($salIncreasePercentage / 100));
            }
        }
        return $current_sal;
    }

    public function updateNotes($empId, $notes)
    {
        $emp = Employee::find($empId);
        $emp->notes = $notes;
        return $emp->save();
    }
}
