<template>
  <div>
    <p>{{ message.name }}</p>
    <button @click="updateMessage">Update Message</button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useStore } from 'vuex'

const store = useStore();

const message = computed(() => store.state.equipmentType);

const updateMessage = () => {
  store.commit('updateMessage', 'New Message');
};
</script>