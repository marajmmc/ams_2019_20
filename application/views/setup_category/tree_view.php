<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$CI =& get_instance();
$action_buttons = array();
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_BACK"),
    'href' => site_url($CI->controller_url)
);
$action_buttons[] = array(
    'label' => $CI->lang->line("ACTION_REFRESH"),
    'href' => site_url($CI->controller_url . '/index/tree_view')
);
$CI->load->view('action_buttons', array('action_buttons' => $action_buttons));
?>

<style>
    .google-visualization-orgchart-node,
    .google-visualization-orgchart-nodesel {
        border:none !important;
    }
    .google-visualization-orgchart-node span{color:#FF0000;white-space:nowrap}
    /*td.google-visualization-orgchart-node{white-space:nowrap}*/
</style>

<div class="row widget main-container">
    <div class="widget-header">
        <div class="title">
            <?php echo $title; ?>
        </div>
        <div class="clearfix"></div>
    </div>

    <div class="row show-grid row-container">
        <div class="col-lg-12">
            <div class="col-xs-12" style="overflow-x:scroll">
                <div id="chart_div" style="text-align:center">

                    <img style="display:inline-block; margin:100px 0" src="<?php echo str_replace($CI->config->item('system_site_root_folder'), 'login_2018_19', base_url('images/spinner.gif')); ?>">

                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        system_off_events();
        system_preset({controller: '<?php echo $CI->router->class; ?>'});

        $.getScript("https://www.gstatic.com/charts/loader.js", function(){
            initiate_organogram();
        });
    });

    function initiate_organogram() {
        google.charts.load('current', {packages: ["orgchart"]});
        google.charts.setOnLoadCallback(drawChart);
    }

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Name');
        data.addColumn('string', 'Manager');
        data.addColumn('string', 'ToolTip');
        <?php if($categories){ ?>
            data.addRows([
                <?php foreach($categories as $category){
                    echo "[ {v:'{$category['id']}', f:'{$category['name']}{$category['status']}'}, '{$category['parent']}', 'ID: {$category['id']}, Order: {$category['ordering']}'],";
                } ?>
            ]);
        <?php } ?>
        // Create the chart.
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        // Draw the chart, setting the allowHtml option to true for the tooltips.
        chart.draw(data, {allowHtml: true});
    }
</script>
