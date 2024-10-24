<template>
  <div class="flex items-center justify-between w-full">
    <button 
      @click="loadPage(currentPage - 1)" 
      :disabled="currentPage === 1"
      class="px-4 py-1 rounded-lg border border-gray-300"
      :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
    >
      Previous
    </button>

    <span class="text-sm">
      <span class="text-sm font-medium">
        <span class="text-green1">Page {{ currentPage }}</span> of {{ totalPages }}
      </span>
    </span>

    <button 
      @click="loadPage(currentPage + 1)"
      :disabled="currentPage === totalPages"
      class="px-3 py-1 rounded-lg border border-gray-300"
      :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
    >
      Next
    </button>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, ref } from 'vue';
import axios from 'axios';

const props = defineProps({
  initialPage: {
    type: Number,
    required: true
  },
  initialTotalPages: {
    type: Number,
    required: true
  },
  routeName: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['updateData']);

const currentPage = ref(props.initialPage);
const totalPages = ref(props.initialTotalPages);

const loadPage = async (pageNumber) => {
  if (pageNumber < 1 || pageNumber > totalPages.value) return;

  try {
    const response = await axios.get(route(props.routeName, { page: pageNumber }), {
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    currentPage.value = response.data.meta.current_page;
    totalPages.value = response.data.meta.last_page;
    emit('updateData', response.data.data);
  } catch (error) {
    console.error('Error fetching data:', error);
  }
};
</script>