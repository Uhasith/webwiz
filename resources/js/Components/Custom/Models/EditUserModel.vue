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
                Edit User
              </h3>
              <button @click="$emit('close')" class="text-gray-400 hover:text-gray-500">
                <span class="sr-only">Close</span>
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
            <form>
              <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700">Select Role</label>
                <select id="role" v-model="userData.role_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-3xl">
                    <option v-for="(key,role) in roles" :key="key" :value="key">{{ role }}</option>
                </select>
              </div>
              <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700">User Name</label>
              <input type="text" id="username" v-model="userData.name" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter username...">
              </div>
              <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" v-model="userData.email" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter email...">
              </div>
              <div class="mb-4">
                <label for="contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="tel" id="contact" v-model="userData.contact" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Enter contact number...">
            </div>
              <div class="mb-4">
                <label for="location" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="location" v-model="userData.status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none   sm:text-sm rounded-3xl">
                  <option value="ACTIVE">ACTIVE</option>
                  <option value="INACTIVE">INACTIVE</option>
                </select>
              </div>
            </form>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button"  @click="submitForm" class="w-full inline-flex justify-center rounded-3xl border border-transparent shadow-sm px-6 py-1 bg-green1 text-base font-medium text-white hover:bg-green1  sm:ml-3 sm:w-auto sm:text-sm">
              Save
            </button>
            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-3xl border border-gray-300 shadow-sm px-6 py-1 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2  sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" @click="$emit('close')">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
  </template>

<script setup>
import { defineProps, defineEmits } from 'vue';
import { onMounted, onUnmounted } from 'vue';
import { ref, watch } from 'vue';

const props = defineProps({
  show: Boolean,
  user: Object,
  roles: Array
});
const emit = defineEmits(['close', 'saveUser']);
const userData = ref({ ...props.user });
const roles = ref([]);

watch(() => props.user, (newValue) => {
  userData.value = { ...newValue };
  if (!userData.value.role_id && newValue.roles && newValue.roles.length) {
    userData.value.role_id = newValue.roles[0].id;
  }
});

const getRoles = async () => {
  const response = await axios.get('/admin/get-roles');
  return response.data;
};

onMounted(async () => {
    roles.value = await getRoles();
});

const submitForm = () => {
  updateUser();
  emit('close');

};

const updateUser = async () => {
  try {
    console.log("user data: ",  userData.value);
    const response = await axios.put(`/admin/user-management/${userData.value.id}`, userData.value);
    console.log('User updated successfully:', response.data);
    emit ('reloadTable');
  } catch (error) {
    console.error('Error updating user:', error);
  }
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
