<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';

const firstDropdown = ref('');
const secondDropdown = ref('');
const secondDropdownOptions = ref([]);

const firstDropdownOptions = [
  { value: 'fruit', label: 'Fruit' },
  { value: 'vegetable', label: 'Vegetable' },
];

watch(firstDropdown, async (newValue) => {
  if (newValue) {
    try {
      const response = await axios.get(route('test.index'), {
        params: { category: newValue }
      });
      secondDropdownOptions.value = response.data;
    } catch (error) {
      console.error('Error fetching options:', error);
    }
  } else {
    secondDropdownOptions.value = [];
  }
  secondDropdown.value = ''; // Reset second dropdown when first dropdown changes
});
</script>

<template>
  <div>
    <select v-model="firstDropdown">
      <option value="">Select Category</option>
      <option v-for="option in firstDropdownOptions" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>

    <select v-model="secondDropdown">
      <option value="">Select Item</option>
      <option v-for="option in secondDropdownOptions" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>
  </div>
</template>