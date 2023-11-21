<?php

$fetchDailyData = "SELECT
    FLOOR(HOUR(date) / 2) * 2 AS time_slot_end,
    SUM(voltage) AS voltage,
    SUM(amp) AS amp,
    device_id
FROM q_power
WHERE date >= CURDATE()
GROUP BY time_slot_end
ORDER BY time_slot_end;";
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
        <canvas id="voltage"></canvas>
        <canvas id="amp"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const phpData = <?php echo $data; ?>;
        const phpLabel = <?php echo $labels; ?>;
        const ampData = <?php echo $dataAmp; ?>;
        createChart(phpData, phpLabel, 'voltage');
        createChart(ampData, phpLabel, 'amp');

        function createChart(data, label, canvax_id) {
            const ctx = document.getElementById(canvax_id);
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: label,
                    datasets: [{
                        label: 'Voltage',
                        data: data,
                        borderWidth: 1
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