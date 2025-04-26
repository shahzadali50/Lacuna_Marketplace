<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Modal } from 'ant-design-vue';
import dayjs from "dayjs";
import { ref, computed ,watch } from 'vue';
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
        addProductForm.gallary_img = Array.from(target.files);
        gallaryPreviews.value = Array.from(target.files).map((file) => URL.createObjectURL(file));
    }
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
                            <a-button class="mx-2" type="default" @click="openAddProductModal()">
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
        <a-modal
            width="1000px"
            v-model:open="isAddProductModalVisible"
            :title="translations.add_product || 'Add Product'"
            @cancel="isAddProductModalVisible = false"
            :footer="null"
        >
            <form @submit.prevent="saveProduct">
                <a-row :gutter="16">
                    <a-col :span="12">
                        <div class="mb-4">
                            <label class="block">{{ translations.category || 'Category' }}</label>
                            <a-select
                                v-model:value="addProductForm.category_id"
                                show-search
                                :placeholder="translations.select_category || 'Select Category'"
                                class="mt-2 w-full"
                                :options="categoryOptions"
                                :filter-option="filterOption"
                            ></a-select>
                            <div v-if="addProductForm.errors.category_id" class="text-red-500">
                                {{ addProductForm.errors.category_id }}
                            </div>
                        </div>
                    </a-col>
                    <a-col :span="12">
                        <div class="mb-4">
                            <label class="block">{{ translations.brand || 'Brand' }}</label>
                            <a-select
                                v-model:value="addProductForm.brand_id"
                                show-search
                                :placeholder="translations.select_brand || 'Select Brand'"
                                class="mt-2 w-full"
                                :options="brandOptions"
                                :filter-option="filterOption"
                                :disabled="!addProductForm.category_id"
                            ></a-select>
                            <div v-if="addProductForm.errors.brand_id" class="text-red-500">
                                {{ addProductForm.errors.brand_id }}
                            </div>
                        </div>
                    </a-col>
                </a-row>
                <a-row :gutter="16">
                    <a-col :span="12">
                        <div class="mb-4">
                            <label class="block">{{ translations.name || 'Name' }}</label>
                            <a-input
                                v-model:value="addProductForm.name"
                                class="mt-2 w-full"
                                :placeholder="translations.name_placeholder || 'Enter Name'"
                            />
                            <div v-if="addProductForm.errors.name" class="text-red-500">
                                {{ addProductForm.errors.name }}
                            </div>
                        </div>
                    </a-col>
                    <a-col :span="12">
                        <div class="mb-4">
                            <label class="block">{{ translations.barcode || 'Barcode' }}</label>
                            <a-input
                                v-model:value="addProductForm.barcode"
                                class="mt-2 w-full"
                                :placeholder="translations.enter_barcode || 'Enter Barcode'"
                            />
                            <div v-if="addProductForm.errors.barcode" class="text-red-500">
                                {{ addProductForm.errors.barcode }}
                            </div>
                        </div>
                    </a-col>
                </a-row>
                <a-row :gutter="16">
                    <a-col :span="12">
                        <div class="mb-4">
                            <label class="block">{{ translations.purchase_price || 'Purchase Price' }}</label>
                            <a-input-number
                                v-model:value="addProductForm.purchase_price"
                                class="mt-2 w-full"
                                :min="0"
                                :step="0.01"
                                :placeholder="translations.enter_purchase_price || 'Enter Purchase Price'"
                            />
                            <div v-if="addProductForm.errors.purchase_price" class="text-red-500">
                                {{ addProductForm.errors.purchase_price }}
                            </div>
                        </div>
                    </a-col>
                    <a-col :span="12">
                        <div class="mb-4">
                            <label class="block">{{ translations.sale_price || 'Sale Price' }}</label>
                            <a-input-number
                                v-model:value="addProductForm.sale_price"
                                class="mt-2 w-full"
                                :min="0"
                                :step="0.01"
                                :placeholder="translations.enter_sale_price || 'Enter Sale Price'"
                            />
                            <div v-if="addProductForm.errors.sale_price" class="text-red-500">
                                {{ addProductForm.errors.sale_price }}
                            </div>
                        </div>
                    </a-col>
                </a-row>
                <a-row :gutter="16">
                    <a-col :span="12">
                        <div class="mb-4">
                            <label class="block">{{ translations.stock || 'Stock' }}</label>
                            <a-input-number
                                v-model:value="addProductForm.stock"
                                class="mt-2 w-full"
                                :min="0"
                                :placeholder="translations.enter_stock || 'Enter Stock'"
                            />
                            <div v-if="addProductForm.errors.stock" class="text-red-500">
                                {{ addProductForm.errors.stock }}
                            </div>
                        </div>
                    </a-col>
                    <a-col :span="12">
                        <div class="mb-4">
                            <label class="block">{{ translations.status || 'Status' }}</label>
                            <a-select
                                v-model:value="addProductForm.status"
                                class="mt-2 w-full"
                                :placeholder="translations.select_status || 'Select Status'"
                            >
                                <a-select-option value="active">{{ translations.active || 'Active' }}</a-select-option>
                                <a-select-option value="inactive">{{ translations.inactive || 'Inactive' }}</a-select-option>
                            </a-select>
                            <div v-if="addProductForm.errors.status" class="text-red-500">
                                {{ addProductForm.errors.status }}
                            </div>
                        </div>
                    </a-col>
                </a-row>
                <div class="mb-4">
                    <label class="block">{{ translations.description || 'Description' }}</label>
                    <a-textarea
                        v-model:value="addProductForm.description"
                        class="mt-2 w-full"
                        :placeholder="translations.enter_description || 'Description'"
                        :auto-size="{ minRows: 3, maxRows: 6 }"
                    />
                    <div v-if="addProductForm.errors.description" class="text-red-500">
                        {{ addProductForm.errors.description }}
                    </div>
                </div>
                <a-row :gutter="16">
                    <a-col :span="12">
                        <div class="mb-4">
                            <label class="block">{{ translations.thumbnail_image || 'Thumbnail Image' }}</label>
                            <input
                                type="file"
                                @change="handleThumnailChange"
                                accept="image/*"
                                class="mt-2 w-full p-2 border rounded"
                            />
                            <div v-if="addProductForm.errors.thumnail_img" class="text-red-500">
                                {{ addProductForm.errors.thumnail_img }}
                            </div>
                            <div v-if="thumnailPreview" class="mt-2">
                                <p class="text-sm text-gray-600 mb-1">{{ translations.preview || 'Preview' }}</p>
                                <img
                                    :src="thumnailPreview"
                                    alt="Thumbnail Preview"
                                    class="w-24 h-24 object-cover rounded border"
                                />
                            </div>
                        </div>
                    </a-col>
                    <a-col :span="12">
                        <div class="mb-4">
                            <label class="block">{{ translations.gallery_images || 'Gallery Images' }}</label>
                            <input
                                type="file"
                                multiple
                                @change="handleGallaryChange"
                                accept="image/*"
                                class="mt-2 w-full p-2 border rounded"
                            />
                            <div v-if="addProductForm.errors.gallary_img" class="text-red-500">
                                {{ addProductForm.errors.gallary_img }}
                            </div>
                            <div v-if="gallaryPreviews.length" class="mt-2">
                                <p class="text-sm text-gray-600 mb-1">{{ translations.preview || 'Preview' }}</p>
                                <div class="flex flex-wrap gap-2">
                                    <img
                                        v-for="(preview, index) in gallaryPreviews"
                                        :key="index"
                                        :src="preview"
                                        alt="Gallery Preview"
                                        class="w-24 h-24 object-cover rounded border"
                                    />
                                </div>
                            </div>
                        </div>
                    </a-col>
                </a-row>
                <div class="mb-4">
                    <label class="flex items-center">
                        <a-checkbox v-model:checked="addProductForm.feature">
                            {{ translations.featured || 'Featured' }}
                        </a-checkbox>
                    </label>
                    <div v-if="addProductForm.errors.feature" class="text-red-500">
                        {{ addProductForm.errors.feature }}
                    </div>
                </div>
                <a-row :gutter="16">
                    <a-col :xs="24" :md="12">
                        <div class="mb-4">
                            <label class="block">Discount (%)</label>
                            <a-input-number 
                                v-model:value="addProductForm.discount" 
                                class="mt-2 w-full" 
                                :min="0" 
                                :max="100" 
                                :step="1" 
                            />
                            <div v-if="addProductForm.errors.discount" class="text-red-500">
                                {{ addProductForm.errors.discount }}
                            </div>
                        </div>
                    </a-col>
                    <a-col :xs="24" :md="12">
                        <div class="mb-4">
                            <label class="block">Final Price</label>
                            <a-input-number 
                                v-model:value="addProductForm.final_price" 
                                class="mt-2 w-full" 
                                :min="0" 
                                :step="0.01" 
                                disabled 
                            />
                        </div>
                    </a-col>
                </a-row>
                <div class="text-right">
                    <a-button type="default" @click="isAddProductModalVisible = false">
                        {{ translations.cancel || 'Cancel' }}
                    </a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">
                        {{ translations.save || 'Save' }}
                    </a-button>
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
