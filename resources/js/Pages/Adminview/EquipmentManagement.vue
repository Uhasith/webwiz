<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import EquipmentDropdown from "@/Components/Custom/Dropdowns/EquipmentDropdown.vue";
import MeasuredByDropdown from "@/Components/Custom/Dropdowns/MeasuredByDropdown.vue";
import ProvinceDropdown from "@/Components/Custom/Dropdowns/ProvinceDropdown.vue";
import AdminPagination from "@/Components/Custom/AdminPagination.vue";
import axios from 'axios';
import { debounce } from "lodash";
import {formatDate } from "../../Utils/LastUpdate";
import AddSensorLocation from "@/Components/Custom/Models/AddSensorLocation.vue";

import {
    computed,
    onMounted,
    onUnmounted,
    ref,
    watch,
    nextTick,
    createApp,
} from "vue";

/// Tab section
const tabs = [{ name: "Equipments Details" }, { name: "Equipment Records" }];

const currentTab = ref("Equipments Details");

const equipments = ref([]);
const sensorLocations = ref([]);
const totalSensors = ref(0);
const totalEquipmentTypes = ref(0);
const activeSensors = ref(0);
const inactiveSensors = ref(0);
const showModal = ref(false);

onMounted(async () => {
    try {
        const response = await axios.get('/admin/equipments-data');
        equipments.value = response.data.sensors;
        sensorLocations.value = response.data.sensorLocations;
        totalSensors.value = response.data.totalSensors;
        totalEquipmentTypes.value = response.data.totalEquipmentTypes;
        activeSensors.value = response.data.activeSensors;
        inactiveSensors.value = response.data.inactiveSensors;
    } catch (error) {
        console.error('Failed to fetch equipment data:', error);
    }
});

const openModal = () => {
  showModal.value = true;
};

const dataTable1 = ref(null);
const searchEquQuery = ref("");
const dataTable2 = ref(null);
const searchRecQuery = ref("");

const loadScript = (src) => {
    return new Promise((resolve, reject) => {
        if (document.querySelector(`script[src="${src}"]`)) {
            resolve();
            return;
        }
        const script = document.createElement("script");
        script.src = src;
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
};

const initializeDataTable = () => {
    dataTable1.value = $("#equipment-table").DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: "/admin/equipments-details",
            data: function (d) {
                d.search = searchEquQuery.value;
                            },
        },
        columns: [
            { data: "name", name: "name" },
            { data: "type_name", name: "type_name"},
            { data: "organization_name", name: "organization_name" },
            // { data: null , render: function() { return "Desc 1"; }},
            {data: "status", name: "status" },
            { data: null, render: function() { return "Admin 2"; }},
            { data: null, orderable: false, searchable: false, render: function(data, type, row) {
                return `  <div class="relative inline-block text-left">
                      <div>
                        <button
                          type="button"
                          class="py-3 px-6"
                          id="options-menu"
                          aria-haspopup="true"
                          aria-expanded="true"
                        >
                          &#8942;
                        </button>
                      </div>
                    </div>`;
            }},
        ],
        language: {
            paginate: {
                previous: "Previous",
                next: "Next",
            },
            info: "Page _PAGE_ of _PAGES_",
        },
        dom: "rt",
        pagingType: "simple",
        pageLength: 10,
        lengthChange: false,
        searching: false,
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: true,
 drawCallback: function (settings) {
             var api = this.api();
             var pageInfo = api.page.info();
             $(".dataTables_info").html(
                 "Page " + (pageInfo.page + 1) + " of " + pageInfo.pages
             );
             updateCustomPagination(this.api());
        },
        createdRow: function (row, data, dataIndex) {
            $("td", row).addClass("border-b border-gray-200 custpadding");
        },
    });

};

