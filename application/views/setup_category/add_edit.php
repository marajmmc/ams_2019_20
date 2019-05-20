<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_SAVE"),
    'id' => 'button_action_save',
    'data-form' => '#save_form'
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_SAVE_NEW"),
    'id' => 'button_action_save_new',
    'data-form' => '#save_form'
);
$action_buttons[] = array(
    'type' => 'button',
    'label' => $CI->lang->line("ACTION_CLEAR"),
    'id' => 'button_action_clear',
    'data-form' => '#save_form'
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>
<form id="save_form" action="<?php echo site_url($CI->controller_url . '/index/save'); ?>" method="post">
    <input type="hidden" id="id" name="id" value="<?php echo $item['id']; ?>"/>
    <input type="hidden" id="system_save_new_status" name="system_save_new_status" value="0"/>

    <div class="row widget">
        <div class="widget-header">
            <div class="title">
                <?php echo $title; ?>
            </div>
            <div class="clearfix"></div>
        </div>

        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_NAME'); ?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="item[name]" id="name" class="form-control" value="<?php echo $item['name']; ?>"/>
            </div>
        </div>
        <div class="row show-grid">
            <div class="col-xs-4">
                <label class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PREFIX'); ?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-1">
                <input type="text" name="item[prefix]" id="prefix" class="form-control" maxlength="<?php echo $CI->prefix_length; ?>" value="<?php echo $item['prefix']; ?>"/>
            </div>
            <div class="col-xs-3" style="padding-left:0">
                <label class="control-label" style="font-weight:normal;margin-top:5px"><span style="color:#FF0000">( Must be 3 characters &amp; alphabetic )</span></label>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="parent" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_PARENT'); ?> &nbsp;</label>
            </div>
            <div class="col-xs-4">
                <select id="parent" name="item[parent]" data-placeholder="Select" class="form-control" tabindex="-1">
                    <option value="0">None</option>
                    <?php
                    foreach ($categories as $key => $category)
                    {
                        ?>
                        <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $item['parent'])? 'selected':''; ?>><?php echo $category['prefix'].$category['name']; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="ordering" class="control-label pull-right"><?php echo $CI->lang->line('LABEL_ORDER'); ?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <input type="text" name="item[ordering]" id="ordering" class="form-control" value="<?php echo $item['ordering'] ?>">
            </div>
        </div>
        <div style="" class="row show-grid">
            <div class="col-xs-4">
                <label for="status" class="control-label pull-right"><?php echo $CI->lang->line('STATUS'); ?> <span style="color:#FF0000">*</span></label>
            </div>
            <div class="col-xs-4">
                <select id="status" name="item[status]" class="form-control">
                    <option value="<?php echo $CI->config->item('system_status_active'); ?>" <?php echo ($item['status'] == $CI->config->item('system_status_active'))? 'selected':''; ?>><?php echo $CI->lang->line('ACTIVE') ?></option>
                    <option value="<?php echo $CI->config->item('system_status_inactive'); ?>" <?php echo ($item['status'] == $CI->config->item('system_status_inactive'))? 'selected':''; ?>><?php echo $CI->lang->line('INACTIVE') ?></option>
                </select>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
</form>
<script type="text/javascript">
    jQuery(document).ready(function () {
        system_preset({controller: '<?php echo $CI->router->class; ?>'});
    });
</script>
