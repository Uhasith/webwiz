<script setup>
import Chart from "chart.js/auto";
import { onMounted, onBeforeUnmount, ref } from "vue";
import {colours} from "@/Utils/Ranking.js";

const chartCanvas = ref(null);
const props = defineProps({
    data: Object
});
const airQualityIndex = props.data?.aqi?.AQI;
const airQualityIndexText = props.data?.aqi?.SL;
const aqiData = {
  labels: [
    "Good",
    "Moderate",
    "Slightly Unhealthy",
    "Unhealthy",
    "Very Unhealthy",
    "Hazardous",
  ],
  datasets: [
    {
      label: "Air Quality Index",
      data: [50, 50, 50, 50, 50, 50],
      backgroundColor: [
        "#19AF54", // Good
        "#cfea2b", // Moderate
        "#EEB530", // Slightly Unhealthy
        "#fd2525", // Unhealthy
        "#9e37f2", // Very Unhealthy
        "#b10007", // Hazardous
      ],
      borderColor: [
        "#19AF54", // Good
        "#cfea2b", // Moderate
        "#EEB530", // Slightly Unhealthy
        "#fd2525", // Unhealthy
        "#9e37f2", // Very Unhealthy
        "#b10007", // Hazardous
      ],
      borderWidth: 1,
      circumference: 220,
      rotation: 250,
      cutout: "85%",
    },
  ],
};

const centerTextPlugin = {
  id: "centerTextPlugin",
  beforeDraw: (chart) => {
    const { width, height, ctx } = chart;
    ctx.restore();
    const fontSize = (height / 50).toFixed(2);
    ctx.font = `${fontSize}em sans-serif`;
    ctx.textBaseline = "middle";
    const text = airQualityIndex.toString();
    const textX = Math.round((width - ctx.measureText(text).width) / 2);
    const textY = height / 2 + 20;
    ctx.fillText(text, textX, textY);
    ctx.save();
  },
};

const gaugeNeedle = {
  id: "gaugeNeedle",
  afterDatasetsDraw: (chart, args, plugins) => {
    const { ctx, data } = chart;

    // Save the entire context state before making changes
    ctx.save();

    const xCenter = chart.getDatasetMeta(0).data[0].x;
    const yCenter = chart.getDatasetMeta(0).data[0].y;

    const outerRadius = chart.getDatasetMeta(0).data[0].outerRadius;
    const outerPointerRadius = 6; // Set the radius of the outer pointer circle
    const innerPointerRadius = 3; // Set the radius of the inner pointer circle
    const offsetY = 6; // Adjust this offset to move the pointer down

    // Draw the outer circle as the pointer
    ctx.fillStyle = "black"; // Change this to the desired outer color
    ctx.beginPath();
    ctx.arc(
      xCenter,
      yCenter - outerRadius + offsetY,
      outerPointerRadius,
      0,
      2 * Math.PI
    );
    ctx.fill();

    // Draw the inner circle as the pointer
    ctx.fillStyle = "white"; // Change this to the desired inner color
    ctx.beginPath();
    ctx.arc(
      xCenter,
      yCenter - outerRadius + offsetY,
      innerPointerRadius,
      0,
      2 * Math.PI
    );
    ctx.fill();

    // Restore the context state to revert any changes
    ctx.restore();
  },
};

const gaugeChartText = {
  id: "gaugeChartText",
  afterDraw: (chart, args, options) => {
    const { ctx, data } = chart;
    ctx.save();
    const xCenter = chart.getDatasetMeta(0).data[0].x; // Get the x coordinate of the center of the circle from the dataset
    const yCenter = chart.getDatasetMeta(0).data[0].y; // Get the y coordinate of the center of the circle from the dataset
    ctx.fillStyle = "black"; // Change this to the desired text color


    ctx.font = "11px sans-serif";
    ctx.textAlign = "center";
    ctx.fillText("AQI", xCenter, yCenter - 12);

    ctx.fillStyle = colours(airQualityIndexText,false);
      ctx.font = "bold 21px sans-serif"; // Change this to the desired font
      ctx.textAlign = "center"; // Change this to the desired alignment
      ctx.fillText(airQualityIndex, xCenter, yCenter - 30); // Change this to the desired text

    // ctx.font = '15px sans-serif'; // Change this to the desired font
    // ctx.textAlign = 'center'; // Change this to the desired alignment
    // ctx.fillText('Good', xCenter, yCenter + 20); // Change this to the desired text

    ctx.font = "15px sans-serif"; // Change this to the desired font
    ctx.textAlign = "center"; // Change this to the desired alignment

    // Adding background color, padding, and border radius for the 'Good' text
    const text = props.data?.aqi?.SL;
    const paddingTop = 6;
    const paddingBottom = 6;
    const paddingLeftRight = 8;
    const borderRadius = 12;
    ctx.font = "13px sans-serif";
    const textWidth = ctx.measureText(text).width;
    const textHeight = parseInt(ctx.font, 10); // This will approximate the height of the text
    const rectWidth = textWidth + paddingLeftRight * 2;
    const rectHeight = textHeight + paddingTop + paddingBottom;
    ctx.fillStyle = colours(airQualityIndexText, true); // Background color


      // Function to draw rounded rectangle
    const drawRoundedRect = (x, y, width, height, radius) => {
      ctx.beginPath();
      ctx.moveTo(x + radius, y);
      ctx.lineTo(x + width - radius, y);
      ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
      ctx.lineTo(x + width, y + height - radius);
      ctx.quadraticCurveTo(
        x + width,
        y + height,
        x + width - radius,
        y + height
      );
      ctx.lineTo(x + radius, y + height);
      ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
      ctx.lineTo(x, y + radius);
      ctx.quadraticCurveTo(x, y, x + radius, y);
      ctx.closePath();
      ctx.fill();
    };

    // Draw the rounded rectangle
    drawRoundedRect(
      xCenter - rectWidth / 2,
      yCenter + 12 - rectHeight / 2,
      rectWidth,
      rectHeight,
      borderRadius
    );

    ctx.fillStyle = colours(airQualityIndexText,false); // Text color
    ctx.fillText(text, xCenter, yCenter + 13 + textHeight / 4); // Change this to the desired text

    ctx.restore();
  },
};

// config
const config = {
  type: "doughnut",
  data: aqiData,
  options: {
    plugins: {
      legend: {
        display: false,
      },
      tooltip: {
        enabled: true,
        displayColors: false,
        padding: {
          top: 8,
          left: 12,
          right: 12,
        },
        callbacks: {
          label: function (context) {
            return "";
          },
        },
        position: "average",
        yAlign: "bottom",
        xAlign: "bottom",
        caretSize: 0
      },
    },
    aspectRatio: 1.5,
  },
  plugins: [gaugeNeedle, gaugeChartText],
};

onMounted(() => {
  if (chartCanvas.value) {
    new Chart(chartCanvas.value.getContext("2d"), config);
  }
});
</script>

<template>
  <div class="w-full text-center">
    <canvas ref="chartCanvas" class="chartcanvas"></canvas>
  </div>
</template>

<style scoped>
.chartcanvas {
  /* width: 230px !important; */
  height: 100px !important;
  display: inline !important;
}
</style>
