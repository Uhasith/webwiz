<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link } from "@inertiajs/vue3";
</script>

<template>
  <AdminLayout>
    <div class="p-4">
      <h1 class="text-2xl font-bold mb-8">Settings</h1>

      <div>
        <div
          class="rounded-[12px] border px-4 py-2 flex justify-between items-center mb-[12px] cursor-pointer"
        >
          <div>
            <h2 class="font-semibold text-[#131313]">
              Account Settings
            </h2>
            <p class="text-sm text-[#697077]">
              Allows users to update their personal information and account
              details
            </p>
          </div>
          <div class="">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-[#131313] "
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </div>
        </div>
        <Link href="/admin/dashboard/settings/notification-setting"
          class="rounded-[12px] border px-4 py-2 flex justify-between items-center mb-[12px] cursor-pointer"
        >
          <div>
            <h2 class="font-semibold text-[#131313]">
              Notification Settings
            </h2>
            <p class="text-sm text-[#697077]">
              Users can manage their notification preferences.
            </p>
          </div>
          <div class="cursor-pointer">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-[#131313] "
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </div>
        </Link>
        <div
          class="rounded-[12px] border px-4 py-2 flex justify-between items-center mb-[12px] cursor-pointer"
        >
          <div>
            <h2 class="font-semibold text-[#131313]">
              System Preferences
            </h2>
            <p class="text-sm text-[#697077]">
              Allows users to set their preferences for the system's appearance and behavior.
            </p>
          </div>
          <div class="cursor-pointer">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-[#131313] "
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </div>
        </div>
        <div
          class="rounded-[12px] border px-4 py-2 flex justify-between items-center mb-[12px] cursor-pointer"
        >
          <div>
            <h2 class="font-semibold text-[#131313]">
              Language Settings
            </h2>
            <p class="text-sm text-[#697077]">
              Allows users to select their preferred language.
            </p>
          </div>
          <div class="cursor-pointer">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-[#131313] "
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 5l7 7-7 7"
              />
            </svg>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
