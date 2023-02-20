<?php
require "data.php";
require "db.php";

$url = $_GET['url'];
$data = Data::getStats(sprintf(Env::$DOCUMENT, $url));
$name = (new DB())->getNameByWorksheetUrl($url);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Статистика прибыли</title>
</head>
<body>
  <div id="chart_div"></div>
  <script type="text/javascript" src="loader.js"></script>
<script>
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {
    let result = [
      ['Дата', 'Прибыль']
    ];
    for (let row of <?php echo json_encode($data); ?>) {
      result.push([row[0], parseInt(row[1].replace(/\D/g, ''))]);
    }

    let data = google.visualization.arrayToDataTable(result);
    let options = {
      title: '<?php echo $name; ?> | Статистика прибыли',
      width: 400,
      height: 400,
      legend: {position: 'none'},
      hAxis: {title: 'Даты последней недели'},
      vAxis: {title: 'Сумма прибыли, ₽'}
    };

    let formatter = new google.visualization.NumberFormat({
      pattern: '# ₽'
    });
    formatter.format(data, 1);

    let chart = new google.visualization.ColumnChart(document.querySelector('#chart_div'));
    chart.draw(data, options);
  }
</script>
</body>
</html>