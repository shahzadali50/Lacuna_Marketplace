<script setup lang="ts">
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { ShoppingCartOutlined, HeartOutlined } from '@ant-design/icons-vue';
import { Row, Col, Card, Button } from 'ant-design-vue';
import { router } from '@inertiajs/vue3';

interface Product {
  id: number;
  title: string;
  price: number;
  thumbnail_image: string;
  gallery_images: string[];
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
    thumbnail_image: "https://picsum.photos/seed/wallet/400/400",
    gallery_images: [
      "https://picsum.photos/seed/wallet1/400/400",
      "https://picsum.photos/seed/wallet2/400/400",
      "https://picsum.photos/seed/wallet3/400/400"
    ],
    category: "Accessories"
  },
  {
    id: 2,
    title: "Wireless Noise-Cancelling Headphones",
    price: 249.99,
    thumbnail_image: "https://picsum.photos/seed/headphones/400/400",
    gallery_images: [
      "https://picsum.photos/seed/headphones1/400/400",
      "https://picsum.photos/seed/headphones2/400/400",
      "https://picsum.photos/seed/headphones3/400/400"
    ],
    category: "Electronics"
  },
  {
    id: 3,
    title: "Organic Green Tea Collection",
    price: 34.99,
    thumbnail_image: "https://picsum.photos/seed/tea/400/400",
    gallery_images: [
      "https://picsum.photos/seed/tea1/400/400",
      "https://picsum.photos/seed/tea2/400/400",
      "https://picsum.photos/seed/tea3/400/400"
    ],
    category: "Food & Beverages"
  },
  {
    id: 4,
    title: "Handcrafted Ceramic Vase",
    price: 59.99,
    thumbnail_image: "https://picsum.photos/seed/vase/400/400",
    gallery_images: [
      "https://picsum.photos/seed/vase1/400/400",
      "https://picsum.photos/seed/vase2/400/400",
      "https://picsum.photos/seed/vase3/400/400"
    ],
    category: "Home & Decor"
  },
  {
    id: 5,
    title: "Smart Fitness Watch",
    price: 199.99,
    thumbnail_image: "https://picsum.photos/seed/watch/400/400",
    gallery_images: [
      "https://picsum.photos/seed/watch1/400/400",
      "https://picsum.photos/seed/watch2/400/400",
      "https://picsum.photos/seed/watch3/400/400"
    ],
    category: "Electronics"
  },
  {
    id: 6,
    title: "Natural Skincare Set",
    price: 4000.99,
    thumbnail_image: "https://picsum.photos/seed/skincare/400/400",
    gallery_images: [
      "https://picsum.photos/seed/skincare1/400/400",
      "https://picsum.photos/seed/skincare2/400/400",
      "https://picsum.photos/seed/skincare3/400/400"
    ],
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

// Navigate to product detail page
const goToProductDetail = (productId: number) => {
  router.visit(`/products/${productId}`);
};

// Add to cart function
const addToCart = (e: Event, productId: number) => {
  e.stopPropagation(); // Prevent triggering the card click
  // In a real app, this would add the product to the cart
  console.log(`Adding product ${productId} to cart`);
};

// Add to favorites function
const addToFavorites = (e: Event, productId: number) => {
  e.stopPropagation(); // Prevent triggering the card click
  // In a real app, this would add the product to favorites
  console.log(`Adding product ${productId} to favorites`);
};
</script>

<template>
  <section class=" bg-gray-50 py-14">
    <div class="container mx-auto px-2 sm:px-4">
      <div class="text-center mb-8 sm:mb-12">
        <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3 sm:mb-4">{{ page.props.translations.products.title }}</h2>
        <p class="text-base sm:text-lg text-gray-600 max-w-3xl mx-auto">{{ page.props.translations.products.subtitle }}</p>
      </div>

      <Row :gutter="[8, 8]">
        <Col :xs="12" :sm="12" :md="8" :lg="8" :xl="6" v-for="product in products" :key="product.id">
          <Card
            hoverable
            class="h-full product-card cursor-pointer"
            @click="goToProductDetail(product.id)"
          >
            <template #cover>
              <div class="relative h-28 sm:h-32 md:h-40 lg:h-48 overflow-hidden">
                <img :src="product.thumbnail_image" :alt="product.title" class="w-full h-full object-cover">
                <div class="absolute top-1 right-1 bg-white rounded-full px-1.5 py-0.5 text-[10px] sm:text-xs font-medium text-gray-800">
                  {{ product.category }}
                </div>
              </div>
            </template>
            <div class="">
              <h3 class="text-[15px] sm:text-xl font-semibold text-gray-900 mb-1 line-clamp-2">{{ product.title }}</h3>
              <div class="flex justify-between items-center">
                <span class="text-xs sm:text-sm md:text-base font-bold text-primary">{{ formatPrice(product.price) }}</span>
                <div class="flex gap-1">
                  <Button
                    type="primary"
                    shape="circle"
                    size="small"
                    class="flex items-center justify-center bg-primary !w-6 !h-6"
                    @click="(e) => addToCart(e, product.id)"
                  >
                    <template #icon><shopping-cart-outlined /></template>
                  </Button>
                  <Button
                    type="primary"
                    shape="circle"
                    size="small"
                    class="flex items-center justify-center bg-danger hover:!bg-pink-700 !w-6 !h-6"
                    @click="(e) => addToFavorites(e, product.id)"
                  >
                    <template #icon><heart-outlined /></template>
                  </Button>
                </div>
              </div>
            </div>
          </Card>
        </Col>
      </Row>

      <div class="text-center mt-8 sm:mt-12">
        <Button size="middle" class="btn-primary">
          <a href="/marketplace">{{ page.props.translations.products.view_all }}</a>
        </Button>
      </div>
    </div>
  </section>
</template>

