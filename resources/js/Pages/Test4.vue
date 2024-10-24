<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import AddUserModel from "@/Components/Custom/Models/AddUserModel.vue";
import EditUserModel from "@/Components/Custom/Models/EditUserModel.vue";
import AddRoleModel from "@/Components/Custom/Models/AddRoleModel.vue";
import FiltersModel from "@/Components/Custom/Models/FiltersModel.vue";
import AdminPagination from "@/Components/Custom/AdminPagination.vue";

const showAddUserModal = ref(false);
const showEditUserModal = ref(false);
const showAddRoleModal = ref(false);
const showFiltersModal = ref(false);

import { computed, onMounted, onUnmounted, ref, defineProps, watch } from "vue";



const currentTab = ref("User Lists");
const searchQuery = ref('');

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
  initialUsers: Object,
});

const users = ref(props.initialUsers.data);

const updateUsers = (newUsers) => {
  users.value = newUsers;
};

const formatRoles = (roles) => {
  if (!roles || roles.length === 0) return 'No roles';
  return roles.map(role => role.name).join(', ');
};

watch(searchQuery, () => {
  console.log(searchQuery.value)
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
    <FiltersModel :show="showFiltersModal" @close="showFiltersModal = false" />

    <div class="p-4">
      <h1 class="text-2xl font-bold">User Management</h1>
      <p class="text-gray-600">
        A summary of the user management features and current user statistics.
      </p>
    </div>

    <div class="w-full">
      <div class="bg-white">

        <!-- Tab content -->
        <div class="p-6">
          <div>
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
                <button
                  @click="showAddUserModal = true"
                  class="px-4 ml-4 py-2 bg-green1 text-white rounded-full"
                >
                  + Add New User
                </button>
              </div>
            </div>

            <div class="container mx-auto my-4">
              <table class="min-w-full bg-white">
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
                    <th class="py-3 px-6">Role</th>
                    <th class="py-3 px-6">Email</th>
                    <th class="py-3 px-6">Contact Number</th>
                    <th class="py-3 px-6">Location</th>
                    <th class="py-3 px-6">Last Login</th>
                    <th class="py-3 px-6"></th>
                    <!-- Empty header for the three dots column -->
                  </tr>
                </thead>
                <tbody>
                  <tr
                    class="border-b border-t border-gray-200"
                    v-for="user in users"
                    :key="user.id"
                  >
                    <td class="py-3 px-6">{{ user.name }}</td>
                    <td class="py-3 px-6">{{ formatRoles(user.roles) }}</td>
                    <td class="py-3 px-6">{{ user.email }}</td>
                    <td class="py-3 px-6">+94 77 586 896</td>
                    <td class="py-3 px-6">Colombo</td>
                    <td class="py-3 px-6">
                      <div class="text-black">23 Hrs Ago</div>
                      <div class="text-gray-400 test-sm">Colombo</div>
                    </td>
                    <td
                      class="text-right text-gray-500 font-bold cursor-pointer"
                    >
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
                </tbody>
              </table>
            </div>

            <AdminPagination
              :initialPage="initialUsers.meta.current_page"
              :initialTotalPages="initialUsers.meta.last_page"
              routeName="usermanagement.index"
              @updateData="updateUsers"
            />
          </div>
          
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

<style>
</style>