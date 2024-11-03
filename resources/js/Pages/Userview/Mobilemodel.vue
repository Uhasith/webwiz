<template>
  <div class=" md:w-[600px] mx-auto p-2 bg-white">
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

    <!-- <div class="flex space-x-2">
      <EquipmentDropdown />
      <MeasuredByDropdown />
    </div> -->

<!--    <div>-->
<!--      <EquipmentDropdown />-->
<!--      <MeasuredByDropdown class="mt-3" />-->
<!--    </div>-->

    <div class="w-full  flex justify-center items-center mt-[26px]">
      <img src="/images/pinicon.png" class="h-4 w-auto mr-2" />
      <p class="font-bold text-lg">
        {{
          markarData?.sensor_location?.location?.name ||
          ""
        }}
      </p>
    </div>

    <div class="grid grid-cols-8 gap-4 mt-[26px]">
      <div class="col-span-4 flex items-center justify-center">
        <Chart3 :data="markarData" />
      </div>
      <div class="col-span-4">
        <div class="mb-4">
          <div class="w-full text-center">
            <div class="inline-flex items-center space-x-1">
              <p class="text-2xl font-bold"> {{
                      markarData?.weather_records?.temperature
                  }}</p>
              <div class="flex flex-col">
                <p class="text-sm">°C</p>
                <p class="text-xs">{{
                        markarData?.weather_records?.cloud
                    }}</p>
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
                <span class="text-2xl font-bold">{{
                        markarData?.weather_records?.humidity
                    }}</span>
                <span class="text-sm font-bold">%</span>
              </div>
              <span class="text-md text-gray-600 ml-1">{{
                $t("Humidity")
              }}</span>
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
          <p class="text-xs">{{ $t("Pressure") }}</p>
          <p class="text-sm font-bold">{{
                  markarData?.weather_records?.pressure
              }}mb</p>
        </div>
      </div>

      <div class="flex-1">
        <div
          class="w-full py-3 px-7 border custom_border_color custom_bg_gray rounded-lg mt-4 flex flex-col items-center justify-center"
        >
          <img src="/images/wind.png" class="h-6" alt="Wind icon" />
          <p class="text-xs">{{ $t("Wind") }}</p>
          <p class="text-sm font-bold">{{
                  markarData?.weather_records?.wind
              }}km/h</p>
        </div>
      </div>
    </div>

    <div>
      <h3 class="mt-7 text-[20px] font-bold">AQI Trend in Last 24 Hours</h3>
    </div>

    <div class="mt-6">
      <Chart5 :data="markarData" />
    </div>

    <div class="bg-white rounded-lg md:col-span-3">
      <h3 class="font-bold text-md mt-6">
        {{ $t("Air Pollutant Parameters") }}
      </h3>
      <div class="grid grid-cols-6 gap-1 mt-6">
        <div
          class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
        >
          <div>
            <p class="text-[10px] text-gray-500">PM2.5</p>
            <div v-if=" markarData?.pm2_5_status === 'ACTIVE'"
              class="flex flex-row items-center justify-start text-left font-semibold"
            >
              <div class="w-1/2 text-[10px] md:text-[12px] mr-[-5px]">{{ markarData?.pm2_5 }}</div>
              <div class="w-1/2 text-[8px]">µg/m³</div>
            </div>
            <div v-if=" markarData?.pm2_5_status === 'ACTIVE'"
              class="flex flex-row items-center justify-between font-semibold"
            >
              <div class="w-1/2 text-[8px]">AQI</div>
              <div
                class="w-1/2 text-[8px] text-center rounded-lg px-[2px] bg-gray-200"
              >
                  {{ markarData?.pm2_5_aqi?.AQI }}
              </div>
            </div>
          </div>
          <div v-if=" markarData?.pm2_5_status === 'ACTIVE'" class="bg-green3 h-10 rounded-md mt-2"></div>
        </div>
        <div
          class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
        >
          <div>
            <p class="text-[10px] text-gray-500">PM10</p>
            <div v-if=" markarData?.pm10_status === 'ACTIVE'"
              class="flex flex-row items-center justify-start text-left font-semibold"
            >
              <div class="w-1/2 text-[10px] md:text-[12px] mr-[-5px]"> {{ markarData?.pm10 }}</div>
              <div class="w-1/2 text-[8px]">µg/m³</div>
            </div>
            <div v-if=" markarData?.pm10_status === 'ACTIVE'"
              class="flex flex-row items-center justify-between font-semibold"
            >
              <div class="w-1/2 text-[8px]">AQI</div>
              <div
                class="w-1/2 text-[8px] text-center rounded-lg px-[2px] bg-gray-200"
              >
                  {{ markarData?.pm10_aqi?.AQI }}
              </div>
            </div>
          </div>
          <div v-if=" markarData?.pm10_status === 'ACTIVE'" class="bg-green3 h-12 rounded-md mt-2"></div>
        </div>
        <div
          class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
        >
          <div>
            <p class="text-[10px] text-gray-500">O3</p>
            <div v-if=" markarData?.o3_status === 'ACTIVE'"
              class="flex flex-row items-center justify-start text-left font-semibold"
            >
              <div class="w-1/2 text-[10px] md:text-[12px] mr-[-5px]">{{ markarData?.o3 }}</div>
              <div class="w-1/2 text-[8px]">ppb</div>
            </div>
            <div v-if=" markarData?.o3_status === 'ACTIVE'"
              class="flex flex-row items-center justify-between font-semibold"
            >
              <div class="w-1/2 text-[8px]">AQI</div>
              <div
                class="w-1/2 text-[8px] text-center rounded-lg px-[2px] bg-gray-200"
              >
                  {{ markarData?.o3_aqi?.AQI }}
              </div>
            </div>
          </div>
          <div v-if=" markarData?.o3_status === 'ACTIVE'" class="bg-green3 h-5 rounded-md mt-2"></div>
        </div>
        <div
          class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
        >
          <div>
            <p class="text-[10px] text-gray-500">CO</p>
            <div v-if=" markarData?.co_status === 'ACTIVE'"
              class="flex flex-row items-center justify-start text-left font-semibold"
            >
              <div class="w-1/2 text-[10px] md:text-[12px] mr-[-5px]">{{ markarData?.co }}</div>
              <div class="w-1/2 text-[8px]">ppb</div>
            </div>
            <div v-if=" markarData?.co_status === 'ACTIVE'"
              class="flex flex-row items-center justify-between font-semibold"
            >
              <div class="w-1/2 text-[8px]">AQI</div>
              <div
                class="w-1/2 text-[8px] text-center rounded-lg px-[2px] bg-gray-200"
              >
                  {{ markarData?.co_aqi?.AQI }}
              </div>
            </div>
          </div>
          <div v-if=" markarData?.co_status === 'ACTIVE'" class="bg-green3 h-12 rounded-md mt-2"></div>
        </div>
        <div
          class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
        >
          <div>
            <p class="text-[10px] text-gray-500">NO2</p>
            <div v-if=" markarData?.no2_status === 'ACTIVE'"
              class="flex flex-row items-center justify-start text-left font-semibold"
            >
              <div class="w-1/2 text-[10px] md:text-[12px] mr-[-5px]">{{ markarData?.no2 }}</div>
              <div class="w-1/2 text-[8px]">ppb</div>
            </div>
            <div v-if=" markarData?.no2_status === 'ACTIVE'"
              class="flex flex-row items-center justify-between font-semibold"
            >
              <div class="w-1/2 text-[8px]">AQI</div>
              <div
                class="w-1/2 text-[8px] text-center rounded-lg px-[2px] bg-gray-200"
              >
                  {{ markarData?.n02_aqi?.AQI }}
              </div>
            </div>
          </div>
          <div v-if=" markarData?.no2_status === 'ACTIVE'"  class="bg-green3 h-12 rounded-md mt-2"></div>
        </div>
        <div
          class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
        >
          <div>
            <p class="text-[10px] text-gray-500">SO2</p>
            <div v-if=" markarData?.so2_status === 'ACTIVE'"
              class="flex flex-row items-center justify-start text-left font-semibold"
            >
              <div class="w-1/2 text-[10px] md:text-[12px] mr-[-5px]"> {{ markarData?.so2 }}</div>
              <div class="w-1/2 text-[8px]">ppb</div>
            </div>
            <div v-if=" markarData?.so2_status === 'ACTIVE'"
              class="flex flex-row items-center justify-between font-semibold"
            >
              <div class="w-1/2 text-[8px]">AQI</div>
              <div
                class="w-1/2 text-[8px] text-center rounded-lg px-[2px] bg-gray-200"
              >
                  {{ markarData?.so2_aqi?.AQI }}
              </div>
            </div>
          </div>
          <div v-if=" markarData?.so2_status === 'ACTIVE'" class="bg-green3 h-12 rounded-md mt-2"></div>
        </div>
      </div>

      <div class="text-xs text-gray-500 text-center mt-6">
        {{ $t("Last Update") }}:{{formatDate( markarData?.created_at) }}
      </div>
    </div>
  </div>
</template>

<script setup>
import EquipmentDropdown from "@/Components/Custom/Dropdowns/Popup/EquipmentDropdown.vue";
import MeasuredByDropdown from "@/Components/Custom/Dropdowns/Popup/MeasuredByDropdown.vue";
import { Link } from "@inertiajs/vue3";
import { formatDate } from "@/Utils/LastUpdate";
import Chart3 from "@/Components/Custom/Charts/Chart3.vue";
import Chart5 from "@/Components/Custom/Charts/Chart5.vue";
import { useI18n } from "vue-i18n";
import { useStore } from "vuex";
import { computed, onMounted, watch } from "vue";

const { t } = useI18n();

const store = useStore();

let markarData = computed(() => store.state.markarData);

watch(() => store.state.markarData, (newData) => {
});
onMounted(() => {

});


</script>
