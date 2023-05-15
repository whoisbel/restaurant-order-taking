<template>
  <div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Order List</h1>
    <input
      type="text"
      class="form-control rounded-md bg-gray-100 text-gray-500 shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 border-2"
      placeholder="Search orders..."
      v-model="searchTerm"
    />
    <table class="table-auto border w-full">
      <thead>
        <tr>
          <th class="px-4 py-2">#</th>
          <th class="px-4 py-2">Order ID</th>
          <th class="px-4 py-2">Customer Name</th>
          <th class="px-4 py-2">Payment Method</th>
          <th class="px-4 py-2">Total</th>
          <th class="px-4 py-2">Order Date</th>
          <th class="px-4 py-2">Order Time</th>
          <th class="px-4 py-2">Status</th>
          <th class="px-4 py-2">Order Details</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(order, index) in filteredOrders" :key="order.OrderID">
          <td class="border px-4 py-2">{{ index + 1 }}</td>
          <td class="border px-4 py-2">{{ order.OrderID }}</td>
          <td class="border px-4 py-2">{{ order.CustomerName }}</td>
          <td class="border px-4 py-2">{{ order.PaymentMethod }}</td>
          <td class="border px-4 py-2">₱{{ order.Total }}</td>
          <td class="border px-4 py-2 whitespace-nowrap">
            {{ order.OrderDate }}
          </td>

          <td class="border px-4 py-2">{{ order.OrderTime }}</td>
          <td class="border px-4 py-2">
            <div class="flex justify-center items-center">
              <span
                class="text-center flex-1 mr-4"
                :class="{
                  'bg-green-100 text-green-800':
                    order.OrderStatus === 'completed',
                  'bg-red-100 text-red-800': order.OrderStatus === 'cancelled',
                }"
                >{{ order.OrderStatus }}</span
              >
            </div>
          </td>
          <td class="border px-4 py-2">
            <div class="flex flex-col">
              <div
                v-for="(detail, detailIndex) in order.OrderDetails"
                :key="detailIndex"
                class="flex items-center mb-2"
              >
                <img
                  :src="`../src/assets/${detail.MenuItem.replace(
                    / /g,
                    ''
                  )}.png`"
                  alt="item image"
                  class="w-16 h-16 mr-4"
                />
                <div>
                  <div class="text-lg font-medium">{{ detail.MenuItem }}</div>
                  <div class="text-sm text-gray-500">
                    {{ detail.Quantity }} x {{ detail.Price }}
                  </div>
                </div>
              </div>
            </div>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import axios from "axios";

const orders = ref([]);
const searchTerm = ref("");
// Fetch orders data from server using axios
axios
  .get("http://localhost/restaurant-order-taking/public/get_orders.php/")
  .then((response) => {
    orders.value = response.data.orders.filter(
      (order) =>
        order.OrderStatus === "completed" || order.OrderStatus === "cancelled"
    );
  })
  .catch((error) => {
    console.error(error);
  });
const filteredOrders = computed(() => {
  if (searchTerm.value.trim() === "") {
    return orders.value;
  } else {
    const searchValue = searchTerm.value.toLowerCase();
    return orders.value.filter((order) => {
      const customerName = order.CustomerName.toLowerCase();
      const orderId = order.OrderID.toString().toLowerCase();
      return (
        customerName.includes(searchValue) || orderId.includes(searchValue)
      );
    });
  }
});

watch(searchTerm, () => {
  filteredOrders.value = filteredOrders.value.slice();
});
</script>
