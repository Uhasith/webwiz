<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import AuthenticationCard from "@/Components/AuthenticationCard.vue";
import AuthenticationCardLogo from "@/Components/AuthenticationCardLogo.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const form = useForm({
  email: "",
  password: "",
  remember: false,
});

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      remember: form.remember ? "on" : "",
    }))
    .post(route("login"), {
      onFinish: () => form.reset("password"),
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
          class="block text-black text-sm"
        />
        <TextInput
          id="email"
          v-model="form.email"
          type="email"
          placeholder="Enter your email"
          class="rounded-2xl text-sm mt-1 w-full px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
          required
          autofocus
          autocomplete="username"
        />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="mb-4">
        <InputLabel
          for="password"
          value="Password"
          class="block text-black text-sm"
        />
        <TextInput
          id="password"
          v-model="form.password"
          type="password"
          placeholder="********"
          class="rounded-2xl text-sm mt-1 w-full px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
          required
          autocomplete="current-password"
        />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center">
          <Checkbox
            v-model:checked="form.remember"
            id="remember_me"
            name="remember_me"
            class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded text-sm"
          />
          <label for="remember_me" class="ml-2 block text-gray-900 text-xs"
            >Remember for 30 days</label
          >
        </div>
        <Link
          v-if="canResetPassword"
          :href="route('password.request')"
          class="text-xs text-green-600 hover:underline"
          >Forgot password</Link
        >
      </div>

      <div>
        <PrimaryButton
          class="rounded-2xl w-full text-sm bg-green1 text-white py-1 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50"
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
