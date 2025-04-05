<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import SaleManagement from '@/components/admin/SaleManagement.vue';
import { TaobaoCircleOutlined,DollarCircleOutlined ,ShoppingCartOutlined ,BranchesOutlined ,CiOutlined } from '@ant-design/icons-vue';
import DashboardCard from '@/components/admin/DashboardCard.vue';
import dayjs from 'dayjs';
import isBetween from 'dayjs/plugin/isBetween';
import OrderManagement from '@/components/admin/OrderManagement.vue';

dayjs.extend(isBetween);

const props = defineProps({
    brands: Number,
    totalProduct: Number,
    category: Number,
    orders: Array,
    products: Array
});
</script>

<template>
    <AdminLayout>
        <Head title="Dashboard" />
        <div class="mb-5" style="background-color: #ececec; padding: 20px">
            <a-row :gutter="[16, 16]">
                <a-col :lg="24" :md="24" :sm="24" :xs="24">
                    <h2 class="text-2xl">Welcome to Dashboard</h2>
                </a-col>
                <a-col :lg="6" :sm="12" :xs="24">
                    <DashboardCard
                        title="Total Sale"
                        :value="orders.reduce((sum, order) => sum + Number(order.total_price), 0)"
                       :icon="DollarCircleOutlined" bgColor="bg-yellow-800"
                    />
                </a-col>
                <a-col :lg="6" :sm="12" :xs="24">
                    <DashboardCard title="Total Orders"
                        :value="orders.length"
                     :icon="ShoppingCartOutlined"
                    bgColor="bg-green-700" />
                </a-col>
                <a-col :lg="6" :sm="12" :xs="24">
                    <DashboardCard
                        title="Brands"
                        :value="brands"
                        :icon="BranchesOutlined"
                        bgColor="bg-sky-900"
                    />
                </a-col>
                <a-col :lg="6" :sm="12" :xs="24">
                    <DashboardCard
                        title="Products"
                        :value="totalProduct"
                        :icon="CiOutlined "
                        bgColor="bg-red-700"
                    />
                </a-col>
            </a-row>
        </div>

        <SaleManagement :orders="props.orders" />
        <OrderManagement :orders="props.orders" />
        <!-- Latest Products -->
        <div class="mt-5" style="background-color: #ececec; padding: 20px">
            <a-row :gutter="[16, 16]">
                <a-col :lg="24" :md="24" :sm="24" :xs="24">
                    <h2 class="text-2xl">Latest Products</h2>
                </a-col>
                <a-col :lg="6" :sm="12" :xs="24" v-for="product in props.products" :key="product.id">
                    <DashboardCard
                        :title="'Stock (' + product.total_stock + ')' "
                        :value="product.name"
                        :icon="TaobaoCircleOutlined"
                        bgColor="bg-sky-900"
                    />
                </a-col>
            </a-row>
        </div>
    </AdminLayout>
</template>
