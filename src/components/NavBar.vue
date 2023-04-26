<template>
  <nav class="bg-slate-700">
    <!-- Desktop Navigation -->
    <div
      class="hidden md:flex md:justify-between md:items-center md:w-full mx-auto px-4 py-2 md:py-4"
    >
      <div class="flex justify-between items-center">
        <router-link to="/dashboard" class="text-white text-2xl font-bold">
          Restobar
        </router-link>
      </div>
      <ul class="flex justify-between items-center">
        <li class="pt-2 pr-0 text-white text-lg md:pl-4 md:pt-0">
          <router-link to="/dashboard">Dashboard</router-link>
        </li>
        <li class="pt-2 pr-0 text-white text-lg md:pl-4 md:pt-0">
          <router-link to="/orders">Orders</router-link>
        </li>
        <li class="pt-2 pr-0 text-white text-lg md:pl-4 md:pt-0">
          <router-link to="/history">History</router-link>
        </li>
        <!-- render logout button if user is logged in -->
        <li
          v-if="isLoggedIn"
          class="pt-2 pr-0 text-white text-lg md:pl-4 md:pt-0"
        >
          <button
            @click="handleLogout"
            class="bg-red-600 hover:bg-red-700 text-white rounded-lg py-2 px-4 font-bold"
          >
            Logout
          </button>
        </li>
      </ul>
    </div>

    <!-- Mobile Navigation -->
    <div
      class="md:hidden flex justify-between items-center w-full mx-auto px-4 py-2"
    >
      <router-link to="/dashboard" class="text-white text-2xl font-bold">
        Restobar
      </router-link>
      <button @click="toggleMobileNav" class="text-white">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-6 h-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M3.75 5.25h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5m-16.5 4.5h16.5"
          />
        </svg>
      </button>
    </div>

    <div class="md:hidden" v-show="showMobileNav">
      <ul class="text-white text-lg py-2 ml-4 font-bold">
        <li class="pb-2">
          <router-link to="/dashboard" @click="showMobileNav = false"
            >Dashboard</router-link
          >
        </li>
        <li class="pb-2">
          <router-link to="/orders" @click="showMobileNav = false"
            >Orders</router-link
          >
        </li>
        <li class="pb-2">
          <router-link to="/history" @click="showMobileNav = false"
            >History</router-link
          >
        </li>
        <li v-if="isLoggedIn" class="pb-2">
          <button
            @click="handleLogout"
            class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
          >
            Logout
          </button>
        </li>
      </ul>
    </div>
  </nav>
</template>

<script setup>
import { ref } from "vue";
import router from "../router";

const showMobileNav = ref(false);
const isLoggedIn = localStorage.getItem("isLoggedIn");

const toggleMobileNav = () => {
  showMobileNav.value = !showMobileNav.value;
};

const handleLogout = () => {
  localStorage.removeItem("isLoggedIn");
  // perform any additional logout logic here
  router.push("/");
};
</script>
