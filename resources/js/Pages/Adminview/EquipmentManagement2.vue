<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import EquipmentDropdown from "@/Components/Custom/Dropdowns/EquipmentDropdown.vue";
import MeasuredByDropdown from "@/Components/Custom/Dropdowns/MeasuredByDropdown.vue";
import ProvinceDropdown from "@/Components/Custom/Dropdowns/ProvinceDropdown.vue";
import AdminPagination from "@/Components/Custom/AdminPagination.vue";
import EquipmentFiltersModel from "@/Components/Custom/Models/EquipmentFiltersModel.vue";
import { nextTick, onMounted, ref, watch } from "vue";
import { debounce } from "lodash";

const showEquipmentFiltersModel = ref(false);

/// Tab section
const tabs = [{ name: "Equipments Details" }, { name: "Equipment Records" }];

const currentTab = ref("Equipments Details");
/// Tab end

const props = defineProps({
  initialEquipments: Object,
  equipmentTypes: Object,
  sensors: Object,
});

const selectedEquipmentType = ref(props.equipmentTypes.data[0]);
const typeFilteredSensors = ref(props.sensors);

async function handleEquipmentTypeChange(newType) {
  selectedEquipmentType.value = newType;

  await axios
    .get(`/sensors/${newType.id}`)
    .then((response) => {
      typeFilteredSensors.value = response.data;
    })
    .catch((error) => {
      console.error("Error fetching sensors:", error);
    });
}

const equipments = ref(props.initialEquipments.data);

const updateEquipments = (newEquipments) => {
  equipments.value = newEquipments;
};

// Datatable

const dataTable = ref(null);
const searchQuery = ref("");

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
  dataTable.value = $("#equipments-table").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: route("equipment.data"),
      data: function (d) {
        d.search = searchQuery.value;
      },
    },
    columns: [
      {
        data: "name",
        name: "name",
        render: function (data, type, row) {
          return `<div class="flex items-center">
                <span>${data}</span>
              </div>`;
        },
      },
      { data: "equipmentType", name: "equipmentType" },
      {
        data: null,
        render: function (data, type, row) {
          return "Manufacturer 1";
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return "Institute 1";
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return "Lorem Ipsum";
        },
      },
      {
        data: "status",
        name: "status",
        render: function (data, type, row) {
          const statusColor =
            data === "active" ? "text-green-500" : "text-red-500";
          return `<span class="${statusColor}">${data}</span>`;
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return "Admin 1";
        },
      },
    ],
    language: {
      search: "_INPUT_",
      searchPlaceholder: "Search...",
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

      // Mount AdminUserMgmentDropdown components
      api.rows().every(function () {
        const rowData = this.data();
        const container = document.getElementById(
          `dropdown-container-${rowData.id}`
        );
        if (container) {
          const app = createApp(AdminUserMgmentDropdown);
          app.mount(container);
        }
      });
    },
    createdRow: function (row, data, dataIndex) {
      $("td", row).addClass("border-b border-gray-200 custpadding");
    },
    initComplete: function () {
      $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var min = $("#min").val();
        var max = $("#max").val();
        var age = parseFloat(data[3]) || 0;

        if (
          (isNaN(min) && isNaN(max)) ||
          (isNaN(min) && age <= max) ||
          (min <= age && isNaN(max)) ||
          (min <= age && age <= max)
        ) {
          return true;
        }
        return false;
      });

      $("#min, #max").keyup(function () {
        dataTable.value.draw();
      });
    },
  });

  window.editUser = function (userId) {
    console.log("Edit user with ID:", userId);
  };
};

const reloadTable = () => {
  if (dataTable.value) {
    dataTable.value.ajax.reload();
  }
};

onMounted(() => {
  loadScript("https://code.jquery.com/jquery-3.6.0.min.js")
    .then(() =>
      loadScript(
        "https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"
      )
    )
    .then(() => initializeDataTable());
});

const handleSearch = debounce(() => {
  reloadTable();
}, 300);

watch(searchQuery, () => {
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
    dataTable.value.page("previous").draw("page");
  }
};

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    dataTable.value.page("next").draw("page");
  }
};

watch(currentTab, (newTab, oldTab) => {
  if (newTab === "Equipments Details") {
    nextTick(() => {
      initializeDataTable();
    });
  }
});
</script>

