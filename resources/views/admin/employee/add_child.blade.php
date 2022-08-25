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
                        <div class="info_heading child_listing_title">
                            <p class="text-left">Add Child</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <form action="{{url('/employee/save_child')}}" method="POST" id="addChildForm">
                            @csrf
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="from_date">Name: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="name">
                                            <input type="hidden" name="empId" value="{{$empId}}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="dob">Age:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="age">
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
{!! JsValidator::formRequest('App\Http\Requests\ChildRequest', '#addChildForm'); !!}
@endsection
