<script setup>
import { ref } from "vue";
import axios from "axios";
import { v4 as uuidv4 } from "uuid";
const name = ref("");
const age = ref("");
const address = ref("");
const password = ref("");
const confirmPassword = ref("");
const errorMessage = ref("");
const successMessage = ref("");
const gender = ref("");

const handleRegistration = async () => {
  if (
    !name.value ||
    !age.value ||
    !address.value ||
    !password.value ||
    !confirmPassword.value
  ) {
    errorMessage.value = "All fields are required";
    return;
  }

  if (password.value !== confirmPassword.value && password.value.length) {
    errorMessage.value = "Passwords do not match";
    return;
  }

  if (password.value.length < 8 && confirmPassword.value.length < 8) {
    errorMessage.value = "Password length must be greater than 8";
    return;
  }
  if (age.value <= 0) {
    errorMessage.value = "Age must be greater than 0";
    return;
  }

  if (age.value > 120) {
    errorMessage.value = "Age is too large";
    return;
  }

  const currentDate = new Date();
  const day = ("0" + currentDate.getDate()).slice(-2);
  const month = ("0" + (currentDate.getMonth() + 1)).slice(-2);

  const initials = name.value
    .split(" ")
    .map((n) => n.charAt(0))
    .join("");
  const email = `${initials}${day}${month}@restobar.com`;
  const data = {
    name: name.value,
    age: age.value,
    address: address.value,
    email: email,
    password: password.value,
    employeeID: uuidv4(),
    gender: gender.value,
  };
  // In a real-world app, you would likely make a request to a server to handle registration
  // For the sake of this example, we'll just simulate a successful registration
  axios
    .post(
      "http://localhost/restaurant-order-taking/public/register.php/",
      JSON.stringify(data),
      { "Content-Type": "application/json" }
    )
    .then((response) => {
      console.log(response.data);
      successMessage.value = `Successfully registered! Your email is ${email}`;
    })
    .catch((error) => {
      console.log(error);
    });
  errorMessage.value = "";
};
</script>

<template>
  <div class="bg-gray-800 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-md p-8 max-w-md w-full">
      <h3 class="text-2xl font-bold text-center mb-8">Create an account</h3>
      <form @submit.prevent="handleRegistration">
        <div class="grid grid-cols-2 gap-4 mb-4">
          <div>
            <label class="block font-bold mb-2" for="name">Name:</label>
            <input
              v-model="name"
              type="text"
              id="name"
              class="block w-full p-3 rounded-md border-2 border-gray-400 focus:outline-none focus:border-blue-500"
              placeholder="Enter your name"
            />
          </div>
          <div>
            <label class="block font-bold mb-2" for="age">Age:</label>
            <input
              v-model="age"
              type="number"
              id="age"
              class="block w-full p-3 rounded-md border-2 border-gray-400 focus:outline-none focus:border-blue-500"
              placeholder="Enter your age"
            />
          </div>
        </div>
        <div class="mb-4">
          <label class="block font-bold mb-2" for="gender">Gender:</label>
          <select
            v-model="gender"
            id="gender"
            class="block w-full p-3 rounded-md border-2 border-gray-400 focus:outline-none focus:border-blue-500"
          >
            <option disabled value="">Select your gender</option>
            <option>Male</option>
            <option>Female</option>
            <option>Non-binary</option>
            <option>Prefer not to say</option>
          </select>
        </div>
        <div>
          <label class="block font-bold mb-2" for="address">Address:</label>
          <input
            v-model="address"
            type="text"
            id="address"
            class="block w-full p-3 rounded-md border-2 border-gray-400 focus:outline-none focus:border-blue-500"
            placeholder="Enter your address"
          />
        </div>
        <div class="mb-4">
          <label class="block font-bold mb-2" for="password">Password:</label>
          <input
            v-model="password"
            type="password"
            id="password"
            class="block w-full p-3 rounded-md border-2 border-gray-400 focus:outline-none focus:border-blue-500"
            placeholder="Enter your password"
          />
        </div>
        <div class="mb-4">
          <label class="block font-bold mb-2" for="confirm-password"
            >Confirm Password:</label
          >
          <input
            v-model="confirmPassword"
            type="password"
            id="confirm-password"
            class="block w-full p-3 rounded-md border-2 border-gray-400 focus:outline-none focus:border-blue-500"
            placeholder="Confirm your password"
          />
        </div>
        <div class="flex justify-between">
          <router-link
            to="/"
            class="text-blue-500 hover:text-blue-700 font-bold text-sm px-4 py-2"
          >
            <span>Login</span>
          </router-link>
          <button
            type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:bg-blue-600"
          >
            Register
          </button>
        </div>
      </form>
      <p v-if="errorMessage" class="text-red-500 mt-4">{{ errorMessage }}</p>
      <p v-if="successMessage" class="text-green-500 mt-4">
        {{ successMessage }}
      </p>
    </div>
  </div>
</template>
