<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
/////// UzUautoJobs /////////////

/////// UzUautoJobs /////////////

Route::post('/myTestInfo', 'Inventory\CommissionUserController@test');
// Route::post('/myTestInfo', 'Inventory\CommissionUserController@test');
// Route::post('/getEmployeeVacationInfo', 'EmployeeController@employeeVacationDaysInfo');
Route::post('/getEmployeeVacationInfo', 'EmployeeController@employeeVacationDaysInfo');
Route::post('/warehouse-finder/material-finder', 'SapIntegration\SapController@MaterialFinder');
Route::post('/sap/warehouse-responsible', 'SapIntegration\WarehouseResponsibleController@index');
Route::post('/sap/warehouse-responsible/update', 'SapIntegration\WarehouseResponsibleController@update');
Route::delete('/sap/warehouse-responsible/delete/{id}', 'SapIntegration\WarehouseResponsibleController@destroy');

//////////////////////Employee Dpeartment API
Route::get('/employee-department', 'DocumentController@CreateEmployeeDepartment');
/////////////////////

// Route::get('departments/testDepartmentEmployee/{id}', 'DepartmentController@getChildrenEmployees');
// Route::get('departments/testDepartmentRateEmployee/{id}', 'DepartmentController@getRateCountEmployees');
// Route::get('departments/head-counter', 'DepartmentController@headCounter2');
// Route::get('/nelekvid/send/{id}', 'SapIntegration\SapController@send');
// Route::get('/department-history', 'DepartmentHistoryController@getDepartmentsToHistory');
// Route::post('/checkupdate', 'Inventory\CommissionUserController@changeParol');
/////// UzUautoJobs /////////////
Route::group(['middleware' => ['candidate.uzautojobs']], function () {
    Route::post('/candidate/updateCandidate', 'Uzautojobs\CandidateSubmittedController@updateCandidate');
    Route::post('/candidate/vacancies', 'Uzautojobs\CandidateSubmittedController@setvacancies');
});
/////// Inventory 2024 /////////////
Route::group(['middleware' => ['mobile.inventory']], function () {
    Route::post('/inventory/mobile/eo-product', 'Inventory\ProductController@eoProduct');
    Route::post('/user-check', 'Inventory\CommissionUserController@userCheck');
    Route::post('/user-check1', 'Inventory\CommissionUserController@userCheck1');
    Route::post('/main/get-stock-products', 'Inventory\MainInventoryController@getStockProducs');
    Route::post('/main/set-item-products', 'Inventory\MainInventoryController@setItemIndex');
    Route::post('/inventory/mobile', "Inventory\InventoryController@readFromMobile");
    Route::post('/inventory/mobile/main-inventory-sync', "Inventory\MainInventoryController@mainInventorySync");
    Route::post('/testControl', 'Inventory\InventoryController@testControl');
});
Route::get('/isresponsiple-employee/{id}', 'EmployeeController@isResponsibleEmployee');
/////// UzUautoJobs /////////////
Route::post('/inventory/mobile/eo-product', 'Inventory\ProductController@eoProduct');
/////// Inventory 2024 /////////////

Route::get('/document-report/{menu}/{template_id}', 'DocumentReportController@index');
Route::post('employees/get-employee2', 'EmployeeController@getEmployee');
Route::get('generateWorkingCalendar/{year}', 'WorkCalendarController@generateCalendar');
Route::get('getParentDepartment/{tabel}', 'DepartmentController@getParentDepartment');
Route::get('get-pdi-employee/{tabel}', 'EmployeeController@pdiEmployee');
Route::get('/documents/eimzoinfo/{id}', 'DocumentController@eimzoinfo');
Route::get('get-all-employees/{tabel}', 'EmployeeController@getAllEmployees');
Route::post('generate-pdf-for-sign-document', 'HomeController@generatePdfForSignDocument');
Route::get('changeStaffAndSaveAs400/{id}', 'DocumentSignerController@changeStaffAndSaveAs400');
Route::post('/employees/get-out', 'EmployeeController@getOutEmployee');

Route::post('/tenge/document/create', 'DocumentController@TengeDocumentCreate');
Route::post('/tenge/document/check', 'DocumentController@checkTengeDocumentStatus');
Route::get('/tenge/document/list', 'DocumentController@TengeDocumentsList');

//Discipline Punishment List
Route::get('/discipline-punishment/document/list', 'DisciplinaryPunishmentController@DisciplinePunishmentDocumentsList');


Route::get('/mehmon/guest-info/{passport}', 'DocumentDetailAttributeValueController@getGuestInformation');

Route::get('/employees/birthday', 'EmployeeController@empBirthday');

Route::post('tenge/document-check', 'DocumentController@tengeDocumentCheck');

