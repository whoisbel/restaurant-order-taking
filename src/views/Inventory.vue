<script setup>
import { ref } from "vue";
import axios from "axios";

const foodItems = ref([]);

axios
  .get("http://localhost/restaurant-order-taking/public/items.php/")
  .then((response) => {
    foodItems.value = response.data;
  })
  .catch((error) => {
    console.log(error);
  });

async function updateFoodItem(id, foodItem) {
  try {
    const response = await axios.put(
      `http://localhost/restaurant-order-taking/public/items.php/?id=${id}`,
      {
        availability: foodItem.availability,
        price: foodItem.price,
      }
    );
    if (typeof response.data === "object" && "id" in response.data) {
      const updatedItemIndex = foodItems.value.findIndex(
        (item) => item.id === id
      );
      if (updatedItemIndex >= 0) {
        foodItem = response.data;
        foodItems.value[updatedItemIndex] = foodItem;
      }
    } else {
      console.error("Invalid response data", response.data);
    }
  } catch (error) {
    console.log(error);
  }
}
const deleteFoodItem = (foodItem) => {
  const confirmDelete = confirm("Are you sure you want to delete this item?");
  if (confirmDelete) {
    axios
      .post(
        `http://localhost/restaurant-order-taking/public/itemdelete.php`,
        { id: foodItem.id },
        {
          headers: {
            "Content-Type": "application/json;charset=UTF-8",
          },
        }
      )
      .then((response) => {
        console.log(response.data);
        // Remove the deleted item from the array
        const index = foodItems.value.findIndex(
          (item) => item.id === foodItem.id
        );
        if (index !== -1) {
          foodItems.value.splice(index, 1);
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
};
async function updateAvailability(foodItem) {
  const newAvailability = !foodItem.availability;
  foodItem.availability = newAvailability;
  await updateFoodItem(foodItem.id, foodItem);
}

async function updatePrice(foodItem) {
  const newPrice = prompt("Enter new price:");
  if (newPrice !== null && foodItem.price !== undefined) {
    foodItem.price = newPrice;
    await updateFoodItem(foodItem.id, foodItem);
  }
}
const newFoodItem = ref({
  name: "",
  price: 0,
  img: null,
  availability: false,
});

async function addFoodItem() {
  const formData = new FormData();
  formData.append("name", newFoodItem.value.name);
  formData.append("price", newFoodItem.value.price);
  formData.append("img", newFoodItem.value.img);
  formData.append("availability", newFoodItem.value.availability);

  const response = await axios.post(
    "http://localhost/restaurant-order-taking/public/items.php",
    formData,
    {
      headers: {
        "Content-Type": "multipart/form-data",
      },
    }
  );

  if (typeof response.data === "object" && "id" in response.data) {
    const addedFoodItem = { ...response.data };
    if (newFoodItem.value.img) {
      addedFoodItem.img = URL.createObjectURL(
        new File([newFoodItem.value.img], addedFoodItem.name)
      );
    }
    foodItems.value.push(addedFoodItem);
    newFoodItem.value = {
      name: "",
      price: 0,
      img: null,
      availability: false,
    };
    alert("Item added successfully");
  } else {
    console.error("Invalid response data", response.data);
    alert("Failed to add item");
  }
}

function onImageChange(event) {
  const files = event.target.files;
  if (files.length > 0) {
    newFoodItem.value.img = files[0];
    newFoodItem.value.imgUrl = URL.createObjectURL(files[0]);
  }
}
</script>

<template>
  <div class="flex">
    <div class="w-1/2 p-4">
      <div class="bg-gray-100 min-h-full">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
          <h1 class="text-3xl font-bold text-gray-900 mb-8">
            Add a New Food Item
          </h1>
          <form
            @submit.prevent="addFoodItem"
            class="bg-white rounded-lg shadow-md p-4"
            enctype="multipart/form-data"
          >
            <div class="mb-4">
              <label for="name" class="block text-gray-700 font-bold mb-2"
                >Name</label
              >
              <input
                type="text"
                id="name"
                name="name"
                v-model="newFoodItem.name"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                required
              />
            </div>
            <div class="mb-4">
              <label for="price" class="block text-gray-700 font-bold mb-2"
                >Price</label
              >
              <input
                type="number"
                id="price"
                name="price"
                min="0"
                v-model="newFoodItem.price"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                required
              />
            </div>
            <div class="mb-4">
              <label for="image" class="block text-gray-700 font-bold mb-2"
                >Image</label
              >
              <input
                type="file"
                id="image"
                ref="image"
                name="image"
                accept="image/*"
                @change="onImageChange"
                required
              />
            </div>
            <div class="mb-4">
              <label
                for="availability"
                class="block text-gray-700 font-bold mb-2"
                >Availability</label
              >
              <input
                type="checkbox"
                id="availability"
                name="availability"
                class="mr-2 leading-tight"
                @change="newFoodItem.availability = $event.target.checked"
              />
              <span class="text-gray-700">Available</span>
            </div>
            <div class="text-center">
              <button
                type="submit"
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
              >
                Add Food Item
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="w-1/2 p-4">
      <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Available Items</h1>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="foodItem in foodItems"
            :key="foodItem.id"
            class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 ease-in-out overflow-hidden"
          >
            <div class="h-64">
              <!-- Use v-if to conditionally show the image -->
              <img
                v-if="foodItem.availability"
                :src="foodItem.img"
                alt="Food Item Image"
                class="w-full h-full object-cover rounded-t-lg"
              />
              <div v-else class="w-full h-full bg-gray-300 rounded-t-lg"></div>
            </div>
            <div class="p-4">
              <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-900">
                  {{ foodItem.name }}
                </h2>
                <span class="font-bold text-gray-900">
                  <!-- Use v-if to conditionally show the price or "Not available" message -->
                  {{
                    foodItem.availability
                      ? "₱" + foodItem.price
                      : "Not available"
                  }}
                </span>
              </div>
              <div class="flex justify-center items-center">
                <div class="flex flex-col space-y-2">
                  <button
                    v-if="foodItem.availability"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                    @click="updatePrice(foodItem)"
                  >
                    Edit Price
                  </button>
                  <button
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
                    @click="updateAvailability(foodItem)"
                  >
                    {{
                      foodItem.availability
                        ? "Mark as unavailable"
                        : "Mark as available"
                    }}
                  </button>
                  <button
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                    @click="deleteFoodItem(foodItem)"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </div>
            <div class="p-4 bg-red-200" v-if="!foodItem.availability">
              <span class="font-bold text-gray-900">
                This item is not currently available
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
