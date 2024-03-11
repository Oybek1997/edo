<?php

namespace App\Http\Controllers;

use App\Http\Models\CarModel;
use App\Http\Models\CarPurchase;
use App\Http\Models\Department;
use App\Http\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\File;
use App\Http\Models\Position;
use Illuminate\Support\Facades\DB;

class CarPurchaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $carPurchases = CarPurchase::with('employee')
            ->with('employee.staff.department')
            ->with('employee.staff.position')
            ->with('carModel')
            ->with('createdBy.employee')
            ->with('accountantEmployee')
            ->with('file')
            ->leftJoin('employees', 'employees.id', 'car_purchases.employee_id')
            ->leftJoin('employee_staff', 'employees.id', 'employee_staff.employee_id')
            ->leftJoin('staff', 'employee_staff.staff_id', 'staff.id')
            ->leftJoin('departments', 'staff.department_id', 'departments.id')
            ->leftJoin('positions', 'staff.position_id', 'positions.id')
            ->leftJoin('car_models', 'car_models.id', 'car_purchases.car_model_id');
        if (!Auth::user()->hasPermission('car-purchase-update')) {
            $carPurchases->where('car_purchases.created_by', Auth::id());
        }
        if (isset($search)) {
            $carPurchases->where(function (Builder $query) use ($search) {
                return $query
                    ->where('employees.firstname_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('employees.firstname_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('employees.lastname_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('employees.lastname_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('employees.middlename_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('employees.middlename_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('employees.tabel', 'like', "%" . $search . "%")
                    ->orWhere('car_models.name', 'like', "%" . $search . "%")
                    ->orWhere('car_models.options', 'like', "%" . $search . "%")
                    ->orWhere('departments.department_code', 'like', "%" . $search . "%")
                    ->orWhere('departments.name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('departments.name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('departments.name_ru', 'like', "%" . $search . "%")
                    ->orWhere('positions.name_uz_latin', 'like', "%" . $search . "%")
                    ->orWhere('positions.name_uz_cyril', 'like', "%" . $search . "%")
                    ->orWhere('positions.name_ru', 'like', "%" . $search . "%");
            });
        }

        return $carPurchases->select('car_purchases.*')->distinct()->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CarPurchase  $carPurchase
     * @return \Illuminate\Http\Response
     */
    public function show(CarPurchase $carPurchase)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CarPurchase  $carPurchase
     * @return \Illuminate\Http\Response
     */
    public function edit(CarPurchase $carPurchase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CarPurchase  $carPurchase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CarPurchase $carPurchase)
    {
        // $cp = CarPurchase::where('employee_id', $request->input('employee_id'))->first();
        // if ($cp && $cp->accountant_status != 2) {
        //     return 0;
        // }
        $model = CarPurchase::find($request->input('id'));
        if (!$model) {
            $employee  = CarPurchase::where('employee_id', $request->input('employee_id'))->where('accountant_status', 0)->first();
            if ($employee) {
                return 0;
            }
            $model = new CarPurchase();
            $model->created_by = Auth::id();
        }
        $model->employee_id = $request->input('employee_id');
        $model->car_model_id = $request->input('car_model_id');
        if ($request['accountant_status']) {
            $model->accountant_comment = $request['accountant_comment'];
            $model->accountant_status = $request['accountant_status'];
            $model->account_employee_id = Auth::user()->employee_id;
        }
        // $model->registry_status = $request['registry_status'];
        $model->save();
        if ($request->file('file')) {
            $this->saveFile($request, $model->id);
        }
        return CarPurchase::with('file')->find($model->id);
    }

    public function saveFile(Request $request, $object_id)
    {
        $cp = CarPurchase::find($object_id);
        $model = $request->file('file');
        $object_type_id = 7;
        if (!$cp->file) {
            $filename = time() . rand();
            Storage::putFileAs(
                'documents',
                $model,
                $filename
            );
            $file = new File();
            $file->object_type_id = $object_type_id;
            $file->file_name = $model->getClientOriginalName();
            $file->physical_name = $filename;
            $file->object_id = $object_id;
            $file->created_by = Auth::id();
            $file->save();
        } else {
            $filename = $cp->file->physical_name;

            $file = File::find($cp->file->id);
            $file->file_name = $model->getClientOriginalName();
            $file->updated_by = Auth::id();
            $file->save();

            // dd($filename);
            if (Storage::exists('documents/' . $filename)) {
                Storage::delete('documents/' . $filename);
            }
            Storage::putFileAs(
                'documents',
                $model,
                $filename
            );
        }
    }

    public function getRef($locale)
    {
        return [
            'car_models' => CarModel::whereIn('id', [17])->get(),
            'employees' => Employee::select(
                'id',
                'tabel',
                $locale == 'uz_latin' ? 'firstname_uz_latin' : 'firstname_uz_cyril',
                $locale == 'uz_latin' ? 'middlename_uz_latin' : 'middlename_uz_cyril',
                $locale == 'uz_latin' ? 'lastname_uz_latin' : 'lastname_uz_cyril'
            )->where('is_active', 1)->get(),
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CarPurchase  $carPurchase
     * @return \Illuminate\Http\Response
     */
    public function destroy(CarPurchase $carPurchase, $id)
    {
        $model = CarPurchase::find($id);
        $model->delete();
    }

    public function getCountByCar()
    {
        $model = CarPurchase::select(DB::raw('count(id) as model'), 'car_model_id')->groupBy('car_model_id')->get();
        return $model;
    }

    public function getExcel(Request $request)
    {
        $page = $request->input('page');
        $perPage = $request->input('perPage');
        $carPurchases = CarPurchase::with('employee')
            ->with('employee.staff.department')
            ->with('employee.staff.position')
            ->with('carModel')
            ->with('createdBy.employee')
            ->with('accountantEmployee')
            ->with('file')
            ->leftJoin('employees', 'employees.id', 'car_purchases.employee_id')
            ->leftJoin('employee_staff', 'employees.id', 'employee_staff.employee_id')
            ->leftJoin('staff', 'employee_staff.staff_id', 'staff.id')
            ->leftJoin('departments', 'staff.department_id', 'departments.id')
            ->leftJoin('positions', 'staff.position_id', 'positions.id')
            ->leftJoin('car_models', 'car_models.id', 'car_purchases.car_model_id')
            ->select('car_purchases.*')->get();
        $excel = [];
        $date = '';
        $employee = '';
        $department = '';
        $position = '';
        $car_model = '';
        $file = '';
        $comment = '';
        $status = '';
        $accountant = '';
        $createdBy = '';
        foreach ($carPurchases as $index => $value) {
            $date = $value->updated_at ? substr($value->updated_at, 0, 10) : '';
            $employee = $value->employee ? $value->employee->firstname_uz_cyril . ' ' . $value->employee->lastname_uz_cyril . ' ' . $value->employee->middlename_uz_cyril : '';
            $department = $value->employee->staff[0]->department ? $value->employee->staff[0]->department->name_uz_cyril : '';
            $position = $value->employee->staff[0]->position ? $value->employee->staff[0]->position->name_uz_cyril : '';
            $car_model = $value->carModel ? $value->carModel->name : "";
            $file = $value->file ? $value->file->file_name : "";
            $comment = $value->accountant_comment ? $value->accountant_comment : '';
            $status = $value->accountant_status ? $value->accountant_status : '';
            $accountant = $value->accountantEmployee ? $value->accountantEmployee->firstname_uz_cyril . ' ' . $value->accountantEmployee->lastname_uz_cyril . ' ' . $value->accountantEmployee->middlename_uz_cyril : '';
            $createdBy = $value->createdBy ? $value->createdBy->employee->firstname_uz_cyril . ' ' . $value->createdBy->employee->lastname_uz_cyril . ' ' . $value->createdBy->employee->middlename_uz_cyril : '';
            array_push($excel, (object)[
                "№" => $index + 1 + $page * $perPage - $perPage,
                "Дата" => $date,
                "Сотрудник" => $employee,
                "Подразделения" => $department,
                "Должность" => $position,
                "Модель автомобиля" => $car_model,
                "Файл" => $file,
                "Комментарий" => $comment,
                "Статус" => $status,
                "Бухгалтер" => $accountant,
                "Создан" => $createdBy
            ]);
        }
        return $excel;
    }
}
