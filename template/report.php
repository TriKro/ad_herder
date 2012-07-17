<?php
/*
Copyright 2011 Tristan Kromer, Peter Backx (email : tristan@grasshopperherder.com)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
*/
?>
<div class="wrap">
	<?php screen_icon('options-general'); ?>
	<h2>AdHerder Engagement Reports</h2>
	
	<?php 
		if($message) {
			echo '<div id="message" class="error">' . $message . '</div>';
		} 
		include(plugin_dir_path(__FILE__).'/../template/feedback.php');
	?>
	
	<div id="dashboard">
		<table>
			<tr>
				<td style="width: 300px; vertical-align: top;">
					<div id="control-report"></div>
					<div id="control-status"></div>
					<div id="control-impressions"></div>
					<div id="control-clicks"></div>
				</td>
				<td>
					<div id="chart-report"></div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div id="chart-legend"></div>
				</td>
			</tr>
		</table>
	</div>
	
	<form id="adherder_switch_status" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<input type="hidden" name="adherder_switch_ad_id" id="adherder_switch_ad_id" />
		<input type="hidden" name="adherder_switch_status" />
	</form>
	<form id="adherder_switch_report" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<input type="hidden" name="adherder_switch_report_ad_id" id="adherder_switch_report_ad_id" />
	</form>
	<form id="adherder_clear_history" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<input type="hidden" name="adherder_clear_ad_id" id="adherder_clear_ad_id" />
		<input type="hidden" name="adherder_clear_history" />
	</form>
	<form id="adherder_cleanup_old_data" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<p><input type="submit" name="adherder_cleanup_old_data" value="Clean up old impression tracking data" class="button-secondary" /></p>
	</form>
	
<script type="text/javascript">
      google.load("visualization", "1.1", {packages:["corechart", "table", "controls"]});
      google.setOnLoadCallback(drawChart);
      var table, data;
      function drawChart() {
        data = new google.visualization.DataTable();
        data.addColumn('string', 'Ad id');
        data.addColumn('string', 'Title');
        data.addColumn('string', 'Status');
        data.addColumn('number', 'Impressions');
        data.addColumn('number', 'Clicks');
        data.addColumn('number', 'Conversion %');
        data.addColumn('number', 'Confidence %');
        data.addColumn('boolean', 'Relevant?');
        data.addColumn('string', 'Report?');
        data.addColumn('string', 'Online?');
        data.addColumn('string', 'Clear data');
        data.addColumn('string', 'In Report Data?');
        data.addRows(<?php echo count($reports); ?>);
        <?php 
        $count = 0;
        foreach($reports as $report) {
          echo "data.setValue(" . $count . ", 0, '" . $report->id . "');\n";
          echo "data.setValue(" . $count . ", 1, '" . $report->post_title . "');\n";
          echo "data.setValue(" . $count . ", 2, '" . $report->post_status . "');\n";
          echo "data.setValue(" . $count . ", 3,  " . $report->impressions . ");\n";
          echo "data.setValue(" . $count . ", 4,  " . $report->clicks . ");\n";
          echo "data.setValue(" . $count . ", 5,  " . $report->conversion . ");\n";
          echo "data.setValue(" . $count . ", 6,  " . $report->confidence . ");\n";
          echo "data.setValue(" . $count . ", 7,  " . $report->relevant . ");\n";
          $switchValue = $report->in_report ? 'checked="checked"' : '';
          $switchValue = '<input type="checkbox" ' . $switchValue . ' onclick="switchReport(' . $report->id . ')">';
          echo "data.setValue(" . $count . ", 8,  '" . $switchValue . "');\n";
          $switchValue = "";
          switch($report->post_status) {
            case 'publish' : $switchValue = 'checked="checked"'; break;
            case 'pending' : $switchValue = ''; break;
          }
          if($report->post_status == "publish" || $report->post_status == "pending") {
            $switchValue = '<input type="checkbox" ' . $switchValue . ' onclick="switchStatus(' . $report->id . ')">';
          }
          echo "data.setValue(" . $count . ", 9,  '" . $switchValue . "');\n";
          $clearValue = '<input type="checkbox" onclick="clearHistory(' . $report->id . ')">';
          echo "data.setValue(" . $count . ", 10, '" . $clearValue . "');\n";
          echo "data.setValue(" . $count . ", 11, '" . ($report->in_report?"Yes":"No") . "');\n";
          $count++;
        } ?>

		var reportPicker = new google.visualization.ControlWrapper({
			'controlType': 'CategoryFilter',
			'containerId': 'control-report',
			'options'    : {
				'filterColumnLabel': 'In Report Data?',
				'ui': {
					'labelStacking'  : 'vertical',
					'allowTyping'    : false,
					'allowMultiple'  : false
				}
			},
			'state': { 'selectedValues' : ['Yes'] }
		});

        var statusPicker = new google.visualization.ControlWrapper({
          'controlType': 'CategoryFilter',
          'containerId': 'control-status',
          'options'    : {
            'filterColumnLabel': 'Status',
            'ui': {
              'labelStacking'  : 'vertical',
              'allowTyping'    : false,
              'allowMultiple'  : false
            }
          }
        });

        var impressionsSlider = new google.visualization.ControlWrapper({
          'controlType': 'NumberRangeFilter',
          'containerId': 'control-impressions',
          'options': {
            'filterColumnLabel': 'Impressions',
            'ui': {'labelStacking': 'vertical'}
          }
        });

        var clicksSlider = new google.visualization.ControlWrapper({
          'controlType': 'NumberRangeFilter',
          'containerId': 'control-clicks',
          'options': {
            'filterColumnLabel': 'Clicks',
            'ui': {'labelStacking': 'vertical'}
          }
        });

        var chart = new google.visualization.ChartWrapper({
          'chartType'  : 'ColumnChart',
          'containerId': 'chart-report',
          'options'    : {
            'width'    : 400,
            'height'   : 240,
            'title'    : 'Ad engagement',
          },
          'view'       : {
            'columns'  : [0, 3, 4]
          }
        });

        table = new google.visualization.ChartWrapper({
          'chartType'  : 'Table',
          'containerId': 'chart-legend',
          'options'    : {
            'allowHtml'     : true
          },
          'view'       : {
            'columns'  : [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
          }
        });

        new google.visualization.Dashboard(document.getElementById('dashboard'))
          .bind([reportPicker, statusPicker, impressionsSlider, clicksSlider], [table, chart])
          .draw(data);
      }
      function switchStatus(adId) {
        if(!adId) return;
        jQuery('#adherder_switch_ad_id').val(adId);
        jQuery('#adherder_switch_status').submit();
      }
      function switchReport(adId) {
		  if(!adId) return;
		  jQuery('#adherder_switch_report_ad_id').val(adId);
		  jQuery('#adherder_switch_report').submit();		  
	  }
      function clearHistory(adId) {
        if(!adId) return;
        jQuery('#adherder_clear_ad_id').val(adId);
        jQuery('#adherder_clear_history').submit();
      }
</script>
</div>
