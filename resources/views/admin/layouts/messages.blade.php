@if(session('status'))
<div class="alert dark alert-{{ Session::get('status') }} alert-dismissible" role="alert" id="common_message">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">×</span>
    </button>
  {{ Session::get('message') }}
</div>
@endif
