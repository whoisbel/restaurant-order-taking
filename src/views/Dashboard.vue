<script setup>
import { ref, computed } from "vue";
import axios from "axios";
import { v4 as uuidv4 } from "uuid";
//get payment from user
const selectedPayment = ref("CASH");
//ALL FOOD ITEMS
const foodQuantity = ref([
  {
    name: "Sizzling Sisig",
    id: 1,
    quantity: 0,
    img: "../src/assets/sisig.png",
    price: 150,
  },
  {
    name: "Steak",
    id: 2,
    quantity: 0,
    img: "../src/assets/steak.png",
    price: 250,
  },
  {
    name: "Burger",
    id: 3,
    quantity: 0,
    img: "../src/assets/burger.png",
    price: 150,
  },
  {
    name: "Red Horse 500ML",
    id: 4,
    quantity: 0,
    img: "../src/assets/redhorse.png",
    price: 100,
  },
  {
    name: "Iced tea",
    id: 5,
    quantity: 0,
    img: "../src/assets/icedtea.png",
    price: 50,
  },
  {
    name: "Fries",
    id: 6,
    quantity: 0,
    img: "../src/assets/fries.png",
    price: 75,
  },
  {
    name: "Hummus",
    id: 7,
    quantity: 0,
    img: "../src/assets/hummus.png",
    price: 90,
  },
]);
//GET TOTAL
const totalPrice = computed(() => {
  return foodQuantity.value.reduce((acc, curr) => {
    if (curr.quantity > 0) {
      return acc + curr.quantity * curr.price;
    }
    return acc;
  }, 0);
});
//GET ORDERS THAT IS NON ZERO
const orders = computed(() => {
  return foodQuantity.value.filter((item) => item.quantity > 0);
});
//SEND DATA TO PHP API BACKEND THROUGH POST REQUEST
const sendData = () => {
  const validItems = foodQuantity.value.filter((item) => item.quantity > 0);
  const customerID = uuidv4();
  const orderID = uuidv4();
  const currentDate = new Date().toISOString().slice(0, 10);
  const currentTime = new Date().toLocaleTimeString("it-IT");

  if (validItems.length === 0) {
    console.log("No valid items to send");
    window.alert("No valid items to send");
    return;
  }

  const url = "http://localhost/restaurant-order-taking/public/post.php";
  const data = {
    orderID: orderID,
    customerID: customerID,
    order: validItems,
    total: totalPrice.value,
    payment: selectedPayment.value,
    date: currentDate,
    time: currentTime,
    status: "pending",
  };

  const headers = { "Content-Type": "application/json" };

  axios
    .post(url, JSON.stringify(data), { headers })
    .then((response) => {
      console.log(response.data);
      window.alert(
        "Thank you for your order! We have received it and will start processing it shortly."
      );
    })
    .catch((error) => {
      console.log(error);
    });
};
</script>

<template>
  <div class="flex flex-col md:flex-row bg-gray-100 h-screen">
    <div class="flex-1 bg-gray-800 text-white p-4">
      <h1 class="text-4xl font-bold mb-8">Food Menu</h1>
      <div
        class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4"
      >
        <div v-for="(food, index) in foodQuantity" :key="index">
          <div class="rounded-lg overflow-hidden shadow-lg">
            <img :src="food.img" alt="" class="w-full h-48 object-cover" />
            <div class="px-4 py-2">
              <h2 class="text-xl font-bold mb-2">{{ food.name }}</h2>
              <p class="text-gray-400 text-base mb-4">₱{{ food.price }}</p>
              <div class="flex justify-between">
                <label class="text-lg">Quantity:</label>
                <input
                  v-model="foodQuantity[index].quantity"
                  type="number"
                  min="0"
                  class="rounded-md border-gray-300 py-2 px-3 w-20 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                  placeholder="0"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="flex-1 bg-gray-100 p-4">
      <h1 class="text-4xl font-bold mb-8 text-gray-800">Chosen Orders</h1>
      <div class="mb-8">
        <div
          v-for="(order, index) in orders"
          :key="index"
          class="flex items-center p-4 bg-white rounded-lg mb-4 shadow-lg"
        >
          <img
            :src="order.img"
            alt=""
            class="w-16 h-16 object-cover rounded-lg mr-4"
          />
          <div>
            <h2 class="text-xl font-bold mb-2">{{ order.name }}</h2>
            <p class="text-gray-600 text-base">₱{{ order.price }}</p>
          </div>
        </div>
        <div v-if="orders.length === 0" class="text-lg text-gray-600">
          No orders yet.
        </div>
      </div>
      <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-3xl font-bold text-gray-800">Total:</h2>
          <span class="text-2xl font-bold text-gray-800"
            >₱{{ totalPrice }}</span
          >
        </div>
        <div class="flex justify-between items-center">
          <label for="payment" class="text-xl text-gray-800 mr-4"
            >Payment Method:</label
          >
          <select
            v-model="selectedPayment"
            name="payment"
            id="payment"
            class="form-select rounded-md border-gray-300 py-2 px-3 w-full md:w-1/2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
          >
            <option value="CASH">CASH</option>
            <optgroup label="E-MONEY"></optgroup>
            <option value="GCASH">GCASH</option>
            <option value="PAYMAYA">PAYMAYA</option>
            <option value="GRABPAY">GRABPAY</option>
            <optgroup label="BANK">BANK</optgroup>
            <option>UNIONBANK</option>
            <option>LANDBANK</option>
            <option>BDO</option>
            <option>BPI</option>
          </select>
          <div class="flex flex-col justify-center items-center">
            <button
              @click="sendData()"
              class="py-1 px-4 bg-gray-800 hover:bg-gray-700 text-white mx-auto rounded-lg border-2 border-gray-800 focus:outline-none"
            >
              SUBMIT
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
