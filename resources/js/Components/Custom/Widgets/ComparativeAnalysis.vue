<template>
    <div class="mt-8 hidden lg:block comparative_analysis px-4 lg:px-0">
        <h1 class="text-2xl md:text-[2.8rem] font-bold mb-4">
            {{ $t("Comparative Analysis") }}
        </h1>
        <p class="text-md mt-2">
            {{ $t("Compare Air Quality Across Locations") }}
        </p>

        <div class="grid lg:grid-cols-2 grid-cols-1 gap-4 my-8">
            <!-- <SelectCityDropDown cityno="1" /> -->
            <!-- <SelectCityDropDown cityno="2" /> -->
            <div>
                <CompareCityBefore ui="CompareCityBefore"
                                   :initial-locations="initialLocations"
                                   @item-selected="leftLocationSelected"
                                   v-if="!leftCitySelected"
                />
                <CompareCityAfter ui="CompareCityAfter"
                                  :default-selected="leftLocation"
                                  @item-selected="leftAfterLocationSelected"
                                  v-if="leftCitySelected"
                />
                <div class="border p-5 bg-white rounded-lg" v-if="leftCitySelected">
                    <div class="w-full flex justify-end">
                        <div class="relative">
                            <MeasuredByDropdown ui="comparative-chart1" :sensors="leftLocationSensors"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-4 mt-4">
                        <div class="col-span-2">
                            <div class="w-full text-center">
                                <div class="inline-flex items-center space-x-1">
                                    <p class="text-4xl font-bold">{{ leftSensorData?.weather_records?.temperature }}</p>
                                    <div class="flex flex-col text-left">
                                        <p class="text-sm">°C</p>
                                        <p class="text-xs">{{ leftSensorData?.weather_records?.cloud }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full text-center">
                                <img
                                    :src="weatherIcons(leftSensorData?.weather_records?.cloud)"
                                    class="h-10 mt-4 inline-block"
                                    alt="Sunny weather"
                                />
                            </div>

                            <div class="flex justify-center items-center">
                                <div
                                    class="w-fit py-3 px-7 flex-1 border custom_bg_gray custom_border_color rounded-lg mt-4 flex flex-col items-center justify-center"
                                >
                                    <img
                                        src="/images/pressure.png"
                                        class="h-4"
                                        alt="Sunny weather"
                                    />
                                    <p class="text-xs">{{ $t("Pressure") }}</p>
                                    <p class="text-sm font-bold">{{ leftSensorData?.weather_records?.pressure }}mb</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-3">
                            <div class="w-full flex justify-center items-center mb-2">
                                <img src="/images/greenpinicon.png" class="h-4 w-auto mr-2"/>
                                <p class="text-green-700 font-bold">{{
                                        leftSensorData?.sensor_location?.location.name
                                    }}</p>
                            </div>
                            <Chart3 v-if="leftSensorData" :data="leftSensorData"/>
                        </div>
                        <div class="col-span-2">
                            <div class="flex items-end">
                                <span class="text-4xl font-bold leading-none">{{
                                        leftSensorData?.weather_records?.humidity
                                    }}</span>
                                <div class="flex flex-col items-start ml-1 mb-1">
                                    <span class="text-2xl font-bold leading-none">%</span>
                                    <span class="text-xs">{{ $t("Humidity") }}</span>
                                </div>
                            </div>
                            <div class="w-full text-center">
                                <img
                                    :src="leftSensorData?.weather_records?.precipitation > 50 ? '/images/drops.png':'/images/sun.png'"
                                    class="h-10 mt-4 inline-block"
                                    alt="Drops"
                                />
                            </div>

                            <div class="flex justify-center items-center">
                                <div
                                    class="w-fit py-3 px-7 flex-1 border custom_bg_gray custom_border_color rounded-lg mt-4 flex flex-col items-center justify-center"
                                >
                                    <img src="/images/wind.png" class="h-4" alt="Sunny weather"/>
                                    <p class="text-xs">{{ $t("Wind") }}</p>
                                    <p class="text-sm font-bold">{{ leftSensorData?.weather_records?.wind }}km/h</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 md:col-span-3">
                        <h3 class="font-bold text-lg mb-2">
                            {{ $t("Air Pollutant Parameters") }}
                        </h3>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div>
                                    <span class="text-xs text-gray-600">{{ $t("SL AQI") }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-sm text-gray-600">{{
                                        leftSensorData?.sensor_location?.location.name
                                    }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-6 gap-1">
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">PM2.5</p>
                                    <p class="text-sm font-bold">{{ leftSensorData?.pm2_5_aqi?.AQI }}</p>
                                </div>
                                <div v-if="leftSensorData?.pm2_5_status === 'ACTIVE' "
                                     class="bg-green3 h-10 rounded-md mt-2"></div>
                            </div>
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">PM10</p>
                                    <p class="text-sm font-bold">{{ leftSensorData?.pm10_aqi?.AQI }}</p>
                                </div>
                                <div v-if="leftSensorData?.pm10_status === 'ACTIVE' "
                                     class="bg-green3 h-12 rounded-md mt-2"></div>
                            </div>
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">O3</p>
                                    <p class="text-sm font-bold">{{ leftSensorData?.o3_aqi?.AQI }}</p>
                                </div>
                                <div v-if="leftSensorData?.o3_status === 'ACTIVE' "
                                     class="bg-green3 h-5 rounded-md mt-2"></div>
                            </div>
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">CO</p>
                                    <p class="text-sm font-bold">{{ leftSensorData?.co_aqi?.AQI }}</p>
                                </div>
                                <div v-if="leftSensorData?.co_status === 'ACTIVE' "
                                     class="bg-green3 h-12 rounded-md mt-2"></div>
                            </div>
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">NO2</p>
                                    <p class="text-sm font-bold">{{ leftSensorData?.no2_aqi?.AQI }}</p>
                                </div>
                                <div v-if="leftSensorData?.no2_status === 'ACTIVE' "
                                     class="bg-green3 h-12 rounded-md mt-2"></div>
                            </div>
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">SO2</p>
                                    <p class="text-sm font-bold">{{ leftSensorData?.so2_aqi?.AQI }}</p>
                                </div>
                                <div v-if="leftSensorData?.so2_status === 'ACTIVE' "
                                     class="bg-green3 h-12 rounded-md mt-2"></div>
                            </div>
<!--                            <div-->
<!--                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"-->
<!--                            >-->
<!--                                <div>-->
<!--                                    <p class="text-xs text-gray-500">CO2</p>-->
<!--                                    <p class="text-sm font-bold">{{ leftSensorData?.co2_aqi?.AQI }}</p>-->
<!--                                </div>-->
<!--                                <div v-if="leftSensorData?.co2_status === 'ACTIVE' "-->
<!--                                     class="bg-green3 h-12 rounded-md mt-2"></div>-->
<!--                            </div>-->
                        </div>
                        <div class="text-xs text-gray-500 text-center mt-3">
                            {{ $t("Last Update") }} {{ formatDate(leftSensorData?.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <CompareCityAfter
                    :default-selected="rightLocation"
                    @item-selected="rightAfterLocationSelected"
                    v-if="rightCitySelected"
                />
                <CompareCityBefore
                    :initial-locations="initialLocations"
                    @item-selected="rightLocationSelected"
                    v-if="!rightCitySelected"
                />
                <div class="border p-5 bg-white rounded-lg" v-if="rightCitySelected">
                    <div class="w-full flex justify-end">
                        <div class="relative">
                            <MeasuredByDropdown ui="comparative-chart2" :sensors="rightLocationSensors"/>
                        </div>
                    </div>
                    <div class="grid grid-cols-7 gap-4 mt-4">
                        <div class="col-span-2">
                            <div class="w-full text-center">
                                <div class="inline-flex items-center space-x-1">
                                    <p class="text-4xl font-bold">{{
                                            rightSensorData?.weather_records?.temperature
                                        }}</p>
                                    <div class="flex flex-col">
                                        <p class="text-sm">°C</p>
                                        <p class="text-xs">{{ $t("Sunny") }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="w-full text-center">
                                <img :src="weatherIcons(rightSensorData?.weather_records?.cloud)"
                                     class="h-10 mt-4 inline-block"
                                     alt="Sunny weather"
                                />
                            </div>

                            <div class="flex justify-center items-center">
                                <div
                                    class="w-fit py-3 px-7 flex-1 border custom_bg_gray custom_border_color rounded-lg mt-4 flex flex-col items-center justify-center"
                                >
                                    <img
                                        src="/images/pressure.png"
                                        class="h-4"
                                        alt="Sunny weather"
                                    />
                                    <p class="text-xs">{{ $t("Pressure") }}</p>
                                    <p class="text-sm font-bold">{{ rightSensorData?.weather_records?.pressure }}mb</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-3">
                            <div class="w-full flex justify-center items-center mb-2">
                                <img src="/images/greenpinicon.png" class="h-4 w-auto mr-2"/>
                                <p class="text-green-700 font-bold">{{
                                        rightSensorData?.sensor_location?.location.name
                                    }}</p>
                            </div>
                            <Chart3 v-if="rightSensorData" :data="rightSensorData"/>
                        </div>
                        <div class="col-span-2">
                            <div class="flex items-end">
                                <span class="text-4xl font-bold leading-none">{{
                                        rightSensorData?.weather_records?.humidity
                                    }}</span>
                                <div class="flex flex-col items-start ml-1 mb-1">
                                    <span class="text-2xl font-bold leading-none">%</span>
                                    <span class="text-xs">{{ $t("Humidity") }}</span>
                                </div>
                            </div>
                            <div class="w-full text-center">
                                <img
                                    :src="rightSensorData?.weather_records?.precipitation > 50 ? '/images/drops.png':'/images/sun.png'"
                                    class="h-10 mt-4 inline-block"
                                    alt="Drops"
                                />
                            </div>

                            <div class="flex justify-center items-center">
                                <div
                                    class="w-fit py-3 px-7 flex-1 border custom_bg_gray custom_border_color rounded-lg mt-4 flex flex-col items-center justify-center"
                                >
                                    <img src="/images/wind.png" class="h-4" alt="Sunny weather"/>
                                    <p class="text-xs">{{ $t("Wind") }}</p>
                                    <p class="text-sm font-bold">{{ rightSensorData?.weather_records?.wind }}km/h</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-4 md:col-span-3">
                        <h3 class="font-bold text-lg mb-2">
                            {{ $t("Air Pollutant Parameters") }}
                        </h3>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div>
                                    <span class="text-xs text-gray-600">{{ $t("SL AQI") }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="text-sm text-gray-600">{{ rightSensorData?.sensor_location?.location.name }}</span>
                            </div>
                        </div>
                        <div class="grid grid-cols-6 gap-1">
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">PM2.5</p>
                                    <p class="text-sm font-bold">{{ rightSensorData?.pm2_5_aqi?.AQI }}</p>
                                </div>
                                <div v-if="rightSensorData?.pm2_5_status === 'ACTIVE'"
                                     class="bg-green3 h-10 rounded-md mt-2"></div>
                            </div>
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">PM10</p>
                                    <p class="text-sm font-bold">{{ rightSensorData?.pm10_aqi?.AQI }}</p>
                                </div>
                                <div v-if="rightSensorData?.pm10_status === 'ACTIVE'"
                                     class="bg-green3 h-12 rounded-md mt-2"></div>
                            </div>
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">O3</p>
                                    <p class="text-sm font-bold">{{ rightSensorData?.o3_aqi?.AQI }}</p>
                                </div>
                                <div v-if="rightSensorData?.o3_status === 'ACTIVE'"
                                     class="bg-green3 h-5 rounded-md mt-2"></div>
                            </div>
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">CO</p>
                                    <p class="text-sm font-bold">{{ rightSensorData?.co_aqi?.AQI }}</p>
                                </div>
                                <div v-if="rightSensorData?.co_status === 'ACTIVE'"
                                     class="bg-green3 h-12 rounded-md mt-2"></div>
                            </div>
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">NO2</p>
                                    <p class="text-sm font-bold">{{ rightSensorData?.no2_aqi?.AQI }}</p>
                                </div>
                                <div v-if="rightSensorData?.no2_status === 'ACTIVE'"
                                     class="bg-green3 h-12 rounded-md mt-2"></div>
                            </div>
                            <div
                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
                            >
                                <div>
                                    <p class="text-xs text-gray-500">SO2</p>
                                    <p class="text-sm font-bold">{{ rightSensorData?.so2_aqi?.AQI }}</p>
                                </div>
                                <div v-if="rightSensorData?.so2_status === 'ACTIVE'"
                                     class="bg-green3 h-12 rounded-md mt-2"></div>
                            </div>
<!--                            <div-->
<!--                                class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"-->
<!--                            >-->
<!--                                <div>-->
<!--                                    <p class="text-xs text-gray-500">CO2</p>-->
<!--                                    <p class="text-sm font-bold">{{ rightSensorData?.co2_aqi?.AQI }}</p>-->
<!--                                </div>-->
<!--                                <div v-if="rightSensorData?.co2_status === 'ACTIVE'"-->
<!--                                     class="bg-green3 h-12 rounded-md mt-2"></div>-->
<!--                            </div>-->
                        </div>
                        <div class="text-xs text-gray-500 text-center mt-3">
                            {{ $t("Last Update") }}{{ formatDate(rightSensorData?.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-2 grid-cols-1 gap-4 my-8" v-if="leftCitySelected || rightCitySelected">
            <div class="border p-5 bg-white rounded-lg max-sm:overflow-y-scroll">
                <div class="lg:flex items-center justify-between">
                    <div class="flex justify-between">
                        <h2 class="text-lg font-semibold mb-2 mt-2 lg:mt-0">
                            {{ $t("Comparative AQI level") }}
                        </h2>

                        <img
                            src="/images/dropicon.svg"
                            class="h-10 w-auto block lg:hidden cursor-pointer"
                            @click="showDropdown4 = !showDropdown4"
                        />
                    </div>
                    <div class="lg:flex lg:space-x-2" :class="{ hidden: !showDropdown4 }">
                        <!-- <div class="mb-2"><SelectParameterDropdown /></div> -->
                        <div class="mb-2">
<!--                            <MeasuredByDropdown ui="comparative-chart3" :sensors="leftLocationSensors"/>-->

                        </div>
                    </div>
                </div>

                <hr class="my-4"/>

                <Chart1 :custom-max="customMax" :custom-data-sets="customDataSets" :custom-labels="customLabels" ui="comparative_3"/>

                <p class="w-full text-center text-xs text-gray-500 mt-3">
                    {{ $t("Last Update") }}: {{formatDate(comparativeUpdateDate_1)}}
                </p>
            </div>
            <div class="border p-5 bg-white rounded-lg max-sm:overflow-y-scroll">
                <div class="lg:flex items-center justify-between">
                    <div class="flex justify-between">
                        <h2 class="text-lg font-semibold mb-2 mt-2 lg:mt-0">
                            {{ $t("Comparative pollutant level") }}
                        </h2>
                        <img
                            src="/images/dropicon.svg"
                            class="h-10 w-auto block lg:hidden cursor-pointer"
                            @click="showDropdown5 = !showDropdown5"
                        />
                    </div>
                    <div class="lg:flex lg:space-x-2" :class="{ hidden: !showDropdown5 }">
                        <div class="mb-2">
                            <SelectParameterDropdown ui="comparative"/>
                        </div>
                    </div>
                </div>

                <hr class="my-4"/>

                <Chart4 :custom-max="customMax" :custom-data-sets="customDataSets" :custom-labels="customLabels" ui="comparative_4"/>

                <p class="w-full text-center text-xs text-gray-500 mt-3">
                    {{ $t("Last Update") }}: {{formatDate(comparativeUpdateDate_2)}}
                </p>
            </div>
        </div>
    </div>
</template>
<script setup>
import MeasuredByDropdown from "@/Components/Custom/Dropdowns/MeasuredByDropdown.vue";
import Chart1 from "@/Components/Custom/Charts/Chart1.vue";
import Chart3 from "@/Components/Custom/Charts/Chart3.vue";
import Chart4 from "@/Components/Custom/Charts/Chart4.vue";
import SimpleEquipmentDropdown from "@/Components/Custom/Dropdowns/SimpleEquipmentDropdown.vue";
import SelectParameterDropdown from "@/Components/Custom/Dropdowns/SelectParameterDropdown.vue";
import CompareCityBefore from "@/Components/Custom/Widgets/CompareCityBefore.vue";
import CompareCityAfter from "@/Components/Custom/Widgets/CompareCityAfter.vue";
import {defineEmits, ref} from "vue";
import getSensorsByLocation from "@/Services/getSensorsByLocation.js";
import getMapDataService from "@/Services/getMapData.js";
import store from "@/store/index.js";
import {formatDate, getDaysByDate, getHoursWithAMPM, getMonthsByDate} from "@/Utils/LastUpdate";
import {ranking} from "@/Utils/Ranking.js";
import {weatherIcons} from "@/Utils/WeatherData.js";

const props = defineProps({
    initialLocations: Array,
    sensors: Object,
    sensorData: Object,
    customLabels: Array,
});

const showDropdown4 = ref(false);
const showDropdown5 = ref(false);

const leftLocation = ref(null);
const rightLocation = ref(null);
const leftCitySelected = ref(false);
const rightCitySelected = ref(false);
const leftLocationSensors = ref(props.sensors);
const rightLocationSensors = ref(props.sensors);
let leftSensorData = ref(null);
let rightSensorData = ref(null);
let leftLocationDetails = null;
let rightLocationDetails = null;
let customDataSets = [
    {}, {}
];
let customLabels = ref(props.customLabels);
let customMax = [];
let comparativeUpdateDate_1 = null;
let comparativeUpdateDate_2 = null;
const leftLocationSelected = async (location) => {

    leftLocationDetails = location;
    if (!leftCitySelected) {
        leftLocationSensors.value = await getSensorsByLocation(location.id);
    }
    leftLocation.value = location;
    leftCitySelected.value = true;
};
const leftAfterLocationSelected = async (location) => {
    leftLocationDetails = location;
    leftLocationSensors.value = await getSensorsByLocation(location.id);

};
const rightLocationSelected = async (location) => {
    rightLocationDetails = location;
    if (!rightCitySelected) {
        rightLocationSensors.value = await getSensorsByLocation(location.id);
    }
    rightLocation.value = location;
    rightCitySelected.value = true;

};
const rightAfterLocationSelected = async (location) => {
    rightLocationDetails = location;
    rightLocationSensors.value = await getSensorsByLocation(location.id);
};

function fetchData(locationId, sensor) {
    let response = getMapDataService.fetchSensorDataHistory(
        locationId, sensor, null, null, true);
    return response;
}

store.subscribe(async (mutation, state) => {

    if (mutation.type === 'updateAnalysisPollutantSensor_1') {
        let response = await fetchData(leftLocationDetails.id, mutation.payload.id);
        if (response) {
            leftSensorData.value = null;
            leftSensorData.value = await response.result;
             store.dispatch("updateComparativeLevelChart3Action", {
                 "allData": response.today,
                 "customMax": setCustomMax(response.today),
                "customLabels": setCustomLabels(response.today),
                 "title": leftSensorData.value.name,
                "customDataSets": setCustomDataSets(await response.today, Array(12).fill("#5BB98A"))
            });
        }
    }
    if (mutation.type === 'updateAnalysisPollutantSensor_2') {
        let response = await fetchData(rightLocationDetails.id, mutation.payload.id);
        if (response) {
            rightSensorData.value = null;
            rightSensorData.value = await response.result;
            store.dispatch("updateComparativeLevelChart4Action", {
                "allData": response.today,
                "customMax": setCustomMax(response.today),
                "customLabels": setCustomLabels(response.today,false),
                "customDataSets": setCustomDataSets(await response.today, Array(12).fill("#00d595"),false)
            });
        }
    }
});

async function setCustomDataSets(data,color,left=true) {
    let aqis = data.map(item => item.aqi.AQI);

    return {
        label: left? leftLocationDetails.name: rightLocationDetails.name,
        data: aqis,
        backgroundColor: color,
        borderWidth: 1,
        fill: false,
    }
}
async function setCustomLabels(data,left = true) {
    let createdAts = data.map(item => item.created_at);
    if(left){
        comparativeUpdateDate_1 = createdAts[createdAts.length - 1];
    }else{
        comparativeUpdateDate_2 = createdAts[createdAts.length - 1];
    }
    const dateObj1 = new Date(comparativeUpdateDate_1);
    const dateObj2 = new Date(comparativeUpdateDate_2);

    if (dateObj1 > dateObj2) {
        comparativeUpdateDate_2 = comparativeUpdateDate_1;
    } else if (dateObj1 < dateObj2) {
        comparativeUpdateDate_1 = comparativeUpdateDate_2;
    }
    return getMonthsByDate(createdAts)
}
async function setCustomMax(data) {
    let aqis = data.map(item => item.aqi.AQI);
    return Math.ceil(Math.max(...aqis.map(Number)) / 100) * 100
}

</script>
