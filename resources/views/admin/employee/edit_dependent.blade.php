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
                            <p class="text-left">Add Dependent</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <form action="{{url('/employee/fegli/updateDependent')}}/{{$dependent['FEGLIDependentId']}}" method="POST" id="addDepartment">
                            @csrf
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="dob">Date of Birth:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="dob" value="{{ isset($dependent['DateOfBirth']) && $dependent['DateOfBirth'] != NULL ? date('Y-m-d', strtotime($dependent['DateOfBirth'])) : '' }}">
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
                                                <label for="dob">Dependent Age:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="number" name="age" value="{{ $dependent['age'] ?? '' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">

                                        </div>
                                        <div class="col-md-9">
                                            <input type="checkbox" name="cover_after_22" value="1" {{ isset($dependent['CoverAfter22']) && $dependent['CoverAfter22'] == 1 ? 'checked' : '' }}>
                                            <label for="cover_after_22">Cover After 22</label>
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
{!! JsValidator::formRequest('App\Http\Requests\AddDepartmentRequest', '#addDepartment'); !!}
@endsection
