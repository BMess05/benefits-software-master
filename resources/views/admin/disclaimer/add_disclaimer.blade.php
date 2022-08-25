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
                    <p class="text-left">Add Disclaimer</p>
                </div>
                <div class="add_emp_wrap">
                    <div class="row">
                        <form action="{{url('/disclaimer/save')}}" method="POST" id="disclaimerForm">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="name">Text:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <textarea id="disclaimerText" name="disclaimer_text" cols="60" rows="20"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">

                                        </div>
                                        <div class="col-md-9">
                                            <input type="checkbox" name="make_default" value="1">
                                            <label for="make_default">Make Default</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="configuration_title">
                                            <input type="submit" value="Save" class="btn btn-sm btns-configurations">
                                            <button class="btn btn-sm btns-configurations">Remove</button>
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
{!! JsValidator::formRequest('App\Http\Requests\DisclaimerRequest', '#disclaimerForm'); !!}

<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('disclaimerText');
</script>

@endsection
