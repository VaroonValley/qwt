let myChartVoltage;
let myChartAmp;
let chartBySlot;
const urlParams = new URLSearchParams(window.location.search);
const dateParam = urlParams.get('date');
const device_id = urlParams.get('device_id');
const currentDate = new Date().toISOString().slice(0, 10);
const date = dateParam ? dateParam : currentDate;

function voltageChartByDate(voltageData, labelData) {
    if (myChartVoltage) {
        myChartVoltage.destroy();
    }
    let min = 220;
    let max = 230;
    let minColor = 'orange';
    let maxColor = 'red';
    let color = 'skyblue';
    myChartVoltage = createChart(voltageData, labelData, 'voltage', 'Voltage', color, min, max, maxColor, minColor, (id) => { chartByTimeSlot(id, true) });
}

function ampChartByDate(ampData, labelData) {
    if (myChartAmp) {
        myChartAmp.destroy();
    }
    let min = 80;
    let max = 100;
    let minColor = 'orange';
    let maxColor = 'red';
    let color = 'skyblue';
    myChartAmp = createChart(ampData, labelData, 'amp', 'Ampere', color, min, max, maxColor, minColor, (id) => { chartByTimeSlot(id, false) });
}

function chartByTimeSlot(time_slot, isVoltage) {
    if (chartBySlot) {
        chartBySlot.destroy();
    }
    $.ajax({
        url: 'getPowerByTime.php',
        method: 'GET',
        data: {
            date: date,
            device_id: device_id,
            time_slot: time_slot * 2
        },
        success: function (response) {
            let min = 220;
            let max = 230;
            let chartLabel = 'Voltage';
            let fetchData = 'voltage';
            if (!isVoltage) {
                min = 80;
                max = 100;
                chartLabel = 'Ampere';
                fetchData = 'amp';
            }
            document.getElementById("chartPopUp").style.display = "block";
            let data = [];
            let label = [];
            response.forEach((item) => {
                label.push(item['date'].slice(10, item['date'].length));
                data.push(item[fetchData]);
            });

            chartBySlot = createChart(data, label, 'chartBySlot', chartLabel, 'blue', min, max, 'red', 'orange')
        },
        error: function (xhr, status, error) {
            // Handle errors
            console.error(error);
        }
    });
}

function applyChart(response) {
    let voltage = new Array(12).fill(0);
    let amp = new Array(12).fill(0);
    Object.keys(response).forEach(item => {
        let index = parseInt(item) / 2;
        voltage[index] = response[item]['max_voltage'];
        amp[index] = response[item]['max_amp'];
    });
    let labels = Array.from({ length: 13 }, (_, i) => i * 2);
    voltageChartByDate(voltage, labels);
    ampChartByDate(amp, labels);
}

function fetchData() {
    $.ajax({
        url: 'getPowerByDate.php',
        method: 'GET',
        data: {
            date: date,
            device_id: device_id
        },
        success: function (response) {
            applyChart(response);
        },
        error: function (xhr, status, error) {
            // Handle errors
            console.error(error);
        }
    });
}
function createChart(data, label, canvax_id, chartLabel, color, min, max, colorAbove, colorBelow, onChartClick) {
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
            onClick: (evt, activeEls) => {
                if (activeEls && activeEls.length > 0 && activeEls[0].index) {
                    if (onChartClick) { onChartClick(activeEls[0].index); }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

fetchData();
setInterval(fetchData, 1000 * 60);