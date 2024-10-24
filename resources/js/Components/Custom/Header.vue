<template>
  <header class="bg-white pt-3">
    <div class="w-full mx-auto px-4 lg:px-[50px] grid grid-cols-10 pb-5">
      <div class="col-span-6 lg:col-span-3">
        <div class="flex items-center">
          <Link href="/">
            <img
              src="/images/logo.png"
              alt="Central Environmental Authority Logo"
              class="w-auto"
            />
          </Link>
        </div>
      </div>

      <nav
        class="lg:col-span-4 md:col-span-5 hidden lg:flex justify-center space-x-6 mt-4"
      >
        <Link
          href="/"
          :class="[
            isActive('/')
              ? 'bg-green1 text-white px-6 rounded-full'
              : 'text-green3 hover:text-green2 text-right',
            'py-2 h-10 ',
          ]"
        >
          <div class="min-w-[78px]">{{ $t("Dashboard") }}</div>
        </Link>
        <Link
          href="/public/about-us"
          :class="[
            isActive('/public/about-us')
              ? 'bg-green1 text-white px-6 rounded-full'
              : 'text-green3 hover:text-green2',
            'py-2 h-10',
          ]"
        >
          <div class="min-w-[67px]">{{ $t("About Us") }}</div>
        </Link>
        <Link
          href="/public/faq"
          :class="[
            isActive('/public/faq')
              ? 'bg-green1 text-white px-6 rounded-full'
              : 'text-green3 hover:text-green2',
            'py-2 h-10',
          ]"
        >
          {{ $t("FAQ") }}
        </Link>
      </nav>

      <div
        class="lg:col-span-3 md:md:col-span-10 hidden lg:flex justify-end space-x-4 mt-4"
      >
        <div
          @click="toggleSwitch"
          class="w-[6.5rem] h-10 flex items-center bg-green1 rounded-full px-1 cursor-pointer relative"
        >
          <span
            class="text-white text-xs absolute transition-all duration-500 ease-in-out"
            :class="isOn ? 'right-4' : 'left-4'"
          >
            {{ switchText }}
          </span>
          <div
            id="switchBall"
            class="bg-white w-8 h-8 rounded-full transition-all duration-500 ease-in-out"
            :class="isOn ? 'ml-0' : 'ml-[calc(100%-2rem)]'"
          ></div>
        </div>

        <div class="relative inline-block text-left">
          <div>
            <LanguageDropdown />
          </div>
        </div>
        <!-- <button class="bg-green1 text-white px-5 rounded-full py-2 h-10">
          {{ $t('Login') }}
        </button> -->
      </div>

      <!-- Mobile nav -->
      <div class="col-span-4 block lg:hidden">
        <div class="flex justify-end items-center space-x-2 h-full">
          <Link class="mr-[1px]" href="/public/recent-notifications">
            <img
              src="/images/Bell.svg"
              alt="Notification Icon"
              class="w-6 h-6"
            />
          </Link>
          <button @click="toggleMobileMenu">
            <img src="/images/mobilemenu.svg" alt="Menu Icon" class="w-6 h-6" />
          </button>
        </div>
      </div>

      <div class="col-span-10 block lg:hidden">
        <div class="flex justify-end space-x-4 mt-4">
          <div
            @click="toggleSwitch"
            class="w-[6.5rem] h-9 flex items-center justify-between relative bg-green1 rounded-full pl-1 cursor-pointer"
          >
            <span
              class="text-white text-xs absolute transition-all duration-500 ease-in-out"
              :class="isOn ? 'right-4' : 'left-4'"
            >
              {{ switchText }}
            </span>
            <div
              id="switchBall"
              class="bg-white w-7 h-7 rounded-full transition-all duration-500 ease-in-out"
              :class="isOn ? 'ml-0' : 'ml-[calc(100%-2rem)]'"
            ></div>
          </div>

          <div class="relative inline-block text-left">
            <div>
              <LanguageDropdown />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <transition name="fade">
      <div v-if="isMobileMenuOpen" class="fixed inset-0 z-[3000] bg-white">
        <div
          class="flex justify-between items-center border-b pb-3 mb-4 w-full p-4"
        >
          <img src="/images/logo.png" alt="Logo" class="h-8 w-auto" />
          <div class="flex space-x-4">
            <img
              src="/images/Bell.svg"
              alt="Notification Icon"
              class="w-6 h-6"
            />
            <img
              src="/images/mobilemenu.svg"
              v-if="!isMobileMenuOpen"
              alt="Profile Icon"
              class="w-6 h-6 rounded-full"
            />
            <button @click="toggleMobileMenu" class="text-gray-700">
              <span class="sr-only">Close menu</span>
              <svg
                class="h-6 w-6"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                aria-hidden="true"
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
        </div>
        <nav class="">
          <Link
            href="/"
            :class="[
              isActive('/') ? 'bg-green1 text-white' : 'text-green3',
              'block text-lg px-4 py-2',
            ]"
            >{{ $t("Dashboard") }}</Link
          >
          <Link
            href="/public/about-us"
            :class="[
              isActive('/public/about-us') ? 'bg-green1 text-white' : 'text-green3',
              'block text-lg px-4 py-2',
            ]"
            >{{ $t("About Us") }}</Link
          >
          <Link
            href="/public/faq"
            :class="[
              isActive('/public/faq') ? 'bg-green1 text-white' : 'text-green3',
              'block text-lg px-4 py-2',
            ]"
            >{{ $t("FAQ") }}</Link
          >
        </nav>
      </div>
    </transition>
  </header>
</template>

<script setup>
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import LanguageDropdown from "@/Components/Custom/Dropdowns/LanguageDropdown.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useStore } from 'vuex'

const store = useStore();

const { t } = useI18n();

const isOn = ref(true);

const switchText = computed(() => (isOn.value ? t("AQI - SL") : t("AQI - US")));

const toggleSwitch = () => {
  isOn.value = !isOn.value;
  store.commit('updateAqiRegion', isOn.value ? t("AQI - SL") : t("AQI - US"));
};

const isMobileMenuOpen = ref(false);
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const page = usePage();

const isActive = (path) => {
  return page.url === path;
};
</script>

<style scoped>
</style>
