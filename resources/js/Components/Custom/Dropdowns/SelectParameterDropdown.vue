<template>
  <div class="relative" ref="dropdownContainer">
    <button
      @click="toggleDropdown"
      class="inline-flex justify-center w-full dropdowncustom custom_border_color font-medium
      rounded-full border hover:border-gray-400 focus:outline-none focus:border-gray-400"
    >
      <span class="flex items-center">
        <span class="pr-1 font-semibold text-[#13131380] text-left">{{ selectedParameter || $t('Select Parameter') }}</span>
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
      class="origin-top-right z-[2001] absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="menu-button"
    >
      <div class="py-1" role="none">
        <!-- Dropdown options -->
        <a
          v-for="(value, key) in pollutantParameters"
          :key="key"
          href="#"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          role="menuitem"
          @click.prevent="changeSelection(key, value)"
        >
          {{ value }}
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { usePage } from '@inertiajs/vue3';
import {useStore} from "vuex";

const page = usePage();
const store = useStore();
const pollutantParameters = ref({
  ...page.props.pollutantParameters
});
const props = defineProps({
    ui: String
});

const dropdownOpen = ref(false);
const dropdownContainer = ref(null);
const selectedParameter = ref('');

const emit = defineEmits(['selectionChanged']);

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value;
}

function changeSelection(key, parameter) {
  selectedParameter.value = parameter;
  dropdownOpen.value = false;
  emit('selectionChanged', { key: key, parameter: parameter });
    if (props.ui === "historical") {
        store.dispatch('pollutantParamChangedAction',parameter);
    }
    if (props.ui === "comparative") {
        store.dispatch('comparativePollutantParamChangedAction',parameter);
    }
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
