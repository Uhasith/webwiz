<script setup>
import { onMounted } from "vue";
import AdminUserDropdown from "@/Components/Custom/Dropdowns/AdminUserDropdown.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { Toaster } from '@/Components/ui/sonner';
import useAuth from "@/Utils/useAuth";
import axios from 'axios';

const page = usePage();
const { hasPermission } = useAuth();

onMounted(() => {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = page.props.csrf_token || '';
});

const isActive = (paths) => {
  // Ensure paths is an array
  if (!Array.isArray(paths)) {
    paths = [paths];
  }
  return paths.includes(page.url);
};
</script>

<template>
  <Toaster richColors closeButton position="top-right" />
  <div class="h-screen flex flex-col">
    <div class="w-full border-b border-gray-300 p-5 flex justify-between items-center">
        <a href="/admin/dashboard"><img src="/images/logo.png" alt="Centered Image" class="h-12" /></a>

      <!-- <div class="relative">
        <input
          type="text"
          placeholder="Search"
          class="text-green-700 border border-gray-300 placeholder-gray-300 rounded-full pl-10 pr-4 py-2 w-80 focus:outline-none focus:ring-2 focus:ring-green-500"
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
      </div> -->
      <AdminUserDropdown />
    </div>
    <div class="flex flex-grow overflow-y-auto">
      <!-- Sidebar -->
      <div class="w-1/6 border-l border-r border-gray-300 flex-grow px-4">
        <!-- Sidebar content here -->
        <ul class="space-y-4 mt-8">
          <li>
            <Link href="/admin/dashboard" :class="[
                isActive(['/admin/dashboard'])
                  ? 'bg-green1 text-white'
                  : 'text-gray-700',
                'flex items-center px-2 py-3 space-x-2 rounded-full cursor-pointer',
              ]">
            <img src="/images/dashboardicon.svg" alt="Centered Image" class="" />
            <span class="text-md">Dashboard</span>
            </Link>
          </li>

          <li v-if="hasPermission('View Sub Admin')">
            <Link href="/admin/dashboard/user-management" :class="[
                isActive(['/admin/dashboard/user-management'])
                  ? 'bg-green1 text-white'
                  : 'text-gray-700',
                'flex items-center px-2 py-3 space-x-2 rounded-full cursor-pointer',
              ]">
            <img src="/images/usermgmenticon.svg" alt="Centered Image" class="" />
            <span class="text-md">User Management</span>
            </Link>
          </li>
          <li v-if="hasPermission('View Sensor Data')">
            <Link href="/admin/dashboard/data-management" :class="[
                isActive(['/admin/dashboard/data-management'])
                  ? 'bg-green1 text-white'
                  : 'text-gray-700',
                'flex items-center px-2 py-3 space-x-2 rounded-full cursor-pointer',
              ]">
            <img src="/images/datamgmenticon.svg" alt="Centered Image" class="" />
            <span class="text-md">Data Management</span>
            </Link>
          </li>
            <li v-if="hasPermission('View Sensor Data')">
                <Link href="/admin/dashboard/historical-data" :class="[
                isActive(['/admin/dashboard/historical-data'])
                  ? 'bg-green1 text-white'
                  : 'text-gray-700',
                'flex items-center px-2 py-3 space-x-2 rounded-full cursor-pointer',
              ]">
                    <img src="/images/data-history.svg" alt="Centered Image" class="" />
                    <span class="text-md">Historical Data</span>
                </Link>
            </li>
          <li>
            <Link href="/admin/dashboard/equipments" :class="[
                isActive(['/admin/dashboard/equipments'])
                  ? 'bg-green1 text-white'
                  : 'text-gray-700',
                'flex items-center px-2 py-3 space-x-2 rounded-full cursor-pointer',
              ]">
            <img src="/images/equipmenticon.svg" alt="Centered Image" class="" />
            <span class="text-md">Equipment</span>
            </Link>
          </li>
          <li>
            <Link href="/admin/dashboard/reports" :class="[
                isActive(['/admin/dashboard/reports'])
                  ? 'bg-green1 text-white'
                  : 'text-gray-700',
                'flex items-center px-2 py-3 space-x-2 rounded-full cursor-pointer',
              ]">
            <img src="/images/reportsicon.svg" alt="Centered Image" class="" />
            <span class="text-md">Reports</span>
            </Link>
          </li>
          <li>
            <Link href="/admin/dashboard/settings" :class="[
                isActive(['/admin/dashboard/settings', '/admin/dashboard/settings/notification-setting'])
                  ? 'bg-green1 text-white'
                  : 'text-gray-700',
                'flex items-center px-2 py-3 space-x-2 rounded-full cursor-pointer',
              ]">
            <img src="/images/settingsicon.svg" alt="Centered Image" class="" />
            <span class="text-md">Settings</span>
            </Link>
          </li>
        </ul>
      </div>
      <!-- Main content -->
      <div class="w-5/6 overflow-y-auto">
        <div class="">
          <slot />
        </div>
      </div>
    </div>
  </div>
</template>
