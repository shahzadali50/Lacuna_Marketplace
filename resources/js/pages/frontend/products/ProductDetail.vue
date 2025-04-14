<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import UserLayout from '@/layouts/UserLayout.vue';
import {
  StarFilled,
  ShoppingCartOutlined,
  HeartOutlined,
  ShareAltOutlined,
  CheckCircleOutlined,
  CarOutlined,
  SafetyCertificateOutlined,
  ArrowLeftOutlined,
  ArrowRightOutlined,
  MinusOutlined,
  PlusOutlined,
  EnvironmentOutlined,
  ClockCircleOutlined,
  UserOutlined,
  StarOutlined
} from '@ant-design/icons-vue';
import { message } from 'ant-design-vue';

interface PageProps extends Record<string, any> {
  translations: {
    product: {
      add_to_cart: string;
      buy_now: string;
      add_to_wishlist: string;
      share: string;
      quantity: string;
      description: string;
      specifications: string;
      reviews: string;
      related_products: string;
      in_stock: string;
      out_of_stock: string;
      free_shipping: string;
      secure_payment: string;
      money_back: string;
      seller_info: string;
      contact_seller: string;
      view_more: string;
      view_less: string;
      rating: string;
      reviews_count: string;
      write_review: string;
      your_review: string;
      submit: string;
      cancel: string;
      all_reviews: string;
      verified_purchase: string;
      helpful: string;
      report: string;
      sort_by: string;
      newest: string;
      highest_rated: string;
      lowest_rated: string;
      most_helpful: string;
    };
  };
  productId: number;
}

const page = usePage<PageProps>();
const translations = computed(() => page.props.translations?.product || {});
const productId = computed(() => page.props.productId);

