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
            @include('admin.layouts.messages')
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="listing-title">
                    <p class="text-left">Manage Cases</p>
                    <p class="text-right"><a href="{{url('employee/employee/add')}}">Add New Case</a></p>
                </div>
                <div class="case_search_wrap">
                    <div class="row">
                        <div class="col-md-7">
                            <div class="row">
                                <form action="{{url('cases/search')}}" method="post" id="caseSerachForm">
                                    @csrf
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="case_id">CASE#</label>
                                            <input type="text" name="case_id" class="caseIdS">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="employee">EMPLOYEE</label>
                                            <input type="text" name="emp_name" class="employeeNameS">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="advisor">ADVISOR</label>
                                            <input type="text" name="advisor_name" class="advisorNameS">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <input type="button" value="search" class="btn btn-default btn-sm btn_search_case">
                                            <a href="#" class="btn btn-default btn-sm btn_search_case_clear">Clear</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-md-5">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive table_wrap">
                    <?php
                    if (Auth::user()->role == 0) {
                        $column_width1 = "12";
                        $column_width2 = "7";
                        $col_span = 16;
                    } else {
                        $column_width1 = "15";
                        $column_width2 = "10";
                        $col_span = 10;
                    }
                    ?>
                    <table class="table table-bordered" id="<!-- sortTable -->">
                        <thead class="case_list_head">
                            <tr>
                                <th colspan="{{ $col_span }}">Rows {{ $cases->count() }} of {{ $cases->total() }}, Page {{ $cases->currentPage() }} | {{ $cases->links() }}</th>
                            </tr>
                            <tr>
                                <th style="width: 5%;">Case#</th>
                                <th style="width: 7%;">Employee</th>
                                <th style="width: {{ $column_width1 }}%;">Advisor</th>
                                <th style="width: {{ $column_width2 }}%;">Workshop Code</th>
                                <th style="width: {{ $column_width2 }}%;">Date Received</th>
                                <th style="width: {{ $column_width2 }}%;">Due Date</th>
                                <th style="width: {{ $column_width2 }}%;">Date Completed</th>
                                <th style="width: {{ $column_width2 }}%;">System</th>
                                <th style="width: {{ $column_width2 }}%;">Employee Type</th>
                                @if(Auth::user()->role == 0)
                                <th style="width: {{ $column_width2 }}%;">Created By</th>
                                <th style="width: {{ $column_width2 }}%;">Last Updated By</th>
                                <th style="width: {{ $column_width2 }}%;">Last Updated At</th>
                                @endif
                                <th style="width: 5%;">Edit</th>
                                @if(Auth::user()->role == 0)
                                <th style="width: 2.5%;">Delete</th>
                                <th style="width: 2.5%;">History</th>
                                @endif
                                <th style="width: 3%;">Duplicate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cases as $case)
                            <tr>
                                <td style="width: 5%;"><a href="{{url('/employee/basic_info')}}/{{$case->EmployeeId}}">{{$case->EmployeeId}}</a></td>
                                <td style="width: 7%;">{{$case->EmployeeName}}</td>
                                <td style="width: {{$column_width1}}">{{ $case->advisor->AdvisorName ?? "" }}</td>
                                <td style="width: {{$column_width2}}">{{ $case->advisor->workshop_code ?? "" }}</td>
                                <td style="width: {{$column_width2}}">{{date('m/d/Y', strtotime($case->DateReceived))}}</td>
                                <td style="width: {{$column_width2}}">{{date('m/d/Y', strtotime($case->DueDate))}}</td>
                                <td style="width: {{$column_width2}}">{{($case->DateCompleted == null) ? "" : date('m/d/Y', strtotime($case->DateCompleted))}}</td>
                                <td style="width: {{$column_width2}}">{{ ($case->SystemTypeId) ? (resolve('applookup')->getById($case->SystemTypeId)->AppLookupName) : ''}}</td>
                                <td style="width: {{$column_width2}}">{{ ($case->EmployeeTypeId) ? (resolve('applookup')->getById($case->EmployeeTypeId)->AppLookupName) : ''}}</td>
                                @if(Auth::user()->role == 0)
                                <td style="width: {{$column_width2}}">{{ $case->CreatedByName ?? "" }}</td>
                                <td style="width: {{$column_width2}}">{{ $case->UpdatedByName ?? "" }}</td>
                                <td style="width: {{$column_width2}}">{{ ($case->updated_at == null) ? "" : date('m/d/Y', strtotime($case->updated_at)) ?? "" }}</td>
                                @endif
                                <td style="width: 5%;">
                                    <a class="btn btn-primary btn-sm" href="{{ route('basicInformation', $case->EmployeeId) }}"><i class="fa fa-edit"></i></a>
                                </td>
                                @if(Auth::user()->role == 0)
                                <td style="width: 2.5%;">
                                    <a onclick="javascript:confirmationDelete($(this));return false;" class="btn btn-danger btn-sm" href="{{ route('deleteCase', $case->EmployeeId) }}"><i class="fa fa-trash"></i></a>
                                </td>
                                <td style="width: 2.5%;">
                                    <a class="btn btn-info btn-sm" href="{{ route('caseHistory', $case->EmployeeId) }}"><i class="fa fa-history" aria-hidden="true"></i>
                                    </a>
                                </td>
                                @endif
                                <td style="width: 3%;">
                                    <a onclick="javascript:confirmationDuplicate($(this));return false;" class="btn btn-info btn-sm" href="{{ route('makeDuplicate', $case->EmployeeId) }}"><i class="fa fa-copy"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{$col_span}}">No cases found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script>
    $('.caseIdS, .employeeNameS, .advisorNameS').keyup(function(e) {
        // let enter key press trigger search button
        if (e.keyCode === 13) {
            $('.btn_search_case').trigger('click');
        }
    });
    $('.btn_search_case').click(function(e) {
        e.preventDefault();
        var csrf_token = $('meta[name="csrf-token"]').attr('content');
        var case_id = $.trim($('.caseIdS').val());
        var emp_name = $.trim($('.employeeNameS').val());
        var advisor_name = $.trim($('.advisorNameS').val());
        var reqUrl = '/cases/search';
        if ((case_id == '') && (emp_name == '') && (advisor_name == '')) {
            console.log(null);
            return;
        } else {
            $.ajax({
                type: "POST",
                url: reqUrl,
                data: {
                    'case_id': case_id,
                    'emp_name': emp_name,
                    'advisor_name': advisor_name
                },
                dataType: "html",
                headers: {
                    'X-CSRF-TOKEN': csrf_token
                },
                success: function(response) {
                    $('.table_wrap').html(response);
                }
            });
        }

    });

    $('.btn_search_case_clear').click(function() {
        $('.caseIdS').val('');
        $('.employeeNameS').val('');
        $('.advisorNameS').val('');
    });

    function confirmationDelete(anchor) {
        swal({
                title: "Are you sure want to delete this Case?",
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
    }

    function confirmationDuplicate(anchor) {
        swal({
                title: "Are you sure want to duplicate this Case?",
                text: "Once proceeded, Whole information of this case will be duplicated.",
                icon: "warning",
                buttons: true,
                dangerMode: false,
            })
            .then((willDelete) => {
                if (willDelete) {
                    window.location = anchor.attr("href");
                }
            });
    }
</script>
@endsection
