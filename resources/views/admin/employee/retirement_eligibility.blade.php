@extends('admin.layouts.admin_layout')
@section('style')
<style>
    #alert_ret_calc {
        display: inline;
        float: right;
        color: green;
    }
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
                            <p class="text-left">General Information</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="warnings_wrap">

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="basic_info_form_wrap">
                            <form action="{{ url('employee/retirementEligibility/update') }}/{{ $empId }}" method="POST" id="retirement_eligibility_form">
                                @csrf
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="birthdate">Birthdate: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <input type="date" name="birthdate" value="{{isset($data['DateOfBirth']) ? date('Y-m-d', strtotime($data['DateOfBirth'])) : ""}}">
                                            @if($minimumRetirementAge['success'] == 1)
                                            <span id="mra_wrap">
                                                <span class="mra_emp"> {{ $minimumRetirementAge['mra_str'] }} </span> </span>
                                            </span>
                                            @endif
                                            <label class="error-help-block">{{ $errors->first('birthdate') }}</label>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="leave_scd">Leave SCD: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <input type="date" name="leave_scd" value="{{isset($data['LeaveSCD']) ? date('Y-m-d', strtotime($data['LeaveSCD'])) : ""}}">
                                            <label class="error-help-block">{{ $errors->first('leave_scd') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="retirement_date">Retirement Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <input type="date" name="retirement_date" value="{{isset($data['RetirementDate']) ? date('Y-m-d', strtotime($data['RetirementDate'])) : ""}}" id="min_ret_date_input">
                                            <label class="error-help-block">{{ $errors->first('retirement_date') }}</label>
                                            <br>
                                            <input type="button" value="Calculate Fully-Eligible" class="btn btn-sm btns-configurations calcFullR">
                                            <input type="button" value="Calculate MRA+10" class="btn btn-sm btns-configurations calcEarliestR">
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="configuration_title">
                                        <input type="hidden" name="next" value="0">
                                        <input type="submit" value="Save" class="btn btn-sm btns-configurations">
                                        <a class="btn btn-sm btns-configurations">Save & Next</a>
                                        <button class="btn btn-sm btns-configurations">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xs-12 lower_gap_20">
                        <div class="retirement_eligibility_blocks">
                            <div class="info_heading child_listing_title">
                                <p class="text-left">Active Duty Military Service</p>
                                <p class="text-right"><a href="{{url('employee/militaryService/add/active')}}/{{$empId}}">Add Active Duty Military Service</a></p>
                            </div>
                            <table class="table table-bordered">
                                @if(!empty($activeMilitaryService))
                                <thead class="case_list_head">
                                    <tr>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Deposit Made</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                @endif
                                <tbody>
                                    @forelse($activeMilitaryService as $service)
                                    <tr>
                                        <td><a href="{{url('employee/militaryService/edit')}}/{{$service['EmployeeId']}}/{{$service['MilitaryServiceId']}}">{{ date('m/d/Y', strtotime($service['FromDate'])) }}</a></td>
                                        <td>{{ date('m/d/Y', strtotime($service['ToDate'])) }}</td>
                                        <td>{{ ($service['DepositOwed'] == 1) ? 'true' : 'false' }}</td>
                                        <td>${{ round($service['AmountOwed'], 2) }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" href="{{ url('employee/militaryService/edit') }}/{{ $service['EmployeeId'] }}/{{$service['MilitaryServiceId']}}">Edit</a>
                                            <a onclick="javascript:confirmationDelete($(this));return false;" class="btn btn-danger btn-sm" href="{{ route('deleteMilitaryService', $service['MilitaryServiceId']) }}">Delete</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td class="no_data_found">No results...</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-xs-12 lower_gap_20">
                        <div class="retirement_eligibility_blocks">
                            <div class="info_heading child_listing_title">
                                <p class="text-left">Non Deduction Service</p>
                                <p class="text-right"><a href="{{url('/employee/nonDeductionService/add')}}/{{$empId}}">Add New Non Deduction Service</a></p>
                            </div>
                            <table class="table table-bordered">
                                @if(!empty($nonDeductionServices))
                                <thead class="case_list_head">
                                    <tr>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Amount Owed</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                @endif
                                <tbody>
                                    @forelse($nonDeductionServices as $service)
                                    <tr>
                                        <td><a href="{{url('/employee/nonDeductionService/edit')}}/{{ $service['EmployeeId'] }}/{{ $service['NonDeductionServiceId'] }}">{{ date('m/d/Y', strtotime($service['FromDate'])) }}</a></td>
                                        <td>{{ date('m/d/Y', strtotime($service['ToDate'])) }}</td>
                                        <td>${{ round($service['AmountOwed'], 2) }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" href="{{url('/employee/nonDeductionService/edit')}}/{{ $service['EmployeeId'] }}/{{ $service['NonDeductionServiceId'] }}">Edit</a>
                                            <a onclick="javascript:confirmationDelete($(this));return false;" class="btn btn-danger btn-sm" href="{{ route('deleteNonDeductionService', $service['NonDeductionServiceId']) }}">Delete</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td class="no_data_found" colspan="5">No results...</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-xs-12 lower_gap_20">
                        <div class="retirement_eligibility_blocks">
                            <div class="info_heading child_listing_title">
                                <p class="text-left">Refunded Service</p>
                                <p class="text-right"><a href="{{ url('/employee/refundedService/add') }}/{{$empId}}">Add New Refunded Service</a></p>
                            </div>
                            <table class="table table-bordered">
                                @if(!empty($refundedServices))
                                <thead class="case_list_head">
                                    <tr>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Amount Owed</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                @endif
                                <tbody>
                                    @forelse($refundedServices as $service)
                                    <tr>
                                        <td><a href="{{ url('employee/refundedService/edit') }}/{{$service['EmployeeId']}}/{{ $service['RefundedServiceId'] }}">{{ date('m/d/Y', strtotime($service['FromDate'])) }}</a></td>
                                        <td>{{ date('m/d/Y', strtotime($service['ToDate'])) }}</td>
                                        <td>${{ round($service['AmountOwed'], 2) }}</td>
                                        <td>
                                            <a class="btn btn-primary btn-sm" href="{{ url('employee/refundedService/edit') }}/{{$service['EmployeeId']}}/{{ $service['RefundedServiceId'] }}">Edit</a>
                                            <a onclick="javascript:confirmationDelete($(this));return false;" class="btn btn-danger btn-sm" href="{{ route('deleteRefundedService', $service['RefundedServiceId']) }}">Delete</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td class="no_data_found" colspan="5">No results...</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-xs-12 lower_gap_20">
                        <div class="summary_retirement_eligibility">
                            @foreach($summary_arr as $label => $val)
                            <div class="row in_gap">
                                <div class="form-group">
                                    <div class="col-xs-3">
                                        <div class="label_div">
                                            <label for="birthdate">{{$label}}: </label>
                                        </div>
                                    </div>
                                    <div class="col-xs-9">
                                        <span>{{$val}}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<!-- <script src="{{-- URL::asset('vendor/jsvalidation/js/jsvalidation.js') --}}"></script> -->
{!! JsValidator::formRequest('App\Http\Requests\RetirementEligibilityRequest', '#retirement_eligibility_form'); !!}

<script>
    $(document).on('click', '.calcEarliestR', function(e) {
        let dob = $("input[name=birthdate]").val();
        let next_url = "";
        if (dob == "") {
            $('.warnings_wrap').text("Please add birthdate.");
            return;
        } else {
            next_url = `/${dob}`;
        }
        e.preventDefault();
        var emp_id = '{{$empId}}';
        $.ajax({
            url: "{{ url('employee/retirement/earliest')}}" + '/' + emp_id + next_url,
            type: "GET",
            dataType: 'json',
            success: function(res) {
                if (res.success == true) {
                    var min_ret_date = res.minRetirementDate;
                    $('#min_ret_date_input').val(min_ret_date);
                    $("p#alert_ret_calc").remove();
                    $('<p id="alert_ret_calc">!Earliest retirement date calculated</p>').insertAfter('#min_ret_date_input').delay(1000).fadeOut();

                } else {
                    $('.warnings_wrap').text(res.penalty_message);
                }

            }
        })
    });

    $(document).on('click', '.calcFullR', function(e) {
        let dob = $("input[name=birthdate]").val();
        let leaveSCD = $("input[name=leave_scd]").val();
        let next_url = "";
        if (dob == "" || leaveSCD == "") {
            $('.warnings_wrap').text("Please add birthdate and Leave SCD.");
            return;
        } else {
            next_url = `/${dob}/${leaveSCD}`;
        }
        e.preventDefault();
        var emp_id = '{{$empId}}';
        $.ajax({
            url: "{{ url('employee/retirement/full')}}" + '/' + emp_id + next_url,
            type: "GET",
            dataType: 'json',
            success: function(res) {
                if (res.success == true) {
                    var min_ret_date = res.fullRetirementDate;
                    $('#min_ret_date_input').val(min_ret_date);
                    $("p#alert_ret_calc").remove();
                    $('<p id="alert_ret_calc">!Full retirement date calculated</p>').insertAfter('#min_ret_date_input').delay(1000).fadeOut();

                } else {
                    $('.warnings_wrap').text(res.penalty_message);
                }

            }
        })
    });

    function confirmationDelete(anchor) {
        swal({
                title: "Are you sure want to delete this Service?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    window.location = anchor.attr("href");
                }
            });
        //   var conf = confirm("Are you sure want to delete this User?");
        //   if (conf) window.location = anchor.attr("href");
    }
</script>
@endsection
