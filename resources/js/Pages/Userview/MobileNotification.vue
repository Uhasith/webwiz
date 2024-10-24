<template>
  <div class="mx-auto p-2 bg-white">
    <div class="flex justify-start mb-4">
      <Link href="/" class="text-gray-600">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M10 19l-7-7m0 0l7-7m-7 7h18"
          />
        </svg>
      </Link>
    </div>
      <div style="height: 650px;overflow-y: auto;">
          <Notification />
      </div>
  </div>
</template>
<script setup>
import Notification from "@/Components/Custom/Notification.vue";
import { Link } from "@inertiajs/vue3";
</script>
