<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Menu;
use App\Models\UserMenuAccess;
use App\Models\UserModulePermission;
use App\Models\Permissions;
use App\Models\Modules;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $user_list = User::select('*')
            ->where([
                [function ($query) use ($request) {
                    if (($s = $request->q)) {
                        $query->orWhere('name','like', '%'.$s.'%');
                        $query->get();
                    }
                }]
            ])
            ->where([
                [function ($query) use ($request) {
                    if ($request->filter_date) {
                        if($request->filter_date == 'created_at' && $request->date ) {
                            $query->whereBetween('created_at', [$request->date." 00:00:00", $request->date." 23:59:00"]);
                        }
                    }

                    $query->get();
                }]
            ])
            ->orderByDesc('created_at')
            ->paginate(20);

        if (mod_access('users', 'view', Auth::id())) {
            return view('users/index', ['user_list' => $user_list]);
        } else {
            return view('error/no-access');
        } 
    }

    public function create()
    {
        $permissions = [];

        $user_list = array();

        $roles = [];

        $is_create = true;

        if (mod_access('users', 'add', Auth::id())) {
            return view('users/create', compact('permissions', 'user_list', 'roles', 'is_create'));
        } else {
            return view('error/no-access');
        } 

       
    }

    public function store(Request $request)
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_no' => 'required',
            'email_address' => 'required|email|unique:users,email,'.$request->user_id,
        ];

        $password_rules = [
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required'
        ];

        if ($request->is_edit_password) {
            $rules = array_merge($rules, $password_rules);
        }

        $validator = Validator::make($request->all(), 
            $rules,
            [
            'first_name' => 'First Name is required',
            'last_name' => 'Last Name is required',
            'mobile_no' => 'Mobile No is required',
            'email_address' => 'Mobile No is required',
            'password' => 'New Password is required',
            'password_confirmation' => 'Confirmation Password is required',
            'password.confirmed' => 'Password field does not match',
            'email_address.unique' => 'This data is already used!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()]);
        }

        DB::connection()->beginTransaction();

        try {
            $name = ucwords($request->first_name)." ".ucwords($request->last_name);

            if (!isset($request->user_id)) {
                $user = User::create([
                    'name'=>$name,
                    'email'=>$request->email_address,
                    'mobile_no'=>clean(str_replace("-", "", $request->mobile_no)),
                    'first_name'=>$request->first_name,
                    'last_name'=>$request->last_name,
                    'middle_name'=>$request->middle_name,
                    'is_active'=>$request->is_active,
                    'reported_to'=>$request->reported_to,
                    'password'=>Hash::make($request->password),
                    'avatar'=>$request->image_path,
                    'role_id'=>$request->role_id,
                    'company'=>$request->company,
                    'department'=>$request->department,
                    'position'=>$request->position,
                ]);

                $user_id = $user->id;

            } else {

                $user_id = $request->user_id;
                $user = User::findOrFail($user_id);
                $user->name = $name;
                $user->first_name = $request->first_name;
                $user->last_name = $request->last_name;
                $user->middle_name = $request->middle_name;
                $user->email = $request->email_address;
                $user->department = $request->department;
                $user->company = $request->company;
                $user->designation = $request->position;
                $user->reported_to = $request->reported_to;
                $user->avatar = $request->image_path;
                $user->is_active = ($request->is_active == true) ? 1 : 0;
                $user->role_id = $request->role_id;
                $user->mobile_no = clean(str_replace("-", "", $request->mobile_no));

                if ($request->is_edit_password == 1) {
                    $user->password = Hash::make($request->password);
                }
                $user->save();
            }

            //save on menu access
            $menu_dtl = array();
            if(isset($request->menu_access)) {
                for($x=0; $x < count($request->menu_access); $x++ ) {
                    $menu_dtl[] = array(
                        'user_id'=>$user_id,
                        'menu_id'=>$request->menu_access[$x],
                    );
                }
                if($menu_dtl) {
                    UserMenuAccess::where('user_id',$user_id)->delete();
                    UserMenuAccess::insert($menu_dtl);
                }
            }

            //save on module access
            $module_dtl = array();
            if(isset($request->module_access)) {
                for($x=0; $x < count($request->module_access); $x++ ) {
                    $module = explode("_",$request->module_access[$x]);
                    $module_dtl[] = array(
                        'user_id'=>$user_id,
                        'module_id'=>$module[1],
                        'permission_id'=>$module[2],
                        'created_at'=>$this->current_datetime,
                        'updated_at'=>$this->current_datetime,
                    );
                }
                if($module_dtl) {
                    UserModulePermission::where('user_id',$user_id)->delete();
                    UserModulePermission::insert($module_dtl);
                }
            }
    
            DB::connection()->commit();

            return response()->json([
                'success'  => true,
                'message' => 'Saved successfully!',
                'data'    => $user,
                'id'=> _encode($user->id)
            ]);


        } catch (Throwable $e) {

            DB::connection()->rollback();
            return response()->json([
                'success'  => false,
                'message' => 'Unable to process request. Please try again.',
                'data'    => $e->getMessage()
            ]);
        }


    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $id = _decode($id);

        $permissions = Permissions::all();
        $modules = Modules::all();

        $user_list = [];

        $user_menu_access = UserMenuAccess::where('user_id', $id)->get();

        $user_module_access = UserModulePermission::where('user_id', $id)->get();

        $menu_list = $this->getUserMenu();

        $roles = [];

        $user = User::findOrFail($id);

        $is_create = false;

        if (mod_access('users', 'edit', Auth::id())) {
            return view('users/edit', compact(
                'permissions', 
                'user', 
                'user_list', 
                'roles', 
                'is_create',
                'menu_list', 
                'user_menu_access',
                'user_module_access',
                'modules')
            );
        } else {
            return view('error/no-access');
        } 

        
    }

    public function getUserMenu($user_id=0)
    {
        $menus = Menu::select('*')
            ->where('menus.is_enabled', '1')
            ->groupBy('menus.id')
            ->orderBy('menus.sort', 'ASC')
            ->get();

        return $menus;
    }
}
