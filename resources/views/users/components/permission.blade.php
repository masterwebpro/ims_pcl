
<?
    $mod_permission = [];
    foreach($user_module_access as $module_access)  {
       $mod_permission[$module_access->permission_id][] = $module_access->module_id;
    }

   // dd($permissions);
?>
<table class="table">
        <tr>
            <td>&nbsp;</td>
            <td>Module Name</td>
            <? foreach($permissions as $permission) : ?>
                <td class="text-uppercase">{{$permission->name}}</td>
            <? endforeach;?>
        </tr>

        <? foreach($modules as $module) : ?>
            <tr>
            <td>&nbsp;</td>
            <td>{{$module->display_name}}</td>
            <? foreach($permissions as $permission) : $i=0; ?>
                <td class="text-uppercase">
                    <?

                        $checked = "";
                        if(isset($mod_permission[$permission->id])) {
                            if(in_array($module->id, $mod_permission[$permission->id])) {
                                $checked = "checked";
                            }
                        }
                    ?>
                    <div class="form-check form-switch form-switch-success form-switch-md" dir="ltr">
                        <input type="checkbox" class="form-check-input module module_{{$i}}" name="module" id="module_{{$module->id}}_{{$permission->id}}"  {{$checked}} value="module_{{$module->id}}_{{$permission->id}}"  >
                    </div>
                </td>
            <? $i++;  endforeach;?>
        <? endforeach;?>
</table>