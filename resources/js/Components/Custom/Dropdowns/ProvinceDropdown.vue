<template>
  <div class="relative" ref="dropdownContainer">
    <button
      @click="toggleDropdown"
      class="inline-flex justify-start md:justify-center w-full py-2 text-sm text-gray-700 focus:outline-none"
    >
      <img :src="icon" class="h-5 w-auto mr-2" />
      <span class="text-[18px] md:text-base" :class="color">{{ selectedProvinceName }}</span>
      <svg
        :class="`${color} ml-1 h-6 w-6`"
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
        <!-- Default 'All' option -->
        <a
          href="#"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          role="menuitem"
          @click.prevent="changeSelection({ id: 'all', name: 'All' })"
        >
          All
        </a>
        <!-- Dropdown options -->
        <a
          v-for="province in provinces"
          :key="province.id"
          href="#"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          role="menuitem"
          @click.prevent="changeSelection(province)"
        >
          {{ province.name }}
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from "vue";
import { defineProps, defineEmits } from 'vue';
import axios from 'axios';

// Define props
const props = defineProps({
  color: String,
  icon: String,
  province: {
    type: [String, Number, Array], // Allow Array if you want to pass pre-loaded provinces
    default: null,
  }
});

// Define emits for sending data back to parent
const emit = defineEmits(['provinceChanged']);

// State variables
const dropdownOpen = ref(false);
const provinces = ref([]);
const selectedProvinceName = ref('Select Province');
const dropdownContainer = ref(null);  // Correctly defined dropdownContainer ref

// Function to toggle dropdown visibility
function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value;
}

// Function to handle selection change and emit event to parent
function changeSelection(province) {
  selectedProvinceName.value = province.name;
  emit('provinceChanged', { id: province.id, name: province.name });
  dropdownOpen.value = false;
}

// Fetch provinces, initially or based on province prop
async function fetchProvinces() {
  let url = '/public/sensor-locations';
  try {
    if (props.province == "") {
      const response = await axios.get(url);
      console.log(url);

      provinces.value = response.data;
    } else {
      provinces.value = props.province;  // Use preloaded provinces if passed as prop
    }

    // Set default province to 'All' if there's no province and provinces are available
    if (!props.province || props.province === 'all') {
      selectedProvinceName.value = 'All';
      emit('provinceChanged', { id: 'all', name: 'All' });
    }
  } catch (error) {
    console.error('Error fetching provinces:', error);
  }
}

// Watch for changes to the province prop and refetch provinces
watch(() => props.province, () => {
  fetchProvinces();
});

// Close the dropdown when clicking outside
function closeDropdown(event) {
  if (dropdownContainer.value && !dropdownContainer.value.contains(event.target)) {
    dropdownOpen.value = false;
  }
}

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', closeDropdown);
  fetchProvinces(); // Fetch initial data on mount
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown);
});
</script>
