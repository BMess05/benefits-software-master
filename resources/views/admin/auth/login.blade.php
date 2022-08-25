@extends('admin.layouts.admin_layout')
@section('style')
<style>


</style>
@endsection
@section('content')
<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @if( \Session::has('error') )
                <div class="alert alert-danger alert-dismissible fade in">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <strong> {{\Session::get('error')}}!</strong>
                </div>
                @endif
            </div>
            <div class="col-md-12">
                <div class="form_layout">
                    <div class="form_title">
                        Login
                    </div>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form_inputs">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form_lower">
                            <input type="submit" class="btn btn-login btn-sm" name="login_btn" value="Login">
                            @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif
                            <p class="text-center">*Password is case sensitive</p>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
