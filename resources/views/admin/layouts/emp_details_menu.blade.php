<div class="employee_detail_menu">
    <ul>
        <li class="{{($active_tab == 'files') ? 'active' : ''}}"><a href="{{url('/employee/files')}}/{{$empId}}">Files</a></li>
        <li class="{{($active_tab == 'notes') ? 'active' : ''}}"><a href="{{url('/employee/notes')}}/{{$empId}}">Internal Notes @if($has_notes == 1) <i class="fa fa-dot-circle-o text-red" aria-hidden="true"></i> @endif</a></li>
        <li class="{{($active_tab == 'basic_info') ? 'active' : ''}}"><a href="{{url('/employee/basic_info')}}/{{$empId}}">Basic Information</a></li>
        <li class="{{($active_tab == 'retirement_eligibility') ? 'active' : ''}}"><a href="{{url('/employee/retirementEligibility')}}/{{$empId}}">Retirement Eligibility</a></li>
        <li class="{{($active_tab == 'parttime_service') ? 'active' : ''}}"><a href="{{url('/employee/parttime_service')}}/{{$empId}}">Part Time Service</a></li>
        <li class="{{($active_tab == 'pay_and_leave') ? 'active' : ''}}"><a href="{{url('/employee/payAndLeave')}}/{{$empId}}">Pay and Leave</a></li>
        <li class="{{($active_tab == 'social_security') ? 'active' : ''}}"><a href="{{url('/employee/social_security/edit')}}/{{$empId}}">Social Security</a></li>
        <li class="{{($active_tab == 'fegli') ? 'active' : ''}}"><a href="{{url('/employee/fegli/edit')}}/{{$empId}}">FEGLI</a></li>
        <li class="{{($active_tab == 'health_benifits') ? 'active' : ''}}"><a href="{{url('/employee/healthBenefits/edit')}}/{{$empId}}">Health Benefits</a></li>
        <li class="{{($active_tab == 'fltcip') ? 'active' : ''}}"><a href="{{url('/employee/fltcip/edit')}}/{{$empId}}">FLTCIP</a></li>
        <li class="{{($active_tab == 'tsp') ? 'active' : ''}}"><a href="{{url('/employee/tsp/edit')}}/{{$empId}}">TSP</a></li>
        <li class="{{($active_tab == 'calc_debug') ? 'active' : ''}}"><a href="{{url('/employee/calculate_and_debug')}}/{{$empId}}/{{isset($scenarioid) ? $scenarioid : 1}}">Calculate & Debug</a></li>
        <li class="{{($active_tab == 'config') ? 'active' : ''}}"><a href="{{url('/employee/configuration/edit')}}/{{$empId}}">Configuration</a></li>
        <li class="{{($active_tab == 'download_pdf') ? 'active' : ''}}"><a href="{{url('/employee/fegli/report')}}/{{$empId}}">Download PDF</a></li>
    </ul>
</div>
