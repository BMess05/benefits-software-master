<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', 'Auth\LoginController@login')->name('admin.login');

Route::get('/', 'Auth\LoginController@viewLogin');
Route::get('/login', function () {
    return view('auth.login');
});
Route::get('logout', 'Auth\LoginController@logout');
Route::group(['middleware' => ['isLogin', 'checkIsActive', 'web']], function () {

    Route::get('/employee/notes/{empid}', 'Admin\EmployeeController@addNotes')->name('addNotes');
    Route::post('/employee/notes/save/{empid}', 'Admin\EmployeeController@saveNotes')->name('saveNotes');


    Route::group(['middleware' => 'adminCheck'], function () {
        Route::get('/disclaimer/delete/{id}', 'Admin\DisclaimerController@delete_disclaimer')->name('admin.disclaimer.delete');
        Route::get('/advisor/delete/{adId}', 'Admin\AdvisorController@delete')->name('admin.advisor.delete');

        Route::get('employee/delete/{id}', 'Admin\EmployeeController@deleteCase')->name('deleteCase');

        Route::get('users', 'Admin\UserController@listStandardUsers')->name('listStandardUsers');
        Route::get('user/add', 'Admin\UserController@addUser')->name('addUser');
        Route::post('user/save', 'Admin\UserController@saveUser')->name('saveUser');

        Route::get('user/edit/{id}', 'Admin\UserController@editUser')->name('editUser');
        Route::post('user/update/{id}', 'Admin\UserController@updateUser')->name('updateUser');

        Route::get('user/delete/{id}', 'Admin\UserController@deleteUser')->name('deleteUser');
        Route::get('employee/history/{id}', 'Admin\EmployeeController@caseHistory')->name('caseHistory');
    });
    Route::get('/dashboard/cases', 'Admin\DashboardController@casesListing')->name('admin.cases');
    Route::any('/cases/search', 'Admin\DashboardController@searchCases')->name('cases.search');

    Route::get('/dashboard/advisors', 'Admin\DashboardController@advisors_listing')->name('admin.advisors');
    Route::get('/dashboard/configurations', 'Admin\DashboardController@configurations')->name('admin.configurations');

    Route::any('configurations/save', 'Admin\DashboardController@saveConfigs');
    Route::get('/advisor/edit/{adId}', 'Admin\AdvisorController@edit')->name('admin.advisor.edit');
    Route::get('/advisor/add', 'Admin\AdvisorController@add_advisor')->name('admin.advisor.add');
    Route::any('/advisor/save', 'Admin\AdvisorController@save')->name('admin.advisor.save');
    Route::get('/advisor/details/{adId}', 'Admin\AdvisorController@getDetails')->name('admin.advisor.details');
    Route::any('/advisor/update/{adId}', 'Admin\AdvisorController@update')->name('admin.advisor.update');

    Route::post('/advisor/change_status', 'Admin\AdvisorController@change_status')->name('admin.advisor.change_status');

    /* Disclaimer Routes */
    Route::get('/disclaimer/edit/{empid}', 'Admin\DisclaimerController@edit_disclaimer')->name('admin.disclaimer.edit');
    Route::any('/disclaimer/update/{empid}', 'Admin\DisclaimerController@update');
    Route::get('/dashboard/disclaimers', 'Admin\DashboardController@disclaimers_listing')->name('admin.disclaimers');
    Route::get('/disclaimer/add', 'Admin\DisclaimerController@add_disclaimer')->name('admin.disclaimer.add');
    Route::any('/disclaimer/save', 'Admin\DisclaimerController@save_disclaimer');

    /* Disclaimer Routes */


    Route::get('/home', 'Admin\DashboardController@casesListing')->name('home');
    Route::get('/employee/files/{empid}', 'Admin\EmployeeController@listFiles')->name('listEmpFiles');

    Route::get('/employee/basic_info/{empid}', 'Admin\EmployeeController@basicInformation')->name('basicInformation');
    Route::get('/employee/add_child/{empid}', 'Admin\EmployeeController@add_child');
    Route::get('/employee/update_child/{childid}', 'Admin\EmployeeController@update_child');
    Route::post('/employee/updateChildData', 'Admin\EmployeeController@update_child_data');
    Route::any('/employee/save_child', 'Admin\EmployeeController@save_child');
    Route::any('/employee/basic_info/update/{empid?}', 'Admin\EmployeeController@updateBasicInfo')->name('basicInfoUpdate');
    Route::any('/employee/save', 'Admin\EmployeeController@saveEmployee')->name('employee.save');

    Route::get('/employee/files/add/{empid}', 'Admin\EmployeeController@add_employeeFile')->name('addEmpFile');
    Route::any('/employee/files/save/{empid}', 'Admin\EmployeeController@saveEmployeeFile')->name('saveEmployeeFile');

    Route::get('/employee/retirementEligibility/{empid}', 'Admin\EmployeeController@retirement_eligibility')->name('retirementEligibility');

    Route::any('/employee/retirementEligibility/update/{empid}', 'Admin\EmployeeController@retirementEligibilityUpdate')->name('retirementEligibilityUpdate');

    Route::get('/employee/militaryService/add/active/{empid}', 'Admin\EmployeeController@addActiveMilitaryService')->name('addActiveMilitaryService');

    Route::get('/employee/militaryService/add/reserve/{empid}', 'Admin\EmployeeController@addReserveMilitaryService')->name('addReserveMilitaryService');

    Route::get('/employee/militaryService/delete/{serviceid}', 'Admin\EmployeeController@deleteMilitaryService')->name('deleteMilitaryService');

    Route::get('/employee/nonDeductionService/delete/{serviceid}', 'Admin\EmployeeController@deleteNonDeductionService')->name('deleteNonDeductionService');

    Route::get('/employee/refundedService/delete/{serviceid}', 'Admin\EmployeeController@deleteRefundedService')->name('deleteRefundedService');

    Route::get('/employee/nonDeductionService/add/{empid}', 'Admin\EmployeeController@addNonDeductionService')->name('addNonDeductionService');

    Route::any('employee/nonDeductionService/save/{empid}', 'Admin\EmployeeController@saveNonDeductionService')->name('saveNonDeductionService');

    Route::get('/employee/nonDeductionService/edit/{empid}/{sid}', 'Admin\EmployeeController@editNonDeductionService')->name('editNonDeductionService');

    Route::any('/employee/nonDeductionService/update/{sid}', 'Admin\EmployeeController@updateNonDeductionService')->name('updateNonDeductionService');

    Route::get('/employee/refundedService/add/{empid}', 'Admin\EmployeeController@addRefundedService')->name('addRefundedService');

    Route::any('/employee/refundedService/save/{empid}', 'Admin\EmployeeController@saveRefundedService')->name('saveRefundedService');

    Route::get('/employee/refundedService/edit/{empid}/{sid}', 'Admin\EmployeeController@updateRefundedServiceView')->name('updateRefundedServiceView');

    Route::get('/employee/militaryService/edit/{empid}/{sid}', 'Admin\EmployeeController@editMilitaryService')->name('editMilitaryService');

    Route::any('/employee/militaryService/update/{sid}', 'Admin\EmployeeController@updateMilitaryService')->name('updateMilitaryService');

    Route::any('/employee/refundedService/update/{empid}', 'Admin\EmployeeController@updateRefundedService')->name('updateRefundedService');

    Route::any('/employee/militaryService/save/{empid}', 'Admin\EmployeeController@saveMilitaryService')->name('saveMilitaryService');

    Route::get('/employee/parttime_service/{empid}', 'Admin\EmployeeController@part_time_service')->name('partTimeService');
    Route::get('/employee/parttime_service/add/{empid}', 'Admin\EmployeeController@add_part_time_service')->name('AddPartTimeService');

    Route::get('/employee/parttime_service/edit/{empId}/{sid}', 'Admin\EmployeeController@edit_part_time_service')->name('EditPartTimeService');

    Route::any('/employee/parttime_service/save/{empid}', 'Admin\EmployeeController@savePartTimeService')->name('savePartTimeService');

    Route::any('/employee/parttime_service/update/{sid}', 'Admin\EmployeeController@updatePartTimeService')->name('updatePartTimeService');

    Route::get('/employee/parttime_service/delete/{sid}', 'Admin\EmployeeController@deletePartTimeService')->name('deletePartTimeService');

    Route::get('/employee/payAndLeave/{empid}', 'Admin\EmployeeController@pay_and_leave')->name('payAndLeave');
    Route::any('/employee/payAndLeave/update/{empid}', 'Admin\EmployeeController@payAndLeaveUpdate')->name('payAndLeaveUpdate');

    Route::get('/employee/tsp/edit/{empid}', 'Admin\EmployeeController@tsp_edit')->name('empTspEdit');

    Route::any('/employee/tsp/update/{empid}', 'Admin\EmployeeController@tsp_update');

    Route::get('/employee/fegli/edit/{empid}', 'Admin\EmployeeController@fegli_edit')->name('empFegliEdit');

    Route::any('/employee/fegli/update/{empid}', 'Admin\EmployeeController@fegli_update');

    Route::get('/employee/fegli/addDependent/{empid}', 'Admin\EmployeeController@add_dependent')->name('addDependent');
    Route::any('/employee/fegli/saveDependent', 'Admin\EmployeeController@save_dependent');

    Route::get('/employee/fegli/editDependent/{id}', 'Admin\EmployeeController@edit_dependent')->name('editDependent');
    Route::any('/employee/fegli/updateDependent/{id}', 'Admin\EmployeeController@update_dependent');

    Route::get('/employee/fegli/deleteDependent/{id}', 'Admin\EmployeeController@delete_dependent')->name('deleteDependent');

    Route::get('/employee/healthBenefits/edit/{empid}', 'Admin\EmployeeController@health_benefits_edit')->name('healthBenefits');

    Route::any('/employee/healthBenefits/update/{empid}', 'Admin\EmployeeController@healthBenefitsUpdate')->name('healthBenefitsUpdate');

    Route::get('/employee/fltcip/edit/{empid}', 'Admin\EmployeeController@fltcip_edit')->name('fltcipEdit');
    Route::any('/employee/fltcip/update/{empid}', 'Admin\EmployeeController@updateFltcip')->name('updateFltcip');
    Route::get('/employee/social_security/edit/{empid}', 'Admin\EmployeeController@social_security_edit')->name('socialSecurityEdit');

    Route::any('/employee/social_security/update/{empid}', 'Admin\EmployeeController@social_security_update')->name('socialSecurityUpdate');

    Route::get('/employee/calculate_and_debug/{empid}/{scenarioid?}', 'Admin\EmployeeController@calculate_and_debug')->name('calcAndDebug');

    Route::get('/employee/configuration/edit/{empid}', 'Admin\EmployeeController@configuration_edit')->name('configurationEdit');
    Route::get('/employee/reportNotes/edit/{empid}', 'Admin\EmployeeController@report_notes_edit')->name('reportNotes');
    Route::get('/employee/employee/add', 'Admin\EmployeeController@add_new_employee')->name('addNewEmployee');
    Route::get('/employee/fegli/report/{empid}', 'Admin\EmployeeController@createPdfFEGLI')->name('createFegliReport');
    Route::any('employee/configurations/save/{empid}', 'Admin\EmployeeController@saveEmployeeConf');
    Route::get('employee/deduction/add/{empid}', 'Admin\EmployeeController@addDeduction');
    Route::any('employee/deduction/save/{empid}', 'Admin\EmployeeController@saveDeduction');
    Route::get('employee/deduction/edit/{empid}/{deductionid}', 'Admin\EmployeeController@editDeduction');
    Route::any('employee/deduction/update/{deductionid}', 'Admin\EmployeeController@updateDeduction');

    Route::get('/images/{file_path}', 'Admin\ImageController')->name('get-file-url');
    Route::get('/employee/file/download/{empid}/{fileName}', 'Admin\EmployeeController@downloadFile');

    Route::get('/employee/retirement/earliest/{empid}/{dob?}', 'Admin\EmployeeController@earliestRetirement');

    Route::get('/employee/retirement/full/{empid}/{dob?}/{leaveSCD?}', 'Admin\EmployeeController@fullRetirement');

    Route::get('/app/lookups/add', 'Admin\AppLookupController@addAppLookups');
    Route::any('/app/lookups/save', 'Admin\AppLookupController@saveAppLookups');

    Route::get('employee/duplicate/{id}', 'Admin\EmployeeController@makeDuplicate')->name('makeDuplicate');
});
Auth::routes();

// DoesNotMeetFiveYear ER: 2062
