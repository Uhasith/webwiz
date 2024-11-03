<template>
  <div class="relative" ref="dropdownContainer">
    <div
      @click="toggleDropdown"
      class="flex items-center space-x-2 cursor-pointer"
    >
      <div class="relative">
        <div
          class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center"
        >
          <span class="text-lg font-semibold text-gray-700">{{ initials }}</span>
        </div>
        <div
          class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"
        ></div>
      </div>
      <div class="flex flex-col w-24 flex-shrink-0 ">
        <span class="font-medium text-sm flex-wrap">{{ user.name }}</span>
        <span class="text-xs text-gray-500">{{ role }}</span>
      </div>
    </div>

    <!-- Dropdown menu -->
    <div
      v-if="dropdownOpen"
      class="origin-top-right absolute z-[2001] right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
      role="menu"
      aria-orientation="vertical"
      aria-labelledby="menu-button"
    >
      <div class="py-1" role="none">
        <!-- Dropdown options -->
        <Link
          :href="route('profile.show')"
          class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
        >
          Profile
        </Link>

        <form @submit.prevent="logout">
          <DropdownLink as="button"> Log Out </DropdownLink>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from "vue";
import { Link, router } from "@inertiajs/vue3";
import DropdownLink from '@/Components/DropdownLink.vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const user = ref(page.props.auth.user);
const roles = ref(page.props.roles);
const dropdownOpen = ref(false);
const dropdownContainer = ref(null);

const initials = computed(() => {
  const name = user.value.name || '';
  const nameParts = name.match(/[A-Z][a-z]*/g) || []; 

  return nameParts.length >= 2
    ? nameParts[0][0] + nameParts[1][0]
    : name.substring(0, 2);
});

const role = computed(() => {
  if (roles.value && Object.keys(roles.value).length > 0) {
    return Object.values(roles.value)[0];
  }
  return ''; 
});

const logout = () => {
    router.post(route('logout-now'));
};

function toggleDropdown() {
  dropdownOpen.value = !dropdownOpen.value;
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
