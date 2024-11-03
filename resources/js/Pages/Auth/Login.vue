<script setup>
import { Head, Link, useForm, usePage } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { watch } from "vue";

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const page = usePage();

const form = useForm({
  email: "",
  password: "",
  remember: false,
  showPassword: true,

  errors: {
    email: "",
    password: "",
    general: "",
  },
});

const toggleShow = () => {
  form.showPassword = !form.showPassword;
  return
};

const submit = () => {

  form.errors.email = '';
  form.errors.password = '';
  form.errors.general = '';

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!form.email) {
    form.errors.email = 'Email is required';
    return

  } else if (!emailRegex.test(form.email)) {
    form.errors.general = 'Invalid email or password';
    return
  }

  if (!password.value) {
    form.errors.password  = 'Password is required';
    return;
  }
  
  form
    .transform((data) => ({
      ...data,
      remember: form.remember ? "on" : "",
      _token: page.props.csrf_token,
    }))
    .post(route("login"), {
      onFinish: () => form.reset("password"),
      onError: () => {
        
      },
    });
};
</script>

<template>
  <Head title="Log in" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit" class="mt-6">
      <div class="mb-4">
        <InputLabel
          for="email"
          value="Email"
          class="block text-black text-base  ml-0 lg:ml-8"
        />
        <TextInput
          id="email"
          v-model="form.email"
          type="text"
          placeholder="Enter your email"
          class="ml-0 lg:ml-8 text-base mt-1 w-full  lg:w-[544px]  px-3 py-1 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          autofocus
          autocomplete="username"
        />
        <InputError class="mt-3 ml-0 lg:ml-8" :message="form.errors.email" />
      </div>

      <div class="mb-4">
        <InputLabel
          for="password"
          value="Password"
          class="block text-black text-base mb-1  ml-0 lg:ml-8 "
        />

        <div class="relative flex lg:max-w-[578px]">
        <input v-if="form.showPassword"
          id="password"
          v-model="form.password"
          type="password"
          placeholder="••••••••••••"
          class="rounded-md  text-base ml-0 lg:ml-8  m-auto w-full  px-3 py-1 border border-gray-300  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          autocomplete="current-password"
          
        />
        <input v-else
          id="password"
          v-model="form.password"
          type="text"
          placeholder="••••••••••••"
          class="rounded-md  text-base ml-0 lg:ml-8  m-auto w-full  px-3 py-1 border border-gray-300  focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          autocomplete="current-password"
          
        />
        
        <button class="absolute right-2 top-1/2 transform -translate-y-1/2 hover:bg-gray-100 rounded-full" @click.prevent="toggleShow">
          <img
            :src="form.showPassword ? '/images/closedEye.svg' : '/images/eye.svg'"
            :alt="showPassword ? 'Hide password' : 'Show password'"
            class="h-4 w-8"
        />
        </button>
      </div>
        <InputError class="mt-3 ml-0 lg:ml-8" :message="form.errors.password || form.errors.general" />
       
      </div>

      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center">
          <Checkbox
            v-model:checked="form.remember"
            id="remember_me"
            name="remember_me"
            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded text-sm mt-5 ml-0 lg:ml-8 "
          />
          <label for="remember_me" class="block text-gray-500 text-sm mt-5 ml-2"
            >Remember for 30 days</label
          >
        </div>
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-base text-green-600 hover:underline mt-5 mr-8 sm:mr-0"
          >Forgot password</Link
        >
      </div>

      <div>
        <PrimaryButton
          class="rounded-2xl  ml-0 w-full  lg:w-[544px] text-sm bg-green1 text-white py-1 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50 mt-5 lg:ml-8 "
          :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing"
          style="display: flex; align-items: center; justify-content: center"
        >
          Sign in
        </PrimaryButton>
      </div>

<!--      <p class="mt-6 text-center text-gray-600 text-xs">-->
<!--        Don't have an account?-->
<!--        <a href="#" class="text-green-600 hover:underline">Sign up</a>-->
<!--      </p>-->
    </form>

    <!-- <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <Checkbox v-model:checked="form.remember" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link v-if="canResetPassword" :href="route('password.request')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Forgot your password?
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Log in
                </PrimaryButton>
            </div>
        </form> -->
  </AuthenticationCard>
</template>
