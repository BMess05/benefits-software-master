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
                            <p class="text-left">Edit Refunded Service</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="basic_info_form_wrap">
                            <form action="{{url('employee/refundedService/update')}}/{{ $refundedService['RefundedServiceId'] }}" method="POST" id="updateRefundedServiceForm">
                                @csrf
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <div class="label_div">
                                                <label for="from_date">From Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="date" name="from_date" value="{{ date('Y-m-d', strtotime($refundedService['FromDate'])) }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <div class="label_div">
                                                <label for="to_date">To Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="date" name="to_date" value="{{ date('Y-m-d', strtotime($refundedService['ToDate'])) }}">
                                        </div>
                                    </div>
                                </div>
                                {{--
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <div class="label_div"><label>Withdrawal</label></div>
                                        </div>
                                        <div class="col-md-8">
                                            <!-- <label for="withdrawal"><input type="checkbox" name="withdrawal" {{ ($refundedService['Withdrawal']) ? 'checked' : '' }}> Withdrawal</label> -->
                                <div for="yes"><input type="radio" name="withdrawal" value="1" {{ ($refundedService['Withdrawal'] == 1) ? 'checked' : '' }}> Yes </div>
                                <div for="no"><input type="radio" name="withdrawal" value="0" {{ ($refundedService['Withdrawal'] == 0) ? 'checked' : '' }}> No </div>
                        </div>
                    </div>
                </div>
                <div class="row in_gap">
                    <div class="form-group">
                        <div class="col-md-4">
                            <div class="label_div">
                                <label for=""> Redeposit Made: </label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <!-- <label for="redeposit"><input type="checkbox" name="redeposit" {{ ($refundedService['Redeposit']) ? 'checked' : '' }}> Redeposit</label> -->
                            <div for="yes"><input type="radio" name="redeposit" value="1" {{ ($refundedService['Redeposit'] == 1) ? 'checked' : '' }}> Yes </div>
                            <div for="no"><input type="radio" name="redeposit" value="0" {{ ($refundedService['Redeposit'] == 0) ? 'checked' : '' }}> No </div>
                        </div>
                    </div>
                </div>
                --}}
                <div class="row in_gap">
                    <div class="form-group">
                        <div class="col-md-4">
                            <div class="label_div">
                                <label for="amount_owned">Amount Owed: </label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="amount_owned" value="{{round($refundedService['AmountOwed'], 2)}}">
                            <input type="hidden" name="employeeId" value="{{$refundedService['EmployeeId']}}">
                        </div>
                    </div>
                </div>
                <div class="row in_gap">
                    <div class="configuration_title">
                        <input type="submit" value="save" class="btn btn-sm btns-configurations">
                        <button type="button" class="btn btn-sm btns-configurations">Cancel</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
</section>
@endsection
@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\RefundedServiceRequest', '#updateRefundedServiceForm'); !!}
@endsection
