<script setup>
import axios from 'axios';
import { ref, onMounted, watch } from 'vue';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { toast } from 'vue-sonner';
import { useGlobalStore } from '@/store/global';
import { Loader2 } from 'lucide-vue-next';

const globalStore = useGlobalStore();

const isLoading = ref(false);
const showEditUserModal = defineModel('showEditUserModal');
const selectedUser = defineModel('selectedUser');
const selectedUserId = defineModel('selectedUserId');
const organizations = ref([]);
const roles = ref([]);

onMounted(async () => {
  roles.value = await getRoles();
  organizations.value = await getOrgnizations();
});

const getOrgnizations = async () => {
  const response = await axios.get('/admin/get-organization');
  return response.data;
};

const getRoles = async () => {
  const response = await axios.get('/admin/get-roles');
  return response.data;
};

const { errors, handleSubmit, defineField, resetForm, setFieldError, setValues } = useForm({
  validationSchema: yup.object({
    organization_id: yup
      .number()
      .required('Organization is required'),
    role_id: yup
      .number()
      .required('Role is required'),
    username: yup
      .string()
      .required('Username is required')
      .min(4, 'The user name must be 4 to 20 characters long (min 4 max 20 characters)')
      .max(20, 'The user name must be 4 to 20 characters long (min 4 max 20 characters)')
      .matches(/^[a-zA-Z0-9_-]+$/, 'The user name is invalid (Allow only letters, numbers, underscore, and dash)'),
    email: yup
      .string()
      .required('Email is required')
      .email('Enter a valid email address'),
    contact: yup
      .string()
      .required('The contact number is required')
      .matches(/^[+0-9]+$/, 'The contact number is invalid (allow only numeric values and + symbol)')
      .min(6, 'The contact number must be between 6 and 15 characters long')
      .max(15, 'The contact number must be between 6 and 15 characters long')
  }),
  initialValues: {
    organization_id: null,
    role_id: null,
    username: null,
    email: null,
    contact: null
  },
});

const [organization_id, organizationIdAttrs] = defineField('organization_id');
const [role_id, roleIdAttrs] = defineField('role_id');
const [username, userNameAttrs] = defineField('username');
const [email, emailAttrs] = defineField('email');
const [contact, contactAttrs] = defineField('contact');

// Watch the selectedSensor value and update the form values
watch(selectedUser, (newValue) => {
  setValues({
    role_id: (newValue?.roles && newValue.roles.length > 0) ? newValue.roles[0].id : null,
    organization_id: (newValue?.user_organizations && newValue.user_organizations.length > 0) ? newValue.user_organizations[0].organization_id : null,
    username: newValue?.name || null,
    email: newValue?.email || null,
    contact: newValue?.contact || null,
    data: newValue,
  });
});

watch(showEditUserModal, () => {
  if (!showEditUserModal.value) {
    resetForm();
  }
});

const onSubmit = handleSubmit((values) => {
  console.log(values);

  isLoading.value = true;

  const data = {
    ...values
  };

  axios.put(route('user.update', selectedUserId.value), data)
    .then(response => {
      globalStore.refreshTableAction();
      resetForm();
      showEditUserModal.value = false;
      toast.success('User updated', {
        description: 'User updated successfully.',
      });
    })
    .catch(error => {
      console.error(error.response.data);
      toast.error('Failed to update user', {
        description: 'Please contact support if this error persists.',
      });
    }).finally(() => {
      isLoading.value = false;
    });
});
</script>

<template>
  <Dialog v-model:open="showEditUserModal">
    <DialogContent class="sm:max-w-xl grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[80dvh]"
      @interactOutside="(e) => e.preventDefault()">
      <DialogHeader class="p-6 pb-0">
        <DialogTitle>Edit User Form</DialogTitle>
      </DialogHeader>
      <div class="grid gap-4 py-4 overflow-y-auto px-6">
        <div class="flex flex-col justify-between">
          <form id="create-user" @submit="onSubmit">
            <div class="mb-4">
              <label for="organization" class="block text-sm font-medium text-gray-700 mb-1">Select Organization</label>
              <Select v-model="organization_id" v-bind="organizationIdAttrs">
                <SelectTrigger class="w-full rounded-full">
                  <SelectValue placeholder="Select a organization" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectItem v-for="organization in organizations" :key="organization.id" :value="organization.id">
                      <div class="capitalize">
                        {{ organization.name }}
                      </div>
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
              <span v-if="errors.organization_id" class="text-red-500 text-sm">{{ errors.organization_id }}</span>
            </div>
            <div class="mb-4">
              <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Select Role</label>
              <Select v-model="role_id" v-bind="roleIdAttrs">
                <SelectTrigger class="w-full rounded-full">
                  <SelectValue placeholder="Select a role" />
                </SelectTrigger>
                <SelectContent>
                  <SelectGroup>
                    <SelectItem v-for="(key, role) in roles" :key="role" :value="parseInt(role, 10)">
                      <div class="capitalize">
                        {{ key }}
                      </div>
                    </SelectItem>
                  </SelectGroup>
                </SelectContent>
              </Select>
              <span v-if="errors.role_id" class="text-red-500 text-sm">{{ errors.role_id }}</span>
            </div>
            <div class="mb-4">
              <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
              <input v-model="username" v-bind="userNameAttrs" type="text" id="username"
                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl" placeholder="Example: John">
              <span v-if="errors.username" class="text-red-500 text-sm">{{ errors.username }}</span>
            </div>
            <div class="mb-4">
              <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
              <input v-model="email" v-bind="emailAttrs" type="email" id="email" disabled
                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl cursor-not-allowed bg-gray-100"
                placeholder="Example: cea@example.com">
              <span v-if="errors.email" class="text-red-500 text-sm">{{ errors.email }}</span>
            </div>
            <div class="mb-4">
              <label for="contact" class="block text-sm font-medium text-gray-700">Contact Number</label>
              <input v-model="contact" v-bind="contactAttrs" type="tel" id="contact"
                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl"
                placeholder="Example: +639123456789">
              <span v-if="errors.contact" class="text-red-500 text-sm">{{ errors.contact }}</span>
            </div>
          </form>
        </div>
      </div>
      <DialogFooter class="p-6 pt-0">
        <div class="px-4 py-3 text-right">
             <button form="create-user" type="submit" :disabled="isLoading"
            class="bg-green-500 text-white rounded-3xl px-4 py-2"
            :class="{ 'opacity-50 cursor-not-allowed': isLoading }">
            <div class="flex items-center space-x-2">
              <Loader2 v-show="isLoading" class="w-4 h-4 mr-2 animate-spin" />
              Add
            </div>
          </button>
          <button type="button" @click="showEditUserModal = false" :disabled="isLoading"
            class="ml-3 bg-gray-300 text-gray-700 rounded-3xl px-4 py-2"
            :class="{ 'opacity-50 cursor-not-allowed': isLoading }">Cancel</button>
        </div>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
