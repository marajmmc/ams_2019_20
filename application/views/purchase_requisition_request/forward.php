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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save_forward');?>" method="post">
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
        <?php
        if(sizeof($files)>0)
        {
            ?>
            <table class="table table-bordered table-responsive system_table_details_view">
                <thead>
                <tr>
                    <th style="width: 10px;">SL#</th>
                    <th style="width: 400px;">File Name</th>
                    <th>Remarks</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $serial_no=0;
                foreach($files as $file)
                {
                    ++$serial_no;
                    ?>
                    <tr>
                        <td><?php echo $serial_no;?></td>
                        <td>
                            <a href="<?php echo $CI->config->item('system_base_url_picture') . $file['file_location']; ?>" target="_blank" class="external blob">
                                <?php echo $file['file_name']?>
                            </a>
                        </td>
                        <td><?php echo nl2br($file['remarks'])?></td>
                    </tr>
                <?php
                }
                ?>
                </tbody>
            </table>
            <hr/>
        <?php
        }
        ?>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_FORWARD');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="status_forward" class="form-control" name="item[status_forward]">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <option value="<?php echo $this->config->item('system_status_forwarded')?>"><?php echo $this->config->item('system_status_forwarded')?></option>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">

            </div>
            <div class="col-sm-4 col-xs-4">
                <div class="action_button pull-right">
                    <button id="button_action_save" type="button" class="btn" data-form="#save_form" data-message-confirm="Are You Sure Requisition Forwarded?">Save</button>
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

    });
</script>

