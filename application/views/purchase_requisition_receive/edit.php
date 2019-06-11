<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array
(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_SAVE"),
    'id'=>'button_action_save',
    'data-form'=>'#save_form'
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));

?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php
        $data['accordion']['collapse']='in';
        echo $CI->load->view("info_basic", $data, true);
        ?>
        <table class="table table-bordered table-responsive system_table_details_view">
            <tbody>
            <?php
            $categories_all=Category_helper::get_category_parent_children();
            if(isset($categories_all['parents'][$item['category_id']]))
            {
                $category_parent=$categories_all['parents'][$item['category_id']];
                for($i=sizeof($category_parent); $i>=0; $i--)
                {
                    $child_id=isset($category_parent[$i])?$category_parent[$i]:0;
                    if(isset($categories_all['category'][$child_id]))
                    {
                        $sub_categories=$categories_all['category'][$child_id];
                        ?>
                        <tr>
                            <td><label class="control-label pull-right">Category</label></td>
                            <td><?php echo $sub_categories['name']?></td>
                        </tr>
                    <?php
                    }
                }
                ?>
                <tr>
                    <td><label class="control-label pull-right">Category</label></td>
                    <td><?php echo $categories_all['category'][$item['category_id']]['name']?></td>
                </tr>
            <?php
            }
            ?>
            <!--<tr>
                <td><label class="control-label pull-right"><?php /*echo $CI->lang->line('LABEL_DATE');*/?></label></td>
                <td><?php /*echo System_helper::display_date($item['date_requisition']);*/?></td>
            </tr>-->
            <tr>
                <td><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SUPPLIER_NAME');?></label></td>
                <td><?php echo $item['supplier_name'];?></td>
            </tr>
            <!--<tr>
                <td><label class="control-label pull-right"><?php /*echo $CI->lang->line('LABEL_MODEL_NUMBER');*/?></label></td>
                <td><?php /*echo $item['model_number'];*/?></td>
            </tr>-->
            <tr>
                <td><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL');?></label></td>
                <td><?php echo System_helper::get_string_quantity($item['quantity_total']);?></td>
            </tr>
            <tr>
                <td><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_PRICE_UNIT');?></label></td>
                <td><?php echo System_helper::get_string_amount($item['amount_price_unit']);?></td>
            </tr>
            <tr>
                <td><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_PRICE_TOTAL');?></label></td>
                <td><?php echo System_helper::get_string_amount($item['amount_price_total']);?></td>
            </tr>
            <tr>
                <td><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SPECIFICATION');?></label></td>
                <td><?php echo nl2br($item['specification']);?></td>
            </tr>
            <tr>
                <td><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REASON');?></label></td>
                <td><?php echo nl2br($item['reason']);?></td>
            </tr>
            <tr>
                <td><label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?></label></td>
                <td><?php echo nl2br($item['remarks']);?></td>
            </tr>
            </tbody>
        </table>
        <hr/>
        <table class="table table-bordered table-responsive system_table_details_view">
            <thead>
            <tr>
                <th colspan="3" class="text-center">
                    <button class="btn btn-sm btn-info">
                        <?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL');?>: <?php echo System_helper::get_string_quantity($item['quantity_total']);?>
                    </button>
                    <button class="btn btn-sm btn-success">
                        Receive Quantity: <?php echo sizeof($assets);?>
                    </button>
                    <button class="btn btn-sm btn-danger">
                        Not Receive: <?php echo System_helper::get_string_quantity($item['quantity_total']-sizeof($assets));?>
                    </button>
                </th>
            </tr>
            <tr>
                <th>Barcode <small>(Auto Generated)</small></th>
                <th>Serial No</th>
                <th>Receive/Pending</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if(sizeof($assets)>0)
            {
                foreach($assets as $asset)
                {
                    ?>
                    <tr>
                        <td><?php echo Barcode_helper::get_barcode_asset($item['prefix'], $asset['barcode'])?></td>
                        <td><?php echo $asset['serial_no']?></td>
                        <td></td>
                    </tr>
                <?php
                }
                for($i=0; $i<($item['quantity_total']-sizeof($assets));$i++)
                {
                    ?>
                    <tr>
                        <td></td>
                        <td><input type="text" name="items[<?php echo $i;?>][serial_no]" class="form-control" value=""/></td>
                        <td>
                            <div class="checkbox">
                                <label>
                                    <input type="radio" name="items[<?php echo $i;?>][receive]" value="<?php echo $CI->config->item('system_status_received')?>"><span class="label label-success"><?php echo $CI->config->item('system_status_received')?></span>
                                </label>
                                <label>
                                    <input type="radio" name="items[<?php echo $i;?>][receive]" value="<?php echo $CI->config->item('system_status_pending')?>" checked="true" ><span class="label label-danger"><?php echo $CI->config->item('system_status_pending')?></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                <?php
                }
            }
            else
            {
                for($i=0; $i<$item['quantity_total'];$i++)
                {
                    ?>
                    <tr>
                        <td></td>
                        <td><input type="text" name="items[<?php echo $i;?>][serial_no]" class="form-control" value=""/></td>
                        <td>
                            <div class="checkbox">
                                <label>
                                    <input type="radio" name="items[<?php echo $i;?>][receive]" value="<?php echo $CI->config->item('system_status_received')?>"><span class="label label-success"><?php echo $CI->config->item('system_status_received')?></span>
                                </label>
                                <label>
                                    <input type="radio" name="items[<?php echo $i;?>][receive]" value="<?php echo $CI->config->item('system_status_pending')?>" checked="true" ><span class="label label-danger"><?php echo $CI->config->item('system_status_pending')?></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                <?php
                }
            }
            ?>
            </tbody>
        </table>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Warranty Date Start</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_warranty_start]" id="date_warranty_start" value="<?php echo System_helper::display_date($item['date_warranty_start'])?>" class="form-control datepicker" readonly>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Warranty End Start</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_warranty_end]" id="date_warranty_end" value="<?php echo System_helper::display_date($item['date_warranty_end'])?>" class="form-control datepicker" readonly>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Depreciation Rate</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[depreciation_rate]" id="depreciation_rate" value="<?php echo $item['depreciation_rate']?>" class="form-control float_type_positive" >
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Depreciation Year</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[depreciation_year]" id="depreciation_year" value="<?php echo $item['depreciation_year']?>" class="form-control float_type_positive" >
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
        $(".datepicker").datepicker({dateFormat : display_date_format,changeMonth: true,changeYear: true});
    });
</script>