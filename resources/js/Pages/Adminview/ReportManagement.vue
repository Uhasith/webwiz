<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { English } from "flatpickr/dist/l10n/default";
import { onMounted, ref, watch, nextTick } from "vue";
import { debounce } from "lodash";
import PreviewTemplate from "@/Components/Custom/Models/PreviewTemplate.vue";

const tabs = [{ name: "Report Templates" }, { name: "Report History" }];
const currentTab = ref("Report Templates");
const dataTable1 = ref(null);
const dataTable2 = ref(null);
const isOpen = ref(false);
const searchEquQuery = ref("");
const searchRecQuery = ref("");
const currentPage = ref(1);
const totalPages = ref(1);
const showPreviewModal = ref(false);
const reportId = ref(null);

const redirectToReportDetails = () => {
  window.location.href = '/admin/dashboard/create-report'; // Redirect to the route
};
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
        .then(() => initializeDataTable1());
});
// onMounted(() => {
//             attachEventListeners();
//         });
watch(currentTab, (newTab) => {
    if (newTab === "Report Templates") {
        nextTick(() => {
            initializeDataTable1();
        });
    }
    if (newTab === "Report History") {
        nextTick(() => {
            initializeDataTable2();
        });
    }
});



const initializeDataTable1 = () => {
    dataTable1.value = $("#template-table").DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: "/admin/report-templates",
            data: function (d) {
                d.search = searchEquQuery.value;
            },
        },
        columns: [
            { data: "name", name: "name" },
            { data: "description", name: "description" },
            { data: "id", render: function (data, type, row) {
                return `<button class="btn preview-btn py-3 px-6 text-green1 font-semibold" style="font-weight: 600;" data-id="${row.id}">Preview Template</button>`;
                }
            },
            { data: null, render: function() { return `<button class="btn py-3 px-6 text-green3 font-semibold" style="font-weight: 600;">Generate Report</button>`; }},
            { data: null, orderable: false, searchable: false, render: function() {
                return `<div class="relative inline-block text-left">
                          <button type="button" class="py-3 px-6">&#8942;</button>
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
        drawCallback: function () {
            const api = this.api();
            const pageInfo = api.page.info();
            $(".dataTables_info").html("Page " + (pageInfo.page + 1) + " of " + pageInfo.pages);
            updateCustomPagination(api);
            attachEventListeners();
        },
        createdRow: function (row) {
            $("td", row).addClass("border-b border-gray-200 custpadding");
        },
    });
};

const initializeDataTable2 = () => {
    dataTable2.value = $("#history-table").DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: "/admin/report-history",
            data: function (d) {
                d.search = searchEquQuery.value;
            },
        },
        columns: [
            { data: "created_at", name: "created_at" },
            { data: "name", name: "name" },
            { data: "description", name: "description" },
            { data: null, render: function() { return '<button class="btn py-3 px-6 text-green1 font-semibold" style="font-weight: 600;">Preview Template</button>'; }},
            { data: "username", name: "username" },
            { data: null, orderable: false, searchable: false, render: function() {
                return `<div class="relative inline-block text-left">
                          <button type="button" class="py-3 px-6">&#8942;</button>
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
        drawCallback: function () {
            const api = this.api();
            const pageInfo = api.page.info();
            $(".dataTables_info").html("Page " + (pageInfo.page + 1) + " of " + pageInfo.pages);
            updateCustomPagination(api);
        },
        createdRow: function (row) {
            $("td", row).addClass("border-b border-gray-200 custpadding");
        },
    });
};

const reloadTable = () => {
    if (dataTable1.value) {
        dataTable1.value.ajax.reload();
    }
};
const reloadTable2 = () => {
    if (dataTable2.value) {
        dataTable2.value.ajax.reload();
    }
};
const handleSearch = debounce(() => {
    reloadTable();
}, 300);

const handleSearch2 = debounce(() => {
    reloadTable2();
}, 300);

watch(searchEquQuery, () => {
    handleSearch();
});

watch(searchRecQuery, () => {
    handleSearch2();
});

const updateCustomPagination = (api) => {
    const info = api.page.info();
    currentPage.value = info.page + 1;
    totalPages.value = info.pages;
};

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

const attachEventListeners = () => {
    document.querySelectorAll('.preview-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            const rId = Number(event.currentTarget.getAttribute("data-id"));
            showPreviewModal.value = true;
            reportId.value = rId;
            console.log("Preview Template ID:", rId);
        });
    });
};
</script>