// Product data (in a real app, this would come from the backend)
const product = ref({
  id: 1,
  name: 'Premium Wireless Headphones',
  price: 129.99,
  originalPrice: 199.99,
  discount: 35,
  rating: 4.5,
  reviewsCount: 128,
  inStock: true,
  stockCount: 42,
  description: 'Experience crystal-clear sound with our premium wireless headphones. Featuring active noise cancellation, 30-hour battery life, and comfortable ear cushions for extended listening sessions. Perfect for music lovers, gamers, and professionals who demand high-quality audio.',
  features: [
    'Active Noise Cancellation',
    '30-hour battery life',
    'Bluetooth 5.0 connectivity',
    'Built-in microphone',
    'Touch controls',
    'Foldable design',
    'Compatible with all devices'
  ],
  specifications: {
    'Brand': 'AudioPro',
    'Model': 'WH-1000XM4',
    'Color': 'Midnight Blue',
    'Connectivity': 'Bluetooth 5.0',
    'Battery Life': 'Up to 30 hours',
    'Charging Time': '3 hours',
    'Weight': '250g',
    'Warranty': '2 years'
  },
  thumbnail_image: '/assets/images/products/headphones-1.jpg',
  gallery_images: [
      "https://picsum.photos/seed/watch1/400/400",
      "https://picsum.photos/seed/watch2/400/400",
      "https://picsum.photos/seed/watch3/400/400"
    ],
  seller: {
    id: 1,
    name: 'AudioPro Store',
    rating: 4.8,
    reviewsCount: 1250,
    verified: true,
    joinedDate: '2020-05-15',
    location: 'New York, USA',
    responseRate: '98%',
    responseTime: '2 hours'
  },
  reviews: [
    {
      id: 1,
      user: 'John D.',
      rating: 5,
      date: '2023-06-15',
      verified: true,
      helpful: 12,
      title: 'Excellent sound quality',
      content: 'These headphones exceeded my expectations. The sound quality is incredible, and the noise cancellation works perfectly. Battery life is also impressive - I can use them for days without charging.',
      images: ['/assets/images/reviews/review-1-1.jpg', '/assets/images/reviews/review-1-2.jpg']
    },
    {
      id: 2,
      user: 'Sarah M.',
      rating: 4,
      date: '2023-05-22',
      verified: true,
      helpful: 8,
      title: 'Great headphones with minor issues',
      content: 'Overall, these are excellent headphones. The sound is amazing, and they\'re very comfortable. The only issue I have is that the touch controls can be a bit finicky sometimes.',
      images: []
    },
    {
      id: 3,
      user: 'Michael R.',
      rating: 5,
      date: '2023-04-10',
      verified: true,
      helpful: 15,
      title: 'Best headphones I\'ve ever owned',
      content: 'I\'ve tried many headphones, but these are by far the best. The noise cancellation is incredible, and the sound quality is outstanding. Worth every penny!',
      images: ['/assets/images/reviews/review-3-1.jpg']
    }
  ],
  relatedProducts: [
    {
      id: 2,
      name: 'Wireless Earbuds',
      price: 79.99,
      thumbnail_image: '/assets/images/products/earbuds-1.jpg',
      gallery_images: [
      "https://picsum.photos/seed/headphones1/400/400",
      "https://picsum.photos/seed/headphones2/400/400",
      "https://picsum.photos/seed/headphones3/400/400"
    ],
      rating: 4.3
    },
    {
      id: 3,
      name: 'Bluetooth Speaker',
      price: 89.99,
      thumbnail_image: '/assets/images/products/speaker-1.jpg',
      gallery_images: [
      "https://picsum.photos/seed/tea1/400/400",
      "https://picsum.photos/seed/tea2/400/400",
      "https://picsum.photos/seed/tea3/400/400"
    ],
      rating: 4.7
    },
    {
      id: 4,
      name: 'Audio Interface',
      price: 149.99,
      thumbnail_image: '/assets/images/products/interface-1.jpg',
      gallery_images: [
      "https://picsum.photos/seed/vase1/400/400",
      "https://picsum.photos/seed/vase2/400/400",
      "https://picsum.photos/seed/vase3/400/400"
    ],
      rating: 4.6
    },
    {
      id: 5,
      name: 'Microphone',
      price: 69.99,
      thumbnail_image: '/assets/images/products/mic-1.jpg',
      gallery_images: [
      "https://picsum.photos/seed/watch1/400/400",
      "https://picsum.photos/seed/watch2/400/400",
      "https://picsum.photos/seed/watch3/400/400"
    ],
      rating: 4.4
    }
  ]
});

// UI state
const currentImageIndex = ref(0);
const quantity = ref(1);
const showFullDescription = ref(false);
const showAllSpecifications = ref(false);
const showAllReviews = ref(false);
const reviewSort = ref('newest');
const reviewText = ref('');
const reviewRating = ref(5);
const showReviewForm = ref(false);

// Fetch product data based on ID
onMounted(() => {
  // In a real app, this would fetch the product data from the backend
  console.log(`Loading product with ID: ${productId.value}`);
  // For now, we'll just use the hardcoded data
  // In a real app, you would make an API call here
});

// Methods
const nextImage = () => {
  currentImageIndex.value = (currentImageIndex.value + 1) % product.value.gallery_images.length;
};

const prevImage = () => {
  currentImageIndex.value = currentImageIndex.value === 0
    ? product.value.gallery_images.length - 1
    : currentImageIndex.value - 1;
};

const increaseQuantity = () => {
  if (quantity.value < product.value.stockCount) {
    quantity.value++;
  }
};

const decreaseQuantity = () => {
  if (quantity.value > 1) {
    quantity.value--;
  }
};

const addToCart = () => {
  message.success('Product added to cart');
  // In a real app, this would add the product to the cart
};

const buyNow = () => {
  message.success('Redirecting to checkout');
  // In a real app, this would redirect to checkout
};

const addToWishlist = () => {
  message.success('Product added to wishlist');
  // In a real app, this would add the product to the wishlist
};

const shareProduct = () => {
  message.success('Product link copied to clipboard');
  // In a real app, this would share the product
};

const toggleDescription = () => {
  showFullDescription.value = !showFullDescription.value;
};

