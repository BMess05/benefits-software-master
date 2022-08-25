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
                    <p class="text-left">Add User</p>
                </div>
                <div class="add_emp_wrap">
                    <div class="row">
                        <form action="{{ route('saveUser') }}" method="POST" id="addUserForm">
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
                                            <input type="text" name="name" value="{{ old('name') }}">
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
                                            <input type="email" name="email" value="{{ old('email') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Password:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="password" name="password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Confirm Password:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input id="password-confirm" type="password" name="password_confirmation" required>
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
                                            <input type="checkbox" name="status" checked>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="configuration_title">
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
{!! JsValidator::formRequest('App\Http\Requests\UserAddRequest', '#addUserForm'); !!}
@endsection