<template>
  <AdminLayout>
    <PreviewTemplate :show="showPreviewModal" :rId="reportId" @close="showPreviewModal = false" />

    <div class="p-4">
      <h1 class="text-2xl font-bold">Report Management</h1>

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

      <div class="">
        <div v-if="currentTab === 'Report Templates'">
          <div class="flex items-center justify-between bg-white mt-7">
            <div class="flex items-center">
              <div class="relative">
                <input
                  type="text"
                   v-model="searchEquQuery"
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
              <button
                @click="redirectToReportDetails"
                class="px-4 ml-4 py-2 bg-green1 text-white rounded-full"
              >
                + Add New Data
              </button>
            </div>
          </div>

          <div class="container mx-auto my-4">
            <table class="min-w-full bg-white" id="template-table">
              <thead>
                <tr
                  class="w-full text-left text-xs font-medium text-gray-400 uppercase tracking-wider"
                >
                  <th class="py-3 px-6 flex items-center">
                    <img
                      src="/images/checkboxicon.svg"
                      alt="Icon"
                      class="mr-2 w-4 h-4"
                    />
                    Template Name
                  </th>
                  <th class="py-3 px-6">Template Description</th>
                  <th class="py-3 px-6">Preview Template</th>
                  <th class="py-3 px-6">Generate</th>
                  <th class="py-3 px-6"></th>
                  <!-- Empty header for the three dots column -->
                </tr>
              </thead>
            </table>
          </div>

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
                class="px-3 py-1 rounded-lg border border-gray-300"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages, }">
                Next
            </button>
        </div>
        </div>
        <div v-else-if="currentTab === 'Report History'">
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

              <button
                @click="redirectToReportDetails"
                class="px-4 ml-4 py-2 bg-green1 text-white rounded-full"
              >
                + Add New Data
              </button>
            </div>
          </div>

          <div class="container mx-auto my-4">
            <table class="min-w-full bg-white" id="history-table">
              <thead>
                <tr
                  class="w-full text-left text-xs font-medium text-gray-400 uppercase tracking-wider"
                >
                  <th class="py-3 px-6">Date & Time</th>
                  <th class="py-3 px-6 flex items-center">
                    <img
                      src="/images/checkboxicon.svg"
                      alt="Icon"
                      class="mr-2 w-4 h-4"
                    />Report Name
                  </th>
                  <th class="py-3 px-6">Report Description</th>
                  <th class="py-3 px-6">Preview Template</th>
                  <th class="py-3 px-6">Generated By</th>
                  <th class="py-3 px-6"></th>
                </tr>
              </thead>
              <!-- <tbody>
                <tr class="border-b border-t border-gray-200">
                  <td class="py-3 px-6">Daily Summary</td>
                  <td class="py-3 px-6">
                    Provides a summary of the daily AQI data
                  </td>
                  <td class="py-3 px-6 text-green1 font-semibold">
                    Preview Template
                  </td>
                  <td class="py-3 px-6 text-green3 font-semibold">
                    Generate Report
                  </td>
                  <td class="text-right text-gray-500 font-bold cursor-pointer">
                    <div class="relative inline-block text-left">
                      <div>
                        <button
                          @click="toggleDropdown"
                          type="button"
                          class="py-3 px-6"
                          id="options-menu"
                          aria-haspopup="true"
                          aria-expanded="true"
                        >
                          &#8942;
                        </button>
                      </div>

                      <div
                        v-if="isOpen"
                        class="font-medium origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
                      >
                        <div
                          class="py-1"
                          role="menu"
                          aria-orientation="vertical"
                          aria-labelledby="options-menu"
                        >
                          <a
                            href="#"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                            role="menuitem"
                            @click="showEditUserModal = true"
                            >Edit User</a
                          >
                          <a
                            href="#"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
                            role="menuitem"
                            >Next Option</a
                          >
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
                <tr class="border-b border-t border-gray-200">
                  <td class="py-3 px-6">Monthly Trend Report</td>
                  <td class="py-3 px-6">
                    Shows trends in AQI data over the month
                  </td>
                  <td class="py-3 px-6 text-green1 font-semibold">
                    Preview Template
                  </td>
                  <td class="py-3 px-6 text-green3 font-semibold">
                    Generate Report
                  </td>
                  <td
                    class="py-3 px-6 text-right text-gray-500 font-bold cursor-pointer"
                  >
                    &#8942;
                  </td>
                </tr>
              </tbody> -->
            </table>
          </div>

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
                class="px-3 py-1 rounded-lg border border-gray-300"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages, }">
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

.btn-dark-blue {
    background-color: #1e3a8a;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-dark-blue:hover {
    background-color: #1c3b89;
}

.btn-dark-green {
    background-color: #166534;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
}

.btn-dark-green:hover {
    background-color: #14552e;
}

.custpadding {
    padding-top: 0.75rem !important;
    padding-bottom: 0.75rem !important;
    padding-left: 1.5rem !important;
    padding-right: 1.5rem !important;
}
</style>
