<script setup>
import { ref, onMounted, onUnmounted, defineEmits } from 'vue';
import EditSensorData from '../Models/EditSensorData.vue';
import SuccessAlert from '@/Components/SuccessAlert.vue';
import ErrorAlert from '@/Components/ErrorAlert.vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Button } from '@/Components/ui/button';
import { reset } from '@/Utils/Alert';
import { useGlobalStore } from '@/store/global';
import axios from 'axios';
import { toast } from 'vue-sonner';

const globalStore = useGlobalStore();

const props = defineProps({
  itemId: {
    required: true
  },
  status: {
    required: true,
  }
});

const emit = defineEmits(['dataDeleted', 'dataNotDeleted', 'dataUpdated']);

const isOpen = ref(false);
const showConfirmDelete = ref(false);
const selectedSensor = ref(null);
const selectedSensorId = ref(props.itemId);
const status = ref(props.status);
const selectedStatus = ref(null);
const showEditSensorModal = ref(false);
const { successMessage, errorMessage, resetMessages } = reset();

const handleEditSensorData = async (sensorId) => {
  try {
    console.log("sensor id: ", sensorId);
    const response = await axios.get(`/admin/data-management/${sensorId}/edit`);
    selectedSensor.value = response.data;
    console.log("response: ", response.data);
    showEditSensorModal.value = true;
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

const editSensorData = () => {
  handleEditSensorData(props.itemId)
};

const statusChangeConfirm = (status) => {
  showConfirmDelete.value = true;
  selectedStatus.value = status;
};

const confirmStatusUpdate = () => {
  showConfirmDelete.value = false;
  statusUpdate(selectedStatus.value);
};

const statusUpdate = async (changedStatus) => {
  const data = {
    status: changedStatus,
    sensor_data_id: selectedSensorId.value
  };

  await axios.put(route('sensor.data.status.update'), data)
    .then(response => {
      console.log(response.data);
      globalStore.refreshTableAction();
      toast.success('Sensor Data Status Updated', {
        description: 'Sensor data status updated successfully.',
      });
    })
    .catch(error => {
      console.error(error);
      toast.error('Failed to update sensor data status', {
        description: 'Please contact support if this error persists.',
      });
    });
}

const deleteData = async () => {
  try {
    const response = await axios.delete(`/admin/data-management/${props.itemId}`);
    console.log('Data deleted successfully:', response.data);
    emit('dataDeleted');
    successMessage.value = "The data has been successfully removed."
    resetMessages();

  } catch (error) {
    console.log(error);
    errorMessage.value = "Something went wrong, the item could not be removed."
    resetMessages();

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
        <button @click="editSensorData()"
          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
          Edit Data
        </button>
        <button v-if="status !== 'ACTIVE'" @click="statusChangeConfirm('ACTIVE')"
          class="block w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-100 hover:text-green-900">Activate</button>
        <button v-else @click="statusChangeConfirm('INACTIVE')"
          class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-100 hover:text-red-900">Inactivate</button>
        <button @click="deleteData()"
          class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-100 hover:text-red-900">Archive
          Data</button>
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
        <Button @click="confirmStatusUpdate()">
          Yes
        </Button>
      </div>
    </DialogContent>
  </Dialog>
  <EditSensorData v-model:show="showEditSensorModal" v-model:selectedSensor="selectedSensor"
    v-model:selectedSensorId="selectedSensorId" @dataUpdated="globalStore.refreshTableAction()" />
</template>
