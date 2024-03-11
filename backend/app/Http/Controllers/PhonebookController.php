<?php

namespace App\Http\Controllers;

use App\Http\Models\Department;
use App\Http\Models\Employee;
use App\Http\Models\Phonebook;
use Illuminate\Http\Request;

class PhonebookController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->input('pagination')['page'];
        $itemsPerPage = $request->input('pagination')['itemsPerPage'];
        $filter = $request->input('filter');
        $models = Phonebook::query()->orderBy('branch', 'asc')->orderBy('name', 'desc')->whereNotIn('phone', [3000,1220,1102,3008,1101,1700,3002,4000,1316,1030,4001,1313]);

        if (isset($filter['name'])) {
            $models->where('name', 'ilike', "%" . $filter['name'] . "%");
        }
        if (isset($filter['branch'])) {
            $models->where('branch', $filter['branch']);
        }
        if (isset($filter['department_name'])) {
            $models->where('department_name', 'ilike', "%" . $filter['department_name'] . "%");
        }
        if (isset($filter['position_name'])) {
            $models->where('position_name', 'ilike', "%" . $filter['position_name'] . "%");
        }
        if (isset($filter['email'])) {
            $models->where('email', 'ilike', "%" . $filter['email'] . "%");
        }
        if (isset($filter['phone'])) {
            $models->where('phone', 'ilike', "%" . $filter['phone'] . "%");
        }
        return $models->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
    }
}
