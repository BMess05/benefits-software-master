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
            <div class="col-sm-12">
                @include('admin.layouts.sections.nameTitle')
            </div>
        </div>
        <div class="row employee_details_wrap">
            <div class="col-sm-2">
                <div class="employee_detail_sidebar">
                    @include('admin.layouts.emp_details_menu')
                </div>
            </div>
            <div class="col-sm-10 employee_detail_block_wrap">
                <div class="row">
                    <div class="col-sm-12">
                        @include('admin.layouts.messages')
                    </div>
                </div>
                <div class="row">
                    <form action="{{url('employee/tsp/update')}}/{{$empId}}" method="POST" id="tspForm">
                        @csrf
                        <div class="col-sm-12">
                            <div class="info_heading">
                                <p class="text-left">Current Information</p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-sm-3 col-xs-3">
                                            <div class="label_div">
                                                <label for="current_salary">Current Salary: </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9 col-xs-9">
                                            <input type="text" name="current_salary" value="{{ round($current_sal, 2) }}" class=""> (yearly)
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-sm-3 col-xs-3">
                                            <div class="label_div">
                                                <label for="annual_increase">Annual Increase: </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9 col-xs-9">
                                            <input type="text" name="annual_increase" value="{{$emp_conf['SalaryIncreaseDefault']}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-sm-3 col-xs-3">
                                            <div class="label_div">
                                                <label for="statement_date">Statement Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9 col-xs-9">
                                            <input type="date" name="statement_date" value="{{(isset($tsp['StatementDate']) ? date('Y-m-d', strtotime($tsp['StatementDate'])) : "")}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="info_heading">
                                <p class="text-left">Contributions & Loan Repayment</p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap">
                                    <div class="col-sm-4">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="contribution_loan_head text-center">
                                                    <label class="text-uppercase">New Contributions</label>
                                                    <input type="hidden" name="age_this_year" value="{{$age_by_end_of_year}}">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <div class="label_div">
                                                    <label for="regular_tsp_contribution">Traditional: </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                $ <input style="width: 115px;" type="text" name="regular_tsp_contribution" value="{{ isset($tsp['ContributionRegular']) ? round($tsp['ContributionRegular'], 2) : 0 }}" class=""> /pp
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <div class="label_div">
                                                    <label for="tsp_contribution_catchup">Roth: </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                $ <input style="width: 115px;" type="text" name="tsp_contribution_catchup" value="{{ isset($tsp['ContributionCatchUp']) ? round($tsp['ContributionCatchUp'], 2) : 0 }}"> /pp
                                                @if($errors->has('tsp_contribution_catchup'))
                                                <div class="error">{{ $errors->first('tsp_contribution_catchup') }}</div>
                                                @endif
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <div class="label_div">
                                                    <label for="tsp_contribution_catchup">Total: </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">$ {{ $tsp['total_contribution_amount'] }} /pp</div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="contribution_loan_head text-center">
                                                    <label class="text-uppercase">LOAN (GENERAL)</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <div class="label_div">
                                                    <label for="loan_balance_general">Loan Balance: </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                $ <input style="width: 115px;" type="text" name="loan_balance_general" value="{{ isset($tsp['loan_balance_general']) ? round($tsp['loan_balance_general'], 2) : 0.00}}"> <!-- (Pay period) -->
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <div class="label_div">
                                                    <label for="loan_repayment_general">Loan Repayment: </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                $ <input style="width: 115px;" type="text" name="loan_repayment_general" value="{{ isset($tsp['loan_repayment_general']) ? round($tsp['loan_repayment_general'], 2) : 0.00}}"> /pp
                                                <!-- (Pay period) -->
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <div class="label_div">
                                                    <label for="payoff_date_general">Payoff Date: </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <input style="width: 155px;" type="date" name="payoff_date_general" value="{{isset($tsp['payoff_date_general']) && ($tsp['payoff_date_general'] != "") ? date('Y-m-d', strtotime($tsp['payoff_date_general'])) : ''}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="contribution_loan_head text-center">
                                                    <label class="text-uppercase">LOAN (RESIDENTIAL)</label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <div class="label_div">
                                                    <label for="loan_balance_residential">Loan Balance: </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                $ <input style="width: 115px;" type="text" name="loan_balance_residential" value="{{ isset($tsp['loan_balance_residential']) ? round($tsp['loan_balance_residential'], 2) : 0.00}}"> <!-- (Pay period) -->
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <div class="label_div">
                                                    <label for="loan_repayment_residential">Loan Repayment: </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                $ <input style="width: 120px;" type="text" name="loan_repayment_residential" value="{{ isset($tsp['loan_repayment_residential']) ? round($tsp['loan_repayment_residential'], 2) : 0.00}}"> /pp
                                                <!-- (Pay period) -->
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <div class="label_div">
                                                    <label for="payoff_date_residential">Payoff Date: </label>
                                                </div>
                                            </div>
                                            <div class="col-sm-6 col-xs-6 in_gap">
                                                <input style="width: 155px;" type="date" name="payoff_date_residential" value="{{ isset($tsp['payoff_date_residential']) ? date('Y-m-d', strtotime($tsp['payoff_date_residential'])) : ''}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12 col-xs-12">
                            <div class="info_heading">
                                <p class="text-left">Current Balances, New Allocations & New Balance</p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="gfund">GFund: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="gfund" value="{{ isset($tsp['GFund']) ? round($tsp['GFund'], 2) : 0 }}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="gfund"><input type="text" name="gfund_distri" value="{{isset($tsp['GFundDist']) ? round($tsp['GFundDist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span></label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['GFund']) ? number_format(round($tsp['new_balance']['GFund'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="ffund">FFund: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="ffund" value="{{ isset($tsp['FFund']) ? round($tsp['FFund'], 2) : 0 }}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ffund"><input type="text" name="ffund_distri" value="{{isset($tsp['FFundDist']) ? round($tsp['FFundDist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['FFund']) ? number_format(round($tsp['new_balance']['FFund'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="cfund">CFund: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="cfund" value="{{isset($tsp['CFund']) ? round($tsp['CFund'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="cfund"><input type="text" name="cfund_distri" value="{{isset($tsp['CFundDist']) ? round($tsp['CFundDist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['CFund']) ? number_format(round($tsp['new_balance']['CFund'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="sfund">SFund: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="sfund" value="{{isset($tsp['SFund']) ? round($tsp['SFund'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="sfund"><input type="text" name="sfund_distri" value="{{isset($tsp['SFundDist']) ? round($tsp['SFundDist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['SFund']) ? number_format(round($tsp['new_balance']['SFund'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="ifund">IFund: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="ifund" value="{{isset($tsp['IFund']) ? round($tsp['IFund'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><input type="text" name="ifund_distri" value="{{isset($tsp['IFundDist']) ? round($tsp['IFundDist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['IFund']) ? number_format(round($tsp['new_balance']['IFund'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="lincome">LIncome: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="lincome" value="{{isset($tsp['LIncome']) ? round($tsp['LIncome'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="lincome"><input type="text" name="lincome_distri" value="{{isset($tsp['LIncomeDist']) ? round($tsp['LIncomeDist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['LIncome']) ? number_format(round($tsp['new_balance']['LIncome'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="l2025">L2025: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="l2025" value="{{isset($tsp['L2025']) ? round($tsp['L2025'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="l2025"><label for="l2025"><input type="text" name="l2025_distri" value="{{isset($tsp['L2025Dist']) ? round($tsp['L2025Dist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['L2025']) ? number_format(round($tsp['new_balance']['L2025'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="l2030">L2030: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="l2030" value="{{isset($tsp['L2030']) ? round($tsp['L2030'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="l2030"><input type="text" name="l2030_distri" value="{{isset($tsp['L2030Dist']) ? round($tsp['L2030Dist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['L2030']) ? number_format(round($tsp['new_balance']['L2030'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="l2035">L2035: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="l2035" value="{{isset($tsp['L2035']) ? round($tsp['L2035'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="l2035"><input type="text" name="l2035_distri" value="{{isset($tsp['L2035Dist']) ? round($tsp['L2035Dist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['L2035']) ? number_format(round($tsp['new_balance']['L2035'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="l2040">L2040: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="l2040" value="{{isset($tsp['L2040']) ? round($tsp['L2040'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="l2040"><input type="text" name="l2040_distri" value="{{isset($tsp['L2040Dist']) ? round($tsp['L2040Dist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['L2040']) ? number_format(round($tsp['new_balance']['L2040'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="l2045">L2045: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="l2045" value="{{isset($tsp['L2045']) ? round($tsp['L2045'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="l2045"><label for="l2045"><input type="text" name="l2045_distri" value="{{isset($tsp['L2045Dist']) ? round($tsp['L2045Dist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['L2045']) ? number_format(round($tsp['new_balance']['L2045'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>


                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="l2050">L2050: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="l2050" value="{{isset($tsp['L2050']) ? round($tsp['L2050'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="l2050"><input type="text" name="l2050_distri" value="{{isset($tsp['L2050Dist']) ? round($tsp['L2050Dist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['L2050']) ? number_format(round($tsp['new_balance']['L2050'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="l2055">L2055: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="l2055" value="{{isset($tsp['L2055']) ? round($tsp['L2055'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="l2055"><input type="text" name="l2055_distri" value="{{isset($tsp['L2055Dist']) ? round($tsp['L2055Dist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['L2055']) ? number_format(round($tsp['new_balance']['L2055'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="l2060">L2060: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="l2060" value="{{isset($tsp['L2060']) ? round($tsp['L2060'], 2) : 0}}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="l2060"><label for="l2060"><input type="text" name="l2060_distri" value="{{isset($tsp['L2060Dist']) ? round($tsp['L2060Dist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['L2060']) ? number_format(round($tsp['new_balance']['L2060'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label for="l2065">L2065: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <input type="text" name="l2065" value="{{ isset($tsp['L2065']) ? round($tsp['L2065'], 2) : 0 }}" class="">
                                    </div>
                                    <div class="col-xs-3 visible-xs label_div">%</div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="l2065"><label for="l2065"><input type="text" name="l2065_distri" value="{{isset($tsp['L2065Dist']) ? round($tsp['L2065Dist'], 2) : 0.00}}" class=""> <span class="hidden-xs">%</span> </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['L2065']) ? number_format(round($tsp['new_balance']['L2065'], 2), 2) : "0" }}</label> </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="col-sm-3 col-xs-3">
                                        <div class="label_div">
                                            <label>Current Total: </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-9">
                                        <label>${{ isset($tsp['GFund']) ? number_format($tsp['GFund'] + $tsp['FFund'] + $tsp['CFund'] + $tsp['SFund'] + $tsp['IFund'] + $tsp['LIncome'] + $tsp['L2025'] + $tsp['L2030'] + $tsp['L2035'] + $tsp['L2040'] + $tsp['L2045'] + $tsp['L2050'] + $tsp['L2055'] + $tsp['L2060'] + $tsp['L2065'], 2) : 0.00 }}</label>
                                    </div>
                                    <div class="col-sm-3 col-xs-9">
                                        <div class="grid_label">
                                            <label for="ifund"><span class="hidden-xs">$</span>{{ isset($tsp['new_balance']['new_balance']) ? number_format(round($tsp['new_balance']['new_balance'], 2), 2) : "" }}</label> </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="info_heading">
                                <p class="text-left">Estimated Returns</p>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-sm-2 col-xs-3">
                                            <div class="grid_label">
                                                <label>GFund: </label>
                                            </div>
                                            <label>{{round($tsp_conf['gfund'], 2)}}%</label>
                                        </div>
                                        <div class="col-sm-2 col-xs-3">
                                            <div class="grid_label">
                                                <label for="ffund_distri">FFund: </label>
                                            </div>
                                            <label>{{round($tsp_conf['ffund'], 2)}}%</label>
                                        </div>
                                        <div class="col-sm-2 col-xs-3">
                                            <div class="grid_label">
                                                <label for="cfund_distri">CFund: </label>
                                            </div>
                                            <label>{{round($tsp_conf['cfund'], 2)}}%</label>
                                        </div>
                                        <div class="col-sm-3 col-xs-3">
                                            <div class="grid_label">
                                                <label for="sfund_distri">SFund: </label>
                                            </div>
                                            <label>{{round($tsp_conf['sfund'], 2)}}%</label>
                                        </div>
                                        <div class="col-sm-3 col-xs-12">
                                            <div class="grid_label">
                                                <label for="ifund_distri">IFund: </label>
                                            </div>
                                            <label>{{round($tsp_conf['ifund'], 2)}}%</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="row in_gap">
                                <div class="configuration_title">
                                    <input type="hidden" name="next" value="0">
                                    <input type="submit" value="Save" class="btn btn-sm btns-configurations">
                                    <a class="btn btn-sm btns-configurations">Save & Next</a>
                                    <button class="btn btn-sm btns-configurations">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="important_link">
                    <a target="_blank" href="https://www.tsp.gov/PlanningTools/Calculators/retirementCalculator.html">https://www.tsp.gov/PlanningTools/Calculators/retirementCalculator.html</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<!-- <script src="{{-- URL::asset('vendor/jsvalidation/js/jsvalidation.js') --}}"></script> -->

{!! JsValidator::formRequest('App\Http\Requests\TspRequest', '#tspForm'); !!}

@endsection
