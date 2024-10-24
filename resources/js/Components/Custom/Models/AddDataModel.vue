<template>
  <Transition name="modal">
    <div
      v-if="show"
      class="fixed z-10 inset-0 overflow-y-auto"
      aria-labelledby="modal-title"
      role="dialog"
      aria-modal="true"
    >
      <div
        class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0"
      >
        <div
          class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
          aria-hidden="true"
          @click="$emit('close')"
        ></div>

        <span
          class="hidden sm:inline-block sm:align-middle sm:h-screen"
          aria-hidden="true"
          >&#8203;</span
        >

        <div
          class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full"
        >
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium leading-6" id="modal-title">
                {{ currentStep !== 4 ? "Add New Data Form" : "Preview Data" }}
              </h3>
              <button
                @click="$emit('close')"
                class="text-gray-400 hover:text-gray-500"
              >
                <span class="sr-only">Close</span>
                <svg
                  class="h-6 w-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"
                  />
                </svg>
              </button>
            </div>

            <!-- Step Indicators -->
            <div class="flex items-center mb-4" v-if="currentStep !== 4">
              <template v-for="(step, index) in totalSteps - 1" :key="step">
                <div
                  class="flex items-center flex-grow"
                  :class="{
                    flex_left: step === 1 || step === 2,
                    flex_right: step === 3,
                  }"
                >
                  <div
                    class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-medium"
                    :class="{
                      'bg-green1 text-white': step <= currentStep,
                      'bg-gray-200 text-gray-500': step > currentStep,
                    }"
                  >
                    {{ step }}
                  </div>
                  <div
                    v-if="index < totalSteps - 1"
                    class="flex-grow mx-2 h-1 relative"
                  >
                    <div class="absolute inset-0 bg-gray-300"></div>
                    <div
                      class="absolute inset-0 bg-green1 transition-all duration-300 ease-in-out"
                      :class="{ 'delay-300': step === currentStep }"
                      :style="{ width: getProgressWidth(step) }"
                    ></div>
                  </div>
                </div>
              </template>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submitForm">
              <div v-if="currentStep === 1">
                <h4 class="mb-2 text-lg font-semibold">
                  Step 1 - Add Location Details
                </h4>
                <!-- Add your Step 1 form fields here -->
                <div>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                      <label
                        for="date"
                        class="block text-sm font-medium text-gray-700"
                        >Date</label
                      >
                      <input
                        type="date"
                        id="date"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-full"
                        value="2025-12-25"
                      />
                    </div>

                    <div class="mb-4">
                      <label
                        for="time"
                        class="block text-sm font-medium text-gray-700"
                        >Time</label
                      >
                      <input
                        type="time"
                        id="time"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-full"
                        value="11:30"
                      />
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="country"
                      class="block text-sm font-medium text-gray-700"
                      >Country</label
                    >
                    <select
                      id="country"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-full"
                    >
                      <option>Sri Lanka</option>
                    </select>
                  </div>

                  <div class="mb-4">
                    <label
                      for="state"
                      class="block text-sm font-medium text-gray-700"
                      >State</label
                    >
                    <select
                      id="state"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-full"
                    >
                      <option>Select...</option>
                    </select>
                  </div>

                  <div class="mb-4">
                    <label
                      for="district"
                      class="block text-sm font-medium text-gray-700"
                      >District</label
                    >
                    <select
                      id="district"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-full"
                    >
                      <option>Select...</option>
                    </select>
                  </div>

                  <div class="mb-4">
                    <label
                      for="city"
                      class="block text-sm font-medium text-gray-700"
                      >City</label
                    >
                    <select
                      id="city"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-full"
                    >
                      <option>Select...</option>
                    </select>
                  </div>
                </div>
              </div>
              <div v-else-if="currentStep === 2">
                <h4 class="mb-2 text-lg font-semibold">
                  Step 2 - Add Equipment Details
                </h4>

                <div>
                  <div class="mb-4">
                    <label
                      for="role"
                      class="block text-sm font-medium text-gray-700"
                      >Equipement Type</label
                    >
                    <select
                      id="role"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-3xl"
                    >
                      <option>Select...</option>
                    </select>
                  </div>

                  <div class="mb-4">
                    <label
                      for="role"
                      class="block text-sm font-medium text-gray-700"
                      >Equipement Name</label
                    >
                    <select
                      id="role"
                      class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-3xl"
                    >
                      <option>Select...</option>
                    </select>
                  </div>
                </div>
                <!-- Add your Step 2 form fields here -->
              </div>
              <div v-else-if="currentStep === 3">
                <h4 class="mb-2 text-lg font-semibold">
                  Step 3 - Add Air Pollutant Parameters
                </h4>
                <!-- Add your Step 3 form fields or review content here -->

                <div>
                  <div class="mb-4">
                    <label
                      for="pm25"
                      class="block text-sm font-medium text-gray-700"
                      >PM2.5</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="pm25"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >µg/m³</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="pm10"
                      class="block text-sm font-medium text-gray-700"
                      >PM10</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="pm10"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >µg/m³</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="o3"
                      class="block text-sm font-medium text-gray-700"
                      >O3</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="o3"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppb</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="co"
                      class="block text-sm font-medium text-gray-700"
                      >CO</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="co"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppb</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="no2"
                      class="block text-sm font-medium text-gray-700"
                      >NO2</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="no2"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppb</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="so2"
                      class="block text-sm font-medium text-gray-700"
                      >SO2</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="so2"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppb</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="voc"
                      class="block text-sm font-medium text-gray-700"
                      >VOC</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="voc"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppb</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="co2"
                      class="block text-sm font-medium text-gray-700"
                      >CO2</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="co2"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppb</span
                      >
                    </div>
                  </div>
                </div>
              </div>
              <div v-else-if="currentStep === 4">
                <div class="grid grid-cols-3 gap-4 bg-gray-100 p-4 rounded-lg">
                  <div class="flex flex-col">
                    <span class="font-bold">Pannipitiya</span>
                    <span class="text-sm text-gray-500">Colombo</span>
                  </div>
                  <div class="flex flex-col">
                    <span class="font-bold">Equipment Type</span>
                    <span class="text-sm text-gray-500">Name</span>
                  </div>
                  <div class="flex flex-col">
                    <span class="font-bold">25 Oct 2024</span>
                    <span class="text-sm text-gray-500">08:56 AM</span>
                  </div>
                </div>

                <div class="mt-4">
                  <div class="mb-4">
                    <label
                      for="pm25"
                      class="block text-sm font-medium text-gray-700"
                      >PM2.5</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="pm25"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >µg/m³</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="pm10"
                      class="block text-sm font-medium text-gray-700"
                      >PM10</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="pm10"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >µg/m³</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="o3"
                      class="block text-sm font-medium text-gray-700"
                      >O3</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="o3"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppb</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="co"
                      class="block text-sm font-medium text-gray-700"
                      >CO</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="co"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppm</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="no2"
                      class="block text-sm font-medium text-gray-700"
                      >NO2</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="no2"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppb</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="so2"
                      class="block text-sm font-medium text-gray-700"
                      >SO2</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="so2"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppb</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="voc"
                      class="block text-sm font-medium text-gray-700"
                      >VOC</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="voc"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppm</span
                      >
                    </div>
                  </div>

                  <div class="mb-4">
                    <label
                      for="co2"
                      class="block text-sm font-medium text-gray-700"
                      >CO2</label
                    >
                    <div class="relative">
                      <input
                        type="text"
                        id="co2"
                        placeholder="Enter..."
                        class="w-full pl-4 pr-16 py-2 text-base border border-gray-300 rounded-full focus:outline-none sm:text-sm placeholder-gray-400"
                      />
                      <span
                        class="absolute inset-y-0 right-4 flex items-center text-gray-500"
                        >ppm</span
                      >
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>

          <!-- Navigation Buttons -->
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              v-if="currentStep < totalSteps"
              @click="nextStep"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Next
            </button>
            <button
              v-if="currentStep === totalSteps"
              @click="submitForm"
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Submit
            </button>
            <button
              v-if="currentStep > 1"
              @click="previousStep"
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Back
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from "vue";

const currentStep = ref(1);
const totalSteps = 4;

const getProgressWidth = (step) => {
  if (step < currentStep.value) return "100%";
  if (step === currentStep.value) return "50%";
  return "0%";
};

const nextStep = () => {
  if (currentStep.value < totalSteps) {
    currentStep.value++;
  }
};

const previousStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--;
  }
};

const submitForm = () => {
  // Handle form submission logic here
  console.log("Form submitted");
};

const props = defineProps({
  show: Boolean,
});

const emit = defineEmits(["close"]);
</script>

<style scoped>
.delay-300 {
  transition-delay: 300ms;
}

.flex_left {
  flex: 1 !important;
}

.flex_right {
  flex: 0 0 auto !important;
}
</style>
