<script setup>
import Chart from "chart.js/auto";
import {onMounted, onBeforeUnmount, ref} from "vue";
import store from "@/store/index.js";

let chartCanvas = ref(null);
const props = defineProps({
    locationId: null,
    sensor: String,
    dateRange: String,
    ui: String,
    customLabels: Array,
    customDataSets: Array,
    customMax: 400,
    allData: Array,
});
let alignedValues1 = [];
let alignedValues2 = [];
let mergedLabels = [];

let labelsValues = [];
let datasetsValues = [];
let allData = [];
let leftAllData = [];
let rightAllData = [];
let maxValue = 0;
store.subscribe((mutation, state) => {
    if (props.ui === 'comparative_4') {
        // if (mutation.type === 'updateComparativeLevelChart4') {
        //     // getComparativeData(mutation.payload,false)
        //     props.customMax.value = mutation.payload.customMax;
        //     props.customLabels.value = mutation.payload.customLabels;
        //     props.customDataSets.value = mutation.payload.customDataSets;
        // }
        if (mutation.type === 'comparativePollutantParamChanged') {
            getComparativeData(mutation.payload, false, true);
        }
        if (mutation.type === 'updateComparativeLevelChart3') {
            getComparativeData(mutation.payload, true)
        }
        if (mutation.type === 'updateComparativeLevelChart4') {
            getComparativeData(mutation.payload, false)
        }
    }
});

function getComparativeData(payload, left = true, pollutantParam = false) {

    if (pollutantParam) {
        let aqisLeft = [];
        let aqisRight = [];
        Promise.all([labelsValues, datasetsValues, maxValue])
            .then(([labelsResult, datasetsResult, maxResult]) => {

            if (payload === "O3") {
                aqisLeft = leftAllData.map(item => item.o3_status === "ACTIVE" ? item.o3_aqi?.AQI : null);
                aqisRight = rightAllData.map(item => item.o3_status === "ACTIVE" ? item.o3_aqi?.AQI : null);
            }
            if (payload === "CO") {
                aqisLeft = leftAllData.map(item => item.co_status === "ACTIVE" ? item.co_aqi?.AQI : null);
                aqisRight = rightAllData.map(item => item.co_status === "ACTIVE" ? item.co_aqi?.AQI : null);
            }
            if (payload === "NO2") {
                aqisLeft = leftAllData.map(item => item.no2_status === "ACTIVE" ? item.no2_aqi?.AQI : null);
                aqisRight = rightAllData.map(item => item.no2_status === "ACTIVE" ? item.no2_aqi?.AQI : null);
            }
            if (payload === "SO2") {
                aqisLeft = leftAllData.map(item => item.so2_status === "ACTIVE" ? item.so2_aqi?.AQI : null);
                aqisRight = rightAllData.map(item => item.so2_status === "ACTIVE" ? item.so2_aqi?.AQI : null);
            }
            if (payload === "PM2.5") {
                aqisLeft = leftAllData.map(item => item.pm2_5_status === "ACTIVE" ? item.pm2_5_aqi?.AQI : null);
                aqisRight = rightAllData.map(item => item.pm2_5_status === "ACTIVE" ? item.pm2_5_aqi?.AQI : null);
            }
            if (payload === "PM10") {
                aqisLeft = leftAllData.map(item => item.pm10_status === "ACTIVE" ? item.pm10_aqi?.AQI : null);
                aqisRight = rightAllData.map(item => item.pm10_status === "ACTIVE" ? item.pm10_aqi?.AQI : null);
            }

            let maxLeft = Math.ceil(Math.max(...aqisLeft.map(Number)) / 100) * 100;
            let maxRight = Math.ceil(Math.max(...aqisRight.map(Number)) / 100) * 100;
            let max = maxLeft > maxRight? maxLeft : maxRight;

                displayChartWithCustomDataSets( max, labelsResult, {
                    label: chartCanvas.data.datasets[0].label,
                    data: aqisLeft,
                    backgroundColor: datasetsResult.backgroundColor,
                    borderWidth: 1,
                    fill: false,
                } , true);

                displayChartWithCustomDataSets(max, labelsResult, {
                    label: chartCanvas.data.datasets[1].label,
                    data: aqisRight,
                    backgroundColor: datasetsResult.backgroundColor,
                    borderWidth: 1,
                    fill: false,
                }, false);

        });
    } else {
        labelsValues = Promise.resolve(payload.customLabels);
        datasetsValues = Promise.resolve(payload.customDataSets);
        maxValue = Promise.resolve(payload.customMax);
        allData = Promise.resolve(payload.allData);
        Promise.all([labelsValues, datasetsValues, maxValue, allData])
            .then(([labelsResult, datasetsResult, maxResult, allDataResult]) => {
                    let aqis = [];
                    if(left){
                        leftAllData = allDataResult;
                    }else{
                        rightAllData = allDataResult
                    }

                    aqis = allDataResult.map(item => item.pm2_5_status === "ACTIVE" ? item.pm2_5_aqi.AQI : null);
                    datasetsResult.data = aqis;
                    displayChartWithCustomDataSets(maxResult, labelsResult, datasetsResult, left);

            });
    }
}

async function displayChartWithCustomDataSets(maxResult, labelsResult, datasetsResult, left, pollutantParamValue = "PM2.5") {

    if (chartCanvas) {
        const alignData = (labels, values, mergedLabels) => {
            const alignedData = mergedLabels.map(label => {
                const index = labels.indexOf(label);
                return index !== -1 ? values[index] : null; // Fill missing values with `null`
            });
            return alignedData;
        };
        if (config.options.scales.y.max < maxResult) {
            config.options.scales.y.max = maxResult;
        }
        if (left) {
            chartCanvas.data.datasets[0].label = datasetsResult.label;
            mergedLabels = Array.from(new Set([...labelsResult, ...chartCanvas.data.labels]));
            chartCanvas.data.labels = mergedLabels;
            alignedValues1 = alignData(labelsResult, datasetsResult.data, mergedLabels);
            chartCanvas.data.datasets[0].data = alignedValues1;
            chartCanvas.update();

        } else {

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

    } else {
        chartCanvas = new Chart(chartCanvas.value.getContext("2d"), config);
    }
}

const data = {
    labels: [],
    datasets: [
        {
            label: "First Location",
            data: [],
            backgroundColor: Array(12).fill("#5BB98A"),
            // barThickness: 10,
            fill: false,

        },
        {
            label: "Second Location",
            data: [],
            backgroundColor: Array(12).fill("#E1F296"),
            // barThickness: 10,
            fill: false,

        },
    ],
};

const isMobile = window.innerWidth <= 768;

const config = {
    type: "bar",
    data,
    options: {
        responsive: !isMobile,
        maintainAspectRatio: !isMobile,
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    stepSize: 10
                }
            },
        },
        plugins: {
            legend: {
                position: "top",
                align: isMobile ? "start" : "end",
                labels: {
                    usePointStyle: true,
                    pointStyle: "circle",
                    padding: 20,
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
        barPercentage: 0.8,      // Adjusted to maintain a small gap between bars in a group
    },
};


onMounted(() => {
    chartCanvas = new Chart(chartCanvas.value.getContext("2d"), config);
});
</script>

<template>
    <div>
        <canvas ref="chartCanvas"></canvas>
    </div>
</template>

<style scoped>
@media screen and (max-width: 768px) {
    canvas {
        height: 342px !important;
    }
}
</style>
