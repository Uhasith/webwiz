<template  >
  <div v-for="notification in notifications" :key="notification.id"
    class="mx-auto p-4 border border-gray-100 bg-gray-100 rounded-lg mb-4"
  >
    <div class="flex mb-1">
      <img :src="notification?.type === 'Warning' ? '/images/warning-vector.png':
            (notification?.type === 'Danger' ? '/images/danger-vector.png' : '/images/success-vector.png')" class="h-5 w-auto mr-2" />
      <div class="text-sm font-medium"
           :class="notification?.type === 'Warning' ? 'text-yellow-400':
            (notification?.type === 'Danger' ? 'text-red-400' : 'text-green-400')">{{ notification?.type }}</div>
    </div>
    <div>
      <div class="text-md font-bold text-black">{{ notification?.title }}</div>
      <div class="text-xs text-gray-600">
          {{ notification?.description }}
      </div>
    </div>
  </div>
</template>

<script setup>
import {useI18n} from "vue-i18n";
import {onMounted, ref} from "vue";
import axios from "axios";

const { t } = useI18n();
let notifications = ref([]);
onMounted( () => {
    fetchNotifications();
});

async function fetchNotifications() {
    try {
        let response = await axios.get('/public/notifications');
        notifications.value = response.data;
    } catch (error) {

    }
}
</script>
