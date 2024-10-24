<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import EquipmentDropdown from "@/Components/Custom/Dropdowns/EquipmentDropdown.vue";
import MeasuredByDropdown from "@/Components/Custom/Dropdowns/MeasuredByDropdown.vue";
import ProvinceDropdown from "@/Components/Custom/Dropdowns/ProvinceDropdown.vue";
import ParameterModel from "@/Components/Custom/Models/ParameterModel.vue";
import SuccessAlert from "@/Components/SuccessAlert.vue";
import ErrorAlert from "@/Components/ErrorAlert.vue";
import axios from "axios";
import { debounce } from "lodash";
import { reset } from "@/Utils/Alert";

import {
    computed,
    onMounted,
    onUnmounted,
    ref,
    watch,
    nextTick,
    createApp,
} from "vue";
let selectedRows = [];
const props = defineProps({
  equipmentTypes: Object,
  sensors: Object,
  province: Object,
  Parameter: Array,
});
const showParameterModal = ref(false);
let numberOfPages = 0; // To store the number of pages
const sensorData = ref({ data: [], total_count: 0, active_count: 0 });
const selectedParameters = ref([]);
const selectedEquipmentType = ref("");
const typeFilteredSensors = ref(props.sensors);
const SelectFilteredSensors = ref("");
const typeFilteredProvince = ref("");
const selectProvince = ref("");
const { successMessage, errorMessage, resetMessages } = reset();


var reportName = '';
var   dateRange = '';
var  description = '';


async function handleEquipmentTypeChange(newType) {
  selectedEquipmentType.value = newType.id;
  SelectFilteredSensors.value = "";
  typeFilteredProvince.value = "";
  if(selectedEquipmentType.value != ''){
  await axios
    .get(`/sensors/${newType.id}`)
    .then((response) => {
      typeFilteredSensors.value = response.data;
    })
    .catch((error) => {
      console.error("Error fetching sensors:", error);
    });
  }
}

async function handleTypeChanged(newType) {
  SelectFilteredSensors.value = newType.id;
 if(SelectFilteredSensors.value != '' && selectedEquipmentType.value != ''){
  await axios
    .get(`/admin/location/${selectedEquipmentType.value}/${newType.id}`)
    .then((response) => {
      typeFilteredProvince.value = response.data;
    })
    .catch((error) => {
      console.error("Error fetching sensors:", error);
    });
  }
}

function handlerprovinceChanged(newType) {
  selectProvince.value = newType.name;

}

const fetchSensorData = async () => {
  try {
    const response = await axios.get('/admin/sensor-data');
    sensorData.value = response.data;
  } catch (error) {
    console.error('Error fetching sensor data:', error);
  }
};

onMounted(() => {
  fetchSensorData();
});

const showAddDataModel = ref(false);

/// Section for Add new data dropdown
const editUserDropDown = ref(false);

const isOpen = ref(false);

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

// Optionally, close the dropdown when clicking outside
const closeDropdown = (event) => {
  if (!event.target.closest("#options-menu")) {
    isOpen.value = false;
  }
};

