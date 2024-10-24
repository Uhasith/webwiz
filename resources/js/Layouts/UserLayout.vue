<template>
  <div class="bg-customGray">
    <Header />
    <main>
      <slot />
    </main>
    <Footer/>
  </div>
</template>

<script setup>
import Header from "@/Components/Custom/Header.vue";
import Footer from '@/Components/Custom/Footer.vue';


</script>