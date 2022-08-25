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
                    <p class="text-left">Edit Disclaimer</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 edit_advisor_form">
                <form action="{{url('disclaimer/update'.'/'. base64_encode($disclaimer->DisclaimerId))}}" method="POST" id="editDisclaimer">
                    @csrf
                    <div class="">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="name">Name: </label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="name" value="{{$disclaimer->DisclaimerName}}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="address">Text: </label>
                                </div>
                                <div class="col-md-8">
                                    <textarea id="disclaimerText" name="disclaimer_text" class="form-control" rows="12">{{$disclaimer->DisclaimerText}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="make_default">Make Default</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="checkbox" name="make_default" value="1" class="suppress_confidential_chk">
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="col-md-4"></div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="configuration_title">
                            <input type="submit" value="Save" class="btn btn-default btn-sm btns-configurations">
                            @if(Auth::user()->role == 0)
                            <a href="{{ route('admin.disclaimer.delete', $disclaimer->DisclaimerId) }}" class="btn btn-default btn-sm btns-remove">Remove</a>
                            @endif
                            <button class="btn btn-default btn-sm btns-configurations">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</section>
@endsection

@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\DisclaimerRequest', '#editDisclaimer'); !!}

<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('disclaimerText', {
        toolbar: 'simple'
    });
</script>

@endsection
