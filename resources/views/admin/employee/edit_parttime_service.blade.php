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
                        <div class="info_heading child_listing_title">
                            <p class="text-left">Part Time Service</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <form action="{{url('employee/parttime_service/update')}}/{{$service['PartTimeServiceId']}}" method="POST" id="addPArtTimeServiceForm">
                            @csrf
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="from_date">From Date:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="from_date" value="{{ ($service['FromDate'] != '') ? date('Y-m-d', strtotime($service['FromDate'])) : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="to_date">To Date:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="to_date" value="{{ ($service['ToDate'] != '') ? date('Y-m-d', strtotime($service['ToDate'])) : '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="hours_weekly">Hours/Week:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="hours_weekly" value="{{ $service['HoursWeek'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="percentage">Percentage:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="number" name="percentage" step="0.1" value="{{$service['percentage']}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="configuration_title">
                                            <input type="submit" value="Save" class="btn btn-sm btns-configurations">
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
    </div>
</section>
@endsection

@section('scripts') 
{!! JsValidator::formRequest('App\Http\Requests\ParttimeServiceRequest', '#addPArtTimeServiceForm'); !!}
@endsection