const toggleSpecifications = () => {
  showAllSpecifications.value = !showAllSpecifications.value;
};

const toggleReviews = () => {
  showAllReviews.value = !showAllReviews.value;
};

const submitReview = () => {
  if (!reviewText.value) {
    message.error('Please enter your review');
    return;
  }

  message.success('Review submitted successfully');
  reviewText.value = '';
  reviewRating.value = 5;
  showReviewForm.value = false;

  // In a real app, this would submit the review to the backend
};

const contactSeller = () => {
  message.success('Redirecting to seller chat');
  // In a real app, this would redirect to the seller chat
};

// Computed properties
const displayedDescription = computed(() => {
  if (showFullDescription.value) {
    return product.value.description;
  }
  return product.value.description.substring(0, 200) + '...';
});

const displayedSpecifications = computed(() => {
  const specs = Object.entries(product.value.specifications);
  if (showAllSpecifications.value) {
    return specs;
  }
  return specs.slice(0, 4);
});

const displayedReviews = computed(() => {
  const reviews = [...product.value.reviews];

  // Sort reviews
  switch (reviewSort.value) {
    case 'newest':
      reviews.sort((a, b) => new Date(b.date).getTime() - new Date(a.date).getTime());
      break;
    case 'highest_rated':
      reviews.sort((a, b) => b.rating - a.rating);
      break;
    case 'lowest_rated':
      reviews.sort((a, b) => a.rating - b.rating);
      break;
    case 'most_helpful':
      reviews.sort((a, b) => b.helpful - a.helpful);
      break;
  }

  if (!showAllReviews.value) {
    return reviews.slice(0, 2);
  }

  return reviews;
});

const averageRating = computed(() => {
  const sum = product.value.reviews.reduce((acc, review) => acc + review.rating, 0);
  return (sum / product.value.reviews.length).toFixed(1);
});