<template>
  <AdminLayout>
    <EquipmentFiltersModel
      :show="showEquipmentFiltersModel"
      @close="showEquipmentFiltersModel = false"
      :availableEquipments="sensors"
      @applyFilters="handleApplyFilters"
      :initialSelectedEquipments="selectedRoles"
    />
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
              <div class="mt-3 text-3xl font-bold">125</div>
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
              <div class="mt-3 text-3xl font-bold">3</div>
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
              <div class="mt-3 text-3xl font-bold">10</div>
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
              <div class="mt-3 text-3xl font-bold">2</div>
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
                  v-model="searchQuery"
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
              <div class="ml-4 flex items-center">
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
              </div>
              <div class="ml-4 flex items-center">
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
              </div>
            </div>
            <div class="flex items-center">
              <button
                class="flex items-center px-6 py-2 border border-gray-300 rounded-full text-green-700 font-semibold"
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
            <table id="equipments-table" class="min-w-full">
              <thead>
                <tr
                  class="w-full text-left text-xs font-medium text-gray-400 uppercase tracking-wider"
                >
                  <th class="py-3 px-6">EQUIPMENT NAME</th>
                  <th class="py-3 px-6">TYPE</th>
                  <th class="py-3 px-6">MANUFACTURER</th>
                  <th class="py-3 px-6">INSTITUTE</th>
                  <th class="py-3 px-6">DESCRIPTION</th>
                  <th class="py-3 px-6">STATUS</th>
                  <th class="py-3 px-6">ENTERED BY</th>
                  <th class="py-3 px-6"></th>
                </tr>
              </thead>
            </table>
          </div>

          <div class="flex items-center justify-between w-full mt-4">
            <button
              @click="previousPage"
              :disabled="currentPage === 1"
              class="px-4 py-1 rounded-lg border border-gray-300"
              :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
            >
              Previous
            </button>

            <span class="text-sm">
              <span class="text-sm font-medium">
                <span class="text-green1">Page {{ currentPage }}</span> of
                {{ totalPages }}
              </span>
            </span>

            <button
              @click="nextPage"
              :disabled="currentPage === totalPages"
              class="px-3 py-1 rounded-lg border border-gray-300"
              :class="{
                'opacity-50 cursor-not-allowed': currentPage === totalPages,
              }"
            >
              Next
            </button>
          </div>
        </div>
        <div v-else-if="currentTab === 'Equipment Records'">
          <div class="flex space-x-4 mt-7">
            <EquipmentDropdown
              side="left"
              :equipment-types="equipmentTypes"
              @type-changed="handleEquipmentTypeChange"
            />
            <MeasuredByDropdown :sensors="typeFilteredSensors" />
            <ProvinceDropdown
              color="text-green-700"
              icon="/images/greenpinicon.png"
            />
          </div>

          <div class="flex items-center justify-between bg-white mt-7">
            <div class="flex items-center">
              <div class="relative">
                <input
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
              <div class="ml-4 flex items-center">
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
              </div>
              <div class="ml-4 flex items-center">
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
              </div>
            </div>
            <div class="flex items-center">
              <button
                class="flex items-center px-6 py-2 border border-gray-300 rounded-full text-green-700 font-semibold"
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
            <table class="min-w-full bg-white">
              <thead>
                <tr
                  class="w-full text-left text-xs font-medium text-gray-400 uppercase tracking-wider"
                >
                  <th class="py-3 px-6 text-left">Equipment Name</th>
                  <th class="py-3 px-6 text-left">Location</th>
                  <th class="py-3 px-6 text-center">Last Update</th>
                  <th class="py-3 px-6 text-left">Permission Status</th>
                  <th class="py-3 px-6"></th>
                  <!-- Empty header for the three dots column -->
                </tr>
              </thead>
              <tbody>
                <tr class="border-b border-t border-gray-200">
                  <td class="py-3 px-6">Equipment 1</td>
                  <td class="py-3 px-6">
                    Panipitiya<br /><span class="text-gray-500 text-sm"
                      >Colombia</span
                    >
                  </td>
                  <td class="py-3 px-6 text-center">
                    25 Oct 2024<br /><span class="text-gray-500 text-sm"
                      >08:56 AM</span
                    >
                  </td>
                  <td class="py-3 px-6">
                    <span class="text-green-500">Active</span>
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
                <tr class="border-b border-t border-gray-200">
                  <td class="py-3 px-6">Equipment 1</td>
                  <td class="py-3 px-6">
                    Battaramulla<br /><span class="text-gray-500 text-sm"
                      >Colombia</span
                    >
                  </td>
                  <td class="py-3 px-6 text-center">
                    25 Oct 2024<br /><span class="text-gray-500 text-sm"
                      >08:56 AM</span
                    >
                  </td>
                  <td class="py-3 px-6">
                    <span class="text-red-500">Inactive</span>
                  </td>
                  <td
                    class="py-3 px-6 text-right text-gray-500 font-bold cursor-pointer"
                  >
                    &#8942;
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="flex items-center justify-between w-full">
            <button class="px-4 py-1 rounded-lg border border-gray-300">
              Previous
            </button>

            <span class="text-lg text-sm">
              <span class="text-sm font-medium">
                <span class="text-green1">Page 1</span> of 10
              </span>
            </span>

            <button class="px-3 py-1 rounded-lg border border-gray-300">
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
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
