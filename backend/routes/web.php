<?php

use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentSignerController;
use App\Http\Models\Employee;
use App\Http\Models\Document;
use App\Http\Models\Phonebook;
use App\Http\Models\EDI\Material;
use App\Http\Models\File;
use App\Http\Models\KpiObject;
use App\PostOrder;
use App\InventoryProductList;
use App\InventoryCommission;
use App\CommissionWarehouse;
use App\InventoryWarehouseList;
use App\InventoryAddress;
use App\InventoryUniqueProduct;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;


Route::get('/test-pgsql', "PostgreDBController@migrate");
Route::get('/write-new-employee/{id}', "DocumentSignerController@changeStaffAndSaveAs400");
Route::get('/chart10/{id}', "DepartmentController@orgCharttest10");
Route::get('/test-pgsql1', "PostgreDBController@migrate1");

Route::get('/write-to-mater-res-people', "MaterialResponsiblePeopleController@writeToMaterResponsiblePeople");

Route::get('/inventory/correct', "Inventory\TestInventoryController@correct");
Route::get('/employe-staff/vacancies', 'EmployeeStaffController@employeeStaffVacancies');
Route::get('/test-abc', function () {
	return User::getAdUsers();
});

Route::post('/backend-mobile-verify', function (Request $request) {
	$data = $request->all();
	$ip = $request->ip();
	$real_ip = $request->header('x-real-ip');
	$data['ip'] = $real_ip ? $real_ip : $ip;
	return Document::backendMobileVerify($data);
});

Route::get('/frontend-mobile-sign', function () {
	return Document::frontendMobileSign();
});

Route::get('/frontend-mobile-status/{doc_id}', function ($doc_id) {
	return Document::frontendMobileStatus($doc_id);
});


// Route::post('/backend-mobile-verify', function (Request $request) {
// 	return Document::backendMobileVerify([$request->input('document'), $request->input('documentId')]);
// });

// Route::get('/abc', function () {
// 	return Document::eimzoMobileAuth();
// });

Route::get('/diller-delete-cancelled', function () {
	$documents = Document::whereIn('document_type_id', [50, 72])->where('status', 6)->limit(50)
		->whereHas('files')
		->get();
	foreach ($documents as $key => $value) {
		$files = File::where('object_type_id', 5)->where('object_id', $value->id)->get();
		if ($files) {
			foreach ($files as $k => $v) {
				if (Storage::exists('/documents_new/' . $v->physical_name)) {
					Storage::delete('/documents_new/' . $v->physical_name);
					$v->delete();
					echo $v->physical_name . '<br>';
				}
			}
		}
	}
	return $documents->count();
});


Route::get('/delete-upload-file-cancelled-documents', function () {
	$count = 0;
	$documents = Document::query()
		// ->whereIn('document_type_id',[50,72])
		->where('status', 6)
		->where('created_at', '<', '2023-12-20 00:00:00')
		->limit(60)
		->whereHas('files')
		->get();
	foreach ($documents as $key => $value) {
		echo "ID: " . $value->id . " - - - - - Status: ";
		echo $value->status . " - - - - - FIle count: ";
		$files = File::where('object_type_id', 5)->where('object_id', $value->id)->get();
		echo count($files) . "<br>";
		if ($files) {
			foreach ($files as $k => $v) {
				if (Storage::exists('/documents_new/' . $v->physical_name)) {
					Storage::delete('/documents_new/' . $v->physical_name);
					$v->delete();
					$count++;
				} else if (Storage::exists('/documents/' . $v->physical_name)) {
					Storage::delete('/documents/' . $v->physical_name);
					$v->delete();
					$count++;
				}
			}
		}
	}
	if ($documents->count() > 0) {
		echo date('Y-m-d H:i:s') . "<br>" . $count . "<script>location.reload();window.reload();</script>";
	}
	return $count . '/' . $documents->count();
});

