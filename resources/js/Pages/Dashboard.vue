<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import Notification from "@/Components/Custom/Notification.vue";
import { onMounted, ref } from "vue";

const totalSensors = ref(0);
const totalEquipmentTypes = ref(0);
const activeSensors = ref(0);
const inactiveSensors = ref(0);
const activeUsers = ref(0);
const newUsersThisMonth = ref(0);

onMounted(async () => {
    try {
        const response = await axios.get('/admin/dashboard-data');
        totalSensors.value = response.data.totalSensors;
        totalEquipmentTypes.value = response.data.totalEquipmentTypes;
        activeSensors.value = response.data.activeSensors;
        inactiveSensors.value = response.data.inactiveSensors;
        activeUsers.value = response.data.activeUsers;
        newUsersThisMonth.value = response.data.newUsersThisMonth;
    } catch (error) {
        console.error('Failed to fetch equipment data:', error);
    }
});
</script>

<template>
  <AdminLayout>
    <div class="p-4">
      <h1 class="text-2xl font-bold">Dashboard</h1>

      <div class="grid grid-cols-1 lg:grid-cols-24 gap-5 first_div mt-4">
        <div class="col-span-1 lg:col-span-19">
          <div class="grid grid-cols-1 md:grid-cols-8 gap-4 mb-4">
            <div
              class="bg-white border border-gray-200 rounded-lg p-4 md:col-span-2"
            >
              <h3 class="font-bold text-lg mb-2">Live AQI Index</h3>
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div>
                    <span class="text-xs text-gray-600">SL AQI</span>
                    <div class="text-4xl font-bold">38</div>
                  </div>
                </div>
                <div class="w-px h-8 bg-gray-300 mx-4"></div>
                <div class="flex flex-col items-center">
                  <span class="text-sm text-gray-600">Colombo</span>
                  <img
                    src="/images/greenface.png"
                    class="h-10"
                    alt="Green face"
                  />
                </div>
              </div>
              <div class="mt-2 text-center">
                <div
                  class="text-md font-semibold text-green-400 border border-green-400 rounded-full py-1 my-4 cursor-pointer"
                >
                  Good
                </div>
                <div class="text-xs text-gray-500">
                  Last Update: 01 July 2024, 08.25 AM
                </div>
              </div>
            </div>

            <div
              class="bg-white border border-gray-200 rounded-lg p-4 md:col-span-3"
            >
              <h3 class="font-bold text-lg mb-2">Air Pollutant Parameters</h3>
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div>
                    <span class="text-xs text-gray-600">SL AQI</span>
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
                Last Update: 01 July 2024, 08.25 AM
              </div>
            </div>
            <div
              class="bg-white border border-gray-200 rounded-lg p-4 md:col-span-3"
            >
              <h3 class="font-bold text-lg mb-2">Weather Facts</h3>
              <div class="flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex items-start space-x-1">
                    <p class="text-4xl font-bold">27</p>
                    <div class="flex flex-col">
                      <p class="text-sm">°C</p>
                      <p class="text-xs">Sunny</p>
                    </div>
                  </div>
                </div>
                <div class="flex flex-col items-center">
                  <img
                    src="/images/sunny.png"
                    class="h-10"
                    alt="Sunny weather"
                  />
                </div>
              </div>
              <div class="w-full flex space-x-2 mt-5">
                <div
                  class="flex-1 bg-gray-100 border border-gray-300 p-2 rounded-lg flex flex-col items-center justify-center"
                >
                  <img src="/images/drop.png" class="h-4" alt="Sunny weather" />
                  <p class="text-xs">Humidity</p>
                  <p class="text-sm font-bold">0%</p>
                </div>
                <div
                  class="flex-1 bg-gray-100 border border-gray-300 rounded-lg p-2 flex flex-col items-center justify-center"
                >
                  <img src="/images/wind.png" class="h-4" alt="Sunny weather" />
                  <p class="text-xs">Wind</p>
                  <p class="text-sm font-bold">0%</p>
                </div>
                <div
                  class="flex-1 bg-gray-100 border border-gray-300 rounded-lg p-2 flex flex-col items-center justify-center"
                >
                  <img
                    src="/images/pressure.png"
                    class="h-4"
                    alt="Sunny weather"
                  />
                  <p class="text-xs">Pressure</p>
                  <p class="text-sm font-bold">0%</p>
                </div>
              </div>
              <div class="text-xs text-gray-500 mt-3 text-center">
                Last Update: 01 July 2024, 08.25 AM
              </div>
            </div>
          </div>
          <div class="mt-8">
            <h2 class="text-lg font-bold">Equipment Management</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2 mt-4">
              <div
                class="col-span-1 bg-green3 text-white rounded-lg shadow p-4"
              >
                <div class="flex items-start">
                  <!-- Icon (assuming it's an SVG or an image) -->
                  <div class="">
                    <img
                      src="/images/totalequipementicon.svg"
                      class="w-auto mt-2"
                    />
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-semibold">Total Equipments</div>
                    <div class="text-xs mt-2">
                      Last Update: 01 July 2024, 08:25 AM
                    </div>
                    <div class="mt-3 text-3xl font-bold">{{totalSensors}}</div>
                  </div>
                </div>
              </div>

              <div
                class="col-span-1 bg-green1 text-white rounded-lg shadow p-4"
              >
                <div class="flex items-start">
                  <!-- Icon (assuming it's an SVG or an image) -->
                  <div class="">
                    <img
                      src="/images/totaleqtypesicon.svg"
                      class="w-auto mt-2"
                    />
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-semibold">
                      Total Equipment Types
                    </div>
                    <div class="text-xs mt-2">
                      Last Update: 01 July 2024, 08:25 AM
                    </div>
                    <div class="mt-3 text-3xl font-bold">{{totalEquipmentTypes}}</div>
                  </div>
                </div>
              </div>

              <div
                class="col-span-1 bg-yellow1 text-green-700 rounded-lg shadow p-4"
              >
                <div class="flex items-start">
                  <!-- Icon (assuming it's an SVG or an image) -->
                  <div class="">
                    <img
                      src="/images/totalacequipmenticon.svg"
                      class="w-auto mt-2"
                    />
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-semibold">
                      Total Active Equipments
                    </div>
                    <div class="text-xs mt-2">
                      Last Update: 01 July 2024, 08:25 AM
                    </div>
                    <div class="mt-3 text-3xl font-bold">{{activeSensors}}</div>
                  </div>
                </div>
              </div>

              <div
                class="col-span-1 bg-gray-100 text-green-700 rounded-lg shadow p-4"
              >
                <div class="flex items-start">
                  <!-- Icon (assuming it's an SVG or an image) -->
                  <div class="">
                    <img
                      src="/images/totalinaceqicon.svg"
                      class="w-auto mt-2"
                    />
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-semibold">
                      Total Inactive Equipments
                    </div>
                    <div class="text-xs mt-2">
                      Last Update: 01 July 2024, 08:25 AM
                    </div>
                    <div class="mt-3 text-3xl font-bold">{{ inactiveSensors }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 first_div mt-4">
            <div
              class="col-span-1 bg-white border border-gray-200 rounded-lg p-4"
            >
              <h2 class="text-lg font-bold">Recent Activity Log</h2>

              <div class="timeline-container relative mt-3 pt-2 pb-1">
                <div class="flex items-start space-x-3 relative mt-3 mb-5">
                  <div
                    class="flex-shrink-0 w-6 h-6 mt-1 rounded-full border border-gray-200 flex items-center justify-center bg-white z-10"
                  >
                    <div class="bg-green3 w-4 h-4 rounded-full"></div>
                  </div>
                  <div>
                    <p class="text-md font-medium text-gray-900">
                      Data updated by Admin1 on 2024-06-26
                    </p>
                    <p
                      class="text-sm text-gray-500 px-3 py-1 rounded-full bg-gray-100 w-fit"
                    >
                      Data Update
                    </p>
                  </div>
                </div>
                <div class="flex items-start space-x-3 relative mt-3 mb-5">
                  <div
                    class="flex-shrink-0 w-6 h-6 mt-1 rounded-full border border-gray-200 flex items-center justify-center bg-white z-10"
                  >
                    <div class="bg-green3 w-4 h-4 rounded-full"></div>
                  </div>
                  <div>
                    <p class="text-md font-medium text-gray-900">
                      Data updated by Admin1 on 2024-06-26
                    </p>
                    <p
                      class="text-sm text-gray-500 px-3 py-1 rounded-full bg-gray-100 w-fit"
                    >
                      Data Update
                    </p>
                  </div>
                </div>
                <div class="flex items-start space-x-3 relative mt-3 mb-5">
                  <div
                    class="flex-shrink-0 w-6 h-6 mt-1 rounded-full border border-gray-200 flex items-center justify-center bg-white z-10"
                  >
                    <div class="bg-green3 w-4 h-4 rounded-full"></div>
                  </div>
                  <div>
                    <p class="text-md font-medium text-gray-900">
                      Data updated by Admin1 on 2024-06-26
                    </p>
                    <p
                      class="text-sm text-gray-500 px-3 py-1 rounded-full bg-gray-100 w-fit"
                    >
                      Data Update
                    </p>
                  </div>
                </div>

                <!-- Repeat for other items -->
              </div>
            </div>
            <div class="col-span-1">
              <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3">
                <h2 class="text-lg font-bold mb-2">User Management Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div
                    class="bg-gray-100 p-4 rounded-md text-center border border-gray-300"
                  >
                    <p class="text-gray-500">Active Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{activeUsers}}</p>
                  </div>
                  <div
                    class="bg-gray-100 p-4 rounded-md text-center border border-gray-300"
                  >
                    <p class="text-gray-500">New Users This Month</p>
                    <p class="text-2xl font-semibold text-gray-900">{{newUsersThisMonth}}</p>
                  </div>
                </div>
              </div>
              <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                  <h2 class="text-lg font-semibold text-gray-800">
                    Recent Reports Overview
                  </h2>
                  <a href="#" class="text-sm text-green-500 hover:underline"
                    >View All</a
                  >
                </div>
                <div class="space-y-3">
                  <div
                    class="flex justify-between items-center p-2 border border-gray-300 bg-gray-100 rounded-lg"
                  >
                    <div>
                      <h3 class="text-sm font-medium text-gray-700">
                        Daily Summary
                      </h3>
                      <p class="text-xs text-gray-500">2024-06-25</p>
                    </div>
                    <a href="#" class="text-xs text-green-500 hover:underline"
                      >View Report</a
                    >
                  </div>
                  <div
                    class="flex justify-between items-center p-2 border border-gray-300 bg-gray-100 rounded-lg"
                  >
                    <div>
                      <h3 class="text-xs font-medium text-gray-700">
                        Monthly Summary
                      </h3>
                      <p class="text-xs text-gray-500">2024-06-25</p>
                    </div>
                    <a href="#" class="text-xs text-green-500 hover:underline"
                      >View Report</a
                    >
                  </div>
                  <div
                    class="flex justify-between items-center p-2 border border-gray-300 bg-gray-100 rounded-lg"
                  >
                    <div>
                      <h3 class="text-sm font-medium text-gray-700">
                        Weekly Report
                      </h3>
                      <p class="text-xs text-gray-500">2024-06-25</p>
                    </div>
                    <a href="#" class="text-xs text-green-500 hover:underline"
                      >View Report</a
                    >
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-span-1 lg:col-span-5">
          <div
            class="border border-gray-200 rounded-lg p-4 bg-white mb-4 text-center"
          >
            <p class="text-md">System Health Status</p>
            <p class="text-2xl mt-3 text-green-600 font-bold">Operational</p>
          </div>
          <div class="border border-gray-200 rounded-lg p-4 bg-white">
            <h3 class="text-lg font-bold mb-4">Alerts & Notifications</h3>
              <div style="height: 700px;overflow-y: auto;">
                  <Notification/>
              </div>
            <div class="px-2 flex text-green-600 font-bold cursor-pointer mt-5">
              <p class="mr-2">View All</p>
              <img src="/images/rightgreenarrow.png" class="h-6" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style>
.timeline-container::before {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  left: 11px; /* Adjust this value to align with your dots */
  width: 2px;
  background-color: #e5e7eb; /* Light gray color, adjust as needed */
}
</style>
