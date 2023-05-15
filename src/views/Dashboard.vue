<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";
import { v4 as uuidv4 } from "uuid";

const router = useRouter();
//get payment from user
const selectedPayment = ref("CASH");
//ALL FOOD ITEMS
const customerName = ref("");
const employeeID = ref(localStorage.getItem("employeeID"));
const foodItems = ref([]);

axios
  .get("http://localhost/restaurant-order-taking/public/items.php")
  .then((response) => {
    foodItems.value = response.data.map((food) => {
      return {
        ...food,
        quantity: 0, // Add a quantity property with a default value of 0
      };
    });
  })
  .catch((error) => {
    console.error("Error:", error);
  });

//GET TOTAL
const totalPrice = computed(() => {
  return foodItems.value.reduce((acc, curr) => {
    if (curr.quantity > 0) {
      return acc + curr.quantity * curr.price;
    }
    return acc;
  }, 0);
});
//GET ORDERS THAT IS NON ZERO
const orders = computed(() => {
  return foodItems.value.filter((item) => item.quantity > 0);
});
//SEND DATA TO PHP API BACKEND THROUGH POST REQUEST
const sendData = () => {
  const validItems = foodItems.value.filter((item) => item.quantity > 0);
  const currentDate = new Date().toISOString().slice(0, 10);
  const currentTime = new Date().toLocaleTimeString("it-IT");
  if (validItems.length === 0) {
    console.log("No valid items to send");
    window.alert("No valid items to send");
    return;
  }

  if (
    customerName.value.trim() === "" || // Check if customer name is empty
    /\d/.test(customerName.value.trim()) || // Check if customer name contains numbers
    customerName.value.trim().length < 3 // Check if customer name has at least 3 characters
  ) {
    console.log("Invalid customer name");
    window.alert("Invalid customer name");
    return;
  }

  const url = "http://localhost/restaurant-order-taking/public/post.php";
  const data = {
    order: validItems,
    total: totalPrice.value,
    payment: selectedPayment.value,
    date: currentDate,
    time: currentTime,
    status: "pending",
    customerName: customerName.value,
    customerID: uuidv4(),
    orderID: uuidv4(),
    employeeID: employeeID.value,
  };

  const headers = { "Content-Type": "application/json" };

  axios
    .post(url, JSON.stringify(data), { headers })
    .then((response) => {
      console.log(response.data);
      window.alert("Order submitted");
      router.go(0);
    })
    .catch((error) => {
      console.log(error);
    });
};
</script>

<template>
  <div class="flex flex-col md:flex-row bg-gray-100 h-screen w-screen">
    <div class="max-w-7xl mx-auto px-4">
      <div
        class="bg-gray-50 text-gray-800 p-4 rounded-lg border-gray-300 border-2"
      >
        <h1 class="text-4xl font-bold mb-8">Food Menu</h1>
        <div
          class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8"
        >
          <div
            v-for="(food, index) in foodItems"
            :key="index"
            class="rounded-lg overflow-hidden shadow-lg hover:bg-gray-200 hover:shadow-xl transition duration-300 ease-in-out"
          >
            <div class="h-64 relative">
              <img
                :src="food.img"
                alt=""
                class="w-full h-full object-cover rounded-t-lg"
                v-if="food.availability"
              />
              <div
                class="absolute top-0 left-0 w-full h-full bg-gray-300 rounded-t-lg flex justify-center items-center"
                v-else
              >
                <span class="text-xl font-bold text-gray-800"
                  >Not available</span
                >
              </div>
            </div>
            <div class="px-4 py-2">
              <h2 class="text-xl font-bold mb-2">{{ food.name }}</h2>
              <p class="text-gray-700 text-base mb-4" v-if="food.availability">
                ₱{{ food.price }}
              </p>
              <p class="text-gray-700 text-base mb-4" v-else>Not available</p>
              <div class="flex justify-between items-center">
                <label class="text-lg text-gray-700">Quantity:</label>
                <input
                  v-model.number="food.quantity"
                  type="number"
                  min="0"
                  class="rounded-md border-gray-300 py-2 px-3 w-20 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  :disabled="!food.availability"
                  placeholder="0"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="flex flex-col items-center py-12 px-4 bg-gray-50 flex-[50%] h-screen"
    >
      <div class="max-w-7xl w-full mx-auto">
        <h1 class="text-4xl font-bold mb-8 text-gray-900 text-center">
          Chosen Orders
        </h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(order, index) in orders"
            :key="index"
            class="bg-white rounded-lg shadow-lg overflow-hidden"
          >
            <img :src="order.img" alt="" class="w-full h-56 object-cover" />
            <div class="p-4">
              <h2 class="text-xl font-bold mb-2">{{ order.name }}</h2>
              <p class="text-gray-600 text-base">₱{{ order.price }}</p>
              <p class="text-gray-600 text-base">
                Quantity: {{ order.quantity }}
              </p>
            </div>
          </div>
          <div
            v-if="orders.length === 0"
            class="text-lg text-gray-600 text-center col-span-full"
          >
            No orders yet.
          </div>
        </div>

        <div class="mt-12 bg-white rounded-lg shadow-lg p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-3xl font-bold text-gray-900">Total:</h2>
            <span class="text-2xl font-bold text-gray-900"
              >₱{{ totalPrice }}</span
            >
          </div>
          <div class="mb-4">
            <label
              for="customerName"
              class="block text-lg font-medium text-gray-900 mb-2"
              >Customer Name:</label
            >
            <input
              v-model="customerName"
              type="text"
              id="customerName"
              class="form-input rounded-lg border-gray-300 py-2 px-4 w-full text-gray-900 leading-tight focus:outline-none focus:shadow-outline"
              placeholder="Enter your name"
            />
          </div>
          <div class="mb-4">
            <label
              for="payment"
              class="block text-lg font-medium text-gray-900 mb-2"
              >Payment Method:</label
            >
            <select
              v-model="selectedPayment"
              name="payment"
              id="payment"
              class="form-select rounded-lg border-gray-300 py-2 px-4 w-full text-gray-900 leading-tight focus:outline-none focus:shadow-outline"
            >
              <option value="CASH">CASH</option>
              <optgroup label="E-MONEY">
                <option value="GCASH">GCASH</option>
                <option value="PAYMAYA">PAYMAYA</option>
                <option value="GRABPAY">GRABPAY</option>
              </optgroup>
              <optgroup label="BANK">
                <option>UNIONBANK</option>
                <option>LANDBANK</option>
                <option>BDO</option>
                <option>BPI</option>
              </optgroup>
            </select>
          </div>
          <div class="flex justify-end">
            <button
              @click="sendData()"
              class="py-2 px-6 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg shadow-md focus:outline-none focus:shadow-outline"
            >
              Submit Order
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
