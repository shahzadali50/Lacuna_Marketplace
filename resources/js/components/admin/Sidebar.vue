<script setup lang="ts">
import { ref, watch, computed, defineProps } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
    UserOutlined,
    DatabaseOutlined,
    AppstoreOutlined,
    TrademarkCircleOutlined,
    ShoppingOutlined,
    ShoppingCartOutlined,
    FileTextOutlined,
    OrderedListOutlined
} from '@ant-design/icons-vue';

defineProps<{ collapsed: boolean }>();

const selectedKeys = ref<string[]>([]);
const page = usePage();
const currentPath = computed(() => page.url);

// Watch URL changes to update active keys
watch(currentPath, (newUrl) => {
    selectedKeys.value = [newUrl];
}, { immediate: true });
</script>

<template>
    <a-layout-sider :collapsed="collapsed" :trigger="null" collapsible>
        <div class="logo">
            <h3 class="mb-0" v-if="!collapsed">POS Application</h3>
        </div>
        <div class="scrollbar" id="style-2">
        <a-menu v-model:selectedKeys="selectedKeys" class="force-overflow">
            <!-- Dashboard -->
            <a-menu-item key="1" :class="{ 'active': currentPath.startsWith(route('user.dashboard', {}, false)) }">
                <Link :href="route('user.dashboard')">
                <DatabaseOutlined />
                <span>Dashboard</span>
                </Link>
            </a-menu-item>

            <!-- Categories -->
            <a-menu-item key="2"  :class="{ 'active': currentPath.startsWith(route('user.categories', {}, false)) }">
                <Link :href="route('user.categories')">
                <AppstoreOutlined  />
                <span>Categories</span>
                </Link>
            </a-menu-item>

            <!-- Brands -->
            <a-menu-item key="3" :class="{ 'active': currentPath.startsWith(route('user.brands', {}, false)) }">
                <Link :href="route('user.brands')"
                    >
                <TrademarkCircleOutlined  />
                <span>Brands</span>
                </Link>
            </a-menu-item>

            <!-- Products -->
            <a-menu-item key="4" :class="{ 'active': currentPath.startsWith(route('user.products', {}, false)) }">
                <Link :href="route('user.products')"
                    >
                <ShoppingOutlined />
                <span>Products</span>
                </Link>
            </a-menu-item>

            <!-- Purchase Products -->
            <a-menu-item key="5" :class="{ 'active': currentPath.startsWith(route('user.purchase.product.list', {}, false)) }">
                <Link :href="route('user.purchase.product.list')"
                    >
                <ShoppingCartOutlined  />
                <span>Purchase Products</span>
                </Link>
            </a-menu-item>

            <!-- Get Orders -->
            <a-menu-item key="6" :class="{ 'active': currentPath.startsWith(route('user.order.create', {}, false)) }">
                <Link :href="route('user.order.create')"
                    >
                <FileTextOutlined  />
                <span>Get Orders</span>
                </Link>
            </a-menu-item>

            <!-- Order List -->
            <a-menu-item key="7"   :class="{ 'active': currentPath.startsWith(route('user.order.list', {}, false)) }">
                <Link :href="route('user.order.list')"
                  >
                <OrderedListOutlined  />
                <span>Order List</span>
                </Link>
            </a-menu-item>
            <!-- Order List -->



            <!-- Profile -->
            <a-menu-item key="8"  :class="{ 'active': currentPath.startsWith(route('profile.edit', {}, false)) }">
                <Link :href="route('profile.edit')"
                   >
                <UserOutlined />
                <span>Profile</span>
                </Link>
            </a-menu-item>
        </a-menu>
        </div>
    </a-layout-sider>
</template>

<style scoped>
.logo {
    margin-top: 10px;
    height: 42px;
    color: rgb(255, 255, 255) !important ;
    background-color: #232330 !important ;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;

}
</style>
