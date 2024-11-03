<script setup>
import { computed, watch } from 'vue';
import moment from 'moment';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { toast } from 'vue-sonner';

const emit = defineEmits(['dataUpdated']);
const show = defineModel('show');
const selectedSensor = defineModel('selectedSensor');
const selectedSensorId = defineModel('selectedSensorId');

const formattedDate = computed(() => {
  if (!selectedSensor.value || !selectedSensor.value.created_at) {
    return '';
  }
  return moment(selectedSensor.value.created_at).format('DD MMM YYYY'); // Example: "25 Oct 2024"
});

const formattedTime = computed(() => {
  if (!selectedSensor.value || !selectedSensor.value.created_at) {
    return '';
  }
  return moment(selectedSensor.value.created_at).format('hh:mm A'); // Example: "08:56 AM"
});

// Define the validation schema using yup
const { errors, handleSubmit, defineField, setValues } = useForm({
  validationSchema: yup.object({
    data: yup.object().nullable(),

    pm2_5: yup
      .number()
      .transform((value, originalValue) => (originalValue === '' ? null : value))
      .when('data.pm2_5', {
        is: (val) => val !== null, // If data.pm2_5 is not null, require the field
        then: (schema) => schema.required('PM2.5 is required').min(0, 'Invalid value for PM2.5'),
        otherwise: (schema) => schema.nullable(), // Otherwise, it can be nullable
      })
      .typeError('Value must be a number'),

    pm10: yup
      .number()
      .transform((value, originalValue) => (originalValue === '' ? null : value))
      .when('data.pm10', {
        is: (val) => val !== null,
        then: (schema) => schema.required('PM10 is required').min(0, 'Invalid value for PM10'),
        otherwise: (schema) => schema.nullable(),
      })
      .typeError('Value must be a number'),

    o3: yup
      .number()
      .transform((value, originalValue) => (originalValue === '' ? null : value))
      .when('data.o3', {
        is: (val) => val !== null,
        then: (schema) => schema.required('O3 is required').min(0, 'Invalid value for O3'),
        otherwise: (schema) => schema.nullable(),
      })
      .typeError('Value must be a number'),

    co: yup
      .number()
      .transform((value, originalValue) => (originalValue === '' ? null : value))
      .when('data.co', {
        is: (val) => val !== null,
        then: (schema) => schema.required('CO is required').min(0, 'Invalid value for CO'),
        otherwise: (schema) => schema.nullable(),
      })
      .typeError('Value must be a number'),

    no2: yup
      .number()
      .transform((value, originalValue) => (originalValue === '' ? null : value))
      .when('data.no2', {
        is: (val) => val !== null,
        then: (schema) => schema.required('NO2 is required').min(0, 'Invalid value for NO2'),
        otherwise: (schema) => schema.nullable(),
      })
      .typeError('Value must be a number'),

    so2: yup
      .number()
      .transform((value, originalValue) => (originalValue === '' ? null : value))
      .when('data.so2', {
        is: (val) => val !== null,
        then: (schema) => schema.required('SO2 is required').min(0, 'Invalid value for SO2'),
        otherwise: (schema) => schema.nullable(),
      })
      .typeError('Value must be a number'),

    co2: yup
      .number()
      .transform((value, originalValue) => (originalValue === '' ? null : value))
      .when('data.co2', {
        is: (val) => val !== null,
        then: (schema) => schema.required('CO2 is required').min(0, 'Invalid value for CO2'),
        otherwise: (schema) => schema.nullable(),
      })
      .typeError('Value must be a number'),
  }),

  initialValues: {
    pm2_5: selectedSensor.value?.pm2_5 || null,
    pm10: selectedSensor.value?.pm10 || null,
    o3: selectedSensor.value?.o3 || null,
    co: selectedSensor.value?.co || null,
    no2: selectedSensor.value?.no2 || null,
    so2: selectedSensor.value?.so2 || null,
    co2: selectedSensor.value?.co2 || null,
    sensor_location_id : selectedSensor.value?.sensor_location_id || null,
    data: selectedSensor.value, // Pass the whole data object as initial value
  },
});

// Watch the selectedSensor value and update the form values
watch(selectedSensor, (newValue) => {
  setValues({
    pm2_5: newValue?.pm2_5 || null,
    pm10: newValue?.pm10 || null,
    o3: newValue?.o3 || null,
    co: newValue?.co || null,
    no2: newValue?.no2 || null,
    so2: newValue?.so2 || null,
    co2: newValue?.co2 || null,
    sensor_location_id: selectedSensor.value?.sensor_location_id || null,
    data: newValue,
  });
});

const [pm2_5, pm2_5Attrs] = defineField('pm2_5');
const [pm10, pm10Attrs] = defineField('pm10');
const [o3, o3Attrs] = defineField('o3');
const [co, coAttrs] = defineField('co');
const [no2, no2Attrs] = defineField('no2');
const [so2, so2Attrs] = defineField('so2');
const [co2, co2Attrs] = defineField('co2');

// Handle form submission
const onSubmit = handleSubmit((values) => {
  console.log(values);
  console.log(selectedSensorId.value);

  const data = {
    pm2_5: values.pm2_5,
    pm10: values.pm10,
    o3: values.o3,
    co: values.co,
    no2: values.no2,
    so2: values.so2,
    co2: values.co2,
    sensor_data_id: selectedSensorId.value,
    sensor_location_id: values.sensor_location_id
  };

  axios.put(route('sensor.data.update'), data)
    .then(response => {
      console.log(response.data);
      emit('dataUpdated');
      toast.success('Sensor Data Updated', {
        description: 'Sensor data updated successfully.',
      });
      show.value = false;
    })
    .catch(error => {
      console.error(error);
      toast.error('Failed to update sensor data', {
        description: 'Please contact support if this error persists.',
      });
    });
});
</script>

