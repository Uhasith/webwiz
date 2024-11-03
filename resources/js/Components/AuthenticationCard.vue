<template>
  <div
    class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100"
  >
    <!-- <div>
            <slot name="logo" />
        </div> -->

    <div
    class="w-full sm:max-w-md lg:max-w-[650px] min-h-[700px] lg:max-h-[700px] overflow-y-auto  px-6 py-4 bg-white border border-gray-200 sm:rounded-lg"
    
    >
      <div class="text-center">
        <img src="/images/logo.png" alt="Centered Image" class="mx-auto h-12 mt-4" />
        <h1 class="text text-4xl font-semibold mt-4">Log in to your account</h1>
        <p class="mt-1 font-bold text-gray-500">
          Welcome back! Please enter your details.
        </p>
        <img
          src="/images/loginscreen.svg"
          alt="Centered Image"
          class="mx-auto"
        />
      </div>

      <!-- <form action="#" method="POST" class="mt-6">
        <div class="mb-4">
          <label for="email" class="block text-black text-sm">Email</label>
          <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            class="text-sm mt-1 w-full px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
          />
        </div>
        <div class="mb-4">
          <label for="password" class="block text-black text-sm">Password</label>
          <input
            type="password"
            id="password"
            name="password"
            placeholder="********"
            class="text-sm mt-1 w-full px-3 py-1 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
          />
        </div>
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center">
            <input
              type="checkbox"
              id="remember_me"
              name="remember_me"
              class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded text-sm"
            />
            <label for="remember_me" class="ml-2 block text-gray-900 text-xs"
              >Remember for 30 days</label
            >
          </div>
          <a href="#" class="text-xs text-green-600 hover:underline"
            >Forgot password</a
          >
        </div>
        <div>
          <button
            type="submit"
            class="w-full text-sm bg-green-600 text-white py-1 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-opacity-50"
          >
            Sign in
          </button>
        </div>
        <p class="mt-6 text-center text-gray-600 text-xs">
          Don't have an account?
          <a href="#" class="text-green-600 hover:underline">Sign up</a>
        </p>
      </form> -->
      <slot />
    </div>
  </div>
</template>
