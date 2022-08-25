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
                        <div class="listing-title">
                            <p class="text-left">Report Notes</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="report_type_search_wrap">
                            <div class="form-group">
                                <div class="label_div_repo">
                                    <label for="report_notes_type">TYPE</label>
                                </div>
                                <select class="report_notes_dropdown" name="report_notes_type" id="">
                                    @for($r=1; $r <= 15; $r++) <option value="{{$r}}">Annuity/Eligibility</option>
                                        @endfor
                                </select>
                                <input type="submit" value="Search" class="btn btn-sm">
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="table_wrap">
                            <table class="table table-bordered">
                                <thead class="case_list_head">
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($i = 8; $i <= 27; $i++) <tr>
                                        <td><input type="checkbox" name="report_notes[]"></td>
                                        <td>{{$i}}</td>
                                        <td>Client is first eligible to retire (with a full annuity) on 3-4-2016 at age 57+ with 30 years of service. For this report, I used the last day of the month following this date.</td>
                                        </tr>
                                        @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
