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
import BulkUploadModel from "@/Components/Custom/Models/BulkUploadModel.vue";

const props = defineProps({
  equipmentTypes: Object,
  sensors: Object,
  province: Object,
    manuallyAddedHourlySensorDataCount: Number,
    lastUpdatedDate: String
});

const globalStore = useGlobalStore();

let numberOfPages = 0; // To store the number of pages
const sensorData = ref({ data: [], total_count: 0, active_count: 0 });
const selectedEquipmentType = ref("");
const typeFilteredSensors = ref(props.sensors);
const manuallyAddedHourlySensorDataCount = ref(props.manuallyAddedHourlySensorDataCount);
const lastUpdatedDate = ref(props.lastUpdatedDate);
const SelectFilteredSensors = ref("");
const typeFilteredProvince = ref("");
const selectProvince = ref("");
const successMessage = ref("");
const errorMessage = ref("");
const selectedStatus = ref("ALL");
const showBulkUploadModal = ref(false);

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
  if (selectedEquipmentType.value !== '') {
    await axios
      .get(`/sensors/${newType.id}`)
      .then((response) => {
        typeFilteredSensors.value = response.data;
        handleTypeChanged({'id':''})
      })
      .catch((error) => {
        console.error("Error fetching sensors:", error);
      });
  }
}

async function handleTypeChanged(newType) {
    SelectFilteredSensors.value = newType.id;
    let equipmentType = selectedEquipmentType.value;
    let sensorId = newType.id;
    if(SelectFilteredSensors.value === ''){
        sensorId = "all";
    }
    if(selectedEquipmentType.value === ''){
        equipmentType = "all";
    }
        await axios
            .get(`/admin/location/${equipmentType}/${sensorId}`)
            .then((response) => {
                typeFilteredProvince.value = response.data;
            })
            .catch((error) => {
                console.error("Error fetching sensors:", error);
            });

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
const isOpen = ref(false);

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

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
      url: "/admin/historical-data-sensor-details",
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
        manuallyAddedHourlySensorDataCount.value = json.manuallyAddedHourlySensorDataCount;
          lastUpdatedDate.value = json.lastUpdatedDate;
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
      { data: "sensor_location.location.name", name: "location" },
      { data: "pm2_5", name: "pm2_5" },
      { data: "pm10", name: "pm10" },
      { data: "o3", name: "o3" },
      { data: "co", name: "co" },
      { data: "no2", name: "no2" },
      { data: "so2", name: "so2" },
      { data: "temperature", name: "temperature" },
      { data: "pressure", name: "pressure" },
      { data: "wind", name: "wind" },
      { data: "humidity", name: "humidity" },
      { data: "precipitation", name: "precipitation" },
      {
        data: "status",
        name: "status",
        render: function (data) {
          var color = data === 'ACTIVE' ? 'green' : 'red';
          return `<span style="color:${color};">${data}</span>`;
        }
      }
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
      $("td", row).addClass("border-b border-gray-200 custpadding text-sm");
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

</script>

<template>
  <AdminLayout>
      <BulkUploadModel v-model:showBulkUploadModal="showBulkUploadModal"
                       :equipment-types="equipmentTypes"
                       :sensors="typeFilteredSensors"
                       :province="typeFilteredProvince"
                    @close="(message) => { showBulkUploadModal = false; successMessage = message; }" />
    <AddDataModel :show="showAddDataModel" @close="showAddDataModel = false" />
    <div class="p-4">
      <div v-if=successMessage class="fixed top-4 right-4">
        <SuccessAlert :value="successMessage" />
      </div>
      <div v-if=errorMessage class="fixed top-4 right-4">
        <ErrorAlert :value="errorMessage" />
      </div>
      <h1 class="text-2xl font-bold">Historical Data</h1>

      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
        <div class="col-span-1 bg-green1 text-white rounded-lg shadow p-4">
          <div class="flex items-start">
            <div class="">
              <img src="/images/totaldataentriesicon.svg" class="w-auto mt-2" />
            </div>
            <div class="ml-3">
              <div class="text-sm font-semibold">Total Data Entries</div>
              <div class="text-xs mt-2">
                Last Update: {{ formatDate(lastUpdatedDate) }}
              </div>
              <div class="mt-3 text-3xl font-bold">{{ manuallyAddedHourlySensorDataCount }}</div>
            </div>
          </div>
        </div>

      </div>

      <div class="flex space-x-4 mt-7">
        <EquipmentDropdown side="left" :equipment-types="equipmentTypes" @type-changed="handleEquipmentTypeChange" />
        <MeasuredByDropdown :sensors="typeFilteredSensors" @sensorChanged="handleTypeChanged" />
        <ProvinceDropdown ui="historical" :province="typeFilteredProvince" @provinceChanged="handlerprovinceChanged"
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
                      <SelectItem value="PENDING">
                      Pending
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
            </div>
          </div>

        </div>
        <div class="flex items-center">
          <div class="relative inline-block text-left">
            <div>
                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                    <a @click="showBulkUploadModal = true" href="#" class="block px-4 py-2 text-sm text-green3"
                       role="menuitem">⬆️ &nbsp; Upload Historical Data</a>
                </div>
            </div>

          </div>
        </div>
      </div>

      <div class="container mx-auto my-4" style="overflow-x: scroll">
        <table class="min-w-full bg-white ctable" id="data-table" style="max-width: 100px !important;">
          <thead>
            <tr class="w-full text-left text-sm text-gray-400  tracking-wider">
              <th class="py-3 px-4" style="min-width: 100px">Date & Time</th>
              <th class="py-3 px-4">Location</th>
              <th class="py-3 px-4">PM2.5(μg/m³)</th>
              <th class="py-3 px-4">PM10(μg/m³)</th>
              <th class="py-3 px-4">O3(ppb)</th>
              <th class="py-3 px-4">CO(ppb)</th>
              <th class="py-3 px-4">NO2(ppb)</th>
              <th class="py-3 px-4">SO2(ppb)</th>
              <th class="py-3 px-4">Temp(°C)</th>
              <th class="py-3 px-4">Pressure(mb)</th>
              <th class="py-3 px-4">Wind(km/h)</th>
              <th class="py-3 px-4">Humidity(%)</th>
              <th class="py-3 px-4">Rain(mm)</th>
              <th class="py-3 px-4">Status</th>
            </tr>
          </thead>

        </table>
      </div>
    </div>

    <!-- Pagination -->
    <div class="flex items-center justify-between w-full mt-4 p-4">
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