const ratingDistribution = computed(() => {
  const distribution = [0, 0, 0, 0, 0];
  product.value.reviews.forEach(review => {
    distribution[review.rating - 1]++;
  });
  return distribution;
});
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
          <a href="/products/category" class="hover:text-primary">Audio</a>
          <span class="mx-2">/</span>
          <span class="text-gray-700">{{ product.name }}</span>
        </div>
      </div>
    </div>

    <!-- Product Detail Section -->
    <div class="container mx-auto px-4 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div class="relative">
          <div class="aspect-square rounded-lg overflow-hidden bg-gray-100">
            <img
              :src="product.gallery_images[currentImageIndex]"
              :alt="product.name"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Image Navigation -->
          <button
            @click="prevImage"
            class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100"
          >
            <ArrowLeftOutlined />
          </button>
          <button
            @click="nextImage"
            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100"
          >
            <ArrowRightOutlined />
          </button>

          <!-- Thumbnails -->
          <div class="flex mt-4 space-x-2 overflow-x-auto">
            <div
              v-for="(image, index) in product.gallery_images"
              :key="index"
              @click="currentImageIndex = index"
              class="w-16 h-16 rounded-md overflow-hidden cursor-pointer border-2"
              :class="currentImageIndex === index ? 'border-primary' : 'border-transparent'"
            >
              <img :src="image" :alt="`${product.name} - ${index + 1}`" class="w-full h-full object-cover" />
            </div>
          </div>
        </div>

        <!-- Product Info -->
        <div>
          <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ product.name }}</h1>

          <!-- Rating -->
          <div class="flex items-center mb-4">
            <div class="flex items-center">
              <StarFilled class="text-yellow-400 mr-1" />
              <span class="font-semibold">{{ product.rating }}</span>
            </div>
            <span class="mx-2 text-gray-400">|</span>
            <a href="#reviews" class="text-primary hover:underline">
              {{ product.reviewsCount }} {{ translations.reviews }}
            </a>
          </div>

          <!-- Price -->
          <div class="mb-6">
            <div class="flex items-center">
              <span class="text-3xl font-bold text-gray-900">${{ product.price }}</span>
              <span class="ml-3 text-lg text-gray-500 line-through">${{ product.originalPrice }}</span>
              <span class="ml-3 bg-red-100 text-red-600 px-2 py-1 rounded text-sm font-medium">
                -{{ product.discount }}%
              </span>
            </div>
          </div>

          <!-- Stock Status -->
          <div class="mb-6">
            <div class="flex items-center">
              <CheckCircleOutlined class="text-green-500 mr-2" />
              <span class="text-gray-700">
                {{ product.inStock ? translations.in_stock : translations.out_of_stock }}
                <span v-if="product.inStock" class="text-gray-500">({{ product.stockCount }} available)</span>
              </span>
            </div>
          </div>

          <!-- Shipping & Payment Info -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
              <CarOutlined class="text-primary text-xl mr-2" />
              <span class="text-sm">{{ translations.free_shipping }}</span>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
              <SafetyCertificateOutlined class="text-primary text-xl mr-2" />
              <span class="text-sm">{{ translations.secure_payment }}</span>
            </div>
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
              <CheckCircleOutlined class="text-primary text-xl mr-2" />
              <span class="text-sm">{{ translations.money_back }}</span>
            </div>
          </div>

          <!-- Quantity -->
          <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">{{ translations.quantity }}</label>
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
                :max="product.stockCount"
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
              @click="addToCart"
              class="flex-1 bg-primary text-white py-3 px-6 rounded-md font-medium hover:bg-primary-dark transition-colors flex items-center justify-center"
            >
              <ShoppingCartOutlined class="mr-2" />
              {{ translations.add_to_cart }}
            </button>
            <button
              @click="buyNow"
              class="flex-1 bg-gray-900 text-white py-3 px-6 rounded-md font-medium hover:bg-gray-800 transition-colors"
            >
              {{ translations.buy_now }}
            </button>
          </div>

          <div class="flex gap-3 mb-6">
            <button
              @click="addToWishlist"
              class="flex-1 border border-gray-300 py-3 px-6 rounded-md font-medium hover:bg-gray-50 transition-colors flex items-center justify-center"
            >
              <HeartOutlined class="mr-2" />
              {{ translations.add_to_wishlist }}
            </button>
            <button
              @click="shareProduct"
              class="flex-1 border border-gray-300 py-3 px-6 rounded-md font-medium hover:bg-gray-50 transition-colors flex items-center justify-center"
            >
              <ShareAltOutlined class="mr-2" />
              {{ translations.share }}
            </button>
          </div>

          <!-- Seller Info -->
          <div class="border border-gray-200 rounded-lg p-4 mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">{{ translations.seller_info }}</h3>
            <div class="flex items-center mb-3">
              <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                <UserOutlined />
              </div>
              <div>
                <div class="font-medium">{{ product.seller.name }}</div>
                <div class="flex items-center text-sm text-gray-500">
                  <StarFilled class="text-yellow-400 mr-1" />
                  <span>{{ product.seller.rating }}</span>
                  <span class="mx-1">•</span>
                  <span>{{ product.seller.reviewsCount }} reviews</span>
                </div>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-2 text-sm mb-3">
              <div class="flex items-center">
                <EnvironmentOutlined class="text-gray-400 mr-2" />
                <span>{{ product.seller.location }}</span>
              </div>
              <div class="flex items-center">
                <ClockCircleOutlined class="text-gray-400 mr-2" />
                <span>Response: {{ product.seller.responseTime }}</span>
              </div>
            </div>
            <button
              @click="contactSeller"
              class="w-full bg-gray-100 text-gray-800 py-2 rounded-md font-medium hover:bg-gray-200 transition-colors"
            >
              {{ translations.contact_seller }}
            </button>
          </div>
        </div>
      </div>

      <!-- Product Details Tabs -->
      <div class="mt-12">
        <div class="border-b border-gray-200">
          <nav class="flex -mb-px">
            <button
              class="py-4 px-6 border-b-2 border-primary font-medium text-primary"
            >
              {{ translations.description }}
            </button>
            <button
              class="py-4 px-6 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
            >
              {{ translations.specifications }}
            </button>
            <button
              class="py-4 px-6 border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300"
            >
              {{ translations.reviews }}
            </button>
          </nav>
        </div>

        <!-- Description Tab -->
        <div class="py-6">
          <div class="prose max-w-none">
            <p>{{ displayedDescription }}</p>
            <button
              @click="toggleDescription"
              class="text-primary hover:underline mt-2"
            >
              {{ showFullDescription ? translations.view_less : translations.view_more }}
            </button>
          </div>

          <!-- Features -->
          <div class="mt-8">
            <h3 class="text-lg font-semibold mb-4">Key Features</h3>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-3">
              <li
                v-for="(feature, index) in product.features"
                :key="index"
                class="flex items-start"
              >
                <CheckCircleOutlined class="text-green-500 mt-1 mr-2 flex-shrink-0" />
                <span>{{ feature }}</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Specifications Tab -->
        <div class="py-6 hidden">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div
              v-for="(value, key) in displayedSpecifications"
              :key="key"
              class="border-b border-gray-200 py-3"
            >
              <div class="font-medium text-gray-700">{{ key }}</div>
              <div class="text-gray-600">{{ value }}</div>
            </div>
          </div>
          <button
            v-if="!showAllSpecifications && Object.keys(product.specifications).length > 4"
            @click="toggleSpecifications"
            class="text-primary hover:underline mt-4"
          >
            {{ translations.view_more }}
          </button>
        </div>

        <!-- Reviews Tab -->
        <div class="py-6 hidden" id="reviews">
          <!-- Review Summary -->
          <div class="flex flex-col md:flex-row gap-8 mb-8">
            <div class="md:w-1/3">
              <div class="text-center p-6 border border-gray-200 rounded-lg">
                <div class="text-4xl font-bold text-gray-900 mb-2">{{ averageRating }}</div>
                <div class="flex justify-center mb-2">
                  <StarFilled v-for="i in 5" :key="i" class="text-yellow-400" />
                </div>
                <div class="text-gray-500">{{ product.reviewsCount }} {{ translations.reviews }}</div>
              </div>

              <!-- Rating Distribution -->
              <div class="mt-4">
                <div
                  v-for="(count, index) in ratingDistribution"
                  :key="index"
                  class="flex items-center mb-2"
                >
                  <div class="w-12 text-sm">{{ 5 - index }} {{ translations.rating }}</div>
                  <div class="flex-1 h-2 bg-gray-200 rounded-full mx-2">
                    <div
                      class="h-2 bg-yellow-400 rounded-full"
                      :style="{ width: `${(count / product.reviewsCount) * 100}%` }"
                    ></div>
                  </div>
                  <div class="w-12 text-sm text-gray-500">{{ count }}</div>
                </div>
              </div>
            </div>

            <div class="md:w-2/3">
              <!-- Review Form -->
              <div v-if="!showReviewForm" class="mb-6">
                <button
                  @click="showReviewForm = true"
                  class="bg-primary text-white py-2 px-4 rounded-md font-medium hover:bg-primary-dark transition-colors"
                >
                  {{ translations.write_review }}
                </button>
              </div>

              <div v-else class="mb-6 p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold mb-3">{{ translations.your_review }}</h3>
                <div class="mb-3">
                  <div class="flex items-center mb-2">
                    <span class="mr-2">{{ translations.rating }}:</span>
                    <div class="flex">
                      <StarOutlined
                        v-for="i in 5"
                        :key="i"
                        class="text-xl cursor-pointer"
                        :class="i <= reviewRating ? 'text-yellow-400' : 'text-gray-300'"
                        @click="reviewRating = i"
                      />
                    </div>
                  </div>
                </div>
                <textarea
                  v-model="reviewText"
                  rows="4"
                  class="w-full border border-gray-300 rounded-md p-2 focus:outline-none focus:ring-2 focus:ring-primary"
                  :placeholder="translations.write_review"
                ></textarea>
                <div class="flex justify-end mt-3 gap-2">
                  <button
                    @click="showReviewForm = false"
                    class="py-2 px-4 border border-gray-300 rounded-md font-medium hover:bg-gray-50 transition-colors"
                  >
                    {{ translations.cancel }}
                  </button>
                  <button
                    @click="submitReview"
                    class="py-2 px-4 bg-primary text-white rounded-md font-medium hover:bg-primary-dark transition-colors"
                  >
                    {{ translations.submit }}
                  </button>
                </div>
              </div>

              <!-- Review Sort -->
              <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold">{{ translations.all_reviews }}</h3>
                <div class="flex items-center">
                  <span class="text-sm text-gray-500 mr-2">{{ translations.sort_by }}:</span>
                  <select
                    v-model="reviewSort"
                    class="border border-gray-300 rounded-md py-1 px-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"
                  >
                    <option value="newest">{{ translations.newest }}</option>
                    <option value="highest_rated">{{ translations.highest_rated }}</option>
                    <option value="lowest_rated">{{ translations.lowest_rated }}</option>
                    <option value="most_helpful">{{ translations.most_helpful }}</option>
                  </select>
                </div>
              </div>

              <!-- Reviews List -->
              <div class="space-y-6">
                <div
                  v-for="review in displayedReviews"
                  :key="review.id"
                  class="border-b border-gray-200 pb-6"
                >
                  <div class="flex justify-between mb-2">
                    <div class="font-medium">{{ review.user }}</div>
                    <div class="text-sm text-gray-500">{{ review.date }}</div>
                  </div>
                  <div class="flex items-center mb-2">
                    <div class="flex">
                      <StarFilled
                        v-for="i in 5"
                        :key="i"
                        class="text-yellow-400"
                        :class="i <= review.rating ? 'text-yellow-400' : 'text-gray-300'"
                      />
                    </div>
                    <span class="ml-2 font-medium">{{ review.title }}</span>
                  </div>
                  <div class="mb-3">
                    <p>{{ review.content }}</p>
                  </div>
                  <div v-if="review.images.length > 0" class="flex gap-2 mb-3">
                    <div
                      v-for="(image, index) in review.images"
                      :key="index"
                      class="w-16 h-16 rounded-md overflow-hidden"
                    >
                      <img :src="image" :alt="`Review image ${index + 1}`" class="w-full h-full object-cover" />
                    </div>
                  </div>
                  <div class="flex items-center text-sm text-gray-500">
                    <div v-if="review.verified" class="flex items-center mr-4">
                      <CheckCircleOutlined class="text-green-500 mr-1" />
                      <span>{{ translations.verified_purchase }}</span>
                    </div>
                    <button class="mr-4 hover:text-primary">
                      <span>{{ translations.helpful }} ({{ review.helpful }})</span>
                    </button>
                    <button class="hover:text-primary">
                      <span>{{ translations.report }}</span>
                    </button>
                  </div>
                </div>
              </div>

              <button
                v-if="!showAllReviews && product.reviews.length > 2"
                @click="toggleReviews"
                class="text-primary hover:underline mt-4"
              >
                {{ translations.view_more }}
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Products -->
      <div class="mt-16">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ translations.related_products }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
          <div
            v-for="product in product.relatedProducts"
            :key="product.id"
            class="group"
          >
            <div class="aspect-square rounded-lg overflow-hidden bg-gray-100 mb-3">
              <img :src="product.thumbnail_image" :alt="product.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
            </div>
            <h3 class="font-medium text-gray-900 mb-1 group-hover:text-primary transition-colors">
              {{ product.name }}
            </h3>
            <div class="flex items-center justify-between">
              <span class="font-bold text-gray-900">${{ product.price }}</span>
              <div class="flex items-center">
                <StarFilled class="text-yellow-400 mr-1" />
                <span class="text-sm text-gray-500">{{ product.rating }}</span>
              </div>
            </div>
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
