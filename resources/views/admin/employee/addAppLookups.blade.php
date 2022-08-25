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
                    <p class="text-left">Add AppLookups</p>
                </div>
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
                            <p class="text-left">Add AppLookups</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <form action="{{url('/app/lookups/save')}}" method="POST" id="addChildForm">
                            @csrf
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="from_date">AppLookup Type Name: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="AppLookupTypeName">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="dob">AppLookup Name:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="AppLookupName">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="from_date">AppLookup Description: </label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="AppLookupDescription">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row in_gap">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <div class="label_div">
                                                <label for="dob">Display Order:</label>
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <input type="text" name="DisplayOrder">
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

                <div class="row">
                    <div class="col-md-12">
                        <h4>Retirement Types</h4>
                    </div>
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered">
                            <tr>
                                <th>AppLookupTypeName</th>
                                <th>AppLookupName</th>
                                <th>AppLookupDescription</th>
                                <th>DisplayOrder</th>
                            </tr>
                            @forelse($data['retirementTypes'] as $rType)
                            <tr>
                                <td>{{$rType['AppLookupTypeName']}}</td>
                                <td>{{$rType['AppLookupName']}}</td>
                                <td>{{$rType['AppLookupDescription']}}</td>
                                <td>{{$rType['DisplayOrder']}}</td>
                            </tr>
                            @empty
                            @endforelse
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
@section('scripts') 
{!! JsValidator::formRequest('App\Http\Requests\ChildRequest', '#addChildForm'); !!}
@endsection
