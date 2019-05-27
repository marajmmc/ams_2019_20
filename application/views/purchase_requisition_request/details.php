<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>'Pending List',
    'href'=>site_url($CI->controller_url)
);
$action_buttons[]=array
(
    'label'=>'All List',
    'href'=>site_url($CI->controller_url.'/index/list_all')
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
</div>
<div class="clearfix"></div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
    });
</script>

