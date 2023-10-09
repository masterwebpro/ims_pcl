<?php


namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Submenu;
use App\Models\UserModulePermission;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ComponentsController extends Controller
{

    /**
     * Display a listing of the resource.ComponentsController
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function listSidebar()
    {
        $submenus = Menu::select(
            'menus.id as menu_id',
            'menus.menu_name as parent_menu',
            DB::raw("COALESCE(menus.icon,'ri-side-bar-line') as icon"),
            DB::raw("group_concat(DISTINCT CONCAT(submenus.id,',',submenus.submenu_name,',',submenus.url)
                    ORDER BY submenus.sort
                    SEPARATOR ';') as submenu_names")
        )
            ->leftJoin('submenus', 'menus.id', '=', 'submenus.menu_id')
            ->where('menus.is_enabled', '1')
            ->where('submenus.is_enabled', '1')
            ->groupBy('menus.id')
            ->orderBy('menus.sort', 'ASC')
            ->get();

        return $submenus;
    }

    public function client_type() {
       return [
            'T' => "Third-Party", 
            'C' => "Customer" , 
            'O' => "Company"
        ];
    }


    public function _userSubMenuAccess() {
        $user_menu = DB::table('user_menu_access')->select('menu_id')->where('user_id', Auth::id())->get();
        $main_submenu = [];
        foreach($user_menu as $menu) {
            $main_submenu[] = $menu->menu_id;
        }
        return $main_submenu;
    }

    public function _userMenuAccess() {
        $user_main_menu = DB::table('user_menu_access')->select('menus.id', DB::raw("count(submenus.id) as cnt"))
            ->leftJoin('submenus', 'submenus.id', '=', 'user_menu_access.menu_id')
            ->leftJoin('menus', 'menus.id', '=', 'submenus.menu_id')
            ->where('user_menu_access.user_id', Auth::id())
            ->groupBy('menus.menu_name')
            ->get();

        $main_menu = [];
        foreach($user_main_menu as $menu) {
            $main_menu[$menu->id][] = $menu->cnt;
        }

        return $main_menu;
    }
}
