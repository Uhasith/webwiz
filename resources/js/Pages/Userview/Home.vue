<style>
/* Rotate content when in landscape orientation */
@media screen and (orientation: landscape) {
   /* #app {
        transform: rotate(-90deg);
        width: 100vh;
        height: 100vw;
        overflow: hidden;
        position: absolute;
        top: 50%;
        left: 50%;
        transform-origin: center center;
    } */
}

</style>
<template>
    <UserLayout>
        <div class="px-4 bg-lightergray py-6">
            <div class="text-center">
                <h1 class="text-lg md:text-3xl font-bold mb-3">
                    {{ $t("Air Quality Monitoring Dashboard") }}
                </h1>
                <p class="text-sm">
                    {{ $t("Real-Time Air Quality Data and Insights at Your Fingertips") }}
                </p>
            </div>
        </div>
        <div class="fixed inset-x-0 bottom-0 lg:hidden mobile_compare z-[2001] mb-16">
            <div class="flex justify-end px-4 pb-4">
                <Link
                    href="/public/comparison"
                    class="bg-green1 text-white rounded-full py-3 px-6 shadow-lg flex items-center space-x-2"
                >
                    <img src="/images/mobilecompare.svg" class="h-6 w-auto"/>
                    <span>Comparison</span>
                </Link>
            </div>
        </div>
        <div>
            <div class="w-full mx-auto lg:px-[50px] mt-8">
                <div class="grid grid-cols-1 lg:grid-cols-24 gap-5 first_div">
                    <div class="col-span-1 lg:col-span-19">
                        <div class="lg:flex lg:justify-between px-4 lg:px-0 mb-6 lg:mb-4">
                            <div class="flex justify-between">
                                <div>
                                    <h1 class="text-2xl md:text-[2.8rem] font-bold mb-3">
                                        {{ $t("Sri Lanka") }}
                                    </h1>
                                    <div class="inline-block text-left">
                                        <!-- <ProvinceDropdown icon="/images/pinicon.png" /> -->
                                        <div
                                            class="inline-flex justify-center w-full mt-1 lg:mt-2 text-sm font-medium text-gray-700 focus:outline-none"
                                        >
                                            <img src="/images/pinicon.png" class="h-4 w-auto mr-2"/>
                                            <span class="text-[#131313] text-[15px]">
                          {{
                                                    markarData.sensor_location ?
                                                        markarData.sensor_location?.location.name + "," + markarData.sensor_location?.location.district.province.province_name
                                                        : mapDefaultData?.sensor_location?.location.name + "," + mapDefaultData?.sensor_location?.location.district.province.province_name
                                                }}
                      </span>
                                        </div>
                                    </div>
                                </div>

                                <img
                                    src="/images/dropicon.svg"
                                    class="h-8 w-auto mr-2 block lg:hidden cursor-pointer"
                                    @click="showDropdown1 = !showDropdown1"
                                />
                            </div>
                            <div
                                class="lg:flex md:space-x-4 mt-[10px]"
                                :class="{ hidden: !showDropdown1 }"
                            >
                                <div class="mb-4">
                                    <EquipmentDropdown
                                        customClass="min-w-[206px] justify-between"
                                        :equipment-types="equipmentTypes"
                                        @type-changed="handleEquipmentTypeChange"
                                    />
                                </div>
                                <div class="mb-4">
                                    <MeasuredByDropdown ui="home-top"
                                        customClass="min-w-[206px] justify-between"
                                        :sensors="typeFilteredSensors"
                                        @sensor-changed="handleSensorChange"
                                    />
                                </div>
                                <div class="mb-4">
                                    <RankingDropdown
                                        customClass="min-w-[206px] justify-between"
                                        @ranking-changed="handleRankingChange"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="">
                            <div class="scale hidden lg:block">
                                <h3 class="text-lg font-bold mt-5">
                                    {{ $t("Air Quality Index Scale") }}
                                </h3>
                                <img src="/images/aqibar.png" class="w-full h-auto mt-2"/>
                            </div>

                            <MapChart :data="mapData" :default_data="mapDefaultData"/>
                            <!-- <img src="/images/tempmap.png" class="w-full h-auto mt-8" /> -->
                        </div>
                        <!-- Parent Container -->
                        <div
                            class="grid grid-cols-1 lg:grid-cols-8 gap-4 mt-8 px-4 lg:px-0"
                        >
                            <div v-if="markarData?.sensor_location || mapDefaultData?.aqi"
                                 class="bg-white border border-gray-200 rounded-lg p-4 lg:col-span-2"
                            >
                                <h3 class="font-bold text-lg mb-2">
                                    {{ $t("Live AQI Index") }}
                                </h3>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div>
                      <span class="text-xs text-gray-600">{{
                              $t("SL AQI")
                          }}</span>
                                            <div class="text-4xl font-bold">
                                                {{
                                                    markarData.sensor_location ? markarData.aqi.AQI : mapDefaultData?.aqi.AQI
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-px h-8 bg-gray-300 mx-4"></div>
                                    <div class="flex flex-col items-center">
                                        <span class="text-sm text-gray-600">{{
                                               $t( markarData.sensor_location?.location.name ?? mapDefaultData?.sensor_location?.location.name)
                                            }}</span>
                                        <img :src="faces(markarData?.aqi,mapDefaultData?.aqi)"
                                             class="h-10 mt-3"
                                        />
                                    </div>
                                </div>
                                <div style="margin-top: 21px" class=" text-center">
                                    <div :class="ranking(markarData?.aqi,mapDefaultData?.aqi)"
                                         class="text-md font-semibold border rounded-full py-1 my-4"
                                    >
                                        {{ $t(markarData?.aqi?.SL ?? mapDefaultData?.aqi.SL) }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $t("Last Update") }}:
                                        {{ formatDate(markarData?.created_at ?? mapDefaultData?.created_at) }}
                                    </div>
                                </div>
                            </div>
                            <div v-else
                                 class="bg-white text-gray-500 text-center border border-gray-200 rounded-lg p-4 lg:col-span-2">
                                No data available
                            </div>


                            <div
                                class="bg-white border border-gray-200 rounded-lg p-4 lg:col-span-3"
                            >
                                <AirPollutantParameters :data="markarData" :default-data="mapDefaultData"
                                                        :showHeaders="true" :complexLayout="false"/>
                            </div>
                            <div v-if="markarData?.weather_records || mapDefaultData?.weather_records"
                                 class="bg-white border border-gray-200 rounded-lg p-4 lg:col-span-3"
                            >
                                <h3 class="font-bold text-lg mb-2">{{$t("Weather Facts")}}</h3>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex items-start space-x-1">
                                            <p class="text-4xl font-bold">
                                                {{
                                                    markarData.sensor_location ? markarData.weather_records?.temperature : mapDefaultData?.weather_records?.temperature
                                                }}

                                            </p>
                                            <div class="flex flex-col">
                                                <p class="text-sm">°C</p>
                                                <p class="text-xs">
                                                    {{
                                                        markarData.sensor_location ? markarData.weather_records?.cloud : mapDefaultData?.weather_records?.cloud
                                                    }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-center">
                                        <img
                                            :src="weatherIcons(markarData.sensor_location ? markarData.weather_records?.cloud : mapDefaultData?.weather_records?.cloud)"
                                            class="h-10"
                                        />
                                    </div>
                                </div>
                                <div class="w-full flex space-x-2 mt-5">
                                    <div
                                        class="flex-1 custom_bg_gray border custom_border_color p-2 rounded-lg flex flex-col items-center justify-center"
                                    >
                                        <img
                                            src="/images/drop.png"
                                            class="h-4"
                                            alt="Sunny weather"
                                        />
                                        <p class="text-xs">{{ $t("Humidity") }}</p>
                                        <p class="text-sm font-bold">
                                            {{
                                                markarData.sensor_location ? markarData.weather_records?.humidity : mapDefaultData?.weather_records?.humidity
                                            }}%
                                        </p>
                                    </div>
                                    <div
                                        class="flex-1 custom_bg_gray border custom_border_color rounded-lg p-2 flex flex-col items-center justify-center"
                                    >
                                        <img
                                            src="/images/wind.png"
                                            class="h-4"
                                            alt="Sunny weather"
                                        />
                                        <p class="text-xs">{{ $t("Wind") }}</p>
                                        <p class="text-sm font-bold">
                                            {{
                                                markarData.sensor_location ? markarData.weather_records?.wind : mapDefaultData?.weather_records?.wind
                                            }}
                                            km/h
                                        </p>
                                    </div>
                                    <div
                                        class="flex-1 custom_bg_gray border custom_border_color rounded-lg p-2 flex flex-col items-center justify-center"
                                    >
                                        <img
                                            src="/images/pressure.png"
                                            class="h-4"
                                            alt="Sunny weather"
                                        />
                                        <p class="text-xs">{{ $t("Pressure") }}</p>
                                        <p class="text-sm font-bold">
                                            {{
                                                markarData.sensor_location ? markarData.weather_records?.pressure : mapDefaultData?.weather_records?.pressure
                                            }}
                                            mb

                                        </p>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-3 text-center">
                                    {{ $t("Last Update") }}:
                                    {{
                                        formatDate(markarData?.weather_records?.created_at ?? mapDefaultData?.weather_records?.created_at)
                                    }}
                                </div>
                            </div>
                            <div v-else
                                 class="text-gray-500 text-center bg-white border border-gray-200 rounded-lg p-4 lg:col-span-3">
                                No data available
                            </div>

                        </div>
                    </div>
                    <div
                        class="hidden lg:block col-span-1 lg:col-span-5 border border-gray-200 rounded-lg p-4 bg-white"
                    >
                        <h3 class="text-lg font-bold mb-4">
                            {{ $t("Alerts & Notifications") }}
                        </h3>
                        <div style="height: 700px;overflow-y: auto;">
                            <Notification/>
                        </div>
                    </div>
                </div>

                <!-- Historical Data -->
                <HistoricData :initial-locations="initialLocations" :sensors="sensors"/>

                <!-- Comparative Analysis -->
                <ComparativeAnalysis :initial-locations="initialLocations" :sensors="sensors"/>
            </div>
        </div>
        <!-- <p>{{ markarData?.sensor_location?.location?.name || "not working" }}</p> -->
        <!-- <Vuexcomponent /> -->
    </UserLayout>
</template>

<script setup>
import {computed, onMounted, ref} from "vue";
import {Link} from "@inertiajs/vue3";
import UserLayout from "@/Layouts/UserLayout.vue";
import EquipmentDropdown from "@/Components/Custom/Dropdowns/EquipmentDropdown.vue";
import MeasuredByDropdown from "@/Components/Custom/Dropdowns/MeasuredByDropdown.vue";
import Notification from "@/Components/Custom/Notification.vue";
import RankingDropdown from "@/Components/Custom/Dropdowns/RankingDropdown.vue";
import MapChart from "@/Components/Custom/MapChart.vue";
import getSensorsByType from "@/Services/getSensorsByType";
import getMapDataService from "@/Services/getMapData";
import AirPollutantParameters from "@/Components/Custom/Widgets/AirPollutantParameters.vue";
import {useStore} from "vuex";
import HistoricData from "@/Components/Custom/Widgets/HistoricData.vue";
import ComparativeAnalysis from "@/Components/Custom/Widgets/ComparativeAnalysis.vue";

import {formatDate} from "@/Utils/LastUpdate";
import {ranking, faces} from "@/Utils/Ranking";
import "../../../css/rankings.css";
import {weatherIcons} from "@/Utils/WeatherData.js";
import axios from "axios";

const showDropdown1 = ref(false);
let equipmentTypeId = "all";
let sensorId = "all";
let rankingId = "all";


const mapData = ref(null);
const mapDefaultData = ref(null);

const props = defineProps({
    equipmentTypes: Object,
    sensors: Object,
    initialLocations: Array,
});


const store = useStore();
let markarData = computed(() => store.state.markarData);
const selectedEquipmentType = ref({id: "all", name: "All"});
const typeFilteredSensors = ref(props.sensors);
const selectedSensor = ref({id: "all", name: "All"});

async function handleEquipmentTypeChange(newType) {
    selectedEquipmentType.value = newType;
    sensorId = "all";
    equipmentTypeId = newType.id;
    typeFilteredSensors.value = await getSensorsByType.fetchSensorsByType(equipmentTypeId);
    mapData.value = (await getMapDataService.fetchData(equipmentTypeId, sensorId,rankingId)).data;
}

async function handleSensorChange(newSensor) {
    selectedSensor.value = newSensor;
    sensorId = newSensor.id;
    equipmentTypeId = selectedEquipmentType.value.id;
    mapData.value = (await getMapDataService.fetchData(
        equipmentTypeId,
        sensorId,
        rankingId
    )).data;
}

async function handleRankingChange(ranking) {
    rankingId = ranking.id;
    mapData.value = (await getMapDataService.fetchData(
        equipmentTypeId,
        sensorId,
        rankingId
    )).data;
}


onMounted(async () => {
    await fetchData();
    setInterval(async () => {
        await fetchData();
    }, 60100 * 5);

    async function fetchData() {
        let response = await getMapDataService.fetchData(equipmentTypeId, sensorId,rankingId);

        mapData.value = response.data;
        mapDefaultData.value = response.defaultData;
        markarData = computed(() => store.state.markarData);

        if (markarData.value?.id) {
            let previousSelectedMarker = mapData.value.find(item => item.sensor_location_id === markarData.value.sensor_location_id);
            if(previousSelectedMarker){
                store.commit("updateMarkarData", previousSelectedMarker);
            }
        }
    }
});

// function getCurrentLocationName(){
//     return markarData.sensor_location ?
//          markarData.sensor_location?.location.name+","+markarData.sensor_location?.location.district.province.province_name
//         : this.mapDefaultData?.sensor_location?.location.name+","+this.mapDefaultData?.sensor_location?.location.district.province.province_name;
// }
</script>

