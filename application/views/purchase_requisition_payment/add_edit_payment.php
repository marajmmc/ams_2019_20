<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url.'/index/list_payment/'.$item['item_id'])
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
    $action_buttons[]=array
    (
        'type'=>'button',
        'label'=>$CI->lang->line("ACTION_SAVE_NEW"),
        'id'=>'button_action_save_new',
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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_payment');?>" method="post">
    <input type="hidden" id="item_id" name="item_id" value="<?php echo $item['item_id']?>" />
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <?php
        echo $CI->load->view("info_basic", $data=array(), true);
        ?>
        <div class="row">
            <div class="col-xs-6">
                <button type="button" class="btn btn-success btn-xs">Total Amount: <?php echo System_helper::get_string_amount($amount_total);?></button>
                <button type="button" class="btn btn-warning btn-xs">Paid Amount: <?php echo System_helper::get_string_amount($amount_total_paid);?></button>
                <button type="button" class="btn btn-danger btn-xs">Due Amount: <?php echo System_helper::get_string_amount($amount_total_due);?></button>
            </div>
        </div>
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_DATE_PAYMENT');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_payment]" id="date_payment" class="form-control datepicker" value="<?php echo System_helper::display_date($item['date_payment']);?>" readonly />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PAYMENT_ADVANCE');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="radio" name="item[payment_advance]" id="payment_advance" class=" " value="1" <?php if($item['payment_advance']==1){echo "checked='checked'";}?> /> Yes
                <input type="radio" name="item[payment_advance]" id="payment_advance" class=" " value="0" <?php if($item['payment_advance']==0){echo "checked='checked'";}?> /> No
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <br/>
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <strong class="pull-right bg-success">Total Amount: <span id="amount_total"><?php echo System_helper::get_string_amount($amount_total)?></span></strong>
                <input type="text" name="item[amount]" id="amount" class="form-control float_type_positive " value="<?php echo $item['amount'];?>" oninput="calculate_total()" />
                <strong class="pull-right bg-success">Due Amount: <span id="amount_due"><?php echo System_helper::get_string_amount($amount_total_due)?></span></strong>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REMARKS');?> </label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[remarks]" id="remarks" class="form-control" ><?php echo $item['remarks'];?></textarea>
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
    });

    function calculate_total()
    {
        $('#amount_due').html('0.00');
        var amount_total=parseFloat(<?php echo $amount_total;?>);
        var amount=parseFloat($('#amount').val());
        var amount_due=get_string_amount(amount_total-amount);
        $('#amount_due').html(amount_due);
    }
</script>

