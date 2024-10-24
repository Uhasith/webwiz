<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import EquipmentDropdown from "@/Components/Custom/Dropdowns/EquipmentDropdown.vue";
import MeasuredByDropdown from "@/Components/Custom/Dropdowns/MeasuredByDropdown.vue";
import ProvinceDropdown from "@/Components/Custom/Dropdowns/ProvinceDropdown.vue";
import AdminDataMgmentDropdown from "@/Components/Custom/Dropdowns/AdminDataMgmentDropdown.vue";
import AddDataModel from "@/Components/Custom/Models/AddDataModel.vue";
import SuccessAlert from "@/Components/SuccessAlert.vue";
import ErrorAlert from "@/Components/ErrorAlert.vue";
import axios from "axios";
import { onMounted, onUnmounted, ref, watch, createApp } from "vue";
import { Button, buttonVariants } from '@/Components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/Components/ui/popover';
import { CalendarDate, isEqualMonth } from '@internationalized/date';
import { Calendar as CalendarIcon, ChevronLeft, ChevronRight, ChevronsRight, ChevronsLeft, X } from 'lucide-vue-next';
import { RangeCalendarRoot, useDateFormatter } from 'radix-vue';
import { createMonth, toDate } from 'radix-vue/date';
import { RangeCalendarCell, RangeCalendarCellTrigger, RangeCalendarGrid, RangeCalendarGridBody, RangeCalendarGridHead, RangeCalendarGridRow, RangeCalendarHeadCell } from '@/Components/ui/range-calendar';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { formatDate } from "../../Utils/LastUpdate";
import { cn } from '@/lib/utils';
import { debounce } from "lodash";
import { useGlobalStore } from '@/store/global';

const props = defineProps({
  equipmentTypes: Object,
  sensors: Object,
  province: Object,
});

const globalStore = useGlobalStore();

let numberOfPages = 0; // To store the number of pages
const sensorData = ref({ data: [], total_count: 0, active_count: 0 });
const selectedEquipmentType = ref("");
const typeFilteredSensors = ref(props.sensors);
const SelectFilteredSensors = ref("");
const typeFilteredProvince = ref("");
const selectProvince = ref("");
const successMessage = ref("");
const errorMessage = ref("");
const selectedStatus = ref("ALL");

// Get current date
const currentDate = new Date();
// Create the end date (current date)
const end_date = new CalendarDate(currentDate.getFullYear(), currentDate.getMonth() + 1, currentDate.getDate());
// Create the start date (3 weeks before the current date)
const start_date = end_date.subtract({ days: 21 });

const selectedDate = ref({
  start: start_date,
  end: end_date,
});

const startDate = ref(null);
const endDate = ref(null);

const locale = ref('en-US')
const formatter = useDateFormatter(locale.value)

const placeholder = ref(selectedDate.value.start);
const secondMonthPlaceholder = ref(selectedDate.value.end);

const firstMonth = ref(
  createMonth({
    dateObj: placeholder.value,
    locale: locale.value,
    fixedWeeks: true,
    weekStartsOn: 0,
  }),
);
const secondMonth = ref(
  createMonth({
    dateObj: secondMonthPlaceholder.value,
    locale: locale.value,
    fixedWeeks: true,
    weekStartsOn: 0,
  }),
);

function updateMonth(reference, months) {
  if (reference === 'first') {
    placeholder.value = placeholder.value.add({ months })
  }
  else {
    secondMonthPlaceholder.value = secondMonthPlaceholder.value.add({
      months,
    })
  }
}

function updateYear(reference, years) {
  if (reference === 'first') {
    placeholder.value = placeholder.value.add({ years })
  }
  else {
    secondMonthPlaceholder.value = secondMonthPlaceholder.value.add({
      years,
    })
  }
}

watch(placeholder, (_placeholder) => {
  firstMonth.value = createMonth({
    dateObj: _placeholder,
    weekStartsOn: 0,
    fixedWeeks: false,
    locale: locale.value,
  })
  if (isEqualMonth(secondMonthPlaceholder.value, _placeholder)) {
    secondMonthPlaceholder.value = secondMonthPlaceholder.value.add({
      months: 1,
    })
  }
});

