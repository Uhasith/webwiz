<template>
  <div class="max-w-md mx-auto p-4 bg-white">
    <div class="flex justify-start mb-4">
      <Link href="/" class="text-gray-600">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10 19l-7-7m0 0l7-7m-7 7h18"
          />
        </svg>
      </Link>
    </div>
    <div>
      <h1 class="text-2xl font-bold">{{ $t('Comparative Analysis') }}</h1>
      <p class="text-md">{{ $t('Compare Air Quality Across Locations') }}</p>

      <div>
        <div class="mt-5">
            <CompareCityAfter ui="CompareCityBefore"
                               :initial-locations="initialLocations"
            />
        </div>
        <div class="mt-4">
            <CompareCityAfter ui="CompareCityBefore"
                               :initial-locations="initialLocations"
            />
        </div>

        <button
          @click="toggleDropdown"
          class="text-md mt-5 py-2 px-6 justify-center w-full font-semibold rounded-full text-white bg-green1"
        >
          Compare
        </button>
      </div>

      <div class="border p-4 bg-white rounded-lg mt-5">
        <div class="w-full flex justify-end">
            <div class="relative">
                <MeasuredByDropdown ui="home-mobile"
                                    customClass="min-w-[206px] justify-between"
                                    :sensors="typeFilteredSensors"
                                    @sensor-changed="handleSensorChange"
                />
            </div>
        </div>

        <div class="w-full flex justify-center items-center mt-4 mb-2">
          <img src="/images/greenpinicon.png" class="h-4 w-auto mr-2" />
          <p class="font-bold text-lg text-green3">Pannipitiya Road, Colombo</p>
        </div>

        <div class="grid grid-cols-8 gap-4 mt-5">
          <div class="col-span-4">
            <Chart3 />
          </div>
          <div class="col-span-4">
            <div class="mb-4">
              <div class="w-full text-center">
                <div class="inline-flex items-center space-x-1">
                  <p class="text-2xl font-bold">27</p>
                  <div class="flex flex-col">
                    <p class="text-sm">°C</p>
                    <p class="text-xs">{{ $t('Sunny') }}</p>
                  </div>
                </div>
                <img
                  src="/images/sunny.png"
                  alt="Humidity"
                  class="w-8 h-8 mt-1 mx-auto"
                />
              </div>
            </div>

            <!-- Existing content -->
            <div class="flex justify-center">
              <div>
                <div class="flex items-center">
                  <div class="flex items-start">
                    <span class="text-2xl font-bold">0</span>
                    <span class="text-sm font-bold">%</span>
                  </div>
                  <span class="text-md text-gray-600 ml-1">{{ $t('Humidity') }}</span>
                </div>
                <img
                  src="/images/drops.png"
                  alt="Humidity"
                  class="w-8 h-8 mt-1 mx-auto"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-center items-center space-x-2">
          <div class="flex-1">
            <div
              class="w-full py-3 px-7 border custom_border_color custom_bg_gray rounded-lg mt-4 flex flex-col items-center justify-center"
            >
              <img src="/images/pressure.png" class="h-6" alt="Pressure icon" />
              <p class="text-xs">{{ $t('Pressure') }}</p>
              <p class="text-sm font-bold">0%</p>
            </div>
          </div>

          <div class="flex-1">
            <div
              class="w-full py-3 px-7 border custom_border_color custom_bg_gray rounded-lg mt-4 flex flex-col items-center justify-center"
            >
              <img src="/images/wind.png" class="h-6" alt="Wind icon" />
              <p class="text-xs">{{ $t('Wind') }}</p>
              <p class="text-sm font-bold">0%</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg md:col-span-3 mt-5">
          <h3 class="font-bold text-lg mb-2">{{ $t('Air Pollutant Parameters') }}</h3>
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div>
                <span class="text-xs text-gray-600">{{ $t("SL AQI") }}</span>
              </div>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-sm text-gray-600">Colombo</span>
            </div>
          </div>
          <div class="grid grid-cols-7 gap-1">
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">PM2.5</p>
                <p class="text-sm font-bold">130</p>
              </div>
              <div class="bg-green3 h-10 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">PM10</p>
                <p class="text-sm font-bold">80</p>
              </div>
              <div class="bg-green3 h-12 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">O3</p>
                <p class="text-sm font-bold">12</p>
              </div>
              <div class="bg-green3 h-5 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">CO</p>
                <p class="text-sm font-bold">12</p>
              </div>
              <div class="bg-green3 h-12 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">NO2</p>
                <p class="text-sm font-bold">12</p>
              </div>
              <div class="bg-green3 h-12 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">SO2</p>
                <p class="text-sm font-bold">80</p>
              </div>
              <div class="bg-green3 h-12 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">CO2</p>
                <p class="text-sm font-bold">12</p>
              </div>
              <div class="bg-green3 h-12 rounded-md mt-2"></div>
            </div>
          </div>
          <div class="text-xs text-gray-500 text-center mt-3">
            {{ $t("Last Update") }}: 01 July 2024, 08.25 AM
          </div>
        </div>
      </div>

      <div class="border p-4 bg-white rounded-lg mt-5">
        <div class="w-full flex justify-end">
          <div class="relative">
              <MeasuredByDropdown ui="home-mobile"
                                  customClass="min-w-[206px] justify-between"
                                  :sensors="typeFilteredSensors"
                                  @sensor-changed="handleSensorChange"
              />
          </div>
        </div>

        <div class="w-full flex justify-center items-center mt-4 mb-2">
          <img src="/images/greenpinicon.png" class="h-4 w-auto mr-2" />
          <p class="font-bold text-lg text-green3">Pannipitiya Road, Colombo</p>
        </div>

        <div class="grid grid-cols-8 gap-4 mt-5">
          <div class="col-span-4">
            <Chart3 />
          </div>
          <div class="col-span-4">
            <div class="mb-4">
              <div class="w-full text-center">
                <div class="inline-flex items-center space-x-1">
                  <p class="text-2xl font-bold">27</p>
                  <div class="flex flex-col">
                    <p class="text-sm">°C</p>
                    <p class="text-xs">{{ $t('Sunny') }}</p>
                  </div>
                </div>
                <img
                  src="/images/sunny.png"
                  alt="Humidity"
                  class="w-8 h-8 mt-1 mx-auto"
                />
              </div>
            </div>

            <!-- Existing content -->
            <div class="flex justify-center">
              <div>
                <div class="flex items-center">
                  <div class="flex items-start">
                    <span class="text-2xl font-bold">0</span>
                    <span class="text-sm font-bold">%</span>
                  </div>
                  <span class="text-md text-gray-600 ml-1">{{ $t('Humidity') }}</span>
                </div>
                <img
                  src="/images/drops.png"
                  alt="Humidity"
                  class="w-8 h-8 mt-1 mx-auto"
                />
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-center items-center space-x-2">
          <div class="flex-1">
            <div
              class="w-full py-3 px-7 border custom_border_color custom_bg_gray rounded-lg mt-4 flex flex-col items-center justify-center"
            >
              <img src="/images/pressure.png" class="h-6" alt="Pressure icon" />
              <p class="text-xs">{{ $t('Pressure') }}</p>
              <p class="text-sm font-bold">0%</p>
            </div>
          </div>

          <div class="flex-1">
            <div
              class="w-full py-3 px-7 border custom_border_color custom_bg_gray rounded-lg mt-4 flex flex-col items-center justify-center"
            >
              <img src="/images/wind.png" class="h-6" alt="Wind icon" />
              <p class="text-xs">{{ $t('Wind') }}</p>
              <p class="text-sm font-bold">0%</p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg md:col-span-3 mt-5">
          <h3 class="font-bold text-lg mb-2">{{ $t('Air Pollutant Parameters') }}</h3>
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <div>
                <span class="text-xs text-gray-600">{{ $t("SL AQI") }}</span>
              </div>
            </div>
            <div class="flex flex-col items-center">
              <span class="text-sm text-gray-600">Colombo</span>
            </div>
          </div>
          <div class="grid grid-cols-7 gap-1">
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">PM2.5</p>
                <p class="text-sm font-bold">130</p>
              </div>
              <div class="bg-green3 h-10 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">PM10</p>
                <p class="text-sm font-bold">80</p>
              </div>
              <div class="bg-green3 h-12 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">O3</p>
                <p class="text-sm font-bold">12</p>
              </div>
              <div class="bg-green3 h-5 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">CO</p>
                <p class="text-sm font-bold">12</p>
              </div>
              <div class="bg-green3 h-12 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">NO2</p>
                <p class="text-sm font-bold">12</p>
              </div>
              <div class="bg-green3 h-12 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">SO2</p>
                <p class="text-sm font-bold">80</p>
              </div>
              <div class="bg-green3 h-12 rounded-md mt-2"></div>
            </div>
            <div
              class="col-span-1 border border-gray-200 rounded-md p-1 flex flex-col justify-between"
            >
              <div>
                <p class="text-xs text-gray-500">CO2</p>
                <p class="text-sm font-bold">12</p>
              </div>
              <div class="bg-green3 h-12 rounded-md mt-2"></div>
            </div>
          </div>
          <div class="text-xs text-gray-500 text-center mt-3">
            {{ $t("Last Update") }}: 01 July 2024, 08.25 AM
          </div>
        </div>
      </div>

      <div class="grid lg:grid-cols-2 grid-cols-1 gap-4 my-8">
        <div class="border p-5 bg-white rounded-lg max-sm:overflow-y-scroll">
          <div class="lg:flex items-center justify-between">
            <div class="flex justify-between">
              <h2 class="text-md font-semibold mb-2 mt-2 lg:mt-0">
                {{ $t('Comparative AQI level') }}
              </h2>

