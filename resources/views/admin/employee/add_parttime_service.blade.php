@extends('admin.layouts.admin_layout')
@section('style')
<style>
.percentage_block {
    display: none;
}
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
                        @include('admin.layouts.messages')
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12">
                        <div class="info_heading child_listing_title">
                            <p class="text-left">Part Time Service</p>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <form action="{{url('employee/parttime_service/save')}}/{{$empId}}" method="POST" id="addPArtTimeServiceForm">
                            @csrf

                            <div class="col-xs-12 percentage_block">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="percentage">Percentage:</label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="number" name="percentage" step="0.1" value="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <a href="#" class="show_dates_link" data-id="0">Add Dates</a>
                                    </div>
                                </div>
                            </div>
                            <div id="addPartTimeMain">
                                <div id="partTimeWrap">
                                    <div class="col-xs-12">
                                        <a href="#" class="show_percentage_link" data-id="0">Add Percentage</a>
                                    </div>
                                    <div class="partTimeAddWrap" id="partTimeAddWrap0">
                                        <div class="date_block">
                                            <div class="col-xs-12">
                                                <div class="row in_gap">
                                                    <div class="form-group">
                                                        <div class="col-xs-3">
                                                            <div class="label_div">
                                                                <label for="from_date">From Date:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-9">
                                                            <input type="date" name="from_date[]">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12">
                                                <div class="row in_gap">
                                                    <div class="form-group">
                                                        <div class="col-xs-3">
                                                            <div class="label_div">
                                                                <label for="to_date">To Date:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-9">
                                                            <input type="date" name="to_date[]">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-12">
                                                <div class="row in_gap">
                                                    <div class="form-group">
                                                        <div class="col-xs-3">
                                                            <div class="label_div">
                                                                <label for="hours_weekly">Hours/ Week:</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-9">
                                                            <input type="text" name="hours_weekly[]" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="col-xs-12">
                                    <div class="row in_gap">
                                        <div class="form-group">
                                            <div class="col-xs-3">

                                            </div>
                                            <div class="col-xs-9 text-right">
                                                <button type="button" class="btm btn-primary btn-sm btn_add_more_parttime">Add More</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-12">
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
{!! JsValidator::formRequest('App\Http\Requests\ParttimeServiceRequest', '#addPArtTimeServiceForm'); !!}
<script>
$('.btn_add_more_parttime').on('click', function() {
    $('.partTimeAddWrap').each(function(index, value) {
        $(this).attr('id', `partTimeAddWrap${index}`)
    });
    $('.removeService').each(function(index, element) {
        $(this).attr('data-id', index + 1);
    });
    $('.show_percentage_link').each(function(index, element) {
        $(this).attr('data-id', index);
    });
    $('.show_dates_link').each(function(index, element) {
        $(this).attr('data-id', index);
    });

    var index = $('.partTimeAddWrap').length;
    var html = `<div class="partTimeAddWrap" id="partTimeAddWrap${index}">
            <div class="row head_newService">
                <div class="col-xs-6">
                    <label for="from_date">Add Part Time Service</label>
                </div>
                <div class="col-xs-6 text-right">
                    <button type="button" class="btn btn-primary btn-sm removeService" data-id="${index}">Remove</button>
                </div>
            </div>
            <div class="date_block">
                <div class="col-xs-12">
                    <div class="row in_gap">
                        <div class="form-group">
                            <div class="col-xs-3">
                                <div class="label_div">
                                    <label for="from_date">From Date:</label>
                                </div>
                            </div>
                            <div class="col-xs-9">
                                <input type="date" name="from_date[]">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="row in_gap">
                        <div class="form-group">
                            <div class="col-xs-3">
                                <div class="label_div">
                                    <label for="to_date">To Date:</label>
                                </div>
                            </div>
                            <div class="col-xs-9">
                                <input type="date" name="to_date[]">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="row in_gap">
                        <div class="form-group">
                            <div class="col-xs-3">
                                <div class="label_div">
                                    <label for="hours_weekly">Hours/Week:</label>
                                </div>
                            </div>
                            <div class="col-xs-9">
                                <input type="text" name="hours_weekly[]" value="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
    $(html).appendTo('#partTimeWrap');
});
$(document).on('click', '.removeService', function() {
    var id = $(this).data('id');
    $(`#partTimeAddWrap${id}`).remove();
});

$(document).on('click', '.show_percentage_link', function(e) {
    e.preventDefault();
    var index = $(this).data('id');
    $(`.percentage_block`).fadeIn();
    $(`#addPartTimeMain`).fadeOut();
});

$(document).on('click', '.show_dates_link', function(e) {
    e.preventDefault();
    var index = $(this).data('id');
    $(`.percentage_block`).fadeOut();
    $(`#addPartTimeMain`).fadeIn();
});
</script>
@endsection
