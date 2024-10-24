<template>
  <div v-if="isDataAvailable || isDefaultDataAvailable">
    <h3 class="font-bold font-[20px] mb-2">
      {{ $t("Air Pollutant Parameters") }}
    </h3>
    <div v-if="showHeaders" class="flex items-center justify-between">
      <div class="flex items-center">
        <div>
          <span class="text-xs text-gray-600">{{ $t("SL AQI") }}</span>
        </div>
      </div>
      <div class="flex flex-col items-center">
        <span class="text-sm text-gray-600">{{ getData().sensor_location?.location.name }}</span>
      </div>
    </div>
    <div class="grid grid-cols-6 gap-1">
      <div
        class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
      >
        <div>
          <p class="text-xs text-gray-500">PM2.5</p>
          <div v-if="getData().pm2_5_status === 'ACTIVE'"
            class="flex flex-row items-center justify-start text-left font-semibold"
          >
            <div class="w-1/2 text-[14px] mr-[-8px]">{{ getData().pm2_5 }}</div>
            <div class="w-1/2 text-[10px]" v-if="complexLayout && getData().pm2_5_status === 'ACTIVE'">µg/m³</div>
          </div>
          <div class="flex flex-row items-center justify-between font-semibold" v-if="complexLayout">
            <div class="w-1/2 text-[10px]" v-if="getData().pm2_5_status === 'ACTIVE'">AQI={{ getData().pm2_5_aqi?.AQI }}</div>
            <div
              class="w-1/2 text-[10px] text-center rounded-lg px-[2px] bg-gray-200"
            >
              {{ getData().pm2_5_aqi?.sl }}
            </div>
          </div>
        </div>
        <div v-if="getData().pm2_5_status === 'ACTIVE'" class="bg-green3 h-14 rounded-md mt-2"></div>
      </div>
      <div
        class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
      >
        <div>
          <p class="text-xs text-gray-500">PM10</p>
          <div v-if="getData().pm10_status === 'ACTIVE'"
            class="flex flex-row items-center justify-start text-left font-semibold"
          >
            <div class="w-1/2 text-[14px] mr-[-8px]">{{ getData().pm10 }}</div>
            <div class="w-1/2 text-[10px]" v-if="complexLayout && getData().pm10_status === 'ACTIVE'">µg/m³</div>
          </div>
          <div class="flex flex-row items-center justify-between font-semibold" v-if="complexLayout">
              <div class="w-1/2 text-[10px] d-flex" v-if="getData().pm10_status === 'ACTIVE'">AQI={{ getData().pm10_aqi?.AQI }}</div>
            <div
              class="w-1/2 text-[10px] text-center rounded-lg px-[2px] bg-gray-200"
            >
              {{ getData().pm10_aqi?.sl }}
            </div>
          </div>
        </div>
        <div v-if="getData().pm10_status === 'ACTIVE'" class="bg-green3 h-12 rounded-md mt-2"></div>
      </div>
      <div
        class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
      >
        <div>
          <p class="text-xs text-gray-500">O3</p>
          <div v-if="getData().o3_status === 'ACTIVE'"
            class="flex flex-row items-center justify-start text-left font-semibold"
          >
            <div class="w-1/2 text-[14px] mr-[-8px]">{{ getData().o3 }}</div>
            <div class="w-1/2 text-[10px]" v-if="complexLayout && getData().o3_status === 'ACTIVE'">ppb</div>
          </div>
          <div class="flex flex-row items-center justify-between font-semibold" v-if="complexLayout">
              <div class="w-1/2 text-[10px]" v-if="getData().o3_status === 'ACTIVE'">AQI={{ getData().o3_aqi?.AQI }}</div>
            <div
              class="w-1/2 text-[10px] text-center rounded-lg px-[2px] bg-gray-200"
            >
              {{ getData().o3_aqi?.sl }}
            </div>
          </div>
        </div>
        <div v-if="getData().o3_status === 'ACTIVE'" class="bg-green3 h-5 rounded-md mt-2"></div>
      </div>
      <div
        class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
      >
        <div>
          <p class="text-xs text-gray-500">CO</p>
          <div v-if="getData().co_status === 'ACTIVE'"
            class="flex flex-row items-center justify-start text-left font-semibold"
          >
            <div class="w-1/2 text-[14px] mr-[-8px]">{{ getData().co }}</div>
            <div class="w-1/2 text-[10px]" v-if="complexLayout && getData().co_status === 'ACTIVE'">ppb</div>
          </div>
          <div class="flex flex-row items-center justify-between font-semibold" v-if="complexLayout">
              <div class="w-1/2 text-[10px]" v-if="getData().co_status === 'ACTIVE'">AQI={{ getData().co_aqi?.AQI }}</div>
            <div
              class="w-1/2 text-[10px] text-center rounded-lg px-[2px] bg-gray-200"
            >
              {{ getData().co_aqi?.sl }}
            </div>
          </div>
        </div>
        <div v-if="getData().co_status === 'ACTIVE'" class="bg-green3 h-12 rounded-md mt-2"></div>
      </div>
      <div
        class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
      >
        <div>
          <p class="text-xs text-gray-500">NO2</p>
          <div v-if="getData().no2_status === 'ACTIVE'"
            class="flex flex-row items-center justify-start text-left font-semibold"
          >
            <div class="w-1/2 text-[14px] mr-[-8px]">{{ getData().no2 }}</div>
            <div class="w-1/2 text-[10px]" v-if="complexLayout && getData().no2_status === 'ACTIVE'">ppb</div>
          </div>
          <div class="flex flex-row items-center justify-between font-semibold" v-if="complexLayout">
              <div class="w-1/2 text-[10px]" v-if="getData().no2_status === 'ACTIVE'">AQI={{ getData().no2_aqi?.AQI }}</div>
            <div
              class="w-1/2 text-[10px] text-center rounded-lg px-[2px] bg-gray-200"
            >
              {{ getData().no2_aqi?.sl }}
            </div>
          </div>
        </div>
        <div v-if="getData().no2_status === 'ACTIVE'" class="bg-green3 h-12 rounded-md mt-2"></div>
      </div>
      <div
        class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"
      >
        <div>
          <p class="text-xs text-gray-500">SO2</p>
          <div v-if="getData().so2_status === 'ACTIVE'"
            class="flex flex-row items-center justify-start text-left font-semibold"
          >
            <div class="w-1/2 text-[14px] mr-[-8px]">{{ getData().so2 }}</div>
            <div class="w-1/2 text-[10px]" v-if="complexLayout && getData().so2_status === 'ACTIVE'">µg/m³</div>
          </div>
          <div class="flex flex-row items-center justify-between font-semibold" v-if="complexLayout">
              <div class="w-1/2 text-[10px]" v-if="getData().so2_status === 'ACTIVE'">AQI={{ getData().so2_aqi?.AQI }}</div>
            <div
              class="w-1/2 text-[10px] text-center rounded-lg px-[2px] bg-gray-200"
            >
              {{ getData().so2_aqi?.sl }}
            </div>
          </div>
        </div>
        <div v-if="getData().so2_status === 'ACTIVE'" class="bg-green3 h-12 rounded-md mt-2"></div>
      </div>
