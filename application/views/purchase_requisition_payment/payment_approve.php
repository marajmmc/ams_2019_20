<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array(
    'type'=>'button',
    'label'=>$CI->lang->line("ACTION_CLEAR"),
    'id'=>'button_action_clear',
    'data-form'=>'#save_form'
);
$CI->load->view('action_buttons',array('action_buttons'=>$action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_payment_approve');?>" method="post">
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
        <div class="row">
            <div class="col-md-3">

            </div>
            <div class="col-md-6">
                <table class="table table-bordered table-responsive">
                    <thead>
                    <tr>
                        <th style="width: 10px;"><?php echo $CI->lang->line('LABEL_ID'); ?></th>
                        <th style="width: 150px"><?php echo $CI->lang->line('LABEL_DATE_PAYMENT'); ?></th>
                        <th style="width: 10px"><?php echo $CI->lang->line('LABEL_IS_ADVANCE'); ?></th>
                        <th style="width: 150px" class="text-right"><?php echo $CI->lang->line('LABEL_AMOUNT'); ?></th>
                        <th><?php echo $CI->lang->line('LABEL_REMARKS'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $amount_paid=0;
                    if(sizeof($payments)>0)
                    {

                        foreach($payments as $payment)
                        {
                            $amount_paid+=$payment['amount'];
                            ?>
                            <tr>
                                <td><?php echo $payment['id']?></td>
                                <td><?php echo System_helper::display_date($payment['date_payment'])?></td>
                                <td><?php echo $payment['is_advance']?></td>
                                <td class="text-right"><?php echo System_helper::get_string_amount($payment['amount'])?></td>
                                <td><?php echo nl2br($payment['remarks'])?></td>
                            </tr>
                        <?php
                        }
                    }
                    else
                    {
                        ?>
                        <tr>
                            <td colspan="5" class="text-center text-danger">No Payment.</td>
                        </tr>
                    <?php
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <th colspan="3" class="text-right">Total Paid:</th>
                        <th class="text-right"><?php echo System_helper::get_string_amount($amount_paid)?></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Total Price:</th>
                        <th class="text-right"><?php echo System_helper::get_string_amount($item['amount_price_total'])?></th>
                        <th></th>
                    </tr>
                    <tr>
                        <th colspan="3" class="text-right">Due:</th>
                        <th class="text-right"><?php echo System_helper::get_string_amount($item['amount_price_total']-$amount_paid)?></th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <!--<div class="col-md-3">&nbsp;</div>-->
        </div>
        <hr/>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_APPROVED');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status_payment_approve" class="form-control" name="item[status_payment_approve]">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <option value="<?php echo $this->config->item('system_status_approved')?>"><?php echo $this->config->item('system_status_approved')?></option>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><span id="label_remarks"><?php echo $CI->lang->line('LABEL_REMARKS');?></span> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks_payment]" id="remarks_payment" class="form-control" ><?php echo $item['remarks_payment'];?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Sure Payment Approved?">Save</button>
                </div>
            </div>
            <div class="col-sm-4 col-xs-4">

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
        $(".datepicker").datepicker({dateFormat : display_date_format});
        $("#status_payment_approve").on('change', function(){
            $('#label_remarks').html($(this).val()+' Remarks');
        })
    });
</script>

