<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Modal } from 'ant-design-vue';
import dayjs from "dayjs";
import { ref, computed } from 'vue';
const isLoading = ref(false);
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};

// Define table columns
const columns = [
    { title: 'ID', dataIndex: 'id', key: 'id' },
    { title: 'Name', dataIndex: 'name', key: 'name' },
    { title: 'Description', dataIndex: 'description', key: 'description' },
    { title: 'Brand', dataIndex: 'brand', key: 'brand' },
    { title: 'Created At', dataIndex: 'created_at', key: 'created_at' },
    { title: 'Action', dataIndex: 'action', key: 'action' },
];

// Receive brands as a prop from Inertia
const props = defineProps({
    products: Object,
    brands: Array,
});

// Convert brands to Ant Design Select options
const brandOptions = computed(() => {
    return props.brands?.map((brand: any) => ({ value: brand.id, label: brand.name })) || [];
});

// Search filter function
const filterOption = (input: string, option: any) => {
    return option.label.toLowerCase().includes(input.toLowerCase());
};
const form = useForm({});
const deleteProduct = (id: number) => {
    Modal.confirm({
        title: 'Are you sure you want to delete this Product?',
        content: 'This action cannot be undone.',
        okText: 'Yes, Delete',
        okType: 'danger',
        cancelText: 'Cancel',
        onOk() {
            isLoading.value = true;
            form.delete(route('user.product.delete', id), {
                onSuccess: () => {
                },
                onFinish: () => {
                    isLoading.value = false;
                }
            });
        },
    });
};
const isAddProductModalVisible = ref(false);
const isEditModalVisible = ref(false);
const isPurchaseModalVisible = ref(false);
const selectedProductName = ref("");
const addProductForm = useForm({
    name: '',
    description: '',
    brand_id: null,
});
const editForm = useForm({
    id: null,
    name: '',
    description: '',
});
const purchaseDetailForm = useForm({
    id: null,
    purchase_price: '',
    sale_price: '',
    stock: '',
    product_id: null,
    description: '',
});
const openAddProductModal = () => {

    isAddProductModalVisible.value = true;
}
const openEditModal = (product: any) => {
    editForm.id = product.id;
    editForm.name = product.name;
    editForm.description = product.description;
    isEditModalVisible.value = true;
}
const openPurchaseDetailModal = (product: any) => {
    purchaseDetailForm.id = product.id;
    isPurchaseModalVisible.value = true;
    selectedProductName.value = product.name;
    purchaseDetailForm.product_id = product.id;
}
// saveProduct
const saveProduct = () => {
    isLoading.value = true;
    addProductForm.post(route('user.product.store'), {
        onSuccess: () => {
            addProductForm.reset();
            isAddProductModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};
// Update brand
const updateProduct = () => {
    isLoading.value = true;
    editForm.put(route('user.product.update', editForm.id), {
        onSuccess: () => {
            isEditModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};
const savePurchaseProductDetail = () => {
    isLoading.value = true;
    purchaseDetailForm.post(route('user.purchase.product.detail.store'), {
        onSuccess: () => {
            purchaseDetailForm.reset();
            isPurchaseModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

</script>

<template>
    <div v-if="isLoading" class="loading-overlay">
        <a-spin size="large" />
    </div>
    <AdminLayout>

        <Head title="Product List" />
        <a-row>
            <a-col :span="24">
                <div class="bg-white rounded-lg p-4 shadow-md responsive-table">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Product List </h2>

                        <div>
                            <a-button class="mx-2" type="default" @click="openAddProductModal()">
                                Add Product
                            </a-button>
                            <Link :href="route('user.product-log')">
                            <a-button class="mx-2" type="default">Product Logs</a-button>
                            </Link>
                        </div>
                    </div>

                    <!-- Display table -->
                    <a-table v-if="products" :columns="columns" :data-source="products.data" rowKey="id" bordered>
                        <template #bodyCell="{ column, record, index }">
                            <template v-if="column.dataIndex === 'id'">
                                {{ index + 1 }}
                            </template>
                            <template v-if="column.dataIndex === 'name'">
                                {{ record.name }}
                            </template>
                            <template v-if="column.dataIndex === 'description'">
                                {{ record.description ? record.description : 'N/A' }}
                            </template>
                            <template v-if="column.dataIndex === 'brand'">
                                {{ record.brand.slug }}
                            </template>

                            <template v-else-if="column.dataIndex === 'created_at'">
                                {{ formatDate(record.created_at) }}
                            </template>
                            <template v-else-if="column.dataIndex === 'action'">
                                <a-tooltip placement="top">
                                    <template #title>Delete</template>
                                    <a-button type="link" @click="deleteProduct(record.id)"><i
                                            class="fa fa-trash text-red-500" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>Edit</template>
                                    <a-button type="link" @click="openEditModal(record)"><i
                                            class="fa fa-pencil-square-o text-s text-green-500"
                                            aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>Add Purchase Details</template>
                                    <a-button type="link" @click="openPurchaseDetailModal(record)"><i
                                            class="fa fa-shopping-cart text-emerald-950"
                                            aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>Purchase Product List</template>
                                    <Link :href="route('user.related.purchase.product.list', record.slug)"
                                        class="text-blue-500 hover:underline"><i class="fa fa-list text-slate-800"
                                        aria-hidden="true"></i></Link>
                                </a-tooltip>
                            </template>

                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
        <a-modal v-model:open="isAddProductModalVisible" title="Add Product"
            @cancel="isAddProductModalVisible = false" :footer="null">
            <form @submit.prevent="saveProduct">
                <div class="mb-4">
                    <label class="block">Brand</label>
                                    <a-select
                    v-model:value="addProductForm.brand_id"
                    show-search
                    placeholder="Select Brand"
                    style="width: 100%"
                    :options="brandOptions"
                    :filter-option="filterOption"
                >
                </a-select>
                    <div v-if="addProductForm.errors.brand_id" class="text-red-500">{{ addProductForm.errors.brand_id }}
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block">Name</label>
                    <a-input v-model:value="addProductForm.name" class="mt-2 w-full" placeholder="Enter Name" />
                    <div v-if="addProductForm.errors.name" class="text-red-500">{{ addProductForm.errors.name }}</div>
                </div>
                <div class="mb-4">
                    <label class="block">Description</label>
                    <a-textarea v-model:value="addProductForm.description" class="mt-2 w-full" placeholder="Description"
                        :auto-size="{ minRows: 2, maxRows: 5 }" />
                    <div v-if="addProductForm.errors.description" class="text-red-500">{{
                        addProductForm.errors.description }}
                    </div>
                </div>

                <div class="text-right">
                    <a-button type="default" @click="isAddProductModalVisible = false">Cancel</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">Save</a-button>
                </div>
            </form>
        </a-modal>

        <!-- Edit Product Modal -->
        <a-modal v-model:open="isEditModalVisible" title="Edit Product" @cancel="isEditModalVisible = false"
            :footer="null">
            <form @submit.prevent="updateProduct()">
                <div class="mb-4">
                    <label class="block">Name</label>
                    <a-input v-model:value="editForm.name" class="mt-2 w-full" placeholder="Enter Name" />
                    <div v-if="editForm.errors.name" class="text-red-500">{{ editForm.errors.name }}</div>
                </div>
                <div class="mb-4">
                    <label class="block">Description</label>
                    <a-textarea v-model:value="editForm.description" class="mt-2 w-full" placeholder="Description"
                        :auto-size="{ minRows: 2, maxRows: 5 }" />
                    <div v-if="editForm.errors.description" class="text-red-500">{{ editForm.errors.description }}</div>
                </div>
                <div class="text-right">
                    <a-button type="default" @click="isEditModalVisible = false">Cancel</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">Update</a-button>
                </div>
            </form>

        </a-modal>
        <!-- Edit Purchase Product Detail Modal -->
        <a-modal v-model:open="isPurchaseModalVisible" title="Product Purchase Detail"
            @cancel="isPurchaseModalVisible = false" :footer="null">
            <h4 class="text-md">Product - ({{ selectedProductName }})</h4>
            <form @submit.prevent="savePurchaseProductDetail()">
                <a-input hidden v-model:value="purchaseDetailForm.product_id" class="mt-2 w-full"
                    placeholder="Enter Name" />
                <a-row>
                    <a-col :span="24">
                        <div class="mb-1">
                            <label class="block">Purchase Price</label>
                            <a-input type="number" v-model:value="purchaseDetailForm.purchase_price" class="mt-2 w-full"
                                placeholder="Enter Purchase Price" />
                            <div v-if="purchaseDetailForm.errors.purchase_price" class="text-red-500">{{
                                purchaseDetailForm.errors.purchase_price }}</div>
                        </div>

                    </a-col>
                    <a-col :span="24">
                        <div class="mb-1">
                            <label class="block">Sale Price</label>
                            <a-input type="number" v-model:value="purchaseDetailForm.sale_price" class="mt-2 w-full"
                                placeholder="Enter Purchase Price" />
                            <div v-if="purchaseDetailForm.errors.sale_price" class="text-red-500">{{
                                purchaseDetailForm.errors.sale_price }}</div>
                        </div>
                    </a-col>
                    <a-col :span="24">
                        <div class="mb-1">
                            <label class="block">Stock</label>
                            <a-input :min="1" type="number" v-model:value="purchaseDetailForm.stock" class="mt-2 w-full"
                                placeholder="Enter Purchase Price" />
                            <div v-if="purchaseDetailForm.errors.stock" class="text-red-500">{{
                                purchaseDetailForm.errors.stock }}
                            </div>
                        </div>
                    </a-col>
                    <a-col :span="24">
                        <div class="mb-4">
                            <label class="block">Description( Optional)</label>
                            <a-textarea v-model:value="purchaseDetailForm.description" class="mt-2 w-full"
                                placeholder="Description" :auto-size="{ minRows: 2, maxRows: 5 }" />
                            <div v-if="purchaseDetailForm.errors.description" class="text-red-500">{{
                                purchaseDetailForm.errors.description }}</div>
                        </div>
                    </a-col>
                </a-row>

                <div class="text-right">
                    <a-button type="default" @click="isPurchaseModalVisible = false">Cancel</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">Save</a-button>
                </div>
            </form>

        </a-modal>
    </AdminLayout>
</template>
