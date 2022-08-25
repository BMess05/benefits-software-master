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
                    <form action="{{ url('employee/healthBenefits/update') }}/{{ $empId }}" method="POST" id="employeeHealthBenifitsForm">
                        @csrf
                        <div class="col-md-12">
                            <div class="info_heading">
                                <p class="text-left">Health Benefits</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="health_premium">Health Premium: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="health_premium" value="{{ number_format($data['HealthPremium'], 2) }}" class=""> (pay period)

                                            <input type="checkbox" name="does_not_meet_five_year" {{($data['DoesNotMeetFiveYear'] == 1)? 'checked' : ''}}>
                                            <label for="does_not_meet_five_year">(Does Not Meet Five Year) </label>

                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="dental_premium">Dental Premium: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="dental_premium" value="{{ number_format($data['DentalPremium'], 2) }}"> (pay period)
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="vision_premium">Vision Premium: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="vision_premium" value="{{ number_format($data['VisionPremium'], 2) }}"> (Pay period)
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="does_not_meet_five_year">Dental and Vision Premium: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="dental_and_vision" value="{{ isset($data['dental_and_vision']) ? number_format($data['dental_and_vision'], 2) : 0.00 }}"> (Pay period)
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
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\HealthBenifitsRequest', '#employeeHealthBenifitsForm'); !!}
@endsection