//Record Table
const initializeDataTable1 = () => {
    dataTable2.value = $("#record-table").DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: "/admin/equipments-record",
            data: function (d) {
                d.search = searchRecQuery.value;
                            },
        },
        columns: [
            { data: "sensor_name", name: "sensor_name" },
            { data: "location_name", name: "location_name"},
            { data: "updated_at", name: "updated_at" },
            {data: "sensor_locations_status", name: "sensor_locations_status" },
            { data: null, orderable: false, searchable: false, render: function(data, type, row) {
                return `  <div class="relative inline-block text-left">
                      <div>
                        <button
                          type="button"
                          class="py-3 px-6"
                          id="options-menu"
                          aria-haspopup="true"
                          aria-expanded="true"
                        >
                          &#8942;
                        </button>
                      </div>
                    </div>`;
            }},
        ],
        language: {
            paginate: {
                previous: "Previous",
                next: "Next",
            },
            info: "Page _PAGE_ of _PAGES_",
        },
        dom: "rt",
        pagingType: "simple",
        pageLength: 10,
        lengthChange: false,
        searching: false,
        ordering: false,
        info: true,
        autoWidth: false,
        responsive: true,
 drawCallback: function (settings) {
             var api = this.api();
             var pageInfo = api.page.info();
             $(".dataTables_info").html(
                 "Page " + (pageInfo.page + 1) + " of " + pageInfo.pages
             );
             updateCustomPagination(this.api());
        },
        createdRow: function (row, data, dataIndex) {
            $("td", row).addClass("border-b border-gray-200 custpadding");
        },
    });

};

const reloadTable2 = () => {
    if (dataTable2.value) {
        dataTable2.value.ajax.reload();
    }
};
const handleSearch2 = debounce(() => {
    reloadTable2();
}, 300);

watch(searchRecQuery, () => {
    handleSearch2();
});

const reloadTable = () => {
    if (dataTable1.value) {
        dataTable1.value.ajax.reload();
    }
};

const handleSearch = debounce(() => {
    reloadTable();
}, 300);

watch(searchEquQuery, () => {
    handleSearch();
});

// Pagination
const updateCustomPagination = (api) => {
    const info = api.page.info();
    currentPage.value = info.page + 1;
    totalPages.value = info.pages;
};

const currentPage = ref(1);
const totalPages = ref(1);

const previousPage = () => {
    if (currentPage.value > 1) {
        dataTable1.value.page("previous").draw("page");
    }
};

const nextPage = () => {
    if (currentPage.value < totalPages.value) {
        dataTable1.value.page("next").draw("page");
    }
};

watch(currentTab, (newTab, oldTab) => {
    if (newTab === "Equipments Details") {
        nextTick(() => {
            initializeDataTable();
        });
    }
    if (newTab === "Equipment Records") {
        nextTick(() => {
            initializeDataTable1();
        });
    }
});

onMounted(() => {
    loadScript("https://code.jquery.com/jquery-3.6.0.min.js")
        .then(() =>
            loadScript(
                "https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"
            )
        )
        .then(() => initializeDataTable());
});

const exportEquipments = async () =>  {
    const params = {
        search: searchEquQuery.value,
    };

    axios.get('/admin/export-equipments', { params, responseType: 'blob' })
        .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'Equipments.xlsx');
            document.body.appendChild(link);
            link.click();
        })
        .catch(error => {
            console.error("Error exporting data:", error);
        });

};

const exportEquipmentReords = async () =>  {
    const params = {
        search: searchRecQuery.value,
    };

    axios.get('/admin/export-equipment-records', { params, responseType: 'blob' })
        .then(response => {
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', 'Equipment-records.xlsx');
            document.body.appendChild(link);
            link.click();
        })
        .catch(error => {
            console.error("Error exporting data:", error);
        });

};

</script>

