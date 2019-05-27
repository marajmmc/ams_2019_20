<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI=& get_instance();
$action_buttons=array();
$action_buttons[]=array
(
    'label'=>$CI->lang->line("ACTION_BACK"),
    'href'=>site_url($CI->controller_url)
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
<form id="save_form" action="<?php echo site_url($CI->controller_url.'/index/save');?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']?>" />
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0" />
    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="row show-grid" >
            <div class="col-xs-4">
                <label class="control-label pull-right">Responsible User Group</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <?php
                if(sizeof($user_responsible_groups)==1)
                {
                    ?>
                    <label class="control-label"><?php echo $user_responsible_groups[0]['text']?></label>
                    <input type="hidden" name="item[user_responsible_group_id]" id="user_responsible_group_id" value="<?php echo $user_responsible_groups[0]['value']?>" />
                <?php
                }
                else
                {
                    ?>
                    <select id="user_responsible_group_id" name="item[user_responsible_group_id]" class="form-control">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                        <?php
                        foreach($user_responsible_groups as $user_group)
                        {
                            ?>
                            <option value="<?php echo $user_group['value'];?>" <?php if($user_group['value']==$item['user_responsible_group_id']){echo "selected='selected'";}?>><?php echo $user_group['text'];?></option>
                        <?php
                        }
                        ?>
                    </select>
                <?php
                }
                ?>

            </div>
        </div>
        <?php
        if($item['id']>0)
        {
            $categories_all=Category_helper::get_category_parent_children();
            if(isset($categories_all['parents'][$item['category_id']]))
            {
                $category_parent=$categories_all['parents'][$item['category_id']];
                $serial=1;
                for($i=sizeof($category_parent); $i>=0; $i--)
                {
                    $child_id=isset($category_parent[$i])?$category_parent[$i]:0;
                    if(isset($categories_all['category'][$child_id]))
                    {
                        $sub_categories=$categories_all['category'][$child_id];
                        if($serial==1)
                        {
                            ?>
                            <div class="row show-grid" id="category_id_container_<?php echo $serial;?>" data-current-id="<?php echo $serial;?>">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right">Category</label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <select id="parent_id__<?php echo $serial;?>" name="categories[parent_id][]" class="form-control system_button_add_more" data-current-id="<?php echo $serial;?>">
                                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                        <?php
                                        foreach($categories_all['children'][$sub_categories['parent']] as $sub_category)
                                        {
                                            ?>
                                            <option value="<?php echo $categories_all['category'][$sub_category]['id']?>" <?php if($categories_all['category'][$sub_category]['id']==$sub_categories['id']){echo "selected='selected'";}?>><?php echo $categories_all['category'][$sub_category]['name']?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        <?php
                        }
                        else
                        {
                            ?>
                            <div class="row show-grid div_removable" id="category_id_container_<?php echo $serial;?>" data-current-id="<?php echo $serial;?>">
                                <div class="col-xs-4">
                                    <label class="control-label pull-right">Sub Category </label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <select id="parent_id_<?php echo $serial;?>" name="categories[parent_id][]" class="form-control system_button_add_more" data-current-id="<?php echo $serial;?>">
                                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                                        <?php
                                        foreach($categories_all['children'][$sub_categories['parent']] as $sub_category)
                                        {
                                            ?>
                                            <option value="<?php echo $categories_all['category'][$sub_category]['id']?>"<?php if($categories_all['category'][$sub_category]['id']==$sub_categories['id']){echo "selected='selected'";}?>><?php echo $categories_all['category'][$sub_category]['name']?></option>
                                        <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        <?php
                        }
                        $serial++;
                    }
                }
                ?>
                <div class="row show-grid div_removable" id="category_id_container_<?php echo $serial;?>" data-current-id="<?php echo $serial;?>">
                    <div class="col-xs-4">
                        <label class="control-label pull-right">Sub Category </label>
                    </div>
                    <div class="col-sm-4 col-xs-8">
                        <select id="parent_id_<?php echo $serial;?>" name="categories[parent_id][]" class="form-control system_button_add_more" data-current-id="<?php echo $serial;?>">
                            <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                            <?php
                            foreach($categories_all['children'][$categories_all['category'][$item['category_id']]['parent']] as $sub_category)
                            {
                                ?>
                                <option value="<?php echo $categories_all['category'][$sub_category]['id']?>" <?php if($categories_all['category'][$sub_category]['id']==$categories_all['category'][$item['category_id']]['id']){echo "selected='selected'";}?>><?php echo $categories_all['category'][$sub_category]['name']?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            <?php
            }
        }
        else
        {
            ?>
            <div class="row show-grid" id="category_id_container_1" data-current-id="1">
                <div class="col-xs-4">
                    <label class="control-label pull-right">Categories</label>
                </div>
                <div class="col-sm-4 col-xs-8">
                    <select id="parent_id_1" name="categories[parent_id][]" class="form-control system_button_add_more" data-current-id="1">
                        <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    </select>
                </div>
            </div>
        <?php
        }
        ?>
        <div id="items_container">

        </div>
        <!--<div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php /*echo $CI->lang->line('LABEL_DATE');*/?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[date_requisition]" id="date_requisition" class="form-control datepicker" value="<?php /*echo System_helper::display_date($item['date_requisition']);*/?>" readonly />
            </div>
        </div>-->
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right">Supplier</label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <select id="supplier_id" name="item[supplier_id]" class="form-control">
                    <option value=""><?php echo $CI->lang->line('SELECT');?></option>
                    <?php
                    foreach($suppliers as $supplier)
                    {
                        ?>
                        <option value="<?php echo $supplier['value']?>" <?php if($supplier['value']==$item['supplier_id']){echo "selected='selected'";}?>><?php echo $supplier['text']?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_QUANTITY_TOTAL');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[quantity_total]" id="quantity_total" class="form-control integer_type_positive" value="<?php echo $item['quantity_total'];?>" oninput="calculate_total()" />
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_AMOUNT_PRICE_UNIT');?><span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <input type="text" name="item[amount_price_unit]" id="amount_price_unit" class="form-control float_type_positive " value="<?php echo $item['amount_price_unit'];?>" oninput="calculate_total()" />
                <strong class="pull-right bg-success">Total Price: <span id="amount_price_total"><?php echo System_helper::get_string_amount($item['amount_price_total'])?></span></strong>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_SPECIFICATION');?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[specification]" id="specification" class="form-control" ><?php echo $item['specification'];?></textarea>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_REASON');?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-sm-4 col-xs-8">
                <textarea name="item[reason]" id="reason" class="form-control" ><?php echo $item['reason'];?></textarea>
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
<div id="system_content_add_more" style="display: none;">
    <div style="" class="row show-grid div_removable" >
        <div class="col-xs-4">
            <label class="control-label pull-right">Sub Category</label>
        </div>
        <div class="col-sm-4 col-xs-8">
            <select class="form-control parent_id system_button_add_more" >
                <option value=""><?php echo $CI->lang->line('SELECT');?></option>
            </select>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function ()
    {
        system_off_events();
        system_preset({controller:'<?php echo $CI->router->class; ?>'});
        $(".datepicker").datepicker({dateFormat : display_date_format});

        var categories=<?php echo $categories?>;
        $("#parent_id_1").html(drop_down_generator(categories[0]));

        $(document).off("change", ".system_button_add_more");
        $(document).on("change", ".system_button_add_more", function(event)
        {
            var parent_id=$(this).val();
            var category_level=parseInt($(this).attr('data-current-id'));
            $('.div_removable').each(function(index, element)
            {
                var container_data_category_level=parseInt($(element).attr('data-current-id'));
                if(container_data_category_level>category_level)
                {
                    //$(element).remove();
                    $("#category_id_container_"+container_data_category_level).remove();
                }
            });
            category_level=category_level+1;
            if(parent_id>0)
            {
                if(categories[parent_id]!==undefined)
                {
                    var content_id='#system_content_add_more';
                    $(content_id+' .div_removable').attr('id','category_id_container_'+category_level);
                    $(content_id+' .div_removable').attr('data-current-id',category_level);

                    $(content_id+' .parent_id').attr('id','parent_id_'+category_level);
                    $(content_id+' .parent_id').attr('name','categories[parent_id][]');
                    $(content_id+' .parent_id').attr('data-current-id',category_level);

                    var html=$(content_id).html();
                    $("#items_container").append(html);

                    $(content_id+' .div_removable').removeAttr('id');
                    $(content_id+' .div_removable').removeAttr('data-current-id');

                    $(content_id+' .parent_id').removeAttr('id');
                    $(content_id+' .parent_id').removeAttr('name');
                    $(content_id+' .parent_id').removeAttr('data-current-id');

                    $('#parent_id_'+category_level).html(drop_down_generator(categories[parent_id]));
                }
                else
                {
                    //console.log(parent_id+'=> no child')
                }
            }
        });
    });
    function drop_down_generator(items)
    {
        var dropdown_html='<option value="">Select</option>';
        $.each(items,function(key, value)
        {
            dropdown_html+='<option value="'+key+'"';
            dropdown_html+='>'+value+'</option>';
        });
        return dropdown_html;
    }
    function calculate_total()
    {
        $('#amount_price_total').html('0.00');
        var quantity_total=parseInt($('#quantity_total').val());
        var amount_price_unit=parseFloat($('#amount_price_unit').val());
        var amount_price_total=get_string_amount(quantity_total*amount_price_unit);
        $('#amount_price_total').html(amount_price_total);
    }
</script>

