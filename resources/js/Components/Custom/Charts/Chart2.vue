<script setup>
import Chart from "chart.js/auto";
import {defineEmits, onMounted, ref} from "vue";
import store from "@/store/index.js";
import {
    getDaysByDate,
    getHoursWithAMPM,
    getMonthsByDate,
    getWeeksByDate,
    getYearByDate,
    modifyDate
} from "@/Utils/LastUpdate.js";
import axios from "axios";
import getMapDataService from "@/Services/getMapData.js";

let chartCanvas = ref(null);
let sensor = null;
let dateRange = null;
let locationId = null;
let result = null;
let pollutantParam = "?";
const emit = defineEmits(['history-update-date-field2']);

store.subscribe((mutation, state) => {
    if (mutation.type === 'updateComparativePollutantSensor') {
        sensor = mutation.payload.id;
        getHistoricalData();
    }
    if (mutation.type === 'updateDateRange') {
        dateRange = mutation.payload;
        getHistoricalData();
    }
    if (mutation.type === 'updateSelectedLocation') {
        locationId = mutation.payload.id;
    }
    if (mutation.type === 'pollutantParamChanged') {
        pollutantParam = mutation.payload;
        chartCanvas.data.datasets[1].label = pollutantParam;
        let aqis = [];
        if(pollutantParam === "PM10"){
            aqis = result.result.map(item => item.pm10_status === "ACTIVE"? item.pm10_aqi.AQI:null);
        }
        if(pollutantParam === "O3"){
            aqis = result.result.map(item => item.pm10_status === "ACTIVE"? item.pm10_aqi.AQI:null);
        }
        if(pollutantParam === "CO"){
            aqis = result.result.map(item => item.co_status === "ACTIVE"? item.co_aqi.AQI:null);
        }
        if(pollutantParam === "NO2"){
            aqis = result.result.map(item => item.no2_status === "ACTIVE"? item.no2_aqi.AQI:null);
        }
        if(pollutantParam === "SO2"){
            aqis = result.result.map(item => item.so2_status === "ACTIVE"? item.so2_aqi.AQI:null);
        }
        chartCanvas.data.datasets[1].data = aqis;
        chartCanvas.update();
    }
});
async function getHistoricalData() {
    try {


        if (locationId && dateRange && sensor) {
             result = await getMapDataService.fetchSensorDataHistory(
                locationId, sensor, modifyDate(new Date(dateRange.split(" ")[0]), 1),
                modifyDate(new Date(dateRange.split(" ")[1]), 1));

            if (result) {
                let createdAts = result.result.map(item => item.created_at);
                emit('history-update-date-field2', createdAts[createdAts.length - 1]);

                let aqis = result.result.map(item => item.pm2_5_status === "ACTIVE" ? item.pm2_5_aqi.AQI : null);

                if (result.type === "hourly") {
                    displayChart(Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100, getHoursWithAMPM(createdAts), aqis);
                }
                if (result.type === "daily") {
                    displayChart(Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100, getDaysByDate(createdAts), aqis);

                }
                if (result.type === "weekly") {
                    displayChart(Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100, getWeeksByDate(createdAts), aqis);

                }
                if (result.type === "monthly") {
                    displayChart(Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100, getMonthsByDate(createdAts), aqis);

                }
                if (result.type === "annually") {
                    displayChart(Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100, getYearByDate(createdAts), aqis);

                }
            }
        }
    } catch (error) {
        console.log("Error:", error);
        throw error;
    }
}
function displayChart(max, labels, values) {
    if (chartCanvas) {
        config.options.scales.y.max = max;
        chartCanvas.data.labels = labels;
        chartCanvas.data.datasets[0].data = values;
        chartCanvas.update();
    } else {
        chartCanvas = new Chart(chartCanvas.value.getContext("2d"), config);
    }
}
const data = {
  labels: [],
  datasets: [
    {
      label: "PM2.5",
      data: [],
      backgroundColor: Array(12).fill("#5BB98A"),
    },
    {
      label: pollutantParam,
      data: [],
      backgroundColor: Array(12).fill("#E1F296"),
    },
  ],
};

const isMobile = window.innerWidth <= 768;

const config = {
  type: "bar",
  data,
  options: {
    responsive: false,
    maintainAspectRatio: false,
    scales: {
      x: {
        grid: {
          display: false,
        },
      },
      y: {
        beginAtZero: true,
        max: 0,
        ticks: {
          stepSize: 10,
        },
      },
    },
    plugins: {
      legend: {
        position: "top",
        align: isMobile ? "start" : "end",
        labels: {
          usePointStyle: true,
          pointStyle: "circle",
          boxWidth: 10,
          boxHeight: 10,
          padding: 10,
          font: {
            size: 16,
          },
        },
      },
    },
    categoryPercentage: 0.6, // Reduced to make bars thinner
    barPercentage: 0.8, // Adjusted to maintain a small gap between bars in a group
  },
};

onMounted(() => {
    chartCanvas = new Chart(chartCanvas.value.getContext("2d"), config);

});
</script>

<template>
  <div class="chart2">
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>

<style scoped>
.chart2 canvas {
  height: 300px !important;
}

@media screen and (min-width: 768px) {
  .chart2 canvas {
    width: 100% !important;
  }
}
</style>
