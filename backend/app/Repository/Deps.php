<?php
namespace App\Repository;

use App\Http\Models\Department;
use Carbon\Carbon;

class Deps
{

	CONST CACHE_KEY = 'USERS';
	public function all($itemsPerPage,$page)
	{
		$key = 'all';

		 $results = cache()->remember($key,Carbon::now()->addMinutes(30),function() use ($itemsPerPage,$page) {
			$departments = Department::with('company')
			                         ->with('parent')
			                         ->with('departmentType')
			                         ->with('staff')
			                         ->with('staff.position')
			                         ->with('staff.range')
			                         ->with('staff.personalType')
			                         ->with('staff.expenceType')
							->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page name', $page);
				return $departments;
		});
		return $results;
	}

}


?>
