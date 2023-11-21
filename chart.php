<?php

include 'conn.php';

$fetchDailyData = "SELECT
    FLOOR(HOUR(date) / 2) * 2 AS time_slot_end,
    MAX(voltage) AS voltage,
    MAX(amp) AS amp,
    device_id
FROM q_power
WHERE date >= CURDATE() AND device_id ='37C:9E:BD:C0:AE:FC:VTLI16'
GROUP BY time_slot_end, device_id
ORDER BY time_slot_end";

$result = mysqli_query($connection, $fetchDailyData);

$fetchData = [];
while ($row = mysqli_fetch_assoc($result)) {
    $fetchData[] = $row;
}

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
</head>

<body>
    <div>
        <h1></h1>
        <canvas id="voltage"></canvas>
        <canvas id="amp"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const phpData = <?php echo $data; ?>;
        const phpLabel = <?php echo $labels; ?>;
        const ampData = <?php echo $dataAmp; ?>;
        createChart(phpData, phpLabel, 'voltage', 'Voltage','skyblue', 220, 230, 'darkred', 'orange');
        createChart(ampData, phpLabel, 'amp', 'Ampear', 'skyblue', 0 , 100 ,'darkred', 'orange');

        function createChart(data, label, canvax_id, chartLabel, color, min, max, colorAbove, colorBelow, ) {
            const ctx = document.getElementById(canvax_id);
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label,
                    datasets: [{
                        label: chartLabel,
                        data: data,
                        borderWidth: 3,
                        backgroundColor: data.map(value => (value > max ? colorAbove : color ) || (value < min ? colorBelow : color))
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
    </script>
</body>

</html>