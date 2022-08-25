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
                    <form action="{{url('employee/fltcip/update')}}/{{$empId}}" method="POST" id="fltcip_form">
                        @csrf
                        <div class="col-xs-12">
                            <div class="info_heading">
                                <p class="text-left">FLTCIP</p>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="fltcip_premium">FLTCIP Premium: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="fltcip_premium" value="{{ round($fltcip, 2) }}" class="">
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

                        <div class="col-xs-12">
                            <div class="important_link">
                                <a target="_blank" href="https://www.ltcfeds.com/ltcWeb/do/assessing_your_needs/ratecalcOut">https://www.ltcfeds.com/ltcWeb/do/assessing_your_needs/ratecalcOut</a>
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
{!! JsValidator::formRequest('App\Http\Requests\FltcipRequest', '#fltcip_form'); !!}
@endsection
