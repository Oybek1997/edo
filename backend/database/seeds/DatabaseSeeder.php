<?php

use App\Http\Models\Company;
use App\Http\Models\Department;
use App\Http\Models\DepartmentType;
use App\User;
use App\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(LaratrustSeeder::class);

        //Create new Company
        $company = new Company();
        $company->name = 'AJ "Uz Auto Motors"';
        $company->save();


        $role = Role::where('name', 'superadministrator')->first();

        // Create default user for each role gs6416
        $user = new User;
        $user->employee_id = 1;
        $user->email =  'qodirjon.gofurjonov@uzautomotors.com';
        $user->username = 'qg9592';
        $user->password =  Hash::make('123456');
        $user->save();
        $user->attachRole($role);

        $user = new User;
        $user->employee_id = 1;
        $user->email =  'Tohirjon.Boybuvayev@uzautomotors.com';
        $user->username = 'tb8110';
        $user->password =  Hash::make('123456');
        $user->save();
        $user->attachRole($role);

        $user = new User;
        $user->employee_id  = 1;
        $user->email =  'gulomjon.sulaymonov@uzautomotors.com';
        $user->username = 'gs6416';
        $user->password = Hash::make('123456');
        $user->save();
        $user->attachRole($role);

        $user = new User;
        $user->employee_id  = 1;
        $user->email =  'kobiljon.yusupov@uzautomotors.com';
        $user->username = 'ky5431';
        $user->password = Hash::make('123456');
        $user->save();
        $user->attachRole($role);

        $user = new User;
        $user->employee_id  = 1;
        $user->email =  'azizbek.rahmonov@uzautomotors.com';
        $user->username = 'ra8108';
        $user->password = Hash::make('123456');
        $user->save();
        $user->attachRole($role);

        $user = new User;
        $user->employee_id  = 1;
        $user->email =  'alisher.yusupov@uzautomotors.com';
        $user->username = 'ay2275';
        $user->password = Hash::make('123456');
        $user->save();
        $user->attachRole($role);
    }
}
