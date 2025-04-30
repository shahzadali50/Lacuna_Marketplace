<script setup lang="ts">
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import UserLayout from '@/layouts/UserLayout.vue';
import {
  ShoppingCartOutlined,
  HeartOutlined,
  ShareAltOutlined,
  CheckCircleOutlined,
  CarOutlined,
  SafetyCertificateOutlined,
  MinusOutlined,
  PlusOutlined
} from '@ant-design/icons-vue';

interface Product {
  id: number;
  name: string;
  description: string;
  slug: string;
  final_price: number;
  sale_price: number | null;
  discount: number | null;
  stock: number;
  category_name: string;
  thumbnail_image: string | null;
  gallery_images: string[];
}

const props = defineProps<{
  product: Product;
}>();

// UI state
const quantity = ref(1);

// Methods
const increaseQuantity = () => {
  if (quantity.value < props.product.stock) {
    quantity.value++;
  }
};

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};
</script>

<template>
  <UserLayout>
    <Head :title="product.name" />

    <!-- Breadcrumb -->
    <div class="bg-gray-50 py-2">
      <div class="container mx-auto px-4">
        <div class="flex items-center text-sm text-gray-500">
          <a href="/" class="hover:text-primary">Home</a>
          <span class="mx-2">/</span>
          <a href="/products" class="hover:text-primary">Products</a>
          <span class="mx-2">/</span>
          <a href="/products/category" class="hover:text-primary">{{ product.category_name }}</a>
          <span class="mx-2">/</span>
          <span class="text-gray-700">{{ product.name }}</span>
        </div>
      </div>
    </div>

    <!-- Product Detail Section -->
    <div class="container mx-auto px-4 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Info -->
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ product.name }}</h1>

          <!-- Price -->
          <div class="mb-6">
            <div class="flex items-center">
              <span class="text-3xl font-bold text-gray-900">${{ product.final_price }}</span>
              <span v-if="product.sale_price" class="ml-3 text-lg text-gray-500 line-through">${{ product.sale_price }}</span>
              <span v-if="product.discount" class="ml-3 bg-red-100 text-red-600 px-2 py-1 rounded text-sm font-medium">
                -{{ product.discount }}%
              </span>
            </div>
          </div>

          <!-- Stock Status -->
          <div class="mb-6">
            <div class="flex items-center">
              <CheckCircleOutlined :class="product.stock > 0 ? 'text-green-500 mr-2' : 'text-red-500 mr-2'" />
              <span class="text-gray-700">
                {{ product.stock > 0 ? 'In Stock' : 'Out of Stock' }}
                <span v-if="product.stock > 0" class="text-gray-500">({{ product.stock }} Available)</span>
              </span>
            </div>
          </div>

          <!-- Shipping & Payment Info -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="flex items-center p-3 bg-gray-100 rounded-lg">
              <CarOutlined class="text-primary text-xl mr-2" />
              <span class="text-sm">Free Shipping</span>
            </div>
            <div class="flex items-center p-3 bg-gray-100 rounded-lg">
              <SafetyCertificateOutlined class="text-primary text-xl mr-2" />
              <span class="text-sm">Secure Payment</span>
            </div>
            <div class="flex items-center p-3 bg-gray-100 rounded-lg">
              <CheckCircleOutlined class="text-primary text-xl mr-2" />
              <span class="text-sm">Money Back</span>
            </div>
          </div>

          <!-- Quantity -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
            <div class="flex items-center">
              <button
                @click="decreaseQuantity"
                class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-l-md hover:bg-gray-100"
              >
                <MinusOutlined />
              </button>
              <input
                type="number"
                v-model="quantity"
                min="1"
                :max="product.stock"
                class="w-16 h-10 text-center border-t border-b border-gray-300 focus:outline-none"
              />
              <button
                @click="increaseQuantity"
                class="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-r-md hover:bg-gray-100"
              >
                <PlusOutlined />
              </button>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3 mb-6">
            <button
              class="flex-1 bg-primary text-white py-3 px-6 rounded-md font-medium hover:bg-primary-dark transition-colors flex items-center justify-center"
            >
              <ShoppingCartOutlined class="mr-2" />
              Add to Cart
            </button>
            <button
              class="flex-1 bg-gray-900 text-white py-3 px-6 rounded-md font-medium hover:bg-gray-800 transition-colors"
            >
              Buy Now
            </button>
          </div>

          <div class="flex gap-3 mb-6">
            <button
              class="flex-1 border border-gray-300 py-3 px-6 rounded-md font-medium hover:bg-gray-50 transition-colors flex items-center justify-center"
            >
              <HeartOutlined class="mr-2" />
              Add to Wishlist
            </button>
            <button
              class="flex-1 border border-gray-300 py-3 px-6 rounded-md font-medium hover:bg-gray-50 transition-colors flex items-center justify-center"
            >
              <ShareAltOutlined class="mr-2" />
              Share
            </button>
          </div>
        </div>
      </div>

      <!-- Product Description -->
      <div class="mt-12">
        <div class="border-b border-gray-200">
          <nav class="flex -mb-px">
            <button class="py-4 px-6 border-b-2 border-primary font-medium text-primary">
              Description
            </button>
          </nav>
        </div>

        <div class="py-6">
          <div class="prose max-w-none">
            <p>{{ product.description }}</p>
          </div>
          <div>
            <h2>Thumnail Image</h2>
            <div>
              <img :src="'/storage/' + product.thumbnail_image" :alt="product.name" class="w-64" />
            </div>
          </div>
          <div class="flex gap-3 flex-wrap">
  <img
    v-for="(img, index) in product.gallery_images"
    :key="index"
    :src="img"
    class="w-32 h-32 object-cover border rounded-md"
    :alt="`${product.name} image ${index + 1}`"
  />
</div>


        </div>
      </div>
    </div>
  </UserLayout>
</template>

<style scoped>
.prose {
  max-width: 65ch;
  color: #374151;
}

.prose p {
  margin-bottom: 1.25em;
  line-height: 1.75;
}
</style>
