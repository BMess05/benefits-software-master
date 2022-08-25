@extends('admin.layouts.admin_layout')
@section('style')
<style>

</style>
@endsection
@section('content')
@include('admin.layouts.admin_top_menu')
<section class="cases_listing_wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="listing-title">
                    <p class="text-left">Add Advisor</p>
                </div>
                <div class="add_emp_wrap">
                    <div class="row">
                        @include('admin.layouts.messages')
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="row in_gap">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <div class="label_div">
                                            <label>Name:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <label>{{ $advisor->AdvisorName ?? "" }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row in_gap">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <div class="label_div">
                                            <label>Company Name:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <label>{{ $advisor->company_name ?? "" }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row in_gap">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <div class="label_div">
                                            <label>Workshop Code:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <label>{{ $advisor->workshop_code ?? "" }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row in_gap">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <div class="label_div">
                                            <label>Address:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <label>{!! $advisor->AdvisorAddress ?? "" !!}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="row in_gap">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <div class="label_div">
                                            <label>Default Disclaimer:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <label>{{ $advisor->disclaimer->DisclaimerName ?? "" }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row in_gap">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <div class="label_div">
                                            <label>Suppress Confidential:</label>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <label>{{($advisor->SuppressConfidential == 1) ? 'Yes' : 'No'}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="configuration_title">
                                        <a class="btn btn-sm btns-configurations" href="{{url('/advisor/edit')}}/{{$advisor->AdvisorId}}">Edit</a>
                                        @if(Auth::user()->role == 0)
                                        <a class="btn btn-sm btns-configurations" href="{{url('/advisor/delete')}}/{{$advisor->AdvisorId}}">Delete</a>
                                        @endif
                                        <button class="btn btn-sm btns-configurations">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
