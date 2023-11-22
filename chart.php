<?php

include("getPower.php");

$fetchData = getPowerByDate();

$data = array_fill(0, 12, 0);
$amp = array_fill(0, 12, 0);
foreach ($fetchData as $item) {
    $timeSlotEnd = $item['time_slot_end'];
    $value = $item['voltage'];
    $ampValue = $item['amp'];
    $index = floor($timeSlotEnd / 2);
    if ($index < 12) {
        $data[$index] = $value;
        $amp[$index] = $ampValue;
    }
}
$data = json_encode($data);
$labels = json_encode(range(0, 24, 2));
$dataAmp = json_encode($amp);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/style.css">
</head>

<body>
    <div>
        <h3><?php echo $_REQUEST['date'] ?></h3>
        <div class="flex-container">
            <div>
                <?php if ($_REQUEST['date'] > date('Y-m-d', strtotime('- 6 day'))) { ?>
                    <a href="/chart.php?device_id=<?php echo $_REQUEST['device_id'] ?>&date=<?php echo date('Y-m-d', strtotime($_REQUEST['date'] . '- 1 day')) ?>" class="link-button">Prevoious</a>
                <?php } ?>
            </div>
            <div>
                <?php if ($_REQUEST['date'] < date('Y-m-d')) { ?>
                    <a href="/chart.php?device_id=37C:9E:BD:C0:AE:FC:VTLI16&date=<?php echo date('Y-m-d', strtotime($_REQUEST['date'] . '+ 1 day')) ?>" class="link-button">Next</a>
                <?php } ?>
            </div>
        </div>
        <canvas id="voltage"></canvas>
        <canvas id="amp"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        createChart();

        function createChart() {
            const phpData = <?php echo $data; ?>;
            const phpLabel = <?php echo $labels; ?>;
            const ampData = <?php echo $dataAmp; ?>;
            createChart(phpData, phpLabel, 'voltage', 'Voltage', 'skyblue', 220, 230, 'red', 'orange');
            createChart(ampData, phpLabel, 'amp', 'Ampear', 'skyblue', 0, 100, 'red', 'orange');

            function createChart(data, label, canvax_id, chartLabel, color, min, max, colorAbove, colorBelow) {
                const ctx = document.getElementById(canvax_id);
                return new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: label,
                        datasets: [{
                            label: chartLabel,
                            data: data,
                            backgroundColor: data.map(value => {
                                if (value > max) {
                                    return colorAbove;
                                } else if (value < min) {
                                    return colorBelow;
                                } else {
                                    return color;
                                }
                            })
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
                return myChart;
            }
        }
    </script>
</body>

</html>