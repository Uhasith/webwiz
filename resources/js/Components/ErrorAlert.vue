
  <script setup>
  defineProps({
      value: String,
  });
  </script>
  
  <template>
    <div class="w-[400px] bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative flex items-center justify-center" role="alert">
        <svg class="w-5 mr-4 h-5 text-red-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
           <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.857 10.43c.75 1.338-.215 3.008-1.743 3.008H4.143c-1.528 0-2.493-1.67-1.743-3.008L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-2-4a1 1 0 112 0v2a1 1 0 11-2 0V9z" clip-rule="evenodd" />
        </svg>
        <span  v-if="value" class="block sm:inline">{{ value }}</span>
   </div>
  </template>
 