<table cellpadding="5" style="font-family:Arial, sans-serif; font-size:9px; page-break-after: always;">
        <tr>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Year #</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Age</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Bi-Weekly<br>Cost</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Monthly<br>Cost</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Yearly<br>Cost</th>
            <th style="font-family:Arial, sans-serif; border-bottom: 1px solid #ddd; text-align: center;">Running<br>Total of<br>FEHB<br>Cost</th>
        </tr>
        <?php
        $hbYearInRet = $yearsInRet;
        $hb_emp_age = $emp_age;
        $hbBiWeeklyCost = $healthBenifits['biWeekly']['total'];
        $hbMonthlyCost = $healthBenifits['monthly']['total'];
        $hbYearlyCost = $healthBenifits['yearly']['total'];

        if (($emp_age < $retirementAge) && ($is_postal == 1)) {
            $hbBiWeeklyCost = 0;
            $hbMonthlyCost = 0;
            $hbYearlyCost = 0;
        } elseif (($emp_age == $retirementAge) && ($is_postal == 1)) {
            $hbBiWeeklyCost = $healthBenifits['biWeekly']['total'] + ($healthBenifits['biWeekly']['total'] * (15 / 100));
            $hbMonthlyCost = $healthBenifits['monthly']['total'] + ($healthBenifits['monthly']['total'] * (15 / 100));
            $hbYearlyCost = $healthBenifits['yearly']['total'] + ($healthBenifits['yearly']['total'] * (15 / 100));
        }
        $hbIncrement = ($hb_premium_inc / 100) * $hbBiWeeklyCost;
        $fehbTotal = $hbYearlyCost;
        ?>
        @for($i=$emp_age; $i <=90; $i++) 
        <?php
            if ($i > $retirementAge) {
                $hbYearInRet = $hbYearInRet + 1;
            }
            if (($i < $retirementAge) && ($is_postal == 1)) {
                $hbBiWeeklyCost = 0;
                $hbMonthlyCost = 0;
                $hbYearlyCost = 0;
            } elseif (($i >= $retirementAge)) { 
                $hbBiWeeklyCost = $hbBiWeeklyCost + $hbIncrement;
                $hbYearlyCost = $hbBiWeeklyCost * 26;
                $hbMonthlyCost = $hbYearlyCost / 12;
                $fehbTotal = $fehbTotal + $hbYearlyCost;
            }
            if (($i == $retirementAge) && ($is_postal == 1)) {
                $hbBiWeeklyCost = $healthBenifits['biWeekly']['total'] + $hbIncrement;
                $hbYearlyCost = $hbBiWeeklyCost * 26;
                $hbMonthlyCost = $hbYearlyCost / 12;
                $fehbTotal = $fehbTotal + $hbYearlyCost;
            }
            $hbIncrement = ($hb_premium_inc / 100) * $hbBiWeeklyCost;
            if (($i % 2) == 0) {
                $row_back = '#f1f1f1';
            } else {
                $row_back = '#fff';
            }
            if ($hbYearInRet > 0) {
                $years_in_ret = $hbYearInRet;
            } else {
                if ($i == $retirementAge) {
                    $years_in_ret = '<img src="'.url('images/star.png').'">';
                } else {
                    $years_in_ret = '-';
                }
            }
        ?> 
        <tr style="line-height: 7px; background-color: {{$row_back}}">
            <td style="font-family:Arial, sans-serif; text-align: center">{!! $years_in_ret !!}</td>
            <td style="font-family:Arial, sans-serif; text-align: center">{{$hb_emp_age++}}</td>
            <td style="font-family:Arial, sans-serif; text-align: center">${{ number_format(round($hbBiWeeklyCost)) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center">${{ number_format(round($hbMonthlyCost)) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center">${{ number_format(round($hbYearlyCost)) }}</td>
            <td style="font-family:Arial, sans-serif; text-align: center">${{ number_format(round($fehbTotal)) }}</td>
            </tr>
            @endfor
    </table>