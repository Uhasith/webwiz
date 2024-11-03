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
                Filters
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
            <form>
              <div class="mb-4">
                <label
                  for="role"
                  class="block text-sm font-medium text-gray-700"
                  >Select Role</label
                >
                <div class="flex items-center">
                  <select
                    id="role"
                    v-model="selectedRole"
                     @change="addRole"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none sm:text-sm rounded-3xl"
                  >
                    <option value="">Select...</option>
                    <option v-for="role in availableRoles" :key="role" :value="role">
                      {{ role }}
                    </option>
                  </select>
                  <!-- <button
                    type="button"
                    @click="addRole"
                    class="ml-2 px-4 py-2 bg-green-500 text-white rounded-full"
                  >
                    Add
                  </button> -->
                </div>
              </div>

              <div class="flex flex-wrap gap-2 max-w-3xl mb-4">
                <button
                  v-for="role in selectedRoles"
                  :key="role"
                  type="button"
                  @click="removeRole(role)"
                  class="flex items-center px-4 py-2 bg-green-50 rounded-full text-green-700 font-semibold"
                >
                  <span>{{ role }}</span>
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4 ml-2"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M6 18L18 6M6 6l12 12"
                    ></path>
                  </svg>
                </button>
              </div>

              <!-- You can add more filter options here if needed -->

            </form>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              @click="applyFilters"
              class="w-full inline-flex justify-center rounded-3xl border border-transparent shadow-sm px-6 py-1 bg-green1 text-base font-medium text-white hover:bg-green1 sm:ml-3 sm:w-auto sm:text-sm"
            >
              Set Filters
            </button>
            <button
              type="button"
              class="mt-3 w-full inline-flex justify-center rounded-3xl border border-gray-300 shadow-sm px-6 py-1 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              @click="$emit('close')"
            >
              Clear
            </button>
          </div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { defineProps, defineEmits, ref, onMounted, watch } from "vue";

const props = defineProps({
  show: Boolean,
  availableRoles: Array,
  initialSelectedRoles: Array,
});

const emit = defineEmits(["close", "applyFilters"]);

const selectedRoles = ref([]);
const selectedRole = ref('');

const addRole = () => {
  if (selectedRole.value && !selectedRoles.value.includes(selectedRole.value)) {
    selectedRoles.value.push(selectedRole.value);
    selectedRole.value = '';
  }
};



const removeRole = (role) => {
  const index = selectedRoles.value.indexOf(role);
  if (index !== -1) {
    selectedRoles.value.splice(index, 1);
  }
};

const applyFilters = () => {
  emit('applyFilters', { roles: selectedRoles.value });
  emit('close');
};

onMounted(() => {
  selectedRoles.value = [...props.initialSelectedRoles];
});

watch(() => props.initialSelectedRoles, (newValue) => {
  selectedRoles.value = [...newValue];
});
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