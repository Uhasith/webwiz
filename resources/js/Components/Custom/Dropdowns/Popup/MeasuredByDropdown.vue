<template>
  <div class="relative" ref="dropdownContainer">
    <button
      @click="toggleDropdown"
      class="inline-flex justify-center w-full dropdowncustom custom_border_color text-gray-700 rounded-full border border-gray-200 hover:border-gray-400 focus:outline-none focus:border-gray-400"
    >
      <span class="flex items-center">
        <span class="pr-1 font-semibold text-[#13131380]">Measured By:</span>
        <span class="font-semibold text-[#016553]">{{
          selectedType?.name
        }}</span>
      </span>
      <svg
        class="-mr-1 ml-1 h-5 w-5 text-gray-600"
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
      class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="menu-button"
      style="z-index: 2"
    >
      <div class="py-1" role="none">
        <!-- Dropdown options -->
        <label
          v-for="type in types"
          :key="type.id"
          class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer"
        >
          <input
            type="checkbox"
            :checked="selectedType.id === type.id"
            @change="changeSelection(type)"
            class="form-checkbox h-4 w-4 text-gray-500 border-gray-300 rounded mr-2 focus:ring-gray-500"
          />
          {{ type.name }}
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from "vue";
import axios from 'axios';
import { useStore } from 'vuex'

const store = useStore();

const types = ref([
  // { id: "all", name: "All" }
]);
const dropdownOpen = ref(false);
const dropdownContainer = ref(null);
const selectedType = ref(types?.value[0]);

// Get the selected equipment type from the store
const selectedEquipmentType = computed(() => store.state.equipmentType);

// Watch for changes in the selected equipment type
watch(selectedEquipmentType, (newEquipmentType) => {
  fetchData(newEquipmentType.id);
});


async function fetchData(equipmentType = 'all') {
  try {
    let url = '/public/sensors';
    if (equipmentType !== 'all') {
      url += `?equipment_type=${equipmentType}`;
    }
    const response = await axios.get(url);
    types.value = response.data.data;
    selectedType.value = types.value[0]; // Set the first item as selected
  } catch (error) {
    console.error('Error fetching data:', error);
  }
}

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value;
}

function changeSelection(type) {
  selectedType.value = type;
  // updateSensor(type);
  dropdownOpen.value = false;
}

function closeDropdown(event) {
  if (
    dropdownContainer.value &&
    !dropdownContainer.value.contains(event.target)
  ) {
    dropdownOpen.value = false;
  }
}

onMounted(() => {
  fetchData();
  document.addEventListener("click", closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener("click", closeDropdown);
});

// const updateSensor = (type) => {
//   store.commit('updateSensor', type);
// };
</script>
