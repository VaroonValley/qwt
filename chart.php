<?php
$date = isset($_REQUEST["date"]) ? $_REQUEST["date"] : date("Y-m-d");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/popupbox.css">
</head>

<body>
    <div>
        <h3><?php echo date('Y-m-d l', strtotime($date)) ?></h3>
        <div class="flex-container">
            <div>
                <?php if ($date > date('Y-m-d', strtotime('- 6 day'))) { ?>
                    <a href="/chart.php?device_id=<?php echo $_REQUEST['device_id'] ?>&date=<?php echo date('Y-m-d', strtotime($date . '- 1 day')) ?>" class="link-button">Prevoious</a>
                <?php } ?>
            </div>
            <div>
                <?php if ($date < date('Y-m-d')) { ?>
                    <a href="/chart.php?device_id=<?php echo $_REQUEST['device_id'] ?>&date=<?php echo date('Y-m-d', strtotime($date . '+ 1 day')) ?>" class="link-button">Next</a>
                <?php } ?>
            </div>
        </div>
        <canvas id="voltage"></canvas>
        <canvas id="amp"></canvas>
    </div>

    <div id="chartPopUp" class="overlay">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div class="overlay-content">
            <canvas id='chartBySlot' class='canvasPopUp'></canvas>
        </div>
    </div>

    <script src="./js/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./js/index.js"></script>

    <script>
        function closeNav() {
            document.getElementById("chartPopUp").style.display = "none";
        }
    </script>
</body>

</html>