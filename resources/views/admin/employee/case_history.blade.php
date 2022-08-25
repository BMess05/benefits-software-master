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
            <!-- <div class="col-md-2">
                <div class="employee_detail_sidebar">

                </div>
            </div> -->
            <div class="col-md-12 employee_detail_block_wrap">
                <div class="row">
                    <div class="col-md-12">
                        @include('admin.layouts.messages')
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="info_heading">
                            <p class="text-left">Employee History</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="basic_info_form_wrap">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#home">Basic Info</a></li>
                                <li><a data-toggle="tab" href="#retirement_eligibility_tab">Retirement Eligibility</a></li>
                                <li><a data-toggle="tab" href="#part_time_service_tab">Part Time Service</a></li>
                                <li><a data-toggle="tab" href="#pay_and_leave_tab">Pay and Leave</a></li>
                                <li><a data-toggle="tab" href="#tsp_tab">TSP</a></li>
                                <li><a data-toggle="tab" href="#fegli_tab">FEGLI</a></li>
                                <li><a data-toggle="tab" href="#health_benefits_tab">Health Benefits</a></li>
                                <li><a data-toggle="tab" href="#fltcip_tab">FLTCIP</a></li>
                                <li><a data-toggle="tab" href="#social_security_tab">Social Security</a></li>
                                <li><a data-toggle="tab" href="#configuration_tab">Employee Configuration</a></li>
                            </ul>

                            <div class="tab-content">
                                <div id="home" class="tab-pane fade in active">
                                    <div class="demo">
                                        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                            @forelse($basic_tab_data['Employee'] as $k => $tab)
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne{{$k}}" aria-expanded="true" aria-controls="collapseOne{{$k}}">
                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                            <?php
                                                            echo $tab['column_name'];
                                                            unset($tab['column_name']);
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="collapseOne{{$k}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <td>Changed By</td>
                                                                    <td>Changed At</td>
                                                                    <td>Old Value</td>
                                                                    <td>New Value</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse ($tab as $row)
                                                                <tr>
                                                                    <td><a target="_blank" href="{{ route('editUser', $row['updated_by_user']['id']) }}">{{ $row['updated_by_user']['name'] }}</a></td>
                                                                    <td>{{ date('d/m/Y', strtotime($row['updated_at'])) }}</td>
                                                                    <td>{!! $row['old_val_name'] !!}</td>
                                                                    <td>{!! $row['new_val_name'] !!}</td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="4">No Changes updated yet</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @empty

                                            @endforelse

                                        </div><!-- panel-group -->


                                    </div><!-- demo -->
                                </div>
                                <div id="retirement_eligibility_tab" class="tab-pane fade">
                                    <div class="demo">
                                        @foreach($retirement_tab_data as $table_name => $columns)
                                        <h3>{{ $table_name }} Changes Details</h3>
                                        <div class="panel-group" id="accordion{{ $table_name }}" role="tablist" aria-multiselectable="true">
                                            @foreach($columns as $k => $column_data)
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion{{ $table_name }}" href="#{{$table_name}}{{$k}}" aria-expanded="true" aria-controls="{{$table_name}}{{$k}}">
                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                            <?php
                                                            echo $column_data['column_name'];
                                                            unset($column_data['column_name']);
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="{{$table_name}}{{$k}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <td>Changed By</td>
                                                                    <td>Changed At</td>
                                                                    <td>Old Value</td>
                                                                    <td>New Value</td>
                                                                    <td>Link</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($column_data as $row)
                                                                <?php
                                                                if ($table_name == "Eligibility") {
                                                                    $url = route('retirementEligibility', $empId);
                                                                } elseif ($table_name == "MilitaryService") {
                                                                    $url = route('editMilitaryService', [$empId, $row['row_id']]);
                                                                } elseif ($table_name == "NonDeductionService") {
                                                                    $url = route('editNonDeductionService', [$empId, $row['row_id']]);
                                                                } elseif ($table_name == "RefundedService") {
                                                                    $url = route('updateRefundedServiceView', [$empId, $row['row_id']]);
                                                                } else {
                                                                    $url = "#";
                                                                }
                                                                ?>
                                                                <tr>
                                                                    <td><a target="_blank" href="{{ route('editUser', $row['updated_by_user']['id']) }}">{{ $row['updated_by_user']['name'] }}</a></td>
                                                                    <td>{{ date('d/m/Y', strtotime($row['updated_at'])) }}</td>
                                                                    <td>{!! $row['old_val_name'] !!}</td>
                                                                    <td>{!! $row['new_val_name'] !!}</td>
                                                                    <td><a href="{{ $url }}" target="_blank" class="btn btn-sm btn-info"><i class="fa fa-eye"></i></a></td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="5">No Changes updated yet</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- panel-group -->
                                        @endforeach
                                    </div><!-- demo -->
                                </div>
                                <div id="part_time_service_tab" class="tab-pane fade">
                                    <div class="demo">
                                        @foreach($partTime_tab_data as $table_name => $columns)
                                        <h3>{{ $table_name }} Changes Details</h3>
                                        <div class="panel-group" id="accordion{{ $table_name }}" role="tablist" aria-multiselectable="true">
                                            @foreach($columns as $k => $column_data)
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion{{ $table_name }}" href="#{{$table_name}}{{$k}}" aria-expanded="true" aria-controls="{{$table_name}}{{$k}}">
                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                            <?php
                                                            echo $column_data['column_name'];
                                                            unset($column_data['column_name']);
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="{{$table_name}}{{$k}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <td>Changed By</td>
                                                                    <td>Changed At</td>
                                                                    <td>Old Value</td>
                                                                    <td>New Value</td>
                                                                    <td>Link to Service</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($column_data as $row)
                                                                <tr>
                                                                    <td><a target="_blank" href="{{ route('editUser', $row['updated_by_user']['id']) }}">{{ $row['updated_by_user']['name'] }}</a></td>
                                                                    <td>{{ date('d/m/Y', strtotime($row['updated_at'])) }}</td>
                                                                    <td>{!! $row['old_val_name'] !!}</td>
                                                                    <td>{!! $row['new_val_name'] !!}</td>
                                                                    <td><a href="{{ route('EditPartTimeService', [$empId, $row['row_id']]) }}" class="btn btn-sm btn-info" target="_blank"><i class="fa fa-eye"></i></a></td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="5">No Changes updated yet</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- panel-group -->
                                        @endforeach
                                    </div><!-- demo -->
                                </div>
                                <div id="pay_and_leave_tab" class="tab-pane fade">
                                    <div class="demo">
                                        @foreach($pay_and_leave_tab_data as $table_name => $columns)
                                        <h3>{{ $table_name }} Changes Details</h3>
                                        <div class="panel-group" id="accordion{{ $table_name }}" role="tablist" aria-multiselectable="true">
                                            @foreach($columns as $k => $column_data)
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion{{ $table_name }}" href="#{{$table_name}}{{$k}}" aria-expanded="true" aria-controls="{{$table_name}}{{$k}}">
                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                            <?php
                                                            echo $column_data['column_name'];
                                                            unset($column_data['column_name']);
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="{{$table_name}}{{$k}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <td>Changed By</td>
                                                                    <td>Changed At</td>
                                                                    <td>Old Value</td>
                                                                    <td>New Value</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($column_data as $row)
                                                                <tr>
                                                                    <td><a target="_blank" href="{{ route('editUser', $row['updated_by_user']['id']) }}">{{ $row['updated_by_user']['name'] }}</a></td>
                                                                    <td>{{ date('d/m/Y', strtotime($row['updated_at'])) }}</td>
                                                                    <td>{!! $row['old_val_name'] !!}</td>
                                                                    <td>{!! $row['new_val_name'] !!}</td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="5">No Changes updated yet</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- panel-group -->
                                        @endforeach
                                    </div><!-- demo -->
                                </div>
                                <div id="tsp_tab" class="tab-pane fade">
                                    <div class="demo">
                                        @foreach($tsp_tab_data as $table_name => $columns)
                                        <h3>{{ $table_name }} Changes Details</h3>
                                        <div class="panel-group" id="accordion{{ $table_name }}" role="tablist" aria-multiselectable="true">
                                            @foreach($columns as $k => $column_data)
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion{{ $table_name }}" href="#{{$table_name}}{{$k}}tsp" aria-expanded="true" aria-controls="{{$table_name}}{{$k}}tsp">
                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                            <?php
                                                            echo $column_data['column_name'];
                                                            unset($column_data['column_name']);
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="{{$table_name}}{{$k}}tsp" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <td>Changed By</td>
                                                                    <td>Changed At</td>
                                                                    <td>Old Value</td>
                                                                    <td>New Value</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($column_data as $row)
                                                                <tr>
                                                                    <td><a target="_blank" href="{{ route('editUser', $row['updated_by_user']['id']) }}">{{ $row['updated_by_user']['name'] }}</a></td>
                                                                    <td>{{ date('d/m/Y', strtotime($row['updated_at'])) }}</td>
                                                                    <td>{!! $row['old_val_name'] !!}</td>
                                                                    <td>{!! $row['new_val_name'] !!}</td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="5">No Changes updated yet</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- panel-group -->
                                        @endforeach
                                    </div><!-- demo -->
                                </div>
                                <div id="fegli_tab" class="tab-pane fade">
                                    <div class="demo">
                                        @foreach($fegli_tab_data as $table_name => $columns)
                                        <h3>{{ $table_name }} Changes Details</h3>
                                        <div class="panel-group" id="accordion{{ $table_name }}" role="tablist" aria-multiselectable="true">
                                            @foreach($columns as $k => $column_data)
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion{{ $table_name }}" href="#{{$table_name}}{{$k}}" aria-expanded="true" aria-controls="{{$table_name}}{{$k}}">
                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                            <?php
                                                            echo $column_data['column_name'];
                                                            unset($column_data['column_name']);
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="{{$table_name}}{{$k}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <td>Changed By</td>
                                                                    <td>Changed At</td>
                                                                    <td>Old Value</td>
                                                                    <td>New Value</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($column_data as $row)
                                                                <tr>
                                                                    <td><a target="_blank" href="{{ route('editUser', $row['updated_by_user']['id']) }}">{{ $row['updated_by_user']['name'] }}</a></td>
                                                                    <td>{{ date('d/m/Y', strtotime($row['updated_at'])) }}</td>
                                                                    <td>{!! $row['old_val_name'] !!}</td>
                                                                    <td>{!! $row['new_val_name'] !!}</td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="5">No Changes updated yet</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- panel-group -->
                                        @endforeach
                                    </div><!-- demo -->
                                </div>
                                <div id="health_benefits_tab" class="tab-pane fade">
                                    <div class="demo">
                                        @foreach($health_benefits_tab as $table_name => $columns)
                                        <h3>{{ $table_name }} Changes Details</h3>
                                        <div class="panel-group" id="accordion{{ $table_name }}hb" role="tablist" aria-multiselectable="true">
                                            @foreach($columns as $k => $column_data)
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion{{ $table_name }}hb" href="#{{$table_name}}{{$k}}hb" aria-expanded="true" aria-controls="{{$table_name}}{{$k}}hb">
                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                            <?php
                                                            echo $column_data['column_name'];
                                                            unset($column_data['column_name']);
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="{{$table_name}}{{$k}}hb" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <td>Changed By</td>
                                                                    <td>Changed At</td>
                                                                    <td>Old Value</td>
                                                                    <td>New Value</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($column_data as $row)
                                                                <tr>
                                                                    <td><a target="_blank" href="{{ route('editUser', $row['updated_by_user']['id']) }}">{{ $row['updated_by_user']['name'] }}</a></td>
                                                                    <td>{{ date('d/m/Y', strtotime($row['updated_at'])) }}</td>
                                                                    <td>{!! $row['old_val_name'] !!}</td>
                                                                    <td>{!! $row['new_val_name'] !!}</td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="5">No Changes updated yet</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- panel-group -->
                                        @endforeach
                                    </div><!-- demo -->
                                </div>
                                <div id="fltcip_tab" class="tab-pane fade">
                                    <div class="demo">
                                        @foreach($fltcip_tab_data as $table_name => $columns)
                                        <h3>{{ $table_name }} Changes Details</h3>
                                        <div class="panel-group" id="accordion{{ $table_name }}fltcip" role="tablist" aria-multiselectable="true">
                                            @foreach($columns as $k => $column_data)
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion{{ $table_name }}fltcip" href="#{{$table_name}}{{$k}}fltcip" aria-expanded="true" aria-controls="{{$table_name}}{{$k}}fltcip">
                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                            <?php
                                                            echo $column_data['column_name'];
                                                            unset($column_data['column_name']);
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="{{$table_name}}{{$k}}fltcip" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <td>Changed By</td>
                                                                    <td>Changed At</td>
                                                                    <td>Old Value</td>
                                                                    <td>New Value</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($column_data as $row)
                                                                <tr>
                                                                    <td><a target="_blank" href="{{ route('editUser', $row['updated_by_user']['id']) }}">{{ $row['updated_by_user']['name'] }}</a></td>
                                                                    <td>{{ date('d/m/Y', strtotime($row['updated_at'])) }}</td>
                                                                    <td>{!! $row['old_val_name'] !!}</td>
                                                                    <td>{!! $row['new_val_name'] !!}</td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="5">No Changes updated yet</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- panel-group -->
                                        @endforeach
                                    </div><!-- demo -->
                                </div>
                                <div id="social_security_tab" class="tab-pane fade">
                                    <div class="demo">
                                        @foreach($social_security_tab_data as $table_name => $columns)
                                        <h3>{{ $table_name }} Changes Details</h3>
                                        <div class="panel-group" id="accordion{{ $table_name }}ssecurity" role="tablist" aria-multiselectable="true">
                                            @foreach($columns as $k => $column_data)
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion{{ $table_name }}ssecurity" href="#{{$table_name}}{{$k}}ssecurity" aria-expanded="true" aria-controls="{{$table_name}}{{$k}}ssecurity">
                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                            <?php
                                                            echo $column_data['column_name'];
                                                            unset($column_data['column_name']);
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="{{$table_name}}{{$k}}ssecurity" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <td>Changed By</td>
                                                                    <td>Changed At</td>
                                                                    <td>Old Value</td>
                                                                    <td>New Value</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($column_data as $row)
                                                                <tr>
                                                                    <td><a target="_blank" href="{{ route('editUser', $row['updated_by_user']['id']) }}">{{ $row['updated_by_user']['name'] }}</a></td>
                                                                    <td>{{ date('d/m/Y', strtotime($row['updated_at'])) }}</td>
                                                                    <td>{!! $row['old_val_name'] !!}</td>
                                                                    <td>{!! $row['new_val_name'] !!}</td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="5">No Changes updated yet</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- panel-group -->
                                        @endforeach
                                    </div><!-- demo -->
                                </div>
                                <div id="configuration_tab" class="tab-pane fade">
                                    <div class="demo">
                                        @foreach($configuration_tab_data as $table_name => $columns)
                                        <h3>{{ $table_name }} Changes Details</h3>
                                        <div class="panel-group" id="accordion{{ $table_name }}econfiguration" role="tablist" aria-multiselectable="true">
                                            @foreach($columns as $k => $column_data)
                                            <div class="panel panel-default">
                                                <div class="panel-heading" role="tab" id="headingOne">
                                                    <h4 class="panel-title">
                                                        <a role="button" data-toggle="collapse" data-parent="#accordion{{ $table_name }}econfiguration" href="#{{$table_name}}{{$k}}econfiguration" aria-expanded="true" aria-controls="{{$table_name}}{{$k}}econfiguration">
                                                            <i class="more-less glyphicon glyphicon-plus"></i>
                                                            <?php
                                                            echo $column_data['column_name'];
                                                            unset($column_data['column_name']);
                                                            ?>
                                                        </a>
                                                    </h4>
                                                </div>
                                                <div id="{{$table_name}}{{$k}}econfiguration" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                                    <div class="panel-body">
                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <td>Changed By</td>
                                                                    <td>Changed At</td>
                                                                    <td>Old Value</td>
                                                                    <td>New Value</td>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @forelse($column_data as $row)
                                                                <tr>
                                                                    <td><a target="_blank" href="{{ route('editUser', $row['updated_by_user']['id']) }}">{{ $row['updated_by_user']['name'] }}</a></td>
                                                                    <td>{{ date('d/m/Y', strtotime($row['updated_at'])) }}</td>
                                                                    <td>{!! $row['old_val_name'] !!}</td>
                                                                    <td>{!! $row['new_val_name'] !!}</td>
                                                                </tr>
                                                                @empty
                                                                <tr>
                                                                    <td colspan="5">No Changes updated yet</td>
                                                                </tr>
                                                                @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div><!-- panel-group -->
                                        @endforeach
                                    </div><!-- demo -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('scripts')
{!! JsValidator::formRequest('App\Http\Requests\AddMilitaryServiceRequest', '#saveMilitaryServiceForm'); !!}
@endsection