Route::get('/delete-upload-file-new-documents', function () {
	$count = 0;
	$documents = Document::query()
		// ->whereIn('document_type_id',[50,72])
		->where('status', 0)
		->where('document_date', '<', '2023-12-25 00:00:00')
		->limit(50)
		->whereHas('files')
		->get();
	foreach ($documents as $key => $value) {
		echo "ID: " . $value->id . " - - - - - Status: ";
		echo $value->status . " - - - - - FIle count: ";
		$files = File::where('object_type_id', 5)->where('object_id', $value->id)->get();
		echo count($files) . "<br>";
		if ($files) {
			foreach ($files as $k => $v) {
				if (Storage::exists('/documents_new/' . $v->physical_name)) {
					Storage::delete('/documents_new/' . $v->physical_name);
					$v->delete();
					$count++;
				}
			}
		}
	}
	// if($documents->count() > 0){
	echo date('Y-m-d H:i:s') . "<br>" . $count . "<script>location.reload();window.reload();</script>";
	// }
	return $count . '/' . $documents->count();
});

Route::get('/okd', function () {
	return $staffs = Employee::find(11623)->staff;
	$documents = Document::whereHas('documentSigners', function ($q) use ($staffs, $user) {
		$q->select('id')->where(function ($qu) use ($staffs) {
			foreach ($staffs as $key => $value) {
				$qu->orWhere('staff_id', $value->id);
			}
		})
			->where('action_type_id', '!=', 3)
			->where('action_type_id', '!=', 6)
			->where(function ($q) use ($user) {
				return $q->whereNotNull('signer_employee_id')
					->where('signer_employee_id', $user->employee_id)
					->orWhereNull('signer_employee_id');
			})
			->whereNotNull('taken_datetime')
			->where(function ($qu) {
				$qu->where('status', 0);
			});
	})
		->where('document_type_id', $value_type->id)
		->where('status', '!=', 0)
		->where('status', '!=', 6);
	return 111;
});


Route::get('/test-order/{id}', 'PostOrderController@test');

Route::post('/documents/transport-request-completed', 'DocumentSignerController@transportRequestCompleted');
/*Uploads Device or Device_History Table data to the Database*/
Route::get('/uploads', 'TemporaryController@index');

Route::get('/post', function () {
	return PostOrder::generatePdf(1);
});

Route::get('/uvolnitelniy/{id}', function ($id) {
	$dc = new DocumentController();
	return $dc->uvolnitelniyCreate($id);
});

Route::get('/komandirovka/{id}', function ($id) {
	$dc = new DocumentSignerController();
	return $dc->businessTripToAs400($id);
});
Route::get('/otgul/{id}', function ($id) {
	$dc = new DocumentSignerController();
	return $dc->otgulToAs400($id);
});

Route::get('/transport/{id}', function ($id) {
	$ds = new DocumentSignerController();
	return $ds->transportRequestCreate($id);
});

Route::get('/stamp/{id}', function ($id) {
	// return phpinfo(); 
	// dd($width, $height);
	return Document::stampDocument($id);
});

Route::get('/raxbar/{id}', function ($id) {
	// return phpinfo(); 
	// dd($width, $height);
	return KpiObject::getDepID($id);
});

