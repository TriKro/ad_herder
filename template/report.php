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
					<select>
						<option value="none">Bulk Actions</option>
						<option value="publish">Publish</option>
						<option value="pending">Unpublish</option>
						<option value="in_report">Include in report</option>
						<option value="not_in_report">Remove from report</option>
						<option value="clear_data">Clear data</option>
					</select>
					<button class="button-secondary apply-bulk" >Apply</button><br/>
					<div id="chart-legend"></div>
					<select>
						<option value="none">Bulk Actions</option>
						<option value="publish">Publish</option>
						<option value="pending">Unpublish</option>
						<option value="in_report">Include in report</option>
						<option value="not_in_report">Remove from report</option>
						<option value="clear_data">Clear data</option>
					</select>
					<button class="button-secondary apply-bulk" >Apply</button><br/>
				</td>
			</tr>
		</table>
	</div>
	
	<form id="adherder_bulk_action_form" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<input type="hidden" name="adherder_bulk_ad_ids" id="adherder_bulk_ad_ids" />
		<input type="hidden" name="adherder_bulk_action" id="adherder_bulk_action" />
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
          echo "data.setValue(" . $count . ", 8, '" . ($report->in_report?"Yes":"No") . "');\n";
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
            'columns'  : [0, 1, 2, 3, 4, 5, 6, 7]
          }
        });

        new google.visualization.Dashboard(document.getElementById('dashboard'))
          .bind([reportPicker, statusPicker, impressionsSlider, clicksSlider], [table, chart])
          .draw(data);
      }
      jQuery(document).ready(function($) {
		  $('.apply-bulk').click(function() {
			  var action = $(this).prev().val();
			  $('#adherder_bulk_action').val(action);
			  if("none" != action) {
				  var selection = table.getChart().getSelection();
				  if(selection.length != 0) {
					  var ids = "";
					  $.each(selection, function(i, obj) {
						  if(i!=0) {
							  ids += ',';
						  }
						  ids += table.getDataTable().getValue(obj.row,0);
					  });
					  $('#adherder_bulk_ad_ids').val(ids);
					  $('#adherder_bulk_action_form').submit();
				  }
			  }
		  });
	  });
</script>
</div>
