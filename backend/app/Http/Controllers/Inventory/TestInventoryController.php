<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;
use App\Http\Models\Inventory\Item;
use App\Http\Models\Inventory\Product;
use App\Http\Models\Inventory\Quarter;
use App\Http\Models\Inventory\Address;
use App\Http\Models\Inventory\Warehouse;
use App\Http\Models\Inventory\AddressProduct;
use App\Http\Models\Inventory\Eo;
use App\User;
use Illuminate\Support\Facades\DB;


class TestInventoryController extends Controller
{

    public function correct()
    {
        $items = Item::where('quarter_id','<>', 1)->get();
        foreach ($items as $key => $value) {
            $part = $value->product->part_number;
            $p=Product::where('part_number',$part)->where('quarter_id',1)->first();
            if($p){
                $value->product_id = $p->id;
                $value->save();
            } else {
                $p=Product::where('part_number',$part)->where('quarter_id','<>',1)->first();
                $p->quarter_id = 1;
                $p->save();
            }
        }
    }

    public function load()
    {
        
        $arr = [
            ['W201','1019','GM25190939','11900'],
        ];

        return 111;
    }

    public function readFromMobile(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            if (gettype($data) == 'array') {
                foreach ($data as $key => $value) {
                    $item = new Item;
                    $item->address_id = $this->getAddressID($value['address']);
                    $item->eo_id = $this->getEoID($value['eo']);
                    $quarter_id = $this->getQuarterID($value['year'], $value['quarter']);
                    $item->product_id = $this->getProductID($value['part_number'], $quarter_id);
                    $item->quantity = $value['quantity'];
                    $item->fact = $value['fact'];
                    $item->full_qr = $value['full_qr'];
                    $item->un_number = $value['un_number'];
                    $item->is_duplicate = $value['is_duplicate'];
                    $item->is_smesh = $value['is_smesh'];
                    $item->scan_date = $value['scan_date'];
                    $item->quarter_id = $quarter_id;
                    $item->created_by = $value['created_by'];
                    $item->save();
                }
                DB::commit();
                return [
                    'message' => 'Successfully saved',
                    'code' => '200',
                ];
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            return [
                'message' => 'Internal server error',
                'code' => '500',
                'data' => $th->getMessage(),
            ];
        }
    }

    public function getAddressID($address)
    {
        return Address::firstOrCreate(['address_name' => $address])->id;
    }

    public function getEoID($eo_number)
    {
        return Eo::firstOrCreate(['eo_number' => $eo_number])->id;
    }

    public function getQuarterID($year, $quarter)
    {
        return Quarter::firstOrCreate(['year' => $year, 'quarter' => $quarter])->id;
    }

    public function getProductID($part_number, $quarter_id)
    {
        return Product::firstOrCreate(['part_number' => $part_number, 'quarter_id' => $quarter_id])->id;
    }

    public function testControl(Request $request)
    {
        $items = Item::select()->get();
        return $items;
    }

    public function userCheck(Request $request)
    {
        if ($request->header('token') == 'BraveLionWzM-AxCcijOOzKpqDnBfJZp7h0iB3If148pJu76bbwbPnB2THnVDOIQ') {
            $username = $request->input('username');
            $password = $request->input('password');
            try {
                $res = $this->getAdInfo($username);
                $isLogin = Adldap::auth()->attempt($username . '@' . $res['account_suffix'], $password, $bindAsUser = true);
                $resInfo = [];
                if ($isLogin) {
                    $user = User::where('username', $username)->first();
                    if ($user) {
                        $resInfo['user_id'] = $user->id;
                        $resInfo['tabel'] = $user->employee->tabel;
                        $resInfo['username'] = $username;
                        $resInfo['firstname'] = $user->employee->firstname_uz_latin;
                        $resInfo['lastname'] = $user->employee->lastname_uz_latin;
                        $resInfo['middlename'] = $user->employee->middlename_uz_latin;
                        return [
                            'message' => 'Successfully login',
                            'code' => '200',
                            'data' => $resInfo
                        ];
                    } else {
                        return [
                            'message' => 'User not found in EDO system',
                            'code' => '404',
                        ];
                    }
                }
                return [
                    'message' => 'Unauthorized with Active Directory',
                    'code' => '401',
                ];
            } catch (\Adldap\Auth\BindException $e) {
                return [
                    'message' => 'Error connection with Active Directory',
                    'code' => '500',
                    'data' => $e->getMessage()
                ];
            }
        }
        return [
            'message' => 'Access Forbidden',
            'code' => '403',
        ];
    }

    public function getAdInfo($username)
    {
        $user = Adldap::search()->findBy('sAMAccountname', $username);
        if ($user) {
            $res['username'] = $user->samaccountname[0];
            $res['fullname'] = $user->cn[0];
            $res['employer_id'] = $user->employeenumber[0];
            $res['mail'] = $user->mail[0];
            // $res['foto'] = base64_encode($user->thumbnailphoto[0]);
            $distinguishedname = $user->distinguishedname[0];
            $distinguishedname = substr($distinguishedname, strpos($distinguishedname, 'DC'), 1000);
            $res['account_suffix'] = str_replace(',', '.', str_ireplace('dc=', '', $distinguishedname));
        } else {
            return false;
        }
        return $res;
    }
}