Route::get('/add-employee-array', function () {
	$arr = [
		['N352', 'Jumayev', 'Oyatilloxon', 'Tilovoldixon o‘g‘li', 'Жумаев', 'Оятиллохон', 'Тиловолдихон ўғли', 'Жумаев', 'Оятиллохон', 'Тиловолдихон ўғли'],
	];

	DB::BeginTransaction();
	try {
		foreach ($arr as $key => $value) {
			if (!Employee::where('tabel', $value[0])->first()) {
				$emp = new Employee();
				$emp->tabel = $value[0];
				$emp->firstname_uz_latin = $value[2];
				$emp->lastname_uz_latin = $value[1];
				$emp->middlename_uz_latin = $value[3];
				$emp->firstname_uz_cyril = $value[5];
				$emp->lastname_uz_cyril = $value[4];
				$emp->middlename_uz_cyril = $value[6];
				$emp->firstname_ru = $value[8];
				$emp->lastname_ru = $value[7];
				$emp->middlename_ru = $value[9];
				$emp->tariff_scale_id = 4;
				$emp->company_id = 1;
				$emp->nationality_id = 1;
				$emp->born_date = '2022-10-04';
				$emp->INPS = 1;
				$emp->file_type = 'jpg';
				$emp->is_active = 1;
				echo ' - ' . $emp->save();

				//,1,1,'2022-01-01',1,'jpg',1);

			}
		}
		DB::commit();
	} catch (\Throwable $th) {
		DB::rollback();
		throw $th;
	}
});

Route::get('/get-img/{tabel}', function ($tabel) {
	$filename = $tabel . '.jpg';
	if (file_exists(storage_path('app/avatars/' . $filename))) {
		return response()->file(storage_path('app/avatars/' . $filename), []);
	}
	$filename = $tabel . '.JPG';
	if (file_exists(storage_path('app/avatars/' . $filename))) {
		return response()->file(storage_path('app/avatars/' . $filename), []);
	}
});

Route::get('/edi/materials/get-img/{id}', function ($id) {
	$material = Material::find($id);
	if (Storage::exists('edi/materials/' . $material->picture)) {
		return response()->file(storage_path('app/edi/materials/' . $material->picture), []);
	}
});

Route::get('/get-phone/{tabel}', function ($tabel) {
	$phone = Phonebook::select(DB::raw("SUBSTRING_INDEX(name,' ',2) as name"), 'department_name', 'position_name', 'email', 'phone')->where('name', 'like', '%' . $tabel . '%')->get()->toArray();
	return $phone;
});

Route::get('/uvolnitelniy-refresh/{from}/{to}', function ($from, $to) {
	$dc = new DocumentController();
	$documents = Document::where('document_template_id', 287)->where('id', '>=', $from)->where('id', '<=', $to)->whereIn('status', [3, 4, 5])->get();
	echo '<a href="https://b-edo.uzautomotors.com/uvolnitelniy-refresh/' . $to . '/' . ($to + $to - $from) . '" style="margin-left:200px;border:1px solid red; padding:15px;display:block; border-radius:2px;text-decoration:none;">Next</a><hr>';
	foreach ($documents as $key => $value) {
		$dc->uvolnitelniyCreate($value->id);
		echo $value->id . "<hr>";
	}
	// https://b-edo.uzautomotors.com/uvolnitelniy-refresh/179784/179784
});

Route::get('/get-document-number/{from}/{to}', function ($from, $to) {
	$documents = Document::query()
		->where(function($q){
			$q->whereIn('document_number', ['YYXX-0000-0000', 'YYAXX-0000-0000'])
				->orWhereIn('id', [2760915]);
		})
		->where('id', '>=', $from)
		->where('id', '<=', $to)
		->whereIn('status', [3, 4, 5])
		->get();
	// return $documents->count();
	foreach ($documents as $key => $value) {
		Document::generateDocumentNumberNew2022($value->id);
		echo $value->id . "<hr>";
	}
	echo '<a href="https://b-edo.uzautomotors.com/get-document-number/' . $to . '/' . ($to + $to - $from) . '" style="margin-left:200px;border:1px solid red; padding:5px; border-radius:2px;text-decoration:none;">Next</a><hr>';
	// https://b-edo.uzautomotors.com/get-document-number/179784/179784
});

Route::get('/to-as400/{id1}/{id2}', function ($id1, $id2) {
	$documents = Document::where('document_template_id', 114)->where('id', '>=', $id1)->where('id', '<=', $id2)->whereIn('status', [3, 4, 5])->get();
	foreach ($documents as $key => $value) {
		$ds = new App\Http\Controllers\DocumentSignerController();
		return $ds->saveAttributesToAs400($value->id);
		echo ' - - - - ' . $value->id;
	}
	echo 8;
});

