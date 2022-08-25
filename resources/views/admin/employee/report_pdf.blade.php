<!DOCTYPE html>
<html lang="en">

<head>
    <title>Report-AdamG-Advisor-ServingThoseWhoServe</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="font-family:Arial, sans-serif; color: #2e2e2e;">
    <!-- #7d7d7d -->

    <!-- PDF forntpage starts -->
    <table cellpadding="12" style="font-family:Arial, sans-serif;  page-break-after: always;">
        <br><br><br><br>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 34px; text-align: right; font-weight: bold; color: #1f497d; line-height: 44px;">
                <br><br>Federal<br>Retirement<br>Impact<br>Report<br><br>
            </td>
            <td style="text-align: left; font-family:Arial, sans-serif; font-size: 16px; color: #2e2e2e; font-weight: normal;"><br><br><br><br><span style="line-height: 26px;"><i>Prepared especially for</i></span>
                <p style="font-size: 19px; font-weight: bold; line-height: 20px;">{{ $pdf_data['employee']['EmployeeName'] }}</p>{!! $pdf_data['employee']['EmployeeAddress'] !!}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="font-family:Arial, sans-serif; text-align: center;">
                <br><br><br><br><br><br><br><br><br><br>
                <table>
                    <tr>
                        <!-- <td style="width: 2%;"></td> -->
                        <td style="text-align: center;"><span style="font-family:Arial, sans-serif; font-size: 20px; color: #1f497d; font-weight: bold; line-height: 26px;">{{ $pdf_data['employee']['advisor']['company_name'] ?? "" }}</span>
                            <span style="font-family:Arial, sans-serif; font-size: 15px; line-height: 19px; color: #2e2e2e;">{!! $pdf_data['employee']['advisor']['AdvisorAddress'] !!}
                                @if($pdf_data['employee']['advisor']['PhoneNumber'] != null)
                                P: {{ $pdf_data['employee']['advisor']['PhoneNumber'] }}
                                @endif
                            </span>
                            <span style="font-family:Arial, sans-serif; font-size: 15px; color: #2e2e2e; padding: 0px;">{{date('F Y', strtotime($pdf_data['employee']['ReportDate']))}}</span>
                        </td>
                        <!-- <td style="width: 2%;"></td> -->
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0; font-size: 16px;">Disclaimer & Release</td>
        </tr>
    </table>
    <p style="font-family:Arial, sans-serif; font-size: 10px; text-align: justify;">{!! $disclaimerText !!}</p>
    <table style="page-break-after: always;"></table>

    <table>
        <tr>
            <td style="font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0; font-size: 16px;">Data Used in Preparation of This Report</td>
        </tr>
    </table>
    <p style="font-weight: bold; font-size: 11px; line-height: 16px;">Basic Information</p>
    <table cellpadding="5" style="text-align: left; font-family:Arial, sans-serif; font-size: 10px;">
        <tr>
            <td>Employee Name:</td>
            <td>{{$pdf_data['employee']['EmployeeName']}}</td>
            <td></td>
        </tr>
        <tr>
            <td>Address:</td>
            <td>{!! $pdf_data['employee']['EmployeeAddress'] !!}</td>
            <td></td>
        </tr>
        <tr>
            <td>Date of Birth:</td>
            <td>{{$bday}}</td>
            <td></td>
        </tr>
        <tr>
            <td>Current Age:</td>
            <td>{{$pdf_data['emp_age_format']}}</td>
            <td></td>
        </tr>
    </table>
    <br>
    <p style="font-weight: bold; font-size: 11px; line-height: 16px;">Current Employment</p>
    <table cellpadding="3" style="text-align: left; font-family:Arial, sans-serif; font-size: 10px; line-height: 16px;">
        <tr>
            <td>Service Computation Date:</td>
            <td>{{date('m/d/Y', strtotime($pdf_data['employee']['eligibility']['LeaveSCD']))}}</td>
            <td></td>
        </tr>
        <tr>
            <td>Creditable Service:</td>
            <td>{{$pension['creditableServiceYears']}}y {{$pension['creditableServiceMonths']}}m</td>
            <td></td>
        </tr>
        <tr>
            <td>Unused Sick Leave:</td>
            <td>{{ number_format(round($pdf_data['employee']['UnusedSickLeave'])) }} hours ({{ number_format((($pdf_data['employee']['UnusedSickLeave'] / 2087) * 12), 2) }} months)</td>
            <td></td>
        </tr>
        <tr>
            <td>Unused Annual Leave:</td>
            <td>{{ number_format(round($pdf_data['employee']['UnusedAnnualLeave'])) }} hours ({{ number_format((($pdf_data['employee']['UnusedAnnualLeave'] / 2087) * 12), 2) }} months)</td>
            <td></td>
        </tr>
        <tr>
            <td>Current Salary:</td>
            <td>${{ number_format(round($pdf_data['employee']['CurrentSalary'])) }}</td>
            <td></td>
        </tr>
    </table>
    <br>
    <p style="font-weight: bold; font-size: 11px; line-height: 16px;">Planned Retirement</p>
    <table cellpadding="3" style="text-align: left; font-family:Arial, sans-serif; font-size: 10px; line-height: 16px; page-break-after: always;">
        <tr>
            <td>Retirement System:</td>
            <td>{{$pension['systemType']}}</td>
            <td></td>
        </tr>
        <tr>
            <td>Retirement Type:</td>
            <td>{{$pdf_data['employee']['retirementType']}}</td>
            <td></td>
        </tr>
        <tr>
            <td>Employee Type:</td>
            <td>{{ ($pdf_data['employee']['otherEmpType'] == '') ? $pdf_data['employee']['empType'] : $pdf_data['employee']['otherEmpType']}}</td>
            <td></td>
        </tr>
        <tr>
            <td>Planned Retirement Date:</td>
            <td>{{ date('m/d/Y', strtotime($pdf_data['employee']['eligibility']['RetirementDate'])) }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Age at Retirement:</td>
            <td>{{ $pension['retirementAgeY'] }}y {{ $pension['retirementAgeM'] }}m</td>
            <td></td>
        </tr>
        <tr>
            <td>Creditable Service at Retirement:</td>
            <td>{{$pension['serviceYears']}}y {{$pension['serviceMonths']}}m</td>
            <td></td>
        </tr>
        <tr>
            <td>Pay raise % (now to retirement):</td>
            <td>{{$pdf_data['empConf']['SalaryIncreaseDefault']}}%</td>
            <td></td>
        </tr>
        <tr>
            <td>Projected Ending Salary:</td>
            <td>${{ number_format(round($scenaio1['lastSalary'])) }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Projected High-3 Average Salary:</td>
            <td>${{ number_format(round($scenaio1['projectedHigh3Avg'], 2)) }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Projected Annual Leave Payout:</td>
            <td>${{ number_format(round($scenaio1['annual_leaves_payout'], 2)) }}</td>
            <td></td>
        </tr>
    </table>

    <!-- PDF forntpages ends -->

    <!-- Pension block HTML starts -->
    <table cellpadding="12" style="font-family:Arial, sans-serif; page-break-after: always;">
        <br><br><br><br><br><br><br><br>
        <br><br><br><br><br><br><br><br>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-top: 5px solid #1f497d; border-bottom: 5px solid #1f497d; font-size: 28px;">Federal Pension</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-bottom: 5px solid #1f497d; font-size: 17px;">{{ $pension['systemType'] }}</td>
        </tr>
    </table>

    <table>
        @if($pension['systemType'] == 'FERS')
        <tr>
            <td style="font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0; font-size: 16px;">Federal Employees Retirement System<br></td>
        </tr>
        @else
        <tr>
            <td style="font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0; font-size: 16px;">Civil Service Retirement System<br></td>
        </tr>
        @endif
    </table>


    <table cellpadding="0">
        <tr>
            <td width="60%" style="vertical-align: top;"><span style="font-weight: bold; font-size: 11px; padding: 0px; line-height: 12px; margin-bottom: 0;">Eligibility to Retire</span><br><span style="line-height: 12px;font-family:Arial, sans-serif; font-size: 10px; padding-right: 40px; text-align: justify;">FERS employees will be fully-eligible to retire once they have met both the age and service requirements. Employees are strongly encouraged to complete a Certified Summary of Federal Service to confirm their time.<br></span>
            </td>
            <td width="40%" style="vertical-align: bottom;">
                <br>
                <table style="font-family:Arial, sans-serif; font-size:9.5px;">
                    <thead>
                        <tr>
                            <th style="width: 10%;"></th>
                            <th colspan="2" style="font-size: 10px; margin: 0; padding: 0; line-height: 12px; font-weight: bold;">Full Eligibility Requirements</th>
                        </tr>
                        <tr>
                            <th style="width: 10%;"></th>
                            <th style="width: 45%; font-family:Arial, sans-serif; font-weight: bold;">Age</th>
                            <th style="width: 45%; font-family:Arial, sans-serif; font-weight: bold;">Years</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pension['eligibilityForFullRetirement'] as $row)
                        <tr>
                            <td style="width: 10%;"></td>
                            <td style="width: 45%;">{{$row['age']}}</td>
                            <td style="width: 45%;">{{$row['service']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- <br> -->
            </td>
        </tr>
    </table>
    @if($pension['systemType'] == 'FERS')
    <table>
        <thead>
            <tr>
                <th style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 12px;">Minimum Retirement Age (MRA)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="line-height: 12px; font-family:Arial, sans-serif; font-size: 10px; margin:0; text-align: justify; ">A FERS employee's Minimum Retirement Age (MRA) is determined by the year in which they were born and is a sliding scale ranging from age 55 to age 57. Based on the year you were born ({{$pension['bdayYear']}}), your MRA is {{$minimumRetirementAge['mra_year']}}y {{ $minimumRetirementAge['mra_month'] }}m. <br></td>
            </tr>
        </tbody>
    </table>
    @endif

    <table>
        <tr>
            <td style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 12px;">Day of the Month to Retire
                <!-- Choosing a Good Retirement Date -->
            </td>
        </tr>
        <tr>
            <!-- There are a number of factors that go into choosing a "good" retirement date.  -->
            <td style="line-height: 12px;font-family:Arial, sans-serif; font-size: 10px; text-align: justify; ">As a general rule, FERS employees should retire on the last day of the month. This will mean that there will be no gap between when they are paid as an employee and when they are paid as a retiree. If they retire on a day other than the last day of the month, they will not be paid for the remainder of the month they retired.<br>
            </td>
        </tr>

    </table>

    <table>
        <tr>
            <td style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 12px;">Calculating the Federal Pension</td>
        </tr>
        <tr>
            <td style="line-height: 12px;font-family:Arial, sans-serif; font-size: 10px; text-align: justify; ">The three components used to calculate the federal pension are an employee's high-3 average salary, their years/months of creditable service, and the appropriate retirement formula percentage. <br></td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 12px;">High-3 Average Salary</td>
        </tr>
        <tr>
            <td style="line-height: 12px;font-family:Arial, sans-serif; font-size: 10px; text-align: justify; ">The high-3 average salary (the "high-3") is the average of an employee's highest 3 years (36 months) of consecutive earnings. Typically, this is earned at the end of an employee's career. The high-3 will only include certain kinds of pay.<br></td>
        </tr>
    </table>

    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:9.5px;">
        <tr>
            <th style="width: 5%;"></th>
            <th style="width: 55%; font-family:Arial, sans-serif;"><span style=" font-weight: bold;">Pay included in the high-3:</span><br>
                &nbsp;&nbsp;&nbsp; Regular pay<br>
                &nbsp;&nbsp;&nbsp; Locality pay<br>
                &nbsp;&nbsp;&nbsp; Law Enforcement Availability Pay (LEAP)<br>
                &nbsp;&nbsp;&nbsp; Administratively Uncontrollable Overtime (AUO)<br>
                &nbsp;&nbsp;&nbsp; Premium pay (select types)<br>
                &nbsp;&nbsp;&nbsp; Market pay<br>
                &nbsp;&nbsp;&nbsp; Environmental pay<br>
                &nbsp;&nbsp;&nbsp; Night differential pay (for Wage Grade only)
            </th>
            <th style="width: 40%; font-family:Arial, sans-serif;"><span style="font-weight: bold;">Pay NOT included:</span><br>
                &nbsp;&nbsp;&nbsp; Retention pay<br>
                &nbsp;&nbsp;&nbsp; Overseas COLA<br>
                &nbsp;&nbsp;&nbsp; Military pay<br>
                &nbsp;&nbsp;&nbsp; Regular overtime<br>
                &nbsp;&nbsp;&nbsp; Premium pay (select types)<br>
                &nbsp;&nbsp;&nbsp; Bonuses<br>
                &nbsp;&nbsp;&nbsp; Cash awards<br>
                &nbsp;&nbsp;&nbsp; Relocation allowances<br>
            </th>
        </tr>
    </table>

    <table>
        <tr>
            <td style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 12px;">Applying Unused Sick Leave</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; text-align: justify;">If an employee has any unused sick leave at the time of retirement, those hours are converted into years, months and days and added to their creditable service to calculate the pension. Use OPM's "2,087 chart" to see the conversion. Once the sick leave time has been added to the creditable service time, OPM will discard any days which do not total 30 (for a month of credit) from the pension calculation. The most an employee can lose is 29 days based on this rounding method.{{-- @if($pension['systemType'] == 'FERS') FERS employees (retiring on or after January 1, 2014) will have 100% of their unused sick leave applied to their pension. FERS employees retiring prior to January 1, 2014 will have 50% of their unused sick leave applied to their pension.@else CSRS employees will have 100% of their unused sick leave applied to their pension. @endif --}}<br></td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 12px;">Selling Back Annual Leave</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; text-align: justify;">If an employee has any unused annual leave at the time of retirement, the government buys back those hours in the form of a lump sum payment. To calculate the payment, take the hours of leave multiplied by the employee's final hourly rate of pay. Keep in mind that most employees are permitted to carry over a maximum of 240 hours from one year to the next (most postal workers have a 440 hour carryover limit). This payment is usually made within a few weeks after retirement and is fully-taxable as earned income. Certain deductions like federal, state and Social Security taxes occur. <br></td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 12px;">Part-time Service</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; text-align: justify;">Federal employees who have had part-time service at some point in their career will have their pension reduced to give them their "fair share" according to the amount of part-time service they had during their career. The fair share is determined by taking the total number of hours ACTUALLY worked divided by the total number of hours they SHOULD have worked had they been full-time for their entire career. This percentage known as the "part-time proration factor" is multiplied by the full-time pension calculation which produces a lower pension amount.<br></td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 12px;">Cost of Living Adjustments (COLAs)</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; text-align: justify;">FERS retirees begin receiving COLAs to their pension at age 62 (or retirement, if later). Special Provision Employees begin receiving COLAs immediately (regardless of age). COLAs are set on 11/1 each year (effective in December and payable in January). The first year COLA is determined by the # of months in the prior year (November - October) a person was retired. For instance, if they retired 10/31, they receive 1/12ths of the published COLA percentage the following January.</td>
        </tr>
    </table>


    <table style="page-break-before: always;">
        <tr>
            <td style="font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0; font-size: 16px;">Your Federal Pension<br></td>
        </tr>
        <tr>
            @if($pdf_data['employee']['retirementType'] == "Deferred")
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">You are leaving service under a "deferred" retirement scenario. Your federal pension will include creditable federal service and creditable military service. Any unused sick leave is forfeited under a deferred retirement scenario. Your pension will be payable at a later date and is dependent on the number of years of service you have when you separate. That pension is illustrated below and is shown when you are eligible to receive it without penalty. A Cost of Living Adjustment (COLA) has been applied to your pension after it begins (the 10-year average is {{$pension['cola']}}%).<br></td>
            @else
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">Your federal pension will include creditable federal service, creditable military service, and unused sick leave. If you decide to retire on the date you planned ({{ $pension['retirement_date'] }}), your pension is illustrated below. A Cost of Living Adjustment (COLA) has been applied to your pension (the 10-year average is {{$pension['cola']}}%).<br></td>
            @endif
        </tr>
    </table>
    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:9px; page-break-after: always;">
        <tr>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 12.5%;">Year #</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 12.5%;">Age</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 15%;">Monthly<br>Pension<br>(gross)</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 15%;">Monthly<br>MRA+10<br>Penalty</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 15%;">Monthly<br>Deposit<br>Penalty</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 15%;">Monthly<br>Re-Deposit<br>Penalty</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 15%;">Monthly<br>Pension<br>(after penalties)</th>
        </tr>
        @forelse($pension['retDetails'] as $k => $row)
        <?php
        if ($k % 2 == 0) {
            $row_background = '#fff';
        } else {
            $row_background = '#f1f1f1';
        }
        if ($row['years_in_retirement'] == 0) {
            $year_in_ret = '<img src="' . url('images/star.svg') . '">';
        } else {
            $year_in_ret = $row['years_in_retirement'];
        }
        ?>
        <tr style="line-height: 7px; background-color: {!! $row_background !!}">
            <td style="font-family:Arial, sans-serif; text-align: center; width: 12.5%;">{!! $year_in_ret !!}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; width: 12.5%;">{{round($row['age'], 2)}}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; width: 15%;">${{ number_format(round($row['monthly_pension_gross'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; width: 15%;">${{ number_format(round($row['monthlyEarlyOutPenalty'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; width: 15%;">${{ ($row['monthlyDepositPenalty'] === intval($row['monthlyDepositPenalty'])) ? number_format($row['monthlyDepositPenalty']) : number_format(round($row['monthlyDepositPenalty'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; width: 15%;">${{ ($row['monthlyRedepositPenalty'] === intval($row['monthlyRedepositPenalty'])) ? number_format($row['monthlyRedepositPenalty']) : number_format(round($row['monthlyRedepositPenalty'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; width: 15%;">${{ number_format(round($row['net_monthly_pension'])) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7">No Records Found</td>
        </tr>
        @endforelse
    </table>

    {{-- ********** --}}

    @if($pdf_data['employee']['retirementType'] != "Deferred")
    <table>
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Planned & Delayed Retirement</td>
        </tr>
    </table>
    <p style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">You indicate that you plan to retire in {{date('F Y', strtotime($pension['retirement_date']))}} at age {{$pension['retirementAgeY']}} years, {{$pension['retirementAgeM']}} months. This scenario has been illustrated below. Also illustrated are the projections if you were to wait to retire over the next 4 years. We have assumed in all scenarios that you retain a balance of {{round($pdf_data['employee']['UnusedSickLeave'])}} hours ({{ number_format((($pdf_data['employee']['UnusedSickLeave'] / 2087) * 12), 2) }} months) of sick leave.</p>

    <table width="100%" cellpadding="5" style="font-family:Arial, sans-serif; font-size:10px; page-break-after: always;">
        <tr>
            <th style="width:180px; text-align: right;"><strong>Date:</strong></th>
            <th style="font-weight: bold; text-align: center; width: 70px;">{{ date('F Y', strtotime($pension['retirement_date'])) }}</th>
            <th style="font-weight: bold; text-align: center; width: 70px;">{{date('F Y', strtotime($pension['retirement_date'] . '+1 year'))}}</th>
            <th style="font-weight: bold; text-align: center; width: 70px;">{{date('F Y', strtotime($pension['retirement_date'] . '+2 year'))}}</th>
            <th style="font-weight: bold; text-align: center; width: 70px;">{{date('F Y', strtotime($pension['retirement_date'] . '+3 year'))}}</th>
            <th style="font-weight: bold; text-align: center; width: 70px;">{{date('F Y', strtotime($pension['retirement_date'] . '+4 year'))}}</th>
        </tr>
        <tr>
            <td style="width:180px; text-align: right;"><strong>Age:</strong></td>
            <td style="text-align: center; width: 70px;">{{$pension['retirementAgeY']}}y {{$pension['retirementAgeM']}}m</td>
            <td style="text-align: center; width: 70px;">{{$pension['retirementAgeY'] + 1}}y {{$pension['retirementAgeM']}}m</td>
            <td style="text-align: center; width: 70px;">{{$pension['retirementAgeY'] + 2}}y {{$pension['retirementAgeM']}}m</td>
            <td style="text-align: center; width: 70px;">{{$pension['retirementAgeY'] + 3}}y {{$pension['retirementAgeM']}}m</td>
            <td style="text-align: center; width: 70px;">{{$pension['retirementAgeY'] + 4}}y {{$pension['retirementAgeM']}}m</td>
        </tr>
        <tr>
            <td style="width:180px; text-align: right;"><strong>Creditable Service:</strong></td>
            <td style="text-align: center; width: 70px;">{{$pension['serviceYears']}}y {{$pension['serviceMonths']}}m</td>
            <td style="text-align: center; width: 70px;">{{$pension['serviceYears'] + 1}}y {{$pension['serviceMonths']}}m</td>
            <td style="text-align: center; width: 70px;">{{$pension['serviceYears'] + 2}}y {{$pension['serviceMonths']}}m</td>
            <td style="text-align: center; width: 70px;">{{$pension['serviceYears'] + 3}}y {{$pension['serviceMonths']}}m</td>
            <td style="text-align: center; width: 70px;">{{$pension['serviceYears'] + 4}}y {{$pension['serviceMonths']}}m</td>
        </tr>
        <tr>
            <td style="width:180px; text-align: right;"><strong>High-3 Average Salary:</strong></td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($high3AllScenarios[1])) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($high3AllScenarios[2])) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($high3AllScenarios[3])) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($high3AllScenarios[4])) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($high3AllScenarios[5])) }}</td>
        </tr>
        <tr>
            <td style="width:180px; text-align: right;"><strong>Yearly Pension (gross):</strong></td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($pensionScenario1))}}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($pensionScenario2))}}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($pensionScenario3))}}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($pensionScenario4))}}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($pensionScenario5))}}</td>
        </tr>
        <tr>
            <td style="width:180px; text-align: right;"><strong>Yearly MRA+10 Penalty:</strong></td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($earlyoutPenaltyScenario1)) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($earlyoutPenaltyScenario2)) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($earlyoutPenaltyScenario3)) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($earlyoutPenaltyScenario4)) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($earlyoutPenaltyScenario5)) }}</td>
        </tr>
        <tr>
            <td style="width:180px; text-align: right;"><strong>Yearly Deposit Penalty:</strong></td>
            <td style="text-align: center; width: 70px;">${{ $depositPenalty }}</td>
            <td style="text-align: center; width: 70px;">${{ $depositPenalty }}</td>
            <td style="text-align: center; width: 70px;">${{ $depositPenalty }}</td>
            <td style="text-align: center; width: 70px;">${{ $depositPenalty }}</td>
            <td style="text-align: center; width: 70px;">${{ $depositPenalty }}</td>
        </tr>
        <tr>
            <td style="width:180px; text-align: right;"><strong>Yearly Redeposit Penalty:</strong></td>
            <td style="text-align: center; width: 70px;">${{ $redepositPenalty }}</td>
            <td style="text-align: center; width: 70px;">${{ $redepositPenalty }}</td>
            <td style="text-align: center; width: 70px;">${{ $redepositPenalty }}</td>
            <td style="text-align: center; width: 70px;">${{ $redepositPenalty }}</td>
            <td style="text-align: center; width: 70px;">${{ $redepositPenalty }}</td>
        </tr>
        <tr>
            <td style="width:180px; text-align: right;"><strong>Yearly Pension (after penalties):</strong></td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($netPensionScenario1)) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($netPensionScenario2)) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($netPensionScenario3)) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($netPensionScenario4)) }}</td>
            <td style="text-align: center; width: 70px;">${{ number_format(round($netPensionScenario5)) }}</td>
        </tr>
        <?php
        $monthlyPension1 = $netPensionScenario1 / 12;
        $monthlyPension2 = $netPensionScenario2 / 12;
        $monthlyPension3 = $netPensionScenario3 / 12;
        $monthlyPension4 = $netPensionScenario4 / 12;
        $monthlyPension5 = $netPensionScenario5 / 12;
        $monthlyPensionAfterPanelity1 = number_format(round($monthlyPension1));
        $monthlyPensionAfterPanelity2 = number_format(round($monthlyPension2));
        $monthlyPensionAfterPanelity3 = number_format(round($monthlyPension3));
        $monthlyPensionAfterPanelity4 = number_format(round($monthlyPension4));
        $monthlyPensionAfterPanelity5 = number_format(round($monthlyPension5));
        ?>
        <tr>
            <td style="width:180px; text-align: right;"><strong>Monthly Pension (after penalties):</strong></td>
            <td style="text-align: center; width: 70px;">${{ $monthlyPensionAfterPanelity1 }}</td>
            <td style="text-align: center; width: 70px;">${{ $monthlyPensionAfterPanelity2 }}</td>
            <td style="text-align: center; width: 70px;">${{ $monthlyPensionAfterPanelity3 }}</td>
            <td style="text-align: center; width: 70px;">${{ $monthlyPensionAfterPanelity4 }}</td>
            <td style="text-align: center; width: 70px;">${{ $monthlyPensionAfterPanelity5 }}</td>
        </tr>
    </table>
    @endif
    <!-- Pension block HTML ends -->

    <!-- FEHB block starts -->


    <table cellpadding="12" style="font-family:Arial, sans-serif; page-break-after: always;">
        <br><br><br><br><br><br><br><br>
        <br><br><br><br><br><br><br><br>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-top: 5px solid #1f497d; border-bottom: 5px solid #1f497d; font-size: 28px;">Survivor Benefit<br>Plan</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-bottom: 5px solid #1f497d; font-size: 17px;">SBP</td>
        </tr>
    </table>

    <table>
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Your Spouse's Survivor Benefit<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Eligibility</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">The only two types of people who may be named under the Spousal Survivor Benefit Plan are a current spouse, or a former spouse (either voluntarily or with a qualifying court order).<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Coverage & Cost</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">Upon retirement, the Survivor Benefit Plan allows a retiree to protect a portion of their pension for their surviving spouse in the event of their death. A current spouse is ENTITLED to the maximum survivor benefit amount listed below. However, if a current spouse wishes to select an amount less than the maximum, they must provide their notarized consent.<br></td>
        </tr>
    </table>

    <table cellpadding="3" style="font-size: 9px;">
        <tr>
            <td style="width: 3%; border-bottom: 1px solid #ddd;"></td>
            <td style="width: 27%; border-bottom: 1px solid #ddd;">Level</td>
            <td style="width: 35%; text-align: left; border-bottom: 1px solid #ddd;">Amount Protected for a Spouse</td>
            <td style="width: 35%; text-align: left; border-bottom: 1px solid #ddd;">Cost to Retiree</td>
        </tr>
        @if($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers')
        <tr>
            <td style="width: 3%;"></td>
            <td style="width: 27%;">Minimum</td>
            <td style="width: 35%; text-align: left;">25% of the pension</td>
            <td style="width: 35%; text-align: left;">5% of the pension</td>
        </tr>
        <tr>
            <td style="width: 3%;"></td>
            <td style="width: 27%;">Maximum</td>
            <td style="width: 35%; text-align: left;">50% of the pension</td>
            <td style="width: 35%; text-align: left;">10% of the pension<br></td>
        </tr>
        @else
        <tr>
            <td style="width: 3%;"></td>
            <td style="width: 27%;">Minimum</td>
            <td style="width: 35%; text-align: left;">55% of $22</td>
            <td style="width: 35%; text-align: left;">Less than $1/year</td>
        </tr>
        <tr>
            <td style="width: 3%;"></td>
            <td style="width: 27%;">Lowest Price Point</td>
            <td style="width: 35%; text-align: left;">55% of $3,600</td>
            <td style="width: 35%; text-align: left;">$90/year</td>
        </tr>
        <tr>
            <td style="width: 3%;"></td>
            <td style="width: 27%;">Maximum</td>
            <td style="width: 35%; text-align: left;">55% of the pension</td>
            <td style="width: 35%; text-align: left;">Just under 10% of the pension<br></td>
        </tr>
        @endif
    </table>

    <table style="text-align: justify; page-break-after: always;">
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Changes in Retirement</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">Once this benefit is selected, it is irrevocable. There is a short window to make certain changes to this coverage. Within the first 18 months of retirement, coverage may be elected or increased. Within the first 30 days, coverage may be decreased. However, after that point, the decision is final. If divorce were to occur during retirement, the divorce decree would determine if this benefit must be retained. <br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Connection to FEHB for a Current Spouse</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">Should the decision be made to elect "no survivor benefit" for a current spouse, upon the death of the federal retiree, the spouse is no longer eligible for health insurance under FEHB. In order to protect this access to FEHB for a spouse after the retiree dies, at least the minimum survivor benefit must be elected. If a spouse is not (and will not) be reliant on FEHB, the decision about the Survivor Benefit Plan can then be based on the merits of the plan, and not the connection it has to FEHB coverage.<br></td>
        </tr>
        <tr>
            <th style="font-size: 11px; line-height: 13px; font-weight: bold;">What if Your Spouse Passes Away First</th>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">Should a spouse predecease the retiree, the retiree is no longer required to pay the premium for the benefit. However, all of the premiums they have paid into this program up to that point are forfeited. No other person may be named as the beneficiary of this program (such as their children), so once the spouse passes away, this benefit program simply stops once OPM is notified.<br></td>
        </tr>
        <tr>
            <th style="font-size: 11px; line-height: 13px; font-weight: bold;">Considerations for Federal Couples</th>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">If two federal employees are married to one another and both qualify to keep FEHB on their own record (meaning they are enrolled in FEHB at the time of their own retirement, and had been covered for at least 5 years), then they are not required to select a survivor benefit for their spouse to continue to keep health insurance under FEHB.<br></td>
        </tr>
        <tr>
            <th style="font-size: 11px; line-height: 13px; font-weight: bold;">Retiring Under a Deferred Retirement Scenario</th>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">If an employee is not eligible for an immediate retirement at the time of separation, they may be eligible for a deferred retirement scenario (which causes them to begin receiving their pension at a later date). Once the former employee becomes an annuitant (when they file to begin receiving the pension), the survivor benefit election becomes active. If the former employee dies prior to filing to receive the pension, the spouse may be eligible for survivor benefits but must meet several criteria. Verify eligibility requirements with OPM.<br></td>
        </tr>
    </table>

    @if($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers')
    <table>
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Your Spouse's Survivor Benefit</td>
        </tr>
    </table>
    @if($pdf_data['employee']['retirementType'] == "Deferred")
    <p style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">Once you become an annuitant (when you file to begin receiving the pension), your survivor benefit election becomes active. We have illustrated both survivor benefit options available below.</p>
    @else
    <p style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">We have illustrated both survivor benefit options available below. Selecting either option allows your spouse to remain eligible for the health coverage (FEHB) if you should pass first (assuming YOU meet the requirements to keep FEHB).</p>
    @endif

    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:9px; page-break-after: always;">
        <tr>
            <td colspan="3" style="border-top: 1px solid #ddd;"></td>
            <td colspan="4" style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; font-weight: bold; border-top: 1px solid #ddd; border-left: 1px solid #ddd;">FULL SURVIVOR BENEFIT (50%)</td>
            <td colspan="4" style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; font-weight: bold; border-top: 1px solid #ddd; border-left: 3px solid #ddd; border-right: 1px solid #ddd;">PARTIAL SURVIVOR BENEFIT (25%)</td>
        </tr>
        <tr>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Year #</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Age</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Monthly<br>Pension<br>(net)</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; border-left: 1px solid #ddd;">Monthly<br>Benefit to<br>Spouse</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Monthly<br>Cost to<br>Retiree</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Annual<br>Cost to<br>Retiree</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Total<br>Cost to<br>Retiree</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; border-left: 3px solid #ddd;">Monthly<br>Benefit to<br>Spouse</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Monthly<br>Cost to<br>Retiree</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Annual<br>Cost to<br>Retiree</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd; text-align: center;">Total<br>Cost to<br>Retiree</th>
        </tr>
        @forelse($SBP_details as $k => $row)
        <?php
        if ($k % 2 == 0) {
            $row_background = '#fff';
        } else {
            $row_background = '#f1f1f1';
        }
        if ($row['years_in_retirement'] == 0) {
            $years_in_ret = '<img src="' . url('images/star.svg') . '">';
        } else {
            $years_in_ret = $row['years_in_retirement'];
        }
        ?>
        <tr style="line-height: 7px; background-color: {{ $row_background }}">
            <td style="font-family:Arial, sans-serif; text-align: center;">{!! $years_in_ret !!}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">{{ $row['age'] }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; border-right: 1px solid #ddd;">${{ number_format(round($row['monthly_pension_gross'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['monthly_survivor_benefits_50'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['monthly_survivor_benefit_cost_50'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['annual_survivor_benefit_cost_50'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; border-right: 3px solid #ddd;">${{ number_format(round($row['cumulative_survivor_benefit_cost_50'])) }}</td>

            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['monthly_survivor_benefits_25'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['monthly_survivor_benefit_cost_25'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['annual_survivor_benefit_cost_25'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; border-right: 1px solid #ddd;">${{ number_format(round($row['cumulative_survivor_benefit_cost_25'])) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="11">No Records Found</td>
        </tr>
        @endforelse
    </table>

    @elseif($pension['systemType'] == 'CSRS' || $pension['systemType'] == 'CSRS Offset')

    <table>
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Your Spouse's Survivor Benefit</td>
        </tr>
    </table>
    <p style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">CSRS retirees have several options for how much of their pension they can protect for their spouse. We have illustrated two of the survivor benefit options available below. Selecting either option allows your spouse to remain eligible for FEHB coverage if you should pass first (assuming you meet the requirements to keep FEHB). The very minimum SBP that can be elected to retain FEHB for your spouse is "55% of $22" (protecting $12/yr) and costs $0.55/yr while you are living.</p>

    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:9px; page-break-after: always;">
        <tr>
            <td colspan="3"></td>
            <td colspan="4" style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; font-weight: bold; border-top: 1px solid #ddd; border-left: 1px solid #ddd;">FULL SURVIVOR BENEFIT (55%)</td>
            <td colspan="4" style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; font-weight: bold; border-top: 1px solid #ddd; border-left: 3px solid #ddd; border-right: 1px solid #ddd;">PARTIAL SURVIVOR BENEFIT</td>
        </tr>
        <tr>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Year #</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Age</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Monthly<br>Pension<br>(net)</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; border-left: 1px solid #ddd;">Monthly<br>Benefit to<br>Spouse</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Monthly<br>Cost to<br>Retiree</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Annual<br>Cost to<br>Retiree</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Total<br>Cost to<br>Retiree</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; border-left: 3px solid #ddd;">Monthly<br>Benefit to<br>Spouse</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Monthly<br>Cost to<br>Retiree</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Annual<br>Cost to<br>Retiree</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd; text-align: center;">Total<br>Cost to<br>Retiree</th>
        </tr>
        @forelse($SBP_details as $k => $row)
        <?php
        if ($k % 2 == 0) {
            $row_background = '#fff';
        } else {
            $row_background = '#f1f1f1';
        }
        if ($row['years_in_retirement'] == 0) {
            $years_in_ret = '<img src="' . url('images/star.svg') . '">';
        } else {
            $years_in_ret = $row['years_in_retirement'];
        }
        ?>
        <tr style="line-height: 7px; background-color: {{ $row_background }}">
            <td style="font-family:Arial, sans-serif; text-align: center;">{!! $years_in_ret !!}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">{{ $row['age'] }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; border-right: 1px solid #ddd;">${{ number_format(round($row['monthly_pension_gross'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['monthly_survivor_benefits_55'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['monthly_survivor_benefit_cost_55'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['annual_survivor_benefit_cost_55'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; border-right: 3px solid #ddd;">${{ number_format(round($row['cumulative_survivor_benefit_cost_55'])) }}</td>

            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['monthly_survivor_benefits_partial'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['monthly_survivor_benefit_cost_partial'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['annual_survivor_benefit_cost_partial'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center; border-right: 1px solid #ddd;">${{ number_format(round($row['cumulative_survivor_benefit_cost_partial'])) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="11">No Records Found</td>
        </tr>
        @endforelse
    </table>
    @endif


    <table cellpadding="12" style="font-family:Arial, sans-serif;">
        <br><br><br><br><br><br><br><br>
        <br><br><br><br><br><br><br><br>
        <tr>
            <td></td>
            @if($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers')
            <td style="font-family:Arial, sans-serif; border-top: 5px solid #1f497d; border-bottom: 5px solid #1f497d; font-size: 28px;">Special <br>Retirement <br>Supplement & <br>Social Security</td>
            @else
            <td style="font-family:Arial, sans-serif; border-top: 5px solid #1f497d; border-bottom: 5px solid #1f497d; font-size: 28px;">Social Security</td>
            @endif
        </tr>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-bottom: 5px solid #1f497d; font-size: 17px;">@if($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers')SRS &@endif SS</td>
        </tr>
    </table>
    @if($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers')
    <table>
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;"><br pagebreak="true" />FERS Special Retirement Supplement<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Purpose</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">The purpose of the FERS Special Retirement Supplement is to provide a benefit similar to Social Security between the time an employee retires and age 62. Receiving this benefit affects Social Security in absolutely no way. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Eligibility</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">FERS employees (who are retiring on an immediate, non-disability pension prior to age 62) are eligible for the FERS Special Retirement Supplement (SRS). <br></td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">FERS employees retiring under either a deferred retirement or the MRA+10 provision (who have met their MRA with at least 10 years of service, but not the 30 typically required) are not eligible to receive the SRS. FERS employees retiring under an Early Out or Discontinued Service Retirement are eligible to begin receiving the SRS when they have reached their Minimum Retirement Age. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Coverage and Cost</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">The FERS Special Retirement Supplement is generally payable between the time an employee retires and age 62 when they are eligible for Social Security benefits (regardless if they actually begin taking Social Security benefits at that time or not). Additionally, only actual full FERS years of service (no CSRS or military years) are included in the formula. This benefit is free and automatically included by OPM along with the retirement check for those who are eligible. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Receipt of Payment</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">The payment of the Special Retirement Supplement will be delayed until the Office of Personnel Management adjudicates (finalizes) an employee's retirement application (sometimes 9-12 months from the time it is received). All back payments of the Special Retirement Supplement are calculated and a lump-sum will be payable at that time. A retiree is responsible for the taxes in the year in which payment is actually received.<br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Cost of Living Adjustments</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">There are no Cost of Living Adjustments (COLAs) applied to this benefit for any type of employee. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Special Retirement Supplement Earnings Test</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">There is a limit to how much money a retiree may earn once they begin drawing the FERS Special Retirement Supplement. If a retiree has earned income (wages) exceeding the limit (which for {{date('Y')}} is ${{ number_format($ss_arr['ssEarnedIncomeLimit']) }}), then the SRS benefit will be reduced by $1 for every $2 over the limit. Keep in mind that the earnings test only applies to wages (not the CSRS/FERS pension, military pension, TSP withdrawals, dividends, capital gains, etc.). No earned wages have been assumed in the calculation of this benefit. NOTE: The earnings test does not apply to Special Provision retirees until they reach their Minimum Retirement Age. This means they can make as much money as they want prior to reaching their MRA without it affecting their SRS benefit.<br></td>
        </tr>
    </table>
    <p style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold;">Social Security</p>
    <table style=" page-break-after: always;">
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Eligibility</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">Employees who have earned at least 40 Social Security credits may begin drawing Social Security payments as early as age 62. However, continuing to work longer and/or delaying receipt of Social Security payments can increase the benefit payable.<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Social Security Earnings Test</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">There is a limit to how much money a retiree may earn once they begin drawing Social Security benefits prior to reaching their Full Retirement Age (FRA). If a retiree has earned income (wages) exceeding the limit (which for {{date('Y')}} is ${{ number_format($ss_arr['ssEarnedIncomeLimit']) }}), then the SS benefit will be reduced by $1 for every $2 over the limit. Keep in mind that the earnings test only applies to wages (not the CSRS/FERS pension, military pension, TSP withdrawals, dividends, capital gains, etc.). No earned wages have been assumed in the calculation of this benefit.<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Cost of Living Adjustments</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">Once Social Security benefits begin, recipients receive annual Cost of Living Adjustments (COLAs) immediately. The 10-year average for Social Security COLAs is {{ $ss_arr['ss_cola'] }}%<br></td>
        </tr>
    </table>
    @endif

    @if($pension['systemType'] == 'CSRS' || $pension['systemType'] == 'CSRS Offset')
    <p style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold;"><br pagebreak="true" />Social Security<br></p>

    <table style=" page-break-after: always;">
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Eligibility</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">Employees who have earned at least 40 Social Security credits may begin drawing Social Security payments as early as age 62. However, continuing to work longer and/or delaying receipt of Social Security payments can increase the benefit payable.<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Social Security Earnings Test</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">There is a limit to how much money a retiree may earn once they begin drawing Social Security benefits prior to reaching their Full Retirement Age (FRA). If a retiree has earned income (wages) exceeding the limit (which for {{date('Y')}} is ${{ number_format($ss_arr['ssEarnedIncomeLimit']) }}), then the SS benefit will be reduced by $1 for every $2 over the limit. Keep in mind that the earnings test only applies to wages (not the CSRS/FERS pension, military pension, TSP withdrawals, dividends, capital gains, etc.). No earned wages have been assumed in the calculation of this benefit.<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Cost of Living Adjustments</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">Once Social Security benefits begin, recipients receive annual Cost of Living Adjustments (COLAs) immediately. The 10-year average for Social Security COLAs is {{ $ss_arr['ss_cola'] }}%<br></td>
        </tr>
        {{-- @if($pension['systemType'] == 'CSRS' || $pension['systemType'] == 'CSRS Offset') --}}

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Windfall Elimination Provision (WEP)</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">A retiree may be affected by the Windfall Elimination Provision (WEP) if they are a CSRS, CSRS Offset, or FERS Transfer. Their Social Security benefit will be reduced because they receive a pension from CSRS (where they did not contribute to Social Security). The most WEP can reduce the Social Security payment is $498/mo. The only way to avoid the WEP completely is to have at least 30 years of "substantial earnings" under Social Security which is often difficult for CSRS employees to attain.<br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Government Pension Offset (GPO)</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px; line-height: 12px;">A retiree may also be affected by the Government Pension Offset (GPO) if they are a pure CSRS employee (not a CSRS Offset employee). For CSRS retirees who wish to take the “spousal benefit” under Social Security (taking half of their spouse’s benefit instead of their own), they will be subjected to the GPO. The GPO states that the Social Security spousal benefit will be reduced by 2/3 of the CSRS pension they are receiving. For most, this eliminates all of the Social Security spousal benefit that they would have otherwise been entitled to.<br></td>
        </tr>
        {{-- @endif --}}
    </table>
    @endif

    <table>
        <tr>
            @if($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers')
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">FERS Special Retirement Supplement & Social Security<br></td>
            @else
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Social Security<br></td>
            @endif
        </tr>
    </table>

    <p style="font-family:Arial, sans-serif; font-size: 10px;">@if(($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers') && ($pdf_data['employee']['retirementType'] == "Deferred"))As a deferred retiree, you are not eligible for the Special Retirement Supplement.@elseif($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers')The Special Retirement Supplement is subject to the Earnings Test for any wages received between your Minimum Retirement Age (MRA) and age 62, and it never receives a Cost of Living Adjustment (COLA). @endif Your Social Security benefit is subject to the Earnings Test for any wages received prior to reaching your Full Retirement Age (FRA), and it will receive a COLA immediately. The 10-year average for Social Security COLAs is {{ $ss_arr['ss_cola'] }}%. {{(($pension['systemType'] == 'CSRS' || $pension['systemType'] == 'CSRS Offset') && ($pdf_data['employee']['SSYearsEarning'] < 30)) ? 'This report reflects that you have less than 30 years of "Substantial Social Security Earnings," so the proper Windfall Elimination Provision penalty has been applied (reducing your initial Social Security benefit by $'.$wep_penalty.'/mo).' : ''}}</p>

    @php
    if($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers') {
    $col_width1 = "25%";
    $col_width2 = "10%";
    } else {
    $col_width1 = "30%";
    $col_width2 = "25%";
    }
    @endphp
    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:9px">
        <tr>
            <th style="width: 10%; font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Year #</th>
            <th style="width: 10%; font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Age</th>
            <th style="width: 25%; font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Monthly<br>Pension</th>
            @if($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers')
            <th style="width: 20%; font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">FERS Special<br>Retirement<br>Supplement</th>
            @endif
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: {{ $col_width1 }};">Social<br>Security<br>Benefit</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: {{ $col_width2 }};">Total</th>
        </tr>
        @php
        $i = 0;
        @endphp
        @forelse($ss_arr['ss_details'] as $k => $row)
        <?php
        if (($k % 2) == 0) {
            $row_back = '#fff';
        } else {
            $row_back = '#f1f1f1';
        }
        if ($row['years_in_ret'] == 0) {
            $years_in_ret = '<img src="' . url('images/star.svg') . '">';
        } else {
            $years_in_ret = $row['years_in_ret'];
        }
        ?>
        <tr style="line-height: 7px; background-color: {{ $row_back }}">
            <td style="width: 10%; font-family:Arial, sans-serif; text-align: center;">{!! $years_in_ret !!}</td>
            <td style="width: 10%; font-family:Arial, sans-serif; text-align: center;">{{$row['age']}}</td>
            <td style="width: 25%; font-family:Arial, sans-serif; text-align: center;">${{ ($row['monthly_pension'] === intval(round($row['monthly_pension'], 2))) ? number_format($row['monthly_pension']) : number_format(round($row['monthly_pension'], 2)) }}</td>
            @if($pension['systemType'] == 'FERS' || $pension['systemType'] == 'Transfers')
            <td style="width: 20%; font-family:Arial, sans-serif; text-align: center;">${{ ($row['fers_srs'] === intval(round($row['fers_srs'], 2))) ? number_format($row['fers_srs']) : number_format(round($row['fers_srs'], 2)) }}</td>
            @endif
            <td style="font-family:Arial, sans-serif; text-align: center;{{ $col_width1 }};">${{ ($row['ss_benefits'] === intval(round($row['ss_benefits'], 2))) ? number_format($row['ss_benefits']) : number_format(round($row['ss_benefits'], 2)) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;{{ $col_width2 }};">${{ ($row['total'] === intval(round($row['total'], 2))) ? number_format($row['total']) : number_format(round($row['total'], 2)) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6"></td>
        </tr>
        @endforelse
    </table>


    <!-- FEGLI block starts -->
    <table cellpadding="12" style="font-family:Arial, sans-serif; page-break-before: always; page-break-after: always;">
        <!-- page-break-before: always; -->
        <br><br><br><br><br><br><br><br>
        <br><br><br><br><br><br><br><br>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-top: 5px solid #1f497d; border-bottom: 5px solid #1f497d; font-size: 28px;">Federal <br>Employees Group <br>Life Insurance</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-bottom: 5px solid #1f497d; font-size: 17px;">FEGLI</td>
        </tr>
    </table>


    <table style="text-align: justify;">
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Federal Employee Group Life Insurance (FEGLI)<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Purpose</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">The Federal Employees Group Life Insurance program is a "group term" insurance product designed to provide life insurance protection to employees while they are working. It is possible to keep FEGLI into retirement, although it was not designed for that purpose.<br></td>
        </tr>
    </table>
    <table style="text-align: justify;">
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">FEGLI Coverage Available</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">
                <table cellpadding="3" style="font-family:Arial, sans-serif; font-size:9px">
                    <thead>
                        <tr>
                            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd;" width="20%">Coverage</th>
                            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd;" width="80%">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="20%">Basic: </td>
                            <td width="80%">Salary rounded up to the nearest $1,000 & add $2,000</td>
                        </tr>
                        <tr>
                            <td width="20%">Option A: </td>
                            <td width="80%">Flat $10,000</td>
                        </tr>
                        <tr>
                            <td width="20%">Option B (1-5 multiples): </td>
                            <td width="80%">Salary rounded up to the nearest $1,000 & multiply by the # of multiples selected (1-5)</td>
                        </tr>
                        <tr>
                            <td width="20%">Option C (1-5 multiples): </td>
                            <td width="80%">Spouse base of $5,000 (Children base of $2,500) & multiply by the # of multiples selected (1-5)<br></td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>



    <table style="text-align: justify;">
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">FEGLI Extra Benefit for Employees Under Age 45</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">Employees under the age of 45 receive extra FEGLI Basic coverage at no additional cost. The extra benefit is a multiple of the Basic coverage amount (but not to be confused with FEGLI Option B coverage). Those age 35 and under receive 2.0x the normal Basic coverage; age 36 receive 1.9x; age 37 receive 1.8x, age 38 receive 1.7x, age 39 receive 1.6x, age 40 receive 1.5x, age 41 receive 1.4x, age 42 receive 1.3x, age 43 receive 1.2x; age 44 receive 1.1x.<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Adding FEGLI Coverage</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">If an employee wishes to enroll or add to their FEGLI coverage, they may do so within 60 days of a Qualifying Life Event (like marriage, divorce, death of a spouse, or birth/adoption of a child). The amount of coverage an employee is allowed to add is dependent on the QLE experienced. For the Basic, Option A and Option B, an employee may also go through the steps to prove that they are healthy enough to medically qualify for coverage (but this is not available for Option C).<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">FEGLI Open Seasons</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">The FEGLI program does not have regular Open Seasons like with the health benefits (FEHB). In fact, FEGLI Open Seasons are incredibly rare. Should an employee wish to add coverage, see paragraph above. Should an employee wish to reduce coverage, they may do so at any time. Just remember, once coverage is canceled, it is difficult to get it back. In retirement, retirees are never permitted to add to their FEGLI coverage. <br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Postal Workers</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">Most postal workers do not pay for FEGLI Basic coverage while they are working. However, if they wish to keep this coverage in retirement, they will pay the same premium as regular employees/retirees. <br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Keeping FEGLI in Retirement</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">To keep all or part of their FEGLI coverage in place during retirement, an employee must be enrolled in the Basic coverage (and any Options they wish to keep) for at least 5 years immediately prior to retiring, and be enrolled on the day they retire. Deferred retirees will permanently lose FEGLI coverage upon leaving federal service. MRA+10 retirees are permitted to keep FEGLI coverage as long as they are receiving their pension. If they choose to postpone receiving the pension to avoid a penalty, their FEGLI coverage will be restored when they begin receiving their pension.<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Choices in Retirement</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">Employees have a number of options available to them if they would like to retain some or all of their FEGLI coverage in retirement. Each coverage type (Basic and Options A, B & C) have different elections from which to choose. For any coverage an employee wishes to keep in retirement, the coverage amount will stay the same as when employed. At age 65 (or retirement, if later), it will begin reducing if the employee has chosen not to keep the full amount in place. <br></td>
        </tr>
        <tr>
            <td>
                <table cellpadding="3" style="font-family:Arial, sans-serif; font-size:9px;">
                    <thead>
                        <tr>
                            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd;" width="30%">Coverage</th>
                            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd;" width="70%">Election</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="30%">Basic: </td>
                            <td width="70%">Keep 100%, 50% or 25% of coverage</td>
                        </tr>
                        <tr>
                            <td width="30%">Option A: </td>
                            <td width="70%">Automatically reduces to 25% of coverage</td>
                        </tr>
                        <tr>
                            <td width="30%">Option B (1-5 multiples): </td>
                            <td width="70%">Keep 100% or 0%</td>
                        </tr>
                        <tr>
                            <td width="30%">Option C (1-5 multiples): </td>
                            <td width="70%">Keep 100% or 0%</td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <table style="page-break-after: always;"></table>

    <table>
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Federal Employee Group Life Insurance (FEGLI) Coverage Elected</td>
        </tr>
    </table>
    @if($pdf_data['employee']['retirementType'] == "Deferred")
    <p style="font-family:Arial, sans-serif; margin-top:0; font-size:10px;">The coverage you currently have elected is illustrated below. As a deferred retiree, your FEGLI coverage permanently terminates once you leave federal service.</p>
    @else
    <p style="font-family:Arial, sans-serif; margin-top:0; font-size:10px;">The coverage you currently have elected is illustrated below, along with the projection as if you kept all of the coverage in place as you currently have elected.</p>
    @endif

    <table cellpadding="3" style="font-family:Arial, sans-serif; font-size:9px;">
        <thead>
            <tr>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd;" width="20%"><b>&nbsp;&nbsp;&nbsp;&nbsp; Coverage</b></th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd;" width="50%"><b>&nbsp;&nbsp;&nbsp;&nbsp; Coverage Amount</b></th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;" width="15%"><b>Bi-Weekly<br>Cost</b></th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;" width="15%"><b>Monthly<br>Cost</b></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="20%" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp; Basic: </td>
                <td width="50%" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp; ${{ isset($current_fegli['basic']) ? ($current_fegli['basic'] == 0) ? 0 : number_format(round($current_fegli['basic'])) : 0 }}</td>
                <td width="15%" style="text-align: center;">${{ number_format(round($biWeeklyCost['basic'], 2), 2)  }}</td>
                <td width="15%" style="text-align: center;">${{ number_format(round($monthlyCost['basic'], 2), 2)  }}</td>
            </tr>
            <tr>
                <td width="20%" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp; Option A: </td>
                <td width="50%" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp; ${{ isset($current_fegli['optionA']) ? ($current_fegli['optionA'] == 0) ? 0 : number_format(round($current_fegli['optionA'])) : 0 }}</td>
                <td width="15%" style="text-align: center;">${{ number_format(round($biWeeklyCost['optionA'], 2), 2)  }}</td>
                <td width="15%" style="text-align: center;">${{ number_format(round($monthlyCost['optionA'], 2), 2)  }}</td>
            </tr>
            <tr>
                <td width="20%" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp; Option B: </td>
                <td width="50%" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp; ${{ isset($current_fegli['optionB']) ? ($current_fegli['optionB'] == 0) ? 0 : number_format(round($current_fegli['optionB'])) : 0 }} </td>
                <td width="15%" style="text-align: center;">${{ number_format(round($biWeeklyCost['optionB'], 2), 2)  }}</td>
                <td width="15%" style="text-align: center;">${{ number_format(round($monthlyCost['optionB'], 2), 2)  }}</td>
            </tr>
            <tr>
                <td width="20%" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp; Option C: </td>
                <td width="50%" style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp; {{ '$'.number_format(round($current_fegli['optionC_spouse'])) . ' (spouse)' }} {{ '& $'.number_format($current_fegli['optionC_dependent']) }} (children)</td>
                <td width="15%" style="text-align: center;">${{ number_format(round($biWeeklyCost['optionC'], 2), 2) }}</td>
                <td width="15%" style="text-align: center;">${{ number_format(round($monthlyCost['optionC'], 2), 2) }}</td>
            </tr>
            <tr>
                <td style="text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp; </td>
                <td style="font-family:Arial, sans-serif; font-weight: bold; text-align: left;">&nbsp;&nbsp;&nbsp;&nbsp; Total</td>
                <td style="font-family:Arial, sans-serif; font-weight: bold; text-align: center;">${{ number_format(round($biWeeklyCost['total'], 2), 2) }}</td>
                <td style="font-family:Arial, sans-serif; font-weight: bold; text-align: center;">${{ number_format(round($monthlyCost['total'], 2), 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:8.8px;">
        <thead>
            <tr>
                <td style="border-top: 1px solid #ddd; white-space: nowrap;" colspan="3" width="20%"></td>
                <td style="font-family:Arial, sans-serif; text-align: center; border: 1px solid #ddd; white-space: nowrap;" colspan="5" width="52%">COVERAGE</td>
                <td style="font-family:Arial, sans-serif; text-align: center; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd; white-space: nowrap;" colspan="3" width="26%">COST</td>
            </tr>
            <tr>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; white-space: nowrap;" width="6%">Year#</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; white-space: nowrap;" width="5%">Age</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; white-space: nowrap;" width="9%">Annual<br>Salary</th>

                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; border-left: 1px solid #ddd; text-align: center; white-space: nowrap;" width="10%">Basic</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; white-space: nowrap;" width="10%">Option<br>A</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; white-space: nowrap;" width="10%">Option<br>B</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; white-space: nowrap;" width="11%">Option<br>C</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; border-right: 1px solid #ddd; white-space: nowrap;" width="11%">Total<br>Coverage</th>

                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; white-space: nowrap;" width="8%">Monthly<br>Cost</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; white-space: nowrap;" width="8%">Actual<br>Yearly<br>Cost</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; border-right: 1px solid #ddd; white-space: nowrap;" width="10%">Running<br>Total of<br>FEGLI<br>Cost</th>
            </tr>
        </thead>
        <tbody>
            <?php $fegli_rows_count = 1; ?>
            @forelse($fegli_arr as $k => $row)

            <?php
            if ($fegli_rows_count >= 27) {
                break;
            }
            if ($row['age'] == 81) {
                break;
            }
            if ($row['age'] == 80) {
                $row['age'] = $row['age'] . '+';
            }
            $fegli_rows_count++;
            if (($k % 2) == 0) {
                $row_back = '#fff';
            } else {
                $row_back = '#f1f1f1';
            }
            if ($row['age'] == $pension['retirementAgeY']) {
                $years_in_ret = '<img src="' . url('images/star.svg') . '">';
            } else {
                if ($row['years_in_retirement'] == 0) {
                    $years_in_ret = '-';
                } else {
                    $years_in_ret = $row['years_in_retirement'];
                }
            }
            ?>
            <tr style="line-height: 7px; background-color: {{$row_back}}">
                <td style="font-family:Arial, sans-serif; text-align: center; white-space: nowrap;" width="6%">{!! $years_in_ret !!}</td>
                <td style="font-family:Arial, sans-serif; text-align: center; white-space: nowrap;" width="5%">{{ $row['age'] }}</td>
                <td style="font-family:Arial, sans-serif; text-align: center; border-right: 1px solid #ddd; white-space: nowrap;" width="9%">${{ number_format(round($row['annual_sal'])) }}</td>

                <td style="font-family:Arial, sans-serif; text-align: center; white-space: nowrap; " width="10%">${{ number_format(round($row['basic_coverage'])) }}</td>
                <td style="font-family:Arial, sans-serif; text-align: center; white-space: nowrap; " width="10%">${{ number_format(round($row['optionA_coverage'])) }}</td>
                <td style="font-family:Arial, sans-serif; text-align: center; white-space: nowrap; " width="10%">${{ number_format(round($row['optionB_coverage'])) }}</td>
                <td style="font-family:Arial, sans-serif; text-align: center; white-space: nowrap; " width="11%">${{ number_format(round($row['optionC_coverage'])) }}</td>
                <td style="font-family:Arial, sans-serif; text-align: center; white-space: nowrap;  border-right: 1px solid #ddd;" width="11%">${{ number_format(round($row['total_coverage'])) }}</td>

                <td style="font-family:Arial, sans-serif; text-align: center; white-space: nowrap; " width="8%">${{ number_format(round($row['totalMonthly'])) }}</td>
                <td style="font-family:Arial, sans-serif; text-align: center; white-space: nowrap; " width="8%">${{ number_format(round($row['totalYearly']))}}</td>
                <td style="font-family:Arial, sans-serif; text-align: center; white-space: nowrap;  border-right: 1px solid #ddd;" width="10%">${{ number_format(round($row['runningTotalCost']))}}</td>
            </tr>
            @empty
            <tr style="line-height: 7px;">
                <td style="font-family:Arial, sans-serif; text-align: left;" colspan="11">No data found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <table style="page-break-after: always;"></table>

    @if($pdf_data['employee']['retirementType'] != "Deferred")
    <table>
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold;">Federal Employee Group Life Insurance (FEGLI) in Retirement</td>
        </tr>
    </table>
    <p style="font-family:Arial, sans-serif; font-size:10px;">Upon retiring, you will choose how much of your FEGLI coverage to keep and we have illustrated those choices below. To have any of the Optional coverage (A, B and/or C), you must have some version of the Basic coverage. The various choices and approximate monthly costs are shown below.</p>

    <table cellpadding="4" style="font-family:Arial, sans-serif; font-size:8px; text-align: center;">
        <thead>
            <tr>
                <td style="border-top: 1px solid #ddd; border-right: 2px solid #ddd; width: 5%;"></td>
                <td style="font-family:Arial, sans-serif; text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; border-top: 1px solid #ddd; border-right: 2px solid #ddd; width: 35%;" colspan="6">BASIC<br><i>Choose from:</i></td>
                <td style="font-family:Arial, sans-serif; text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; border-top: 1px solid #ddd; border-right: 2px solid #ddd; width: 12%;" colspan="2">OPTION A<br><i>Automatic:</i></td>
                <td style="font-family:Arial, sans-serif; text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; border-top: 1px solid #ddd; border-right: 2px solid #ddd; width: 24%;" colspan="4">OPTION B<br><i>Choose from:</i></td>
                <td style="font-family:Arial, sans-serif; text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; border-top: 1px solid #ddd; width: 24%;" colspan="4">OPTION C<br><i>Choose from:</i></td>
            </tr>
            <tr>
                <td style="border-bottom: 1px solid #ddd; border-right: 2px solid #ddd; width: 5%;"></td>
                <td style="font-family:Arial, sans-serif; text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd; width: 11%;" colspan="2">NO REDUCTION</td>
                <td style="font-family:Arial, sans-serif; text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd; width: 12%;" colspan="2">50% REDUCTION</td>
                <td style="font-family:Arial, sans-serif; text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; border-right: 2px solid #ddd; width: 12%;" colspan="2">75% REDUCTION</td>
                <td style="font-family:Arial, sans-serif; text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; border-right: 2px solid #ddd; width: 12%;" colspan="2">75% REDUCTION</td>
                <td style="font-family:Arial, sans-serif; text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd; width: 12%;" colspan="2">NO REDUCTION</td>
                <td style="font-family:Arial, sans-serif; text-align: center; font-weight: bold; border-bottom: 1px solid #ddd; border-right: 2px solid #ddd; width: 12%;" colspan="2">FULL REDUCTION</td>
                <td style="font-family:Arial, sans-serif; text-align: center; width: 12%; font-weight: bold; border-bottom: 1px solid #ddd; border-right: 1px solid #ddd;" colspan="2">NO REDUCTION</td>
                <td style="font-family:Arial, sans-serif; text-align: center; width: 12%; font-weight: bold; border-bottom: 1px solid #ddd;" colspan="2">FULL REDUCTION</td>
            </tr>
            <tr>
                <th style="border-bottom: 1px solid #ddd; width: 5%; border-right: 2px solid #ddd;">Age</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 11%; border-right: 1px solid #ddd;">COVERAGE<br>(cost/mo)</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 12%; border-right: 1px solid #ddd;">COVERAGE<br>(cost/mo)</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 12%; border-right: 2px solid #ddd;">COVERAGE<br>(cost/mo)</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 12%; border-right: 2px solid #ddd;">COVERAGE<br>(cost/mo)</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 12%; border-right: 1px solid #ddd;">COVERAGE<br>(cost/mo)</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 12%; border-right: 2px solid #ddd;">COVERAGE<br>(cost/mo)</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 12%; border-right: 1px solid #ddd;">COVERAGE<br>(cost/mo)</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center; width: 12%;">COVERAGE<br>(cost/mo)</th>
            </tr>
        </thead>
        <tbody>
            <?php $fegliAll_count = 1; ?>
            @foreach($fegliAllScenarios as $k => $row)
            @php
            if($fegliAll_count >= 25) {
            break;
            }
            $fegliAll_count++;
            if (($k % 2) == 0) {
            $row_back = '#fff';
            } else {
            $row_back = '#f1f1f1';
            }
            if ($row['age'] == 81) {
            break;
            }
            if ($row['age'] == 80) {
            $row['age'] = $row['age'] . '+';
            }
            @endphp
            <tr style="line-height: 7px; background-color: <?php echo $row_back; ?>">
                <td style="width: 5%; border-right: 2px solid #ddd;">{{ $row['age'] }}</td>

                <td style="width: 11%; border-right: 1px solid #ddd;">${{ number_format(round($row['basicCoverageNoReduction'])) }}<br>(${{ number_format(round($row['basicCostNoReduction'])) }})</td>

                <td style="width: 12%; border-right: 1px solid #ddd;">${{ number_format(round($row['basicCoverage50Reduction'])) }}<br>(${{ number_format(round($row['basicCost50Reduction'])) }})</td>

                <td style="width: 12%; border-right: 2px solid #ddd;">${{ number_format(round($row['basicCoverage75Reduction'])) }}<br>(${{ number_format(round($row['basicCost75Reduction'])) }})</td>

                <td style="width: 12%; border-right: 2px solid #ddd;">${{ number_format(round($row['optionACoverage'])) }}<br>(${{ number_format(round($row['OptionACost'])) }})</td>

                <td style="width: 12%; border-right: 1px solid #ddd;">${{ number_format(round($row['optionBCoverageNoReduction'])) }}<br>(${{ number_format(round($row['OptionBCostNoReduction'])) }})</td>

                <td style="width: 12%; border-right: 2px solid #ddd;">${{ number_format(round($row['optionBCoverageFullReduction'])) }}<br>(${{ number_format(round($row['OptionBCostFullReduction'])) }})</td>

                <td style="width: 12%; border-right: 1px solid #ddd;">${{ number_format(round($row['optionCCoverageNoReduction'])) }}<br>(${{ number_format(round($row['optionCCostNoReduction'])) }})</td>

                <td style="width: 12%">${{ number_format(round($row['optionCCoverageFullReduction'])) }}<br>(${{ number_format(round($row['optionCCostFullReduction'])) }})</td>
            </tr>
            @endforeach
        </tbody>

    </table>
    <table style="page-break-after: always;"></table>
    @endif
    <!-- FEHB starts -->

    <table cellpadding="12" style="font-family:Arial, sans-serif; page-break-after: always;">
        <!-- page-break-before: always; -->
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-top: 5px solid #1f497d; border-bottom: 5px solid #1f497d; font-size: 28px;">Federal <br>Employees <br>Health Benefits</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-bottom: 5px solid #1f497d; font-size: 17px;">FEHBP</td>
        </tr>
    </table>


    <table style="text-align: justify;">
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Federal Employees Health Benefits (FEHB)<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Purpose</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">The Federal Employees Health Benefits program is a "group health" insurance product designed to provide health insurance protection to employees and retirees alike. The broader FEHB program also includes the stand-alone dental and vision programs. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">FEHB Coverage Available</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">Several plan options exist within the FEHB program. Many nationwide programs are represented, as well as smaller regional plans. Open Season occurs each November/December allowing employees to enroll/cancel, as well as change carriers, plans and which family members are covered. Retirees may participate in Open Seasons in the same manner as they did as an employee (change carriers, plans, add eligible family members), but they are not permitted to enroll in retirement. It is important to note, if a retiree cancels their FEHB in retirement, they lose their FEHB benefits forever.<br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Taxes on FEHB Premiums</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">While employed, the premium for federal health coverage is paid by the employee with before-tax dollars, meaning they do not pay taxes on the dollar amount of the premium paid to FEHB. This is known as "premium conversion" and is available to most employees. Once retired, this premium is paid with after-tax dollars so it will feel more expensive than while working. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Eligibility to Keep FEHB in Retirement</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">In order to keep FEHB coverage as a retiree, an employee must have been enrolled in the FEHB program for 5 years immediately preceding retirement and be enrolled on the day they retire from federal service. Deferred retirees will permanently lose FEHB coverage upon leaving federal service. MRA+10 retirees are permitted to keep FEHB coverage as long as they are receiving their pension. If they choose to postpone receiving the pension to avoid a penalty, their FEHB coverage will be restored when they begin receiving their pension.<br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Dual Federal Couples</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">If two federal employees are married to each other and are both enrolled in FEHB and wish to keep it in retirement, the government is not concerned if they are under two self-only plans, a self plus one plan, or a self plus family plan. As long as each are enrolled on the date of their own retirement, and had been in FEHB for the 5 years immediately preceding their own retirement, then they are eligible to keep FEHB (regardless of a survivor benefit election). It is important to note, if one spouse will continue to work longer than the other, it may be wise to switch to a "self plus family" or a "self plus one" plan under the spouse who is still working to extend the tax benefit under "premium conversion" for as long as possible. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Changes to FEHB in Retirement</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">Retirees will continue to have the freedom to change their FEHB carriers and plans within the same Open Season dates as employees each November/December. FEHB premiums are the same for employees and retirees, regardless of age.</td>
        </tr>
    </table>

    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:9px; text-align: justify;">
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold;"><br pagebreak="true" />Federal Employees Health Benefits (FEHB)<br></td>
        </tr>
        {{-- @if($is_postal == 0) --}}
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">You are currently paying the following cost for Health, Dental and Vision insurance coverage:</td>
        </tr>
        <tr>
            <td>
                <table cellpadding="3" style="font-family:Arial, sans-serif; font-size:9px;">
                    <tr>
                        <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd;"></th>
                        <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; font-weight: bold; text-align: center;">Bi-Weekly Cost</th>
                        <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; font-weight: bold; text-align: center;">Monthly Cost</th>
                        <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; font-weight: bold; text-align: center;">Yearly Cost</th>
                    </tr>

                    <tr>
                        <td>Health</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['biWeekly']['health'], 2), 2)}}</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['monthly']['health'], 2), 2)}}</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['yearly']['health'], 2), 2)}}</td>
                    </tr>
                    <tr>
                        <td>Dental</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['biWeekly']['dental'], 2), 2)}}</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['monthly']['dental'], 2), 2)}}</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['yearly']['dental'], 2), 2)}}</td>
                    </tr>
                    <tr>
                        <td>Vision</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['biWeekly']['vision'], 2), 2)}}</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['monthly']['vision'], 2), 2)}}</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['yearly']['vision'], 2), 2)}}</td>
                    </tr>
                    <tr>
                        <td>Dental/Vision (combo)</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['biWeekly']['dental_and_vision'], 2), 2)}}</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['monthly']['dental_and_vision'], 2), 2)}}</td>
                        <td style="text-align: center;">${{number_format(round($current_fehb['yearly']['dental_and_vision'], 2), 2)}}</td>
                    </tr>
                    <tr>
                        <td><b>Total</b></td>
                        <td style="text-align: center;"><b>${{number_format(round($current_fehb['biWeekly']['total'], 2), 2)}}</b></td>
                        <td style="text-align: center;"><b>${{number_format(round($current_fehb['monthly']['total'], 2), 2)}}</b></td>
                        <td style="text-align: center;"><b>${{number_format(round($current_fehb['yearly']['total'], 2), 2)}}</b></td>
                    </tr>

                </table>
            </td>
        </tr>
        {{-- @endif --}}
    </table>

    @if($pdf_data['employee']['retirementType'] == "Deferred")
    <p style="font-family:Arial, sans-serif; font-size: 10px;">As a deferred retiree, your FEHB coverage permanently terminates once you leave federal service.</p>
    @else
    <p style="font-family:Arial, sans-serif; font-size: 10px;">The coverage you currently have elected is illustrated below (combining Health, Dental and Vision insurance coverage), along with the projection as if you kept the same plan. We have assumed a {{$pdf_data['empConf']['FEHBAveragePremiumIncrease'] ?? '4.83'}}% cost increase each year (which is the 10-year average).</p>
    @endif

    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:8.5px; page-break-after: always;">
        <tr>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Year #</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Age</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Bi-Weekly<br>Cost</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Monthly<br>Cost</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Yearly<br>Cost</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Running<br>Total of<br>FEHB<br>Cost</th>
        </tr>
        @foreach($healthBenifits_arr as $k => $row)
        <?php
        if (($k % 2) == 0) {
            $row_back = '#f1f1f1';
        } else {
            $row_back = '#fff';
        }
        // echo $row['age'] . " == " . $pension['retirementAgeY'] . "<br>";
        // continue;

        if ($row['age'] == $pension['retirementAgeY']) {
            $years_in_ret = '<img src="' . url('images/star.svg') . '">';
        } elseif ($row['years_in_retirement'] == 0 && ($row['age'] < $pension['retirementAgeY'])) {
            $years_in_ret = '-';
        } else {
            $years_in_ret = $row['years_in_retirement'];
        }
        ?>
        <tr style="line-height: 7px; background-color: {{ $row_back }}">
            <td style="font-family:Arial, sans-serif; text-align: center;">{!! $years_in_ret !!}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">{{ $row['age'] }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['biWeeklyTotalPremium'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['monthlyTotalPremium'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['yearlyTotalPremium'])) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center;">${{ number_format(round($row['accum'])) }}</td>
        </tr>
        @endforeach
    </table>

    <!-- FLTCIP starts -->
    <br /><br /><br /><br /><br /><br /><br /><br /><br />
    <br /><br /><br /><br /><br /><br /><br /><br />
    <table cellpadding="12" style="font-family:Arial, sans-serif; page-break-after: always;">
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-top: 5px solid #1f497d; border-bottom: 5px solid #1f497d; font-size: 28px;">Federal Long <br>Term Care <br>Insurance <br>Program</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-bottom: 5px solid #1f497d; font-size: 17px;">FLTCIP</td>
        </tr>
    </table>

    <table style="text-align: justify;">
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Federal Long Term Care Insurance Program (FLTCIP)<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">What are Considered Long Term Care Services</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">Long term care (LTC) is the kind of care that a person would need if they had an ongoing illness or disability that prevents them from performing typical "activites of daily living" (such as bathing, feeding and dressing themselves). Long term care is not the type of care received in a hospital and is not care intended to cure you. It is most often the kind of care that a person may need for the rest of their life. <br></td>
        </tr>


        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Purpose of Long Term Care Insurance</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">Long term care insurance is designed to help a person pay for long-term care services received either in a facility (such as a nursing home or assisted living facility), or in their home. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Cost of Long Term Care Services</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">The cost of long term care services can be financially devastating. A full financial plan should include a plan to pay for long term care services if and when they are needed. Consider the high out-of-pocket costs if no insurance is in place. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">How the FLTCIP Program is Structured</td>
        </tr>

        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">The Federal Long Term Care Insurance Program is a "group long-term care" insurance product designed to provide long-term care insurance protection to employees and retirees alike. FLTCIP is administered by OPM who contracts out to commercial carriers to provide the actual insurance. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Eligibility</td>
        </tr>

        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">A wide array of people are eligible to apply under FLTCIP such as federal employees, retirees, active members of the military, and qualified family members of the above (current spouses, adult children, parents, parents-in-law, and stepparents). Employees are not required to have FLTCIP in place for the 5 years immediately preceding retirement (as required under other programs like FEGLI and FEHB). Everyone applies on their own health record and pays separately.<br></td>
        </tr>
    </table>

    <table cellpadding="3" style="font-family:Arial, sans-serif; font-size:9px;">
        <tr>
            <td><span style="font-size: 11px; line-height: 11px; font-weight: bold;">Coverage available</span><br><span style="font-family:Arial, sans-serif; font-size: 10px;">The Federal Long Term Care Insurance Program offers various features to its participants:</span></td>
        </tr>

        <tr>
            <td style="border-bottom: 1px solid #ddd;" width="140">&nbsp;&nbsp;&nbsp;&nbsp;Feature</td>
            <td style="border-bottom: 1px solid #ddd;" width="300">Choices</td>
        </tr>
        <tr>
            <td width="140">&nbsp;&nbsp;&nbsp;&nbsp;Daily benefit amount:</td>
            <td width="300">{{ $pdf_data['empConf']['daily_benefit_amount'] ?? '$100-$450/day' }}</td>
        </tr>
        <tr>
            <td width="140">&nbsp;&nbsp;&nbsp;&nbsp;Benefit period:</td>
            <td width="300">{{ $pdf_data['empConf']['benefit_period'] ?? '2, 3, 5 years' }}</td>
        </tr>
        <tr>
            <td width="140">&nbsp;&nbsp;&nbsp;&nbsp;Waiting period:</td>
            <td width="190">{{ $pdf_data['empConf']['waiting_period'] ?? '90 days' }}</td>
        </tr>
        <tr>
            <td width="140">&nbsp;&nbsp;&nbsp;&nbsp;Inflation protection:</td>
            <td width="300">{{ $pdf_data['empConf']['inflation_protection'] ?? 'Automatic 3%, or Future Purchase Option' }}</td>
        </tr>
    </table>

    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:9px; page-break-after: always;">
        <tr>
            <td><span style="font-size: 11px; line-height: 11px; font-weight: bold;">Proof of Insurability</span><br><span style="font-family:Arial, sans-serif; font-size: 10px;">Whether employees purchase the government's LTC program or a private policy, they will need to prove that they are healthy enough to obtain coverage. Keep in mind that long term care companies and life insurance companies are looking for two different kinds of insurability (after all, one is worried about you dying and the other is worried about you living and needing continued care). If someone has been declined for one type of coverage, they may be approved for the other type of coverage.<br></span></td>
        </tr>

        <tr>
            <td><span style="font-size: 11px; line-height: 11px; font-weight: bold;">State Partnership Plans</span><br><span style="font-family:Arial, sans-serif; font-size: 10px;">Most states have arrangements with the private LTC insurance industry to encourage residents to purchase private LTC insurance so they do not become a burden on the state's Medicaid program right away. This state partnership plan allows a person's assets to be protected from the traditional Medicaid "spenddown" limits. However, the government's FLTCIP plan does NOT qualify under the state partnership plans.<br></span></td>
        </tr>

        <tr>
            <td><span style="font-size: 11px; line-height: 11px; font-weight: bold;">Discounts</span><br><span style="font-family:Arial, sans-serif; font-size: 10px;">There are no discounts offered through the federal program (FLTCIP). Under FLTCIP, you simply pay the premium stated for your age and the coverage you select. However, under private LTC programs there are discounts offered for a variety of circumstances (such as if you are married, if your spouse also purchases a policy, and/or if you are in excellent health). <br></span></td>
        </tr>

    </table>

    <table>
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin:0;">FLTCIP Coverage Available</td>
        </tr>
    </table>
    <p style="font-family:Arial, sans-serif; font-size: 10px;">The coverage you currently have elected is illustrated below. Although the premiums are reflected as level, the FLTCIP reserves the right to increase rates at unspecified times, so you may experience an increase to the cost of this coverage.</p>
    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:9px; text-align: center; page-break-after: always;">
        <tr>
            <th>Year #</th>
            <th>Age</th>
            <th>Bi-Weekly<br>Cost</th>
            <th>Monthly<br>Cost</th>
            <th>Yearly<br>Cost</th>
            <th>Running<br>Total of<br>FLTCIP<br>Cost</th>
        </tr>
        @php
        $total_fltcip = 0;
        $yearly_fltcip = ($fltcipBiWeekly * 26);
        $monthly_fltcip = $yearly_fltcip / 12;
        @endphp
        @for($i = $emp_age; $i <= 90; $i++) @php if($i> $retirementAge) {
            $yearsInRet = ($yearsInRet + 1);
            }
            $total_fltcip = $total_fltcip + $yearly_fltcip;
            $print_total = round($total_fltcip);
            $print_biweek = round($fltcipBiWeekly);
            $print_monthly = round($monthly_fltcip);
            $print_yearly = round($yearly_fltcip);
            if($i%2 == 0) {
            $row_back = '#f1f1f1';
            } else {
            $row_back = '#fff';
            }
            if($yearsInRet > 0) {
            $years_in_ret = $yearsInRet;
            } else {
            if($i == $pension['retirementAgeY']) {
            $years_in_ret = '<img src="'.url('images/star.svg').'">';
            } else {
            $years_in_ret = '-';
            }
            }
            @endphp
            <tr style="line-height: 7px; background-color: {{ $row_back }}">
                <td>{!! $years_in_ret !!}</td>
                <td>{{$i}}</td>
                <td>${{ number_format($print_biweek) }}</td>
                <td>${{ number_format($print_monthly) }}</td>
                <td>${{ number_format($print_yearly) }}</td>
                <td>${{ number_format($print_total) }}</td>
            </tr>
            @endfor
    </table>

    <!-- **********TSP Module starts*********** -->
    <table cellpadding="12" style="font-family:Arial, sans-serif; page-break-after: always;">
        <br><br><br><br><br><br><br><br>
        <br><br><br><br><br><br><br><br>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-top: 5px solid #1f497d; border-bottom: 5px solid #1f497d; font-size: 28px;">Thrift Savings <br>Plan</td>
        </tr>
        <tr>
            <td></td>
            <td style="font-family:Arial, sans-serif; border-bottom: 5px solid #1f497d; font-size: 17px;">TSP</td>
        </tr>
    </table>


    <table style="text-align: justify;">
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Thrift Savings Plan (TSP) for FERS<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Eligibility</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">The Thrift Savings Plan (TSP) is similar to a private sector 401(k) program, and is considered a "defined contribution" plan. Both CSRS and FERS employees are eligible to contribute to the TSP. <br></td>
        </tr>

        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Regular Contribution Limits</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">The IRS has established limits to the amount employees may contribute to TSP annually. For {{ $tsp_configurations['year_of_contribution'] }}, they may contribute up to ${{ number_format($tsp_configurations['deff_limit']) }} per year which may be made by selecting a specific dollar amount or percentage of their salary per pay period. <br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Catch-up Contribution Limits</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">Beginning in the calendar year an employee turns age 50, the IRS allows them to contribute an additional ${{ number_format($tsp_configurations['catchup_limit']) }} on top of the normal ${{ number_format($tsp_configurations['deff_limit']) }} limit. There is no longer a separate election for catch-up contributions. Once an employee reaches ${{ number_format($tsp_configurations['deff_limit']) }} in regular contributions, the extra contributions will automatically “spillover” to count as catch-up contributions. <br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Agency Contributions & Matching Funds</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">For FERS employees, their agency automatically contributes 1% of their pay into their Traditional TSP account (regardless if the employee personally contributes anything into TSP). If the employee contributes at least 5% of their salary, the agency will contribute an additional 4% of their salary into their TSP account. It is important that an employee spreads their TSP contributions over all 26 pay periods of the year in order to take full advantage of the 5% "match" that the agency offers. If there is a pay period in which an employee is not contributing, they will lose the 4% match for that pay period. <br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">Tax Advantages</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">The Traditional TSP is a tax-deferred retirement vehicle which means employees receive an immediate tax advantage by not paying tax on the amount they contribute into their Traditional TSP in the current tax year. However, they will pay taxes on both the principal and any growth when the funds are withdrawn at a later date. <br></td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">The Roth TSP is an after-tax retirement vehicle which means employees do not receive an immediate tax advantage now (as they must pay income tax "now" on the amount they contribute into their Roth TSP). However, they will not pay tax on the principal or any growth when the funds are withdrawn from the Roth TSP at a later date. This is true as long as they meet both of the IRS Roth rules when they receive the distribution: 1) they are at least age 59 1/2, and 2) at least 5 years have passed since the Roth TSP was first established. If the Roth TSP is transferred to a Roth IRA, 5 years must have passed since the first Roth IRA that person owns was first funded.<br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">TSP Loans</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">If an employee takes a loan against their TSP account, it is important that they continue to pay back that loan based on the agreed terms established with the TSP. Should an employee default on their payments while still working (or if they should retire prior to having the loan paid off), the TSP will declare a taxable event on the entire outstanding balance due and the employee will be responsible for taxes and applicable penalties. They are given 90 days to repay any outstanding balance <br></td>
        </tr>
        <tr>
            <td style="font-size: 11px; line-height: 13px; font-weight: bold;">The 5 funds available</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">Employees have a choice of 5 funds from which to invest in the TSP, and they may decide to contribute to these funds in any combination they wish. Lifecycle Funds are available and are simply a mixture of the 5 regular funds that become progressively more conservative as an employee nears the timeline by which they plan to begin withdrawing the funds.<br></td>
        </tr>
    </table>
    <table cellpadding="3" style="font-family:Arial, sans-serif; font-size:9px;">
        <thead>
            <tr>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; width: 10%;">&nbsp;&nbsp;&nbsp;Fund<br>&nbsp;&nbsp;&nbsp;Name</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; width: 30%;">Description</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; width: 40%;">Index</th>
                <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; width: 20%;">Rate of Return<br>(10-yr average)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 10%;">&nbsp;&nbsp;&nbsp;G Fund</td>
                <td style="width: 30%;">Government securities</td>
                <td style="width: 40%;">No index (interest rate based)</td>
                <td style="width: 20%;">{{$tsp_configurations['gfund']}}%</td>
            </tr>
            <tr>
                <td style="width: 10%;">&nbsp;&nbsp;&nbsp;F Fund</td>
                <td style="width: 30%;">Mix of government & corporate bonds</td>
                <td style="width: 40%;">Barclays Capital U.S. Aggregate Bond</td>
                <td style="width: 20%;">{{$tsp_configurations['ffund']}}%</td>
            </tr>
            <tr>
                <td style="width: 10%;">&nbsp;&nbsp;&nbsp;C Fund</td>
                <td style="width: 30%;">Large-cap U.S. stocks</td>
                <td style="width: 40%;">Standard & Poor's 500</td>
                <td style="width: 20%;">{{$tsp_configurations['cfund']}}%</td>
            </tr>
            <tr>
                <td style="width: 10%;">&nbsp;&nbsp;&nbsp;S Fund</td>
                <td style="width: 30%;">Small & mid-cap U.S. stocks</td>
                <td style="width: 40%;">Dow Jones U.S. Completion Total Stock Market</td>
                <td style="width: 20%;">{{$tsp_configurations['sfund']}}%</td>
            </tr>
            <tr>
                <td style="width: 10%;">&nbsp;&nbsp;&nbsp;I Fund</td>
                <td style="width: 30%;">Mostly large-cap foreign stocks</td>
                <td style="width: 40%;">Morgan Stanley Capital International EAFE (Europe, Australasia & the Far East)</td>
                <td style="width: 20%;">{{$tsp_configurations['ifund']}}%</td>
            </tr>
        </tbody>
    </table>
    <table style="page-break-after: always;"></table>

    <table>
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin-top:0;">Thrift Savings Plan (TSP)<br></td>
        </tr>
        <tr>
            <td style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 13px;">New Contributions & Additions</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">Your current contributions and any outstanding loan repayments are shown below. You may designate any percentage that you wish to go to the Traditional or Roth side of your account.<br></td>
        </tr>
    </table>


    <!-- /* ********************************* */ -->


    <table cellpadding="3" style="font-family:Arial, sans-serif; font-size:9px; text-align: left;">
        <tr style="border-bottom: 1px solid #ddd;">
            <td style="font-family:Arial, sans-serif; font-weight: bold; border-bottom: 1px solid #ddd; width: 40%;">Your Contributions</td>
            <td style="font-family:Arial, sans-serif; font-weight: bold; border-bottom: 1px solid #ddd; width: 20%; text-align: center;">Bi-weekly</td>
            <td style="font-family:Arial, sans-serif; font-weight: bold; border-bottom: 1px solid #ddd; width: 20%; text-align: center;">Monthly</td>
            <td style="font-family:Arial, sans-serif; font-weight: bold; border-bottom: 1px solid #ddd; width: 20%; text-align: center;">Yearly</td>
        </tr>
        <tr>
            <td style="width: 40%;">Traditional</td>
            <td style="width: 20%; text-align: center;">${{ isset($tsp['ContributionRegular']) ? number_format(round($tsp['ContributionRegular'], 2), 2) : 0 }}</td>
            <td style="width: 20%; text-align: center;">${{ isset($tsp['ContributionRegular']) ? number_format(round((($tsp['ContributionRegular'] * 26) / 12), 2), 2) : 0 }}</td>
            <td style="width: 20%; text-align: center;">${{ isset($tsp['ContributionRegular']) ? number_format(round(($tsp['ContributionRegular'] * 26), 2), 2) : 0 }}</td>
        </tr>
        <tr>
            <td style="width: 40%;">Roth</td>
            <td style="width: 20%; text-align: center;">${{ isset($tsp['ContributionCatchUp']) ? number_format(round($tsp['ContributionCatchUp'], 2), 2) : 0 }}</td>
            <td style="width: 20%; text-align: center;">${{ isset($tsp['ContributionCatchUp']) ? number_format(round((($tsp['ContributionCatchUp'] * 26) / 12), 2), 2) : 0 }}</td>
            <td style="width: 20%; text-align: center;">${{ isset($tsp['ContributionCatchUp']) ? number_format(round(($tsp['ContributionCatchUp'] * 26), 2), 2) : 0 }}</td>
        </tr>
    </table>

    <table cellpadding="3" style="font-family:Arial, sans-serif; font-size:9px; text-align: left;">
        <tr>
            <td style="font-family:Arial, sans-serif; font-weight: bold;border-bottom: 1px solid #ddd; width: 40%;">Loan Repayments</td>
            <td style="font-family:Arial, sans-serif; font-weight: bold;border-bottom: 1px solid #ddd; width: 20%;"></td>
            <td style="font-family:Arial, sans-serif; font-weight: bold;border-bottom: 1px solid #ddd; width: 20%;"></td>
            <td style="font-family:Arial, sans-serif; font-weight: bold;border-bottom: 1px solid #ddd; width: 20%;"></td>
        </tr>
        <tr>
            <td style="width: 40%;">General Purpose @if($tsp['payoff_date_general'] != '')(estimated payoff {{ date('m/d/Y', strtotime($tsp['payoff_date_general'])) }}) @endif</td>
            <td style="width: 20%; text-align: center;">${{ ($tsp['loan_repayment_general'] === intval(round($tsp['loan_repayment_general'], 2))) ? number_format($tsp['loan_repayment_general']) : number_format(round($tsp['loan_repayment_general'], 2), 2) }}</td>
            <td style="width: 20%; text-align: center;">${{ ($tsp['loan_repayment_general'] === intval(round($tsp['loan_repayment_general'], 2))) ? number_format(($tsp['loan_repayment_general'] * 26) / 12) : number_format(round((($tsp['loan_repayment_general'] * 26) / 12), 2), 2) }}</td>
            <td style="width: 20%; text-align: center;">${{ ($tsp['loan_repayment_general'] === intval(round($tsp['loan_repayment_general'], 2))) ? number_format($tsp['loan_repayment_general'] * 26) : number_format(round(($tsp['loan_repayment_general'] * 26), 2), 2) }}</td>
        </tr>

        <tr>
            <td style="width: 40%;">Residential @if($tsp['payoff_date_residential'] != '')(estimated payoff {{ date('m/d/Y', strtotime($tsp['payoff_date_residential'])) }}) @endif</td>
            <td style="width: 20%; text-align: center;">${{ ($tsp['loan_repayment_residential'] === intval(round($tsp['loan_repayment_residential'], 2))) ? number_format($tsp['loan_repayment_residential']) : number_format(round($tsp['loan_repayment_residential'], 2), 2) }}</td>
            <td style="width: 20%; text-align: center;">${{ ($tsp['loan_repayment_residential'] === intval(round($tsp['loan_repayment_residential'], 2))) ? number_format(($tsp['loan_repayment_residential'] * 26) / 12) : number_format(round((($tsp['loan_repayment_residential'] * 26) / 12), 2), 2) }}</td>
            <td style="width: 20%; text-align: center;">${{ ($tsp['loan_repayment_residential'] === intval(round($tsp['loan_repayment_residential'], 2))) ? number_format($tsp['loan_repayment_residential'] * 26) : number_format(round(($tsp['loan_repayment_residential'] * 26), 2), 2) }}<br></td>
        </tr>
    </table>

    <!-- /*********************************** */ -->

    <table>
        <tr>
            <td style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 13px;">Matching Funds</td>
        </tr>
        @if($pension['systemType'] == 'FERS')
        @if($projected_ending_balance_data['sal_percentage_in_contri'] >= 4.95)
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">In order to receive the full match from your agency, you must contribute at least 5% of your salary each pay period. You are currently contributing approximately {{ round($projected_ending_balance_data['sal_percentage_in_contri']) }}% of your salary, so you are receiving the full match from your agency.<br></td>
        </tr>
        @else
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">To receive the full agency match, you must contribute 5% of your salary each pay period. You are currently contributing {{ round($projected_ending_balance_data['sal_percentage_in_contri']) }}%, so you are not receiving the full match. By contributing at your current level, you are missing out on ${{ $projected_ending_balance_data['missing_out_amount'] }} of matching money this year. To receive the full match, you must contribute at least ${{ $projected_ending_balance_data['emp_min_contri_for_full_agency_contri'] }} each pay period.<br></td>
        </tr>
        @endif
        @else
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 10px;">As a CSRS employee, you receive no matching funds from your agency.<br></td>
        </tr>
        @endif
        <tr>
            <td style="font-weight: bold; font-size: 11px; margin: 0; padding: 0; line-height: 13px;">Current Balance & Growth</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size:10px">In order to project an estimate of your TSP account balance at retirement, we have made some assumptions on your continued participation in the TSP and applied the 10-year average rate of return for each fund. Of course, past performance is no guarantee of future results. Investments are subject to market volatility and may lose value.<br></td>
        </tr>
    </table>





    <!-- **************************** -->
    <table cellpadding="5" style="font-family:Arial, sans-serif; font-size:8.5px; text-align: center;">
        <thead>
            <tr style="border-bottom: 1px solid #ddd;">
                <th><b>Fund<br>Name</b></th>
                <th><b>Current Balance<br>of Existing Money</b></th>
                <th><b>Allocation for<br>New Contributions</b></th>
                <th><b>Projected<br>Ending Balance</b></th>
            </tr>
        </thead>
        <tbody>
            <tr style="line-height: 9px;">
                <td>G Fund</td>
                <td>${{ number_format(round($tsp['GFund'])) }}</td>
                <td>{{round($tsp['GFundDist'])}}%</td>
                <td>${{ number_format(round($tsp['new_balance']['GFund'], 2)) }}</td>
            </tr>
            <tr style="background-color: #f1f1f1; line-height: 9px;">
                <td>F Fund</td>
                <td>${{ number_format(round($tsp['FFund'])) }}</td>
                <td>{{round($tsp['FFundDist'])}}%</td>
                <td>${{ number_format(round($tsp['new_balance']['FFund'], 2)) }}</td>
            </tr>
            <tr style="line-height: 9px;">
                <td>C Fund</td>
                <td>${{ number_format(round($tsp['CFund'])) }}</td>
                <td>{{round($tsp['CFundDist'])}}%</td>
                <td>${{ number_format(round($tsp['new_balance']['CFund'], 2)) }}</td>
            </tr>
            <tr style="background-color: #f1f1f1; line-height: 9px;">
                <td>S Fund</td>
                <td>${{ number_format(round($tsp['SFund'])) }}</td>
                <td>{{round($tsp['SFundDist'])}}%</td>
                <td>${{ number_format(round($tsp['new_balance']['SFund'], 2)) }}</td>
            </tr>
            <tr style="line-height: 9px;">
                <td>I Fund</td>
                <td>${{ number_format(round($tsp['IFund'])) }}</td>
                <td>{{ round($tsp['IFundDist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['IFund'], 2)) }}</td>
            </tr>
            <tr style="background-color: #f1f1f1; line-height: 9px;">
                <td>L Income</td>
                <td>${{ number_format(round($tsp['LIncome'])) }}</td>
                <td>{{ round($tsp['LIncomeDist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['LIncome'], 2)) }}</td>
            </tr>
            <tr style="line-height: 9px;">
                <td>L 2025</td>
                <td>${{ number_format(round($tsp['L2025'])) }}</td>
                <td>{{ round($tsp['L2025Dist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['L2025'], 2)) }}</td>
            </tr>
            <tr style="background-color: #f1f1f1; line-height: 9px;">
                <td>L 2030</td>
                <td>${{ number_format(round($tsp['L2030'])) }}</td>
                <td>{{ round($tsp['L2030Dist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['L2030'], 2)) }}</td>
            </tr>
            <tr style="line-height: 9px;">
                <td>L 2035</td>
                <td>${{ number_format(round($tsp['L2035'])) }}</td>
                <td>{{ round($tsp['L2035Dist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['L2035'], 2)) }}</td>
            </tr>
            <tr style="background-color: #f1f1f1; line-height: 9px;">
                <td>L 2040</td>
                <td>${{ number_format(round($tsp['L2040'])) }}</td>
                <td>{{ round($tsp['L2040Dist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['L2040'], 2)) }}</td>
            </tr>
            <tr style="line-height: 9px;">
                <td>L 2045</td>
                <td>${{ number_format(round($tsp['L2045'])) }}</td>
                <td>{{ round($tsp['L2045Dist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['L2045'], 2)) }}</td>
            </tr>
            <tr style="background-color: #f1f1f1; line-height: 9px;">
                <td>L 2050</td>
                <td>${{ number_format(round($tsp['L2050'])) }}</td>
                <td>{{ round($tsp['L2050Dist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['L2050'], 2)) }}</td>
            </tr>
            <tr style="line-height: 9px;">
                <td>L 2055</td>
                <td>${{ number_format(round($tsp['L2055'])) }}</td>
                <td>{{ round($tsp['L2055Dist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['L2055'], 2)) }}</td>
            </tr>
            <tr style="background-color: #f1f1f1; line-height: 9px;">
                <td>L 2060</td>
                <td>${{ number_format(round($tsp['L2060'])) }}</td>
                <td>{{ round($tsp['L2060Dist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['L2060'], 2)) }}</td>
            </tr>
            <tr style="line-height: 9px;">
                <td>L 2065</td>
                <td>${{ number_format(round($tsp['L2065'])) }}</td>
                <td>{{ round($tsp['L2065Dist']) }}%</td>
                <td>${{ number_format(round($tsp['new_balance']['L2065'], 2)) }}</td>
            </tr>
            <tr style="font-weight: bold; line-height: 9px;">
                <td>Total</td>
                <td>${{ number_format(round($tsp['totalCurrentBalance'])) }}</td>
                <td>{{ $tsp['GFundDist'] + $tsp['FFundDist'] + $tsp['CFundDist'] + $tsp['SFundDist'] + $tsp['IFundDist'] + $tsp['LIncomeDist'] + $tsp['L2025Dist'] + $tsp['L2030Dist'] + $tsp['L2035Dist'] + $tsp['L2040Dist'] + $tsp['L2045Dist'] + $tsp['L2050Dist'] + $tsp['L2055Dist'] + $tsp['L2060Dist'] + $tsp['L2065Dist'] }}%
                    <!-- 100% -->
                </td>
                <td>${{ isset($tsp['new_balance']['new_balance']) ? number_format(round($tsp['new_balance']['new_balance'], 2)) : "" }}<br></td>
            </tr>
        </tbody>
    </table>

    <table>
        <tr>
            <td style="font-weight: bold; font-size: 10px; margin: 0; padding: 0; line-height: 11px;">IRS Early Withdrawal Penalty</td>
        </tr>
        <tr>
            <td style="font-family:Arial, sans-serif; font-size: 9px;">{{ $irs_penalty_text }}</td>
        </tr>
    </table>

    <!-- TSP module ends -->
    <!-- Ending pages -->
    <table style="page-break-before: always;">
        <tr>
            <td style="font-size: 16px; font-family:Arial, sans-serif; text-align: center; color: #1f497d; font-weight: bold; margin:0;">Federal Employee Self-Assessment of Potential Action Items for <br> {{ $pdf_data['employee']['EmployeeName'] }}</td>
        </tr>
    </table>
    <p style="font-family:Arial, sans-serif; font-size: 10px; text-align: justify;">Throughout our review of your federal benefits, please take note of the areas in which you feel you would benefit from by exploring alternative solutions or otherwise need further professional guidance. At the end of our meeting, we will review this checklist of potential action items to determine if it makes sense to work together and identifying our next steps.</p>
    <p style="font-family:Arial, sans-serif; font-size: 10px; font-weight: bold;">Mark the areas which you would benefit from my continued professional guidance:</p>


    <ul style="font-family:Arial, sans-serif; font-size: 10px; color: #2e2e2e; font-weight: bold; padding-left: 18px; list-style-type:img|svg|3|3|images/rectangle.svg;">
        <li style="font-family:Arial, sans-serif; font-size: 11px; line-height: 20px; color: #1f497d;">
            Pension (FERS)
            <ul style="font-family:Arial, sans-serif; list-style-type:img|svg|3|3|images/circle.svg; font-size: 10px; color: #2e2e2e; font-weight: normal; padding-left: 18px;">
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>What is the best date for me to retire from federal service?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>Is it worth it to make deposits, redeposit and/or military deposits?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How can I retire with enough guaranteed income to be comfortable?</i></li>
            </ul>
        </li>
        <li style="font-family:Arial, sans-serif; font-size: 11px; line-height: 20px; color: #1f497d;">
            Survivor Benefit Plan (SBP)
            <ul style="font-family:Arial, sans-serif; list-style-type:img|svg|3|3|images/circle.svg; font-size: 10px; color: #2e2e2e; font-weight: normal; padding-left: 18px;">
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How do I secure enough survivor benefit to protect health insurance for my spouse?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>What are the most cost-effective options within the Survivor Benefit Plan?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How would a life insurance option differ from the SBP option?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How do I determine the amount and type of life insurance I actually need to protect my family?</i></li>
            </ul>
        </li>

        @if($pension['systemType'] == 'FERS')
        <li style="font-family:Arial, sans-serif; font-size: 11px; line-height: 20px; color: #1f497d;">
            Social Security and the FERS Special Retirement Supplement
            <ul style="font-family:Arial, sans-serif; list-style-type:img|svg|3|3|images/circle.svg; font-size: 10px; color: #2e2e2e; font-weight: normal; padding-left: 18px;">
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>What age should I begin drawing Social Security, and how am I impacted by my spouse’s benefit?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How do I maximize the amount I draw out of the Social Security program?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>What happens if I keep working while drawing Social Security or the Special Retirement Supplement?</i></li>
            </ul>
        </li>
        @endif

        <li style="font-family:Arial, sans-serif; font-size: 11px; line-height: 20px; color: #1f497d;">
            Thrift Savings Plan (TSP)
            <ul style="font-family:Arial, sans-serif; list-style-type:img|svg|3|3|images/circle.svg; font-size: 10px; color: #2e2e2e; font-weight: normal; padding-left: 18px;">
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>Am I putting the right amount into TSP, and how should I be invested between now and retirement?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>Should I consider the Roth TSP or other tax-free options?</i></li>
                <li><i>How do withdrawal options from TSP compare to withdrawal options from a private account?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How can I make the funds I currently have in TSP last for my lifetime once I retire?</i></li>
            </ul>
        </li>
        <li style="font-family:Arial, sans-serif; font-size: 11px; line-height: 20px; color: #1f497d;">
            Federal Employees Group Life Insurance (FEGLI)
            <ul style="font-family:Arial, sans-serif; list-style-type:img|svg|3|3|images/circle.svg; font-size: 10px; color: #2e2e2e; font-weight: normal; padding-left: 18px;">
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>Can you help me select the most cost-effective, advantageous FEGLI options in retirement?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How do FEGLI options compare to private life insurance options?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How do I determine the amount and type of life insurance I actually need to protect my family?</i></li>
            </ul>
        </li>
        <li style="font-family:Arial, sans-serif; font-size: 11px; line-height: 20px; color: #1f497d;">Federal Employees Health Benefits (FEHB)
            <ul style="font-family:Arial, sans-serif; list-style-type:img|svg|3|3|images/circle.svg; font-size: 10px; color: #2e2e2e; font-weight: normal; padding-left: 18px;">
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How can I secure FEHB coverage for my spouse and/or eligible family members after I pass away?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>Should I enroll in Medicare at age 65, and what should I do with my FEHB coverage?</i></li>
            </ul>
        </li>
        <li style="font-family:Arial, sans-serif; font-size: 11px; line-height: 20px; color: #1f497d;">
            Federal Long Term Care Insurance Program (FLTCIP)
            <ul style="font-family:Arial, sans-serif; list-style-type:img|svg|3|3|images/circle.svg; font-size: 10px; color: #2e2e2e; font-weight: normal; padding-left: 18px;">
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>Can you help me to explore my options under the FLTCIP?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How does FLTCIP compare to private long-term care insurance programs?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>Are there other ways to have long-term care benefits without purchasing LTC insurance?</i></li>
            </ul>
        </li>
        <li style="font-family:Arial, sans-serif; font-size: 11px; line-height: 20px; color: #1f497d;">
            General Concerns for Retirement
            <ul style="font-family:Arial, sans-serif; list-style-type:img|svg|3|3|images/circle.svg; font-size: 10px; color: #2e2e2e; font-weight: normal; padding-left: 18px;">
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How do I keep from running out of money?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>How can I minimize the effect taxes will have on me in retirement?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>Are my federal benefits in alignment with the other financial parts of my life?</i></li>
                <li style="font-family:Arial, sans-serif; line-height: 17px;"><i>Do I have my financial affairs in order, and what is on the financial horizon that I should be aware of?</i></li>
            </ul>
        </li>
    </ul>
</body>

</html>
