<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$action_buttons = array();
if (isset($CI->permissions['action0']) && ($CI->permissions['action0'] == 1))
{
    $action_buttons[] = array(
        'label' => $CI->lang->line("LABEL_TREE_VIEW"),
        'href' => site_url($CI->controller_url . '/index/tree_view')
    );
}
if (isset($CI->permissions['action1']) && ($CI->permissions['action1'] == 1))
{
    $action_buttons[] = array(
        'label' => $CI->lang->line("ACTION_NEW"),
        'href' => site_url($CI->controller_url . '/index/add')
    );
}
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit')
    );
}
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/list')
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<div class="row widget">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>
    <?php
    if (isset($CI->permissions['action6']) && ($CI->permissions['action6'] == 1))
    {
        $CI->load->view('preference', array('system_preference_items' => $system_preference_items));
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>

<script type="text/javascript">
    $(document).ready(function () {
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items'); ?>";
        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key => $value){
                    if($key=='id')
                    {
                    ?> { name: '<?php echo $key; ?>', type: 'number' }, <?php
                    }
                    else
                    {
                    ?> { name: '<?php echo $key; ?>', type: 'string' }, <?php
                    }
                }
                ?>
            ],
            id: 'id',
            type: 'POST',
            url: url,
            data: {max_parent_length: <?php echo $max_parent_length; ?> } // id sent to `get_reporting_items()`
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                source: dataAdapter,
                pageable: true,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pagesize: 50,
                pagesizeoptions: ['50', '100', '200', '300', '500'],
                selectionmode: 'singlerow',
                altrows: true,
                height: '350px',
                enablebrowserselection: true,
                columnsreorder: true,
                columns: [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', pinned: true, dataField: 'id', width: '50', hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_NAME'); ?>', pinned: true, dataField: 'name', width: '180', hidden: <?php echo $system_preference_items['name']?0:1;?>},
                    <?php for($i = 1; $i <= $max_parent_length; $i++){ ?>
                        { text: '<?php echo $CI->lang->line('LABEL_PARENT_' . $i); ?>', pinned: true, dataField: '<?php echo 'parent_'.$i; ?>', width: '180', hidden: <?php echo $system_preference_items['parent_'.$i]?0:1;?>},
                    <?php } ?>
                    { text: '<?php echo $CI->lang->line('LABEL_ORDER'); ?>', dataField: 'ordering', width: '80', cellsalign: 'right', hidden: <?php echo $system_preference_items['ordering']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS');?>', dataField: 'status', cellsalign: 'center', filtertype: 'list', width: 100, hidden: <?php echo $system_preference_items['status']?0:1;?>}
                ]
            });
    });
</script>