Route::get('/to-as400-2/{id1}/{id2}', function ($id1, $id2) {
	$documents = Document::where('document_template_id', 582)->where('id', '>=', $id1)->where('id', '<=', $id2)->whereIn('status', [3, 4, 5])->get();
	foreach ($documents as $key => $value) {
		$ds = new App\Http\Controllers\DocumentSignerController();
		return $ds->saveUquvTatilToAs400($value->id);
		echo ' - - - - ' . $value->id;
	}
	echo 8;
});

Route::post('/get-captcha', 'HomeController@getCaptcha');

Route::get('/get-user/{username}', function ($username) {
	return Adldap::search()->findBy('sAMAccountname', $username);
	return Adldap::search()->findBy('sAMAccountname', $username)['employeenumber'][0];
});

Route::get('/get-employee-by-tabno/{tabno}', function ($tabno) {
	$employee = Employee::where('tabel', $tabno)->first();
	if ($employee) {
		$arr = [];
		$arr['firstname'] = $employee->firstname_uz_latin;
		$arr['lastname'] = $employee->lastname_uz_latin;
		$arr['middlename'] = $employee->middlename_uz_latin;
		if ($employee->staff && $employee->staff[0] && $employee->staff[0]->department)
			$arr['department'] = $employee->staff[0]->department->name_uz_latin;
		else
			$arr['department'] = null;
		if ($employee->staff && $employee->staff && $employee->staff[0]->department && $employee->staff[0]->department->managerStaff && $employee->staff[0]->department->managerStaff->employees && $employee->staff[0]->department->managerStaff->employees[0]) {
			$arr['department_manager_firstname'] = $employee->staff[0]->department->managerStaff->employees[0]->firstname_uz_latin;
			$arr['department_manager_lastname'] = $employee->staff[0]->department->managerStaff->employees[0]->lastname_uz_latin;
			$arr['department_manager_middlename'] = $employee->staff[0]->department->managerStaff->employees[0]->middlename_uz_latin;
			$arr['department_manager_tabno'] = $employee->staff[0]->department->managerStaff->employees[0]->tabel;
		} else {
			$arr['department_manager_firstname'] = null;
			$arr['department_manager_lastname'] = null;
			$arr['department_manager_middlename'] = null;
			$arr['department_manager_tabno'] = null;
		}
		echo '<pre>';
		print_r($arr);
	}
	return null;
});

Route::get('/leavingtest', 'EmployeeStaffController@leavingtest');

Route::post('/get-file', 'HomeController@getFile');

Route::get('/file-captcha/{filename}', 'HomeController@fileCaptcha');

Route::get('/file-download/{id}', 'HomeController@fileDownload');


Route::get('prikaz-to-as400/{id}', 'DocumentSignerController@saveAttributesToAs400');

Route::get('staffs/get-file/{id}', 'StaffController@getFile');
Route::get('get-file-by-name/{name}', 'StaffController@getFileByName');

Route::get('staffs/file-download/{id}', 'StaffController@fileDownload');

Route::get('kpi/file-download/{id}', 'KpiController@fileDownload');

Route::get('temptest', 'DocumentTemplateController@test');

Route::get('/{hash}', 'DocumentController@downloadPdfFile');

Route::get('/department-change-branch', 'DepartmentController@changeBranch');

Route::get('/attribute/content', 'DocumentController@attribute');

Route::get('/attribute-show-table/{id}', 'DocumentController@documentContent');

Route::post('/download-blank', 'BlankTemplatesController@download');

Route::get('/timeline-file/{id}', 'TimelineController@getFile');

Route::get('/ides/showFile/{id}', 'IdesController@showFile');

Route::get('/koreshok/test/{month}/{tabel}', 'KoreshokController@test');

Auth::routes();
