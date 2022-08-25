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
                    <p class="text-left">Manage Users</p>
                    <p class="text-right"><a href="{{route('addUser')}}">Add New User</a></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table_wrap">
                    <table class="table table-bordered" id="sortTable">
                        <thead class="case_list_head">
                            {{--
                                <tr>
                                <th colspan="9">Rows {{$users->count()}} of {{$users->total()}}, Page {{$users->currentPage()}} of 98 | {{ $users->links() }}</th>
                            </tr>
                            --}}
                            <tr>
                                <th>User#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $k => $user)
                            <tr>
                                <td>{{ ++$k }}</a></td>
                                <td>{{ $user->name ?? "" }}</td>
                                <td>{{ $user->email ?? "" }}</td>
                                <td>{{ $user->status == 1 ? "Active" : "Inactive" }}</td>
                                <td>
                                    <a class="btn btn-primary btn-sm" href="{{ route('editUser', $user->id) }}"><i class="fa fa-edit"></i></a>
                                    <a onclick="javascript:confirmationDelete($(this));return false;" class="btn btn-danger btn-sm" href="{{ route('deleteUser', $user->id) }}"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7">No users found</td>
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
    function confirmationDelete(anchor) {
        swal({
                title: "Are you sure want to delete this User?",
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
