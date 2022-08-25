@extends('admin.layouts.admin_layout')
@section('style')
<style>

</style>
@endsection
@section('content')
@include('admin.layouts.admin_top_menu')
<section class="edit_advisor_wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @include('admin.layouts.sections.nameTitle')
            </div>
        </div>
        <div class="row employee_details_wrap">
            <div class="col-md-2">
                <div class="employee_detail_sidebar">
                    @include('admin.layouts.emp_details_menu')
                </div>
            </div>
            <div class="col-md-10 employee_detail_block_wrap">
                <div class="row">
                    <div class="col-md-12">
                        <div class="scenarios_link">
                            <ul class="nav nav-pills">
                                @php
                                $i = 1;
                                @endphp
                                @forelse($scenarios as $scenario)
                                <li class="{{($URLscenariono == $i) ? 'active' : ''}}"><a href="{{url('/employee/calculate_and_debug/'.$scenario['EmployeeId'].'/'.$scenario['ScenarioNo'].'')}}">{{ $scenario['ScenarioNo'] }}</a></li>
                                @php
                                $i++;
                                @endphp
                                @empty
                                @endforelse
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">Scenario Information</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="basic_info_form_wrap">
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Scenario: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{$scenarioData1['scenario_no'] ?? ''}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Report Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ isset($scenarioData1['report_date']) ? date('m/d/Y', strtotime($scenarioData1['report_date'])) : '' }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Retirement Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ isset($scenarioData1['retirement_date']) ? date('m/d/Y', strtotime($scenarioData1['retirement_date'])) : ''}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Earliest Eligible Retirement Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ isset($scenarioData1['earliest_eligible_retirement_date']) ? date('m/d/Y', strtotime($scenarioData1['earliest_eligible_retirement_date'])) : '' }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">SCD: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ isset($scenarioData1['scd']) ? date('m/d/Y', strtotime($scenarioData1['scd'])) : '' }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Eligibility SCD: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ isset($scenarioData1['elibility_scd']) ? date('m/d/Y', strtotime($scenarioData1['elibility_scd'])) : '' }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Annuity SCD: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ isset($scenarioData1['retirement_date']) ? date('m/d/Y', strtotime($scenarioData1['retirement_date'])) : '' }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">DOB: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ isset($scenarioData1['dob']) ? date('m/d/Y', strtotime($scenarioData1['dob'])) : '' }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="row in_gap">
                            <div class="info_heading">
                                <p class="text-left">Report Dates</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <tr>
                                        <th>Report Year</th>
                                        <th>Age</th>
                                        <th>Years Service</th>
                                        <th>Retired</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($report_dates as $key => $row)
                                    <tr>
                                        <td style="text-align: center;">{{$row['report_year']}}</td>
                                        <td style="text-align: center;">{{$row['age']}}</td>
                                        <td style="text-align: center;">{{$row['years_in_service']}}</td>
                                        <td style="text-align: center;">{{($row['is_retired'])}}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4"></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="row in_gap">
                            <div class="info_heading">
                                <p class="text-left">Salary</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <tr>
                                        <th>Year</th>
                                        <th>Age</th>
                                        <th>Monthly</th>
                                        <th>Yearly</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($salReport as $key => $row)
                                    <tr>
                                        <td style="text-align: center;">{{$row['year']}}</td>
                                        <td style="text-align: center;">{{$row['age']}}</td>
                                        <td style="text-align: center;">${{round($row['monthly'], 2)}}</td>
                                        <td style="text-align: center;">${{round($row['yearly'], 2)}}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4"></td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Annuity starts -->
                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">Annuity</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="basic_info_form_wrap">
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Leave SCD: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{date('m/d/Y', strtotime($scenarioData2['leave_scd']))}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Annuity SCD: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{date('m/d/Y', strtotime($scenarioData2['annuity_scd']))}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">High-3 Average: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{number_format(round($scenarioData2['high3_avg'], 2), 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Annuity Before Deduction: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ number_format(round($scenarioData2['annuity_before_deduction'], 2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Survivor Annuity: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ number_format(round($scenarioData2['survivory_annuity'], 2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Survivor Annuity Cost: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ number_format(round($scenarioData2['survivory_annuity_cost'], 2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Part Time Multiplier: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($scenarioData2['part_time_multiplier'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">MRA+10 Multiplier: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ number_format(round($scenarioData2['mra10_multiplier'] ?? 0, 2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Deposit Penalty: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ number_format(round($scenarioData2['deposit_penalty'], 2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Refund Penalty: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ number_format(round($scenarioData2['refund_penalty'], 2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">First-Year Annuity: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ date('m/d/Y', strtotime($scenarioData1['retirement_date'])) }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Annuity COLA: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($scenarioData2['annuity_cola'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Unused Sick Leave: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($scenarioData2['unused_sick_leave'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Unused Annual Leave: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($scenarioData2['unused_annual_leave'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Total FERS Months Service: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($scenarioData2['total_fers_months_service'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Total CSRS Months Service: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($scenarioData2['total_csrs_months_service'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap table-responsive">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <tr>
                                        <th>Report Year</th>
                                        <th>Age</th>
                                        <th>Annual Annuity</th>
                                        <th>Early Penalty</th>
                                        <th>Refund Penalty</th>
                                        <th>Non-Deduct Penalty</th>
                                        <th>Annual Annuity No Survivor</th>
                                        <th>Annual With Surivor</th>
                                        <th>Annual Survivor Benefit</th>
                                        <th>Annual Difference</th>
                                        <th>Monthly Annuity</th>
                                        <th>Monthly With Survivor</th>
                                        <th>Monthly Survivor Benefit</th>
                                        <th>Monthly Difference</th>
                                        <th>Annual Accumulated Difference</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($annuityTable as $row)
                                    <tr>
                                        <td style="text-align: center;">{{$row['reportYear']}}</td>
                                        <td style="text-align: center;">{{$row['age']}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['yearlyPension']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['earlyOutPenalty']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['refundPenalty']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['nonDeductPenalty']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['annualAnnuityNoSurvival']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['annualWithSurvivor']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['annualSurvivorBenifits']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['annualDifference']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['monthlyPension']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['monthlyWithSurvivor']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['monthlySurvivorBenifits']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['monthlyDifference']), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['annualAccumulatedDifference']), 2) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="15">No Data Found</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">Social Security</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="basic_info_form_wrap">
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">SS COLA: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($ssReport['ss_cola'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">SS at 62: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">${{ number_format(round($ssReport['ss_at_62'], 2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">SS Start Age: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($ssReport['ss_start_age'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">SS at Start Age: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">${{ number_format(round($ssReport['ss_at_start_age'], 2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <tr>
                                        <th>Year</th>
                                        <th>Age</th>
                                        <th>Monthly</th>
                                        <th>Annual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $year_ss = date('Y');
                                    $age_ss = $ssReport['emp_age'];
                                    $monthly_ss = 0;
                                    @endphp
                                    @for($j = $ssReport['emp_age']; $j <= 90; $j++) <tr>
                                        <td style="text-align: center;">{{$year_ss++}}</td>
                                        <td style="text-align: center;">{{$age_ss++}}</td>
                                        @php
                                        if($j == $ssReport['ss_start_age']) {
                                        $monthly_ss = $ssReport['ss_at_start_age'];
                                        } elseif(($j == $ssReport['emp_age']) && ($j > $ssReport['ss_start_age'])) {
                                        $monthly_ss = $ssReport['ss_at_start_age'];
                                        for($k = $ssReport['emp_age'] + 1; $k <= $j; $k++) {
                                            $monthly_ss=($monthly_ss * ($ssReport['ss_cola'] / 100)) + $monthly_ss; } } elseif($j> $ssReport['ss_start_age']) {
                                            $monthly_ss = ($monthly_ss * ($ssReport['ss_cola'] / 100)) + $monthly_ss;
                                        }
                                        @endphp
                                        <td style="text-align: center;">${{ number_format(round($monthly_ss, 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round(($monthly_ss * 12), 2), 2) }}</td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">FERS Supplement</p>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <tr>
                                        <th>Year</th>
                                        <th>Age</th>
                                        <th>Monthly</th>
                                        <th>Annual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $year_fers = date('Y');
                                    $age_fers = $fersSupplement['emp_age'];
                                    $fers_retirement = $fersSupplement['retirement_age'];
                                    @endphp
                                    @for($m = $age_fers; $m <= 90; $m++) <tr>
                                        @php
                                        if(($m >= $fers_retirement) && ($m < 62)) { $fers_monthlySRS=$fersSupplement['monthlySRS']; } else { $fers_monthlySRS=0; } @endphp <td style="text-align: center;">{{$year_fers++}}</td>
                                            <td style="text-align: center;">{{$age_fers++}}</td>
                                            <td style="text-align: center;">${{ number_format(round($fers_monthlySRS, 2), 2) }}</td>
                                            <td style="text-align: center;">${{ number_format(round(($fers_monthlySRS * 12), 2), 2) }}</td>
                                            </tr>
                                            @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">All Income (Annual)</p>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <tr>
                                        <th>Year</th>
                                        <th>Age</th>
                                        <th>Salary</th>
                                        <th>Annuity</th>
                                        <th>Supplement</th>
                                        <th>Social Security</th>
                                        <th>Total</th>
                                        <th>Change</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allIncomeAnnual as $row)
                                    <tr>
                                        <td style="text-align: center;">{{$row['year']}}</td>
                                        <td style="text-align: center;">{{$row['age']}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['salary'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round(($row['annuity']), 2), 2)}}</td>
                                        <td style="text-align: center;">{{ number_format(round($row['supplement'], 2), 2)}}</td>
                                        <td style="text-align: center;">{{round($row['ss'], 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['total'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['change'], 2), 2)}}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8">No Data found...</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>



                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">All Income (Monthly)</p>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <tr>
                                        <th>Year</th>
                                        <th>Age</th>
                                        <th>Salary</th>
                                        <th>Annuity</th>
                                        <th>Supplement</th>
                                        <th>Social Security</th>
                                        <th>Total</th>
                                        <th>Change</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allIncomeAnnual as $row)
                                    <tr>
                                        <td style="text-align: center;">{{$row['year']}}</td>
                                        <td style="text-align: center;">{{$row['age']}}</td>
                                        <td style="text-align: center;">${{ number_format(round(($row['salary'] / 12), 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round(($row['annuity'] / 12), 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round(($row['supplement'] / 12), 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round(($row['ss'] / 12), 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round(($row['total'] / 12), 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round(($row['change'] / 12), 2), 2)}}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8">No Data found...</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>



                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">TSP - Salary Increases</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="basic_info_form_wrap">
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Starting Balance: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($tsp_sal_increase['starting_balance'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Yearly Contributions: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">${{ number_format(round($tsp_sal_increase['yearlyContribution'], 2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Loan Repayment: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{ round($tsp_sal_increase['loanRepayment'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Payoff Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{(isset($tsp_sal_increase['payoff_date']) && ($tsp_sal_increase['payoff_date'] != null)) ? date('Y/m/d', strtotime($tsp_sal_increase['payoff_date'])) : ''}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap table-responsive">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <tr>
                                        <th>Report Year</th>
                                        <th>Cont.</th>
                                        <th>G</th>
                                        <th>F</th>
                                        <th>C</th>
                                        <th>S</th>
                                        <th>I</th>
                                        <th>Income</th>
                                        <th>2025</th>
                                        <th>2030</th>
                                        <th>2035</th>
                                        <th>2040</th>
                                        <th>2045</th>
                                        <th>2050</th>
                                        <th>2055</th>
                                        <th>2060</th>
                                        <th>2065</th>
                                        <th>Balance</th>
                                        <th>Salary</th>
                                        <th>% of Year</th>
                                        <th>Employee</th>
                                        <th>Emp%</th>
                                        <th>Catch Up</th>
                                        <th>1%</th>
                                        <th>Match%</th>
                                        <th>Repay</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tsp_sal_increase['tsp_arr'] as $row)
                                    <tr>
                                        <td style="text-align: center;">{{$row['year']}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['gfund'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['cont'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['ffund'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['cfund'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['sfund'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['ifund'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['lIncome'], 2), 2)}}</td>

                                        <td style="text-align: center;">${{ number_format(round($row['l2025'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['l2030'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['l2035'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['l2040'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['l2045'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['l2050'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['l2055'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['l2060'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['l2065'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['balance'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['salary'], 2), 2)}}</td>
                                        <td style="text-align: center;">{{round($row['percentageOfYear'], 2)}}%</td>
                                        <td style="text-align: center;">${{ number_format(round($row['empRegularContri'], 2), 2)}}</td>
                                        <td style="text-align: center;">{{round($row['regularContriPercentage'], 2)}}%</td>
                                        <td style="text-align: center;">${{ number_format(round($row['catchUpContri'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['onePercentSal'], 2), 2)}}</td>
                                        <td style="text-align: center;">{{round($row['matchPercent'], 2)}}%</td>
                                        <td style="text-align: center;">${{ number_format(round($row['repay'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($row['total'], 2), 2)}}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="22">No Data found...</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>




                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">TSP - No Salary Increases</p>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap table-responsive">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <tr>
                                        <th>Report Year</th>
                                        <th>Cont.</th>
                                        <th>G</th>
                                        <th>F</th>
                                        <th>C</th>
                                        <th>S</th>
                                        <th>I</th>
                                        <th>2025</th>
                                        <th>2030</th>
                                        <th>2035</th>
                                        <th>2040</th>
                                        <th>2045</th>
                                        <th>2050</th>
                                        <th>2055</th>
                                        <th>2060</th>
                                        <th>2065</th>
                                        <th>Income</th>
                                        <th>Blance</th>
                                        <th>Salary</th>
                                        <th>% of Year</th>
                                        <th>Employee</th>
                                        <th>Emp%</th>
                                        <th>Catch Up</th>
                                        <th>1%</th>
                                        <th>Match%</th>
                                        <th>Repay</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @if($tsp_Nosal_increase['tsp_arr'] != "") --}}
                                    @forelse ($tsp_Nosal_increase['tsp_arr'] as $tsp_Nosal_increases)
                                    <tr>
                                        <td style="text-align: center;">{{$tsp_Nosal_increases['year']}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['cont'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['gfund'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['ffund'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['cfund'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['sfund'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['ifund'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['lIncome'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['l2025'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['l2030'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['l2035'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['l2040'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['l2045'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['l2050'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['l2055'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['l2060'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['l2065'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['balance'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['salary'], 2), 2)}}</td>
                                        <td style="text-align: center;">{{round($tsp_Nosal_increases['percentageOfYear'], 2)}}%</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['empRegularContri'], 2), 2)}}</td>
                                        <td style="text-align: center;">{{round($tsp_Nosal_increases['regularContriPercentage'], 2)}}%</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['catchUpContri'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['onePercentSal'], 2), 2)}}</td>
                                        <td style="text-align: center;">{{round($tsp_Nosal_increases['matchPercent'], 2)}}%</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['repay'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($tsp_Nosal_increases['total'], 2), 2)}}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="22">No Data found...</td>
                                    </tr>
                                    @endforelse
                                    {{-- @else
                                        <tr>No data found</tr>
                                    @endif --}}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">FEGLI Pre-Retirement (Default Scenario Only)</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap table-responsive">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <th>Age</th>
                                    <th>Salary</th>
                                    <th>Basic</th>
                                    <th>Basic Cost</th>
                                    <th>Option A</th>
                                    <th>Option A Cost</th>
                                    <th>Option B</th>
                                    <th>Option B Cost</th>
                                    <th>Option C</th>
                                    <th>Option C Cost</th>
                                    <th>Total Coverage</th>
                                    <th>Bi-Weekly Cost</th>
                                    <th>Monthly Cost</th>
                                    <th>Annual Cost</th>
                                    <th>Accum. Cost</th>
                                </thead>
                                <tbody>
                                    @forelse ($pre_retirement as $pre_retirement)
                                    <tr>
                                        <td style="text-align: center;">{{$pre_retirement['age']}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['annual_sal'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['basic_coverage'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['costs']['basic']['BiWeekly'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['optionA_coverage'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['costs']['optionA']['BiWeekly'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['optionB_coverage'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['costs']['optionB']['BiWeekly'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['optionC_coverage'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['costs']['optionC']['BiWeekly'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['total_coverage'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['totalBiWeekly'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['totalMonthly'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['totalYearly'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($pre_retirement['runningTotalCost'], 2), 2)}}</td>

                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="22">No Data found...</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">FEGLI Post-Retirement (Default Scenario Only)</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="table_wrap table-responsive">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <th>Age</th>
                                    <th>Salary</th>
                                    <th>Basic</th>
                                    <th>Basic Cost</th>
                                    <th>Option A</th>
                                    <th>Option A Cost</th>
                                    <th>Option B</th>
                                    <th>Option B Cost</th>
                                    <th>Option C</th>
                                    <th>Option C Cost</th>
                                    <th>Total Coverage</th>
                                    <th>Bi-Weekly Cost</th>
                                    <th>Monthly Cost</th>
                                    <th>Annual Cost</th>
                                    <th>Accum. Cost</th>
                                </thead>
                                <tbody>
                                    @forelse ($post_retirement as $post_retirement)
                                    <tr>
                                        <td style="text-align: center;">{{$post_retirement['age']}}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['annual_sal'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['basic_coverage'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['costs']['basic']['BiWeekly'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['optionA_coverage'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['costs']['optionA']['BiWeekly'], 2), 2)}}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['optionB_coverage'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['costs']['optionB']['BiWeekly'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['optionC_coverage'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['costs']['optionC']['BiWeekly'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['total_coverage'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['totalBiWeekly'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['totalMonthly'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['totalYearly'], 2), 2) }}</td>
                                        <td style="text-align: center;">${{ number_format(round($post_retirement['runningTotalCost'], 2), 2) }}</td>

                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="22">No Data found...</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>


                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">Health Benefits</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="basic_info_form_wrap">
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Health COLA: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($FEHBAveragePremiumIncrease, 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Dental Premium: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">${{ number_format(round($healthBenifits['biWeekly']['dental'], 2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Health Premium: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($healthBenifits['biWeekly']['health'], 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Vision Premium: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">${{ number_format(round($healthBenifits['biWeekly']['vision'],2), 2) }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">

                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">Dental & Vision Premium: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">${{ isset($healthBenifits['biWeekly']['dental_and_vision']) ? number_format(round($healthBenifits['biWeekly']['dental_and_vision'],2), 2) : 0 }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table_wrap table-responsive">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <th>Year</th>
                                    <th>Age</th>
                                    <th>Health Biweekly</th>
                                    <th>Health Monthly</th>
                                    <th>Health Annual</th>
                                    <th>Dental Biweekly</th>
                                    <th>Dental Monthly</th>
                                    <th>Dental Annual</th>
                                    <th>Vision Biweekly</th>
                                    <th>Vision Monthly</th>
                                    <th>Vision Annual</th>
                                    <th>Dental & Vision Biweekly</th>
                                    <th>Dental & Vision Monthly</th>
                                    <th>Dental & Vision Annual</th>
                                    <th>Total Biweekly</th>
                                    <th>Total Monthly</th>
                                    <th>Total Annual</th>
                                    <th>Accum.</th>
                                    <th>Change</th>
                                </thead>
                                <tbody>
                                    @foreach($healthBenifits_arr as $row)
                                    <tr>
                                        <td>{{ ($row['years_in_retirement'] > 0) ? $row['years_in_retirement'] : '' }}</td>
                                        <td>{{ $row['age'] }}</td>
                                        <td>${{ round($row['biWeeklyHealthPremium'], 2) }}</td>
                                        <td>${{ round($row['monthlyHealthPremium'], 2) }}</td>
                                        <td>${{ round($row['yearlyHealthPremium'], 2) }}</td>
                                        <td>${{ round($row['biWeeklyDentalPremium'], 2) }}</td>
                                        <td>${{ round($row['monthlyDentalPremium'], 2) }}</td>
                                        <td>${{ round($row['yearlyDentalPremium'], 2) }}</td>
                                        <td>${{ round($row['biWeeklyVisionPremium'], 2) }}</td>
                                        <td>${{ round($row['monthlyVisionPremium'], 2) }}</td>
                                        <td>${{ round($row['yearlyVisionPremium'], 2) }}</td>
                                        <td>${{ round($row['biWeeklyDentalAndVision'], 2) }}</td>
                                        <td>${{ round($row['monthlyDentalAndVision'], 2) }}</td>
                                        <td>${{ round($row['yearlyDentalAndVision'], 2) }}</td>
                                        <td>${{ round($row['biWeeklyTotalPremium'], 2) }}</td>
                                        <td>${{ round($row['monthlyTotalPremium'], 2) }}</td>
                                        <td>${{ round($row['yearlyTotalPremium'], 2) }}</td>
                                        <td>${{ round($row['accum'], 2) }}</td>
                                        <td>${{ round($row['change'], 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">FLTCIP</p>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="basic_info_form_wrap">
                            <div class="row in_gap">
                                <div class="col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <div class="col-md-7 col-xs-7">
                                            <div class="label_div">
                                                <label for="">FLTCIP Premium: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-5 col-xs-5">
                                            <label for="">{{round($fltcip, 2)}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="table_wrap table-responsive">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <th>Year</th>
                                    <th>Age</th>
                                    <th>Biweekly</th>
                                    <th>Monthly</th>
                                    <th>Annual</th>
                                    <th>Accum.</th>
                                </thead>
                                <tbody>
                                    @php
                                    $total_fltcip = 0;
                                    $yearly_fltcip = ($fltcipBiWeekly * 26);
                                    $monthly_fltcip = $yearly_fltcip / 12;
                                    @endphp
                                    @for($i = $emp_age; $i <= 90; $i++) @php $total_fltcip=$total_fltcip + $yearly_fltcip; $print_total=round($total_fltcip); $print_biweek=round($fltcipBiWeekly); $print_monthly=round($monthly_fltcip); $print_yearly=round($yearly_fltcip); @endphp <tr>
                                        <td>{{$yearsInRet++}}</td>
                                        <td>{{$i}}</td>
                                        <td>${{$print_biweek}}</td>
                                        <td>${{$print_monthly}}</td>
                                        <td>${{$print_yearly}}</td>
                                        <td>${{number_format($print_total)}}</td>
                                        </tr>
                                        @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
