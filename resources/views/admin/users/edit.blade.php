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
                    <p class="text-left">Edit User</p>
                </div>
                <div class="add_emp_wrap">
                    <div class="row">
                        <form action="{{ route('updateUser', $user->id) }}" method="POST" id="editUserForm">
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
                                            <input type="text" name="name" value="{{ old('name') ?? $user->name }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Email:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="email" name="email" value="{{ old('email') ?? $user->email }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="pswd">Password:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="pswd" value="{{ old('pswd') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Active:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="checkbox" name="status" {{ ($user->status == 1) ? 'checked' : ''}}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="configuration_title">
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <input type="submit" value="Save" class="btn btn-sm btns-configurations">
                                            <a href="{{ route('listStandardUsers') }}" class="btn btn-sm btns-configurations">Cancel</a>
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
{!! JsValidator::formRequest('App\Http\Requests\UserEditRequest', '#editUserForm'); !!}
@endsection