// Add event listener to close dropdown when clicking outside
onMounted(() => {
  document.addEventListener("click", closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener("click", closeDropdown);
});

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

onMounted(() => {
    loadScript("https://code.jquery.com/jquery-3.6.0.min.js")
        .then(() =>
            loadScript(
                "https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"
            )
        )
        .then(() => initializeDataTable());
});

const dataTable = ref(null);

const initializeDataTable = () => {
    dataTable.value = $("#equ-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "/admin/report-details",
            data: function (d) {
              console.log(d);
                d.page = currentPage.value;
                d.selectedEquipmentType = selectedEquipmentType.value;
                d.selectsensors = SelectFilteredSensors.value;
                d.typeFilteredProvince = selectProvince.value;
                 },
            dataSrc: function(json) {
                // Store the number of pages from the response
               // console.log(json);

                numberOfPages = json.numberOfPages;
                updatePaginationUI(numberOfPages);
                return json.data; // Standard data return for DataTables
            }
        },
        columns: [
       { data: null,
        className: "select-checkbox",
        orderable: false,
        render: function (data, type, full, meta) {
          // Render the checkbox in the first column
       return `<input type="checkbox" class="equ-checkbox" value="${full.id}" />`;
        },
      },
            { data: "name", name: "name" },
            { data: "type_name", name: "type_name"},
            { data: "location_name", name: "location_name" },
            { data: "id", name: "id", visible: false },  // Hidden
    { data: "location_id", name: "location_id", visible: false },  // Hidden
    { data: "equipment_type_id", name: "equipment_type_id", visible: false },  // Hidden
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
        createdRow: function (row, data, dataIndex) {
            $("td", row).addClass("border-b border-gray-200 custpadding");
        },
        drawCallback: function (settings) {
    $('#equ-table tbody').off('change', '.equ-checkbox');
    $('#equ-table tbody').on('change', '.equ-checkbox', function () {
        const rowData = dataTable.value.row($(this).closest('tr')).data();
        const rowObject = {
            id: rowData.id,
            location_id: rowData.location_id,
            equipment_type_id: rowData.equipment_type_id
        };

        if ($(this).is(':checked')) {
            selectedRows.push(rowObject);
            console.log(selectedRows);

        } else {
            selectedRows = selectedRows.filter(row => row.id !== rowData.id);
            console.log(selectedRows);
        }
        console.log('Selected Rows:', selectedRows);
    });
},
    });
};



    // Function to update pagination UI (example)
    function updatePaginationUI(pages) {
      $('#pagination-info').text(pages);
    }
const handleSearch = debounce(() => {
  currentPage.value = 1;
    reloadTable();
}, 300);


const reloadTable = () => {
    if (dataTable.value) {
        dataTable.value.ajax.reload();
    }
};

const currentPage = ref(1);
const totalPages = ref(1);

const nextPage = () => {
    if (currentPage.value < numberOfPages) {
      currentPage.value += 1;
      reloadTable();
    }
        };

const previousPage = () => {
     if (currentPage.value > 1) {
            currentPage.value -= 1;
              reloadTable();
            }
        };
        watch(selectedEquipmentType, () => {
                  reloadTable();
                });
        watch(SelectFilteredSensors, () => {
                  reloadTable();
                });
        watch(selectProvince, () => {
                  reloadTable();
                });
const handleApplyFilters = (filters) => {
  console.log(filters.Parameter);
  selectedParameters.value = filters.Parameter;
  console.log(selectedParameters);

};

const form = ref({
  reportName: '',
  dateRange: '',
  description: ''
});
const submitForm = () => {
  saveReport(form.value);
};
function saveReport(data) {
      if (!data.reportName || !data.dateRange || !data.description) {
        alert('Please fill in all required fields.');
        return;
      }
      const selectedData = selectedRows;
      const sldParameters = selectedParameters.value;

      if (selectedData.length === 0) {
        alert('Please select at least one row from the table.');
        return;
      }

      // Prepare the form data for submission
      const formData = {
        reportName: data.reportName,
        dateRange: data.dateRange,
        description: data.description,
        selectedData: selectedData,
        sldParameters:sldParameters
      };

      console.log('Form Data:', formData);

      axios.post('/admin/save-report', formData)
        .then(response => {
          console.log(response);
          successMessage.value= 'Report generated successfully.'
          resetMessages()

        })
        .catch(error => {
          console.error('Error generating report:', error);
          errorMessage.value='Error generating report'
          console.log(errorMessage);
          resetMessages()

        });
    };

</script>

