<template>
    <header class="bg-white shadow fixed top-0 left-0 right-0 z-50">
        <div class="container mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="/" class="flex items-center">
                        <img src="\assets\images\Logo-LaCuna-JP-azul.fw.png" alt="Logo" class="h-8 w-auto" />
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden md:flex items-center space-x-8">

                    <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                    Home
                    </Link>
                    <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                    Products
                    </Link>
                    <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                    Categories
                    </Link>
                    <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                    Brands
                    </Link>
                </nav>


                <!-- Right Side Actions -->
                <div class="flex items-center space-x-4">
                    <!-- Search -->
                    <div class="hidden md:block">
                        <a-input-search placeholder="Search products..." class="w-64" @search="handleSearch" />
                    </div>

                    <!-- Language Switcher - Visible on both mobile and desktop -->
                    <div>
                        <LanguageSwitcher />
                    </div>

                    <!-- User Actions - Only visible on desktop -->
                    <div class="hidden md:flex items-center space-x-2">
                        <a-button type="text" shape="circle">
                            <template #icon>
                                <HeartOutlined />
                            </template>
                        </a-button>
                        <a-button type="text" shape="circle">
                            <template #icon>
                                <ShoppingCartOutlined />
                            </template>
                        </a-button>
                        <a-dropdown v-if="isAuthenticated">
                            <a-button type="text" shape="circle">
                                <template #icon>
                                    <UserOutlined />
                                </template>
                            </a-button>
                            <template #overlay>
                                <a-menu>
                                    <a-menu-item key="profile">
                                        <Link :href="route('dashboard')" class="text-gray-600 hover:text-gray-900">
                                        Profile
                                        </Link>

                                    </a-menu-item>
                                    <a-menu-divider />
                                    <a-menu-item key="logout">

                                        <a href="#" @click.prevent="handleLogout">Logout</a>
                                    </a-menu-item>
                                </a-menu>
                            </template>
                        </a-dropdown>
                        <div v-else class="flex space-x-2">
                            <a-dropdown>
                                <a-button type="text" shape="circle">
                                    <template #icon>
                                        <UserOutlined />
                                    </template>
                                </a-button>
                                <template #overlay>
                                    <a-menu>
                                        <a-menu-item key="login">
                                            <Link :href="route('login')" class="text-gray-600 hover:text-gray-900">
                                            Login
                                            </Link>
                                        </a-menu-item>
                                        <a-menu-item key="register">
                                            <Link :href="route('register')" class="text-gray-600 hover:text-gray-900">
                                            Register
                                            </Link>
                                        </a-menu-item>
                                    </a-menu>
                                </template>
                            </a-dropdown>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <a-button type="text" shape="circle" @click="toggleMobileMenu">
                            <template #icon>
                                <MenuOutlined v-if="!mobileMenuOpen" />
                                <CloseOutlined v-else />
                            </template>
                        </a-button>
                    </div>
                </div>
            </div>

            <!-- Mobile Search -->
            <div class="md:hidden mt-4">
                <a-input-search placeholder="Search products..." class="w-full" @search="handleSearch" />
            </div>
        </div>
    </header>

    <!-- Mobile Sidebar -->
    <a-drawer :visible="mobileMenuOpen" placement="left" :closable="true" @close="toggleMobileMenu" width="280">
        <div class="flex flex-col h-full">
            <!-- Navigation Links -->
            <nav class="flex-1">
                <a-menu mode="vertical">
                    <a-menu-item key="home">
                        <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                        Home
                        </Link>
                    </a-menu-item>
                    <a-menu-item key="products">
                        <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                        Products
                        </Link>
                    </a-menu-item>
                    <a-menu-item key="categories">
                        <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                        Categories
                        </Link>
                    </a-menu-item>
                    <a-menu-item key="brands">
                        <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                        Brands
                        </Link>
                    </a-menu-item>
                    <a-menu-item key="about">
                        <Link :href="route('home')" class="text-gray-600 hover:text-gray-900">
                        About
                        </Link>
                    </a-menu-item>
                </a-menu>
            </nav>
        </div>
    </a-drawer>

    <!-- Mobile Bottom Navigation -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50">
        <div class="flex justify-around items-center h-16">

            <a href="/" class="flex flex-col items-center">
                <HomeOutlined class="text-xl" />
                <span class="text-xs mt-1">Home</span>
            </a>
            <a href="/categories" class="flex flex-col items-center">
                <AppstoreOutlined class="text-xl" />
                <span class="text-xs mt-1">Categories</span>
            </a>
            <a href="/favorites" class="flex flex-col items-center">
                <HeartOutlined class="text-xl" />
                <span class="text-xs mt-1">Favorites</span>
            </a>
            <a href="/cart" class="flex flex-col items-center">
                <ShoppingCartOutlined class="text-xl" />
                <span class="text-xs mt-1">Cart</span>
            </a>
            <a-dropdown>
                <div class="flex flex-col items-center">
                    <UserOutlined class="text-xl" />
                    <span class="text-xs mt-1">Account</span>
                </div>
                <template #overlay>
                    <a-menu>
                        <a-menu-item v-if="!isAuthenticated" key="login">
                            <a href="/login">Login</a>
                        </a-menu-item>
                        <a-menu-item v-if="!isAuthenticated" key="register">
                            <a href="/register">Register</a>
                        </a-menu-item>
                        <template v-else>
                            <a-menu-item key="profile">
                                <Link :href="route('dashboard')" class="text-gray-600 hover:text-gray-900">
                                Profile
                                </Link>
                            </a-menu-item>

                            <a-menu-divider />
                            <a-menu-item key="logout">
                                <a href="#" @click.prevent="handleLogout">Logout</a>
                            </a-menu-item>
                        </template>
                    </a-menu>
                </template>
            </a-dropdown>
        </div>
    </div>

    <!-- Spacer for fixed header and bottom nav -->
    <div class="h-24 md:h-20"></div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { Link } from "@inertiajs/vue3";
import {
    MenuOutlined,
    CloseOutlined,
    HeartOutlined,
    ShoppingCartOutlined,
    UserOutlined,
    HomeOutlined,
    AppstoreOutlined
} from '@ant-design/icons-vue';

const { props } = usePage();
const mobileMenuOpen = ref(false);
const searchQuery = ref('');
// const currentLocale = computed(() => usePage().props.locale || 'en');
const isAuthenticated = computed(() => props.auth?.user);

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.visit(route('search', { query: searchQuery.value }));
    }
};

const handleLogout = () => {
    router.post(route('logout'));
};
</script>

<style scoped>
.ant-drawer-body {
    padding: 0;
}

.ant-menu-item {
    margin: 0 !important;
}

.ant-menu-item a {
    color: #666;
}

.ant-menu-item-selected a {
    color: #1890ff;
}
</style>
