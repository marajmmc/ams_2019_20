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
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_DETAILS'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/details')
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
if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
{
    $action_buttons[]=array
    (
        'label'=>'Preference',
        'href'=>site_url($CI->controller_url.'/index/set_preference_all')
    );
}
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list_all')
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_LOAD_MORE"),
    'id'=>'button_jqx_load_more'
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
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
    }
    ?>
    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_all');?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                 foreach($system_preference_items as $key=>$item)
                 {
                   if(($key=='id') || ($key=='quantity_total') || ($key=='revision_count_request') )
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
            type: 'POST',
            url: url
        };

        var dataAdapter = new $.jqx.dataAdapter(source);
        var cellsrenderer = function(row, column, value, defaultHtml, columnSettings, record)
        {
            var element = $(defaultHtml);
            if(column=='quantity_total')
            {
                element.html(get_string_quantity(value));
            }
            else if(column=='amount_price_unit' || column=='amount_price_total' )
            {
                element.html(get_string_amount(value));
            }

            return element[0].outerHTML;

        };
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
                columnsreorder: true,
                enablebrowserselection: true,
                columns:
                    [
                        { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id',width:'50',cellsAlign:'right',hidden: <?php echo $system_preference_items['id']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_DATE'); ?>', dataField: 'date_requisition',width:'100', hidden: <?php echo $system_preference_items['date_requisition']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_SUPPLIER_NAME'); ?>', dataField: 'supplier_name',width:'200', hidden: <?php echo $system_preference_items['supplier_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_CATEGORY_NAME'); ?>', dataField: 'category_name',width:'200', hidden: <?php echo $system_preference_items['category_name']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_MODEL_NUMBER'); ?>', dataField: 'model_number',width:'100', hidden: <?php echo $system_preference_items['model_number']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL'); ?>', dataField: 'quantity_total',width:'80',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['quantity_total']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_PRICE_UNIT'); ?>', dataField: 'amount_price_unit',width:'80',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['amount_price_unit']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_AMOUNT_PRICE_TOTAL'); ?>', dataField: 'amount_price_total',width:'80',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['amount_price_total']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_SPECIFICATION'); ?>', dataField: 'specification',width:'300', hidden: <?php echo $system_preference_items['specification']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_REASON'); ?>', dataField: 'reason',width:'300', hidden: <?php echo $system_preference_items['reason']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_REMARKS'); ?>', dataField: 'remarks',width:'300', hidden: <?php echo $system_preference_items['remarks']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_REVISION_COUNT_REQUEST'); ?>',dataField: 'revision_count_request',width:'30',hidden: <?php echo $system_preference_items['revision_count_request']?0:1;?>},
                        { text: '<?php echo $CI->lang->line('LABEL_STATUS_APPROVE'); ?>',dataField: 'status_approve',width:'80',filtertype: 'list',hidden: <?php echo $system_preference_items['status_approve']?0:1;?>}
                    ]
            });
    });
</script>