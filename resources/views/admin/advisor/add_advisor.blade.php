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
                    <p class="text-left">Add Advisor</p>
                </div>
                <div class="add_emp_wrap">
                    <div class="row">
                        @include('admin.layouts.messages')
                    </div>
                    <div class="row">
                        <form action="{{url('/advisor/save')}}" method="POST" id="addAdvisorForm">
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
                                            <label class="error-help-block">{{ $errors->first('name') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="company_name">Company Name:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="company_name">
                                            <label class="error-help-block">{{ $errors->first('company_name') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="workshop_code">Workshop Code:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="workshop_code">
                                            <label class="error-help-block">{{ $errors->first('workshop_code') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Appear on front cover:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <textarea id="advisorAddress" name="address" cols="30" rows="5"></textarea>
                                            <label class="error-help-block">{{ $errors->first('address') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="default_disclaimer">Default Disclaimer:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <select name="default_disclaimer" class="advisor_dropdown">
                                                <option value=""></option>
                                                @forelse($disclaimers as $disclaimer)
                                                <option value="{{$disclaimer->DisclaimerId}}">{{$disclaimer->DisclaimerName}}</option>
                                                @empty
                                                @endforelse
                                            </select>
                                            <label class="error-help-block">{{ $errors->first('default_disclaimer') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Suppress Confidential:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="checkbox" name="suppress_confidential" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="configuration_title">
                                            <input type="submit" value="Save" class="btn btn-sm btns-configurations">
                                            <button class="btn btn-sm btns-configurations">Cancel</button>
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
{!! JsValidator::formRequest('App\Http\Requests\AdvisorRequest', '#addAdvisorForm'); !!}

<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('advisorAddress', {
        toolbar : 'simple'
    });
</script>

@endsection