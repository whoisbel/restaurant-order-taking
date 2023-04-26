<script setup>
import axios from "axios";
import { reactive } from "vue";

const orders = reactive([]);

axios
  .get("http://localhost/restaurant-order-taking/public/get_orders.php")
  .then((response) => {
    orders.push(...response.data);
    fetchOrders();
  })
  .catch((error) => {
    console.log(error);
  });

function fetchOrders() {
  axios
    .get(
      "http://localhost/restaurant-order-taking/public/update_order_status.php"
    )
    .then((response) => {
      if (Array.isArray(response.data)) {
        orders.value = response.data.filter(
          (order) => order.status === "pending"
        );
      }
    })
    .catch((error) => {
      console.error(error);
    });
}

function updateOrderStatus(orderId, status) {
  axios
    .post(
      "http://localhost/restaurant-order-taking/public/update_order_status.php",
      {
        order_id: orderId,
        status: status,
      }
    )
    .then((response) => {
      console.log(response.data);
      // update the status of the order in the frontend
      if (orders.value && orders.value.length > 0) {
        orders.value.forEach((order) => {
          if (order.order_id === orderId) {
            order.status = status;
          }
        });
      }
    })
    .catch((error) => {
      console.error(error);
    });
  location.reload();
}
</script>

<template>
  <div class="py-10">
    <h1 class="text-3xl font-bold mb-4">Orders</h1>
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Order ID
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Customer ID
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Total
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Payment Method
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Status
          </th>
          <th
            scope="col"
            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
          >
            Products
          </th>
        </tr>
      </thead>
      <tbody
        class="bg-white divide-y divide-gray-200"
        v-for="order in orders.filter((order) => order.status === 'pending')"
        :key="order.order_id"
      >
        <tr class="hover:bg-gray-100">
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">
              {{ order.order_id }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">
              {{ order.customer_id }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">
              {{ order.total }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="text-sm font-medium text-gray-900">
              {{ order.payment_method }}
            </div>
          </td>
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex justify-center">
              <span
                class="flex justify-center items-center px-2 text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 mr-2"
              >
                {{ order.status }}
              </span>
              <button
                class="px-4 py-2 text-xs font-semibold leading-5 text-white uppercase transition-colors duration-150 bg-green-500 rounded-lg focus:shadow-outline hover:bg-green-600"
                @click="updateOrderStatus(order.order_id, 'complete')"
              >
                Complete
              </button>
              <button
                class="px-4 py-2 text-xs font-semibold leading-5 text-white uppercase transition-colors duration-150 bg-red-500 rounded-lg focus:shadow-outline hover:bg-red-600"
                @click="updateOrderStatus(order.order_id, 'cancelled')"
              >
                Cancel
              </button>
            </div>
          </td>

          <td class="px-6 py-4 whitespace-nowrap">
            <ul class="divide-y divide-gray-200">
              <li
                v-for="detail in order.details"
                :key="detail.Detail_ID"
                class="flex items-center py-2"
              >
                <img
                  :src="detail.Product_Image"
                  alt="Product Image"
                  class="w-12 h-12 object-cover rounded-lg mr-4"
                />
                <div>
                  <div class="text-sm font-medium text-gray-900">
                    {{ detail.Product_Name }}
                  </div>
                  <div class="text-sm text-gray-500">
                    {{ detail.Quantity }}x {{ detail.Price }}
                  </div>
                </div>
              </li>
            </ul>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
