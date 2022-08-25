<?php

namespace App\Helpers;

class Common
{
    public function getPremiumCostMultiplierFEGLI($emp_age = null) // monthly
    {
        if ($emp_age == null) {
            return false;
        }
        $conf_name = $this->getConfiguarationNameWhileWorking($emp_age);
        $basic = resolve('employee')->getSystemConfigurations($conf_name['basic']);
        $optionA = resolve('employee')->getSystemConfigurations($conf_name['optionA']);
        $optionB = resolve('employee')->getSystemConfigurations($conf_name['optionB']);
        $optionC = resolve('employee')->getSystemConfigurations($conf_name['optionC']);
        return [
            'premiumMultiple_basic' => $basic,
            'premiumMultiple_A' => $optionA,
            'premiumMultiple_B' => $optionB,
            'premiumMultiple_C' => $optionC,
        ];
    }

    public function getPremiumCostMultiplierFEGLI_old($emp_age = null) // monthly
    {
        if ($emp_age == null) {
            return false;
        }

        if ($emp_age < 35) {
            $premiumMultiple_A = 0.043;
            $premiumMultiple_B = 0.043;
            $premiumMultiple_C = 0.480;
        } elseif ($emp_age >= 35 && $emp_age <= 39) {
            $premiumMultiple_A = 0.065;
            $premiumMultiple_B = 0.065;
            $premiumMultiple_C = 0.590;
        } elseif ($emp_age >= 40 && $emp_age <= 44) {
            $premiumMultiple_A = 0.087;
            $premiumMultiple_B = 0.087;
            $premiumMultiple_C = 0.890;
        } elseif ($emp_age >= 45 && $emp_age <= 49) {
            $premiumMultiple_A = 0.152;
            $premiumMultiple_B = 0.152;
            $premiumMultiple_C = 1.280;
        } elseif ($emp_age >= 50 && $emp_age <= 54) {
            $premiumMultiple_A = 0.238;
            $premiumMultiple_B = 0.238;
            $premiumMultiple_C = 1.990;
        } elseif ($emp_age >= 55 && $emp_age <= 59) {
            $premiumMultiple_A = 0.433;
            $premiumMultiple_B = 0.433;
            $premiumMultiple_C = 3.210;
        } elseif ($emp_age >= 60 && $emp_age <= 64) {
            $premiumMultiple_A = 1.30;
            $premiumMultiple_B = 0.953;
            $premiumMultiple_C = 5.850;
        } elseif ($emp_age >= 65 && $emp_age <= 69) {
            $premiumMultiple_A = 1.30;
            $premiumMultiple_B = 1.170;
            $premiumMultiple_C = 6.800;
        } elseif ($emp_age >= 70 && $emp_age <= 74) {
            $premiumMultiple_A = 1.30;
            $premiumMultiple_B = 2.080;
            $premiumMultiple_C = 8.300;
        } elseif ($emp_age >= 75 && $emp_age <= 79) {
            $premiumMultiple_A = 1.30;
            $premiumMultiple_B = 3.900;
            $premiumMultiple_C = 11.400;
        } elseif ($emp_age >= 80) {
            $premiumMultiple_A = 1.30;
            $premiumMultiple_B = 5.720;
            $premiumMultiple_C = 15.600;
        }
        $data = [
            'premiumMultiple_basic' => 0.325,
            'premiumMultiple_A' => $premiumMultiple_A,
            'premiumMultiple_B' => $premiumMultiple_B,
            'premiumMultiple_C' => $premiumMultiple_C,
        ];
        return $data;
    }

    public function getPremiumCostMultiplierFEGLIBiWeekly($emp_age = null) // biWeekly
    {
        if ($emp_age == null) {
            return false;
        }

        $res = $this->getPremiumCostMultiplierFEGLI($emp_age);

        return [
            'premiumMultiple_basic' => (($res['premiumMultiple_basic'] * 12) / 26),
            'premiumMultiple_A' => (($res['premiumMultiple_A'] * 12) / 26),
            'premiumMultiple_B' => (($res['premiumMultiple_B'] * 12) / 26),
            'premiumMultiple_C' => (($res['premiumMultiple_C'] * 12) / 26),
        ];

        // if ($emp_age < 35) {
        //     $premiumMultiple_A = 0.02;
        //     $premiumMultiple_B = 0.02;
        //     $premiumMultiple_C = 0.20;
        // } elseif ($emp_age >= 35 && $emp_age <= 39) {
        //     $premiumMultiple_A = 0.02;
        //     $premiumMultiple_B = 0.02;
        //     $premiumMultiple_C = 0.24;
        // } elseif ($emp_age >= 40 && $emp_age <= 44) {
        //     $premiumMultiple_A = 0.03;
        //     $premiumMultiple_B = 0.03;
        //     $premiumMultiple_C = 0.37;
        // } elseif ($emp_age >= 45 && $emp_age <= 49) {
        //     $premiumMultiple_A = 0.06;
        //     $premiumMultiple_B = 0.06;
        //     $premiumMultiple_C = 0.53;
        // } elseif ($emp_age >= 50 && $emp_age <= 54) {
        //     $premiumMultiple_A = 0.10;
        //     $premiumMultiple_B = 0.10;
        //     $premiumMultiple_C = 0.83;
        // } elseif ($emp_age >= 55 && $emp_age <= 59) {
        //     $premiumMultiple_A = 0.18;
        //     $premiumMultiple_B = 0.18;
        //     $premiumMultiple_C = 1.33;
        // } elseif ($emp_age >= 60 && $emp_age <= 64) {
        //     $premiumMultiple_A = 0.60;
        //     $premiumMultiple_B = 0.40;
        //     $premiumMultiple_C = 2.43;
        // } elseif ($emp_age >= 65 && $emp_age <= 69) {
        //     $premiumMultiple_A = 0.60;
        //     $premiumMultiple_B = 0.48;
        //     $premiumMultiple_C = 2.83;
        // } elseif ($emp_age >= 70 && $emp_age <= 74) {
        //     $premiumMultiple_A = 0.60;
        //     $premiumMultiple_B = 0.86;
        //     $premiumMultiple_C = 3.83;
        // } elseif ($emp_age >= 75 && $emp_age <= 79) {
        //     $premiumMultiple_A = 0.60;
        //     $premiumMultiple_B = 1.80;
        //     $premiumMultiple_C = 5.76;
        // } elseif ($emp_age >= 80) {
        //     $premiumMultiple_A = 0.60;
        //     $premiumMultiple_B = 2.88;
        //     $premiumMultiple_C = 7.80;
        // }
        // $data = [
        //     'premiumMultiple_basic' => 0.160,
        //     'premiumMultiple_A' => $premiumMultiple_A,
        //     'premiumMultiple_B' => $premiumMultiple_B,
        //     'premiumMultiple_C' => $premiumMultiple_C,
        // ];
        // return $data;
    }

