<template>
  <div class="mt-8 historical_data px-4 lg:px-0">
    <h1 class="text-2xl md:text-[2.8rem] font-bold">
      {{ $t("Historical Data") }}
    </h1>
    <p class="text-md md:mt-2">
      {{ $t("Track Air Quality Trends Over Time") }}
    </p>

    <div class="w-full mt-8">
      <div class="md:flex md:items-top md:space-x-5">
        <div class="md:flex md:items-top">
          <SearchableSensorsDropdown
            :initial-locations="initialLocations"
            @item-selected="locationSelected"
          />
          <!-- <ProvinceDropdown
            color="text-green-700"
            icon="/images/greenpinicon.png"
          /> -->
        </div>
        <div class="md:flex md:items-top">
          <!-- <TimeperiodDropdown /> -->
          <TimeRangePicker />
        </div>
      </div>
    </div>

    <div class="grid lg:grid-cols-2 grid-cols-1 gap-4 my-8">
      <div class="border p-5 bg-white rounded-lg max-md:overflow-y-scroll">
        <div class="lg:flex lg:items-center justify-between">
          <div class="flex justify-between">
            <h2 class="text-lg font-semibold mb-2 mt-2 lg:mt-0">
              {{ $t("Comparative AQI level") }}
            </h2>

            <img
              src="/images/dropicon.svg"
              class="h-10 w-auto block lg:hidden cursor-pointer"
              @click="showDropdown2 = !showDropdown2"
            />
          </div>

          <div class="lg:flex lg:space-x-2" :class="{ hidden: !showDropdown2 }">
            <!-- <div class="mb-2"><SelectParameterDropdown /></div> -->
            <div class="mb-2">
              <!-- <SimpleEquipmentDropdown /> -->
              <MeasuredByDropdown ui="chart" :sensors="locationSensors" />
            </div>
          </div>
        </div>

        <hr class="my-4" />

        <div class="w-full max-sm:overflow-y-scroll">
          <Chart1 @history-update-date="setUpdatedDate"/>
        </div>

        <p class="w-full text-center text-xs text-gray-500 mt-3">
          {{ $t("Last Update") }}: {{ historyUpdatedDateField1 }}
        </p>
      </div>
      <div class="border p-5 bg-white rounded-lg max-md:overflow-y-scroll">
        <div class="lg:flex items-center justify-between">
          <div class="flex justify-between">
            <h2 class="lg:text-lg font-semibold mb-2 mt-2 lg:mt-0">
              {{ $t("Comparative pollutant level") }}
            </h2>
            <img
              src="/images/dropicon.svg"
              class="h-10 w-auto block lg:hidden cursor-pointer"
              @click="showDropdown3 = !showDropdown3"
            />
          </div>
          <div class="lg:flex lg:space-x-2" :class="{ hidden: !showDropdown3 }">
            <div class="mb-2"><SelectParameterDropdown ui="historical" @selectionChanged="handleSelectionChange"/></div>
            <div class="mb-2"><MeasuredByDropdown ui="pm-chart" :sensors="locationSensors" /></div>
          </div>
        </div>

        <hr class="my-4" />

        <div class="w-full max-sm:overflow-y-scroll">
          <Chart2  @history-update-date-field2="setUpdatedDate2" />
        </div>

        <p class="w-full text-center text-xs text-gray-500 mt-3">
            {{ $t("Last Update") }}: {{ historyUpdatedDateField2 }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import ProvinceDropdown from "@/Components/Custom/Dropdowns/ProvinceDropdown.vue";
import TimeRangePicker from "@/Components/Custom/Widgets/TimeRangePicker.vue";
import SimpleEquipmentDropdown from "@/Components/Custom/Dropdowns/SimpleEquipmentDropdown.vue";
import SelectParameterDropdown from "@/Components/Custom/Dropdowns/SelectParameterDropdown.vue";
import Chart1 from "@/Components/Custom/Charts/Chart1.vue";
import Chart2 from "@/Components/Custom/Charts/Chart2.vue";
import SearchableSensorsDropdown from "@/Components/Custom/Dropdowns/SearchableSensorsDropdown.vue";
import MeasuredByDropdown from "@/Components/Custom/Dropdowns/MeasuredByDropdown.vue";
import { ref } from "vue";
import getSensorsByLocation from "@/Services/getSensorsByLocation";
import {formatDate} from "@/Utils/LastUpdate.js";

const props = defineProps({
  initialLocations: Array,
  sensors: Object,
    historyUpdatedDate: String,
    historicalData: Object
});

const locationSelected = async (item) => {
  const sensors = await getSensorsByLocation(item.id);
  locationSensors.value = sensors;
};

const showDropdown2 = ref(false);
const showDropdown3 = ref(false);
let historyUpdatedDateField1 = ref(props.historyUpdatedDate);
let historyUpdatedDateField2 = ref(props.historyUpdatedDate);
const locationSensors = ref(props.sensors);
function setUpdatedDate(date) {
    historyUpdatedDateField1.value = formatDate(date);
}
function setUpdatedDate2(date) {
    historyUpdatedDateField2.value = formatDate(date);
}
function handleSelectionChange(selection) {
  // console.log('Selected:', selection);
}
</script>
