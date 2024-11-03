<script setup>
import { Head } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import ResetPassword from "./ResetPassword.vue";
import SuccessAlert from "@/Components/SuccessAlert.vue";
import ErrorAlert from "@/Components/ErrorAlert.vue";
import axios from "axios";
import { reset } from '@/Utils/Alert';
import { ref, Suspense, onMounted } from "vue";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
  status: String,
});

const page = usePage();

onMounted(() => {
  axios.defaults.headers.common['X-CSRF-TOKEN'] = page.props.csrf_token || '';
});

const email = ref('');
const error = ref('');
const isSubmitting = ref(false);
const emailSent = ref(false);
const { successMessage, errorMessage, resetMessages } = reset();

const emailRegex = /^[a-zA-Z0-9]+(?:[._-][a-zA-Z0-9]+)*@[a-zA-Z0-9]+(?:\.[a-zA-Z]{2,})+$/;
const submitEmail = async () => {
  isSubmitting.value = true;
  error.value = '';

  try {

    if (!email.value) {
      error.value = 'The email is required';
      isSubmitting.value = false;
      return;
    }

    if (!emailRegex.test(email.value)) {
      error.value = 'The email is invalid';
      isSubmitting.value = false;
      return;
    }

    const response = await axios.post('/auth/forget-password', { email: email.value });

    console.log(response.data);
    localStorage.setItem('email', email.value);
    emailSent.value = true;
    successMessage.value = "Verificaion code is sent to your email"
    resetMessages();

  } catch (err) {
    error.value = err.response.data.message || 'An error occurred. Please try again.';
    resetMessages();
  } finally {
    isSubmitting.value = false;
  }
};
</script>
<template>

  <Head title="Forgot Password" />
  <div v-if=successMessage class="fixed top-4 right-4">
    <SuccessAlert :value="successMessage" />
  </div>
  <div v-if=errorMessage class="fixed top-4 right-4">
    <ErrorAlert :value="errorMessage" />
  </div>

  <div class="min-h-screen flex flex-col sm:justify-center items-center p-auto sm:pt-0  bg-gray-100">
    <div
      class=" mt-6 px-6 py-4 bg-white border border-gray-200 overflow-hidden sm:rounded-lg md:w-[650px] w-full opacity-0 md:opacity-100">
      <div class="text-center">
        <img src="/images/logo.png" alt="Centered Image" class="mx-auto h-12 mt-3" />
        <h1 class="text-4xl font-semibold mt-10">Forgot Password</h1>
        <p class="mt-1 font-bold text-gray-500">
          Enter your email account to reset password
        </p>
        <img src="/images/loginscreen.svg" alt="Login Screen" class="mx-auto" />
      </div>

      <div v-if="!emailSent" class="mt-6">
        <Suspense>
          <template #default>
            <form @submit.prevent="submitEmail">
              <div class="flex justify-center items-center">
                <div>
                  <InputLabel for="email" value="Email" class="block text-black text-sm" />
                  <TextInput id="email" v-model="email" type="text"
                    class="rounded-2xl text-sm mt-1 w-[550px] px-3 py-1 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    autofocus placeholder="Enter your email" autocomplete="username" />
                  <InputError class="mt-2" :message="error" />
                </div>
              </div>
              <div class="flex justify-center items-center mt-8">
                <PrimaryButton
                  class=" text-center w-[550px] bg-green1 text-white py-1  hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 flex items-center justify-center text-sm font-normal"
                  :class="{ 'opacity-25': isSubmitting }" :disabled="isSubmitting">
                  <template v-if="isSubmitting">Loading...</template>
                  <template v-else>Continue</template>
                </PrimaryButton>
              </div>
            </form>
          </template>
          <template #fallback>
            <div class="flex justify-center items-center">
              <span class="text-sm font-semibold">Loading...</span>
            </div>
          </template>
        </Suspense>
      </div>



      <div v-else>
        <ResetPassword @password-reset-success="(message) => { successMessage = message; }" />
      </div>
      <div class="mt-5 text-right mr-6">
        <a href="/login" class="text-sm font-medium text-green1 hover:underline">
          Log In
        </a>
      </div>
    </div>
  </div>
</template>