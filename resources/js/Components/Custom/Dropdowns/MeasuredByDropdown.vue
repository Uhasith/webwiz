<template>
    <div class="relative" ref="dropdownContainer">
        <button
            @click="toggleDropdown"
            class="inline-flex justify-center w-full dropdowncustom custom_border_color text-gray-700 rounded-full border hover:border-gray-400 focus:outline-none focus:border-gray-400"
            :class="customClass"
        >
      <span class="flex items-center">
        <span class="pr-1 font-semibold text-[#13131380] text-left">{{ $t('Measured By') }}:</span>
        <span class="font-semibold text-[#016553]">{{
                selectedSensor?.name
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
            class="origin-top-right z-[2001] absolute mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
            role="menu"
            aria-orientation="vertical"
            aria-labelledby="menu-button"
        >
            <div class="py-1" role="none">
                <!-- Dropdown options -->
                <label
                    v-for="type in allSensors"
                    :key="type.id"
                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 cursor-pointer"
                >
                    <input
                        type="checkbox"
                        :checked="selectedSensor.id === type.id"
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
import {ref, onMounted, onUnmounted, computed, watch} from "vue";
import axios from 'axios';
import {useStore} from 'vuex'
import {useI18n} from "vue-i18n";

const {t} = useI18n();

const store = useStore();

const props = defineProps({
    customClass: {
        type: String,
        default: '',
    },
    sensors: Object,
    ui: String
});


const dropdownOpen = ref(false);
const dropdownContainer = ref(null);


const allSensors = computed(() => {
    const defaultOption = {id: "all", name: "All"};
    if (props.ui === "chart" || props.ui === "pm-chart" || props.ui === "comparative-chart1" || props.ui === "comparative-chart2" || props.ui === "home-mobile" ) {
        return [...props.sensors];
    }
    return [defaultOption, ...props.sensors];
});

const selectedSensor = ref(allSensors.value[0]);

watch(allSensors, (newSensors) => {
    selectedSensor.value = newSensors[0];
    if (props.ui === "chart") {
        store.dispatch("updateSensorAction", selectedSensor.value);
    }
    if (props.ui === "pm-chart") {
        store.dispatch("updateComparativePollutantSensorAction", selectedSensor.value);
    }
    if (props.ui === "comparative-chart1") {
        store.dispatch("updateAnalysisPollutantSensor_1Action", selectedSensor.value);
    }
    if (props.ui === "comparative-chart2") {
        store.dispatch("updateAnalysisPollutantSensor_2Action", selectedSensor.value);
    }
});

function toggleDropdown() {
    dropdownOpen.value = !dropdownOpen.value;
}

function changeSelection(type) {
    selectedSensor.value = type;
    dropdownOpen.value = false;
    emit('sensorChanged', type);
    if (props.ui === "chart") {
        store.dispatch("updateSensorAction", selectedSensor.value);
    }
    if (props.ui === "pm-chart") {
        store.dispatch("updateComparativePollutantSensorAction", selectedSensor.value);
    }
    if (props.ui === "comparative-chart1") {
        store.dispatch("updateAnalysisPollutantSensor_1Action", selectedSensor.value);
    }
    if (props.ui === "comparative-chart2") {
        store.dispatch("updateAnalysisPollutantSensor_2Action", selectedSensor.value);
    }
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
    document.addEventListener("click", closeDropdown);
});

onUnmounted(() => {
    document.removeEventListener("click", closeDropdown);
});

const emit = defineEmits(['sensorChanged']);


</script>
