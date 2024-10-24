<template>
  <div class="relative" ref="dropdownContainer">
    <button
      @click="toggleDropdown"
      class="min-w-[103px] inline-flex w-full justify-center gap-x-1.5 rounded-full bg-white px-3 py-[5px] lg:py-2 text-md font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
    >
      <img src="/images/Language.png" class="h-6 w-6" />
      {{ currentLanguageCode }}
      <svg
        class="-mr-1 h-6 w-6 text-gray-400"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"
          clip-rule="evenodd"
        />
      </svg>
    </button>
    <!-- Dropdown menu -->
    <div
      v-if="dropdownOpen"
      class="origin-top-right z-[2001] absolute right-0 mt-2 w-32 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="menu-button"
    >
      <div class="py-1" role="none">
        <a
          href="#"
          @click.prevent="changeLanguage('en')"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          role="menuitem"
          >English</a
        >
        <a
          href="#"
          @click.prevent="changeLanguage('si')"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          role="menuitem"
          >සිංහල</a
        >
        <a
          href="#"
          @click.prevent="changeLanguage('ta')"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
          role="menuitem"
          >தமிழ்</a
        >
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";
import Cookies from 'js-cookie'; 

const { locale } = useI18n();

const dropdownOpen = ref(false);
const dropdownContainer = ref(null);

const currentLanguageCode = computed(() => {
  switch (locale.value) {
    case "en":
      return "EN";
    case "si":
      return "සිං";
    case "ta":
      return "த";
    default:
      return "En";
  }
});

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value;
}

function changeLanguage(lang) {
  locale.value = lang;
  Cookies.set('language', lang, { expires: 365 });
  dropdownOpen.value = false;
}

function closeDropdown(event) {
  if (dropdownContainer.value && !dropdownContainer.value.contains(event.target)) {
    dropdownOpen.value = false;
  }
}

onMounted(() => {
  document.addEventListener('click', closeDropdown);
});

onUnmounted(() => {
  document.removeEventListener('click', closeDropdown);
});
</script>