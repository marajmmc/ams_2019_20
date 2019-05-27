<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
if(isset($CI->permissions['action0']) && ($CI->permissions['action0']==1))
{
    $action_buttons[]=array(
        'label'=>'Back',
        'href'=>site_url($CI->controller_url)
    );
}
if(isset($CI->permissions['action2']) && ($CI->permissions['action2']==1))
{
    $action_buttons[]=array(
        'label'=>$CI->lang->line("ACTION_NEW"),
        'href'=>site_url($CI->controller_url.'/index/add_payment/'.$item_id)
    );
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line('ACTION_EDIT'),
        'class'=>'button_jqx_action',
        'data-action-link'=>site_url($CI->controller_url.'/index/edit_payment/'.$item_id)
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
        'href'=>site_url($CI->controller_url.'/index/set_preference')
    );
}
$action_buttons[]=array(
    'label'=>$CI->lang->line("ACTION_REFRESH"),
    'href'=>site_url($CI->controller_url.'/index/list_payment/'.$item_id)
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
    echo $CI->load->view("info_basic", $data=array(), true);
    ?>
    <?php
    if(isset($CI->permissions['action6']) && ($CI->permissions['action6']==1))
    {
        $CI->load->view('preference',array('system_preference_items'=>$system_preference_items));
    }
    ?>
    <div class="row">
        <div class="col-xs-6">
            <button type="button" class="btn btn-success btn-xs">Total Amount: <?php echo System_helper::get_string_amount($amount_total);?></button>
            <button type="button" class="btn btn-warning btn-xs">Paid Amount: <?php echo System_helper::get_string_amount($amount_total_paid);?></button>
            <button type="button" class="btn btn-danger btn-xs">Due: <?php echo System_helper::get_string_amount($amount_total_due);?></button>
        </div>
    </div>

    <div class="col-xs-12" id="system_jqx_container">

    </div>
</div>

<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        var url = "<?php echo site_url($CI->controller_url.'/index/get_items_payment/'.$item_id);?>";

        // prepare the data
        var source =
        {
            dataType: "json",
            dataFields: [
                <?php
                foreach($system_preference_items as $key=>$item)
                {
                    if(($key=='id') || ($key=='amount'))
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
            if(column=='amount')
            {
                element.html(get_string_amount(value));
            }
            if(column=='payment_advance')
            {
                element.html('No');
                if(value==1)
                {
                    element.html('Yes');
                }

            }

            return element[0].outerHTML;

        };
        var aggregates=function (total, column, element, record)
        {
            if(record.amount=="Grand Total")
            {
                return record[element];

            }
            return total;
        };
        var aggregatesrenderer=function (aggregates)
        {
            //console.log('here');
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +aggregates['total']+'</div>';

        };
        var aggregatesrenderer_amount=function (aggregates)
        {
            var text='';
            if(!((aggregates['sum']=='0.00')||(aggregates['sum']=='')))
            {
                text=get_string_amount(aggregates['sum']);
            }
            return '<div style="position: relative; margin: 0px;padding: 5px;width: 100%;height: 100%; overflow: hidden;background-color:'+system_report_color_grand+';">' +text+'</div>';
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
                showaggregates: true,
                showstatusbar: true,
                altrows: true,
               /* rowsheight: 35,
                columnsheight: 40,*/
                columnsreorder: true,
                enablebrowserselection: true,
                columns:
                [
                    { text: '<?php echo $CI->lang->line('LABEL_ID'); ?>', dataField: 'id',width:'50',cellsAlign:'right',hidden: <?php echo $system_preference_items['id']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_DATE_PAYMENT'); ?>', dataField: 'date_payment',width:'100', hidden: <?php echo $system_preference_items['date_payment']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_PAYMENT_ADVANCE'); ?>', dataField: 'payment_advance',width:'50',filtertype: 'list',cellsrenderer: cellsrenderer, hidden: <?php echo $system_preference_items['payment_advance']?0:1;?>},
                    { text: '<?php echo $CI->lang->line('LABEL_AMOUNT'); ?>', dataField: 'amount',width:'120', hidden: <?php echo $system_preference_items['amount']?0:1;?>,cellsrenderer: cellsrenderer,aggregates: ['sum'],aggregatesrenderer:aggregatesrenderer_amount,
                        initeditor: function (row, cellvalue, editor, celltext, pressedkey)
                        {
                            editor.wrap( '<div style="margin: 0px;width: 100%;height: 100%;padding: 5px;;line-height: 25px;">');
                            editor.wrap( '<div class="jqxgrid_input">');
                            editor.addClass('float_type_positive');
                            editor.css('width','100%');
                            editor.css('height','100%');
                            editor.css('border-width','0');
                        }
                    },
                    { text: '<?php echo $CI->lang->line('LABEL_REMARKS'); ?>', dataField: 'remarks',width:'300', hidden: <?php echo $system_preference_items['remarks']?0:1;?>}
                ]
            });
    });
</script>
