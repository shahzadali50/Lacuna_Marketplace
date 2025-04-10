<script setup lang="ts">
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { ShoppingCartOutlined, HeartOutlined } from '@ant-design/icons-vue';
import { Row, Col, Card, Button } from 'ant-design-vue';

interface Product {
  id: number;
  title: string;
  price: number;
  image: string;
  category: string;
}

interface PageProps extends Record<string, any> {
  translations: {
    products: {
      title: string;
      subtitle: string;
      view_all: string;
      price: string;
      category: string;
      add_to_cart: string;
      add_to_favorite: string;
    };
  };
}

const page = usePage<PageProps>();

// Sample products data - in a real app, this would come from your API
const products = ref<Product[]>([
  {
    id: 1,
    title: "Premium Leather Wallet",
    price: 5000.99,
    image: "https://picsum.photos/seed/wallet/400/400",
    category: "Accessories"
  },
  {
    id: 2,
    title: "Wireless Noise-Cancelling Headphones",
    price: 249.99,
    image: "https://picsum.photos/seed/headphones/400/400",
    category: "Electronics"
  },
  {
    id: 3,
    title: "Organic Green Tea Collection",
    price: 34.99,
    image: "https://picsum.photos/seed/tea/400/400",
    category: "Food & Beverages"
  },
  {
    id: 4,
    title: "Handcrafted Ceramic Vase",
    price: 59.99,
    image: "https://picsum.photos/seed/vase/400/400",
    category: "Home & Decor"
  },
  {
    id: 5,
    title: "Smart Fitness Watch",
    price: 199.99,
    image: "https://picsum.photos/seed/watch/400/400",
    category: "Electronics"
  },
  {
    id: 6,
    title: "Natural Skincare Set",
    price: 4000.99,
    image: "https://picsum.photos/seed/skincare/400/400",
    category: "Beauty"
  }
]);

// Format price with currency symbol
const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2
  }).format(price);
};
</script>

<template>
  <section class="py-8 sm:py-12 md:py-16 bg-gray-50">
    <div class="container mx-auto px-2 sm:px-4">
      <div class="text-center mb-8 sm:mb-12">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">{{ page.props.translations.products.title }}</h2>
        <p class="text-base sm:text-lg text-gray-600 max-w-3xl mx-auto">{{ page.props.translations.products.subtitle }}</p>
      </div>

      <Row :gutter="[8, 8]">
        <Col :xs="12" :sm="12" :md="8" :lg="8" :xl="6" v-for="product in products" :key="product.id">
          <Card hoverable class="h-full product-card">
            <template #cover>
              <div class="relative h-28 sm:h-32 md:h-40 lg:h-48 overflow-hidden">
                <img :src="product.image" :alt="product.title" class="w-full h-full object-cover">
                <div class="absolute top-1 right-1 bg-white rounded-full px-1.5 py-0.5 text-[10px] sm:text-xs font-medium text-gray-800">
                  {{ product.category }}
                </div>
              </div>
            </template>
            <div class="">
              <h3 class="text-[15px] sm:text-xl  font-semibold text-gray-900 mb-1 line-clamp-2">{{ product.title }}</h3>
              <div class="flex justify-between items-center">
                <span class="text-xs sm:text-sm md:text-base font-bold text-indigo-600">{{ formatPrice(product.price) }}</span>
                <div class="flex gap-1">
                  <Button type="primary" shape="circle" size="small" class="flex items-center justify-center !bg-indigo-600 !border-indigo-600 hover:!bg-indigo-700 !w-6 !h-6">
                    <template #icon><shopping-cart-outlined /></template>
                  </Button>
                  <Button type="primary" shape="circle" size="small" class="flex items-center justify-center !bg-pink-600 !border-pink-600 hover:!bg-pink-700 !w-6 !h-6">
                    <template #icon><heart-outlined /></template>
                  </Button>

                </div>
              </div>
            </div>
          </Card>
        </Col>
      </Row>

      <div class="text-center mt-8 sm:mt-12">
        <Button type="primary" size="middle" class="!bg-indigo-600 !border-indigo-600 hover:!bg-indigo-700">
          <a href="/marketplace">{{ page.props.translations.products.view_all }}</a>
        </Button>
        <Button class="btn-primary">Primary</Button>
      </div>
    </div>
  </section>
</template>