    public function getPremiumCostMultiplierFEGLIBiWeekly_old($emp_age = null) // biWeekly
    {
        if ($emp_age == null) {
            return false;
        }

        if ($emp_age < 35) {
            $premiumMultiple_A = 0.02;
            $premiumMultiple_B = 0.02;
            $premiumMultiple_C = 0.22;
        } elseif ($emp_age >= 35 && $emp_age <= 39) {
            $premiumMultiple_A = 0.03;
            $premiumMultiple_B = 0.03;
            $premiumMultiple_C = 0.27;
        } elseif ($emp_age >= 40 && $emp_age <= 44) {
            $premiumMultiple_A = 0.04;
            $premiumMultiple_B = 0.04;
            $premiumMultiple_C = 0.41;
        } elseif ($emp_age >= 45 && $emp_age <= 49) {
            $premiumMultiple_A = 0.07;
            $premiumMultiple_B = 0.07;
            $premiumMultiple_C = 0.59;
        } elseif ($emp_age >= 50 && $emp_age <= 54) {
            $premiumMultiple_A = 0.11;
            $premiumMultiple_B = 0.11;
            $premiumMultiple_C = 0.92;
        } elseif ($emp_age >= 55 && $emp_age <= 59) {
            $premiumMultiple_A = 0.20;
            $premiumMultiple_B = 0.20;
            $premiumMultiple_C = 1.48;
        } elseif ($emp_age >= 60 && $emp_age <= 64) {
            $premiumMultiple_A = 0.60;
            $premiumMultiple_B = 0.44;
            $premiumMultiple_C = 2.70;
        } elseif ($emp_age >= 65 && $emp_age <= 69) {
            $premiumMultiple_A = 0.60;
            $premiumMultiple_B = 0.54;
            $premiumMultiple_C = 3.14;
        } elseif ($emp_age >= 70 && $emp_age <= 74) {
            $premiumMultiple_A = 0.60;
            $premiumMultiple_B = 0.96;
            $premiumMultiple_C = 3.83;
        } elseif ($emp_age >= 75 && $emp_age <= 79) {
            $premiumMultiple_A = 0.60;
            $premiumMultiple_B = 1.80;
            $premiumMultiple_C = 5.26;
        } elseif ($emp_age >= 80) {
            $premiumMultiple_A = 0.60;
            $premiumMultiple_B = 2.64;
            $premiumMultiple_C = 7.20;
        }
        $data = [
            'premiumMultiple_basic' => 0.155,
            'premiumMultiple_A' => $premiumMultiple_A,
            'premiumMultiple_B' => $premiumMultiple_B,
            'premiumMultiple_C' => $premiumMultiple_C,
        ];
        return $data;
    }

    public function basicCoverageMultiplier($emp_age = null)
    {
        if ($emp_age == null) {
            return redirect()->back()->with('error', 'Please select an Employee');
        }
        if ($emp_age <= 35) {
            $basicMultiplier = 2.0;
        } elseif ($emp_age == 36) {
            $basicMultiplier = 1.9;
        } elseif ($emp_age == 37) {
            $basicMultiplier = 1.8;
        } elseif ($emp_age == 38) {
            $basicMultiplier = 1.7;
        } elseif ($emp_age == 39) {
            $basicMultiplier = 1.6;
        } elseif ($emp_age == 40) {
            $basicMultiplier = 1.5;
        } elseif ($emp_age == 41) {
            $basicMultiplier = 1.4;
        } elseif ($emp_age == 42) {
            $basicMultiplier = 1.3;
        } elseif ($emp_age == 43) {
            $basicMultiplier = 1.2;
        } elseif ($emp_age == 44) {
            $basicMultiplier = 1.1;
        } else {
            $basicMultiplier = 1;
        }
        return $basicMultiplier;
    }

