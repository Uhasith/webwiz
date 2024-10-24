<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AddUserModel from "@/Components/Custom/Models/AddUserModel.vue";
import EditUserModel from "@/Components/Custom/Models/EditUserModel.vue";
import AddRoleModel from "@/Components/Custom/Models/AddRoleModel.vue";
import FiltersModel from "@/Components/Custom/Models/FiltersModel.vue";
import AdminPagination from "@/Components/Custom/AdminPagination.vue";
import { debounce } from "lodash";

const showAddUserModal = ref(false);
const showEditUserModal = ref(false);
const showAddRoleModal = ref(false);
const showFiltersModal = ref(false);

import {
  computed,
  onMounted,
  onUnmounted,
  ref,
  defineProps,
  watch,
  nextTick,
} from "vue";

const tabs = [{ name: "User Lists" }, { name: "User Permissions" }];

const currentTab = ref("User Lists");
const searchQuery = ref("");

/// Section for edit user dropdown
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
/// Section for edit user dropdown

const props = defineProps({
  roles: Array,
});

const selectedRoles = ref([]);
const dataTable = ref(null);

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
  dataTable.value = $("#users-table").DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: route("test.paginate"),
      data: function (d) {
        d.roles = selectedRoles.value;
        d.search = searchQuery.value; // Add this line
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
      { data: "roles", name: "roles.name" },
      { data: "email", name: "email" },
      {
        data: null,
        render: function (data, type, row) {
          return "+94 77 586 896"; // Placeholder, replace with actual data when available
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return "Colombo"; // Placeholder, replace with actual data when available
        },
      },
      {
        data: null,
        render: function (data, type, row) {
          return `<div class="text-black">23 Hrs Ago</div>
              <div class="text-gray-400 text-sm">Colombo</div>`; // Placeholder, replace with actual data when available
        },
      },
      {
        data: null,
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return `<div class="text-right text-gray-500 font-bold cursor-pointer">&#8942;</div>`;
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
    pageLength: 2,
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
    // rowCallback: function (row, data, index) {
    //   $(row).addClass("border-b border-t border-gray-200");
    //   $("td", row).addClass("py-3 px-6");
    // },
    createdRow: function (row, data, dataIndex) {
      $("td", row).addClass("border-b border-gray-200 custpadding");
    },
    initComplete: function () {
      // Custom filtering function which will search data in column four between two values
      $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
        var min = $("#min").val();
        var max = $("#max").val();
        var age = parseFloat(data[3]) || 0; // use data for the age column

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

      // Event listener to the two range filtering inputs to redraw on input
      $("#min, #max").keyup(function () {
        dataTable.value.draw();
      });
    },
  });

  // Handle the edit button click
  window.editUser = function (userId) {
    console.log("Edit user with ID:", userId);
    // Implement your edit logic here
  };
};

const reloadTable = () => {
  if (dataTable.value) {
    dataTable.value.ajax.reload();
  }
};

