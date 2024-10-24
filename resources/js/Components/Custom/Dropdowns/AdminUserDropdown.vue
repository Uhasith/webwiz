<template>
  <div class="relative" ref="dropdownContainer">
    <div
      @click="toggleDropdown"
      class="flex items-center space-x-2 cursor-pointer"
    >
      <div class="relative">
        <div
          class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center"
        >
          <span class="text-lg font-semibold text-gray-700">KP</span>
        </div>
        <div
          class="absolute bottom-0 right-0 w-3 h-3 bg-red-500 rounded-full border-2 border-white"
        ></div>
      </div>
      <div class="flex flex-col">
        <span class="font-medium text-sm">Katie Pena</span>
        <span class="text-xs text-gray-500">Admin</span>
      </div>
    </div>

    <!-- Dropdown menu -->
    <div
      v-if="dropdownOpen"
      class="origin-top-right absolute z-[2001] right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="menu-button"
    >
      <div class="py-1" role="none">
        <!-- Dropdown options -->
        <Link
          :href="route('profile.show')"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
        >
          Profile
        </Link>

        <form @submit.prevent="logout">
          <DropdownLink as="button"> Log Out </DropdownLink>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { Link, router } from "@inertiajs/vue3";
import DropdownLink from '@/Components/DropdownLink.vue';

const dropdownOpen = ref(false);
const dropdownContainer = ref(null);

const logout = () => {
    router.post(route('logout-now'));
};

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value;
}

function closeDropdown(event) {
  if (dropdownContainer.value && !dropdownContainer.value.contains(event.target)) {
    dropdownOpen.value = false;
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown);
});
</script>
