<template>
  <Transition name="modal">
    <div v-if="show" class="fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="$emit('close')"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-medium leading-6" id="modal-title">
                Add New User Form
              </h3>
              <button  @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Close</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <form @submit.prevent="submitForm">
              <div class="mb-4">
                <label for="organization" class="block text-sm font-medium text-gray-700">Select Organization</label>
                <select v-model="form.organization_id" id="organization" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-3xl">
                  <option v-for="organization in organizations" :key="organization.id" :value="organization.id">{{ organization.name }}</option>
                </select>
              </div>
              <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Select Role</label>
                <select v-model="form.role_id" id="role" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-3xl">
                  <option v-for="(key,role) in roles" :key="role" :value="role">{{ key }}</option>
                </select>
              </div>
              <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">User Name</label>
                <input v-model="form.username" type="text" id="username" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter...">
              </div>
              <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input v-model="form.email" type="email" id="email" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter...">
              </div>
              <div class="mb-4">
                <label for="contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input v-model="form.contact" type="tel" id="contact" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter...">
              </div>
            </form>
          </div>
          <div v-if=errorMessage  class="p-5 flex items-center justify-center">
          <ErrorAlert v-if="errorMessage" :value="errorMessage" />
         </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button @click="submitForm" type="button" class="w-full inline-flex justify-center rounded-3xl border border-transparent shadow-sm px-6 py-1 bg-green1 text-base font-medium text-white hover:bg-green1 sm:ml-3 sm:w-auto sm:text-sm">
              Add
            </button>
            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-3xl border border-gray-300 shadow-sm px-6 py-1 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="$emit('close')">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import axios from 'axios';
import { ref, onMounted } from 'vue';
import { defineProps, defineEmits } from 'vue';
import ErrorAlert from '@/Components/ErrorAlert.vue';
import { reset } from '@/Utils/Alert';

const props = defineProps({
  show: Boolean
})

const roles = defineModel('roles');
const emit = defineEmits(['close'])
const { successMessage, errorMessage, resetMessages } = reset();

const getOrgnizations = async () => {
  const response = await axios.get('/admin/get-organization');
  return response.data;
}

const saveUser = async (formData) => {
  try {
    console.log(formData);
    const response = await axios.post('/admin/save-users', formData);
    successMessage.value='User was added successfully!';
    emit('close', successMessage);
    form.value=getInitialData();
    resetMessages()

  } catch (error) {
    console.error(error);
    errorMessage.value = "The user already exists!"
  }
};

const form = ref({
  organization_id:'',
  username: '',
  email: '',
  contact: '',
  role_id: null
});
const getInitialData = () => ({ organization_id: "", username: "", email: "",contact: "",role_id:null  });

// const roles = ref([]);
const organizations = ref([]);

onMounted(async () => {
  // roles.value = await getRoles(); 
  organizations.value = await getOrgnizations();
});

const submitForm = async () => {
  saveUser(form.value);
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
