@extends('admin.layouts.admin_layout')
@section('style')
<style>
    input.fltcip_conf {
        width: 100%;
    }
</style>
@endsection
@section('content')
@include('admin.layouts.admin_top_menu')
<section class="cases_listing_wrap">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="listing-title">
                    <p class="text-left">Configuration</p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                @include('admin.layouts.messages')
            </div>
            <form action="{{url('configurations/save')}}" id="system_config_form" method="POST">
                @csrf
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="configuration_title">
                                CSRS and FERS
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="csrs_cola">CSRS COLA: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="csrs_cola" value="{{$data['csrs_cola']}}" class="config_input">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="fers_cola">FERS COLA: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="fers_cola" value="{{$data['fers_cola']}}" class="config_input">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="sal_increase">DEFAULT SALARY INCREASE: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="sal_increase_default" value="{{$data['sal_increase_default'] ?? 0}}" class="config_input">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="sal_increase">SALARY INCREASE ({{ date('Y') }}): </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="sal_increase" value="{{$data['sal_increase']}}" class="config_input">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="sal_increase">SALARY INCREASE ({{ date('Y') - 1 }}): </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="sal_increase_1" value="{{$data['sal_increase_1']}}" class="config_input">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="sal_increase">SALARY INCREASE ({{ date('Y') - 2 }}): </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="sal_increase_2" value="{{$data['sal_increase_2']}}" class="config_input">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="configuration_title">
                                Social Security
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="ss_cola">SOCIAL SECURITY COLA: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="ss_cola" value="{{$data['ss_cola']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="pia_formula_fb">PIA Formula First Bend: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="pia_formula_fb" value="{{$data['pia_formula']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="income_limit">Retiree Earned Income Limit: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="income_limit" value="{{$data['income_limit']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>




                <!--  -->

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="configuration_title">
                                FEGLI
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 text-right text-bold pb-16">
                                COST WHILE WORKING (per $1,000)
                            </div>
                            <div class="col-md-8"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-1">`</div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right text-bold pb-16">
                                            At Age:
                                        </div>
                                        <div class="col-md-7 text-bold pb-16">
                                            Basic :
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 text-bold pb-16">Option A:</div>
                            <div class="col-md-2 text-bold pb-16">Option B:</div>
                            <div class="col-md-2 text-bold pb-16">Option C:</div>
                            <div class="col-md-2"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-1"></div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000AgeLessThan35">
                                                < 35 </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000AgeLessThan35" value="{{ $data['WhileWorkingBasicCostPer1000AgeLessThan35'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000Age35To39">35-39 </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000Age35To39" value="{{ $data['WhileWorkingBasicCostPer1000Age35To39'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000Age40To44">40-44 </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000Age40To44" value="{{ $data['WhileWorkingBasicCostPer1000Age40To44'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000Age45To49">45-49 </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000Age45To49" value="{{ $data['WhileWorkingBasicCostPer1000Age45To49'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000Age50To54">50-54 </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000Age50To54" value="{{ $data['WhileWorkingBasicCostPer1000Age50To54'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000Age55To59">55-59 </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000Age55To59" value="{{ $data['WhileWorkingBasicCostPer1000Age55To59'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000Age60To64">60-64 </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000Age60To64" value="{{ $data['WhileWorkingBasicCostPer1000Age60To64'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000Age65To69">65-69 </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000Age65To69" value="{{ $data['WhileWorkingBasicCostPer1000Age65To69'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000Age70To74">70-74 </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000Age70To74" value="{{ $data['WhileWorkingBasicCostPer1000Age70To74'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000Age75To79">75-79 </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000Age75To79" value="{{ $data['WhileWorkingBasicCostPer1000Age75To79'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="WhileWorkingBasicCostPer1000Age80orGreater">80+ </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="WhileWorkingBasicCostPer1000Age80orGreater" value="{{ $data['WhileWorkingBasicCostPer1000Age80orGreater'] }}" class="config_input">
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-2">
                                <!-- While working Option-A starts -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000AgeLessThan35" value="{{ $data['WhileWorkingOptionACostPer1000AgeLessThan35'] }}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000Age35To39" value="{{ $data['WhileWorkingOptionACostPer1000Age35To39'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000Age40To44" value="{{ $data['WhileWorkingOptionACostPer1000Age40To44'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000Age45To49" value="{{ $data['WhileWorkingOptionACostPer1000Age45To49'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000Age50To54" value="{{ $data['WhileWorkingOptionACostPer1000Age50To54'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000Age55To59" value="{{ $data['WhileWorkingOptionACostPer1000Age55To59'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000Age60To64" value="{{ $data['WhileWorkingOptionACostPer1000Age60To64'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000Age65To69" value="{{ $data['WhileWorkingOptionACostPer1000Age65To69'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000Age70To74" value="{{ $data['WhileWorkingOptionACostPer1000Age70To74'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000Age75To79" value="{{ $data['WhileWorkingOptionACostPer1000Age75To79'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionACostPer1000Age80orGreater" value="{{ $data['WhileWorkingOptionACostPer1000Age80orGreater'] }}" class="config_input">
                                        </div>

                                    </div>
                                </div>

                                <!-- While working Option A ends -->
                            </div>
                            <div class="col-md-2">
                                <!-- While working Option-B starts -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000AgeLessThan35" value="{{ $data['WhileWorkingOptionBCostPer1000AgeLessThan35'] }}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000Age35To39" value="{{ $data['WhileWorkingOptionBCostPer1000Age35To39'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000Age40To44" value="{{ $data['WhileWorkingOptionBCostPer1000Age40To44'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000Age45To49" value="{{ $data['WhileWorkingOptionBCostPer1000Age45To49'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000Age50To54" value="{{ $data['WhileWorkingOptionBCostPer1000Age50To54'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000Age55To59" value="{{ $data['WhileWorkingOptionBCostPer1000Age55To59'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000Age60To64" value="{{ $data['WhileWorkingOptionBCostPer1000Age60To64'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000Age65To69" value="{{ $data['WhileWorkingOptionBCostPer1000Age65To69'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000Age70To74" value="{{ $data['WhileWorkingOptionBCostPer1000Age70To74'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000Age75To79" value="{{ $data['WhileWorkingOptionBCostPer1000Age75To79'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionBCostPer1000Age80orGreater" value="{{ $data['WhileWorkingOptionBCostPer1000Age80orGreater'] }}" class="config_input">
                                        </div>

                                    </div>
                                </div>

                                <!-- While working Option B ends -->
                            </div>
                            <div class="col-md-2">
                                <!-- While working Option-C starts -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000AgeLessThan35" value="{{ $data['WhileWorkingOptionCCostPer1000AgeLessThan35'] }}" class="config_input">
                                        </div>
                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000Age35To39" value="{{ $data['WhileWorkingOptionCCostPer1000Age35To39'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000Age40To44" value="{{ $data['WhileWorkingOptionCCostPer1000Age40To44'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000Age45To49" value="{{ $data['WhileWorkingOptionCCostPer1000Age45To49'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000Age50To54" value="{{ $data['WhileWorkingOptionCCostPer1000Age50To54'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000Age55To59" value="{{ $data['WhileWorkingOptionCCostPer1000Age55To59'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000Age60To64" value="{{ $data['WhileWorkingOptionCCostPer1000Age60To64'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000Age65To69" value="{{ $data['WhileWorkingOptionCCostPer1000Age65To69'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000Age70To74" value="{{ $data['WhileWorkingOptionCCostPer1000Age70To74'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000Age75To79" value="{{ $data['WhileWorkingOptionCCostPer1000Age75To79'] }}" class="config_input">
                                        </div>

                                        <div class="form-group">
                                            <input type="text" name="WhileWorkingOptionCCostPer1000Age80orGreater" value="{{ $data['WhileWorkingOptionCCostPer1000Age80orGreater'] }}" class="config_input">
                                        </div>

                                    </div>
                                </div>

                                <!-- While working Option C ends -->
                            </div>
                            <div class="col-md-2"></div>
                        </div>

                        <!-- In Retirement cost configuartion starts -->

                        <hr>
                        <div class="row">
                            <div class="col-md-4 text-right text-bold pb-16">
                                COST IN RETIREMENT (per $1,000)
                            </div>
                            <div class="col-md-8"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 block-border-right">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-4 text-right text-bold pb-16">At Age: </div>
                                        <div class="col-md-8 text-bold pb-16 text-center">
                                            Basic:
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-4">No<br>Reduction</div>
                                                <div class="col-md-4">50%<br>Reduction</div>
                                                <div class="col-md-4">75%<br>Reduction</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 pb-16 text-bold block-border-right text-center">
                                <div class="row">
                                    <div class="col-md-12">
                                        Option A:
                                        <hr>
                                        75%<br>Reduction
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 pb-16 text-bold text-center block-border-right">
                                Option B:
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">Full<br>Reduction</div>
                                    <div class="col-md-6">No<br>Reduction</div>
                                </div>
                            </div>
                            <div class="col-md-3 pb-16 text-bold text-center">
                                Option C:
                                <hr>
                                <div class="row">
                                    <div class="col-md-6">Full<br>Reduction</div>
                                    <div class="col-md-6">No<br>Reduction</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Basic -->
                            <div class="col-md-5 block-border-right">
                                <div class="form-group text-center">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="InRetirementBasicCostPer1000Age50To54NoReduction">
                                                50-54 </label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age50To54NoReduction" value="{{ $data['InRetirementBasicCostPer1000Age50To54NoReduction'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age50To54Reduction50" value="{{ $data['InRetirementBasicCostPer1000Age50To54Reduction50'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age50To54Reduction75" value="{{ $data['InRetirementBasicCostPer1000Age50To54Reduction75'] }}" class="config_input">
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="InRetirementBasicCostPer1000Age55To59NoReduction">
                                                55-59 </label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age55To59NoReduction" value="{{ $data['InRetirementBasicCostPer1000Age55To59NoReduction'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age55To59Reduction50" value="{{ $data['InRetirementBasicCostPer1000Age55To59Reduction50'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age55To59Reduction75" value="{{ $data['InRetirementBasicCostPer1000Age55To59Reduction75'] }}" class="config_input">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="InRetirementBasicCostPer1000Age60To64NoReduction">
                                                60-64 </label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age60To64NoReduction" value="{{ $data['InRetirementBasicCostPer1000Age60To64NoReduction'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age60To64Reduction50" value="{{ $data['InRetirementBasicCostPer1000Age60To64Reduction50'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age60To64Reduction75" value="{{ $data['InRetirementBasicCostPer1000Age60To64Reduction75'] }}" class="config_input">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="InRetirementBasicCostPer1000Age65To69NoReduction">
                                                65-69 </label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age65To69NoReduction" value="{{ $data['InRetirementBasicCostPer1000Age65To69NoReduction'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age65To69Reduction50" value="{{ $data['InRetirementBasicCostPer1000Age65To69Reduction50'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    NA
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="InRetirementBasicCostPer1000Age70To74NoReduction">
                                                70-74 </label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age70To74NoReduction" value="{{ $data['InRetirementBasicCostPer1000Age70To74NoReduction'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age70To74Reduction50" value="{{ $data['InRetirementBasicCostPer1000Age70To74Reduction50'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    NA
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="InRetirementBasicCostPer1000Age75To79NoReduction">
                                                75-79 </label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age75To79NoReduction" value="{{ $data['InRetirementBasicCostPer1000Age75To79NoReduction'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age75To79Reduction50" value="{{ $data['InRetirementBasicCostPer1000Age75To79Reduction50'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    NA
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="form-group text-center">
                                    <div class="row">
                                        <div class="col-md-4 text-right">
                                            <label for="InRetirementBasicCostPer1000Age80NoReduction">
                                                80+ </label>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age80NoReduction" value="{{ $data['InRetirementBasicCostPer1000Age80NoReduction'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="InRetirementBasicCostPer1000Age80Reduction50" value="{{ $data['InRetirementBasicCostPer1000Age80Reduction50'] }}" class="config_input">
                                                </div>
                                                <div class="col-md-4">
                                                    NA
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>

                            <!-- Option A -->
                            <div class="col-md-1 block-border-right">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group text-center">
                                            <input type="text" name="InRetirementOptionACostPer1000Age50To54Reduction75" value="{{ $data['InRetirementOptionACostPer1000Age50To54Reduction75'] }}" class="config_input">
                                        </div>

                                        <div class="form-group text-center">
                                            <input type="text" name="InRetirementOptionACostPer1000Age55To59Reduction75" value="{{ $data['InRetirementOptionACostPer1000Age55To59Reduction75'] }}" class="config_input">
                                        </div>

                                        <div class="form-group text-center">
                                            <input type="text" name="InRetirementOptionACostPer1000Age60To64Reduction75" value="{{ $data['InRetirementOptionACostPer1000Age60To64Reduction75'] }}" class="config_input">
                                        </div>

                                        <div class="form-group text-center">
                                            NA
                                        </div>
                                        <div class="form-group text-center">NA</div>
                                        <div class="form-group text-center">NA</div>
                                        <div class="form-group text-center">NA</div>

                                    </div>
                                </div>
                            </div>

                            <!-- Option B -->
                            <div class="col-md-3 block-border-right">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group text-center">
                                            <input type="text" name="InRetirementOptionBCostPer1000Age50To54FullReduction" value="{{ $data['InRetirementOptionBCostPer1000Age50To54FullReduction'] }}" class="config_input">
                                        </div>

                                        <div class="form-group text-center">
                                            <input type="text" name="InRetirementOptionBCostPer1000Age55To59FullReduction" value="{{ $data['InRetirementOptionBCostPer1000Age55To59FullReduction'] }}" class="config_input">
                                        </div>

                                        <div class="form-group text-center">
                                            <input type="text" name="InRetirementOptionBCostPer1000Age60To64FullReduction" value="{{ $data['InRetirementOptionBCostPer1000Age60To64FullReduction'] }}" class="config_input">
                                        </div>

                                        <div class="form-group text-center">
                                            NA
                                        </div>

                                        <div class="form-group text-center">NA</div>
                                        <div class="form-group text-center">NA</div>
                                        <div class="form-group text-center">NA</div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="col-md-6">
                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionBCostPer1000Age50To54NoReduction" value="{{ $data['InRetirementOptionBCostPer1000Age50To54NoReduction'] }}" class="config_input">
                                            </div>

                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionBCostPer1000Age55To59NoReduction" value="{{ $data['InRetirementOptionBCostPer1000Age55To59NoReduction'] }}" class="config_input">
                                            </div>

                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionBCostPer1000Age60To64NoReduction" value="{{ $data['InRetirementOptionBCostPer1000Age60To64NoReduction'] }}" class="config_input">
                                            </div>

                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionBCostPer1000Age65To69NoReduction" value="{{ $data['InRetirementOptionBCostPer1000Age65To69NoReduction'] }}" class="config_input">
                                            </div>

                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionBCostPer1000Age70To74NoReduction" value="{{ $data['InRetirementOptionBCostPer1000Age70To74NoReduction'] }}" class="config_input">
                                            </div>
                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionBCostPer1000Age75To79NoReduction" value="{{ $data['InRetirementOptionBCostPer1000Age75To79NoReduction'] }}" class="config_input">
                                            </div>
                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionBCostPer1000Age80NoReduction" value="{{ $data['InRetirementOptionBCostPer1000Age80NoReduction'] }}" class="config_input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Option C -->
                            <div class="col-md-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group text-center">
                                            <input type="text" name="InRetirementOptionCCostPer1000Age50To54FullReduction" value="{{ $data['InRetirementOptionCCostPer1000Age50To54FullReduction'] }}" class="config_input">
                                        </div>

                                        <div class="form-group text-center">
                                            <input type="text" name="InRetirementOptionCCostPer1000Age55To59FullReduction" value="{{ $data['InRetirementOptionCCostPer1000Age55To59FullReduction'] }}" class="config_input">
                                        </div>

                                        <div class="form-group text-center">
                                            <input type="text" name="InRetirementOptionCCostPer1000Age60To64FullReduction" value="{{ $data['InRetirementOptionCCostPer1000Age60To64FullReduction'] }}" class="config_input">
                                        </div>

                                        <div class="form-group text-center">
                                            NA
                                        </div>

                                        <div class="form-group text-center">NA</div>
                                        <div class="form-group text-center">NA</div>
                                        <div class="form-group text-center">NA</div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="col-md-6">
                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionCCostPer1000Age50To54NoReduction" value="{{ $data['InRetirementOptionCCostPer1000Age50To54NoReduction'] }}" class="config_input">
                                            </div>

                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionCCostPer1000Age55To59NoReduction" value="{{ $data['InRetirementOptionCCostPer1000Age55To59NoReduction'] }}" class="config_input">
                                            </div>

                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionCCostPer1000Age60To64NoReduction" value="{{ $data['InRetirementOptionCCostPer1000Age60To64NoReduction'] }}" class="config_input">
                                            </div>

                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionCCostPer1000Age65To69NoReduction" value="{{ $data['InRetirementOptionCCostPer1000Age65To69NoReduction'] }}" class="config_input">
                                            </div>

                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionCCostPer1000Age70To74NoReduction" value="{{ $data['InRetirementOptionCCostPer1000Age70To74NoReduction'] }}" class="config_input">
                                            </div>
                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionCCostPer1000Age75To79NoReduction" value="{{ $data['InRetirementOptionCCostPer1000Age75To79NoReduction'] }}" class="config_input">
                                            </div>
                                            <div class="form-group text-center">
                                                <input type="text" name="InRetirementOptionCCostPer1000Age80NoReduction" value="{{ $data['InRetirementOptionCCostPer1000Age80NoReduction'] }}" class="config_input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!--  -->



                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="configuration_title">
                                Fedral Employee Health Benefits
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="avg_prem_increase">Avg. Premimum Increase: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="avg_prem_increase" value="{{$data['avg_premium_inc']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="configuration_title">
                                Thrift Saving Plan
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="deff_limit">Elective Deferral Limit (annual): </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="deff_limit" value="{{$data['deferral_limit']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="catchup_limit">Catch-up Limit (annual): </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="catchup_limit" value="{{$data['catchup_limit']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="year_of_contribution">Year of contributions: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="year_of_contribution" value="{{$data['year_of_contribution']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>




                                <div class="form-group rate_of_return_title">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for=""><br>Total amount allowed for Tranditional and Roth Contributions for year {{ date('Y') }}: </label>
                                        </div>
                                        <div class="col-md-7">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="gfund">For Age less than 50: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="total_allowed_contri_for_age_less_than_50" value="{{$data['total_allowed_contri_for_age_less_than_50']}}" class="config_input"> / pp
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="gfund">For Age 50 or greater: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="total_allowed_contri_for_age_50_or_greater" value="{{$data['total_allowed_contri_for_age_50_or_greater']}}" class="config_input"> / pp
                                        </div>
                                    </div>
                                </div>




                                <div class="form-group rate_of_return_title">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for=""><br>Rates of Return </label>
                                        </div>
                                        <div class="col-md-7">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="gfund">G Fund </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="gfund" value="{{$data['gfund_return']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="ffund">F Fund </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="ffund" value="{{$data['ffund_return']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="cfund">C Fund </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="cfund" value="{{$data['cfund_return']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="sfund">S Fund </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="sfund" value="{{$data['sfund_return']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="ifund">I Fund </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="ifund" value="{{$data['ifund_return']}}" class="config_input">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="configuration_title">
                                FLTCIP Configurations:
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="gfund">Daily benefit amount: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="daily_benefit_amount" value="{{ $data['daily_benefit_amount'] }}" class="fltcip_conf">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="ffund">Benefit period: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="benefit_period" value="{{ $data['benefit_period'] }}" class="fltcip_conf">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="cfund">Waiting period: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="waiting_period" value="{{ $data['waiting_period'] }}" class="fltcip_conf">
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-5 text-right">
                                            <label for="sfund">Inflation protection: </label>
                                        </div>
                                        <div class="col-md-7">
                                            <input type="text" name="inflation_protection" value="{{ $data['inflation_protection'] }}" class="fltcip_conf">
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-4"></div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="configuration_titleisset() ? ">
                                <input type="submit" value="Save" class="btn btn-default btn-sm btns-configurations">
                                <button class="btn btn-default btn-sm btns-configurations">Cancel</button>
                                <button class="btn btn-default btn-sm btns-configurations">Clear cache</button>
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
<script src="{{ URL::asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
{!! JsValidator::formRequest('App\Http\Requests\SystemConfigRequest', '#system_config_form'); !!}
@endsection
