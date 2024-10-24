<!-- RowDropdown.vue -->
<template>
    <EditUserModel :show="showEditUserModal" :user="selectedUser" @close="showEditUserModal = false" />
  <div class="relative inline-block text-left">
    <div class="fixed top-4 right-4">  <SuccessAlert v-if="successMessage" :value="successMessage" /></div>
    <div  class="fixed top-4 right-4"> <ErrorAlert v-if=errorMessage :value="errorMessage" /></div>
    <div>
      <button
        @click="toggleDropdown"
        type="button"
        class="py-3 px-6"
        id="options-menu"
        aria-haspopup="true"
        :aria-expanded="isOpen.toString()"
      >
        &#8942;
      </button>
    </div>

    <div
      v-if="isOpen"
      class="font-medium origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
    >
      <div
        class="py-1"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="options-menu"
      >
        <a
          href="#"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
          role="menuitem"
        @click="editUser"
          >Edit Data</a
        >
        <a
          @click="deleteData()"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900"
          role="menuitem"
          >Delete Data</a
        >

        <!-- <a
          href="#"
          class="block px-4 py-2 text-sm text-green-600"
          role="menuitem"
          >Activate</a
        > -->
      </div>
    </div>
  </div>
</template>

<script setup>
import EditUserModel from "@/Components/Custom/Models/EditUserModel.vue";
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import SuccessAlert from "@/Components/SuccessAlert.vue";
import ErrorAlert from "@/Components/ErrorAlert.vue";
import { reset } from "@/Utils/Alert";

const selectedUser = ref(null);
const showEditUserModal = ref(false);
const { successMessage, errorMessage, resetMessages } = reset();

const handleEditUser = async (userId) => {
  try {
    console.log("user id: ", userId);
    const response = await axios.get(`/admin/user-management/${userId}/edit`);
    selectedUser.value = response.data;
    showEditUserModal.value = true;
  } catch (error) {
    console.error('Error fetching user data:', error);
  }
};

const props = defineProps({
  itemId: {
    type: Number,
    required: true
  }
});

// Declare the emits for the component
const emit = defineEmits(['dataDeleted', 'editUser','dataNotDeleted']);
const isOpen = ref(false);

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const closeDropdown = (event) => {
  if (!event.target.closest("#options-menu")) {
    isOpen.value = false;
  }
};

onMounted(() => {
  document.addEventListener("click", closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener("click", closeDropdown);
});

const deleteData = async () => {
  try {
    const response = await axios.delete(`/admin/user-management/${props.itemId}`);
    console.log('Data deleted successfully:', response.data);
    successMessage.value = "User was successfully removed"
    resetMessages();
    emit('dataDeleted');

  } catch (error) {
    console.error('Error deleting data:', error);
    errorMessage.value = "Something went wrong, user was not removed"
    resetMessages();

  }
};

const editUser = () => {
    emit('editUser', props.itemId);
    handleEditUser(props.itemId)
};
</script>