<!--      <div-->
<!--        class="col-span-1 border custom_border_color rounded-md p-1 flex flex-col justify-between"-->
<!--      >-->
<!--        <div>-->
<!--          <p class="text-xs text-gray-500">CO2</p>-->
<!--          <div v-if="getData().co2_status === 'ACTIVE'"-->
<!--            class="flex flex-row items-center justify-start text-left font-semibold"-->
<!--          >-->
<!--            <div class="w-1/2 text-[14px] mr-[-8px]">{{ getData().co2 }}</div>-->
<!--            <div class="w-1/2 text-[10px]" v-if="complexLayout">µg/m³</div>-->
<!--          </div>-->
<!--          <div class="flex flex-row items-center justify-between font-semibold" v-if="complexLayout">-->
<!--              <div class="w-1/2 text-[10px]" v-if="getData().co2_status === 'ACTIVE'">AQI={{ getData().co2_aqi?.AQI }}</div>-->
<!--            <div-->
<!--              class="w-1/2 text-[10px] text-center rounded-lg px-[2px] bg-gray-200"-->
<!--            >-->
<!--              {{ getData().co2_aqi?.sl }}-->
<!--            </div>-->
<!--          </div>-->
<!--        </div>-->
<!--        <div v-if="getData().co2_status === 'ACTIVE'" class="bg-green3 h-12 rounded-md mt-2"></div>-->
<!--      </div>-->
    </div>
    <div class="text-xs text-gray-500 text-center mt-3">
        {{ $t("Last Update") }} {{ formatDate(getData().created_at) }}
    </div>
  </div>
  <div v-else class="text-center text-gray-500">No data available</div>
</template>
<script setup>
import { useI18n } from "vue-i18n";
import { formatDate } from "@/Utils/LastUpdate";
import { computed } from "vue";

const { t } = useI18n();

const props = defineProps({
  data: Object,
  defaultData: Object,
  showHeaders: {
    type: Boolean,
    default: false, // Optional prop with default value false
  },
  complexLayout: {
    type: Boolean,
    default: true, // Optional prop with default value false
  }
});

const isDataAvailable = computed(
  () => props.data  && Object.keys(props.data).length > 0
);
const isDefaultDataAvailable = computed(
  () => props.defaultData !== null
);
function getData(){
  if(Object.keys(props.data).length <= 0){
      return props.defaultData;
  }
    return props.data;
}
</script>
