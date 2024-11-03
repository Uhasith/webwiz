<script setup>
import { ref, watch } from "vue";
import { Accordion, AccordionContent, AccordionItem, AccordionTrigger } from '@/Components/ui/accordion';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/Components/ui/dialog';
import { Switch } from '@/Components/ui/switch';
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import axios from "axios";
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { toast } from 'vue-sonner';

const groupedPermissions = defineModel('groupedPermissions');
const showAddRoleModal = defineModel('showAddRoleModal');
const rolesWithPermissions = defineModel('rolesWithPermissions');
const roles = defineModel('roles');
const checkedStatus = ref({});

const togglePermission = (permissionId) => {
  checkedStatus.value[permissionId];
};

// Format category names
const formatCategory = (category) => {
  return category
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

const { errors, handleSubmit, defineField, resetForm, setFieldError } = useForm({
  validationSchema: yup.object({
    roleName: yup
      .string()
      .required('Role name is required')
      .min(4, 'Role name must be at least 4 characters')
      .max(20, 'Role name must be at most 20 characters')
      .matches(/^[a-zA-Z\s]+$/, 'Role name can only contain letters and spaces, without special characters'),
  }),
  initialValues: {
    roleName: '',
  },
});

watch(showAddRoleModal, (newValue, oldValue) => {
  if (newValue === false) {
    checkedStatus.value = {};
    resetForm();
  }
});

const [roleName, roleNameAttrs] = defineField('roleName');

const onSubmit = handleSubmit((values) => {

  const selectedPermissions = Object.keys(checkedStatus.value).filter(key => checkedStatus.value[key]);

  if (selectedPermissions.length === 0) {
    toast.error('No permissions selected', {
      description: 'Please select at least one permission.',
    });
    return;
  }

  const payload = {
    role_name: values.roleName,
    permissions: selectedPermissions,
  };

  axios.post(route('role.store'), payload)
    .then(res => {
      roleName.value = '';
      checkedStatus.value = {};
      if (res.data.status === 'success') {
        rolesWithPermissions.value = res.data.rolesWithPermissions;
        roles.value = res.data.roles;
        toast.success('Role Created', {
          description: 'Role created successfully.',
        });
      }

      showAddRoleModal.value = false;
    })
    .catch(error => {
      console.error(error);
      if (error.response.data.message) {
        setFieldError('roleName', error.response.data.message);
      }
      toast.error('Failed to create role', {
        description: 'Please contact support if this error persists.',
      });
    });

});
</script>

<template>
  <Dialog v-model:open="showAddRoleModal">
    <DialogContent class="sm:max-w-xl grid-rows-[auto_minmax(0,1fr)_auto] p-0 max-h-[80dvh]"
      @interactOutside="(e) => e.preventDefault()">
      <DialogHeader class="p-6 pb-0">
        <DialogTitle> Add New User Role Form</DialogTitle>
      </DialogHeader>
      <div class="grid gap-4 py-4 overflow-y-auto px-6">
        <div class="flex flex-col justify-between">
          <form id="add-role" @submit="onSubmit">
            <div class="mb-4 mt-2">
              <InputLabel for="roleName" value="Add New Role" class="block text-black text-sm" />
              <TextInput id="roleName" v-model="roleName" v-bind="roleNameAttrs" type="text" placeholder="Sub Admin"
                class="text-sm mt-1 w-full px-3 py-2 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
              <InputError class="mt-2" :message="errors.roleName" />
            </div>
            <div class="mb-4">
              <label class="block text-sm font-medium mb-3">Select Permissions</label>
              <Accordion v-for="(permission, category) in groupedPermissions" :key="permission.id" type="single"
                collapsible class="border border-gray-300 rounded-md mb-3 px-3">
                <AccordionItem value="item-1">
                  <AccordionTrigger>{{ formatCategory(category) }}</AccordionTrigger>
                  <AccordionContent>
                    <div v-for="item in permission" :key="item.id"
                      class="flex items-center justify-between w-auto px-2 py-2">
                      <p class="text-sm">{{ item.name }}</p>

                      <Switch v-model:checked="checkedStatus[item.id]" @update:checked="togglePermission(item.id)" />
                    </div>
                  </AccordionContent>
                </AccordionItem>
              </Accordion>
            </div>
          </form>
        </div>
      </div>
      <DialogFooter class="p-6 pt-0">
        <div class="px-4 py-3 text-right">
          <button form="add-role" type="submit"
            class="bg-green1 text-base font-medium text-white w-[139px] rounded-3xl px-6 py-2">Add</button>

        </div>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