    public function getSummaryFEGLI()
    {
        return [
            'A0' =>    'Ineligible',
            '90' =>    'Basic + Option B (3x)',
            'B0' =>    'Waived',
            'P0' =>    'Basic + Option A + Option B (3x)',
            'C0' =>    'Basic only',
            'Q1' =>    'Basic + Option B (3x) + Option C (1x)',
            'D0' => 'Basic + Option A',
            'Q2' =>    'Basic + Option B (3x) + Option C (2x)',
            'E1' =>    'Basic + Option C (1x)',
            'Q3' =>    'Basic + Option B (3x) + Option C (3x)',
            'E2' =>    'Basic + Option C (2x)',
            'Q4' =>    'Basic + Option B (3x) + Option C (4x)',
            'E3' =>    'Basic + Option C (3x)',
            'Q5' =>    'Basic + Option B (3x) + Option C (5x)',
            'E4' =>    'Basic + Option C (4x)',
            'R1' =>    'Basic + Option A + Option B (3x) + Option C (1x)',
            'E5' =>    'Basic + Option C (5x)',
            'R2' =>    'Basic + Option A + Option B (3x) + Option C (2x)',
            'F1' =>    'Basic + Option A + Option C (1x)',
            'R3' =>    'Basic + Option A + Option B (3x) + Option C (3x)',
            'F2' =>    'Basic + Option A + Option C (2x)',
            'R4' =>    'Basic + Option A + Option B (3x) + Option C (4x)',
            'F3' =>    'Basic + Option A + Option C (3x)',
            'R5' =>    'Basic + Option A + Option B (3x) + Option C (5x)',
            'F4' =>    'Basic + Option A + Option C (4x)',
            'S0' =>    'Basic + Option B (4x)',
            'F5' =>    'Basic + Option A + Option C (5x)',
            'T0' =>    'Basic + Option A + Option B (4x)',
            'G0' =>    'Basic + Option B (1x)',
            'U1' =>    'Basic + Option B (4x) + Option C (1x)',
            'H0' =>    'Basic + Option B (1x) + Option A',
            'U2' =>    'Basic + Option B (4x) + Option C (2x)',
            'I1' =>    'Basic + Option B (1x) + Option C (1x)',
            'U3' =>    'Basic + Option B (4x) + Option C (3x)',
            'I2' =>    'Basic + Option B (1x) + Option C (2x)',
            'U4' =>    'Basic + Option B (4x) + Option C (4x)',
            'I3' =>    'Basic + Option B (1x) + Option C (3x)',
            'U5' =>    'Basic + Option B (4x) + Option C (5x)',
            'I4' =>    'Basic + Option B (1x) + Option C (4x)',
            'V1' =>    'Basic + Option A + Option B (4x) + Option C (1x)',
            'I5' =>    'Basic + Option B (1x) + Option C (5x)',
            'V2' =>    'Basic + Option A + Option B (4x) + Option C (2x)',
            'J1' =>    'Basic + Option A + Option B (1x) + Option C (1x)',
            'V3' =>    'Basic + Option A + Option B (4x) + Option C (3x)',
            'J2' =>    'Basic + Option A + Option B (1x) + Option C (2x)',
            'V4' =>    'Basic + Option A + Option B (4x) + Option C (4x)',
            'J3' =>    'Basic + Option A + Option B (1x) + Option C (3x)',
            'V5' =>    'Basic + Option A + Option B (4x) + Option C (5x)',
            'J4' =>    'Basic + Option A + Option B (1x) + Option C (4x)',
            'W0' =>    'Basic + Option B (5x)',
            'J5' =>    'Basic + Option A + Option B (1x) + Option C (5x)',
            'X0' =>    'Basic + Option B (5x) + Option A',
            'K0' =>    'Basic + Option B (2x)',
            'Y1' =>    'Basic + Option B (5x) + Option C (1x)',
            'L0' =>    'Basic + Option A + Option B (2x)',
            'Y2' =>    'Basic + Option B (5x) + Option C (2x)',
            'M1' =>    'Basic + Option B (2x) + Option C (1x)',
            'Y3' =>    'Basic + Option B (5x) + Option C (3x)',
            'M2' =>    'Basic + Option B (2x) + Option C (2x)',
            'Y4' =>    'Basic + Option B (5x) + Option C (4x)',
            'M3' =>    'Basic + Option B (2x) + Option C (3x)',
            'Y5' =>    'Basic + Option B (5x) + Option C (5x)',
            'M4' =>    'Basic + Option B (2x) + Option C (4x)',
            'Z1' =>    'Basic + Option A + Option B (5x) + Option C (1x)',
            'M5' =>    'Basic + Option B (2x) + Option C (5x)',
            'Z2' =>    'Basic + Option A + Option B (5x) + Option C (2x)',
            'N1' =>    'Basic + Option A + Option B (2x) + Option C (1x)',
            'Z3' =>    'Basic + Option A + Option B (5x) + Option C (3x)',
            'N2' =>    'Basic + Option A + Option B (2x) + Option C (2x)',
            'Z4' =>    'Basic + Option A + Option B (5x) + Option C (4x)',
            'N3' =>    'Basic + Option A + Option B (2x) + Option C (3x)',
            'Z5' =>    'Basic + Option A + Option B (5x) + Option C (5x)',
            'N4' =>    'Basic + Option A + Option B (2x) + Option C (4x)',
            'N5' =>    'Basic + Option A + Option B (2x) + Option C (5x)',
        ];
    }

