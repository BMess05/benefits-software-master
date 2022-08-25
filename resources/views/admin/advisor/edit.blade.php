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
                <div class="listing-title">
                    <p class="text-left">Edit Advisor</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 edit_advisor_form">
                <form action="{{url('advisor/update')}}/{{$advisor->AdvisorId}}" id="editAdvisorForm" method="POST">
                    @csrf
                    <div class="">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="name">Name: </label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="name" value="{{$advisor->AdvisorName}}" class="form-control">
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
                                        <input type="text" name="company_name" value="{{$advisor->company_name}}" class="form-control">
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
                                        <input type="text" name="workshop_code" value="{{$advisor->workshop_code}}" class="form-control">
                                        <label class="error-help-block">{{ $errors->first('workshop_code') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="address">Address: </label>
                                </div>
                                <div class="col-md-8">
                                    <textarea id="advisorAddress" name="address" class="form-control">{{$advisor->AdvisorAddress}}</textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="default_disclaimer">Default Disclaimer: </label>
                                </div>
                                <div class="col-md-8">
                                    <select name="default_disclaimer" class="form-control">
                                        <option value="">-Select Disclaimer-</option>
                                        @forelse($disclaimers as $disclaimer)
                                        <option value="{{$disclaimer->DisclaimerId}}" {{($disclaimer->DisclaimerId == $advisor->DefaultDisclaimerId) ? 'selected' : ''}}>{{$disclaimer->DisclaimerName}}</option>
                                        @empty
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="default_disclaimer">Suppress Confidential: </label>
                                </div>
                                <div class="col-md-8">
                                    <input type="checkbox" name="suppress_confidential" class="suppress_confidential_chk" {{($advisor->SuppressConfidential == 1) ? 'checked' : ''}}>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="configuration_title">
                                        <input type="submit" value="Save" class="btn btn-default btn-sm btns-configurations">
                                        <button class="btn btn-sm btns-configurations">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-12">

            </div>

        </div>
    </div>
</section>
@endsection
@section('scripts') 
{!! JsValidator::formRequest('App\Http\Requests\AdvisorRequest', '#editAdvisorForm'); !!}

<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('advisorAddress', {
        toolbar : 'simple'
    });
</script>

@endsection
