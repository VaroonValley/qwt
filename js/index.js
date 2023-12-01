import {
  refetchInterval,
  mainChartType,
  detailedChartType,
  lowAmp,
  lowVoltage,
  lowDanger,
  idealAmp,
  idealVoltage,
  idealColor,
  maxAmp,
  maxVoltage,
  maxDanger,
  warning,
} from "../chartVariables.js";

let myChartVoltage;
let myChartAmp;
let chartBySlot;
const urlParams = new URLSearchParams(window.location.search);
const dateParam = urlParams.get("date");
const device_id = urlParams.get("device_id");
const currentDate = new Date().toISOString().slice(0, 10);
const date = dateParam ? dateParam : currentDate;

function voltageChartByDate(voltageData, labelData) {
  if (myChartVoltage) {
    myChartVoltage.destroy();
  }
  myChartVoltage = createChart(
    mainChartType,
    voltageData,
    labelData,
    "voltage",
    "Voltage",
    idealColor,
    lowVoltage,
    idealVoltage,
    maxVoltage,
    lowDanger,
    maxDanger,
    warning,
    (id) => {
      chartByTimeSlot(id, true);
    }
  );
}

function ampChartByDate(ampData, labelData) {
  if (myChartAmp) {
    myChartAmp.destroy();
  }
  myChartAmp = createChart(
    mainChartType,
    ampData,
    labelData,
    "amp",
    "Ampere",
    idealColor,
    lowAmp,
    idealAmp,
    maxAmp,
    lowDanger,
    maxDanger,
    warning,
    (id) => {
      chartByTimeSlot(id, false);
    }
  );
}

function chartByTimeSlot(time_slot, isVoltage) {
  if (chartBySlot) {
    chartBySlot.destroy();
  }
  $.ajax({
    url: "getPowerByTime.php",
    method: "GET",
    data: {
      date: date,
      device_id: device_id,
      time_slot: time_slot * 2,
    },
    success: function (response) {
      let low = lowVoltage;
      let ideal = idealVoltage;
      let max = maxVoltage;
      let chartLabel = "Voltage";
      let fetchData = "voltage";
      let thickness;
      if(window.innerWidth <= 768){
        thickness = 20
      }else{
        thickness = 50
      }
      if (!isVoltage) {
        low = lowAmp;
        ideal = idealAmp;
        max = maxAmp;
        chartLabel = "Ampere";
        fetchData = "amp";
      }
      document.getElementById("chartPopUp").style.display = "block";
      let data = [];
      let label = [];
      response.forEach((item) => {
        label.push(item["date"].slice(10, item["date"].length));
        data.push(item[fetchData]);
      });

      chartBySlot = createChart(
        detailedChartType,
        data,
        label,
        "chartBySlot",
        chartLabel,
        idealColor,
        low,
        ideal,
        max,
        lowDanger,
        maxDanger,
        warning,
        ()=>{},
        thickness
      );
    },
    error: function (xhr, status, error) {
      // Handle errors
      console.error(error);
    },
  });
}

function applyChart(response) {
  let voltage = new Array(12).fill(0);
  let amp = new Array(12).fill(0);
  Object.keys(response).forEach((item) => {
    let index = parseInt(item) / 2;
    voltage[index] = response[item]["max_voltage"];
    amp[index] = response[item]["max_amp"];
  });
  let labels = Array.from({ length: 13 }, (_, i) => i * 2 + ":00");
  voltageChartByDate(voltage, labels);
  ampChartByDate(amp, labels);
}

function fetchData() {
  $.ajax({
    url: "getPowerByDate.php",
    method: "GET",
    data: {
      date: date,
      device_id: device_id,
    },
    success: function (response) {
      applyChart(response);
    },
    error: function (xhr, status, error) {
      // Handle errors
      console.error(error);
    },
  });
}
function createChart(
  type,
  data,
  label,
  canvax_id,
  chartLabel,
  idealColor,
  low,
  ideal,
  max,
  lowDanger,
  maxDanger,
  warning,
  onChartClick,
  thickness
) {
  const ctx = document.getElementById(canvax_id);
  return new Chart(ctx, {
    type: type,
    data: {
      labels: label,
      datasets: [
        {
          label: chartLabel,
          data: data,
          barThickness: thickness ? thickness : 'flex',
          backgroundColor: data.map((value) =>
            value > max
              ? maxDanger
              : value > ideal && value <= max
              ? warning
              : value >= low
              ? idealColor
              : lowDanger
          ),
        },
      ],
    },
    options: {
      onClick: (evt, activeEls) => {
        const clickedIndex = activeEls?.[0]?.index;
        if (clickedIndex !== undefined && clickedIndex !== null) {
          if (onChartClick) {
            onChartClick(clickedIndex);
          }
        }
      },
      plugins: {
        tooltip: {
          bodyFont: {
            size: 16,
          },
        },
      },
      scales: {
        x: {
          ticks: {
            font: {
              size: 16,
              weight: "bold",
            },
          },
        },
        y: {
          suggestedMin: 0,
          suggestedMax: max,
          ticks: {
            font: {
              size: 16,
              weight: "bold",
            },
          },
        },
      },
    },
  });
}

fetchData();
setInterval(fetchData, 1000 * 60 * refetchInterval);