    public function calcSalForPremium($current_sal = null)
    {
        if ($current_sal != null) {
            $sal_remainder = round($current_sal) % 1000;
            if ($sal_remainder != 0) {
                $req_for_thousand = 1000 - $sal_remainder;
                $salForPremium = $current_sal + $req_for_thousand;
            } else {
                $salForPremium = $current_sal;
            }
        } else {
            $salForPremium = $current_sal;
        }

        return round($salForPremium);
    }

    public function calcBasicCoverageFegli($includeBasic = null, $salForPremium = null, $basicPremiumMultiplier = null)
    {
        $basicCoverage = 0;
        if (($includeBasic != null) && ($salForPremium != null)) {
            if ($includeBasic == 1) {
                $basicCoverage = $salForPremium + 2000;
                $basicCoverage = $basicCoverage * $basicPremiumMultiplier;
            }
        }
        return $basicCoverage;
    }

    public function calcOptionCCoverageFegli($inc = 0, $mStatus = 17, $multiple = 1, $dependents = [])
    {
        $spouse_coverage = 0;
        $childrenCoverage = 0;
        $OptionCCoverage = 0;

        if ($inc == 1) {
            if ($mStatus == 16) {
                $spouse_coverage = 5000 * $multiple;
            }
            if (!empty($dependents)) {
                $valid_children = [];
                foreach ($dependents as $child_row) {
                    // if(isset($child['age']) && ($child['age'] > 0)) {
                    //     $child_age = $child['age'];
                    // }   else {
                    //     $child_age = date('Y') - date('Y', strtotime($child['DateOfBirth']));
                    // }
                    if ($child_row['child_age'] < 22 || $child_row['CoverAfter22'] == 1) {
                        array_push($valid_children, $child_row['child_age']);
                    }
                }
                $childCoverage = 2500 * $multiple;
                // echo "<pre>"; print_r($valid_children);
                $childrenCoverage = count($valid_children) * $childCoverage;
            }
            $OptionCCoverage = $childrenCoverage + $spouse_coverage;
        }
        return $OptionCCoverage;
    }

    public function monthlyCostForBasicPremium($inc = 0, $salForPremium = null, $age = 0)
    { // No reduction
        // $multiplier = resolve('employee')->getSystemConfigurations($conf);
        $monthlyCostForBasicPremium = 0.00;
        if (($salForPremium != null) && ($inc == 1)) {
            $salForBasic = $salForPremium + 2000;
            $valueForBasic = ($salForBasic / 1000);
            $monthlyCostForBasicPremium = $valueForBasic * 0.3467;
        }
        return $monthlyCostForBasicPremium;
    }

    public function monthlyCostForBasicPremium_old($inc = 0, $salForPremium = null, $age = 0)
    { // No reduction
        $monthlyCostForBasicPremium = 0.00;
        if (($salForPremium != null) && ($inc == 1)) {
            $salForBasic = $salForPremium + 2000;
            $valueForBasic = ($salForBasic / 1000);
            $monthlyCostForBasicPremium = $valueForBasic * 0.325;
        }
        return $monthlyCostForBasicPremium;
    }

    public function monthlyCostForOptionB($inc = 0, $coverage = 0, $bMultiplier = 0)
    {
        $monthlyCostForOptionB =  0.00;
        $valueForOptionB = ($coverage / 1000);
        if ($inc == 1) {
            $monthlyCostForOptionB =  $valueForOptionB * $bMultiplier;
        }
        // echo $monthlyCostForOptionB; exit;
        return $monthlyCostForOptionB;
    }

    public function monthlyCostForOptionC($inc = 0, $premiumMultiplier = 1, $cMultiple)
    {
        $monthlyCostForOptionC = 0.00;
        if ($cMultiple < 1) {
            $cMultiple = 1;
        }
        if ($inc == 1) {
            $monthlyCostForOptionC = $cMultiple * $premiumMultiplier;
        }

        return $monthlyCostForOptionC;
    }

    public function getPremiumCostMultiplierFEGLIAfterRet($emp_age)
    {
        if ($emp_age == null) {
            return false;
        }
        if ($emp_age <= 65) {
            $basicPremiumMultiplier = 2.455;
        } else {
            $basicPremiumMultiplier = 2.130;
        }
    }

    public function monthlyCostForBasicPremiumZeroReduction($emp_age, $coverage)
    {
        if ($emp_age < 65) {
            $multiplier = config('constants.basicMultiplierZeroReduction.ageLessThan65');
            $costPerMonth = $multiplier * ($coverage / 1000);
        } else {
            $multiplier = config('constants.basicMultiplierZeroReduction.age65orMore');
            $costPerMonth = $multiplier * ($coverage / 1000);
        }
        return $costPerMonth;
    }

    public function monthlyCostForBasicPremiumZeroReduction_old($emp_age, $coverage)
    {
        if ($emp_age < 65) {
            $multiplier = config('constants.basicMultiplierZeroReduction.old.ageLessThan65');
            $costPerMonth = $multiplier * ($coverage / 1000);
        } else {
            $multiplier = config('constants.basicMultiplierZeroReduction.old.age65orMore');
            $costPerMonth = $multiplier * ($coverage / 1000);
        }
        return $costPerMonth;
    }

