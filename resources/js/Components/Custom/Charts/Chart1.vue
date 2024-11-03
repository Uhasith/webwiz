<script setup>
import Chart from "chart.js/auto";
import {onMounted, ref, defineEmits} from "vue";
import store from "@/store/index.js";
import axios from "axios";
import {
    getHoursWithAMPM,
    modifyDate,
    getDaysByDate,
    getWeeksByDate,
    getMonthsByDate, getYearByDate, getMinutesWithHoursWithAMPM
} from "@/Utils/LastUpdate.js";
import getMapData from "@/Services/getMapData.js";
import getMapDataService from "@/Services/getMapData.js";

let chartCanvas = ref(null);
const props = defineProps({
    locationId: null,
    sensor: String,
    dateRange: String,
    ui: String,
    customLabels: Array,
    customDataSets: Array,
    customMax: 400,
});
let sensor = null;
let dateRange = null;
let locationId = null;
const emit = defineEmits(['history-update-date']);

store.subscribe((mutation, state) => {
    if(props.ui !== 'comparative_3'){
        if (mutation.type === 'updateSensor') {
            sensor = mutation.payload.id;
            getHistoricalData();
        }else if (mutation.type === 'updateDateRange') {
            dateRange = mutation.payload;
            getHistoricalData();
        }
        if (mutation.type === 'updateSelectedLocation') {
            locationId = mutation.payload.id;
        }
    }else{
        if (mutation.type === 'updateComparativeLevelChart3') {
            getComparativeData(mutation.payload)
        }
        if (mutation.type === 'updateComparativeLevelChart4') {
            getComparativeData(mutation.payload,false)
        }
    }

});
function getComparativeData(payload,left = true){
    displayChartWithCustomDataSets(payload.customMax, payload.customLabels, payload.customDataSets,left);

}
async function getHistoricalData() {
    try {
        if (locationId && dateRange && sensor) {

            let data = await getMapDataService.fetchSensorDataHistory(
                locationId, sensor, modifyDate(new Date(dateRange.split(" ")[0]), 1),
                modifyDate(new Date(dateRange.split(" ")[1]), 1));

            if (data) {
                let createdAts = data.result.map(item => item.created_at);
                emit('history-update-date', createdAts[createdAts.length - 1]);
                let aqis = data.result.map(item => item.aqi.AQI);
                if (data.type === "today") {
                    displayChart(Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100, getMinutesWithHoursWithAMPM(createdAts), aqis);
                }
                if (data.type === "hourly") {
                    displayChart(Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100, getHoursWithAMPM(createdAts), aqis);
                }
                if (data.type === "daily") {
                    displayChart(Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100, getDaysByDate(createdAts), aqis);
                }
                if (data.type === "weekly") {
                    displayChart(Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100, getWeeksByDate(createdAts), aqis);
                }
                if (data.type === "monthly") {
                    displayChart(Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100, getMonthsByDate(createdAts), aqis);
                }
                if (data.type === "annually") {
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
        chartCanvas.data.datasets[0].label = "Aqi Level";
        chartCanvas.data.datasets[0].data = values;
        chartCanvas.update();
    } else {
        chartCanvas = new Chart(chartCanvas.value.getContext("2d"), config);
    }
}
let alignedValues1 = [];
let alignedValues2 = [];
let mergedLabels = [];
async function displayChartWithCustomDataSets(max, labels, datasets,left) {

    const labelsValues = Promise.resolve(labels);
    const datasetsValues = Promise.resolve(datasets);
    const maxValue = Promise.resolve(max);

    if (chartCanvas) {
        Promise.all([labelsValues, datasetsValues, maxValue])
            .then(([labelsResult, datasetsResult, maxResult]) => {

                const alignData = (labels, values, mergedLabels) => {
                    const alignedData = mergedLabels.map(label => {
                        const index = labels.indexOf(label);
                        return index !== -1 ? values[index] : null; // Fill missing values with `null`
                    });
                    return alignedData;
                };
                if(config.options.scales.y.max < maxResult){
                    config.options.scales.y.max = maxResult;
                }
                if(left){

                    chartCanvas.data.datasets[0].label = datasetsResult.label;
                    mergedLabels = Array.from(new Set([...labelsResult, ...chartCanvas.data.labels]));
                    chartCanvas.data.labels = mergedLabels;
                    alignedValues1 = alignData(labelsResult, datasetsResult.data, mergedLabels);
                    chartCanvas.data.datasets[0].data = alignedValues1;
                    chartCanvas.update();

                }else{

                    chartCanvas.data.datasets[1] = {
                        label: datasetsResult.label,
                        data: [],
                        borderColor: "#00d595",
                        backgroundColor: "#00d595",
                        borderWidth: 1,
                        fill: false,
                    };
                    mergedLabels = Array.from(new Set([...labelsResult, ...chartCanvas.data.labels]));
                    chartCanvas.data.labels = mergedLabels;
                    alignedValues2 = alignData(labelsResult, datasetsResult.data, mergedLabels);
                    chartCanvas.data.datasets[1].data = alignedValues2;
                    chartCanvas.update();

                }

            });
    } else {
        chartCanvas = new Chart(chartCanvas.value.getContext("2d"), config);
    }
}

const aqiData = {
    labels: [],
    datasets: [
        {
            label: "AQI Level",
            data: [],
            borderColor: "#5BB98A",
            backgroundColor: "#5BB98A",
            borderWidth: 1,
            fill: false,
        }
    ],
};

const isMobile = window.innerWidth <= 768;

const config = {
    type: "line",
    data: aqiData,
    options: {
        responsive: true,
        maintainAspectRatio: false,

        scales: {
            y: {
                beginAtZero: true,
                max: 0,
            },
            x: {
                grid: {
                    display: false,
                },
                ticks: {
                    maxTicksLimit: 30 // Adjust to limit the number of labels shown
                }
            },
        },
        plugins: {
            legend: {
                display: true,
                position: "top",
                align: isMobile ? "start" : "end",
                labels: {
                    usePointStyle: true,
                    pointStyle: "circle",
                    boxWidth: 10,
                    boxHeight: 10,
                    padding: 10,
                    font: {
                        size: 16, // Adjust this as needed
                    },
                },
                color: "#131313",
            },
        },
    },
};

onMounted(() => {
    chartCanvas = new Chart(chartCanvas.value.getContext("2d"), config);
});
</script>
<template>
    <div class="chart1">
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<style scoped>
@media screen and (max-width: 768px) {
    .chart1 canvas {
        /* width: 700px !important */
    }
}

.chart1 canvas {
    height: 300px !important;
}

@media screen and (min-width: 768px) {
    .chart1 canvas {
        width: 100% !important;
    }
}
</style>
