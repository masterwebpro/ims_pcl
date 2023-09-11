<?
$menu_array = [];
foreach($user_menu_access as $access)  {
   $menu_array[]= $access->menu_id;
}
?>

<div class="col-xxl-12">
    <div class="accordion accordion-fill-success" id="accordionFill">
        <? 
        $i=0;
        foreach($menu_list as $menu) : ?>
        <div class="accordion-item">
            <h2 class="accordion-header" id="menu_<?=$i?>">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#menu_accor_fill<?=$i?>" aria-controls="menu_accor_fill<?=$i?>">
                   <?=$menu->menu_name?>
                </button>
            </h2>
            <div id="menu_accor_fill<?=$i?>" class="accordion-collapse collapse" aria-labelledby="menu_<?=$i?>" data-bs-parent="#accordionFill">
                <div class="accordion-body">
                    <div class="form-check form-switch form-switch-lg mb-3" dir="ltr">
                        <input type="checkbox" class="form-check-input perm" data-id="{{$i}}" name="perm" id="menus_{{$i}}" >
                        <label class="form-check-label" for="menus_{{$i}}">CHECK / UNCHECK ALL</label>
                    </div>

                    <? foreach($menu->submenus as $submenu) : ?>
                        <div style="padding-left: 40px;">
                            <div class="form-check form-switch form-switch-success form-switch-md" dir="ltr">
                                <input type="checkbox" class="form-check-input perm submenu_{{$i}}" name="perm" id="submenu_{{$submenu->id}}" value="{{$submenu->id}}" <?=(in_array($submenu->id, $menu_array)) ? 'checked':''?> >
                                <label class="form-check-label" for="submenu_{{$submenu->id}}">{{$submenu->submenu_name}}</label>
                            </div>
                        </div>
                    <? endforeach;?>
                </div>
            </div>
        </div>
        <? $i++; endforeach;?>
    </div>
</div><!--end col-->