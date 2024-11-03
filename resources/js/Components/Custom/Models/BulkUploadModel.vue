<script setup>
import EquipmentDropdown from "@/Components/Custom/Dropdowns/EquipmentDropdown.vue";
import MeasuredByDropdown from "@/Components/Custom/Dropdowns/MeasuredByDropdown.vue";
import ProvinceDropdown from "@/Components/Custom/Dropdowns/ProvinceDropdown.vue";
import axios from 'axios';
import {ref, onMounted, onUnmounted, computed} from 'vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Loader2 } from 'lucide-vue-next';
import {toast} from "vue-sonner";

const props = defineProps({
    equipmentTypes: Object,
    sensors: Object,
    province: Object
});

const typeFilteredProvince = ref("");
const SelectFilteredSensors = ref("");
const selectedEquipmentType = ref("");
const typeFilteredSensors = ref(props.sensors);
const selectProvince = ref("");
const isLoading = ref(false);
const showBulkUploadModal = defineModel('showBulkUploadModal');
const emit = defineEmits(['equipmentType']);
const closeModal = computed(() => store.state.closeModal);
const successMessage = ref("");
const errorMessage = ref("");
const selectedFile = ref(null)

onMounted(() => {
    document.addEventListener("click", closeDropdown);
});

onUnmounted(() => {
    document.removeEventListener("click", closeDropdown);

});
const isOpen = ref(false);

const toggleDropdown = () => {
    isOpen.value = !isOpen.value;
};

const closeDropdown = (event) => {
    if (!event.target.closest("#options-menu")) {
        isOpen.value = false;
    }
};

function handleFileUpload(event) {
    selectedFile.value = event.target.files[0]
}
async function submitFile() {
    if (!selectedFile.value) return
    isLoading.value = true

    if(SelectFilteredSensors.value === '' || SelectFilteredSensors.value === 'all' || selectProvince.value === 'all'){
        toast.error('Fields Error', {
            description: "Please select specific sensor and location",
        });
        isLoading.value = false;
        return;
    }
    try {
        const formData = new FormData()
        formData.append('file', selectedFile.value)
        formData.append('equipment_id', SelectFilteredSensors.value) // TODO:: need to replace with real data
        formData.append('location_id', selectProvince.value)  // TODO:: need to replace with real data

        await axios.post('/api/history_data/upload', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        })
        showBulkUploadModal.value = false
        toast.success('Success', {
            description: 'Historical data successfully restored',
        });
    } catch (error) {
        console.error('File upload failed:', error)
        toast.error('Failed to upload historical data', {
            description: error.response.data.message.toString(),
        });
    } finally {
        isLoading.value = false
    }
}

async function handleEquipmentTypeChange(newType) {
    selectedEquipmentType.value = newType.id;
    SelectFilteredSensors.value = "";
    typeFilteredProvince.value = "";
    if (selectedEquipmentType.value !== '') {
        await axios
            .get(`/sensors/${newType.id}`)
            .then((response) => {
                typeFilteredSensors.value = response.data;
                handleTypeChanged({'id':''})
            })
            .catch((error) => {
                console.error("Error fetching sensors:", error);
            });
    }
}

async function handleTypeChanged(newType) {
    SelectFilteredSensors.value = newType.id;
    let equipmentType = selectedEquipmentType.value;
    let sensorId = newType.id;
    if(SelectFilteredSensors.value === ''){
        sensorId = "all";
    }
    if(selectedEquipmentType.value === ''){
        equipmentType = "all";
    }
    await axios
        .get(`/admin/location/${equipmentType}/${sensorId}`)
        .then((response) => {
            typeFilteredProvince.value = response.data;
        })
        .catch((error) => {
            console.error("Error fetching sensors:", error);
        });

}

function handlerProvinceChanged(newType) {
    selectProvince.value = newType.id;
}
function close(){
    selectProvince.value = null;
    typeFilteredProvince.value = null;
    SelectFilteredSensors.value = null;
    selectedFile.value = null;
}

</script>

<template>
  <Dialog v-model:open="showBulkUploadModal">
    <DialogContent @closeModal="close"  class="sm:max-w-xl grid-rows-[auto_minmax(0,1fr)_auto] p-1 max-h-[100dvh]"
      @interactOutside="(e) => e.preventDefault()">
      <DialogHeader class="p-6 mb-2 pb-0">
        <DialogTitle>Upload Historical Data</DialogTitle>
      </DialogHeader>
        <div class="grid gap-4 py-4 overflow-y-auto px-6">
            <div class="flex flex-col gap-4 justify-between items-start">

                <!-- Labels and Dropdowns -->
                <label for="equipment-type" class="font-medium text-gray-700">Select Equipment Type:</label>
                <EquipmentDropdown id="equipment-type" side="left" :equipment-types="equipmentTypes" @type-changed="handleEquipmentTypeChange" />

                <label for="measured-by" class="font-medium text-gray-700">Select Sensor:</label>
                <MeasuredByDropdown id="measured-by" :sensors="typeFilteredSensors" @sensorChanged="handleTypeChanged" />

                <label for="province" class="font-medium text-gray-700">Select Location:</label>
                <ProvinceDropdown ui="bulk" id="province" :province="typeFilteredProvince" @provinceChanged="handlerProvinceChanged"
                                  color="text-green-700" icon="/images/greenpinicon.png" />

                <label for="file-upload" class="font-medium text-gray-700">Select a historical data file to upload:</label>
                <input  accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" type="file" id="file-upload" @change="handleFileUpload" class="mb-2 border border-gray-200 rounded-md p-2" />

                <p v-if="selectedFile" class="text-sm text-gray-500">Selected File: {{ selectedFile.name }}</p>
            </div>
        </div>
      <DialogFooter class="p-6 pt-0">
        <div class="px-4 py-3">
          <button type="button" @click="submitFile" :disabled="isLoading"
            class="ml-3 w-[139px] bg-green1 text-white rounded-3xl px-6 py-2"
            :class="{ 'opacity-50 cursor-not-allowed': isLoading }">
            <div class="items-center space-x-2">
              <Loader2 v-show="isLoading" class="w-4 h-4 mr-2 animate-spin" />
              Upload
            </div>
          </button>
        </div>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
