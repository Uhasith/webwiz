<template>
  <div class="relative" ref="dropdownContainer">
    <button
      @click="toggleDropdown"
      class="inline-flex w-full dropdowncustom custom_border_color text-gray-700 rounded-full border"
      :class="customClass"
    >
      <span class="flex items-center">
        <span class="pr-1 font-semibold text-[#13131380]"
          >{{ $t("Equipment Type") }}:</span
        >
        <span class="font-semibold text-[#016553]">{{
          selectedType.name
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
      class="origin-top-right absolute z-[2001] mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="menu-button"
      :class="sideClass"
    >
      <div class="py-1" role="none">
        <!-- Dropdown options -->
        <label
          v-for="type in allEquipmentTypes"
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
import { ref, onMounted, onUnmounted, computed } from "vue";
import axios from "axios";
import { useStore } from "vuex";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const store = useStore();

const props = defineProps({
  side: {
    type: String,
    default: "right",
  },
  customClass: {
    type: String,
    default: "",
  },
  equipmentTypes: Object,
});

const sideClass = computed(() => {
  return props.side === "left" ? "left-0" : "right-0";
});

const types = ref([{ id: "all", name: "All" }]);
const dropdownOpen = ref(false);
const dropdownContainer = ref(null);
// const selectedType = ref(types.value[0]);

const allEquipmentTypes = computed(() => {
  const defaultOption = { id: "all", name: "All" };
  return [defaultOption, ...props.equipmentTypes.data];
});

const selectedType = ref(allEquipmentTypes.value[0]);

const emit = defineEmits(['typeChanged']);

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value;
}

function changeSelection(type) {
  selectedType.value = type;
  dropdownOpen.value = false;
  emit('typeChanged', type);
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
  // fetchEquipmentTypes();
  document.addEventListener("click", closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener("click", closeDropdown);
});

</script>