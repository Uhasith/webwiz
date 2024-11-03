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
const organizations = ref([]);
const showAddUserModal = defineModel('showAddUserModal');
const roles = defineModel('roles');

onMounted(async () => {
  organizations.value = await getOrgnizations();
});

const getOrgnizations = async () => {
  const response = await axios.get('/admin/get-organization');
  return response.data;
};

const { errors, handleSubmit, defineField, resetForm, setFieldError } = useForm({
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
      .required('Contact number is required')
      .matches(/^[+0-9]+$/, 'The contact number is invalid (allow only numeric values and + symbol)')
      .min(6, 'The contact no must be 6 to 15 characters long')
      .max(15, 'The contact no must be 6 to 15 characters long')
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

watch(showAddUserModal, () => {
  if (!showAddUserModal.value) {
    resetForm();
  }
});

const onSubmit = handleSubmit((values) => {
  console.log(values);

  isLoading.value = true;

  const data = {
    ...values
  };

  axios.post(route('user.store'), data)
    .then(response => {
      globalStore.refreshTableAction();
      resetForm();
      showAddUserModal.value = false;
      toast.success('User Added', {
        description: 'User created successfully.',
      });
    })
    .catch(error => {
      console.error(error.response.data);
      if (error.response.data.message == "The email has already been taken."){
        setFieldError('email', error.response.data.message);
      }

      if (error.response.data.message == "The username has already been taken."){
        setFieldError('username', error.response.data.message);
      }
      
      toast.error('Failed to create user', {
        description: 'Please contact support if this error persists.',
      });
    }).finally(() => {
      isLoading.value = false;
    });
});
</script>

<template>
  <Dialog v-model:open="showAddUserModal">
    <DialogContent class="sm:max-w-xl grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[80dvh]"
      @interactOutside="(e) => e.preventDefault()">
      <DialogHeader class="p-6 mb-2 pb-0">
        <DialogTitle>Add New User Form</DialogTitle>
      </DialogHeader>
      <div class="grid gap-4 py-4 overflow-y-auto px-6">
        <div class="flex flex-col justify-between">
          <form id="create-user" @submit="onSubmit">
            <div class="mb-4">
              <label for="organization" class="block text-sm font-medium text-gray-700 mb-1">Select Organization</label>
              <Select v-model="organization_id" v-bind="organizationIdAttrs">
                <SelectTrigger class="w-full rounded-full">
                  <SelectValue placeholder="Select" />
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
                  <SelectValue placeholder="Select" />
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
              <input v-model="email" v-bind="emailAttrs" type="email" id="email"
                class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-3xl"
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
      <DialogFooter class="p-6 pt-0 mt-28">
        <div class="px-4 py-3">
          <button form="create-user" type="submit" :disabled="isLoading"
            class="ml-3 w-[139px] bg-green1 text-white rounded-3xl px-6 py-2"
            :class="{ 'opacity-50 cursor-not-allowed': isLoading }">
            <div class="items-center space-x-2">
              <Loader2 v-show="isLoading" class="w-4 h-4 mr-2 animate-spin" />
              Add
            </div>
          </button>
        </div>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
