<template>
  <div class="relative" ref="dropdownContainer">
    <button
      @click="toggleDropdown"
      class="bg-white inline-flex justify-between w-full pr-1 font-semibold dropdowncustom custom_border_color
       rounded-full border hover:border-gray-400 focus:outline-none focus:border-gray-400"
    >
      <span class="flex items-center">
        <span class="font-semibold text-[14px]">{{ cityLabel }}</span>
      </span>
      <svg
        class="-mr-1 ml-2 h-5 w-5 text-gray-600"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
          clip-rule="evenodd"
        />
      </svg>
    </button>

    <!-- Dropdown menu -->
    <div
      v-if="dropdownOpen"
      class="origin-top-right z-[2001] absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
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
          >Equipment 1</a
        >
        <a
          href="#"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          role="menuitem"
          @click.prevent="changeSelection('1')"
          >Equipment 2</a
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";

const dropdownOpen = ref(false);
const dropdownContainer = ref(null);

const props = defineProps({
  cityno: {
    type: String,
    default: "1",
  },
});

const cityLabel = computed(() => `Select City ${props.cityno}`);

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