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
                            <p class="text-left">Files</p>
                            <p class="text-right"><a href="{{url('/employee/files/add')}}/{{$empId}}">Add New Files</a></p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            @if(!empty($files))
                            <thead class="case_list_head">
                                <tr>
                                    <th>Files</th>
                                </tr>
                            </thead>
                            @endif
                            <tbody>
                                @forelse($files as $file)
                                <tr>
                                    <td><a href="{{url('/employee/file/download')}}/{{$empId}}/{{$file['StoredFileName']}}">{{ $file['OrigFileName'] }}</a></td>
                                </tr>
                                @empty
                                <tr>
                                    <td class="no_data_found" colspan="2">No results...</td>
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