Route::group(['middleware' => ['auth:api', 'user.log']], function () {

    Route::get('/employees/get-skud', 'EmployeeController@employeeSkud');
    Route::post('/tenge/lsp/create', 'DocumentController@TengeLspCreate');
    Route::get('/diler-registry/{locale}', 'DilerController@dilerRegistry');
    Route::post('/okd-report', 'ReportController@okdReport');
    Route::post('/report-full', 'ReportController@reportFull');
    Route::post('/okd-report-full', 'ReportController@okdReportFull');
    Route::post('/okd-report-full-toshkent', 'ReportToshkentController@okdReportFull');
    Route::post('/okd-report-tab', 'ReportController@okdReportTab');
    Route::post('/okd-report-tab-full', 'ReportController@okdReportTabFull');
    Route::post('/okd-report-item', 'ReportController@OkdReportItem');
    Route::post('/report-item-full', 'ReportController@itemFull');
    Route::post('/report-item-full2', 'ReportController@itemFull2');
    Route::post('/okd-report-item-full-toshkent', 'ReportToshkentController@OkdReportItemFull');
    Route::post('/document-report-employee', 'ReportController@documentReportEmployee');
    Route::post('/document-report-employee-item', 'ReportController@documentReportEmployeeItem');
    Route::post('/document-report-my', 'ReportController@documentReportMy');
    Route::post('/document-report-my-item', 'ReportController@documentReportMyItem');

    //Disciplinary punishment

    Route::post('/discipline-punishment/lsp/create', 'DisciplinaryPunishmentController@DisciplinePunishmentLspCreate');


    /*Uploads Device or Device_History Table data to the Database*/
    Route::get('/uploads', 'TemporaryController@index');

    Route::post('/centrum-upload', 'CentrumController@upload');

    Route::post('/post-order', 'PostOrderController@upload');
    Route::post('/post-order/info', 'PostOrderController@info');
    Route::post('/post-order/view', 'PostOrderController@view');
    Route::post('/post-order/send', 'PostOrderController@send');
    Route::post('/post-order/accept', 'PostOrderController@accept');
    Route::post('/post-order/get-pdf', 'PostOrderController@getPdf');
    Route::post('/post-order/get-pdf-new/{part}', 'PostOrderController@getPdfNew');
    Route::get('/post-order-part', 'PostOrderController@part');
    Route::get('/post-order/{part}', 'PostOrderController@getData');


    Route::get('payment-reestr', 'DocumentController@paymentReestr');
    Route::get('signing-report', 'TestController@signingReport');

    //Dashboard service

    Route::get('dashboard/{locale}', 'HomeController@dashboard');

    Route::get('dashboard-registry/{id}/{locale}', 'HomeController@dashboardRegistry');

    Route::get('vacation-registry/{locale}', 'HomeController@vacationRegistry');
    Route::get('ish-rejimi-registry/{locale}', 'HomeController@IshRejimiRegistry');
    Route::post('ish-rejimi-registry-create', 'DocumentController@ishRejimiRegistryCreate');
    Route::get('otgul-registry/{locale}', 'HomeController@otgulRegistry');
    Route::post('/report/lsp-report', 'DocumentDetailAttributeController@LSPTemplateReport');
    //Tabel uchun mas'ul reyestr qilish
    Route::get('tabel-registry/{locale}', 'HomeController@tabelRegistry');
    Route::post('tabel-registry-create', 'DocumentController@tabelRegistryCreate');

    Route::get('education-registry/{locale}', 'HomeController@educationRegistry');
    Route::get('category-change-registry/{locale}', 'HomeController@categoryChangeRegistry');

    Route::get('business-trip-registry/{locale}', 'HomeController@businessTripRegistry');

    Route::get('/salary-cart/view/{tabel}/{locale}', 'DocumentController@salaryInfo');
    Route::get('/salary-cart/create/{tabel}/{locale}', 'DocumentController@salaryCreate');
    Route::post('vacation-registry-create', 'DocumentController@vacationRegistryCreate');
    Route::get('vacation-registry-delete/{id}', 'DocumentController@vacationRegistrydelete');
    Route::post('otgul-registry-create', 'DocumentController@otgulRegistryCreate');
    Route::post('lsp-registry-create', 'DocumentController@lspRegistryCreate');
    Route::get('otgul-registry-delete/{id}', 'DocumentController@otgulRegistrydelete');
    Route::post('education-registry-create', 'DocumentController@educationRegistryCreate');
    Route::get('education-registry-delete/{id}', 'DocumentController@educationRegistrydelete');
    Route::post('category-change-registry-create', 'DocumentController@categoryChangeRegistryCreate');
    Route::get('category-change-registry-delete/{id}', 'DocumentController@categoryChangeRegistrydelete');
    Route::get('business-trip-delete/{id}', 'DocumentController@businessTripDelete');
    Route::post('business-trip-registry-create', 'DocumentController@businessTripRegistryCreate');
    Route::post('diler-registry-create', 'DilerController@dilerRegistryCreate');
    Route::post('diler-registry-delete/{id}', 'DilerController@dilerRegistryDelete');

    Route::get('work-calendars/{year}', 'WorkCalendarController@index');
    Route::post('work-calendars', 'WorkCalendarController@update');

    //Complaens uchun
    Route::delete('complaens-relatives/delete/{id}', 'ComplaensController@deleteRelative');
    Route::get('/complaens/get-all-questions', 'ComplaensController@getAllQuestions');
    Route::post('/complaens/get-questions', 'ComplaensController@getQuestions');
    Route::post('/complaens/get-excel', 'ComplaensController@getExcel');
    Route::post('/complaens/qrcode', 'ComplaensController@getQrCode');
    Route::post('/complaens/users', 'ComplaensController@complaensUsers');
    Route::post('/complaens/questions/update', 'ComplaensController@addQuestion');
    Route::delete('complaens/questions/delete/{id}', 'ComplaensController@deleteQuestion');
    Route::post('/complaens/resumecapital/update', 'ComplaensController@resumecapitalupdate');


    /*document templates*/
    Route::post('document-templates', 'DocumentTemplateController@index');
    Route::post('document-templates/report/{doc_type_id}', 'DocumentTemplateController@getDocumentTemplates');
    Route::post('document-templates/get-ref', 'DocumentTemplateController@getRef');
    Route::post('document-templates/update', 'DocumentTemplateController@update');
    Route::get('document-templates/edit/{id}/{locale}', 'DocumentTemplateController@edit');
    Route::delete('document-templates/delete/{id}', 'DocumentTemplateController@destroy');
    Route::delete('document-templates/deleteDocumentSignerTemplate/{id}', 'DocumentTemplateController@destroyDocumentSignerTemplate');
    Route::delete('document-templates/deleteDocumentDetailTemplate/{id}', 'DocumentTemplateController@destroyDocumentDetailTemplate');
    Route::delete('document-templates/deleteDocumentDetailAttribute/{id}', 'DocumentTemplateController@destroyDocumentDetailAttribute');
    Route::post('document-templates/get-name', 'DocumentTemplateController@getName');
    Route::get('document-templates/get-favorite/{id}', 'DocumentTemplateController@getDocumentFavorites');
    Route::get('document-templates/get-user-favorite', 'DocumentTemplateController@getUserFavorites');
    Route::post('document-templates/get-name-index', 'DocumentTemplateController@getNameIndex');
    Route::post('document-templates/get-all', 'DocumentTemplateController@getAll');
    Route::post('document-templates/get-list', 'DocumentTemplateController@getList');
    Route::post('document-template/signed-attribute', 'DocumentTemplateController@signedAttribute');
    Route::post('document-templates/get-name-user', 'DocumentTemplateController@getNameForUser');
    Route::post('document-templates/used-vacation-days', 'DocumentTemplateController@usedVacationDays');
    Route::post('document-templates/get-name-type', 'DocumentTemplateController@getNameTypes');

    /*Blank template*/
    Route::post('blank-templates', 'BlankTemplatesController@index');
    Route::post('blank-templates/get-blank', 'BlankTemplatesController@getBlank');
    Route::get('blank-templates/get-blanks', 'BlankTemplatesController@getBlanks');
    Route::post('blank-templates/get-ref/{id}', 'BlankTemplatesController@getRef');
    Route::post('blank-templates/download', 'BlankTemplatesController@download');
    Route::post('blank-templates/delete-file', 'BlankTemplatesController@deleteFile');
    Route::post('blank-templates/update', 'BlankTemplatesController@update');
    Route::post('blank-templates/update-attribute', 'BlankTemplatesController@updateAttribute');
    Route::post('blank-templates/get-file/{id}', 'BlankTemplatesController@getFile');
    Route::post('blank-templates/edit/{id}', 'BlankTemplatesController@edit');
    Route::delete('blank-templates/delete/{id}', 'BlankTemplatesController@destroy');
    Route::delete('blank-templates/delete-attribute/{id}', 'BlankTemplatesController@deleteAttribute');
    Route::get('get-url/{file}', 'BlankTemplatesController@show');

    // Document Blank Template
    Route::post('document-blank-templates/get-form', 'DocumentBlankTemplatesController@getForm');

    // Complaens
    Route::post('complaens/summ', 'ComplaensController@departmentSumm');

    // Table list
    Route::get('directory/list', 'TableListController@index');
    Route::post('table-list', 'TableListController@tableList');
    Route::post('document-table-list', 'TableListController@getTableList');
    Route::post('table_list_employee_staff', 'TableListController@table_list_employee_staff');

    // Kpi
    Route::delete('kpiobjektuser/delete/{id}', 'KpiController@kpiobjektuserdestroy');
    Route::post('kpiobjektuser/update', 'KpiController@kpiobjektuserupdate');
    Route::post('kpisettingdate/update', 'KpiController@kpisettingdateupdate');
    Route::post('kpistatus/update', 'KpiController@kpistatusupdate');
    Route::post('kpiallstatus/update', 'KpiController@kpiallstatusupdate');
    Route::post('kpi/validate-kpi-create', 'KpiController@validateCreateDoc');
    Route::post('kpi/kpiobjektuser', 'KpiController@kpiobjektuser');
    Route::post('kpi/kpi-setting-date', 'KpiController@kpisettingdate');
    Route::post('kpi/send-resolution-employee', 'KpiController@sendResolutionEmployee');
    Route::post('kpi/resolution-employee', 'KpiController@resolutionEmployee');
    Route::post('kpi/resolution-departments', 'KpiController@resolutionDepartments');
    Route::post('kpi/get-attributes', 'KpiController@resolutionAttributesFakts');
    // Route::post('kpi/kpi-getfiles', 'KpiController@kpiGetFiles');
    Route::post('kpi/kpi-getfiles', 'KpiController@kpiGetFilesResolution');
    Route::post('kpi/asistent-comments', 'KpiController@kpiAsistentComments');
    Route::post('kpi/asistant-comment', 'KpiController@asistantComment');
    // iqboljon
    Route::get('kpi/quarter', 'KpiController@quarter');
    Route::post('kpi/otchet', 'KpiController@kpiOtchet');
    // Route::get('kpi/otchet/{quarter}', 'KpiController@kpiOtchet');
    Route::get('kpi/otchet2', 'KpiController@kpiOtchet2');
    Route::get('kpi/otchet3', 'KpiController@kpiOtchet3');
    Route::get('kpi/otchet4', 'KpiController@kpiOtchet4');
    Route::get('kpi/otchet5', 'KpiController@kpiOtchet5');

    Route::post('kpi/validate-kpi', 'KpiController@validateKpiDocument');
    Route::post('kpi/send-kpi-comission', 'KpiController@sendKpiComission');
    Route::get('kpi/validate-document-plan', 'KpiController@validateDocumentPlan');




    // Employee

    Route::post('signer/employee-staff', 'EmployeeStaffController@employeeStaff');
    Route::post('employees/search-employee', 'EmployeeController@searchEmployee');
    Route::post('employees/create-document-transfer', 'EmployeeController@createDocumentTransfer');
    Route::post('staff/search-staff', 'EmployeeController@searchStaff');

    Route::post('employeesView', 'EmployeeController@indexView');
    Route::post('employees/get-excel', 'EmployeeController@getExcel');
    Route::get('employees', 'EmployeeController@index');
    Route::get('employees/show', 'EmployeeController@show');
    Route::delete('employees/delete/{id}', 'EmployeeController@destroy');
    Route::post('employees/update', 'EmployeeController@update');
    Route::post('employees/update-employee-staff', 'EmployeeController@updateEmployeeStaff');
    Route::post('employees/update-employee-history-staff', 'EmployeeController@updateEmployeeHistoryStaff');
    Route::post('employees/update-employee-address', 'EmployeeController@updateEmployeeAddress');
    Route::post('employees/update-employee-coefficient', 'EmployeeController@updateEmployeeCoefficient');
    Route::post('employees/update-employee-phone', 'EmployeeController@updateEmployeePhone');
    Route::delete('employees/delete-address/{id}', 'EmployeeController@deleteAddress');
    Route::delete('employees/delete-employee-staff/{id}', 'EmployeeController@deleteEmployeeStaff');
    Route::delete('employees/delete-coefficient/{id}', 'EmployeeController@deleteEmployeeCoefficient');
    Route::post('employees/get-employee', 'EmployeeController@getEmployee');
    Route::get('get-employee-reports', 'EmployeeController@getEmployeeReport');
    Route::get('employees/show-employee/{id}', 'EmployeeController@showEmployee');
    Route::get('employees/get-avatar/{id}', 'EmployeeController@getAvatar');
    Route::get('employees/get-history/{id}/{locale}', 'EmployeeController@getHistory');
    Route::get('employees/get-ref/{locale}', 'EmployeeController@getRef');
    Route::get('employees/get-pdf/{employee_id}/{locale}', 'EmployeeController@getPdf');
    Route::get('employees/get-chief-employee', 'EmployeeController@getChiefEmployee');
    Route::get('employees/get-chiefs/{employee_id}', 'EmployeeController@getChiefs');
    Route::post('employees/update-chief', 'EmployeeController@updateChiefs');
    Route::delete('employees/delete-chiefs/{id}', 'EmployeeController@deleteChiefs');

    // TariffScale
    Route::post('tariff-scales', 'TariffScaleController@index');
    Route::get('tariff-scales/get-tariff-scales', 'TariffScaleController@getTariffScales');
    Route::get('tariff-scales/show', 'TariffScaleController@show');
    Route::delete('tariff-scales/delete/{id}', 'TariffScaleController@destroy');
    Route::post('tariff-scales/update', 'TariffScaleController@update');

    // Role
    Route::post('roles/update-role-permission', 'RoleController@updateRolePermission');
    Route::get('roles', 'RoleController@index');
    Route::get('roles/get-ref', 'RoleController@getRef');
    Route::get('roles/show', 'RoleController@show');
    Route::delete('roles/delete/{id}', 'RoleController@destroy');
    Route::post('roles/update', 'RoleController@update');

    // Permission
    Route::post('permissions', 'PermissionController@index');
    Route::get('permissions/show', 'PermissionController@show');
    Route::delete('permissions/delete/{id}', 'PermissionController@destroy');
    Route::post('permissions/update', 'PermissionController@update');
    Route::post('check-permission', 'PermissionController@checkPermission');

    // Staff
    Route::get('staffs/staffcoefficient', 'CoefficientController@indexStaffCoefficient');
    Route::post('getStaff', 'StaffController@StaffsIndex');
    Route::post('get-staffs', 'StaffController@search');
    Route::post('staffs', 'StaffController@index');
    Route::post('staffs-new', 'StaffController@indexNew');
    Route::post('staffs-docs', 'StaffController@documents');
    Route::post('staff/update-coefficient', 'StaffController@updateCoefficient');
    Route::post('staff/item-description', 'StaffController@itemDescription');
    Route::post('staff/update-shift', 'StaffController@updateShift');
    Route::post('staffs/get-ref', 'StaffController@getRef');
    Route::get('staffs/get-file-base64/{id}', 'StaffController@getFileBase64');
    Route::get('staffs/show', 'StaffController@show');
    Route::get('staffs/deactivate/{id}', 'StaffController@deactivate');
    Route::post('staffs/update', 'StaffController@update');
    Route::post('staffs/update-requirements', 'StaffController@updateRequitements');
    Route::post('staffs/update-files/{id}', 'StaffController@updateFiles');
    Route::post('files/document-download-log', 'FileController@documentDownloadLog');
    Route::delete('staffs/delete-file/{id}', 'StaffController@deleteFile');
    Route::post('staffs/get-excel', 'StaffController@getExcel');
    Route::post('staffs/attribute', 'StaffController@getAttSigner');
    Route::post('employe-staff/history', 'EmployeeStaffController@getHistory');
    Route::post('employe-staff/full', 'EmployeeStaffController@employeeStaffFull');
    Route::post('employe-staff/vacancies-critical', 'EmployeeStaffController@employeeStaffVacanciesCritical');
    Route::post('employe-staff/save-critical', 'EmployeeStaffController@employeeStaffKritik');
    Route::post('employe-staff/reject-critical', 'EmployeeStaffController@employeeStaffKritikReject');
    Route::post('employe-staff/vacancies', 'EmployeeStaffController@employeeStaffVacancies');
    Route::post('employe-staff/update-min-req', 'EmployeeStaffController@stafMinRec');
    Route::post('change-main-staff', 'EmployeeStaffController@changeMainStaff');
    Route::get('staff-criticals', 'StaffCriticalController@index');
    Route::delete('staff-criticals/delete/{id}', 'StaffCriticalController@destroy');
    Route::get('staff-criticals', 'StaffCriticalController@index');
    Route::get('staff-criticals/get-ref/{locale}', 'StaffCriticalController@getRef');
    Route::get('staff-criticals/show/{id}', 'StaffCriticalController@show');
    Route::post('staff-criticals/update', 'StaffCriticalController@update');
    Route::post('staff-criticals/get-excel', 'StaffCriticalController@getExcel');
    Route::delete('reserves/delete/{id}', 'ReservedEmployeeController@destroy');
    Route::post('reserves/update', 'ReservedEmployeeController@update');
    Route::get('employee-traning-tasks/{id}', 'EmployeeTraningTaskController@index');
    Route::post('employee-traning-tasks/update', 'EmployeeTraningTaskController@update');
    Route::delete('employee-traning-tasks/delete/{id}', 'EmployeeTraningTaskController@destroy');

    // Coefficient
    Route::post('coefficients', 'CoefficientController@index');
    Route::get('coefficients/show', 'CoefficientController@show');
    Route::delete('coefficients/delete/{id}', 'CoefficientController@destroy');
    Route::post('coefficients/update', 'CoefficientController@update');

    // DepartmentType
    Route::post('department-types', 'DepartmentTypeController@index');
    Route::get('department-types/show', 'DepartmentTypeController@show');
    Route::delete('department-types/delete/{id}', 'DepartmentTypeController@destroy');
    Route::post('department-types/update', 'DepartmentTypeController@update');
    Route::post('departments/employeesnew', 'DepartmentController@newProjektEmployees');


    // PositionType
    Route::post('position-types', 'PositionTypeController@index');
    Route::get('position-types/show', 'PositionTypeController@show');
    Route::delete('position-types/delete/{id}', 'PositionTypeController@destroy');
    Route::post('position-types/update', 'PositionTypeController@update');
    Route::get('position-types/stat', 'PositionTypeController@gerStatPositionType');

    // Position
    Route::post('positions', 'PositionController@index');
    Route::get('positions/show', 'PositionController@show');
    Route::delete('positions/delete/{id}', 'PositionController@destroy');
    Route::post('positions/update', 'PositionController@update');

    // PersonalType
    Route::post('personal-types', 'PersonalTypeController@index');
    Route::get('personal-types/show', 'PersonalTypeController@show');
    Route::delete('personal-types/delete/{id}', 'PersonalTypeController@destroy');
    Route::post('personal-types/update', 'PersonalTypeController@update');

    // ExpenceType
    Route::post('expence-types', 'ExpenceTypeController@index');
    Route::get('expence-types/show', 'ExpenceTypeController@show');
    Route::delete('expence-types/delete/{id}', 'ExpenceTypeController@destroy');
    Route::post('expence-types/update', 'ExpenceTypeController@update');

    // RequirementType
    Route::post('requirement-types', 'RequirementTypeController@index');
    Route::get('requirement-types/show', 'RequirementTypeController@show');
    Route::delete('requirement-types/delete/{id}', 'RequirementTypeController@destroy');
    Route::post('requirement-types/update', 'RequirementTypeController@update');


    // Requirement
    Route::post('requirements', 'RequirementController@index');
    Route::get('requirements/show', 'RequirementController@show');
    Route::delete('requirements/delete/{id}', 'RequirementController@destroy');
    Route::post('requirements/update', 'RequirementController@update');

    // User
    Route::get('users', 'UserController@index');
    Route::post('users-filter', 'UserController@indexFilter');
    Route::post('users-deleted', 'UserController@indexDeleted');
    Route::post('employee-search', 'UserController@indexSearch');
    Route::post('user-search', 'UserController@indexSearchUser');
    // Route::get('users/show', 'UserController@show');
    Route::post('users/show', 'UserController@show');
    Route::get('users/permissions', 'UserController@permissions');
    Route::delete('users/delete/{id}', 'UserController@destroy');
    Route::post('users/update', 'UserController@update');
    Route::post('users/eimzo-push', 'UserController@eimzoPush');
    Route::get('users/online', 'UserController@getOnline');
    Route::get('users/report', 'UserController@getUserReport');
    Route::post('users/get-excel', 'UserController@getExcel');
    Route::post('user-role-permission', 'UserRolePermissionController@index');
    Route::post('users-diller-filter', 'UserController@indexFilterDillers');


    //Timeline
    Route::post('timeline/', 'TimelineController@getTimeline');
    Route::get('timeline/get-ref', 'TimelineController@getRef');
    Route::post('timeline/inset-tag', 'TimelineController@insertTag');
    Route::get('timeline/get-count', 'TimelineController@getCount');
    Route::post('timeline/update', 'TimelineController@update');
    Route::post('timeline/like/{id}', 'TimelineController@likeIt');
    Route::delete('timeline/delete/{id}', 'TimelineController@destroy');
    Route::post('timeline/delete-file', 'TimelineController@deleteFile');

    //Companies
    Route::get('/companies', 'CompanyController@index');
    Route::post('/companies/update', 'CompanyController@update');
    Route::delete('/companies/delete/{id}', 'CompanyController@destroy');

    //Departments
    Route::post('departmentsView', 'DepartmentController@indexView');
    Route::post('departmentsGetRef', 'DepartmentController@getRef');
    Route::post('/departments/update', 'DepartmentController@update');
    Route::delete('/departments/delete/{id}', 'DepartmentController@destroy');
    Route::get('/departments', 'DepartmentController@index');
    Route::get('/departments/dep', 'DepartmentController@dep');
    Route::post('/departments-org-chart', 'DepartmentController@orgChart');
    Route::post('/org-chart-new', 'DepartmentController@orgChartNew');
    Route::post('/org-chart-new2', 'DepartmentController@orgChartNew2');
    Route::post('/org-chart-new3', 'DepartmentController@orgChartNew3');
    Route::post('/org-chart-test1', 'DepartmentController@orgCharttest10');
    Route::post('/department-history/getlist', 'DepartmentHistoryController@indexView');
    Route::post('/functional-department', 'FunctionalDepartmentController@index');
    Route::get('/functionaldepartment', 'FunctionalDepartmentController@getFunctionalDepartmet');

    Route::post('/departments-get-documents', 'DepartmentController@getDocuments');
    Route::get('/departments-tree', 'DepartmentController@tree');
    Route::post('/departments/list', 'DepartmentController@getList');
    Route::post('/departments/select', 'DepartmentController@indexSelect');
    Route::post('/departments/get-child-department', 'DepartmentController@getChildDepartment');
    Route::post('/departments/get-department', 'DepartmentController@getDepartment');

    // Joint venture
    Route::post('joint-venture/list', 'DepartmentController@getJointVenture');
    Route::get('department-type-joint-venture/list', 'DepartmentTypeController@departmentTypeJointVenture');
    Route::post('joint-venture/update', 'DepartmentController@jointVentureUpdate');
    Route::delete('joint-venture/delete/{id}', 'DepartmentController@jointVentureDelete');

    // Document types
    Route::post('/document-types/update', 'DocumentTypeController@update');
    Route::post('/document-types/get-name', 'DocumentTypeController@getName');
    Route::delete('/document-types/delete/{id}', 'DocumentTypeController@destroy');
    Route::post('/document-types', 'DocumentTypeController@index');
    Route::get('/document-types', 'DocumentTypeController@getDocumentTypes');



    Route::post('/documents/user-templates', 'DocumentTemplateController@userTemplates');
    Route::get('/documents/status-ready/{id}', 'DocumentController@statusReady');
    Route::post('/kpi-fact-create', 'DocumentController@KpiFactCreate');
    Route::post('/documents/get-attributes', 'KpiController@getAttributesFakts');
    Route::post('/documents/get-attributes-new', 'KpiController@getAttributesFaktsNew');
    Route::post('/documents/get-attributes-plan', 'KpiController@getAttributesFaktsPlan');
    Route::post('/documents/kpi-departments', 'KpiController@kpiDepartments');
    Route::post('/documents/kpi-departments-plan', 'KpiController@kpiDepartmentsPlan');
    Route::post('/documents/kpi-comments', 'KpiController@kpiComments');
    Route::post('/documents/kpi-comments-new', 'KpiController@kpiCommentsNew');
    Route::post('/documents/kpi-comments-plan', 'KpiController@kpiCommentsPlan');
    Route::post('/documents/kpi-getcomments', 'KpiController@kpiGetComments');
    Route::post('/documents/kpi-getfiles', 'KpiController@kpiGetFiles');
    Route::post('/documents/kpi-getfiles-new', 'KpiController@kpiGetFilesNew');
    Route::post('/documents/save-facts', 'KpiController@saveFacts');
    Route::post('/documents/save-facts-new', 'KpiController@saveFactsNew');
    Route::post('/documents/save-reactions', 'KpiController@saveReactions');
    Route::post('/documents/save-reactions-plan', 'KpiController@saveReactionsPlan');
    Route::post('/documents/kpi-updatefiles/{id}', 'KpiController@updateFiles');
    Route::post('/documents/kpi-commentfiles/{id}', 'KpiController@commentFiles');
    Route::post('/kpi-fact-create-new', 'KpiController@KpiFactCreate');
    Route::post('/graphics/get-graphic', 'DocumentController@getGraphic');
    Route::post('/graphics/validate', 'GraphicController@graphicValidate');

    Route::get('/documents/annulirovan/{id}', 'DocumentController@annulirovan');
    Route::get('/documents/add-stamp/{from}/{to}', 'DocumentController@addStamp');
    Route::get('/documents/add-stamp/{id}', 'DocumentController@addStampOne');
    Route::post('/documents/transport-request-completed', 'DocumentSignerController@transportRequestCompleted');
    Route::post('/documents/return-document', 'DocumentController@returnDocument');
    Route::post('/documents/mening-hujjatim-emas', 'DocumentController@meningHujjatimEmas');
    Route::post('/documents/change-reg-data', 'DocumentController@changeRegData');
    Route::post('/documents/star', 'DocumentController@star');
    Route::post('/documents/remove-cancelled-document', 'DocumentController@removeCancelledDocument');
    Route::post('/documents/is-star', 'DocumentController@isStar');
    Route::post('/documents/signed', 'DocumentController@signed');
    Route::post('/documents/filter', 'DocumentController@indexFilter');
    Route::post('documents/get-ref', 'DocumentController@getRef');
    Route::post('/documents/update', 'DocumentController@update');
    Route::post('/documents/get-number/{id}', 'DocumentController@getNumber');
    // Route::get('/documents/eimzoinfo/{id}', 'DocumentController@eimzoinfo');
    Route::post('/documents/publish/{id}', 'DocumentController@publish');
    Route::get('/documents/get-pdf-with-comments/{id}', 'DocumentController@getPdfWithComments');
    Route::delete('/documents/delete/{id}', 'DocumentController@destroy');
    Route::delete('documents/deleteDocumentDetailEmployee/{id}', 'DocumentController@destroyDocumentDetailEmployee');
    Route::delete('documents/deleteDocumentSigner/{id}', 'DocumentController@destroyDocumentSigner');
    Route::post('/documents', 'DocumentController@index');
    Route::get('/documents/lsp-count', 'DocumentController@getLspCount');
    Route::get('/documents/znz-count', 'DocumentController@getZnzCount');
    Route::post('/documents/table-list', 'DocumentController@tableList');
    Route::get('/documents/create/{user_id}', 'DocumentController@create');
    Route::post('documents/show-document', 'DocumentController@showDocument');
    Route::post('documents/show_new-document', 'DocumentController@showDocumentNew');
    Route::post('documents/show_new_test-document', 'DocumentController@showTestDocumentNew');
    Route::post('documents/show-document-signers', 'DocumentController@showDocumentSigners');
    Route::post('documents/show-only-pdf', 'DocumentController@showOnlyPdf');
    // Route::post('documents/show-document-registry', 'DocumentController@showDocumentRegistry');
    Route::get('documents/notification/{locale}', 'DocumentController@getNotification');
    Route::get('documents/notification-new/{locale}', 'DocumentController@getNotificationNew');
    Route::get('documents/signing', 'DocumentController@signing');
    Route::get('documents/get-staff', 'DocumentController@getStaff');
    Route::post('department/get-staffs', 'DepartmentController@getStaff');
    Route::post('department/get-all-staffs', 'DepartmentController@getAllStaff');
    Route::get('documents/list', 'DocumentController@getList');
    Route::get('documents/list-new', 'DocumentController@getListNew');
    Route::get('documents/history/{id}', 'DocumentController@getHistory');
    Route::get('documents/files/{id}', 'DocumentController@getFile');
    Route::post('documents/update-files/{id}', 'DocumentController@updateFile');
    Route::post('documents/comment-files/{id}', 'DocumentController@commentFile');
    Route::post('documents/update-document-relation/{id}', 'DocumentController@updateDocumentRelation');
    Route::post('documents/report', 'DocumentController@getReport');
    Route::get('documents/reports', 'DocumentController@getDocumentReports');
    Route::get('documents/signer-report', 'DocumentController@getDocumentSignerReport');
    Route::post('documents/set-base64', 'DocumentController@setBase64');
    Route::post('documents/delete-relation', 'DocumentController@destroyRelation');
    Route::post('documents/delete-document-staff', 'DocumentController@destroyDocumentStaff');
    Route::delete('documents/delete-files/{id}', 'DocumentController@destroyFile');
    // Route::post('documents/create-pdf-file', 'DocumentController@createPdfFile');
    Route::post('documents/add_signer', 'DocumentController@addSigner');
    Route::post('documents/edit-attribute', 'DocumentController@editAttribute');
    Route::post('documents/get-executor', 'DocumentController@getExecutor');
    Route::post('document/out_of_control', 'DocumentController@outOfControl');
    Route::post('document/out_on_control', 'DocumentController@outOnControl');
    Route::post('document/resolution-employees', 'DocumentController@getResolutionEmployees');

    Route::post('document/create-formal-document/{locale}', 'DocumentController@updateFormalDocument');
    Route::post('document-restore', 'DocumentController@restoreDocument');
    Route::post('document/copy', 'DocumentController@copyDocument');
    Route::post('count-document', 'DocumentController@countDocument');
    Route::post('transfer-document', 'DocumentController@transferDocument');
    Route::post('document-edit-title', 'DocumentController@editDocumentTitle');
    Route::get('document/pre-agreement/{id}', 'DocumentController@preAgreement');

    // ComplaensCencelDocument
    Route::post('complaens-make-document', 'ComplaensCencelDocumentController@makeDocument');
    Route::post('complaens-cencel-documents/add', 'ComplaensCencelDocumentController@add');
    Route::post('complaens-cencel-documents', 'ComplaensCencelDocumentController@getList');
    Route::post('complaens-cencel-documents/update', 'ComplaensCencelDocumentController@update');
    Route::get('complaens-cencel-documents/get-ref', 'ComplaensCencelDocumentController@getRef');
    Route::post('compliance/filter', 'DocumentController@indexFilter');
    Route::post('guest/filter', 'DocumentController@indexFilter');

    //Range
    Route::post('/ranges', 'RangeController@index');
    Route::post('/ranges/update', 'RangeController@update');
    Route::delete('/ranges/delete/{id}', 'RangeController@destroy');


    // Countries
    Route::get('countries', 'CountriesController@index');
    Route::delete('countries/delete/{id}', 'CountriesController@destroy');
    Route::post('countries/update', 'CountriesController@update');

    // District
    Route::get('districts', 'DistrictsController@index');
    Route::delete('districts/delete/{id}', 'DistrictsController@destroy');
    Route::post('districts/update', 'DistrictsController@update');

    // Region
    Route::get('regions', 'RegionController@index');
    Route::delete('regions/delete/{id}', 'RegionController@destroy');
    Route::post('regions/update', 'RegionController@update');

    // Nationality

    Route::get('nationality', 'NationalitiesController@index');
    Route::delete('nationality/delete/{id}', 'NationalitiesController@destroy');
    Route::post('nationality/update', 'NationalitiesController@update');

    /*signers groups */
    Route::post('signers-groups', 'SignerGroupController@index');
    Route::get('signers-groups/get-ref/{locale}', 'SignerGroupController@getRef');
    Route::delete('signers-groups/delete/{id}', 'SignerGroupController@destroy');
    Route::post('signers-groups/update', 'SignerGroupController@update');

    /*signer group detail*/
    Route::post('signers_group_detail', 'SignerGroupController@getRef');
    Route::delete('signers_group_detail/delete/{id}', 'SignerGroupController@destroySignerGroup');
    Route::post('signerGroupDetail/update', 'SignerGroupController@updateSignerGroupDetail');

    /*Object Types*/
    Route::post('object-types', 'ObjectTypeController@index');
    Route::post('object-types/update', 'ObjectTypeController@update');
    Route::delete('object-types/delete/{id}', 'ObjectTypeController@destroy');

    /*Report*/
    Route::get('reports', 'ReportController@index');
    Route::post('as400-to-excel', 'ReportController@As400ToExcel');
    Route::get('reports/{id}', 'ReportController@index');
    Route::post('reports/department', 'ReportController@getDepartment');
    Route::post('reports/department-okd', 'ReportController@getDepartmentOkd');
    Route::get('user-documents/{id}', 'ReportController@getUserDocument');
    Route::post('reports/znz', 'ReportController@getZnzReport');
    Route::post('reports/lsp', 'ReportController@getLspReport');
    Route::post('reports/lsp-excel', 'ReportController@getLspExcel');
    Route::post('unv-report/{id}', 'ReportController@unvReportDocument');
    Route::post('unv-report/download-excel/{id}', 'ReportController@downloadExcel');

    /*Attribute Report*/
    Route::post('attributeReport', 'DocumentDetailAttributeController@templateReport');


    /*Document Signers*/
    Route::post('/document-signers/add-signers', 'DocumentSignerController@addDocumentSigners');
    Route::post('/document-signers/add-rahbar', 'DocumentSignerController@addRahbarSigners');
    Route::post('/document-signers/update', 'DocumentSignerController@update');
    Route::delete('/document-signers/delete-signer/{id}', 'DocumentSignerController@deleteDocumentSigner');
    Route::post('/document-signers/processing', 'DocumentSignerController@processing');
    Route::post('/document-signers/comment', 'DocumentSignerController@comment');
    Route::post('/document-signers/reaction', 'DocumentSignerController@reaction');
    Route::post('/document-signers/pre-agreement', 'DocumentSignerController@preAgreement');
    Route::post('/document-signers/confirmation', 'DocumentSignerController@confirmation');
    Route::post('/document-signers/to-return', 'DocumentSignerController@toReturn');
    Route::delete('/document-signers/delete/{id}', 'DocumentSignerController@destroy');

    /*Not signers*/
    Route::post('notsigner', 'NotsignerController@index');
    Route::get('notsigner/for-template/{id}', 'NotsignerController@getForTemplate');
    Route::post('notsigner/update', 'NotsignerController@update');
    Route::delete('notsigner/delete/{id}', 'NotsignerController@destroy');
    Route::get('notsigner/get-not-signer', 'NotsignerController@getNotSigner');

    /*Document History*/
    Route::post('signer-events', 'DocumentSignerEventController@index');
    Route::post('signer-events/update', 'DocumentSignerEventController@update');
    Route::delete('signer-events/delete/{id}', 'DocumentSignerEventController@destroy');

    /*Document Signers*/
    Route::get('/document-signer-templates/{document_id}', 'DocumentSignerTemplateController@getSigners');

    /*Employee Official Documents*/
    Route::get('official-documents', 'EmployeeOfficialDocumentController@index');
    Route::post('official-documents/update-files/{id}', 'EmployeeOfficialDocumentController@updateFile');
    Route::delete('official-documents/delete-files/{id}', 'EmployeeOfficialDocumentController@deleteFile');
    Route::post('official-documents/update', 'EmployeeOfficialDocumentController@update');
    Route::post('official-documents/update-avatar/{id}', 'EmployeeOfficialDocumentController@updateAvatar');
    Route::delete('official-documents/delete/{id}', 'EmployeeOfficialDocumentController@destroy');


    /*Official Document Types*/
    Route::get('official-document-types', 'OfficialDocumentTypeController@index');

    /*Appeal Contents*/
    Route::get('appeal-contents', 'AppealContentController@index');
    Route::post('appeal-contents/update', 'AppealContentController@update');
    Route::delete('appeal-contents/delete/{id}', 'AppealContentController@destroy');

    /*Organizations*/
    Route::post('organizations', 'OrganizationController@index');
    Route::post('organizations/update', 'OrganizationController@update');
    Route::delete('organizations/delete/{id}', 'OrganizationController@destroy');

    // Action types //
    Route::get('action-type', 'ActionTypeController@index');

    /* Employee phones*/
    Route::get('employee-phones', 'EmployeePhoneController@index');
    Route::post('employee-phones/update', 'EmployeePhoneController@update');
    Route::delete('employee-phones/delete/{id}', 'EmployeePhoneController@destroy');


    /* Purchase Catalogs*/
    Route::post('purchase-catalogs', 'PurchaseCatalogController@index');
    Route::post('purchase-catalogs/update', 'PurchaseCatalogController@update');
    Route::delete('purchase-catalogs/delete/{id}', 'PurchaseCatalogController@destroy');

    /* Unit Measures*/
    Route::post('unit-measures', 'UnitMeasureController@index');
    Route::get('unit-measures/list', 'UnitMeasureController@getMeasure');
    Route::post('unit-measures/update', 'UnitMeasureController@update');
    Route::delete('unit-measures/delete/{id}', 'UnitMeasureController@destroy');

    /* Leaving Reason*/
    Route::get('leaving-reason', 'LeavingReasonController@index');
    Route::post('leaving-reason/update', 'LeavingReasonController@update');
    Route::put('leaving-reason/delete/{id}', 'LeavingReasonController@destroy');
    Route::delete('leaving-reason/delete/{id}', 'LeavingReasonController@destroy');

    Route::post('employee-staff/update', 'EmployeeStaffController@update');
    Route::post('employee-staff/update-employee-history-staff', 'EmployeeStaffController@editStaffHistory');
    Route::delete('employee-staff/delete/{id}', 'EmployeeStaffController@destroy');

    Route::post('inactive-employees', 'EmployeeController@dismissedEmployeeView');

    /* Partners*/
    Route::post('partners', 'PartnersController@index');
    Route::post('partners/update', 'PartnersController@update');
    Route::delete('partners/delete/{id}', 'PartnersController@destroy');

    // Currency
    Route::get('get-currencies', 'CurrencyController@getCurrencyRate');
    Route::post('currencies', 'CurrencyController@index');
    Route::post('currencies/update', 'CurrencyController@update');
    Route::delete('currencies/delete/{id}', 'CurrencyController@destroy');

    // Currency History
    Route::post('currency-histories', 'CurrencyHistoryController@index');
    Route::get('currency-histories/update', 'CurrencyHistoryController@update');

    Route::get('requestdoc', 'RequestdocController@index');
    Route::delete('requestdoc/delete/{id}', 'RequestdocController@destroy');
    Route::post('requestdoc/update', 'RequestdocController@update');

    //Access Department
    Route::post('access-departments', 'AccessDepartmentController@index');
    Route::get('access-departments/get-ref/{locale}', 'AccessDepartmentController@getRef');
    Route::post('access-departments/update', 'AccessDepartmentController@update');
    Route::delete('access-departments/delete/{id}', 'AccessDepartmentController@destroy');

    //Access Types
    Route::get('access-types', 'AccessTypeController@index');
    Route::post('access-types/update', 'AccessTypeController@update');
    Route::delete('access-types/delete/{id}', 'AccessTypeController@destroy');

    //Company Requisites
    Route::post('company-requisites', 'CompanyRequisiteController@index');
    Route::post('company-requisites/update', 'CompanyRequisiteController@update');
    Route::delete('company-requisites/delete/{id}', 'CompanyRequisiteController@destroy');

    //Car Models
    Route::get('car-models', 'CarModelController@index');
    Route::post('car-models/update', 'CarModelController@update');
    Route::delete('car-models/delete/{id}', 'CarModelController@destroy');

    //Mailing
    Route::get('mailing', 'MailingController@index');
    Route::post('mailing/update', 'MailingController@update');
    Route::delete('mailing/delete/{id}', 'MailingController@destroy');
    Route::get('selected-templates', 'SelectedTemplatesController@index');
    Route::post('selected-templates/update', 'SelectedTemplatesController@update');
    Route::delete('selected-templates/delete/{id}', 'SelectedTemplatesController@destroy');
    Route::get('all-templates', 'SelectedTemplatesController@alltemplate');

    // Car Purchases
    Route::post('car-purchases', 'CarPurchaseController@index');
    Route::post('car-purchases/get-excel', 'CarPurchaseController@getExcel');
    Route::get('car-purchases/get-ref/{locale}', 'CarPurchaseController@getRef');
    Route::get('car-purchases/count', 'CarPurchaseController@getCountByCar');
    Route::post('car-purchases/update-file/{file}', 'CarPurchaseController@getRef');
    Route::post('car-purchases/update', 'CarPurchaseController@update');
    Route::delete('car-purchases/delete/{id}', 'CarPurchaseController@destroy');

    Route::get('task-comment', 'EmployeeTraningTaskController@index');
    Route::post('task-comment/update', 'EmployeeTraningTaskCommentController@update');
    Route::delete('task-comment/delete/{id}', 'EmployeeTraningTaskCommentController@destroy');


    // Inventory
    Route::post('inventory', 'InventoryController@index');
    Route::post('inventory/change-status', 'InventoryController@changeStatus');
    Route::get('inventory/report', 'InventoryController@report');
    Route::get('inventory/get-ref', 'InventoryController@getRef');
    Route::post('inventory/get-ref1', 'InventoryController@getRef1');
    Route::post('attaching/get-commission', 'InventoryController@getComission');
    Route::get('inventory/get-part/{part_number}/{warehouse_id}', 'InventoryController@getPart');
    Route::get('inventory/check/{id}', 'InventoryController@check');
    Route::get('inventory/get-address/{id}', 'InventoryController@getAddress');
    Route::get('inventory/get-warehouses/{id}', 'InventoryController@getWarehouses');
    Route::post('inventory/report1', 'InventoryController@report1');
    Route::post('inventory/report2', 'InventoryController@report2');
    Route::post('inventory/attaching', 'InventoryController@attach');
    Route::post('attaching/get-address/{id}', 'InventoryController@attaching');
    Route::get('inventory/checkedReport', 'InventoryController@checkedReport');
    Route::post('inventory/blanks', 'InventoryController@blank');
    Route::post('inventory/blankReport', 'InventoryController@blankReport');

    Route::post('inventory/update', 'InventoryController@update');
    Route::delete('inventory/delete/{id}', 'InventoryController@destroy');

    // Inventory Addresses
    Route::post('inventory-addresses', 'InventoryAddressController@index');
    Route::post('inventory-addresses/update', 'InventoryAddressController@update');
    Route::delete('inventory-addresses/delete/{id}', 'InventoryAddressController@destroy');

    // Inventory Commissions
    Route::post('inventory-commissions', 'InventoryCommissionController@index');
    Route::post('inventory-commissions/update', 'InventoryCommissionController@update');
    Route::delete('inventory-commissions/delete/{id}', 'InventoryCommissionController@destroy');

    // Inventory Product Lists
    Route::post('inventory-products', 'InventoryProductListController@index');
    Route::post('inventory-products/update', 'InventoryProductListController@update');
    Route::delete('inventory-products/delete/{id}', 'InventoryProductListController@destroy');

    // Notification
    Route::post('notifications', 'NotificationController@index');
    Route::post('notifications/update', 'NotificationController@update');
    Route::delete('notifications/delete/{id}', 'NotificationController@destroy');

    // Directory
    Route::post('directories', 'DirectoryController@index');
    Route::get('barn', 'DirectoryController@barn');
    Route::get('yearmonth', 'DirectoryController@yearmonth');
    Route::post('directories/update', 'DirectoryController@update');
    Route::post('directories/add-type', 'DirectoryController@addType');
    Route::delete('directories/delete/{id}', 'DirectoryController@destroy');

    // Family relative
    Route::get('family-relative', 'FamilyRelativeController@index');
    Route::post('family-relative/update', 'FamilyRelativeController@update');
    Route::delete('family-relative/delete/{id}', 'FamilyRelativeController@destroy');



    // Employee Relative
    Route::get('employee-relatives', 'EmployeeRelativeController@index');
    Route::post('employee-relatives/update', 'EmployeeRelativeController@update');
    Route::delete('employee-relatives/delete/{id}', 'EmployeeRelativeController@destroy');

    // for Androit
    Route::post('mobile/document-list', 'DocumentController@indexMobile');

    // document-detail-signer-attributes
    Route::post('document-detail-signer-attributes/edit', 'DocumentDetailSignerAttributeController@edit');

    //document-detail-attributes
    Route::get('document-detail-attributes/{id}', 'DocumentDetailAttributeController@getAttributes');

    // DocumentBlankTemplate
    Route::get('document-blank-templates/edit/{id}', 'DocumentBlankTemplateController@edit');
    Route::post('document-blank/update', 'DocumentBlankTemplateController@update');
    Route::delete('document-blank-template/delete/{id}', 'DocumentBlankTemplateController@destroy');

    Route::post('koreshok', 'KoreshokController@index');

    // Employee Work History
    Route::get('employee-work-history', 'EmployeeWorkHistoryController@index');
    Route::post('employee-work-history/update', 'EmployeeWorkHistoryController@update');
    Route::delete('employee-work-history/delete/{id}', 'EmployeeWorkHistoryController@destroy');

    // Employee Education History
    Route::get('employee-education-history', 'EmployeeEducationHistoryController@index');
    Route::post('employee-education-history/update', 'EmployeeEducationHistoryController@update');
    Route::delete('employee-education-history/delete/{id}', 'EmployeeEducationHistoryController@destroy');


    // HR languages
    Route::get('hr-language', 'HrLanguageController@index');
    Route::post('hr-language/update', 'HrLanguageController@update');
    Route::delete('hr-language/delete/{id}', 'HrLanguageController@destroy');

    // HR parties
    Route::get('hr-party', 'HrPartyController@index');
    Route::post('hr-party/update', 'HrPartyController@update');
    Route::delete('hr-party/delete/{id}', 'HrPartyController@destroy');

    // HR study type
    Route::get('hr-study-type', 'HrStudyTypeController@index');
    Route::post('hr-study-type/update', 'HrStudyTypeController@update');
    Route::delete('hr-study-type/delete/{id}', 'HrStudyTypeController@destroy');

    // Employee Language
    Route::get('employee-languages', 'EmployeeLanguageController@index');
    Route::post('employee-languages/update', 'EmployeeLanguageController@update');
    Route::delete('employee-languages/delete/{id}', 'EmployeeLanguageController@destroy');

    // Employee Parties
    Route::get('employee-parties', 'EmployeePartyController@index');
    Route::post('employee-parties/update', 'EmployeePartyController@update');
    Route::delete('employee-parties/delete/{id}', 'EmployeePartyController@destroy');

    // Employee Military Rank
    Route::get('employee-military-ranks', 'EmployeeMilitaryRankController@index');
    Route::post('employee-military-ranks/update', 'EmployeeMilitaryRankController@update');
    Route::delete('employee-military-ranks/delete/{id}', 'EmployeeMilitaryRankController@destroy');

    // Employee State awards
    Route::get('employee-state-awards', 'EmployeeStateAwardController@index');
    Route::post('employee-state-awards/update', 'EmployeeStateAwardController@update');
    Route::delete('employee-state-awards/delete/{id}', 'EmployeeStateAwardController@destroy');

    // HR Military rank
    Route::get('hr-military-rank', 'HrMilitaryRankController@index');
    Route::post('hr-military-rank/update', 'HrMilitaryRankController@update');
    Route::delete('hr-military-rank/delete/{id}', 'HrMilitaryRankController@destroy');

    // HR State awards
    Route::get('hr-state-awards', 'HrStateAwardController@index');
    Route::post('hr-state-awards/update', 'HrStateAwardController@update');
    Route::delete('hr-state-awards/delete/{id}', 'HrStateAwardController@destroy');

    // HR Universities
    Route::post('hr-universities', 'HrUniversityController@index');
    Route::post('hr-universities/update', 'HrUniversityController@update');
    Route::delete('hr-universities/delete/{id}', 'HrUniversityController@destroy');

    // HR Majors
    Route::post('hr-majors', 'HrMajorController@index');
    Route::post('hr-majors/update', 'HrMajorController@update');
    Route::delete('hr-majors/delete/{id}', 'HrMajorController@destroy');

    // HR Study Degree
    Route::get('hr-study-degrees', 'HrStudyDegreeController@index');
    Route::post('hr-study-degrees/update', 'HrStudyDegreeController@update');
    Route::delete('hr-study-degrees/delete/{id}', 'HrStudyDegreeController@destroy');

    // Sap  Transaction
    Route::post('sap-transactions', 'SapTransactionController@index');
    Route::post('sap-transactions/update', 'SapTransactionController@update');
    Route::delete('sap-transactions/delete/{id}', 'SapTransactionController@destroy');

    // Report Template
    Route::post('report-template/list', 'ReportTemplateController@index');
    Route::get('report-template/edit/{id}', 'ReportTemplateController@edit');
    Route::post('report-template/update', 'ReportTemplateController@update');
    Route::delete('report-template/delete/{id}', 'ReportTemplateController@destroy');

    // Document Control Punkt
    // Route::post('report-template/list', 'ReportTemplateController@index');
    // Route::get('report-template/edit/{id}', 'ReportTemplateController@edit');
    Route::post('control-punkt/update', 'DocumentControlPunktController@update');
    // Route::delete('report-template/delete/{id}', 'ReportTemplateController@destroy');

    // Comments
    // Route::post('report-template/list', 'ReportTemplateController@index');
    Route::post('get-comments', 'CommentController@getComments');
    Route::post('comment/update', 'CommentController@update');
    Route::delete('comment/delete/{id}', 'CommentController@destroy');

    // for EDO mobile
    Route::post('document/resolution-employees-mobile', 'DocumentController@getResolutionEmployeesMobile');
    Route::post('users/show-mobile', 'UserController@showMobile');
    Route::get('dashboard-m/{locale}', 'HomeController@dashboardMobile');
    Route::post('/documents/filter-mobile', 'DocumentController@indexFilterMobile');
    //

    // Attestation Commissions
    Route::get('attestation-commissions', 'CommissionController@index');
    Route::post('attestation-commissions/update', 'CommissionController@update');
    Route::delete('attestation-commissions/delete/{id}', 'CommissionController@destroy');

    // Attestation Question
    Route::get('attestation-questions', 'QuestionController@index');
    Route::post('attestation-questions/update', 'QuestionController@update');
    Route::delete('attestation-questions/delete/{id}', 'QuestionController@destroy');

    // AS400Queries
    Route::get('as400queries', 'AS400QueriesController@index');
    Route::get('as400queries/getQueryList', 'AS400QueriesController@getQueryList');
    Route::get('as400queries/queryName', 'AS400QueriesController@getQueryName');
    Route::post('as400queries/query/array/', 'AS400QueriesController@getValues');
    Route::delete('as400queries/delete/{id}', 'AS400QueriesController@destroy');
    Route::post('as400queries/update', 'AS400QueriesController@update');

    //AS400Permissions
    Route::post('as400query-employee/{id}', 'As400PermissionsController@getEmployees');
    Route::post('as400query-findemployee/{id}', 'As400PermissionsController@findEmployees');
    Route::delete('as400query-deletePermission/{id}', 'As400PermissionsController@destroy');
    Route::post('as400query-attachemployee/{item}/{employee}', 'As400PermissionsController@attachEmployee');

    // AS400DownloadHistory
    Route::get('as400download-history', 'As400DownloadHistoryController@index');
    Route::post('as400download-history/update', 'As400DownloadHistoryController@update');
    Route::post('as400download-history/get-excel', 'As400DownloadHistoryController@getExcel');

    // Organization Texnology Asaka(device_types)
    Route::post('orgtex/device-types', 'DeviceTypeController@index');
    Route::post('orgtex/device-types/update', 'DeviceTypeController@update');
    Route::delete('orgtex/device-types/delete/{id}', 'DeviceTypeController@destroy');
    Route::get('orgtex/device-type/get-ref-branch', 'DeviceTypeController@getRefBranch');

    //Devices
    Route::post('orgtex/devices', 'DeviceController@index');
    Route::get('orgtex/models', 'DeviceController@model');
    Route::post('orgtex/my-devices', 'DeviceHistoryController@myDevices');
    Route::post('orgtex/devices/update', 'DeviceController@update');
    Route::delete('orgtex/devices/delete/{id}', 'DeviceController@destroy');
    Route::get('orgtex/device-type/get-ref/{device_branch_id}', 'DeviceController@getRef');

    //Devices Histories
    Route::post('orgtex/device-histories', 'DeviceHistoryController@index');
    Route::post('orgtex/status', 'DeviceHistoryController@status');
    Route::post('orgtex/device-histories/update', 'DeviceHistoryController@update');
    Route::delete('orgtex/device-histories/delete/{id}', 'DeviceHistoryController@destroy');
    Route::get('employees/get-employee-with-staff/{id}', 'EmployeeController@getEmployeeWithStaff');
    Route::get('orgtex/device-histories/get-ref-device/{device_branch_id}', 'DeviceHistoryController@getRefDevice');

    // Device Barnches
    Route::post('orgtex/device-branches', 'DeviceBranchController@index');
    Route::get('orgtex/device-branches/all', 'DeviceBranchController@all');
    Route::post('orgtex/device-branches/update', 'DeviceBranchController@update');
    Route::delete('orgtex/device-branches/delete/{id}', 'DeviceBranchController@destroy');

    /*Helps*/
    Route::post('helps', 'HelpController@index');
    Route::post('helpComponent', 'HelpController@helpIndex');
    Route::post('helps/update', 'HelpController@update');
    Route::delete('helps/delete/{id}', 'HelpController@destroy');

    // CDPT (competence_types)
    Route::post('cdpt/competence-types', 'CompetenceTypeController@index');
    Route::post('cdpt/competence-types/update', 'CompetenceTypeController@update');
    Route::delete('cdpt/competence-types/delete/{id}', 'CompetenceTypeController@destroy');
    Route::get('cdpt/competence-type/get-ref', 'CompetenceTypeController@getRef');

    // CDPT (specific_skills)
    Route::post('cdpt/specific-skills', 'SpecificSkillController@index');
    Route::post('cdpt/specific-skills/update', 'SpecificSkillController@update');
    Route::delete('cdpt/specific-skills/delete/{id}', 'SpecificSkillController@destroy');

    // CDPT (Department_types)
    Route::post('cdpt/department-types', 'CdptDepartmentTypeController@index');
    Route::post('cdpt/department-types/update', 'CdptDepartmentTypeController@update');
    Route::delete('cdpt/department-types/delete/{id}', 'CdptDepartmentTypeController@destroy');
    Route::get('cdpt/department-type/get-ref', 'CdptDepartmentTypeController@getRef');

    // CDPT (PriorityField)
    Route::post('cdpt/priority-fields', 'PriorityFieldController@index');
    Route::post('cdpt/priority-fields/update', 'PriorityFieldController@update');
    Route::delete('cdpt/priority-fields/delete/{id}', 'PriorityFieldController@destroy');

    //Oybek Attribute Permission
    Route::get('getMainData', 'AttributePermissionController@getMaindata');
    Route::post('templateUser/add', 'AttributePermissionController@add');
    Route::post('template/show', 'AttributePermissionController@index');
    Route::delete('template/delete/{id}', 'AttributePermissionController@destroy');

    // WorkTask
    Route::post('worktask-create', 'WorkTaskController@taskCreate');
    Route::get('worktask-info/{id}', 'WorkTaskAssignmentController@getTaskInfo');
    Route::get('worktask-showinfo/{id}', 'WorkTaskController@showTaskInfo');
    Route::post('worktask-all', 'WorkTaskController@getAll');
    Route::post('worktask-editTask', 'WorkTaskController@taskCreate');
    Route::post('worktask-taskItem', 'WorkTaskController@taskItem');
    Route::post('worktask-action', 'WorkTaskController@updateTaskStatus');
    Route::delete('worktask-delete/{id}', 'WorkTaskController@destroy');
    Route::post('/worktask/updatefiles/{id}', 'WorkTaskController@updateFile');
    Route::get('worktask/file/{id}', 'WorkTaskController@fileDownload');
    Route::delete('/worktask/deletefile/{id}', 'WorkTaskController@deleteFile');
    Route::get('/worktask/employeeTasks', 'WorkTaskAssignmentController@employeeTasks');
    Route::get('/worktask/empInfo', 'WorkTaskAssignmentController@empinfo');

    Route::get('/worktask/getExpired', 'WorkTaskAssignmentController@getExpired');

    Route::get('/worktask-category', 'WorkTaskCategoryController@getAll');
    Route::post('/worktask/category-add', 'WorkTaskCategoryController@save');
    Route::post('/worktask/category-edit', 'WorkTaskCategoryController@edit');
    Route::delete('worktask/category-delete/{id}', 'WorkTaskCategoryController@destroy');

    Route::post('worktask/childTask-create', 'WorkTaskAssignmentController@childTaskCreate');
    Route::delete('worktask/childTask-delete/{id}', 'WorkTaskAssignmentController@destroy');
    Route::get('worktask/childTask-done/{id}', 'WorkTaskAssignmentController@childTaskDone');
    Route::get('worktask/childTaskInfo/{id}', 'WorkTaskAssignmentController@getChildTaskInfo');
    Route::post('worktask/commentAdd', 'WorkTaskAssignmentController@addComment');

    // CDPT (Career_Development_Plans)
    Route::post('cdpt/career-development-plans', 'CareerDevelopmentPlanController@index');
    Route::post('cdpt/career-development-plans/update', 'CareerDevelopmentPlanController@update');
    Route::delete('cdpt/career-development-plans/delete/{id}', 'CareerDevelopmentPlanController@destroy');

    Route::get('skudswod/excel-download/{month}/{tabn}/{type}', 'SkudSwodController@writeExcel');

    //IDES
    Route::post('ides/received', 'IdesController@received');
    Route::post('ides/index', 'IdesController@documentIndex');
    Route::post('ides/index-new', 'IdesController@documentIndexNew');
    Route::get('ides/login', 'IdesController@idesLogin');
    Route::get('ides/show-new/{id}', 'IdesController@showNew');
    Route::post('ides/reaction', 'IdesController@documentReaction');
    Route::post('ides/updatefiles/{id}', 'IdesController@updateFiles');
    Route::get('ides/getCount', 'IdesController@idesCounter');

    Route::post('qrcodeImport', 'QRcodeController@qrcodeImport');

    //Selected templates
    Route::get('selected-templates-for-report', 'SelectedTemplatesController@selectedReportTemplate');

    // Medpunkt Kod Diagnoz
    Route::post('medpunkt/diagnosis-codes', 'DiagnosisCodeController@index');
    Route::post('medpunkt/diagnosis-codes/update', 'DiagnosisCodeController@update');
    Route::delete('medpunkt/diagnosis-codes/delete/{id}', 'DiagnosisCodeController@destroy');

    //MedPunkt Hospital diagnoz
    Route::post('medpunkt/hospital-diagnosis', 'HospitalDiagnosisController@index');
    Route::post('medpunkt/hospital-diagnosis/update', 'HospitalDiagnosisController@update');
    Route::delete('medpunkt/hospital-diagnosis/delete/{id}', 'HospitalDiagnosisController@destroy');
    Route::get('medpunkt/diagnosis-code/get-ref', 'HospitalDiagnosisController@getRef');

    //Medpunkt Reg Balnichniy
    Route::post('medpunkt/registration-period-illness', 'RegistrationPeriodIllnessController@index');
    Route::post('medpunkt/registration-period-illness/update', 'RegistrationPeriodIllnessController@update');
    Route::delete('medpunkt/registration-period-illness/delete/{id}', 'RegistrationPeriodIllnessController@destroy');
    Route::get('employees/get-employee-with-staff/{id}', 'EmployeeController@getEmployeeWithStaff');
    Route::get('medpunkt/registration-period-illness/get-ref-hospital-diagnoses', 'RegistrationPeriodIllnessController@getRefHospitalDiagnoses');
    Route::get('medpunkt/districts/get-ref', 'RegistrationPeriodIllnessController@getRefDistricts');

    // Medpunkt Medicines
    Route::post('medpunkt/medicines', 'MedicineController@index');
    Route::post('medpunkt/medicines/update', 'MedicineController@update');
    Route::delete('medpunkt/medicines/delete/{id}', 'MedicineController@destroy');

    // Medpunkt Ambulance Calls
    Route::post('medpunkt/ambulance-calls', 'AmbulanceCallController@index');
    Route::post('medpunkt/ambulance-calls/update', 'AmbulanceCallController@update');
    Route::delete('medpunkt/ambulance-calls/delete/{id}', 'AmbulanceCallController@destroy');

    // Medpunkt Medical Treatment
    Route::post('medpunkt/medical-treatments', 'MedicalTreatmentController@index');
    Route::post('medpunkt/medical-treatments/update', 'MedicalTreatmentController@update');
    Route::delete('medpunkt/medical-treatments/delete/{id}', 'MedicalTreatmentController@destroy');

    // Medpunkt Medicine Costs
    Route::post('medpunkt/medicine-costs', 'MedicineCostController@index');
    Route::post('medpunkt/medicine-costs/update', 'MedicineCostController@update');
    Route::delete('medpunkt/medicine-costs/delete/{id}', 'MedicineCostController@destroy');
    Route::get('medpunkt/medicine-costs/get-ref', 'MedicineCostController@getRefMedicines');


    //Medpunkt Bemorlarni ro'yhatdan o'tkazish
    Route::post('medpunkt/registration-patients', 'RegistrationPatientController@index');
    Route::post('medpunkt/registration-patients/update', 'RegistrationPatientController@update');
    Route::delete('medpunkt/registration-patients/delete/{id}', 'RegistrationPatientController@destroy');
    Route::post('medpunkt/update-ambulance-calls', 'RegistrationPatientController@updateAmbulanceCall');
    Route::post('medpunkt/update-medical-treatments', 'RegistrationPatientController@updateMedicalTreatment');

    Route::post('medpunkt/diet-foods', 'DietFoodController@index');
    Route::post('medpunkt/diet-foods/update', 'DietFoodController@update');
    Route::delete('medpunkt/diet-foods/delete/{id}', 'DietFoodController@destroy');
    Route::get('employees/get-employee-with-staff/{id}', 'EmployeeController@getEmployeeWithStaff');
    Route::get('medpunkt/diet-foods/get-ref-hospital-diagnoses', 'DietFoodController@getRefHospitalDiagnoses');

    //MedPunkt Reports
    Route::get('medpunkt/report-period-illness', 'RegistrationPeriodIllnessController@reportIllness');
    Route::get('medpunkt/report-period-illness/{month}', 'RegistrationPeriodIllnessController@reportMothIllness');
    Route::get('medpunkt/report/{month}', 'RegistrationPatientController@report');
    Route::post('report/my-reports', 'ReportController@reportMyDocs');
    Route::post('report/my-reports-item', 'ReportController@reportMyDocsItem');

    //ME(manufacture enginering)
    //Status
    Route::post('me/status', 'ME\StatusController@index');
    Route::post('me/status/update', 'ME\StatusController@update');
    Route::delete('me/status/delete/{id}', 'ME\StatusController@destroy');

    //Model
    Route::post('me/models', 'ME\ModelmeController@index');
    Route::post('me/models/update', 'ME\ModelmeController@update');
    Route::delete('me/models/delete/{id}', 'ME\ModelmeController@destroy');

    //TemurMirzo aka shep
    Route::post('find-department-by-user', 'DepartmentController@findDepartmentByUser');

    // Supply transport
    Route::post('supplytransports/all-transportstype', 'Supplytransport\SupplyTransportTypeController@indexTransportsType');
    Route::post('saveupdate/supplytransporttype', 'Supplytransport\SupplyTransportTypeController@saveupdate');
    Route::post('delete/supplytransporttype', 'Supplytransport\SupplyTransportTypeController@destroy');

    Route::post('supplytransports/all-spareparts', 'Supplytransport\SparePartController@indexSpareParts');
    Route::post('saveupdate/sparepart', 'Supplytransport\SparePartController@saveupdate');
    Route::post('delete/sparepart', 'Supplytransport\SparePartController@destroy');

    Route::post('supplytransports/all-vehicles', 'Supplytransport\VehicleController@indexVehicles');
    Route::post('saveupdate/vehicle', 'Supplytransport\VehicleController@saveupdate');
    Route::post('delete/vehicle', 'Supplytransport\VehicleController@destroy');

    Route::post('supplytransports/all-repairplumbers', 'Supplytransport\RepairPlumberController@indexRepairPlumbers');
    Route::post('saveupdate/repairplumber', 'Supplytransport\RepairPlumberController@saveupdate');
    Route::post('delete/repairplumber', 'Supplytransport\RepairPlumberController@destroy');

    Route::post('supplytransports/all-departments', 'Supplytransport\DepartmentController@indexDepartments');
    Route::post('saveupdate/department', 'Supplytransport\DepartmentController@saveupdate');
    Route::post('delete/department', 'Supplytransport\DepartmentController@destroy');


    //linestop
    Route::post('store/ticket', 'Linestop\LineStopController@storeTicket');
    Route::post('linestops/all', 'Linestop\LineStopController@index');
    Route::post('linestops/linestatistics', 'Linestop\LineStopController@getLineStatistics');
    Route::post('linestops/dailyreport', 'Linestop\LinestopTicketController@dailyReport');
    Route::post('linestops/reasonstatistics', 'Linestop\LineStopController@getReasonStatistics');
    Route::get('get/lines', 'Linestop\LineStopController@getlines');

    Route::get('get/lastmonthdata', 'Linestop\LineStopController@getLastMonthData');

    Route::get('get/reasons', 'Linestop\LineStopController@getreasons');
    Route::post('get/all-reasons', 'Linestop\LineStopController@indexReasons');
    Route::post('saveupdate/reason', 'Linestop\LineStopController@saveupdate');
    Route::post('delete/reason', 'Linestop\LineStopController@destroy');

    Route::post('get/all-departments', 'Linestop\LineStopController@indexDepartments');
    Route::post('saveupdate/department', 'Linestop\LineStopController@saveupdatedepartment');
    Route::post('delete/department', 'Linestop\LineStopController@destroydepartment');

    Route::post('get/all-providers', 'Linestop\LineStopController@indexProviders');
    Route::post('saveupdate/provider', 'Linestop\LineStopController@saveupdateprovider');
    Route::delete('delete/provider/{id}', 'Linestop\LineStopController@destroyprovider');
    Route::get('linestop-get/providers', 'Linestop\LineStopController@getproviders');

    Route::post('get/all-productmodels', 'Linestop\LineStopController@indexProductmodels');
    Route::post('productmodel/saveupdate', 'Linestop\LineStopController@saveupdateproductmodel');
    Route::delete('productmodel/delete/{id}', 'Linestop\LineStopController@destroyproductmodel');
    Route::get('linestop-get/productmodels', 'Linestop\LineStopController@getproductmodels');

    Route::post('get/all-lines', 'Linestop\LineStopLineController@indexLines');
    Route::post('saveupdate/line', 'Linestop\LineStopLineController@saveupdate');
    Route::post('delete/line', 'Linestop\LineStopLineController@destroy');
    Route::post('get/all-shops', 'Linestop\LineStopLineController@indexShops');
    Route::post('saveupdate/shop', 'Linestop\LineStopShopController@saveupdate');
    Route::delete('delete/shop/{id}', 'Linestop\LineStopShopController@destroy');

    Route::post('linestop/allautobottickets', 'Linestop\LinestopTicketController@getAllTicketsWithPlcdata');
    Route::post('linestop/allautobotticketsexcel', 'Linestop\LinestopTicketController@getAllTicketsWithPlcdataExcel');

    Route::post('linestop/alltickets', 'Linestop\LinestopTicketController@getAllTickets');
    Route::post('linestop/allticketsexcel', 'Linestop\LinestopTicketController@getAllTicketsExcel');

    Route::post('linestop/allopentickets', 'Linestop\LinestopTicketController@getAllOpenTickets');
    Route::post('linestop/allopenticketsexcel', 'Linestop\LinestopTicketController@getAllOpenTicketsExcel');

    Route::post('linestop/getDepartments', 'Linestop\LinestopTicketController@getDepartments');
    Route::post('linestop/singleDateTicket', 'Linestop\LinestopTicketController@getTicketsBySelectedDate');
    Route::post('linestop/getticket', 'Linestop\LinestopTicketController@getTicket');
    Route::post('linestop/getTicketViewers', 'Linestop\LinestopTicketController@getTicketViewers');
    Route::post('/linestop/get-ticketusers', 'Linestop\LinestopTicketController@getTicketUser');
    Route::post('/linestop/createTicket', 'Linestop\LinestopTicketController@createTicket');
    Route::post('/linestop/acceptTicket', 'Linestop\LinestopTicketController@acceptTicket');
    Route::post('linestop/receive', 'Linestop\LineStopController@websocketApi');

    Route::post('/linestop/updateTicket', 'Linestop\LinestopTicketController@updateTicket');
    Route::post('/linestop/closeTicket', 'Linestop\LinestopTicketController@closeTicket');

    Route::post('/linestop/sendFileComment', 'Linestop\LinestopTicketController@sendFileComment');
    Route::get('/linestop/download-ticfile/{filename}', 'Linestop\LinestopTicketController@ticketDownload');

    Route::post('/linestop/allmylinestops', 'Linestop\LinestopTicketController@AllMyLinestops');


    Route::post('saveupdate/about-company', 'AboutCompanyController@saveupdate');
    Route::post('delete/about-company', 'AboutCompanyController@destroy');


    Route::post('mainpage/getdata', 'Linestop\LineStopController@getDataMainpage');
    // Route::post('attributeReport', 'DocumentDetailAttributeController@templateReport');

    //---------------Clothing Order --------------------------
    // Department Responsible
    Route::post('/clothing-orders/department-responsibles', 'ClothingOrder\DepartmentResponsibleController@index');
    Route::post('/clothing-orders/department-responsibles/update', 'ClothingOrder\DepartmentResponsibleController@update');
    Route::delete('/clothing-orders/department-responsibles/delete/{id}', 'ClothingOrder\DepartmentResponsibleController@delete');

    // Clothing Order
    Route::post('clothing-order/orders', 'ClothingOrder\OrderController@index');
    Route::post('clothing-order/orders/get-ref', 'ClothingOrder\OrderController@getRef');
    Route::post('clothing-order/orders/update', 'ClothingOrder\OrderController@update');
    Route::delete('clothing-order/orders/delete/{id}', 'ClothingOrder\OrderController@destroy');
    Route::post('clothing-order/orders/update-detail', 'ClothingOrder\OrderController@updateDetail');
    Route::delete('clothing-order/orders/delete-detail/{id}', 'ClothingOrder\OrderController@destroyDetail');

    Route::post('/clothing-orders/clo-products', 'ClothingOrder\ProductController@index');
    Route::post('/clothing-orders/clo-products/update', 'ClothingOrder\ProductController@update');
    Route::delete('/clothing-orders/clo-products/delete/{id}', 'ClothingOrder\ProductController@delete');

    Route::post('clothing-order/sizes', 'ClothingOrder\SizeController@index');
    Route::post('clothing-order/sizes/update', 'ClothingOrder\SizeController@update');
    Route::post('clothing-order/products/save-size', 'ClothingOrder\ProductController@saveSize');
    Route::post('clothing-order/products/delete-size/{id}', 'ClothingOrder\ProductController@deleteSize');
    Route::delete('clothing-order/sizes/delete/{id}', 'ClothingOrder\SizeController@delete');

    Route::post('/clothing-order/employee-clothes', 'ClothingOrder\EmployeeClotheController@index');
    Route::get('/clothing-order/employee-clothes/get-ref', 'ClothingOrder\EmployeeClotheController@getRef');
    Route::post('/clothing-order/employee-clothes/update', 'ClothingOrder\EmployeeClotheController@update');
    Route::delete('/clothing-order/employee-clothes/delete/{id}', 'ClothingOrder\EmployeeClotheController@delete');

    //---------------Clothing Order --------------------------

    //EDI
    Route::post('edi/orders', 'EDI\OrderController@index');
    Route::post('edi/orders/get-ref', 'EDI\OrderController@getRef');
    Route::post('edi/orders/update', 'EDI\OrderController@update');
    Route::delete('edi/orders/delete/{id}', 'EDI\OrderController@destroy');
    Route::post('edi/orders/update-detail', 'EDI\OrderController@updateDetail');
    Route::delete('edi/orders/delete-detail/{id}', 'EDI\OrderController@destroyDetail');

    Route::post('edi/stock-parameters', 'EDI\StockParameterController@index');
    Route::post('edi/stock-parameters/get-ref', 'EDI\StockParameterController@getRef');
    Route::post('edi/stock-parameters/update', 'EDI\StockParameterController@update');
    Route::delete('edi/stock-parameters/delete/{id}', 'EDI\StockParameterController@destroy');

    Route::post('edi/materials', 'EDI\MaterialController@index');
    Route::post('edi/materials/get-ref', 'EDI\MaterialController@getRef');
    Route::post('edi/materials/update', 'EDI\MaterialController@update');
    Route::post('edi/materials/file-upload/{id}', 'EDI\MaterialController@fileUpload');
    Route::delete('edi/materials/delete/{id}', 'EDI\MaterialController@destroy');

    Route::post('edi/currencies', 'EDI\CurrencyController@index');
    Route::post('edi/currencies/update', 'EDI\CurrencyController@update');
    Route::delete('edi/currencies/delete/{id}', 'EDI\CurrencyController@destroy');

    Route::post('edi/warehouses', 'EDI\WarehouseController@index');
    Route::post('edi/warehouses/update', 'EDI\WarehouseController@update');
    Route::delete('edi/warehouses/delete/{id}', 'EDI\WarehouseController@destroy');

    Route::post('edi/material-follow-ups', 'EDI\MaterialFollowUpController@index');
    Route::post('edi/material-follow-ups/update', 'EDI\MaterialFollowUpController@update');
    Route::delete('edi/material-follow-ups/delete/{id}', 'EDI\MaterialFollowUpController@destroy');

    Route::post('edi/unit-measures', 'EDI\UnitMeasureController@index');
    Route::post('edi/unit-measures/update', 'EDI\UnitMeasureController@update');
    Route::delete('edi/unit-measures/delete/{id}', 'EDI\UnitMeasureController@destroy');

    Route::post('edi/shipment-types', 'EDI\ShipmentTypeController@index');
    Route::post('edi/shipment-types/update', 'EDI\ShipmentTypeController@update');
    Route::delete('edi/shipment-types/delete/{id}', 'EDI\ShipmentTypeController@destroy');

    Route::post('edi/material-types', 'EDI\MaterialTypeController@index');
    Route::post('edi/material-types/update', 'EDI\MaterialTypeController@update');
    Route::delete('edi/material-types/delete/{id}', 'EDI\MaterialTypeController@destroy');

    Route::post('edi/material-groups', 'EDI\MaterialGroupController@index');
    Route::post('edi/material-groups/update', 'EDI\MaterialGroupController@update');
    Route::delete('edi/material-groups/delete/{id}', 'EDI\MaterialGroupController@destroy');

    Route::post('edi/business-partner-types', 'EDI\BusinessPartnerTypeController@index');
    Route::post('edi/business-partner-types/update', 'EDI\BusinessPartnerTypeController@update');
    Route::delete('edi/business-partner-types/delete/{id}', 'EDI\BusinessPartnerTypeController@destroy');

    Route::post('edi/business-partners', 'EDI\BusinessPartnerController@index');
    Route::post('edi/business-partners/get-ref', 'EDI\BusinessPartnerController@getRef');
    Route::post('edi/business-partners/update', 'EDI\BusinessPartnerController@update');
    Route::delete('edi/business-partners/delete/{id}', 'EDI\BusinessPartnerController@destroy');

    Route::post('edi/contracts', 'EDI\ContractController@index');
    Route::post('edi/contracts/get-ref', 'EDI\ContractController@getRef');
    Route::post('edi/contracts/update', 'EDI\ContractController@update');
    Route::delete('edi/contracts/delete/{id}', 'EDI\ContractController@destroy');
    Route::post('edi/contracts/update-detail', 'EDI\ContractController@updateDetail');
    Route::delete('edi/contracts/delete-detail/{id}', 'EDI\ContractController@destroyDetail');

    Route::post('edi/asns', 'EDI\AsnController@index');
    Route::post('edi/asns/get-ref', 'EDI\AsnController@getRef');
    Route::post('edi/asns/update', 'EDI\AsnController@update');
    Route::delete('edi/asns/delete/{id}', 'EDI\AsnController@destroy');
    Route::post('edi/asns/update-detail', 'EDI\AsnController@updateDetail');
    Route::delete('edi/asns/delete-detail/{id}', 'EDI\AsnController@destroyDetail');


    Route::post('edi/user-business-partner', 'EDI\UserBusinessPartnerController@index');
    Route::post('edi/user-business-partner/get-ref', 'EDI\UserBusinessPartnerController@getRef');
    Route::post('edi/user-business-partner/update', 'EDI\UserBusinessPartnerController@update');
    Route::delete('edi/user-business-partner/delete/{id}', 'EDI\UserBusinessPartnerController@destroy');
    Route::post('edi/user-business-partner/update-detail', 'EDI\UserBusinessPartnerController@updateDetail');
    Route::delete('edi/user-business-partner/delete-detail/{id}', 'EDI\UserBusinessPartnerController@destroyDetail');
    //EDI

    //SAP
    Route::post('/sap/get-nolekvid-report', 'SapIntegration\SapController@nolekvidReport');
    Route::post('/warehouse-finder/send', 'SapIntegration\SapController@warehouseFinder');
    Route::get('/warehouse-finder/getWorkplaces', 'SapIntegration\SapController@getWorkplaces');
    Route::post('/warehouse-finder/getWarehouses', 'SapIntegration\SapController@getWarehouses');
    Route::post('sap/find-parts', 'FindPartsController@index');
    Route::post('sap/container-info', 'FindPartsController@getContainer');
    Route::post('sap/material-responsible/create', 'DocumentController@MaterialResponsibleDocumentCreate');

    //Pg_Inventory
    Route::post('pg_inventory/wh', 'Inventory\WarehouseController@index');
    Route::post('pg_inventory/getwh', 'Inventory\WarehouseController@getwh');
    Route::post('pg_inventory/adress', 'Inventory\AdressController@index');
    Route::post('pg_inventory/product', 'Inventory\ProductController@index');
    Route::post('pg_inventory/quarterindex', 'Inventory\QuarterController@index');
    Route::post('pg_inventory/quarter', 'Inventory\QuarterController@quarter');
    Route::post('pg_inventory/wh/update', 'Inventory\WarehouseController@update');
    Route::post('pg_inventory/adress/update', 'Inventory\AdressController@update');
    Route::post('pg_inventory/product/update', 'Inventory\ProductController@update');
    Route::post('pg_inventory/quarter/update', 'Inventory\QuarterController@update');

    Route::delete('pg_inventory/wh/delete/{id}', 'Inventory\WarehouseController@destroy');
    Route::delete('pg_inventory/adress/delete/{id}', 'Inventory\AdressController@destroy');
    Route::delete('pg_inventory/quarter/delete/{id}', 'Inventory\QuarterController@destroy');
    Route::delete('pg_inventory/product/delete/{id}', 'Inventory\ProductController@destroy');
    Route::post('get-info', 'Inventory\InventoryController@getInfo');
    Route::post('user-password-change', 'Inventory\CommissionUserController@changePassword');
    Route::post('get-info/get-excel', 'Inventory\InventoryController@getExcel');
    Route::post('commissionUser/get-info', 'Inventory\CommissionUserController@getUser');
    Route::post('commissionUser/create', 'Inventory\CommissionUserController@createUser');
    Route::post('commissionUser/update-manual', 'Inventory\CommissionUserController@changeManual');
    Route::post('commissionUser/update', 'Inventory\CommissionUserController@UpdateUser');
    Route::post('commissionUser/cheack-user', 'Inventory\CommissionUserController@cheackUser');

    Route::post('pg_inventory/commission', 'Inventory\CommissionController@index');
    Route::post('pg_inventory/commission/update', 'Inventory\CommissionController@update');
    Route::delete('pg_inventory/commission/delete/{id}', 'Inventory\CommissionController@destroy');

    Route::post('pg_inventory/address-product', 'Inventory\AddressProductController@index');
    Route::get('pg_inventory/get-status', 'Inventory\InventoryReportController@getStatus');
    Route::get('pg_inventory/get-status-blanka', 'Inventory\InventoryReportController@getReportBlanka');
    Route::post('pg_inventory/get-location-report', 'Inventory\InventoryReportController@getLocationReport');
    Route::post('pg_inventory/get-partnumber-report', 'Inventory\InventoryReportController@getPartnumberReport');
    Route::post('pg_inventory/change-status', 'Inventory\InventoryReportController@changeStatus');
    //Pg_Inventory
    /////// UzUautoJobs /////////////
    Route::post('/candidate/setCandidate', 'Uzautojobs\CandidateSubmittedController@setCandidate');
    Route::post('/candidates', 'Uzautojobs\CandidateController@setCandidate');
    Route::post('/matching-candidates', 'Uzautojobs\CandidateController@setMatchingCandidate');
    Route::post('/candidates-index', 'Uzautojobs\CandidateController@CandidateIndex');
    Route::post('/matching-index', 'Uzautojobs\CandidateController@MatchingIndex');
    Route::post('/candidate/reject', 'Uzautojobs\CandidateController@rejectCandidate');
    Route::post('/candidate/set-reject', 'Uzautojobs\CandidateController@rejectListAdd');
    Route::post('/candidate/set-information', 'Uzautojobs\CandidateController@setInformation');
    Route::get('/candidates/document/list', 'Uzautojobs\CandidateController@CandidatesDocumentsList');
    Route::post('/candidates/document/create', 'Uzautojobs\CandidateController@CandidatesDocumentsCreate');
    Route::post('/candidates/order/create', 'Uzautojobs\CandidateController@CandidatesOrderCreate');
    Route::post('/candidates/contract/create', 'Uzautojobs\CandidateController@CandidatesСontractCreate');
    /////// UzUautoJobs /////////////
    //////  Moddiy Javobgarlik///////
    Route::post('material-responsible-people', 'MaterialResponsiblePeopleController@index');
    Route::post('edi/business-partners/get-ref', 'EDI\BusinessPartnerController@getRef');
    Route::post('material-responsible-people/update', 'MaterialResponsiblePeopleController@update');
    Route::delete('material-responsible-people/delete/{id}', 'MaterialResponsiblePeopleController@destroy');


    //////  Xarbiy xizmatga olish///////
    Route::post('employee-military', 'EmployeeMilitaryController@index');
    Route::post('employee-military/get-ref', 'EmployeeMilitaryController@getRef');
    Route::post('employee-military/update', 'EmployeeMilitaryController@update');
    Route::delete('employee-military/delete/{id}', 'EmployeeMilitaryController@destroy');



    // ---------------------- Components ---------------------------------
    Route::post('components/users', 'ComponentController@users');
    Route::post('components/departments', 'ComponentController@departments');
    Route::post('components/employees', 'ComponentController@employees');
    Route::post('components/products', 'ComponentController@products');
    // ---------------------- Components ---------------------------------

    Route::post('user-logs', 'UserLogController@index');
    Route::post('/document-download-log', 'DocumentDownloadLogController@index');


    Route::get('chat-users', 'Chat\ChatController@chatUsers');
});

