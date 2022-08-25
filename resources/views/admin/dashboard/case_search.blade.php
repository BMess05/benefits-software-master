<table class="table table-bordered" id="sortTable">
    <thead class="case_list_head">
        <tr>
            <th>Case#</th>
            <th>Employee</th>
            <th>Advisor</th>
            <th>Due Date</th>
            <th>System</th>
            <th>Employee Type</th>
        </tr>
    </thead>
    <tbody>
        @forelse($cases as $case)
        <tr>
            <td><a href="{{url('/employee/basic_info')}}/{{$case->EmployeeId}}">{{$case->EmployeeId}}</a></td>
            <td>{{$case->EmployeeName}}</td>
            <td>{{$case->advisor->AdvisorName}}</td>
            <td>{{date('m/d/Y', strtotime($case->DueDate))}}</td>
            <td>{{ ($case->SystemTypeId) ? (resolve('applookup')->getById($case->SystemTypeId)->AppLookupName) : ''}}</td>
            <td>{{ ($case->EmployeeTypeId) ? (resolve('applookup')->getById($case->EmployeeTypeId)->AppLookupName) : ''}}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6">No Result Found</td>
        </tr>
        @endforelse
    </tbody>
</table>
