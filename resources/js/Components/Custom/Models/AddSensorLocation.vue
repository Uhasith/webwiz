<template>
    <Transition name="modal">
      <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>
  
          <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
  
          <div  style="width: 741px; height: 968px;" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
            <div class="bg-white p-8 sm:p-6 sm:pb-4">
              <div class="flex justify-between items-center mb-12">
                <h3 class="text-2xl font-semibold leading-6" id="modal-title">
                  Assign New Location Form
                </h3>
                <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
                  <span class="sr-only">Close</span>
                  <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
              <form @submit.prevent="submitForm">
                <div class="mb-6">
                  <label for="organization" class="block text-base font-medium text-gray-700">Select Organization</label>
                  <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-3xl">
                    <option value="" disabled selected>Select</option>              
                  </select>
                </div>
                <div class="mb-6">
                  <label for="equipment" class="block text-base font-medium text-gray-700">Select Equipment</label>
                  <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-3xl" >
                    <option value="" disabled selected>Select</option>  
                  </select>
                </div>
                <div class="mb-6">
                  <label for="province" class="block text-base font-medium text-gray-700">Select Province</label>
                  <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-3xl">
                    <option value="" disabled selected>Select</option>  
                  </select>
                </div>
                <div class="mb-6">
                  <label for="district" class="block text-base font-medium text-gray-700">Select District</label>
                  <select class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-3xl">
                    <option value="" disabled selected>Select</option>  
                  </select>
                </div>
                <div class="mb-6">
                  <label for="location" class="block text-base font-medium text-gray-700">Location Name</label>
                  <input v-model="form.location" type="text" id="location" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl  text-gray-500" placeholder="Search Location">
                </div>
                <div class="mb-6">
                  <label for="longitudes" class="block text-base font-medium text-gray-700">Longitudes</label>
                  <input v-model="form.longitudes" type="text" id="longitudes" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter...">
                </div>
                <div class="mb-6">
                  <label for="latitudes" class="block text-base font-medium text-gray-700">Latitudes</label>
                  <input v-model="form.latitudes" type="text" id="latitudes" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter...">
                </div>
                <div class="mb-6">
                  <label for="locationKey" class="block text-base font-medium text-gray-700">Location Key</label>
                  <input v-model="form.locationKey" type="text" id="locationKey" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter...">
                </div>
              </form>

              <div class="mt-20 sm:flex sm:flex-row-reverse">
                <button @click="submitForm" type="button" style="width:139px; height: 40px;" class="inline-flex items-center justify-center rounded-3xl border border-transparent shadow-sm bg-green1 text-base font-medium text-white hover:bg-green1 sm:ml-3 sm:w-auto sm:text-sm">
                 Add
                </button>
            </div>
            </div>
           
          </div>
        </div>
      </div>
    </Transition>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue';
  import { defineProps, defineEmits } from 'vue';
  
  const props = defineProps({
    show: Boolean
  })
  
  const emit = defineEmits(['close'])
    
 
  
  const form = ref({
  
  });
  
  const roles = ref([]);
  
  onMounted(async () => {
   
  });
  
  const submitForm = () => {
  
  };
  </script>
  
  <style scoped>
  .modal-enter-active,
  .modal-leave-active {
    transition: opacity 0.3s ease;
  }
  .modal-enter-from,
  .modal-leave-to {
    opacity: 0;
  }
  </style>
  