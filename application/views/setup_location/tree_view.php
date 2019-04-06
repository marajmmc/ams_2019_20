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
                <div id="chart_div"></div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $.getScript("https://www.gstatic.com/charts/loader.js", function () {
        initiate_organogram();
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
        <?php if($locations){ ?>
            data.addRows([
                <?php foreach($locations as $location){
                    echo "[ {v:'{$location['id']}', f:'{$location['name']}'}, '{$location['parent']}', 'ID: {$location['id']}, Order: {$location['ordering']}'],";
                } ?>
            ]);
        <?php } ?>
        // Create the chart.
        var chart = new google.visualization.OrgChart(document.getElementById('chart_div'));
        // Draw the chart, setting the allowHtml option to true for the tooltips.
        chart.draw(data, {allowHtml: true});
    }
</script>