    public function monthlyCostForBasicPremium50Reduction($emp_age, $coverage)
    {
        if ($emp_age < 65) {
            $multiplier = config('constants.basicMultiplier50Reduction.ageLessThan65');
            $costPerMonth = $multiplier * ($coverage / 1000);
        } else {
            $multiplier = config('constants.basicMultiplier50Reduction.age65orMore');
            $costPerMonth = $multiplier * ($coverage / 1000);
        }
        return $costPerMonth;
    }

    public function monthlyCostForBasicPremium50Reduction_old($emp_age, $coverage)
    {
        if ($emp_age < 65) {
            $multiplier = config('constants.basicMultiplier50Reduction.old.ageLessThan65');
            $costPerMonth = $multiplier * ($coverage / 1000);
        } else {
            $multiplier = config('constants.basicMultiplier50Reduction.old.age65orMore');
            $costPerMonth = $multiplier * ($coverage / 1000);
        }
        return $costPerMonth;
    }

    public function monthlyCostForBasicPremium75Reduction($emp_age, $coverage)
    {
        if ($emp_age < 65) {
            $multiplier = config('constants.basicMultiplier75Reduction.ageLessThan65');
            $costPerMonth = $multiplier * ($coverage / 1000);
        } else {
            $multiplier = config('constants.basicMultiplier75Reduction.age65orMore');
            $costPerMonth = $multiplier * ($coverage / 1000);
        }
        return $costPerMonth;
    }

    public function monthlyCostForBasicPremium75Reduction_old($emp_age, $coverage)
    {
        if ($emp_age < 65) {
            $multiplier = config('constants.basicMultiplier75Reduction.old.ageLessThan65');
            $costPerMonth = $multiplier * ($coverage / 1000);
        } else {
            $multiplier = config('constants.basicMultiplier75Reduction.old.age65orMore');
            $costPerMonth = $multiplier * ($coverage / 1000);
        }
        return $costPerMonth;
    }

    public function monthlyCostForOptionAPremium75Reduction($multiplier, $maxCoverage)
    {
        return ($maxCoverage / 1000) * $multiplier;
    }

    public function monthlyCostForOptionBPremiumFullReduction($multiplier, $maxCoverage)
    {
        return ($maxCoverage / 1000) * $multiplier;
    }

    public function biweeklyCostForOptionBPremiumFullReduction($emp_age, $maxCoverage)
    {
        if ($emp_age < 35) {
            $multiplier = 0.02;
        } elseif (($emp_age >= 35) && ($emp_age <= 39)) {
            $multiplier = 0.03;
        } elseif (($emp_age >= 40) && ($emp_age <= 44)) {
            $multiplier = 0.04;
        } elseif (($emp_age >= 45) && ($emp_age <= 49)) {
            $multiplier = 0.07;
        } elseif (($emp_age >= 50) && ($emp_age <= 54)) {
            $multiplier = 0.11;
        } elseif (($emp_age >= 55) && ($emp_age <= 59)) {
            $multiplier = 0.20;
        } elseif (($emp_age >= 60) && ($emp_age <= 64)) {
            $multiplier = 0.44;
        } elseif (($emp_age >= 65) && ($emp_age <= 69)) {
            $multiplier = 0.54;
        } elseif (($emp_age >= 70) && ($emp_age <= 74)) {
            $multiplier = 0.96;
        } elseif (($emp_age >= 75) && ($emp_age <= 79)) {
            $multiplier = 1.80;
        } else { // 80 and above
            $multiplier = 2.64;
        }
        return ($maxCoverage / 1000) * $multiplier;
    }

    public function monthlyCostForOptionBNoReduction($emp_age, $maxCoverage)
    {
        // Monthly multipliers
        // if ($emp_age < 35) {
        //     $multiplier = 0.043;
        // } elseif (($emp_age >= 35) && ($emp_age <= 39)) {
        //     $multiplier = 0.065;
        // } elseif (($emp_age >= 40) && ($emp_age <= 44)) {
        //     $multiplier = 0.087;
        // } elseif (($emp_age >= 45) && ($emp_age <= 49)) {
        //     $multiplier = 0.152;
        // } elseif (($emp_age >= 50) && ($emp_age <= 54)) {
        //     $multiplier = 0.238;
        // } elseif (($emp_age >= 55) && ($emp_age <= 59)) {
        //     $multiplier = 0.433;
        // } elseif (($emp_age >= 60) && ($emp_age <= 64)) {
        //     $multiplier = 0.953;
        // } elseif (($emp_age >= 65) && ($emp_age <= 69)) {
        //     $multiplier = 1.170;
        // } elseif (($emp_age >= 70) && ($emp_age <= 74)) {
        //     $multiplier = 2.080;
        // } elseif (($emp_age >= 75) && ($emp_age <= 79)) {
        //     $multiplier = 3.900;
        // } else { // if ($emp_age >= 80) {
        //     $multiplier = 5.720;
        // }

        // echo $multiplier;
        // die;
        $conf_name = $this->getConfiguarationNameInRetirement($emp_age);
        $premiumMultiple_B = resolve('employee')->getSystemConfigurations($conf_name['optionB']['no_reduction']);
        return ($maxCoverage / 1000) * $premiumMultiple_B;
    }