<!--              <img-->
<!--                src="/images/dropicon.svg"-->
<!--                class="h-8 w-auto block lg:hidden cursor-pointer"-->
<!--                @click="showDropdown1 = !showDropdown1"-->
<!--              />-->
            </div>
            <div
              class="lg:flex lg:space-x-2"
              :class="{ hidden: !showDropdown1 }"
            >
              <div class="mb-2"><SelectParameterDropdown /></div>
              <div class="mb-2">  <EquipmentDropdown
                  customClass="min-w-[206px] justify-between"
                  :equipment-types="equipmentTypes"
                  @type-changed="handleEquipmentTypeChange"
              />
              </div>
            </div>
          </div>

          <hr class="my-4" />

          <Chart1 />
          <p class="w-full text-center text-xs text-gray-500 mt-3">
            {{ $t("Last Update") }}: 01 July 2024, 08.25 AM
          </p>
        </div>
        <div class="border p-5 bg-white rounded-lg max-sm:overflow-y-scroll">
          <div class="lg:flex items-center justify-between">
            <div class="flex justify-between">
              <h2 class="text-md font-semibold mb-2 mt-2 lg:mt-0">
                {{ $t('Comparative pollutant level') }}
              </h2>
              <img
                src="/images/dropicon.svg"
                class="h-8 w-auto block lg:hidden cursor-pointer"
                @click="showDropdown2 = !showDropdown2"
              />
            </div>
            <div
              class="lg:flex lg:space-x-2"
              :class="{ hidden: !showDropdown2 }"
            >
              <div class="mb-2"><SelectParameterDropdown /></div>
