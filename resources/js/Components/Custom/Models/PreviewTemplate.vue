<template>
  <Transition name="modal">
    <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 max-w-[90%] sm:align-middle">
          <div class="relative">
            <button @click="$emit('close')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-500">
              <span class="sr-only">Close</span>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <div class="max-w-6xl mx-auto bg-white p-8 rounded-lg shadow-lg">
            <!-- Report Name -->
            <h1 class="text-2xl font-semibold text-gray-800">Report</h1>

            <!-- Equipment Details -->
            <div class="mt-8">
              <h2 class="text-lg font-semibold text-gray-700">Equipment Details</h2>
              <div class="grid grid-cols-2 gap-4 mt-4">
                <!-- Equipment Types -->
                <div>
                  <h3 class="text-gray-700 font-semibold">Equipment Types</h3>
                  <div class="bg-gray-100 p-4 rounded-lg">
                    <p v-for="(report, index) in reports" :key="index">{{ report.e_name }}</p>
                  </div>
                </div>

                <!-- Equipment -->
                <div>
                  <h3 class="text-gray-700 font-semibold">Equipment's</h3>
                  <div class="bg-gray-100 p-4 rounded-lg">
                    <p v-for="(report, index) in reports" :key="index">{{ report.s_name }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Location & Parameter Details -->
            <div class="mt-8">
              <h2 class="text-lg font-semibold text-gray-700">Location & Parameter Details</h2>

              <!-- Table -->
              <div class="overflow-x-auto mt-4">
                <table class="min-w-full table-auto">
                  <thead class="bg-gray-100">
                    <tr>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-600">Location</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-600">PM2.5</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-600">PM10</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-600">O3</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-600">CO</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-600">NO2</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-600">SO2</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-600">VOC</th>
                      <th class="py-3 px-6 text-left text-sm font-semibold text-gray-600">CO2</th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="(sensorData, index) in paginatedData" :key="index">
                      <td class="py-4 px-6 text-sm text-gray-700">{{ sensorData.location }}</td>
                      <td class="py-4 px-6 text-sm text-gray-700">{{ sensorData.pm2_5 }} µg/m³</td>
                      <td class="py-4 px-6 text-sm text-gray-700">{{ sensorData.pm10 }} µg/m³</td>
                      <td class="py-4 px-6 text-sm text-gray-700">{{ sensorData.o3 }} ppb</td>
                      <td class="py-4 px-6 text-sm text-gray-700">{{ sensorData.co }} ppm</td>
                      <td class="py-4 px-6 text-sm text-gray-700">{{ sensorData.no2 }} ppb</td>
                      <td class="py-4 px-6 text-sm text-gray-700">{{ sensorData.so2 }} ppb</td>
                      <td class="py-4 px-6 text-sm text-gray-700">{{ sensorData.voc }} ppm</td>
                      <td class="py-4 px-6 text-sm text-gray-700">{{ sensorData.co2 }} ppm</td>
                    </tr>
                  </tbody>
                </table>
                <!-- Pagination -->
                <div class="flex items-center justify-between mt-4">
                  <button
                    class="py-2 px-4 bg-gray-200 text-gray-600 rounded-lg"
                    @click="prevPage"
                    :disabled="currentPage === 1">
                    Previous
                  </button>
                  <p class="text-gray-600">Page {{ currentPage }} of {{ totalPages }}</p>
                  <button
                    class="py-2 px-4 bg-gray-200 text-gray-600 rounded-lg"
                    @click="nextPage"
                    :disabled="currentPage === totalPages">
                    Next
                  </button>
                </div>
              </div>
            </div>

          <!--  <div class="bg-gray-50 px-4 py-3 mt-8 sm:px-6 sm:flex sm:flex-row-reverse">
               <button @click="submitForm" type="button" class="w-full inline-flex justify-center py-3 px-6 rounded-3xl border border-transparent shadow-sm bg-green1 text-base font-medium text-white hover:bg-green1 sm:ml-3 sm:w-auto sm:text-sm">
                Generate Report
              </button>
             <button type="button" class="mt-3 w-full inline-flex justify-center rounded-3xl border border-gray-300 shadow-sm py-3 px-6 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="$emit('close')">
                Delete Template
              </button>
            </div> -->
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>


<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { defineProps, defineEmits } from 'vue';
import axios from 'axios';

const props = defineProps({
  show: Boolean,
  rId: Number
});

const reports = ref([]);
const sensorDataList = ref([]);

const emit = defineEmits(['close']);

// Pagination variables
const currentPage = ref(1);
const itemsPerPage = ref(5); // Adjust this to the number of items you want per page

// Computed property for paginated data
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return sensorDataList.value.slice(start, end);
});

// Compute total pages
const totalPages = computed(() => {
  return Math.ceil(sensorDataList.value.length / itemsPerPage.value);
});

// Pagination methods
const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

// Fetch preview data
const getPreview = async () => {
  try {
    const response = await axios.get(`/admin/report-preview/${props.rId}`);
    reports.value = response.data.reports;
    sensorDataList.value = response.data.data;
  } catch (error) {
    console.error(error);
  }
};

// Watch for changes in rId and fetch data accordingly
watch(() => props.rId, () => {
  getPreview();
});

// Fetch data on mount
onMounted(() => {
  if (props.rId) {
    getPreview();
  }
});
</script>


  <style scoped>
  .modal-enter-active,
  .modal-leave-active {
    transition: opacity 0.3s ease;
  }
  .modal-enter-from,
  .modal-leave-to {
    opacity: 0;
  }

  .scrollable-table-container {
      max-height: 300px;
      overflow-y: auto;
    }
  </style>