<template>
  <AdminLayout>
    <ParameterModel :show="showParameterModal" @close="showParameterModal = false" :availableParameter="Parameter"
    @applyFilters="handleApplyFilters" :initialSelectedParameter="selectedParameters" />
    <div class="p-4">
       <div v-if=successMessage class="fixed top-4 right-4">
          <SuccessAlert :value="successMessage" />
        </div>
        <div v-if=errorMessage class="fixed top-4 right-4">
          <ErrorAlert  :value="errorMessage" />
        </div>
      <h1 class="text-2xl font-bold">Create New Report</h1>
      <h3 class="font-bold">Step 1 - Report Details</h3>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        <form @submit.prevent="submitForm">
  <div class="mb-4">
    <label for="reportname" class="block text-sm font-medium text-gray-700">Report Name</label>
    <input v-model="form.reportName" type="text" id="reportname" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter...">
  </div>
  <div class="mb-4">
    <label for="SelectDateRange" class="block text-sm font-medium text-gray-700">Select Date Range</label>
    <input v-model="form.dateRange" type="date" id="Date" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter...">
  </div>
  <div class="mb-4">
    <label for="description" class="block text-sm font-medium text-gray-700">Report Description</label>
    <input v-model="form.description" type="text" id="description" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter...">
  </div>
</form>
      </div>

      <br>
      <h3 class="font-bold">Step 2 - Equipment type details</h3>
      <div class="flex space-x-4 mt-7">
        <EquipmentDropdown
          side="left"
          :equipment-types="equipmentTypes"
          @type-changed="handleEquipmentTypeChange"
        />
        <MeasuredByDropdown :sensors="typeFilteredSensors"
        @sensorChanged="handleTypeChanged"  />
        <ProvinceDropdown :province="typeFilteredProvince" @provinceChanged="handlerprovinceChanged" color="text-green-700" icon="/images/greenpinicon.png" />

          <div class="ml-4 flex items-center">
                                    <button @click="showParameterModal= true"
                                        class="flex items-center px-6 py-2 border border-gray-300 rounded-full text-green-700 font-semibold">
                                        <img src="/images/filterlinesicon.svg" alt="Filter Icon" class="w-4 h-4 mr-2" />
                                        Parameters
                                    </button>
         </div>
         <!-- <div class="ml-4 flex items-center">
                                    <button v-for="role in selectedParameters" :key="role"
                                        class="flex items-center px-4 py-2 bg-green-50 rounded-full text-green-700 font-semibold mr-2">
                                        <span>{{ role }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" @click.stop="removeRole(role)">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div> -->

      </div>

      <div class="container mx-auto my-4">
    <table class="min-w-full bg-white ctable" id="equ-table">
      <thead>
        <tr class="w-full text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
          <th class="py-3 px-4"></th>
          <th class="py-3 px-4">Equipment Name</th>
          <th class="py-3 px-4">Type</th>
          <th class="py-3 px-4">Location</th>

        </tr>
      </thead>
    </table>
  </div>
      </div>

               <!-- Pagination -->
               <div class="flex items-center justify-between w-full mt-4">
                            <button @click="previousPage"
                                class="px-4 py-1 rounded-lg border border-gray-300">
                                Previous
                            </button>

                            <span class="text-sm">
                                <span class="text-sm font-medium">
                                    <span class="text-green1">Page {{ currentPage }}</span> of
                                    <span id="pagination-info"></span>
                                </span>
                            </span>

                            <button @click="nextPage"
                                class="px-3 py-1 rounded-lg border border-gray-300">
                                Next
                            </button>
                        </div>

          <div class="bg-gray-50 px-4 py-6 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="submitForm" type="button" class="w-full inline-flex justify-center rounded-3xl border border-transparent shadow-sm px-6 py-1 bg-green1 text-base font-medium text-white hover:bg-green1 sm:ml-3 sm:w-auto sm:text-sm">
  Generate Report
  </button>
            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-3xl border border-gray-300 shadow-sm px-6 py-1 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="$emit('close')">
              Cancel
            </button>
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