    public function biweeklyCostForOptionBNoReduction($multiplier, $maxCoverage)
    {
        return ($maxCoverage / 1000) * $multiplier;
    }

    public function monthlyCostForOptionCFullReduction($multiplier, $coverageMultiple)
    {
        return $coverageMultiple * $multiplier;
    }

    public function monthlyCostForOptionCNoReduction($multiplier, $coverageMultiple)
    { // need to merge these functions, will do in free time ** Same calculation in all optionC functions **
        return $coverageMultiple * $multiplier;
    }

    public function GetMonthDiffRoundToZero($from_date = null, $to_date = null)
    {
        return ($this->GetYearDiff($from_date, $to_date) * 12) + $this->GetOnlyMonthDiffRoundToZero($from_date, $to_date);
    }

    public function GetYearDiff($from_date = null, $to_date = null)
    {
        $date1 = new \DateTime($from_date);
        $date2 = new \DateTime($to_date);
        $diff = $date1->diff($date2);
        $years = $diff->y;
        $months = $diff->m;

        return $years;
    }

    public function GetOnlyMonthDiffRoundToZero($from_date = null, $to_date = null)
    {
        $days = 0;
        $date1 = new \DateTime($from_date);
        $date2 = new \DateTime($to_date);

        $diff = $date1->diff($date2);
        $months = $diff->m;
        $days = $diff->d;
        $year_start = date('Y', strtotime($from_date));
        $year_end = date('Y', strtotime($to_date));

        for ($i = $year_start; $i <= $year_end; $i++) {
            if ($i % 4 == 0) {
                $days++;
            }
        }
        $months = $months + round($days / 30);

        return $months;
    }

    public function hoursToMonths($hours = 0)
    {
        return ($hours / 2087) * 12;
    }

    public function calcBasicCoverage50Reduction($includeBasic = 0, $maxBasicCoverage, $basicCoverage50Reduction_pre)
    {
        $coverage = 0;
        if ($includeBasic == 1) {
            $percent50OfCoverage = $maxBasicCoverage * (50 / 100);
            $reduction_amount = ($maxBasicCoverage / 100) * 12; // 1%

            if ($basicCoverage50Reduction_pre > $percent50OfCoverage) {
                $coverage = $basicCoverage50Reduction_pre - $reduction_amount;
            } else {
                $coverage = $percent50OfCoverage;
            }

            if ($coverage < $percent50OfCoverage) {
                $coverage = $percent50OfCoverage;
            }
        }
        // echo $coverage; die;
        return $coverage;
    }

    public function calcBasicCoverage75Reduction($includeBasic = 0, $maxBasicCoverage, $basicCoverage75Reduction_pre)
    {
        $coverage = 0;
        if ($includeBasic == 1) {
            $percent75OfCoverage = $maxBasicCoverage * (25 / 100); // 75% reduction and 25$ remaining
            $reduction_amount = ($maxBasicCoverage * (2 / 100)) * 12;
            if ($basicCoverage75Reduction_pre >= $percent75OfCoverage) {
                $coverage = $basicCoverage75Reduction_pre - $reduction_amount;
            } else {
                $coverage = $percent75OfCoverage;
            }

            if ($coverage <= $percent75OfCoverage) {
                $coverage = $percent75OfCoverage;
            }
        }
        return $coverage;
    }

    public function monthlyCostForBasicPremium_conf($inc = 0, $salForPremium = null, $age = 0) // new function, as now configurations are saved in DB
    { // No reduction
        $monthlyCostForBasicPremium = 0.00;
        if (($salForPremium != null) && ($inc == 1)) {
            $conf_name = $this->getConfiguarationNameWhileWorking($age);
            $multiplier = resolve('employee')->getSystemConfigurations($conf_name['basic']);
            $salForBasic = $salForPremium + 2000;
            $valueForBasic = ($salForBasic / 1000);
            $monthlyCostForBasicPremium = $valueForBasic * $multiplier; // 0.3467;
        }
        return $monthlyCostForBasicPremium;
    }

