<template>
  <div
    class="inline-flex items-center border custom_border_color rounded-full px-4 space-x-1"
  >
    <span class="whitespace-nowrap text-[#13131380] text-sm font-semibold"
      >Time Period:</span
    >
    <input
      ref="flatpickrInput"
      type="text"
      class="flex-grow border-none bg-transparent focus:border-none focus:outline-none box-shadow-none text-[#016553] text-sm"
      readonly
    />
    <img src="/images/Calendar.png" class="ml-1 h-4 w-auto mr-2" />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";
import {useStore} from "vuex";

const flatpickrInput = ref(null);
const store = useStore();

onMounted(() => {
  flatpickr(flatpickrInput.value, {
    mode: "range",
    dateFormat: "M d Y",
    defaultDate: [new Date(), new Date()],
    onClose: (selectedDates) => {
      if (selectedDates.length === 2) {
        const [start, end] = selectedDates;
        flatpickrInput.value.value = `${formatDate(start)} ${formatDate(end)}`;
        store.dispatch("updateDateRangeAction",flatpickrInput.value.value );

      }
    },
  });
});

function formatDate(date) {
    return date.toISOString().split('T')[0];
}
</script>

<style scoped>
.box-shadow-none {
  box-shadow: none;
}
</style>