<template>
  <Dialog v-model:open="show">
    <DialogContent class="sm:max-w-xl grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[80dvh]"
      @interactOutside="(e) => e.preventDefault()">
      <DialogHeader class="p-6 pb-0">
        <DialogTitle>Edit Data</DialogTitle>
      </DialogHeader>
      <div class="grid gap-4 py-4 overflow-y-auto px-6">
        <div class="mb-6">
          <div style="background-color: #F7FBF9;" class="flex justify-between items-center px-4 py-2 rounded-lg">
            <div>
              <p class=" text-gray-800 text-base font-medium">
                {{ selectedSensor?.sensor_location?.location?.district?.district_name }}
              </p>
              <p class="text-base font-medium text-gray-500">
                {{ selectedSensor?.sensor_location?.location?.name }}
              </p>
            </div>
            <div>
              <p class="text-base text-gray-800 font-medium">Equipment Type</p>
              <p class="text-base font-medium text-gray-500">{{
                selectedSensor?.sensor_location?.sensor?.equipment_type?.type_name }}</p>
            </div>
            <div>
              <p class="text-base text-gray-800 font-medium">{{ formattedDate }}</p>
              <p class="text-base font-medium text-gray-500">{{ formattedTime }}</p>
            </div>
          </div>
        </div>
        <div class="flex flex-col justify-between">
          <form id="edit-sensor" @submit="onSubmit">
            <div class="space-y-4">
              <!-- PM2.5 Field -->
              <div class="mb-6">
                <label for="pm2_5" class="block text-base font-medium text-gray-800">PM2.5</label>
                <div class="relative mt-1">
                  <input v-model="pm2_5" v-bind="pm2_5Attrs" type="number" id="pm2_5"
                    class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl pr-16 px-2" />
                  <span
                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none">µg/m³</span>
                </div>
                <span v-if="errors.pm2_5" class="text-red-500 text-sm">{{ errors.pm2_5 }}</span>
              </div>

              <!-- PM10 Field -->
              <div class="mb-6">
                <label for="pm10" class="block text-base font-medium text-gray-800">PM10</label>
                <div class="relative mt-1">
                  <input v-model="pm10" v-bind="pm10Attrs" type="number" id="pm10"
                    class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl pr-16 px-2" />
                  <span
                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none">µg/m³</span>
                </div>
                <span v-if="errors.pm10" class="text-red-500 text-sm">{{ errors.pm10 }}</span>
              </div>

              <!-- O3 Field -->
              <div class="mb-6">
                <label for="o3" class="block text-base font-medium text-gray-800">O3</label>
                <div class="relative mt-1">
                  <input v-model="o3" v-bind="o3Attrs" type="number" id="o3"
                    class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl pr-16 px-2" />
                  <span
                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none">ppb</span>
                </div>
                <span v-if="errors.o3" class="text-red-500 text-sm">{{ errors.o3 }}</span>
              </div>

              <!-- CO Field -->
              <div class="mb-6">
                <label for="co" class="block text-base font-medium text-gray-800">CO</label>
                <div class="relative mt-1">
                  <input v-model="co" v-bind="coAttrs" type="number" id="co"
                    class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl pr-16 px-2" />
                  <span
                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none">ppm</span>
                </div>
                <span v-if="errors.co" class="text-red-500 text-sm">{{ errors.co }}</span>
              </div>

              <!-- NO2 Field -->
              <div class="mb-6">
                <label for="no2" class="block text-base font-medium text-gray-800">NO2</label>
                <div class="relative mt-1">
                  <input v-model="no2" v-bind="no2Attrs" type="number" id="no2"
                    class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl pr-16 px-2" />
                  <span
                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none">ppb</span>
                </div>
                <span v-if="errors.no2" class="text-red-500 text-sm">{{ errors.no2 }}</span>
              </div>

              <!-- SO2 Field -->
              <div class="mb-6">
                <label for="so2" class="block text-base font-medium text-gray-800">SO2</label>
                <div class="relative mt-1">
                  <input v-model="so2" v-bind="so2Attrs" type="number" id="so2"
                    class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl pr-16 px-2" />
                  <span
                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none">ppb</span>
                </div>
                <span v-if="errors.so2" class="text-red-500 text-sm">{{ errors.so2 }}</span>
              </div>

              <!-- CO2 Field -->
              <div class="mb-6">
                <label for="co2" class="block text-base font-medium text-gray-800">CO2</label>
                <div class="relative mt-1">
                  <input v-model="co2" v-bind="co2Attrs" type="number" id="co2"
                    class="block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl pr-16 px-2" />
                  <span
                    class="absolute inset-y-0 right-3 flex items-center text-gray-500 text-sm pointer-events-none">ppm</span>
                </div>
                <span v-if="errors.co2" class="text-red-500 text-sm">{{ errors.co2 }}</span>
              </div>
            </div>
          </form>
        </div>
      </div>
      <DialogFooter class="p-6 pt-0">
        <div class="px-4 py-3 text-right">
          <button form="edit-sensor" type="submit" class="bg-green-500 text-white rounded-3xl px-4 py-2">Add</button>
          <button type="button" @click="show = false"
            class="ml-3 bg-gray-300 text-gray-700 rounded-3xl px-4 py-2">Cancel</button>
        </div>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
