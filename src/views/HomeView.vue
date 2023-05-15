<template>
  <div class="bg-gray-800 min-h-screen flex items-center justify-center">
    <div
      class="bg-gray-100 text-gray-700 rounded-lg shadow-md p-8 max-w-md w-full"
    >
      <h3 class="text-2xl font-bold text-center mb-8">Login to your account</h3>
      <form @submit.prevent="handleLogin">
        <div class="mb-4">
          <label class="block font-bold mb-2" for="username">Username</label>
          <input
            v-model="username"
            type="text"
            id="username"
            class="block w-full p-3 rounded-md border-2 border-gray-400 focus:outline-none focus:border-blue-500"
          />
        </div>
        <div class="mb-4">
          <label class="block font-bold mb-2" for="password">Password</label>
          <input
            v-model="password"
            type="password"
            id="password"
            class="block w-full p-3 rounded-md border-2 border-gray-400 focus:outline-none focus:border-blue-500"
          />
        </div>
        <div class="flex justify-between items-center mb-4">
          <button
            type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600"
          >
            Login
          </button>
          <router-link to="/register" class="text-blue-500 hover:text-blue-700">
            Create an account
          </router-link>
        </div>
      </form>
      <p v-if="errorMessage" class="text-red-500 mt-4">{{ errorMessage }}</p>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const username = ref("");
const password = ref("");
const router = useRouter();
const errorMessage = ref("");

localStorage.setItem("isLoggedIn", "false");

const handleLogin = () => {
  axios
    .post("http://localhost/restaurant-order-taking/public/login.php", {
      email: username.value,
      password: password.value,
    })
    .then((response) => {
      if (response.data.success) {
        localStorage.setItem("isLoggedIn", "true"); // Set isLoggedIn to true
        localStorage.setItem("employeeID", response.data.employeeID); // Store employeeID in localStorage
        router.push("/dashboard");
      } else {
        errorMessage.value = response.data.message;
      }
    })
    .catch((error) => {
      errorMessage.value = "Network error occurred. Please try again.";
      console.log(error);
    });
};
</script>
