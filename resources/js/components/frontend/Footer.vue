
<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import {
  FacebookOutlined,
  TwitterOutlined,
  InstagramOutlined,
  LinkedinOutlined,
  CarOutlined,
  SafetyCertificateOutlined,
  CustomerServiceOutlined
} from '@ant-design/icons-vue';
import { message } from 'ant-design-vue';

const email = ref('');

const subscribe = () => {
  if (!email.value) {
    message.error('Please enter your email address');
    return;
  }

  // Here you would typically send the email to your backend
  message.success('Thank you for subscribing!');
  email.value = '';
};
</script>
<template>
  <!-- Top Footer Section -->
  <div class="bg-blue-600 text-white py-8">
    <div class="container mx-auto px-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Free Shipping -->
        <div class="flex items-center">
          <div class="mr-4">
            <CarOutlined class="text-3xl" />
          </div>
          <div>
            <h3 class="text-lg font-semibold">Free Shipping</h3>
            <p class="text-sm text-blue-100">On orders over $50</p>
          </div>
        </div>

        <!-- Secure Payment -->
        <div class="flex items-center">
          <div class="mr-4">
            <SafetyCertificateOutlined class="text-3xl" />
          </div>
          <div>
            <h3 class="text-lg font-semibold">Secure Payment</h3>
            <p class="text-sm text-blue-100">100% secure payment</p>
          </div>
        </div>

        <!-- 24/7 Support -->
        <div class="flex items-center">
          <div class="mr-4">
            <CustomerServiceOutlined class="text-3xl" />
          </div>
          <div>
            <h3 class="text-lg font-semibold">24/7 Support</h3>
            <p class="text-sm text-blue-100">Dedicated support</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Footer -->
  <footer class="bg-gray-100 pt-12 pb-8">
    <div class="container mx-auto px-4">
      <!-- Main Footer Content -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
        <!-- Company Info -->
        <div class="flex flex-col">
          <div class="mb-4">
            <Link :href="route('home')" class="flex items-center">
              <img src="\assets\images\Logo-LaCuna-JP-azul.fw.png" alt="Logo" class="h-8 w-auto" />
            </Link>
          </div>
          <p class="text-gray-600 mb-4">
            Your trusted marketplace for quality products and services.
          </p>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-500 hover:text-gray-700">
              <FacebookOutlined class="text-xl" />
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-700">
              <TwitterOutlined class="text-xl" />
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-700">
              <InstagramOutlined class="text-xl" />
            </a>
            <a href="#" class="text-gray-500 hover:text-gray-700">
              <LinkedinOutlined class="text-xl" />
            </a>
          </div>
        </div>

        <!-- Quick Links -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
          <ul class="space-y-2">
            <li>
              <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">Home</Link>
            </li>
            <li>
              <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">Products</Link>
            </li>
            <li>
              <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">Categories</Link>
            </li>
            <li>
              <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">Brands</Link>
            </li>
            <li>
              <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">About Us</Link>
            </li>
          </ul>
        </div>

        <!-- Customer Service -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Customer Service</h3>
          <ul class="space-y-2">
            <li>
              <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">Contact Us</Link>
            </li>
            <li>
              <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">FAQ</Link>
            </li>
            <li>
              <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">Shipping Info</Link>
            </li>
            <li>
              <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">Returns</Link>
            </li>
            <li>
              <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">Privacy Policy</Link>
            </li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div>
          <h3 class="text-lg font-semibold mb-4">Newsletter</h3>
          <p class="text-gray-600 mb-4">
            Subscribe to our newsletter for updates and exclusive offers.
          </p>
          <div class="flex">
            <a-input
              v-model:value="email"
              placeholder="Your email"
              class="rounded-r-none"
            />
            <a-button type="primary" class="rounded-l-none" @click="subscribe">
              Subscribe
            </a-button>
          </div>
        </div>
      </div>

      <!-- Bottom Footer -->
      <div class="border-t border-gray-200 pt-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
          <div class="mb-4 md:mb-0">
            <p class="text-gray-600 text-sm">
              &copy; {{ new Date().getFullYear() }} Lacuna Marketplace. All rights reserved.
            </p>
          </div>
          <div class="flex space-x-4">
            <Link :href="route('home')" class="text-gray-600 hover:text-gray-900 text-sm">Terms</Link>
            <Link :href="route('home')" class="text-gray-600 hover:text-gray-900 text-sm">Privacy</Link>
            <Link :href="route('home')" class="text-gray-600 hover:text-gray-900 text-sm">Cookies</Link>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>



<style scoped>
.ant-input {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

.ant-btn {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}
</style>
