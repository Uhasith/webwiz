<script setup>
import EditUserModel from "@/Components/Custom/Models/EditUserModel.vue";
import { ref, onMounted, onUnmounted, defineProps } from 'vue';
import axios from 'axios';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/Components/ui/dialog';
import { Button } from '@/Components/ui/button';
import SuccessAlert from "@/Components/SuccessAlert.vue";
import ErrorAlert from "@/Components/ErrorAlert.vue";
import { reset } from "@/Utils/Alert";
import { useGlobalStore } from '@/store/global';
import { toast } from 'vue-sonner';
import useAuth from "@/Utils/useAuth";

const globalStore = useGlobalStore();

const { hasPermission } = useAuth();

const props = defineProps({
  itemId: {
    type: Number,
    required: true
  },
  status: {
    type: String,
    required: true
  },
});

const isOpen = ref(false);
const showConfirmDelete = ref(false);
const selectedUser = ref(null);
const selectedUserId = ref(props.itemId);
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

const editUser = () => {
  handleEditUser(props.itemId)
};

const confirmUserDelete = () => {
  showConfirmDelete.value = true;
};

const confirmUserDeleteInBackend = async () => {
  deleteData();
}

const deleteData = async () => {
  try {
    const response = await axios.delete(route('user.delete', selectedUserId.value));
    globalStore.refreshTableAction();
    toast.success('User Deleted', {
      description: 'User deleted successfully.',
    });

  } catch (error) {
    console.error(error);
    toast.error('Failed to delete user', {
      description: 'Please contact support if this error persists.',
    });
  }
};
</script>

<template>
  <div class="relative inline-block text-left">
    <div class="fixed top-4 right-4">
      <SuccessAlert v-if="successMessage" :value="successMessage" />
    </div>
    <div class="fixed top-4 right-4">
      <ErrorAlert v-if=errorMessage :value="errorMessage" />
    </div>
    <div>
      <button @click="toggleDropdown" type="button" class="py-3 px-6" id="options-menu" aria-haspopup="true"
        :aria-expanded="isOpen.toString()">
        &#8942;
      </button>
    </div>
    <div v-if="isOpen"
      class="font-medium origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
      <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
        <button @click="editUser()" v-if="hasPermission('Update Sub Admin')"
          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
          Edit User
        </button>
        <button @click="confirmUserDelete()" v-if="hasPermission('Update Sub Admin')"
          class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-100 hover:text-red-900">Inactive
          User</button>
        <!-- <a
          href="#"
          class="block px-4 py-2 text-sm text-green-600"
          role="menuitem"
          >Activate</a
        > -->
      </div>
    </div>
  </div>
   <Dialog v-model:open="showConfirmDelete">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Are you sure?</DialogTitle>
        <DialogDescription> You will not be able to undo this action. </DialogDescription>
      </DialogHeader>
      <div class="flex items-center justify-end gap-4">
        <Button variant="secondary" @click="showConfirmDelete = false;">
          Cancel
        </Button>
        <Button @click="confirmUserDeleteInBackend()">
          Yes
        </Button>
      </div>
    </DialogContent>
  </Dialog>
  <EditUserModel v-model:showEditUserModal="showEditUserModal" v-model:selectedUser="selectedUser"
    v-model:selectedUserId="selectedUserId" @close="showEditUserModal = false" />
</template>
