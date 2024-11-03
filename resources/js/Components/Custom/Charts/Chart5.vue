<script setup>
import Chart from "chart.js/auto";
import { onMounted, ref } from "vue";
import getMapDataService from "@/Services/getMapData.js";
import {getHoursWithAMPM} from "@/Utils/LastUpdate.js";


let chartCanvas = ref(null);
const props = defineProps({
    data: Object
});

const hourlyData = {
  labels: ['12AM', '3AM', '6AM', '9AM', '12PM', '3PM', '6PM', '9PM', '12AM'],
  datasets: [
    {
      label: 'Aqi',
      data: [],
      borderColor: "#5BB98A",
      backgroundColor: "rgba(91, 185, 138, 0.2)",
      borderWidth: 2,
      fill: true,
      tension: 0.4,
    },
  ],
};

const config = {
  type: "line",
  data: hourlyData,
  options: {
    responsive: true,
    maintainAspectRatio: false, // Adjust this value to change the height of the chart
    scales: {
      y: {
        beginAtZero: false,
        min: 0,
        max: 40,
        ticks: {
          stepSize: 10,
         
        },
        grid: {
          drawBorder: false,
        },
        border: {
          display: false,
        },
      },

      
      x: {
        grid: {
          display: false,
        },
      },
    },
    plugins: {
      legend: {
        display: false,
      },
    },
    elements: {
      point: {
        radius: 0,
      },
    },
  },
};

onMounted(() => {
  if (chartCanvas.value) {
      chartCanvas = new Chart(chartCanvas.value.getContext("2d"), config);
  }
  if(props.data){
      fetch24HData(props.data.sensor_location_id);
  }
});

async function fetch24HData(sensor_location_id) {
    let data = (await getMapDataService.fetch2hHData(
        sensor_location_id))
    if(data){
        let aqis = data.map(item => item.aqi.AQI);
        let createdAts = data.map(item => item.created_at);

        chartCanvas.data.datasets[0].data = aqis;
        chartCanvas.data.labels = getHoursWithAMPM(createdAts);
        config.options.scales.y.max = Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100;
        chartCanvas.update();
    }
}
</script>

<template>
  <div >
    <canvas ref="chartCanvas"></canvas>
  </div>
</template>
