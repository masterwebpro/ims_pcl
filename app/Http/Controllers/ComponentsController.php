<?php


namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Submenu;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
}
