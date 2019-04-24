<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'label'=>'Pending List',
        'href'=>site_url($CI->controller_url)
    );
}
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $button_label=$CI->lang->line('LABEL_FILE_IMAGE');
    if($file_type==$CI->config->item('system_file_type_video'))
    {
        $button_label=$file_type;
    }
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW").' '.$button_label,
        'href'=>site_url($CI->controller_url.'/index/add_file/'.$item_id)
    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT').' '.$button_label,
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_file/'.$item_id)
    );
}
if(isset($CI->permissions['action4']) && ($CI->permissions['action4']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_PRINT"),
        'class'=>'button_action_download',
        'data-title'=>"Print",
        'data-print'=>true
    );
}
if(isset($CI->permissions['action5']) && ($CI->permissions['action5']==1))
{
    $action_buttons[]=array(
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_DOWNLOAD"),
        'class'=>'button_action_download',
        'data-title'=>"Download"
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/'.strtolower('list_'.$file_type).'/'.$item_id)
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
    <?php echo $CI->load->view("info_basic", '', true); ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/'.strtolower('get_items_file').'/'.$item_id);?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    if(($key=='id'))
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'number' },
                        <?php
                    }
                    else
                    {
                        ?>
                        { name: '<?php echo $key ?>', type: 'string' },
                        <?php
                    }
                }
                ?>
            ],
            id: 'id',
            url: url
        };
        var tooltiprenderer = function (element) {
            $(element).jqxTooltip({position: 'mouse', content: $(element).text() });
        };
        var cellsrenderer = function (row, column, value, defaultHtml, columnSettings, record) {
            var element = $(defaultHtml);
            element.css({'margin': '0px', 'width': '100%', 'height': '100%', padding: '5px'});
            return element[0].outerHTML;
        };
        var dataAdapter = new $.jqx.dataAdapter(source);
        // create jqxgrid.
        $("#system_jqx_container").jqxGrid(
            {
                width: '100%',
                height: '350px',
                source: dataAdapter,
                filterable: true,
                sortable: true,
                showfilterrow: true,
                columnsresize: true,
                pageable: true,
                pagesize:50,
                pagesizeoptions: ['50', '100', '200','300','500','1000','5000'],
                selectionmode: 'singlerow',
                altrows: true,
               /* rowsheight: 35,
                columnsheight: 40,*/
                rowsheight: 50,
                columnsreorder: true,
                enablebrowserselection: true,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id',width:'50',cellsAlign:'right', cellsrenderer: cellsrenderer, rendered: tooltiprenderer,hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_FILE_IMAGE'); ?>', dataField: 'file_image_video',width:'300', cellsrenderer: cellsrenderer, rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['file_image_video']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_REMARKS'); ?>', dataField: 'remarks',width:'300', cellsrenderer: cellsrenderer, rendered: tooltiprenderer, hidden: <?php echo $system_preference_items['remarks']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_LINK_URL'); ?>',dataField: 'link_url',width:'200', cellsrenderer: cellsrenderer, rendered: tooltiprenderer,hidden: <?php echo $system_preference_items['link_url']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_REVISION_COUNT'); ?>',dataField: 'revision_count',width:'50',cellsAlign:'right', cellsrenderer: cellsrenderer, rendered: tooltiprenderer,hidden: <?php echo $system_preference_items['revision_count']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_ORDERING'); ?>',dataField: 'ordering',width:'50',cellsAlign:'right', cellsrenderer: cellsrenderer, rendered: tooltiprenderer,hidden: <?php echo $system_preference_items['ordering']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_STATUS'); ?>',dataField: 'status',width:'80',filtertype: 'list', cellsrenderer: cellsrenderer, rendered: tooltiprenderer,hidden: <?php echo $system_preference_items['status']?0:1;?>}
                ]
            });
    });
</script>
