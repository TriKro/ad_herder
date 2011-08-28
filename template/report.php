<div>
<h2>Calls to Action Engagement Reports</h2>
<?php if($message) {
  echo "<div id='setting-error-settings_updated' class='updated settings-error'><p><strong>" . $message . "</strong></p></div>";
} ?>
<div id="dashboard">
  <table>
    <tr>
      <td style="width: 300px; vertical-align: top;">
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
<form id="ctopt_switchStatus" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
  <input type="hidden" name="ctopt_switchCallId" id="ctopt_switchCallId" />
  <input type="hidden" name="ctopt_switchStatus" />
</form>
<form id="ctopt_clearHistory" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
  <input type="hidden" name="ctopt_clearCallId" id="ctopt_clearCallId" />
  <input type="hidden" name="ctopt_clearHistory" />
</form>
<form id="ctopt_cleanupOldTracking" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
  <p><input type="submit" name="ctopt_cleanupOldTracking" value="Clean up old impression tracking data" /></p>
</form>
<script type="text/javascript">
      google.load("visualization", "1.1", {packages:["corechart", "table", "controls"]});
      google.setOnLoadCallback(drawChart);
      var table, data;
      function drawChart() {
        data = new google.visualization.DataTable();
        data.addColumn('string', 'Call id');
        data.addColumn('string', 'Title');
        data.addColumn('string', 'Status');
        data.addColumn('number', 'Impressions');
        data.addColumn('number', 'Clicks');
        data.addColumn('number', 'Conversion %');
        data.addColumn('string', 'Switch status');
        data.addColumn('string', 'Clear history');
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
          $switchValue = "";
          switch($report->post_status) {
            case 'publish' : $switchValue = 'Go offline'; break;
            case 'pending' : $switchValue = 'Go online'; break;
          }
          if($switchValue != "") {
            $switchValue = "<a href=\"#\" onclick=\"switchStatus(" . $report->id . ")\">" . $switchValue . "</a>";
          }
          echo "data.setValue(" . $count . ", 6,  '" . $switchValue . "');\n";
          $clearValue = "<a href=\"#\" onclick=\"clearHistory(" . $report->id . ")\">remove data</a>";
          echo "data.setValue(" . $count . ", 7,  '" . $clearValue . "');\n";
          
          $count++;
        } ?>

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
          },
          'state': { 'selectedValues' : ['publish'] }
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
            'title'    : 'Call engagement',
          },
          'view'       : {
            'columns'  : [0, 3, 4]
          }
        });

        table = new google.visualization.ChartWrapper({
          'chartType'  : 'Table',
          'containerId': 'chart-legend',
          'options'    : {
            'allowHtml': true
          }
        });

        new google.visualization.Dashboard(document.getElementById('dashboard'))
          .bind([statusPicker, impressionsSlider, clicksSlider], [table, chart])
          .draw(data);
      }
      function switchStatus(callId) {
        if(!callId) return;
        jQuery('#ctopt_switchCallId').val(callId);
        jQuery('#ctopt_switchStatus').submit();
      }
      function clearHistory(callId) {
        if(!callId) return;
        jQuery('#ctopt_clearCallId').val(callId);
        jQuery('#ctopt_clearHistory').submit();
      }
</script>
</div>
