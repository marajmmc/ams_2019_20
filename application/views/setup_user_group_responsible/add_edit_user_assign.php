<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
if((isset($CI->permissions['action1']) && ($CI->permissions['action1']==1)) || (isset($CI->permissions['action2']) && ($CI->permissions['action2']==1)))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE"),
        'id'=>'button_action_save',
        'data-form'=>'#save_form'
    );
}
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_user_assign');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-responsive">
                    <thead>
                    <tr>
                        <th style="width: 10px;">SL#</th>
                        <th style="width: 10px;">
                            <input type="checkbox" class="allSelectCheckbox" name="" >
                            All
                        </th>
                        <th>Employee Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $serial_no=0;
                    foreach($users as $user)
                    {
                        ++$serial_no;
                        ?>
                    <tr>
                        <td><?php echo $serial_no?></td>
                        <td>
                            <input type="checkbox" id="user_id_<?php echo $user['id']?>" name="items[]" class="" value="<?php echo $user['id']?>" <?php if(in_array($user['id'],$user_ids)){echo "checked='true'";}?> />
                        </td>
                        <td><label for="user_id_<?php echo $user['id']?>" style="cursor: pointer"><?php echo $user['name']?></label></td>
                    </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});

        $(document).on("click",'.allSelectCheckbox',function()
        {
            if($(this).is(':checked'))
            {
                $('input:checkbox').prop('checked', true);
            }
            else
            {
                $('input:checkbox').prop('checked', false);
            }
        });

    });
</script>

