<template>
  <div class="relative w-64">
    <div class="flex items-center cursor-pointer" @click="openDropdown">
      <img
        src="/images/greenpinicon.png"
        class="h-5 w-auto"
        alt="Location pin"
      />
      <input
        type="text"
        v-model="searchQuery"
        @input="debouncedSearch"
        @focus="openDropdown"
        :placeholder="placeholder"
        class="inputsearch text-[18px] md:text-base text-green-700 border-none bg-transparent box-shadow-none outline-none focus:border-none focus:box-shadow-none focus:outline-none cursor-pointer"
      />
      <svg
        class="text-green-700 ml-1 h-6 w-6"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 20 20"
        fill="currentColor"
        aria-hidden="true"
      >
        <path
          fill-rule="evenodd"
          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 011.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
          clip-rule="evenodd"
        ></path>
      </svg>
    </div>

    <ul
      v-if="showDropdown"
      class="absolute w-full mt-1 bg-white border border-gray-300 rounded-md text-green-800 shadow-lg max-h-60 overflow-auto z-10"
    >
      <li
        v-for="item in displayedItems"
        :key="item.id"
        @click="selectItem(item)"
        class="px-4 py-2 hover:bg-gray-100 cursor-pointer"
      >
        {{ item.title || item.name }}
      </li>
      <li v-if="loading" class="px-4 py-2 text-gray-500">Loading...</li>
      <li
        v-if="!loading && displayedItems.length === 0"
        class="px-4 py-2 text-gray-500"
      >
        No results found
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import debounce from "lodash/debounce";
import axios from "axios";
import store from "@/store/index.js";

const props = defineProps({
  placeholder: {
    type: String,
    default: "Search for a City",
  },
   initialLocations: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits(['item-selected']);

const searchQuery = ref("");
const items = ref(props.initialLocations);
const loading = ref(false);
const showDropdown = ref(false);
const isHardcodedData = ref(true);

const displayedItems = computed(() => {
  return items.value;
});

const fetchItems = async () => {
  if (searchQuery.value.trim() === "") {
    items.value = props.initialLocations;
    isHardcodedData.value = true;
    return;
  }

  loading.value = true;
  isHardcodedData.value = false;
  try {
    items.value = [];
    const response = await axios.get(
      `/public/cities-with-province?q=${searchQuery.value}`
    );
    items.value = response.data;
  } catch (error) {
    console.error("Error fetching items:", error);
    items.value = [];
  } finally {
    loading.value = false;
  }
};

const debouncedSearch = debounce(() => {
  fetchItems();
}, 300);

const selectItem = (item) => {
  searchQuery.value = item.title || item.name;
  showDropdown.value = false;
  store.dispatch("updateSelectedLocationAction",item)
    emit("item-selected", item);
  items.value = props.initialLocations;
  isHardcodedData.value = true;
};

const openDropdown = () => {
  showDropdown.value = true;
  if (isHardcodedData.value) {
    items.value = props.initialLocations;
  } else {
    fetchItems();
  }
};

onMounted(() => {
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".relative")) {
      showDropdown.value = false;
    }
  });
});
</script>

<style>
.inputsearch {
  box-shadow: none !important;
  border: none !important;
  outline: none !important;
}

.inputsearch::placeholder {
  color: #016553;
}
</style>
