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
                @include('admin.layouts.messages')
            </div>
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
                        <div class="info_heading child_listing_title">
                            <p class="text-left">Add Notes</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <form action="{{url('/employee/notes/save/'.$empId)}}" method="POST" id="addNotesForm">
                            @csrf
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-1">
                                            <div class="label_div">
                                                <label for="from_date">Notes: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-11">
                                            <textarea name="notes" id="notes" class="form-control">{{ old('notes') ?? $notes }}</textarea>
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

<script src="/vendor/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace('notes', {
        toolbar: 'simple',
        removeButtons: 'Cut,Undo,Redo,Copy,Paste,PasteText,PasteFromWord,Source,Save,NewPage,ExportPdf,Preview,Print,Find,Templates,Replace,SelectAll,Scayt,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Bold,CopyFormatting,NumberedList,Outdent,Blockquote,JustifyLeft,BidiLtr,Link,Image,Flash,BidiRtl,Unlink,JustifyCenter,CreateDiv,Indent,BulletedList,RemoveFormat,Italic,Underline,Strike,Subscript,Superscript,JustifyRight,JustifyBlock,Language,Anchor,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,Format,Styles,TextColor,BGColor,ShowBlocks,Maximize,About',
        enterMode: CKEDITOR.ENTER_BR
    });
</script>
@endsection