<template>
  <AdminLayout>
    <div class="p-4">
      <h1 class="text-2xl font-bold">Equipment Management</h1>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        <div class="col-span-1 bg-green3 text-white rounded-lg shadow p-4">
          <div class="flex items-start">
            <!-- Icon (assuming it's an SVG or an image) -->
            <div class="">
              <img src="/images/totalequipementicon.svg" class="w-auto mt-2" />
            </div>
            <div class="ml-3">
              <div class="text-sm font-semibold">Total Equipments</div>
              <div class="text-xs mt-2">
                Last Update: 01 July 2024, 08:25 AM
              </div>
              <div class="mt-3 text-3xl font-bold">{{ totalSensors }}</div>
            </div>
          </div>
        </div>

        <div class="col-span-1 bg-green1 text-white rounded-lg shadow p-4">
          <div class="flex items-start">
            <!-- Icon (assuming it's an SVG or an image) -->
            <div class="">
              <img src="/images/totaleqtypesicon.svg" class="w-auto mt-2" />
            </div>
            <div class="ml-3">
              <div class="text-sm font-semibold">Total Equipment Types</div>
              <div class="text-xs mt-2">
                Last Update: 01 July 2024, 08:25 AM
              </div>
              <div class="mt-3 text-3xl font-bold">{{ totalEquipmentTypes }}</div>
            </div>
          </div>
        </div>

        <div class="col-span-1 bg-yellow1 text-green-700 rounded-lg shadow p-4">
          <div class="flex items-start">
            <!-- Icon (assuming it's an SVG or an image) -->
            <div class="">
              <img src="/images/totalacequipmenticon.svg" class="w-auto mt-2" />
            </div>
            <div class="ml-3">
              <div class="text-sm font-semibold">Total Active Equipments</div>
              <div class="text-xs mt-2">
                Last Update: 01 July 2024, 08:25 AM
              </div>
              <div class="mt-3 text-3xl font-bold">{{ activeSensors }}</div>
            </div>
          </div>
        </div>

        <div
          class="col-span-1 bg-gray-100 text-green-700 rounded-lg shadow p-4"
        >
          <div class="flex items-start">
            <!-- Icon (assuming it's an SVG or an image) -->
            <div class="">
              <img src="/images/totalinaceqicon.svg" class="w-auto mt-2" />
            </div>
            <div class="ml-3">
              <div class="text-sm font-semibold">Total Inactive Equipments</div>
              <div class="text-xs mt-2">
                Last Update: 01 July 2024, 08:25 AM
              </div>
              <div class="mt-3 text-3xl font-bold">{{ inactiveSensors }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="border-b border-gray-200 mt-5">
        <nav class="-mb-px flex">
          <a
            v-for="tab in tabs"
            :key="tab.name"
            @click="currentTab = tab.name"
            :class="[
              currentTab === tab.name
                ? 'border-green-500 text-green-600'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
              'whitespace-nowrap py-4 px-8 border-b-2 font-medium text-md cursor-pointer',
            ]"
          >
            {{ tab.name }}
          </a>
        </nav>
      </div>

      <!-- Tab content -->
      <div class="">
        <div v-if="currentTab === 'Equipments Details'">
          <div class="flex items-center justify-between bg-white mt-7">
            <div class="flex items-center">
              <div class="relative">
                <input
                  type="text" v-model="searchEquQuery"
                  placeholder="Search"
                  class="text-green-700 border border-gray-300 placeholder-gray-300 rounded-full pl-10 pr-4 py-2 w-60 focus:outline-none focus:ring-2 focus:ring-green-500"
                />
                <svg
                  class="absolute left-3 top-1/2 transform -translate-y-1/2 text-green-700 w-5 h-5"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-4.35-4.35m3.23-4.88a6.375 6.375 0 11-12.75 0 6.375 6.375 0 0112.75 0z"
                  />
                </svg>
              </div>
              <!-- <div class="ml-4 flex items-center">
                <button
                  @click="showFiltersModal = true"
                  class="flex items-center px-6 py-2 border border-gray-300 rounded-full text-green-700 font-semibold"
                >
                  <img
                    src="/images/filterlinesicon.svg"
                    alt="Filter Icon"
                    class="w-4 h-4 mr-2"
                  />
                  filters
                </button>
              </div> -->
              <!-- <div class="ml-4 flex items-center">
                <button
                  class="flex items-center px-4 py-2 bg-green-50 rounded-full text-green-700 font-semibold"
                >
                  <span>Status</span>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 ml-2"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div> -->
            </div>
            <div class="flex items-center">
              <button
                class="flex items-center px-6 py-2 border border-gray-300 rounded-full text-green-700 font-semibold"
                @click="exportEquipments"
              >
                <img
                  src="/images/exportdataicon.svg"
                  alt="Filter Icon"
                  class="w-4 h-4 mr-2"
                />
                Export Data
              </button>
            </div>
          </div>

          <div class="container mx-auto my-4">
            <table class="min-w-full bg-white" id="equipment-table">
              <thead>
                <tr
                  class="w-full text-left text-xs font-medium text-gray-400 uppercase tracking-wider"
                >
                  <th class="py-3 px-6">Equipment Name</th>
                  <th class="py-3 px-6">Type</th>
                  <th class="py-3 px-6">Manufacturer</th>
<!--                  <th class="py-3 px-6">Description</th>-->
                  <th class="py-3 px-6">Status</th>
                  <th class="py-3 px-6">Entered By</th>
                  <th class="py-3 px-6"></th>
                </tr>
              </thead>
              <!--<tbody>
                <tr
                 v-for="equipment in equipments" :key="equipment.id"
                  class="border-b border-t border-gray-200"
                >
                  <td class="py-3 px-6">{{ equipment.name }}</td>
                  <td class="py-3 px-6">{{ equipment.type_name }}</td>
                  <td class="py-3 px-6">{{ equipment.organization_name }}</td>
                  <td class="py-3 px-6">Desc 1</td>
                  <td
                    class="py-3 px-6"
                    :class="
                      equipment.status === 'ACTIVE'
                        ? 'text-green-500'
                        : 'text-red-500'
                    "
                  >
                  {{ equipment.status }}
                  </td>
                  <td class="py-3 px-6">Admin 2</td>

                  <td
                    class="py-3 px-6 text-right text-gray-500 font-bold cursor-pointer"
                  >
                    &#8942;
                  </td>
                </tr>
              </tbody>-->
            </table>
          </div>

          <!-- <AdminPagination -->
            <div class="flex items-center justify-between w-full mt-4">
                            <button @click="previousPage" :disabled="currentPage === 1"
                                class="px-4 py-1 rounded-lg border border-gray-300"
                                :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
                                Previous
                            </button>

                            <span class="text-sm">
                                <span class="text-sm font-medium">
                                    <span class="text-green1">Page {{ currentPage }}</span> of
                                    {{ totalPages }}
                                </span>
                            </span>

                            <button @click="nextPage" :disabled="currentPage === totalPages"
                                class="px-3 py-1 rounded-lg border border-gray-300" :class="{
            'opacity-50 cursor-not-allowed': currentPage === totalPages,
        }">
                                Next
                            </button>
                        </div>
        </div>
        <div v-else-if="currentTab === 'Equipment Records'">
          <div class="flex space-x-4 mt-7">
            <!-- <EquipmentDropdown side="left" />
            <MeasuredByDropdown />
            <ProvinceDropdown
              color="text-green-700"
              icon="/images/greenpinicon.png"
            /> -->
          </div>

          <div class="flex items-center justify-between bg-white">
            <div class="flex items-center">
              <div class="relative">
                <input v-model="searchRecQuery"
                  type="text"
                  placeholder="Search"
                  class="text-green-700 border border-gray-300 placeholder-gray-300 rounded-full pl-10 pr-4 py-2 w-60 focus:outline-none focus:ring-2 focus:ring-green-500"
                />
                <svg
                  class="absolute left-3 top-1/2 transform -translate-y-1/2 text-green-700 w-5 h-5"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-4.35-4.35m3.23-4.88a6.375 6.375 0 11-12.75 0 6.375 6.375 0 0112.75 0z"
                  />
                </svg>
              </div>
              <!-- <div class="ml-4 flex items-center">
                <button
                  @click="showFiltersModal = true"
                  class="flex items-center px-6 py-2 border border-gray-300 rounded-full text-green-700 font-semibold"
                >
                  <img
                    src="/images/filterlinesicon.svg"
                    alt="Filter Icon"
                    class="w-4 h-4 mr-2"
                  />
                  filters
                </button>
              </div> -->
              <!-- <div class="ml-4 flex items-center">
                <button
                  class="flex items-center px-4 py-2 bg-green-50 rounded-full text-green-700 font-semibold"
                >
                  <span>Status</span>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6 ml-2"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    />
                  </svg>
                </button>
              </div> -->
            </div>
            <div class="flex items-center gap-3">
              <button
                class="flex items-center px-6 py-2 border border-gray-300 rounded-full text-green-700 font-semibold"
                @click="exportEquipmentReords"
              >
                <img
                  src="/images/exportdataicon.svg"
                  alt="Filter Icon"
                  class="w-4 h-4 mr-2"
                />
                Export Data
              </button>
              <button
                @click="openModal"
                class="flex items-center px-6 py-2 rounded-full border-transparent shadow-sm bg-green1 text-base font-medium text-white hover:bg-green-600"
              >
                + Add New Location Data
              </button>
            </div>
          </div>

          <div class="container mx-auto my-4">
            <table class="min-w-full bg-white" id="record-table">
              <thead>
                <tr
                  class="w-full text-left text-xs font-medium text-gray-400 uppercase tracking-wider"
                >
                  <th class="py-3 px-6 text-left">Equipment Name</th>
                  <th class="py-3 px-6 text-left">Location</th>
                  <th class="py-3 px-6 text-center">Last Update</th>
                  <th class="py-3 px-6 text-left">Status</th>
                  <th class="py-3 px-6"></th>
                  <!-- Empty header for the three dots column -->
                </tr>
              </thead>
             <!--- <tbody>
                <tr class="border-b border-t border-gray-200" v-for="sensorLocation in sensorLocations" :key="sensorLocation.id">
                  <td class="py-3 px-6">{{ sensorLocation.sensor_name }}</td>
                  <td class="py-3 px-6">
                    {{ sensorLocation.location_name}}
                  </td>
                  <td class="py-3 px-6 text-center">
                    {{ formatDate(sensorLocation.updated_at) }}
                  </td>
                  <td class="py-3 px-6">
                    <span class="text-green-500">{{sensorLocation.sensor_locations_status}}</span>
                  </td>
                  <td class="text-right text-gray-500 font-bold cursor-pointer">
                    <div class="relative inline-block text-left">
                      <div>
                        <button
                          type="button"
                          class="py-3 px-6"
                          id="options-menu"
                          aria-haspopup="true"
                          aria-expanded="true"
                        >
                          &#8942;
                        </button>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>-->
            </table>
          </div>

                   <!-- <AdminPagination -->
                    <div class="flex items-center justify-between w-full mt-4">
                            <button @click="previousPage" :disabled="currentPage === 1"
                                class="px-4 py-1 rounded-lg border border-gray-300"
                                :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }">
                                Previous
                            </button>

                            <span class="text-sm">
                                <span class="text-sm font-medium">
                                    <span class="text-green1">Page {{ currentPage }}</span> of
                                    {{ totalPages }}
                                </span>
                            </span>

                            <button @click="nextPage" :disabled="currentPage === totalPages"
                                class="px-3 py-1 rounded-lg border border-gray-300" :class="{
            'opacity-50 cursor-not-allowed': currentPage === totalPages,
        }">
                                Next
                            </button>
                        </div>
        </div>
      </div>
    </div>

    <AddSensorLocation
    v-if="showModal"
    :show="showModal"
    @close="showModal = false"/>

  </AdminLayout>
</template>
<style scoped>
@import "https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css";

table.dataTable thead th,
table.dataTable thead td {
    border-bottom: none;
}

table.dataTable.no-footer {
    border-bottom: none;
}
</style>
<style>
.custpadding {
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
}
</style>