Route::get('/edi/asns/print/{id}', 'EDI\AsnController@print');
Route::post('get/about-company', 'AboutCompanyController@aboutCompany');

Route::post('find-department-by-user', 'DepartmentController@findDepartmentByUser');
Route::get('ides/file/{id}', 'IdesController@fileDownload');
Route::get('ides/getFile/{id}', 'IdesController@getFile');
Route::get('/imtq', 'FileController@imtq');


Route::post('unblock-users', 'UnblockedUserController@index');
Route::post('unblock-users/update', 'UnblockedUserController@update');
Route::delete('unblock-users/delete/{id}', 'UnblockedUserController@destroy');

Route::post('user-templates', 'UserTemplateController@index');
Route::post('user-templates/update', 'UserTemplateController@update');
Route::delete('user-templates/delete/{id}', 'UserTemplateController@destroy');


Route::post('phonebook', 'PhonebookController@index');
Route::post('koreshoks', 'KoreshokController@getOylik');
Route::post('koreshoks/update-status', 'KoreshokController@statusUpdate');
Route::get('get-document-type', 'DocumentController@getDocumentTypes');
Route::get('just', 'PhonebookController@just');
Route::get('sasa', 'ReportToshkentController@test');

Route::get('all-templates-for-vayvooo', 'SelectedTemplatesController@alltemplate');
Route::get('centrum-template', 'SelectedTemplatesController@centrumTemplate');
Route::get('avia-report-template', 'SelectedTemplatesController@aviaReportTemplate');

Route::post('logisticReportTest', 'CentrumController@getReportTest');
Route::post('logisticReport', 'CentrumController@getReport');

Route::post('centrum/testRep', 'CentrumController@report123');

Route::post('centrum/attributeReport', 'CentrumController@getAttributeReport');
Route::post('report/aviaReport', 'DocumentDetailAttributeController@aviaTemplateReport');

Route::post('executor-report', 'ReportController@executorReport');
Route::post('control-punkt-report', 'ReportController@controlPunktReport');
Route::post('report/signer-report', 'ReportController@signerReport');
Route::post('document-sheets-report', 'ReportController@documentSheetsReport');
Route::post('requisites-report', 'ReportController@RequisitesReport');
