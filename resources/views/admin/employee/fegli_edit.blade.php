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
                    <form action="{{url('employee/fegli/update')}}/{{$empId}}" method="POST" id="fegliUpdateForm">
                        @csrf
                        <div class="col-md-12">
                            <div class="info_heading">
                                <p class="text-left">FEGLI</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-xs-3">
                                            <div class="label_div">
                                                <label for="salary_override">Salary Override: </label>
                                            </div>
                                        </div>
                                        <div class="col-xs-9">
                                            <input type="text" name="salary_override" value="{{!(empty($data)) ? round($data['SalaryForFEGLI'], 2) : '0'}}" class=""> (yearly)

                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-sm-3 col-xs-3">
                                            <div class="label_div">
                                                <label for="does_meet_five_year">Does Not Meet Five Year: </label>
                                            </div>
                                        </div>
                                        <div class="col-sm-9 col-xs-9">
                                            <input type="checkbox" name="does_meet_five_year" value="1" {{(!empty($data) ? ($data['DoesNotMeetFiveYear'] == 1) ? 'checked' : '' : '')}}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--
                        <div class="col-md-12">
                            <div class="old_new_row">
                                <div class="row">
                                    <div class="col-md-8"></div>
                                    <div class="col-md-4">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="bg_blue"><strong class="text-white">New</strong></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-right bg_blue"><strong class="text-white">Old</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        --}}
                        <div class="col-md-12">
                            <div class="info_heading child_listing_title">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="text-left"><input type="checkbox" name="include_basic" vlaue="1" {{(!empty($data) ? ($data['BasicInc'] == 1) ? 'checked' : '' : '')}}> Basic</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-right"><a>${{(!empty($data) ? ($data['BasicInc'] == 1) ? $costForBasicPremium : 0 : 0)}}</a></p>
                                        <input type="hidden" name="basic_amount" value="{{ $costForBasicPremium }}">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap border_layout">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="grid_label">
                                                <label for="basicReductionAfterRetirement">Basic Premium Reduction After Retirement: </label>
                                            </div>
                                            @forelse($basicReductionOptions as $option)
                                            <label class="radio-inline">
                                                <input type="radio" name="basicReductionAfterRetirement" value="{{$option->AppLookupDescription}}" {{!empty($data) ? (($data['basicReductionAfterRetirement'] == $option->AppLookupDescription) ? 'checked' : '') : (($option->AppLookupDescription == 0) ? 'checked' : '')}}>{{$option->AppLookupDescription}} %
                                            </label>
                                            @empty
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="info_heading child_listing_title">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="text-left"><input type="checkbox" name="include_optionA" vlaue="1" {{(!empty($data) ? ($data['OptionAInc'] == 1) ? 'checked' : '' : '')}}> Option A</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-right"><a>${{(!empty($data) ? ($data['OptionAInc'] == 1) ? $costForOptionAPremium : 0 : 0)}}</a></p>
                                        <input type="hidden" name="optionA_amount" value="{{(isset($data['OptionAAmount']) && $data['OptionAAmount'] > 0) ? $data['OptionAAmount'] : $costForOptionAPremium}}">

                                    </div>
                                </div>


                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap border_layout">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="grid_label">
                                                <label for="optionAReductionAfterRetirement">Option A Premium Reduction After Retirement: </label>
                                            </div>
                                            @forelse($optionAReductionOptions as $option)
                                            <label class="radio-inline">
                                                <input type="radio" name="optionAReductionAfterRetirement" value="{{$option->AppLookupDescription}}" {{!empty($data) ? (($data['optionAReductionAfterRetirement'] == $option->AppLookupDescription) ? 'checked' : '') : (($option->AppLookupDescription == 75) ? 'checked' : '')}}>{{$option->AppLookupDescription}} %
                                            </label>
                                            @empty

                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="col-md-12">
                            <div class="info_heading child_listing_title">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="text-left"><input type="checkbox" name="include_optionB" vlaue="1" {{(!empty($data) ? ($data['OptionBInc'] == 1) ? 'checked' : '' : '')}}> Option B</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-right"><a>${{(!empty($data) ? ($data['OptionBInc'] == 1) ? $costForOptionBPremium : 0 : 0)}}</a></p>
                                        <input type="hidden" name="optionB_amount" value="{{(isset($data['OptionBAmount']) && $data['OptionBAmount'] > 0) ? $data['OptionBAmount'] : $costForOptionBPremium }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap border_layout">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="grid_label">
                                                <label for="b_multiplier">B Multipler: </label>
                                            </div>
                                            <label class="radio-inline">
                                                <input type="radio" name="b_multiplier" value="1" {{(!empty($data) ? ($data['OptionBMultiplier'] == 1) ? 'checked' : '' : 'checked')}}>1 Times
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="b_multiplier" value="2" {{(!empty($data) ? ($data['OptionBMultiplier'] == 2) ? 'checked' : '' : '')}}>2 Times
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="b_multiplier" value="3" {{(!empty($data) ? ($data['OptionBMultiplier'] == 3) ? 'checked' : '' : '')}}>3 Times
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="b_multiplier" value="4" {{(!empty($data) ? ($data['OptionBMultiplier'] == 4) ? 'checked' : '' : '')}}>4 Times
                                            </label>
                                            <label class="radio-inline">
                                                <input type="radio" name="b_multiplier" value="5" {{(!empty($data) ? ($data['OptionBMultiplier'] == 5) ? 'checked' : '' : '')}}>5 Times
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row in_gap border_layout">
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="grid_label">
                                                <label for="optionBReductionAfterRetirement">Option B Premium Reduction After Retirement: </label>
                                            </div>
                                            @forelse($optionBReductionOptions as $option)
                                            <label class="radio-inline">
                                                <input type="radio" name="optionBReductionAfterRetirement" value="{{$option->AppLookupDescription}}" {{!empty($data) ? (($data['optionBReductionAfterRetirement'] == $option->AppLookupDescription) ? 'checked' : '') : (($option->AppLookupDescription == 0) ? 'checked' : '')}}>{{$option->AppLookupDescription}} %
                                            </label>
                                            @empty

                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="info_heading child_listing_title">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="text-left">
                                            <input type="checkbox" name="include_optionC" vlaue="1" {{(!empty($data) ? ($data['OptionCInc'] == 1) ? 'checked' : '' : '')}}> Option C
                                        </p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-right">
                                            <a>${{(!empty($data) ? ($data['OptionCInc'] == 1) ? $costForOptionCPremium : 0 : 0)}}</a>
                                        </p>
                                        <input type="hidden" name="optionC_amount" value="{{(isset($data['OptionCAmount']) && $data['OptionCAmount'] > 0) ? $data['OptionCAmount'] : $costForOptionCPremium}}">
                                    </div>
                                </div>


                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="basic_info_form_wrap">
                                <div class="row in_gap border_layout">
                                    <div class="form-group">
                                        <div class="row in_gap border_layout">
                                            <div class="col-md-12">
                                                <div class="grid_label">
                                                    <label for="c_multiplier">C Multipler: </label>
                                                </div>
                                                <label class="radio-inline">
                                                    <input type="radio" name="c_multiplier" value="1" {{(!empty($data) ? ($data['OptionCMultiplier'] == 1) ? 'checked' : '' : 'checked')}}>1 Times
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="c_multiplier" value="2" {{(!empty($data) ? ($data['OptionCMultiplier'] == 2) ? 'checked' : '' : '')}}>2 Times
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="c_multiplier" value="3" {{(!empty($data) ? ($data['OptionCMultiplier'] == 3) ? 'checked' : '' : '')}}>3 Times
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="c_multiplier" value="4" {{(!empty($data) ? ($data['OptionCMultiplier'] == 4) ? 'checked' : '' : '')}}>4 Times
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="c_multiplier" value="5" {{(!empty($data) ? ($data['OptionCMultiplier'] == 5) ? 'checked' : '' : '')}}>5 Times

                                                </label>
                                            </div>
                                        </div>

                                        <div class="row in_gap border_layout">
                                            <div class="col-md-12">
                                                <div class="grid_label">
                                                    <label for="optionCReductionAfterRetirement">Option C Premium Reduction After Retirement: </label>
                                                </div>
                                                @forelse($optionCReductionOptions as $option)
                                                <label class="radio-inline">
                                                    <input type="radio" name="optionCReductionAfterRetirement" value="{{$option->AppLookupDescription}}" {{!empty($data) ? (($data['optionCReductionAfterRetirement'] == $option->AppLookupDescription) ? 'checked' : '') : (($option->AppLookupDescription == 0) ? 'checked' : '')}}>{{$option->AppLookupDescription}} %
                                                </label>
                                                @empty

                                                @endforelse
                                            </div>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="listing-title">
                                                <p class="text-left">Dependents</p>
                                                <p class="text-right"><a href="{{url('/employee/fegli/addDependent/'.$empId)}}">Add New Dependents</a></p>

                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <table class="table table-bordered">
                                                @if(!empty($data) && !empty($data['fegli_dependent']))
                                                <thead class="case_list_head">
                                                    <tr>
                                                        <th>Date Of Birth</th>
                                                        <th>Age</th>
                                                        <th>Cover After 22</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($data['fegli_dependent'] as $dependent)
                                                    <tr>
                                                        <td>{{ ($dependent['DateOfBirth'] == NULL) ? '' : date('m/d/Y', strtotime($dependent['DateOfBirth'])) }}</td>
                                                        <td>{{ $dependent['age'] }}</td>
                                                        <td>{{ ($dependent['CoverAfter22'] == 0) ? 'false' : 'true' }}</td>
                                                        <td>
                                                            <a class="btn btn-primary btn-sm" href="{{ route('editDependent', $dependent['FEGLIDependentId']) }}">Edit</a>
                                                            <a onclick="javascript:confirmationDelete($(this));return false;" class="btn btn-danger btn-sm" href="{{ route('deleteDependent', $dependent['FEGLIDependentId']) }}">Delete</a>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="3" class="no_data_found">No results...</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                                @else
                                                <tr>
                                                    <td colspan="3" class="no_data_found">No results...</td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="info_heading child_listing_title input_titlelook">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="text-left">A + B + C amount</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-right">${{number_format($abcPlus, 2)}}</p>
                                    </div>
                                </div>


                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="info_heading child_listing_title input_titlelook">
                                <div class="row">
                                    <div class="col-md-8">
                                        <p class="text-left">Total Amount</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="text-right">${{ number_format(($costForBasicPremium + $costForOptionAPremium + $costForOptionBPremium + $costForOptionCPremium), 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="row in_gap">
                                <div class="configuration_title">
                                    <input type="hidden" name="next" value="0">
                                    <input type="submit" value="Save" class="btn btn-sm btns-configurations">
                                    <a class="btn btn-sm btns-configurations">Save & Next</a>
                                    <button class="btn btn-sm btns-configurations">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="important_link">
                    <a target="_blank" href="https://www.opm.gov/retirement-services/calculators/fegli-calculator/">https://www.opm.gov/retirement-services/calculators/fegli-calculator/</a>
                </div>

                <div class="summary_block_wrap">
                    <div class="row">
                        @foreach($summary_arr as $key => $summary)
                        <div class="col-md-6">
                            <label for="" class="summary_label">{{$key}} </label> <label class="summary_desc">{{$summary}}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\FegliRequest', '#fegliUpdateForm'); !!}
<script>
    function confirmationDelete(anchor) {
        swal({
                title: "Are you sure want to delete this Dependent?",
                text: "Once deleted, you will not be able to recover this data!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    window.location = anchor.attr("href");
                }
            });
        //   var conf = confirm("Are you sure want to delete this User?");
        //   if (conf) window.location = anchor.attr("href");
    }
</script>
@endsection
