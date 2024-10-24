<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { defineProps, defineEmits } from 'vue';
import { ref,Suspense } from 'vue';
import axios from 'axios';
import { reset } from '@/Utils/Alert';

const props = defineProps({
    email: String,
    token: String,
});

const otp = ref('');
const password = ref('');
const password_confirmation = ref('');
const error = ref('');
const passwordError = ref('');
const passwordNotMatchError = ref('')
const isSubmitting = ref(false);
const { successMessage, errorMessage, resetMessages } = reset();
const emit = defineEmits(['password-reset-success']);


const validatePassword = (password) => {
  const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])(?!^\s).{8,}$/;
  return passwordRegex.test(password) && !/^\s/.test(password);
};

const submit = async () => {
  isSubmitting.value = true;
  error.value = '';
  passwordError.value = '';
  passwordNotMatchError.value='';
  

  if (otp.value.trim() === '') {
    error.value = "OTP is required.";
    isSubmitting.value = false;
    return;
  }

  if (password.value.trim() === '') {
    passwordError.value = "Password is required.";
    isSubmitting.value = false;
    return;
  }

  if (password_confirmation.value.trim() === '') {
    passwordError.value = "Password confirmation is required.";
    isSubmitting.value = false;
    return;
  }

 
  if (!validatePassword(password.value)) {
    passwordError.value = 'Password should contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
    isSubmitting.value = false;
    return;
  } 
  
  if (password.value !== password_confirmation.value) {
    passwordNotMatchError.value = 'Passwords do not match.';
    isSubmitting.value = false;
    return;
  }

  const payload = {
    email: localStorage.getItem('email'),
    otp: otp.value,
    password: password.value,
    password_confirmation: password_confirmation.value
  };
  console.log(payload);

  try {
    const response = await axios.post('/auth/set-password',payload);
    console.log(response.data);
    localStorage.clear();
    successMessage.value='Password has been successfully reset!'
    emit('password-reset-success',successMessage.value );
    resetMessages();
    setTimeout(() => {
      router.visit('/login', {
      });
    }, 3000); 
    
    
  } catch (err) {
    error.value = err.response.data.message || 'An error occurred. Please try again.';

  } finally {
    isSubmitting.value = false;
  }
};
  
</script>

<template>
    <Head title="Reset Password" />
    <Suspense>
      <template #default>
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="otp" value="OTP" />
                <TextInput
                    id="otp"
                    v-model="otp"
                    type="text"
                    class="mt-1 block w-full"
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="error" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="password"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="passwordError" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    v-model="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="passwordNotMatchError" />
            </div>

            <div class="flex justify-center items-center mt-8">
                <PrimaryButton
                  class="rounded-2xl text-center w-[600px] bg-green1 text-white py-1 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 flex items-center justify-center text-sm font-normal"
                  :class="{ 'opacity-25': isSubmitting }"
                  :disabled="isSubmitting"
                >
                  <template v-if="isSubmitting">Resetting...</template>
                  <template v-else>Reset Password</template>
                </PrimaryButton>
              </div>
          </form>
          </template>
          <template #fallback>
            <div class="flex justify-center items-center">
              <span class="text-sm font-semibold">Resetting...</span>
            </div>
          </template>
        </Suspense>
</template>
