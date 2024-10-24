
<script setup>
defineProps({
    value: String,
});
</script>
<template>
  <div class="w-[400px] bg-green-100 border rounded border-green-600 rounded-b text-teal-900 px-4 py-3 shadow-md" role="alert">
  <div class="flex">
    <div>
      <span class="block sm:inline">{{ value }}</span>
    </div>
  </div>
</div>
</template>