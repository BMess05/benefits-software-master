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
                        @include('admin.layouts.messages')
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="info_heading">
                            <p class="text-left">Configuration</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <form action="{{url('employee/configurations/save')}}/{{$empId}}" method="POST" id="employeeConfForm">
                        @csrf
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="configuration_title">
                                        CSRS and FERS
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form_section_wrap">
                                        <div class="form-group">
                                            <label for="csrs_cola">CSRS COLA: </label>
                                            <input type="text" name="csrs_cola" value="{{!empty($emp_conf) ? $emp_conf['CSRSCola'] : ''}}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <label for="fers_cola">FERS COLA: </label>
                                            <input type="text" name="fers_cola" value="{{!empty($emp_conf) ? $emp_conf['FERSCola'] : ''}}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <label for="default_salary_increase">DEFAULT SALARY INCREASE: </label>
                                            <input type="text" name="sal_increase" value="{{!empty($emp_conf) ? $emp_conf['SalaryIncreaseDefault'] : ''}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4"></div>
                            </div>

                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="configuration_title">
                                        Social Security
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form_section_wrap">
                                        <div class="form-group">
                                            <label for="social_security_cola">SOCIAL SECURITY COLA: </label>
                                            <input type="text" name="ss_cola" value="{{!empty($emp_conf) ? $emp_conf['SSCola'] : ''}}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <label for="pia_formula_fb">PIA Formula First Bend: </label>
                                            <input type="text" name="pia_formula_fb" value="{{!empty($emp_conf) ? $emp_conf['PIAFormula'] : ''}}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <label for="rt_ei_limit">Retiree Earned Income Limit: </label>
                                            <input type="text" name="income_limit" value="{{!empty($emp_conf) ? $emp_conf['SSEarnedIncomeLimit'] : ''}}" class="config_input">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="configuration_title">
                                        Fedral Employee Health Benefits
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form_section_wrap">
                                        <div class="form-group">
                                            <label for="avg_prem_increase">Avg. Premimum Increase: </label>
                                            <input type="text" name="avg_prem_increase" value="{{!empty($emp_conf) ? $emp_conf['FEHBAveragePremiumIncrease'] : ''}}" class="config_input">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4"></div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="configuration_title">
                                        Thrift Saving Plan
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form_section_wrap">
                                        <div class="form-group">
                                            <label for="ed_limit">Elective Deferral Limit (annual): </label>
                                            <input type="text" name="deff_limit" value="{{!empty($tspConf) ? $tspConf['deff_limit'] : ''}}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <label for="catchup_limit">Catch-up Limit (annual): </label>
                                            <input type="text" name="catchup_limit" value="{{!empty($tspConf) ? $tspConf['catchup_limit'] : ''}}" class="config_input">
                                        </div>
                                        <div class="form-group rate_of_return_title">
                                            <label for="">Rates of Return </label>
                                        </div>
                                        <div class="form-group">
                                            <label for="gfund">G Fund </label>
                                            <input type="text" name="gfund" value="{{!empty($tspConf) ? $tspConf['gfund'] : ''}}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <label for="ffund">F Fund </label>
                                            <input type="text" name="ffund" value="{{!empty($tspConf) ? $tspConf['ffund'] : ''}}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <label for="cfund">C Fund </label>
                                            <input type="text" name="cfund" value="{{!empty($tspConf) ? $tspConf['cfund'] : ''}}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <label for="sfund">S Fund </label>
                                            <input type="text" name="sfund" value="{{!empty($tspConf) ? $tspConf['sfund'] : ''}}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <label for="ifund">I Fund </label>
                                            <input type="text" name="ifund" value="{{!empty($tspConf) ? $tspConf['ifund'] : ''}}" class="config_input">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4"></div>
                                <div class="col-md-4"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="configuration_title">
                                        <input type="submit" value="Save" class="btn btn-sm btns-configurations">
                                        <button class="btn btn-sm btns-configurations">Reset to Current</button>
                                        <button class="btn btn-sm btns-configurations">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\SystemConfigRequest', '#employeeConfForm'); !!}
@endsection
