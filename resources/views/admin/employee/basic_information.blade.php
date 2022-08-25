@extends('admin.layouts.admin_layout')
@section('style')
<style>

</style>
@endsection
@section('content')
@include('admin.layouts.admin_top_menu')
@if($employee['EmployeeTypeId'] == 11 || $employee['EmployeeTypeId'] == 0)
<style>
    .other_emp_type {
        display: none;
    }
</style>
@endif
<section class="edit_advisor_wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                @include('admin.layouts.messages')
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 emp_details_section">
                @include('admin.layouts.sections.nameTitle')
            </div>
        </div>
        <div class="row employee_details_wrap">
            <div class="col-md-2">
                <div class="employee_detail_sidebar">
                    @include('admin.layouts.emp_details_menu')
                </div>
            </div>
            <div class="col-xs-10 employee_detail_block_wrap">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="info_heading">
                            <p class="text-left">Basic Information</p>
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <div class="basic_info_form_wrap">
                            <form action="{{url('employee/basic_info/update')}}/{{$empId}}" method="POST" id="basic_info_form">
                                @csrf
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="name">Name: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <input type="text" name="name" value="{{$employee['EmployeeName']}}" class="">
                                            <label class="error-help-block">{{ $errors->first('name') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="advisor">Advisor: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <select name="advisor" id="advisor_list">
                                                @forelse($data['advisors'] as $advisor)
                                                <option value="{{ $advisor['AdvisorId'] }}" {{($employee['AdvisorId'] == $advisor['AdvisorId']) ? 'selected' : ''}}>{{$advisor['workshop_code'] . " - " . $advisor['AdvisorName'] }}</option>
                                                @empty
                                                <option> -- </option>
                                                @endforelse
                                            </select>
                                            <label class="error-help-block">{{ $errors->first('advisor') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="date_received">Date Received: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <input type="date" name="date_received" value="{{date('Y-m-d', strtotime($employee['DateReceived']))}}">
                                            <label class="error-help-block">{{ $errors->first('date_received') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="due_date">Due Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <input type="date" name="due_date" value="{{date('Y-m-d', strtotime($employee['DueDate']))}}">
                                            <label class="error-help-block">{{ $errors->first('due_date') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="date_completed">Date Completed: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <input type="date" name="date_completed" value="{{ ($employee['DateCompleted']) ? (date('Y-m-d', strtotime($employee['DateCompleted']))) : ''}}">
                                            <label class="error-help-block">{{ $errors->first('date_completed') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="report_month">Report Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <select name="report_month" id="report_date">
                                                @foreach($months as $key => $val)
                                                <option value="{{$key}}" {{($key == date('m', strtotime($employee['ReportDate']))) ? 'selected' : ''}}>{{$key}} - {{$val}}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="report_year" value="{{date('Y', strtotime($employee['ReportDate']))}}">
                                            <label class="error-help-block">{{ $errors->first('report_month') }}</label>
                                            <label class="error-help-block">{{ $errors->first('report_year') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="address">Address: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <textarea name="address" id="employeeAddress" cols="30" rows="5">{{ $employee['EmployeeAddress'] }}</textarea>
                                            <label class="error-help-block">{{ $errors->first('address') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="system">Retirement System: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            @forelse($data['systemTypes'] as $system)
                                            <div class="radio">
                                                <label><input type="radio" name="system" value="{{$system['AppLookupId']}}" {{($system['AppLookupId'] == $employee['SystemTypeId']) ? 'checked' : ''}}>{{ ($system['AppLookupName'] == 'Transfers') ? 'FERS ' : '' }}{{ $system['AppLookupName'] }}</label>

                                                @if($system['AppLookupName'] == 'CSRS Offset')
                                                <input type="date" name="csrs_offset_date" value="{{($employee['CSRSOffsetDate'] != null) ? date('Y-m-d', strtotime($employee['CSRSOffsetDate'])) : ''}}">
                                                <label class="error-help-block">{{ $errors->first('csrs_offset_date') }}</label>
                                                @endif

                                                @if($system['AppLookupName'] == 'Transfers')
                                                <input type="date" name="fers_transfer_date" value="{{($employee['FERSTransferDate'] != null) ? date('Y-m-d', strtotime($employee['FERSTransferDate'])) : ''}}">
                                                <label class="error-help-block">{{ $errors->first('fers_transfer_date') }}</label>
                                                @endif
                                            </div>
                                            @empty
                                            @endforelse
                                            <label class="error-help-block">{{ $errors->first('system') }}</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="retirement_type">Retirement Type: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            @forelse($data['retirementTypes'] as $ret)
                                            <div class="radio">
                                                <label><input type="radio" name="retirement_type" value="{{$ret['AppLookupId']}}" {{($ret['AppLookupId'] == $employee['RetirementTypeId']) ? 'checked' : ''}}>{{ $ret['AppLookupName'] }}</label>
                                            </div>
                                            @empty
                                            <div class="radio">
                                                <label><input type="radio" name="retirement_type" value="8">Optional</label>
                                            </div>
                                            <div class="radio">
                                                <label><input type="radio" name="retirement_type" value="9">Mandatory</label>
                                            </div>
                                            <div class="radio">
                                                <label><input type="radio" name="retirement_type" value="10">Disability</label>
                                            </div>
                                            @endforelse
                                            <label class="error-help-block">{{ $errors->first('retirement_type') }}</label>
                                        </div>
                                    </div>
                                </div>
                                {{--
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-4">
                                            <div class="label_div">
                                                <label for="csrs_offset_date">CSRS Offset Date: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-8">
                                            <input type="date" name="csrs_offset_date" value="{{($employee['CSRSOffsetDate'] != null) ? date('Y-m-d', strtotime($employee['CSRSOffsetDate'])) : ''}}">
                                <label class="error-help-block">{{ $errors->first('csrs_offset_date') }}</label>
                        </div>
                    </div>
                </div>
                <div class="row in_gap">
                    <div class="form-group">
                        <div class="col-xs-4">
                            <div class="label_div">
                                <label for="fers_transfer_date">FERS Transfer Date: </label>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <input type="date" name="fers_transfer_date" value="{{($employee['FERSTransferDate'] != null) ? date('Y-m-d', strtotime($employee['FERSTransferDate'])) : ''}}">
                            <label class="error-help-block">{{ $errors->first('fers_transfer_date') }}</label>
                        </div>
                    </div>
                </div>
                <div class="row in_gap">
                    <div class="form-group">
                        <div class="col-xs-4">
                            <div class="label_div">
                                <label for="special_provisions_date">Special Provisions Date: </label>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            <input type="date" name="special_provisions_date" value="{{($employee['SpecialProvisionsDate'] != null) ? date('Y-m-d', strtotime($employee['SpecialProvisionsDate'])) : ''}}">
                            <label class="error-help-block">{{ $errors->first('special_provisions_date') }}</label>
                        </div>
                    </div>
                </div>
                --}}
                <div class="row in_gap">
                    <div class="form-group">
                        <div class="col-xs-4">
                            <div class="label_div">
                                <label for="employee_type">Employee Type: </label>
                            </div>
                        </div>
                        <div class="col-xs-8">
                            @for($i = 0; $i <= (count($data['employeeTypes']) -1); $i++) <div class="radio">
                                <label title="{{$data['employeeTypes'][$i]['AppLookupDescription']}}"><input class="input_emp_type" type="radio" name="employee_type" value="{{$data['employeeTypes'][$i]['AppLookupId']}}" {{($data['employeeTypes'][$i]['AppLookupId'] == $employee['EmployeeTypeId']) ? 'checked' : ''}}>{{ ($data['employeeTypes'][$i]['AppLookupName'] == 'Other') ? 'Special Provision' : $data['employeeTypes'][$i]['AppLookupName'] }}</label>
                        </div>
                        @endfor
                        <label class="error-help-block">{{ $errors->first('employee_type') }}</label>
                    </div>
                </div>
            </div>
            <div class="row in_gap other_emp_type">
                <div class="form-group">
                    <div class="col-xs-4">
                        <div class="label_div">
                            <label for="other_type">Other Type: </label>
                        </div>
                    </div>
                    <div class="col-xs-8">
                        @forelse($data['otherEmployeeTypes'] as $oempType)
                        <div class="radio">
                            <label><input type="radio" name="other_employee_type" value="{{$oempType['AppLookupId']}}" {{ ($oempType['AppLookupId'] == $employee['OtherEmployeeTypeId']) ? 'checked' : '' }}>{{$oempType['AppLookupName']}}</label>
                        </div>
                        @empty
                        @endforelse
                        <input type="date" name="special_provisions_date" value="{{($employee['SpecialProvisionsDate'] != null) ? date('Y-m-d', strtotime($employee['SpecialProvisionsDate'])) : ''}}">
                        <label class="error-help-block">{{ $errors->first('special_provisions_date') }}</label>
                        <label class="error-help-block">{{ $errors->first('other_employee_type') }}</label>
                    </div>
                </div>
            </div>
            <div class="row in_gap">
                <div class="form-group">
                    <div class="col-xs-4">
                        <div class="label_div">
                            <label for="postal_employee">Postal Employee: </label>
                        </div>
                    </div>
                    <div class="col-xs-8">
                        <div class="">
                            <label><input type="checkbox" name="postal_employee" value="1" {{($employee['PostalEmployee'] == 1) ? 'checked' : ''}}></label>
                        </div>
                        <label class="error-help-block">{{ $errors->first('postal_employee') }}</label>
                    </div>
                </div>
            </div>
            <div class="row in_gap">
                <div class="form-group">
                    <div class="col-xs-4">
                        <div class="label_div">
                            <label for="marital_status">Marital Status: </label>
                        </div>
                    </div>
                    <div class="col-xs-8">
                        <select name="marital_status" id="marital_status">
                            <option value=""></option>
                            @forelse($data['marital_statuses'] as $mar)
                            <option value="{{$mar['AppLookupId']}}" {{($employee['MaritalStatusTypeId'] == $mar['AppLookupId']) ? 'selected' : ''}}>{{$mar['AppLookupName']}}</option>
                            @empty
                            @endforelse
                        </select>
                        <label class="error-help-block">{{ $errors->first('marital_status') }}</label>
                    </div>
                </div>
            </div>

            <div class="row in_gap">
                <div class="configuration_title">
                    <input type="hidden" name="next" value="0">
                    <input type="submit" value="Save" class="btn btn-sm btns-configurations">
                    <a class="btn btn-sm btns-configurations">Save & Next</a>
                    <button type="button" class="btn btn-sm btns-configurations">Cancel</button>
                </div>
            </div>
            </form>
        </div>
    </div>
    </div>

    </div>
    </div>
    </div>
</section>
@endsection
@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\EmployeeBasicInfoRequest', '#basic_info_form'); !!}

<script>
    $('.input_emp_type').on('click', function() {
        var emp_type = $(this).val();
        if (emp_type == "12") {
            $('.other_emp_type').fadeIn();
        } else {
            $('.other_emp_type').fadeOut();
        }
    });
    $('input[name=system]').click(function() {
        $('input[name=csrs_offset_date]').val("");
        $('input[name=fers_transfer_date]').val("");
    });
</script>
<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('employeeAddress', {
        toolbar: 'simple',
        removeButtons: 'Cut,Undo,Redo,Copy,Paste,PasteText,PasteFromWord,Source,Save,NewPage,ExportPdf,Preview,Print,Find,Templates,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Bold,CopyFormatting,NumberedList,Outdent,Blockquote,JustifyLeft,BidiLtr,Link,Image,Flash,BidiRtl,Unlink,JustifyCenter,CreateDiv,Indent,BulletedList,RemoveFormat,Italic,Underline,Strike,Subscript,Superscript,JustifyRight,JustifyBlock,Language,Anchor,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles,TextColor,BGColor,ShowBlocks,Maximize,About',
        enterMode: CKEDITOR.ENTER_BR
    });

    // CKEDITOR.editorConfig = function(config) {
    //     config.toolbarGroups = [{
    //             name: 'clipboard',
    //             groups: ['clipboard', 'undo']
    //         },
    //         {
    //             name: 'document',
    //             groups: ['mode', 'document', 'doctools']
    //         },
    //         {
    //             name: 'editing',
    //             groups: ['find', 'selection', 'spellchecker', 'editing']
    //         },
    //         {
    //             name: 'forms',
    //             groups: ['forms']
    //         },
    //         '/',
    //         {
    //             name: 'basicstyles',
    //             groups: ['basicstyles', 'cleanup']
    //         },
    //         {
    //             name: 'paragraph',
    //             groups: ['list', 'indent', 'blocks', 'align', 'bidi', 'paragraph']
    //         },
    //         {
    //             name: 'links',
    //             groups: ['links']
    //         },
    //         {
    //             name: 'insert',
    //             groups: ['insert']
    //         },
    //         '/',
    //         {
    //             name: 'styles',
    //             groups: ['styles']
    //         },
    //         {
    //             name: 'colors',
    //             groups: ['colors']
    //         },
    //         {
    //             name: 'tools',
    //             groups: ['tools']
    //         },
    //         {
    //             name: 'others',
    //             groups: ['others']
    //         },
    //         {
    //             name: 'about',
    //             groups: ['about']
    //         }
    //     ];

    //     config.removeButtons = 'Cut,Undo,Redo,Copy,Paste,PasteText,PasteFromWord,Source,Save,NewPage,ExportPdf,Preview,Print,Find,Templates,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Bold,CopyFormatting,NumberedList,Outdent,Blockquote,JustifyLeft,BidiLtr,Link,Image,Flash,BidiRtl,Unlink,JustifyCenter,CreateDiv,Indent,BulletedList,RemoveFormat,Italic,Underline,Strike,Subscript,Superscript,JustifyRight,JustifyBlock,Language,Anchor,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles,TextColor,BGColor,ShowBlocks,Maximize,About';
    // };
</script>
@endsection
