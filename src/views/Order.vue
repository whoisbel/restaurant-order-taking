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
          <th class="px-4 py-2 whitespace-nowrap">Order Time</th>
          <th class="px-4 py-2">Status</th>
          <th class="px-4 py-2">Order Details</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(order, index) in filteredOrders" :key="order.OrderID">
          <td class="border px-4 py-2">{{ index + 1 }}</td>
          <td class="border px-4 py-2 whitespace-nowrap">
            {{ order.OrderID }}
          </td>
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
                  'bg-yellow-100 text-yellow-800':
                    order.OrderStatus === 'pending',
                }"
                >{{ order.OrderStatus }}</span
              >
              <button
                class="flex bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-xl mr-2"
                @click="updateOrderStatus(order.OrderID, 'completed')"
              >
                Completed
              </button>
              <button
                class="flex bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-xl"
                @click="updateOrderStatus(order.OrderID, 'cancelled')"
              >
                Cancelled
              </button>
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
import { useRouter } from "vue-router";

const searchTerm = ref("");
const router = useRouter();
const orders = ref([]);

// Fetch orders data from server using axios
axios
  .get("http://localhost/restaurant-order-taking/public/get_orders.php/")
  .then((response) => {
    orders.value = response.data.orders.filter(
      (order) => order.OrderStatus === "pending"
    );
  })
  .catch((error) => {
    console.error(error);
  });

const updateOrderStatus = (orderId, status) => {
  axios
    .post(
      "http://localhost/restaurant-order-taking/public/update_order_status.php",
      {
        orderId,
        status,
      },
      {
        headers: {
          "Content-Type": "application/json",
        },
      }
    )
    .then((response) => {
      console.log("Response from server:", response);
      if (response.data.success) {
        const orderIndex = orders.value.findIndex(
          (order) => order.OrderID === orderId
        );
        if (orderIndex !== -1) {
          orders.value[orderIndex].OrderStatus = status;
        }
        router.go(0);
      }
    })
    .catch((error) => {
      console.error("Error from server:", error);
    });
};

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