watch(secondMonthPlaceholder, (_secondMonthPlaceholder) => {
  secondMonth.value = createMonth({
    dateObj: _secondMonthPlaceholder,
    weekStartsOn: 0,
    fixedWeeks: false,
    locale: locale.value,
  })
  if (isEqualMonth(_secondMonthPlaceholder, placeholder.value))
    placeholder.value = placeholder.value.subtract({ months: 1 })
});

// Reload the table when the refreshTable event is emitted
watch(() => globalStore.refreshTable, (newValue) => {
  reloadTable();
});

async function handleEquipmentTypeChange(newType) {
  selectedEquipmentType.value = newType.id;
  SelectFilteredSensors.value = "";
  typeFilteredProvince.value = "";
  if (selectedEquipmentType.value != '') {
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
  if (SelectFilteredSensors.value != '' && selectedEquipmentType.value != '') {
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
const searchQuery = ref("");

const initializeDataTable = () => {
  dataTable.value = $("#data-table").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: "/admin/sensor-details",
      data: function (d) {
        console.log(d);
        d.search = searchQuery.value;
        d.page = currentPage.value;
        d.selectedEquipmentType = selectedEquipmentType.value;
        d.selectsensors = SelectFilteredSensors.value;
        d.typeFilteredProvince = selectProvince.value;
        d.selectedDate = null; // Pass selected date
        d.selectedStatus = selectedStatus.value; // Pass selected status
        d.startDate = startDate.value; // Pass start date
        d.endDate = endDate.value; // Pass end date
      },
      dataSrc: function (json) {
        numberOfPages = json.numberOfPages;
        updatePaginationUI(numberOfPages);
        return json.data; // Standard data return for DataTables
      }
    },
    columns: [
      {
        data: "date_time",
        name: "date_time",
        render: function (data) {
          return `<span>${formatDate(data)}</span>`;
        }
      },
      { data: "location", name: "location" },
      { data: "pm2_5", name: "pm2_5" },
      { data: "pm10", name: "pm10" },
      { data: "o3", name: "o3" },
      { data: "co", name: "co" },
      { data: "no2", name: "no2" },
      { data: "so2", name: "so2" },
      { data: null, render: function () { return "N/A"; } },
      { data: "co2", name: "co2" },
      {
        data: "status",
        name: "status",
        render: function (data) {
          var color = data === 'ACTIVE' ? 'green' : 'red';
          return `<span style="color:${color};">${data}</span>`;
        }
      },
      {
        data: null,
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return `<div id="dropdown-container-${row.id}"></div>`;
        }
      },
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
      api.rows().every(function () {
        const rowData = this.data();
        const container = document.getElementById(`dropdown-container-${rowData.id}`);
        if (container) {
          const app = createApp(AdminDataMgmentDropdown, {
            itemId: rowData.id, 
            status: rowData.status
          });
          app.mount(container);
        }
      });
    },

    createdRow: function (row, data, dataIndex) {
      $("td", row).addClass("border-b border-gray-200 custpadding");
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

watch(searchQuery, () => {
  handleSearch();
});

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

const formatDateForBackend = (date) => {
  if (!date) return ''; // Return empty string if date is not valid
  return new Intl.DateTimeFormat('en-CA').format(new Date(date));
};

watch(selectedDate, () => {
  if (selectedDate.value.start) {
    const formattedStart = formatDateForBackend(selectedDate.value.start);
    startDate.value = formattedStart;
    console.log("Start Date:", formattedStart);
  } else {
    startDate.value = null;
  }

  if (selectedDate.value.end) {
    const formattedEnd = formatDateForBackend(selectedDate.value.end);
    endDate.value = formattedEnd;
    console.log("End Date:", formattedEnd);
  } else {
    endDate.value = null;
  }

  reloadTable();
});

watch(selectedStatus, () => {
  reloadTable();
});

const exportSensorsData = async () => {
  const params = {
    search: searchQuery.value,
    selectedEquipmentType: selectedEquipmentType.value,
    selectsensors: SelectFilteredSensors.value,
    typeFilteredProvince: selectProvince.value,
    startDate: startDate.value,
    endDate: endDate.value,
    selectedStatus: selectedStatus.value,
  };

  axios.get('/admin/export-sensors-data', { params, responseType: 'blob' })
    .then(response => {
      const url = window.URL.createObjectURL(new Blob([response.data]));
      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', 'Sensors-data.xlsx');
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
    <AddDataModel :show="showAddDataModel" @close="showAddDataModel = false" />
    <div class="p-4">
      <div v-if=successMessage class="fixed top-4 right-4">
        <SuccessAlert :value="successMessage" />
      </div>
      <div v-if=errorMessage class="fixed top-4 right-4">
        <ErrorAlert :value="errorMessage" />
      </div>
      <h1 class="text-2xl font-bold">Data Management</h1>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        <div class="col-span-1 bg-green1 text-white rounded-lg shadow p-4">
          <div class="flex items-start">
            <div class="">
              <img src="/images/totaldataentriesicon.svg" class="w-auto mt-2" />
            </div>
            <div class="ml-3">
              <div class="text-sm font-semibold">Total Data Entries</div>
              <div class="text-xs mt-2">
                Last Update: 01 July 2024, 08:25 AM
              </div>
              <div class="mt-3 text-3xl font-bold">{{ sensorData.total_count }}</div>
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
              <div class="mt-3 text-3xl font-bold">{{ sensorData.active_count }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="flex space-x-4 mt-7">
        <EquipmentDropdown side="left" :equipment-types="equipmentTypes" @type-changed="handleEquipmentTypeChange" />
        <MeasuredByDropdown :sensors="typeFilteredSensors" @sensorChanged="handleTypeChanged" />
        <ProvinceDropdown :province="typeFilteredProvince" @provinceChanged="handlerprovinceChanged"
          color="text-green-700" icon="/images/greenpinicon.png" />

      </div>

      <div class="flex items-center justify-between bg-white mt-7">
        <div class="flex items-center">
          <div class="relative">
            <input v-model="searchQuery" type="text" placeholder="Search"
              class="text-green-700 border border-gray-300 placeholder-gray-300 rounded-full pl-10 pr-4 py-2 w-60 focus:outline-none focus:ring-2 focus:ring-green-500" />
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-green-700 w-5 h-5"
              xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 21l-4.35-4.35m3.23-4.88a6.375 6.375 0 11-12.75 0 6.375 6.375 0 0112.75 0z" />
            </svg>
          </div>
          <div class="relative">
            <div class="ml-4 flex items-center">
              <Popover>
                <PopoverTrigger as-child>
                  <Button variant="outline" :class="cn(
                    'w-[280px] justify-between text-left font-normal rounded-full',
                    !selectedDate && 'text-muted-foreground',
                  )
                    ">
                    <div class="flex items-center">
                      <CalendarIcon class="mr-2 h-4 w-4" />
                      <template v-if="selectedDate.start">
                        <template v-if="selectedDate.end">
                          {{
                            formatter.custom(toDate(selectedDate.start), {
                              dateStyle: "medium",
                            })
                          }}
                          -
                          {{
                            formatter.custom(toDate(selectedDate.end), {
                              dateStyle: "medium",
                            })
                          }}
                        </template>

                        <template v-else>
                          {{
                            formatter.custom(toDate(selectedDate.start), {
                              dateStyle: "medium",
                            })
                          }}
                        </template>
                      </template>
                      <template v-else>
                        Filter by Date Range
                      </template>
                    </div>
                    <X class="mr-2 h-4 w-4" @click.stop="selectedDate = { start: null, end: null }"
                      v-if="selectedDate && selectedDate.start" />
                  </Button>
                </PopoverTrigger>
                <PopoverContent class="w-auto p-0">
                  <RangeCalendarRoot v-slot="{ weekDays }" v-model="selectedDate" v-model:placeholder="placeholder"
                    class="p-3">
                    <div class="flex flex-col gap-y-4 mt-4 sm:flex-row sm:gap-x-4 sm:gap-y-0">
                      <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                          <div class="flex items-center gap-1">
                            <button :class="cn(
                              buttonVariants({ variant: 'outline' }),
                              'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
                            )
                              " @click="updateYear('first', -1)">
                              <ChevronsLeft class="h-4 w-4" />
                            </button>
                            <button :class="cn(
                              buttonVariants({ variant: 'outline' }),
                              'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
                            )
                              " @click="updateMonth('first', -1)">
                              <ChevronLeft class="h-4 w-4" />
                            </button>
                          </div>
                          <div :class="cn('text-sm font-medium')">
                            {{
                              formatter.fullMonthAndYear(
                                toDate(firstMonth.value),
                              )
                            }}
                          </div>
                          <div class="flex items-center gap-1">
                            <button :class="cn(
                              buttonVariants({ variant: 'outline' }),
                              'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
                            )
                              " @click="updateMonth('first', 1)">
                              <ChevronRight class="h-4 w-4" />
                            </button>
                            <button :class="cn(
                              buttonVariants({ variant: 'outline' }),
                              'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
                            )
                              " @click="updateYear('first', 1)">
                              <ChevronsRight class="h-4 w-4" />
                            </button>
                          </div>
                        </div>
                        <RangeCalendarGrid>
                          <RangeCalendarGridHead>
                            <RangeCalendarGridRow>
                              <RangeCalendarHeadCell v-for="day in weekDays" :key="day" class="w-full">
                                {{ day }}
                              </RangeCalendarHeadCell>
                            </RangeCalendarGridRow>
                          </RangeCalendarGridHead>
                          <RangeCalendarGridBody>
                            <RangeCalendarGridRow v-for="(
                    weekDates, index
                  ) in firstMonth.rows" :key="`weekDate-${index}`" class="mt-1 w-full">
                              <RangeCalendarCell v-for="weekDate in weekDates" :key="weekDate.toString()"
                                :date="weekDate">
                                <RangeCalendarCellTrigger :day="weekDate" :month="firstMonth.value" />
                              </RangeCalendarCell>
                            </RangeCalendarGridRow>
                          </RangeCalendarGridBody>
                        </RangeCalendarGrid>
                      </div>
                      <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                          <div class="flex items-center gap-1">
                            <button :class="cn(
                              buttonVariants({ variant: 'outline' }),
                              'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
                            )
                              " @click="updateYear('second', -1)">
                              <ChevronsLeft class="h-4 w-4" />
                            </button>
                            <button :class="cn(
                              buttonVariants({ variant: 'outline' }),
                              'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
                            )
                              " @click="updateMonth('second', -1)">
                              <ChevronLeft class="h-4 w-4" />
                            </button>
                          </div>
                          <div :class="cn('text-sm font-medium')">
                            {{
                              formatter.fullMonthAndYear(
                                toDate(secondMonth.value),
                              )
                            }}
                          </div>
                          <div class="flex items-center gap-1">
                            <button :class="cn(
                              buttonVariants({ variant: 'outline' }),
                              'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
                            )
                              " @click="updateMonth('second', 1)">
                              <ChevronRight class="h-4 w-4" />
                            </button>
                            <button :class="cn(
                              buttonVariants({ variant: 'outline' }),
                              'h-7 w-7 bg-transparent p-0 opacity-50 hover:opacity-100',
                            )
                              " @click="updateYear('second', 1)">
                              <ChevronsRight class="h-4 w-4" />
                            </button>
                          </div>
                        </div>
                        <RangeCalendarGrid>
                          <RangeCalendarGridHead>
                            <RangeCalendarGridRow>
                              <RangeCalendarHeadCell v-for="day in weekDays" :key="day" class="w-full">
                                {{ day }}
                              </RangeCalendarHeadCell>
                            </RangeCalendarGridRow>
                          </RangeCalendarGridHead>
                          <RangeCalendarGridBody>
                            <RangeCalendarGridRow v-for="(
                    weekDates, index
                  ) in secondMonth.rows" :key="`weekDate-${index}`" class="mt-1 w-full">
                              <RangeCalendarCell v-for="weekDate in weekDates" :key="weekDate.toString()"
                                :date="weekDate">
                                <RangeCalendarCellTrigger :day="weekDate" :month="secondMonth.value" />
                              </RangeCalendarCell>
                            </RangeCalendarGridRow>
                          </RangeCalendarGridBody>
                        </RangeCalendarGrid>
                      </div>
                    </div>
                  </RangeCalendarRoot>
                </PopoverContent>
              </Popover>
            </div>
          </div>
          <div class="relative">
            <div class="ml-4 flex items-center">
              <Select v-model="selectedStatus">
                <SelectTrigger class="w-[100px] rounded-full">
                  <SelectValue class="mx-auto" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectItem value="ALL">
                      All
                    </SelectItem>
                    <SelectItem value="ACTIVE">
                      Active
                    </SelectItem>
                    <SelectItem value="INACTIVE">
                      Inactive
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </div>
          </div>
          <!--   <div class="ml-4 flex items-center">
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
          <!--  <div class="ml-4 flex items-center">
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
          </div>-->
        </div>
        <div class="flex items-center">
          <button class="flex items-center px-6 py-2 border border-gray-300 rounded-full text-green-700 font-semibold"
            @click="exportSensorsData">
            <img src="/images/exportdataicon.svg" alt="Filter Icon" class="w-4 h-4 mr-2" />
            Export Data
          </button>
          <div class="relative inline-block text-left">
            <div>
              <button @click="toggleDropdown" type="button" class="px-4 ml-4 py-2 bg-green1 text-white rounded-full"
                id="options-menu" aria-haspopup="true" aria-expanded="true">
                + Add New Data
              </button>
            </div>

            <div v-if="isOpen"
              class="font-medium origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
              <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                <a href="#" class="block px-4 py-2 text-sm text-green1" role="menuitem"
                  @click="showAddDataModel = true">Add
                  Data Manually</a>
                <a href="#" class="block px-4 py-2 text-sm text-green3" role="menuitem">Upload Data Files</a>
              </div>
            </div>
          </div>
          <!-- <button
            @click="toggleDropdown"
            class="px-4 ml-4 py-2 bg-green1 text-white rounded-full"
          >
            + Add New Data
          </button> -->
        </div>
      </div>

      <div class="container mx-auto my-4">
        <table class="min-w-full bg-white ctable" id="data-table">
          <thead>
            <tr class="w-full text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
              <th class="py-3 px-4">Date & Time</th>
              <th class="py-3 px-4">Location</th>
              <th class="py-3 px-4">PM2.5</th>
              <th class="py-3 px-4">PM10</th>
              <th class="py-3 px-4">O3</th>
              <th class="py-3 px-4">CO</th>
              <th class="py-3 px-4">NO2</th>
              <th class="py-3 px-4">SO2</th>
              <th class="py-3 px-4">VOC</th>
              <th class="py-3 px-4">CO2</th>
              <th class="py-3 px-4">Status</th>
              <th class="py-3 px-4"></th>
            </tr>
          </thead>
          <!--
      <tbody>
        <tr v-for="item in sensorData.data" :key="item.date_time" class="border-b border-t border-gray-200">
          <td class="py-3 px-4 text-center">
            <div class="text-black">{{ formatDate(item.date_time) }}</div>
          </td>
          <td class="py-3 px-4">
            <div class="text-black">{{ item.location }}</div>
          </td>
          <td class="py-3 px-4">{{ item.pm2_5 }} µg/m³</td>
          <td class="py-3 px-4">{{ item.pm10 }} µg/m³</td>
          <td class="py-3 px-4">{{ item.o3 }} ppb</td>
          <td class="py-3 px-4">{{ item.co }} ppm</td>
          <td class="py-3 px-4">{{ item.no2 }} ppb</td>
          <td class="py-3 px-4">{{ item.so2 }} ppb</td>
          <td class="py-3 px-4">{{ item.voc || 'N/A' }}</td>
          <td class="py-3 px-4">{{ item.co2 }} ppm</td>
          <td class="py-3 px-4" :class="statusClass(item.status)">{{ item.status }}</td>
          <td class="text-right text-gray-500 font-bold cursor-pointer">
            <AdminDataMgmentDropdown :itemId="item.id" @dataDeleted="fetchSensorData" />
          </td>
        </tr>
      </tbody> -->
        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between w-full mt-4">
      <button @click="previousPage" class="px-4 py-1 rounded-lg border border-gray-300">
        Previous
      </button>

      <span class="text-sm">
        <span class="text-sm font-medium">
          <span class="text-green1">Page {{ currentPage }}</span> of
          <span id="pagination-info"></span>
        </span>
      </span>

      <button @click="nextPage" class="px-3 py-1 rounded-lg border border-gray-300">
        Next
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
