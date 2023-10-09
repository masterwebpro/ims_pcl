@inject('components', 'App\Http\Controllers\ComponentsController')
<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo.png') }}" alt="1" height="40">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/pcl_logo.png') }}" alt="2" height="60">
                <span class="text-success fs-20 mt-4">&nbsp</span>
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo.png') }}" alt="3" height="40">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/pcl_logo.png') }}" alt="4" height="60">
                <span class="text-success fs-20 mt-4">&nbsp;</span>
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>

            <?
                $user_menu = $components->_userSubMenuAccess();          
                $user_main_menu = $components->_userMenuAccess();
            ?>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>@lang('translation.menu')</span></li>
                @foreach ($components->listsidebar() as $_menu)
                <li class="nav-item">
                    @if(isset($_menu->submenu_names))
                    <? if(isset($user_main_menu[$_menu->menu_id])) : ?>
                        <a class="nav-link menu-link" href="#side_menu_{{$_menu->menu_id}}" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="side_menu_{{$_menu->menu_id}}">
                            <i class="{{$_menu->icon}}"></i> <span>{{$_menu->parent_menu}}</span>
                        </a>
                        <div class="collapse menu-dropdown" id="side_menu_{{$_menu->menu_id}}">
                            <ul class="nav nav-sm flex-column">
                                <?php $_submenu_names = explode(";", $_menu->submenu_names); ?>
                                @foreach ($_submenu_names as $submenu_name)
                                    <?php $arr_submenu = explode(',', $submenu_name); ?>

                                <? if( in_array( $arr_submenu[0] ,$user_menu ) ) : ?>
                                    <li class="nav-item">
                                        <a href="{{ URL::to((isset($arr_submenu)) ? $arr_submenu[2] : '') }}" class="nav-link">{{ isset($arr_submenu) ? $arr_submenu[1] : '' }}</a>
                                    </li>
                                <? endif;?>
                                @endforeach
                            </ul>
                        </div>
                    <? endif;?>
                    @else
                        <a class="nav-link menu-link" href="#side_menu_{{$_menu->menu_id}}">
                            <i class="{{$_menu->icon}}"></i> <span>{{$_menu->parent_menu}}</span>
                        </a>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
        <!-- Sidebar -->
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