watch(selectedRoles, () => {
  reloadTable();
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

const clearFilters = () => {
  selectedRoles.value = [];
  reloadTable();
};

const handleSearch = debounce(() => {
  reloadTable();
}, 300);

watch(searchQuery, () => {
  handleSearch();
});

const handleApplyFilters = (filters) => {
  selectedRoles.value = filters.roles;
  console.log("Applied filters:", selectedRoles.value);
  reloadTable();
};

const removeRole = (roleToRemove) => {
  selectedRoles.value = selectedRoles.value.filter(
    (role) => role !== roleToRemove
  );
  reloadTable();
};

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

const changetab = (tab) => {
  reloadTable();
};

watch(currentTab, (newTab, oldTab) => {
  if (newTab === "User Lists") {
    nextTick(() => {
      initializeDataTable();
    });
  }
});
</script>



<template>
  <AdminLayout>
    <AddUserModel :show="showAddUserModal" @close="showAddUserModal = false" />
    <AddRoleModel :show="showAddRoleModal" @close="showAddRoleModal = false" />
    <EditUserModel
      :show="showEditUserModal"
      @close="showEditUserModal = false"
    />
    <FiltersModel
      :show="showFiltersModal"
      @close="showFiltersModal = false"
      :availableRoles="roles"
      @applyFilters="handleApplyFilters"
      :initialSelectedRoles="selectedRoles"
    />

    <div class="p-4">
      <h1 class="text-2xl font-bold">User Management</h1>
      <p class="text-gray-600">
        A summary of the user management features and current user statistics.
      </p>
    </div>

    <div class="w-full">
      <div class="bg-white">
        <!-- Tab navigation -->
        <div class="border-b border-gray-200">
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
        <div class="p-6">
          <div v-if="currentTab === 'User Lists'">
            <div class="flex items-center justify-between bg-white">
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
                    v-for="role in selectedRoles"
                    :key="role"
                    class="flex items-center px-4 py-2 bg-green-50 rounded-full text-green-700 font-semibold mr-2"
                  >
                    <span>{{ role }}</span>
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="h-6 w-6 ml-2"
                      fill="none"
                      viewBox="0 0 24 24"
                      stroke="currentColor"
                      @click.stop="removeRole(role)"
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
                <button
                  @click="showAddUserModal = true"
                  class="px-4 ml-4 py-2 bg-green1 text-white rounded-full"
                >
                  + Add New User
                </button>
              </div>
            </div>

            <div class="container mx-auto my-4">
              <table id="users-table" class="min-w-full">
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
                      <span>User Name</span>
                    </th>
                    <th class="py-3 px-6">Roles</th>
                    <th class="py-3 px-6">Email</th>
                    <th class="py-3 px-6">Contact Number</th>
                    <th class="py-3 px-6">Location</th>
                    <th class="py-3 px-6">Last Login</th>
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

            <!-- <div class="flex items-center justify-between w-full">
              <button class="px-4 py-1 rounded-lg border border-gray-300">
                Previous
              </button>

              <span class="text-sm"> <span class="text-sm font-medium"> <span class="text-green1">Page 1</span> of 10 </span> </span>

              <button class="px-3 py-1 rounded-lg border border-gray-300">
                Next
              </button>
            </div> -->
          </div>
          <div v-else-if="currentTab === 'User Permissions'">
            <!-- User Permissions content -->

            <div class="flex justify-end">
              <button
                @click="showAddRoleModal = true"
                class="px-4 py-2 bg-green1 text-white rounded-full"
              >
                + Add New Role
              </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 first_div mt-4">
              <div class="col-span-1">
                <div
                  class="bg-white border border-gray-200 rounded-lg p-4 mb-4"
                >
                  <div class="flex justify-between items-center">
                    <h3 class="text-md font-semibold">Admin</h3>
                    <div class="text-md cursor-pointer font-bold p-4">
                      &#8942;
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Data Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2 ml-4"
                    >
                      <p class="text-sm">Add New Data</p>
                      <div
                        @click="toggleSwitch"
                        class="w-8 h-5 flex items-center justify-between bg-green1 rounded-full px-1 cursor-pointer"
                      >
                        <div
                          id="switchBall"
                          class="bg-white w-3 h-3 rounded-full transform translate-x-3 transition-transform"
                        ></div>
                      </div>
                    </div>
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2 ml-4"
                    >
                      <p class="text-sm">Delete Data</p>
                      <div
                        @click="toggleSwitch"
                        class="w-8 h-5 flex items-center justify-between bg-green1 rounded-full px-1 cursor-pointer"
                      >
                        <div
                          id="switchBall"
                          class="bg-white w-3 h-3 rounded-full transform translate-x-3 transition-transform"
                        ></div>
                      </div>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Report Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">User Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-white border border-gray-200 rounded-lg p-4 mb-4"
                >
                  <div class="flex justify-between items-center">
                    <h3 class="text-md font-semibold">Sub Admin</h3>
                    <div class="text-md cursor-pointer font-bold p-4">
                      &#8942;
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Data Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Report Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">User Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-white border border-gray-200 rounded-lg p-4 mb-4"
                >
                  <div class="flex justify-between items-center">
                    <h3 class="text-md font-semibold">Moderator</h3>
                    <div class="text-md cursor-pointer font-bold p-4">
                      &#8942;
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Data Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Report Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">User Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-span-1">
                <div
                  class="bg-white border border-gray-200 rounded-lg p-4 mb-4"
                >
                  <div class="flex justify-between items-center">
                    <h3 class="text-md font-semibold">Report Admin</h3>
                    <div class="text-md cursor-pointer font-bold p-4">
                      &#8942;
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Supervisor</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2 ml-4"
                    >
                      <p class="text-sm">Add New Data</p>
                      <div
                        @click="toggleSwitch"
                        class="w-8 h-5 flex items-center justify-between bg-green1 rounded-full px-1 cursor-pointer"
                      >
                        <div
                          id="switchBall"
                          class="bg-white w-3 h-3 rounded-full transform translate-x-3 transition-transform"
                        ></div>
                      </div>
                    </div>
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2 ml-4"
                    >
                      <p class="text-sm">Delete Data</p>
                      <div
                        @click="toggleSwitch"
                        class="w-8 h-5 flex items-center justify-between bg-green1 rounded-full px-1 cursor-pointer"
                      >
                        <div
                          id="switchBall"
                          class="bg-white w-3 h-3 rounded-full transform translate-x-3 transition-transform"
                        ></div>
                      </div>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Data Admin</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">User Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-white border border-gray-200 rounded-lg p-4 mb-4"
                >
                  <div class="flex justify-between items-center">
                    <h3 class="text-md font-semibold">Sub Admin</h3>
                    <div class="text-md cursor-pointer font-bold p-4">
                      &#8942;
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Data Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Report Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">User Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>
                </div>

                <div
                  class="bg-white border border-gray-200 rounded-lg p-4 mb-4"
                >
                  <div class="flex justify-between items-center">
                    <h3 class="text-md font-semibold">Moderator</h3>
                    <div class="text-md cursor-pointer font-bold p-4">
                      &#8942;
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Data Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">Report Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>

                  <div class="border border-gray-300 rounded-md mb-2">
                    <div
                      class="flex items-center justify-between w-auto px-4 py-2"
                    >
                      <p class="text-sm font-bold">User Management</p>
                      <svg
                        class="w-5 h-5 ml-2 -mr-1 cursor-pointer transform rotate-180"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                      >
                        <path
                          fill-rule="evenodd"
                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                          clip-rule="evenodd"
                        />
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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

