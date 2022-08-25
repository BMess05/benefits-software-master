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
                    <p class="text-left">Advisors</p>
                    <p class="text-right"><a href="{{url('/advisor/add')}}">Add New Advisor</a></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table_wrap">
                    <table class="table table-bordered" id="sortTable">
                        <thead class="case_list_head"> 
                            <tr>
                                <th>Advisor</th>
                                <th>Company Name</th>
                                <th>Workshop Code</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($advisors as $advisor)
                            <tr>
                                <td>
                                    <a href="{{url('advisor/details')}}/{{$advisor->AdvisorId}}">{{$advisor->AdvisorName}}</a>
                                </td>
                                <td>{{ $advisor->company_name }}</td>
                                <td>{{ $advisor->workshop_code }}</td>
                                <td>
                                    @if($advisor->IsActive == 1)
                                        Active
                                    @else
                                        Inactive
                                    @endif
                                </td>
                                <td>
                                    @if($advisor->IsActive == 1)
                                    <a href="#" class="btn btn-primary btn-sm advisorBtn" data-id="{{ $advisor->AdvisorId }}" data-action="deactive">Inactive</a>
                                    @else
                                    <a href="#" class="btn btn-primary btn-sm advisorBtn" data-id="{{ $advisor->AdvisorId }}" data-action="active">Active</a>
                                    @endif
                                </td>
                            </tr>
                            @empty
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
$('.advisorBtn').on('click', function(e) {
    e.preventDefault();
    var csrf_token = $('meta[name="csrf-token"]').attr('content');
    var reqUrl = '/advisor/change_status';
    var id = $(this).data('id');
    var action = $(this).data('action');
    $.ajax({
        type: "POST",
        url: reqUrl,
        data: {
            'advisor_id': id,
            'action': action
        },
        dataType: "json",
        headers: {
            'X-CSRF-TOKEN': csrf_token
        },
        success: function(response) {
            location.reload();
        }
    });
});
</script>
@endsection