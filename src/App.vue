<template>
  <div class="h-full">
    <NavBar v-if="!isHome" />
    <router-view />
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useRoute } from "vue-router";
import NavBar from "./components/NavBar.vue";

const isHome = computed(() => {
  return useRoute().name === "Home" || useRoute().name === "register";
});
</script>
