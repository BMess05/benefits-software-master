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
                            <p class="text-left">Part Time Service</p>
                            <p class="text-right"><a href="{{url('/employee/parttime_service/add')}}/{{$empId}}">Add New Part Time Service</a></p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            @if(!empty($pTServices))
                            <thead class="case_list_head">
                                <tr>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Hours/Week</th>
                                    <th>Percentage</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            @endif
                            <tbody>
                                @forelse($pTServices as $service)
                                <tr>
                                    <td><a href="{{url('employee/parttime_service/edit')}}/{{$service['EmployeeId']}}/{{$service['PartTimeServiceId']}}">{{ ($service['FromDate'] == NULL) ? '-' : date('m/d/Y', strtotime($service['FromDate'])) }}</a></td>
                                    <td>{{ ($service['ToDate'] == NULL) ? '-' : date('m/d/Y', strtotime($service['ToDate'])) }}</td>
                                    <td>{{ round($service['HoursWeek'], 2) }}</td>
                                    <td>{{ $service['percentage'] }}</td>
                                    <td>
                                        <a class="btn btn-primary btn-sm" href="{{url('employee/parttime_service/edit')}}/{{$service['EmployeeId']}}/{{$service['PartTimeServiceId']}}">Edit</a>
                                        <a onclick="javascript:confirmationDelete($(this));return false;" class="btn btn-danger btn-sm" href="{{ route('deletePartTimeService', $service['PartTimeServiceId']) }}">Delete</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="no_data_found" colspan="6">No results...</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
<script>
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
    }
</script>
@endsection
