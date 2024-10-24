<template>
  <div class="relative" ref="dropdownContainer">
    <button
      @click="toggleDropdown"
      class="inline-flex justify-center w-full dropdowncustom custom_border_color text-gray-700 rounded-full border hover:border-gray-400 focus:outline-none focus:border-gray-400"
      :class="customClass"
    >
      <span class="flex items-center">
        <span class="pr-1 font-semibold text-[#13131380]">{{ $t('Ranking') }}:</span>
        <span class="font-semibold text-[#016553]">{{ selectedRanking.name }}</span>
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
      class="z-[2000] origin-top-right z-[2001] absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="menu-button"
    >
      <div class="py-1" role="none">
        <!-- Dropdown options -->
        <label
          v-for="ranking in rankings"
          :key="ranking.id"
          class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer"
        >
          <input
            type="checkbox"
            :checked="selectedRanking.id === ranking.id"
            @change="changeSelection(ranking)"
            class="form-checkbox h-4 w-4 text-gray-500 border-gray-300 rounded mr-2 focus:ring-gray-500"
          />
          {{ ranking.name }}
        </label>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
  customClass: {
    type: String,
    default: '',
  },
});
const emit = defineEmits(['rankingChanged']);

const dropdownOpen = ref(false);
const dropdownContainer = ref(null);
const rankings = ref([
  { id: 'all', name: t('All') },
  { id: 'good', name: t('Good') },
  { id: 'moderate', name: t('Moderate') },
  { id: 'poor', name: t('Poor') },
  { id: 'unhealthy', name: t('Unhealthy') },
  { id: 'severe', name: t('Severe') },
  { id: 'hazardous', name: t('Hazardous') }
]);
const selectedRanking = ref(rankings.value[0]);

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value;
}

function changeSelection(ranking) {
  selectedRanking.value = ranking;
  dropdownOpen.value = false;
    emit('rankingChanged', ranking);

    // You can add any additional logic here, like updating a store
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
