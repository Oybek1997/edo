<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Models\Department;
use App\Http\Models\Employee;
use App\Http\Models\ClothingOrder\Product;
use Illuminate\Support\Facades\DB;

class ComponentController extends Controller
{
    public function departments(Request $request)
    {
        $search = $request->input('search');
        $model = Department::query();

        if ($search) {
            $model
                ->where('name_uz_latin', 'ilike', "%" . $search . "%")
                ->orWhere('name_uz_cyril', 'ilike', "%" . $search . "%")
                ->orWhere('name_ru', 'ilike', "%" . $search . "%");
        }

        return $model->orderBy('id')->limit(20)->get()->map(function ($m) {
            return ['value' => $m->id, 'text' => $m->name_uz_latin];
        })->all();
    }

    public function users(Request $request)
    {
        $search = $request->input('search');
        $model = User::query()->with('employee');

        if ($search) {
            $model->whereHas('employee', function ($q) use ($search) {
                $q
                    ->where(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'ilike', "%" . $search . "%")
                    ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $search . "%")
                    ->orWhere('employees.tabel', 'ilike',  "%" . $search . "%");
            });
        }

        return $model->orderBy('id')->limit(20)->get()->map(function ($m) {
            return ['value' => $m->id, 'text' => $m->employee->tabel . " " . $m->employee->full_fio];
        })->all();
    }

    public function employees(Request $request)
    {
        $search = $request->input('search');
        $default = $request->input('default');
        $model = Employee::query();

        if ($search) {
            $model
                ->where(DB::raw("concat(employees.lastname_uz_latin,' ', employees.firstname_uz_latin,' ', employees.middlename_uz_latin)"), 'ilike', "%" . $search . "%")
                ->orWhere(DB::raw("concat(employees.lastname_uz_cyril,' ', employees.firstname_uz_cyril,' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $search . "%")
                ->orWhere(DB::raw("concat(employees.firstname_uz_latin, ' ', employees.lastname_uz_latin, ' ', employees.middlename_uz_latin)"), 'ilike', "%" . $search . "%")
                ->orWhere(DB::raw("concat(employees.firstname_uz_cyril, ' ', employees.lastname_uz_cyril, ' ', employees.middlename_uz_cyril)"), 'ilike', "%" . $search . "%")
                ->orWhere('tabel', 'ilike',  "%" . $search . "%")
                ->orWhere('id', $default);
        }

        return $model->orderBy('id')->limit(20)->get()->map(function ($m) {
            return ['value' => $m->id, 'text' => $m->tabel . " " . $m->full_fio];
        })->all();
    }


    public function products(Request $request)
    {
        $search = $request->input('search');
        $model = Product::query();

        if ($search) {
            $model
                ->where('name', 'ilike', "%" . $search . "%");
        }

        return $model->orderBy('id')->limit(20)->get()->map(function ($m) {
            return ['value' => $m->id, 'text' => $m->name];
        })->all();
    }
}