<!--              <div class="mb-2"><SimpleEquipmentDropdown /></div>-->
            </div>
          </div>

          <hr class="my-4" />

          <Chart4 />
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { Link } from "@inertiajs/vue3";
import SelectCityDropDown from "@/Components/Custom/Dropdowns/SelectCityDropDown.vue";
import MeasuredByDropdown from "@/Components/Custom/Dropdowns/MeasuredByDropdown.vue";
import SelectParameterDropdown from "@/Components/Custom/Dropdowns/SelectParameterDropdown.vue";
import SimpleEquipmentDropdown from "@/Components/Custom/Dropdowns/SimpleEquipmentDropdown.vue";
import Chart1 from "@/Components/Custom/Charts/Chart1.vue";
import Chart3 from "@/Components/Custom/Charts/Chart3.vue";
import Chart4 from "@/Components/Custom/Charts/Chart4.vue";
import { ref } from "vue";
import { useI18n } from "vue-i18n";
import EquipmentDropdown from "@/Components/Custom/Dropdowns/Popup/EquipmentDropdown.vue";
import getSensorsByType from "@/Services/getSensorsByType.js";
import getMapDataService from "@/Services/getMapData.js";
import CompareCityBefore from "@/Components/Custom/Widgets/CompareCityBefore.vue";
import CompareCityAfter from "@/Components/Custom/Widgets/CompareCityAfter.vue";

const { t } = useI18n();
const props = defineProps({
    equipmentTypes: Object,
    sensors: Object,
    initialLocations: Array,
});
let equipmentTypeId = null;
let sensorId = "all";
let rankingId = "all";
const selectedEquipmentType = ref({id: "all", name: "All"});
const typeFilteredSensors = ref(props.sensors);
const selectedSensor = ref({id: "all", name: "All"});

async function handleEquipmentTypeChange(newType) {
    selectedEquipmentType.value = newType;
    sensorId = "all";
    equipmentTypeId = newType.id;
    typeFilteredSensors.value = await getSensorsByType.fetchSensorsByType(equipmentTypeId);
}
async function handleSensorChange(newSensor) {
    selectedSensor.value = newSensor;
    sensorId = newSensor.id;
    equipmentTypeId = selectedEquipmentType.value.id;
}
const showDropdown1 = ref(false);
const showDropdown2 = ref(false);
</script>
