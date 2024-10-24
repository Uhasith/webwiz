<template>
  <div class="relative" ref="dropdownContainer">
    <button
      @click="toggleDropdown"
      class="inline-flex justify-center dropdowncustom custom_border_color text-gray-700 rounded-full border hover:border-gray-400 focus:outline-none focus:border-gray-400"
    >
      <span class="flex items-center">
        <span class="pr-1 font-semibold text-[#13131380]">{{ $t('Time Period') }}:</span>
        <span class="font-semibold text-[#016553]">Jan - Dec 2024</span>
      </span>

      <img src="/images/Calendar.png" class="ml-1 h-4 w-auto mr-2" />
    </button>

    <!-- Dropdown menu -->
    <div
      v-if="dropdownOpen"
      class="z-10 z-[2001] origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="menu-button"
    >
      <div class="py-1" role="none">
        <!-- Dropdown options -->
        <a
          href="#"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          role="menuitem"
          @click.prevent="changeSelection('all')"
          >Province 1</a
        >
        <a
          href="#"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          role="menuitem"
          @click.prevent="changeSelection('all')"
          >Province 2</a
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";

const dropdownOpen = ref(false);
const dropdownContainer = ref(null);

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value;
}

function changeSelection(){
  dropdownOpen.value = false;
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