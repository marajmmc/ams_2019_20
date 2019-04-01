<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action1']) && ($CI->permissions['action1']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
        'href'=>site_url($CI->controller_url.'/index/add')
    );
}
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("LABEL_OGANOGRAM_VIEW"),
        'href'=>site_url($CI->controller_url.'/index/organogram_view')
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list')
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="col-xs-12" style="overflow-x: auto;">
        <table class="table table-hover table-bordered">
            <thead>
            <tr>
                <th style="width:5%"><?php echo $CI->lang->line("SLNO"); ?></th>
                <th style="width:5%"><?php echo $CI->lang->line("ID"); ?></th>
                <th><?php echo $CI->lang->line("NAME"); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(sizeof($items)>0)
            {
                $i=0;
                foreach($items as $key => $item)
                {
                    ?>
                    <tr>
                        <td><?php echo ++$i; ?></td>
                        <td><?php echo $key; ?></td>
                        <td><?php echo $item['prefix']; ?><a href="<?php echo site_url($CI->controller_url.'/index/edit/'.$key); ?>"><?php echo $item['name']; ?></a></td>
                    </tr>
                <?php
                }
            }
            else
            {
                ?>
                <tr>
                    <td colspan="20" class="text-center alert-danger">
                        <?php echo $CI->lang->line('NO_DATA_FOUND'); ?>
                    </td>
                </tr>
            <?php
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>