    /* Functions For FEGLI configuartions */
    public function getConfiguarationNameWhileWorking($age) // new function, as now configurations are saved in DB
    {
        if ($age < 35) {
            $basic = 'WhileWorkingBasicCostPer1000AgeLessThan35';
            $optionA = 'WhileWorkingOptionACostPer1000AgeLessThan35';
            $optionB = 'WhileWorkingOptionBCostPer1000AgeLessThan35';
            $optionC = 'WhileWorkingOptionCCostPer1000AgeLessThan35';
        } elseif ($age >= 35 && $age <= 39) {
            $basic = 'WhileWorkingBasicCostPer1000Age35To39';
            $optionA = 'WhileWorkingOptionACostPer1000Age35To39';
            $optionB = 'WhileWorkingOptionBCostPer1000Age35To39';
            $optionC = 'WhileWorkingOptionCCostPer1000Age35To39';
        } elseif ($age >= 40 && $age <= 44) {
            $basic = 'WhileWorkingBasicCostPer1000Age40To44';
            $optionA = 'WhileWorkingOptionACostPer1000Age40To44';
            $optionB = 'WhileWorkingOptionBCostPer1000Age40To44';
            $optionC = 'WhileWorkingOptionCCostPer1000Age40To44';
        } elseif ($age >= 45 && $age <= 49) {
            $basic = 'WhileWorkingBasicCostPer1000Age45To49';
            $optionA = 'WhileWorkingOptionACostPer1000Age45To49';
            $optionB = 'WhileWorkingOptionBCostPer1000Age45To49';
            $optionC = 'WhileWorkingOptionCCostPer1000Age45To49';
        } elseif ($age >= 50 && $age <= 54) {
            $basic = 'WhileWorkingBasicCostPer1000Age50To54';
            $optionA = 'WhileWorkingOptionACostPer1000Age50To54';
            $optionB = 'WhileWorkingOptionBCostPer1000Age50To54';
            $optionC = 'WhileWorkingOptionCCostPer1000Age50To54';
        } elseif ($age >= 55 && $age <= 59) {
            $basic = 'WhileWorkingBasicCostPer1000Age55To59';
            $optionA = 'WhileWorkingOptionACostPer1000Age55To59';
            $optionB = 'WhileWorkingOptionBCostPer1000Age55To59';
            $optionC = 'WhileWorkingOptionCCostPer1000Age55To59';
        } elseif ($age >= 60 && $age <= 64) {
            $basic = 'WhileWorkingBasicCostPer1000Age60To64';
            $optionA = 'WhileWorkingOptionACostPer1000Age60To64';
            $optionB = 'WhileWorkingOptionBCostPer1000Age60To64';
            $optionC = 'WhileWorkingOptionCCostPer1000Age60To64';
        } elseif ($age >= 65 && $age <= 69) {
            $basic = 'WhileWorkingBasicCostPer1000Age65To69';
            $optionA = 'WhileWorkingOptionACostPer1000Age65To69';
            $optionB = 'WhileWorkingOptionBCostPer1000Age65To69';
            $optionC = 'WhileWorkingOptionCCostPer1000Age65To69';
        } elseif ($age >= 70 && $age <= 74) {
            $basic = 'WhileWorkingBasicCostPer1000Age70To74';
            $optionA = 'WhileWorkingOptionACostPer1000Age70To74';
            $optionB = 'WhileWorkingOptionBCostPer1000Age70To74';
            $optionC = 'WhileWorkingOptionCCostPer1000Age70To74';
        } elseif ($age >= 75 && $age <= 79) {
            $basic = 'WhileWorkingBasicCostPer1000Age75To79';
            $optionA = 'WhileWorkingOptionACostPer1000Age75To79';
            $optionB = 'WhileWorkingOptionBCostPer1000Age75To79';
            $optionC = 'WhileWorkingOptionCCostPer1000Age75To79';
        } else {
            $basic = 'WhileWorkingBasicCostPer1000Age80orGreater';
            $optionA = 'WhileWorkingOptionACostPer1000Age80orGreater';
            $optionB = 'WhileWorkingOptionBCostPer1000Age80orGreater';
            $optionC = 'WhileWorkingOptionCCostPer1000Age80orGreater';
        }

        return [
            'basic' => $basic,
            'optionA' => $optionA,
            'optionB' => $optionB,
            'optionC' => $optionC
        ];
    }

