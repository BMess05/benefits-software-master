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
                    <form action="{{url('/employee/social_security/update')}}/{{$empId}}" method="POST" id="basic_info_form">
                        @csrf
                        <div class="col-xs-12">
                            <div class="info_heading">
                                <p class="text-left">Social Security</p>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="monthly_social_security">
                                                    <!-- Monthly --> SS At 62:
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="monthly_social_security" value="{{ round($data['SSMonthlyAt62'], 2) }}" class="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="social_security_start_age">SS Start Age: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <div class="row">
                                                <div class="col-xs-2">
                                                    Year:
                                                    <input type="number" name="social_security_start_age_year" value="{{ $data['SSStartAge_year'] }}" style="width: 50px;">
                                                </div>
                                                <div class="col-xs-2">
                                                    Month:
                                                    <input type="number" name="social_security_start_age_month" value="{{ $data['SSStartAge_month'] }}" style="width: 50px;">
                                                </div>
                                                <div class="col-xs-10"></div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="monthly_social_security_at_start_age">
                                                    <!-- Monthly --> SS At Start Age:
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="monthly_social_security_at_start_age" value="{{round($data['SSMonthlyAtStartAge'], 2)}}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="social_security_at_age_of_retirement">SS At Retirement Age: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="social_security_at_age_of_retirement" value="{{($data['SSAtAgeOfRetirement'] > 0) ? round($data['SSAtAgeOfRetirement'], 2) : ''}}">
                                            <label class="label_div">(ONLY FOR CSRS OFFSET)</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="ss_substantial_earning_years">SS Substantial Earnings Years: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="ss_substantial_earning_years" value="{{round($data['SSYearsEarning'], 2)}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-xs-12">
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
                    <div class="col-xs-12">
                        <div class="important_link">
                            <p>Social Security Quick Calculator: <a target="_blank" href="https://www.ssa.gov/OACT/quickcalc/index.html" target="_blank">https://www.ssa.gov/OACT/quickcalc/index.html</a></p>
                            <p>Taking SS before FRA: <a target="_blank" href="https://www.ssa.gov/benefits/retirement/planner/agereduction.html" target="_blank">https://www.ssa.gov/benefits/retirement/planner/agereduction.html</a></p>
                            <p>Taking SS after FRA: <a target="_blank" href="https://www.ssa.gov/planners/retire/delayret.html" target="_blank">https://www.ssa.gov/planners/retire/delayret.html</a></p>
                            <p>WEP Fact Sheet for substantial earnings: <a target="_blank" href="https://www.ssa.gov/pubs/EN-05-10045.pdf" target="_blank">https://www.ssa.gov/pubs/EN-05-10045.pdf</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\SocialSecurityRequest', '#basic_info_form'); !!}
@endsection
