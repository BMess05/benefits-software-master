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
                    <form action="{{url('employee/payAndLeave/update')}}/{{$empId}}" method="POST" id="payAndLeaveForm">
                        @csrf
                        <div class="col-md-12">
                            <div class="info_heading">
                                <p class="text-left">High-3 Average</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="current_salary">Current Salary: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="current_salary" value="{{$data['current_sal']}}" class=""> (yearly)
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="annual_increase">Annual Increase: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="annual_increase" value="{{$data['annualIncrease']}}">
                                            <a class="btn btn-sm btn-default projectIncreases">Project Increases</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="projections_list">
                                    @if (isset($projectedHigh3Average['projectedIncreases']) && $projectedHigh3Average['projectedIncreases'] != "")
                                    @forelse($projectedHigh3Average['projectedIncreases'] as $year => $sal)
                                    <div class="row in_gap">
                                        <div class="form-group">
                                            <div class="col-xs-3">
                                                <div class="label_div">
                                                    <label>{{$year}}: </label>
                                                </div>
                                            </div>
                                            <div class="col-xs-9">
                                                <label>${{ number_format(round($sal, 2)) }} </label>
                                            </div>
                                        </div>
                                    </div>
                                    @empty
                                    @endforelse
                                    @endif
                                </div>

                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="">Projected High-3 Average: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <label>${{isset($projectedHigh3Average['projectedHigh3Avg']) ? number_format(round($projectedHigh3Average['projectedHigh3Avg'], 2)) : 0}}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="high3_avg">High-3 Average: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="high3_avg" value="{{$data['high3_avg']}}"> (Use this field only to override projection.)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-12">
                            <div class="info_heading">
                                <p class="text-left">Unused Leave</p>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="unusual_sick_leave">Unused Sick Leave: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">

                                            @if($data['RetirementType'] == "Deferred")
                                            <input type="text" name="unusual_sick_leave" value="{{$data['unused_sick_leave']}}" class="unused_sick_leave" disabled> (in hours)
                                            <input type="hidden" name="unusual_sick_leave" value="0">
                                            @else
                                            <input type="text" name="unusual_sick_leave" value="{{$data['unused_sick_leave']}}" class="unused_sick_leave"> (in hours)
                                            @endif
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="unusual_annual_leave">Unused Annual Leave: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="unusual_annual_leave" value="{{$data['unused_annual_leave']}}" class="unused_annual_leave">
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
                {{-- <div class="row children_listing_wrap">
                    <div class="col-xs-12">
                        <div class="info_heading child_listing_title">
                            <p class="text-left">Other Deductions</p>
                            <p class="text-right"><a href="{{url('employee/deduction/add')}}/{{$empId}}">Add New Deductions</a></p>
            </div>
        </div>
        <div class="col-xs-12">
            <table class="table table-bordered">
                @if(!empty($otherDeductions))
                <thead class="case_list_head">
                    <tr>
                        <th>Deduction</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                @endif
                <tbody>
                    @forelse($otherDeductions as $deduc)
                    <tr>
                        <td><a href="{{url('employee/deduction/edit')}}/{{$empId}}/{{$deduc['DeductionId']}}">{{ $deduc['DeductionName'] }}</a></td>
                        <td>{{ round($deduc['DeductionAmount'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td class="no_data_found" colspan="2">No results...</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div> --}}
    </div>
    </div>
    </div>
</section>
@endsection
@section('scripts')
<!-- <script src="{{-- URL::asset('vendor/jsvalidation/js/jsvalidation.js') --}}"></script> -->
{!! JsValidator::formRequest('App\Http\Requests\PayAndLeaveRequest', '#payAndLeaveForm'); !!}

<script>
/*     $(document).on('click', '.projectIncreases', function() {
        $('.projections_list').slideToggle();
    }); */
$(document).on('keyup', '.unused_sick_leave', function() {
    let val = parseInt($(this).val());
    let alert = '';
    if(val > 2000) {
        alert += `<div class="alert alert-warning sl-warning">
                <strong><i class="fa fa-exclamation-circle" aria-hidden="true"></i></strong> Please make sure there wasn't an error while adding pending sick leave hours.
            </div>`;
    }   else {
        $('.sl-warning').remove();
    }
    $(alert).insertAfter(this);
    
});

$(document).on('keyup', '.unused_annual_leave', function() {
    let al = parseInt($(this).val());
    let alert = '';
    if(al > 300) {
        alert += `<div class="alert alert-warning al-warning">
                <strong><i class="fa fa-exclamation-circle" aria-hidden="true"></i></strong> Please make sure there wasn't an error while adding pending annual leave hours.
            </div>`;
    }   else {
        $('.al-warning').remove();
    }
    $(alert).insertAfter(this);
});
</script>
@endsection
