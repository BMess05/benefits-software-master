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
                    <p class="text-left">Add Case</p>
                </div>
                <div class="add_emp_wrap">
                    <div class="row">
                        <form action="{{url('/employee/save')}}" method="POST" id="addEmployeeForm">
                            @csrf
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Name:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="name">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Advisor:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <select name="advisor" class="advisor_dropdown">
                                                <option value="">-Select Advisor-</option>
                                                @forelse($advisors as $advisor)
                                                <option value="{{$advisor->AdvisorId}}">{{$advisor->workshop_code . " - " . $advisor->AdvisorName}}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Date Received:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="date_received" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Due Date:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="date" name="due_date" value="">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="configuration_title">
                                            <input type="submit" value="Save" class="btn btn-sm btns-configurations">
                                            <a href="{{url()->previous()}}" class="btn btn-sm btns-configurations">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\EmployeeAddRequest', '#addEmployeeForm'); !!}
@endsection