    public function getConfiguarationNameInRetirement($age) // new function, as now configurations are saved in DB
    {
        if ($age >= 50 && $age <= 54) {
            $basic['no_reduction'] = 'InRetirementBasicCostPer1000Age50To54NoReduction';
            $basic['reduction_50'] = 'InRetirementBasicCostPer1000Age50To54Reduction50';
            $basic['reduction_75'] = 'InRetirementBasicCostPer1000Age50To54Reduction75';
            $optionA['reduction_75'] = 'InRetirementOptionACostPer1000Age50To54Reduction75';
            $optionB['full_reduction'] = 'InRetirementOptionBCostPer1000Age50To54FullReduction';
            $optionB['no_reduction'] = 'InRetirementOptionBCostPer1000Age50To54NoReduction';
            $optionC['full_reduction'] = 'InRetirementOptionCCostPer1000Age50To54FullReduction';
            $optionC['no_reduction'] = 'InRetirementOptionCCostPer1000Age50To54NoReduction';
        } elseif ($age >= 55 && $age <= 59) {
            $basic['no_reduction'] = 'InRetirementBasicCostPer1000Age55To59NoReduction';
            $basic['reduction_50'] = 'InRetirementBasicCostPer1000Age55To59Reduction50';
            $basic['reduction_75'] = 'InRetirementBasicCostPer1000Age55To59Reduction75';
            $optionA['reduction_75'] = 'InRetirementOptionACostPer1000Age55To59Reduction75';
            $optionB['full_reduction'] = 'InRetirementOptionBCostPer1000Age55To59FullReduction';
            $optionB['no_reduction'] = 'InRetirementOptionBCostPer1000Age55To59NoReduction';
            $optionC['full_reduction'] = 'InRetirementOptionCCostPer1000Age55To59FullReduction';
            $optionC['no_reduction'] = 'InRetirementOptionCCostPer1000Age55To59NoReduction';
        } elseif ($age >= 60 && $age <= 64) {
            $basic['no_reduction'] = 'InRetirementBasicCostPer1000Age60To64NoReduction';
            $basic['reduction_50'] = 'InRetirementBasicCostPer1000Age60To64Reduction50';
            $basic['reduction_75'] = 'InRetirementBasicCostPer1000Age60To64Reduction75';
            $optionA['reduction_75'] = 'InRetirementOptionACostPer1000Age60To64Reduction75';
            $optionB['full_reduction'] = 'InRetirementOptionBCostPer1000Age60To64FullReduction';
            $optionB['no_reduction'] = 'InRetirementOptionBCostPer1000Age60To64NoReduction';
            $optionC['full_reduction'] = 'InRetirementOptionCCostPer1000Age60To64FullReduction';
            $optionC['no_reduction'] = 'InRetirementOptionCCostPer1000Age60To64NoReduction';
        } elseif ($age >= 65 && $age <= 69) {
            $basic['no_reduction'] = 'InRetirementBasicCostPer1000Age65To69NoReduction';
            $basic['reduction_50'] = 'InRetirementBasicCostPer1000Age65To69Reduction50';
            $basic['reduction_75'] = 'InRetirementBasicCostPer1000Age65To69Reduction75';
            $optionA['reduction_75'] = 'InRetirementOptionACostPer1000Age65To69Reduction75';
            $optionB['full_reduction'] = 'InRetirementOptionBCostPer1000Age65To69FullReduction';
            $optionB['no_reduction'] = 'InRetirementOptionBCostPer1000Age65To69NoReduction';
            $optionC['full_reduction'] = 'InRetirementOptionCCostPer1000Age65To69FullReduction';
            $optionC['no_reduction'] = 'InRetirementOptionCCostPer1000Age65To69NoReduction';
        } elseif ($age >= 70 && $age <= 74) {
            $basic['no_reduction'] = 'InRetirementBasicCostPer1000Age70To74NoReduction';
            $basic['reduction_50'] = 'InRetirementBasicCostPer1000Age70To74Reduction50';
            $basic['reduction_75'] = 'InRetirementBasicCostPer1000Age70To74Reduction75';
            $optionA['reduction_75'] = 'InRetirementOptionACostPer1000Age70To74Reduction75';
            $optionB['full_reduction'] = 'InRetirementOptionBCostPer1000Age70To74FullReduction';
            $optionB['no_reduction'] = 'InRetirementOptionBCostPer1000Age70To74NoReduction';
            $optionC['full_reduction'] = 'InRetirementOptionCCostPer1000Age70To74FullReduction';
            $optionC['no_reduction'] = 'InRetirementOptionCCostPer1000Age70To74NoReduction';
        } elseif ($age >= 75 && $age <= 79) {
            $basic['no_reduction'] = 'InRetirementBasicCostPer1000Age75To79NoReduction';
            $basic['reduction_50'] = 'InRetirementBasicCostPer1000Age75To79Reduction50';
            $basic['reduction_75'] = 'InRetirementBasicCostPer1000Age75To79Reduction75';
            $optionA['reduction_75'] = 'InRetirementOptionACostPer1000Age75To79Reduction75';
            $optionB['full_reduction'] = 'InRetirementOptionBCostPer1000Age75To79FullReduction';
            $optionB['no_reduction'] = 'InRetirementOptionBCostPer1000Age75To79NoReduction';
            $optionC['full_reduction'] = 'InRetirementOptionCCostPer1000Age75To79FullReduction';
            $optionC['no_reduction'] = 'InRetirementOptionCCostPer1000Age75To79NoReduction';
        } else {
            $basic['no_reduction'] = 'InRetirementBasicCostPer1000Age80NoReduction';
            $basic['reduction_50'] = 'InRetirementBasicCostPer1000Age80Reduction50';
            $basic['reduction_75'] = 'InRetirementBasicCostPer1000Age80Reduction75';
            $optionA['reduction_75'] = 'InRetirementOptionACostPer1000Age80Reduction75';
            $optionB['full_reduction'] = 'InRetirementOptionBCostPer1000Age80FullReduction';
            $optionB['no_reduction'] = 'InRetirementOptionBCostPer1000Age80NoReduction';
            $optionC['full_reduction'] = 'InRetirementOptionCCostPer1000Age80FullReduction';
            $optionC['no_reduction'] = 'InRetirementOptionCCostPer1000Age80NoReduction';
        }

        return [
            'basic' => $basic,
            'optionA' => $optionA,
            'optionB' => $optionB,
            'optionC' => $optionC
        ];
    }

    public function monthlyCostForBasicPremiumZeroReduction_conf($emp_age, $coverage) // new function, as now configurations are saved in DB
    {
        $conf_name = $this->getConfiguarationNameInRetirement($emp_age);
        $multiplier = resolve('employee')->getSystemConfigurations($conf_name['basic']['no_reduction']);
        return $multiplier * ($coverage / 1000);
    }

    public function monthlyCostForBasicPremium50Reduction_conf($emp_age, $coverage)
    {
        $conf_name = $this->getConfiguarationNameInRetirement($emp_age);
        $multiplier = resolve('employee')->getSystemConfigurations($conf_name['basic']['reduction_50']);
        return $multiplier * ($coverage / 1000);
    }

    public function monthlyCostForBasicPremium75Reduction_conf($emp_age, $coverage)
    {
        $conf_name = $this->getConfiguarationNameInRetirement($emp_age);
        $multiplier = resolve('employee')->getSystemConfigurations($conf_name['basic']['reduction_75']);
        return $multiplier * ($coverage / 1000);
    }
}
