@php
$emp_data = resolve('employee')->getEmpInfoById($empId);
@endphp
<div class="listing-title row">
    <p class="col-md-2 text-left">{{resolve('employee')->getEmpNameById($empId)}}</p>
    <p class="col-md-3 text-left">DOB: {{$emp_data['dob']}} {{ $emp_data['current_age'] }}</p>
    <p class="col-md-2 text-left">{{$emp_data['SystemType']}}</p>
    <p class="col-md-3 text-left">Retire: {{$emp_data['retirement_date']}} {{ $emp_data['ret_age'] }}</p>
    <p class="col-md-2 text-left">{{$emp_data['workshop_code'] ?? $emp_data['advisor_name']}}</p>
</div>