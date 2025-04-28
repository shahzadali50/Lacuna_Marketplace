<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Modal } from 'ant-design-vue';
import dayjs from "dayjs";
import { ref, computed, watch } from 'vue';
import AddProduct from '@/Components/product/AddProduct.vue';

const isLoading = ref(false);
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};
const page = usePage();
const translations = computed(() => {
    return page.props.translations?.dashboard_all_pages || {};
});

// Define table columns
const columns = computed(() => [
    { title: translations.value.id || 'ID', dataIndex: 'id', key: 'id' },
    { title: translations.value.image || 'Image', dataIndex: 'image', key: 'image' },
    { title: translations.value.name || 'Name', dataIndex: 'name', key: 'name' },
    { title: translations.value.description || 'Description', dataIndex: 'description', key: 'description' },
    { title: translations.value.stock || 'Stock', dataIndex: 'stock', key: 'stock' },
    { title: translations.value.purchase_price || 'Purchase Price', dataIndex: 'purchase_price', key: 'purchase_price' },
    { title: translations.value.sale_price || 'Sale Price', dataIndex: 'sale_price', key: 'sale_price' },
    { title: translations.value.brand || 'Brand', dataIndex: 'brand_name', key: 'brand_name' },
    { title: translations.value.category || 'Category', dataIndex: 'category_name', key: 'category_name' },
    { title: translations.value.created_at || 'Created At', dataIndex: 'created_at', key: 'created_at' },
    { title: translations.value.action || 'Action', dataIndex: 'action', key: 'action' },
]);

// Receive brands as a prop from Inertia
const props = defineProps<{
    products: { data: Array<any>; current_page: number; per_page: number; total: number };
    brands: Array<{ id: number; name: string; category_id: number }>;
    categories: Array<{ id: number; name: string }>;
}>();

// Convert brands to Ant Design Select options
const brandOptions = computed(() => {
    return props.brands
        ?.filter((brand) => brand.category_id === addProductForm.category_id)
        .map((brand) => ({
            value: brand.id,
            label: brand.name,
        })) || [];
});

const categoryOptions = computed(() => {
    return props.categories?.map((category) => ({
        value: category.id,
        label: category.name,
    })) || [];
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
            form.delete(route('admin.product.delete', id), {
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
    name: "",
    description: "",
    brand_id: null,
    category_id: null,
    thumnail_img: null as File | null,
    gallary_img: [] as File[],
    stock: 0,
    status: "active",
    purchase_price: null as number | null,
    sale_price: null as number | null,
    feature: false,
    barcode: "",
    discount: 0,
    final_price: 0,
});
// Reset brand_id when category_id changes
watch(() => addProductForm.category_id, () => {
    addProductForm.brand_id = null;
});
const thumnailPreview = ref<string | null>(null);
const gallaryPreviews = ref<string[]>([]);

const handleThumnailChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        addProductForm.thumnail_img = target.files[0];
        thumnailPreview.value = URL.createObjectURL(target.files[0]);
    }
};

const handleGallaryChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files) {
        // Append new files to existing ones
        const newFiles = Array.from(target.files);
        addProductForm.gallary_img = [...addProductForm.gallary_img, ...newFiles];

        // Create previews for new files and append to existing previews
        const newPreviews = newFiles.map((file) => URL.createObjectURL(file));
        gallaryPreviews.value = [...gallaryPreviews.value, ...newPreviews];
    }
};

const removeGalleryImage = (index: number, event: Event) => {
    event.preventDefault();
    event.stopPropagation();
    // Remove the file from the form data
    addProductForm.gallary_img.splice(index, 1);
    // Remove the preview
    gallaryPreviews.value.splice(index, 1);
};

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

    addProductForm.reset();
    thumnailPreview.value = null;
    gallaryPreviews.value = [];
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
    addProductForm.post(route("admin.product.store"), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            addProductForm.reset();
            isAddProductModalVisible.value = false;
            thumnailPreview.value = null;
            gallaryPreviews.value = [];
        },
        onFinish: () => {
            isLoading.value = false;
        },
    });
};
// Update brand
const updateProduct = () => {
    isLoading.value = true;
    editForm.put(route('admin.product.update', editForm.id), {
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
    purchaseDetailForm.post(route('admin.purchase.product.detail.store'), {
        onSuccess: () => {
            purchaseDetailForm.reset();
            isPurchaseModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

const finalPrice = computed(() => {
    if (addProductForm.sale_price && addProductForm.discount) {
        const discountAmount = (addProductForm.sale_price * addProductForm.discount) / 100;
        return addProductForm.sale_price - discountAmount;
    }
    return addProductForm.sale_price;
});

watch([() => addProductForm.sale_price, () => addProductForm.discount], () => {
    addProductForm.final_price = finalPrice.value;
});

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
                            <a-button class="mx-2" type="default" @click="isAddProductModalVisible = true">
                                Add Product
                            </a-button>
                            <Link :href="route('admin.product-log')">
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
                            <template v-if="column.dataIndex === 'image'">

                                <div>
                                    <img v-if="record.thumnail_img" :src="'/storage/' + record.thumnail_img" alt="thumnail_img"
                                        class="w-12 h-12 object-cover rounded mb-1 cursor-pointer hover:opacity-80 transition-opacity" />
                                    <span v-else class="text-gray-400 mb-1">No Image</span>
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'name'">
                                {{ record.name }}
                            </template>
                            <template v-if="column.dataIndex === 'description'">
                                {{ record.description ? record.description : 'N/A' }}
                            </template>
                            <template v-else-if="column.dataIndex === 'brand'">
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
                                    <Link :href="route('admin.related.purchase.product.list', record.slug)"
                                        class="text-blue-500 hover:underline"><i class="fa fa-list text-slate-800"
                                        aria-hidden="true"></i></Link>
                                </a-tooltip>
                            </template>

                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>

        <AddProduct
            :is-visible="isAddProductModalVisible"
            :categories="categories"
            :brands="brands"
            :translations="translations"
            @update:is-visible="isAddProductModalVisible = $event"
            @success="isAddProductModalVisible = false"
        />

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
