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
                            <p class="text-left">Add Refunded Service for which a redeposit has NOT been repaid</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="basic_info_form_wrap">
                            <form action="{{url('employee/refundedService/save')}}/{{$empId}}" method="POST" id="saveRefundedServiceForm">
                                @csrf
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <div class="label_div">
                                                <label for="from_date">From Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="date" name="from_date">
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
                                            <input type="date" name="to_date">
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <div class="label_div"></div>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="withdrawal"><input type="checkbox" name="withdrawal"> Withdrawal</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <div class="label_div">Redeposit Made: </div>
                                        </div>
                                        <div class="col-md-8">
                                            <label for="redeposit"><input type="checkbox" name="redeposit"> Redeposit Owed</label>
                                            <div for="yes"><input type="radio" name="redeposit" value="1"> Yes </div>
                                            <div for="no"><input type="radio" name="redeposit" value="0"> No </div>
                                        </div>
                                    </div>
                                </div>
                                -->
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-4">
                                            <div class="label_div">
                                                <label for="amount_owned">Amount Owed: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="amount_owned" value="0.00">
                                            <input type="hidden" name="employeeId" value="{{$empId}}">
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
{!! JsValidator::formRequest('App\Http\Requests\RefundedServiceRequest', '#saveRefundedServiceForm'); !!}
@endsection
