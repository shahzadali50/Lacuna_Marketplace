<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Modal } from 'ant-design-vue';
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import dayjs from "dayjs";
const isLoading = ref(false);
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};
const columns = [
    { title: 'Sr.', dataIndex: 'id', key: 'id' },
    { title: 'Image', dataIndex: 'image', key: 'image' },
    { title: 'Name', dataIndex: 'name', key: 'name' },
    { title: 'Description', dataIndex: 'description', key: 'description' },
    { title: 'Created At', dataIndex: 'created_at', key: 'created_at' },
    { title: 'Action', dataIndex: 'action', key: 'action' },
];

defineProps({
    categories: Object,
})

const form = useForm({
    name: '',
    description: '',
    id: null,
    image: null as File | null
})
const editForm = useForm({
    id: null,
    name: '',
    description: '',
    image: null as File | null,
    _method: 'PUT'
});
const brandForm = useForm({
    id: null,
    name: '',
    description: '',
    category_id: null,
});

const isAddCategoryModalVisible = ref(false);
const selectedCategoryName = ref('');
const currentImage = ref('');
const imagePreview = ref('');
const editImagePreview = ref('');
const isImagePreviewModalVisible = ref(false);
const previewImageUrl = ref('');

const openAddCategoryModal = () => {
    form.reset();
    imagePreview.value = '';
    isAddCategoryModalVisible.value = true;
};

const handleImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        form.image = target.files[0];
        // Create preview URL
        imagePreview.value = URL.createObjectURL(target.files[0]);
    }
};

const handleEditImageChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        editForm.image = target.files[0];
        // Create preview URL
        editImagePreview.value = URL.createObjectURL(target.files[0]);
    }
};

const saveCategory = () => {
    isLoading.value = true;
    form.post(route('user.category.store'), {
        onSuccess: () => {
            form.reset();
            isAddCategoryModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false; // ✅ Stop loading
        }
    })
}
const deleteCategory = (id: number) => {
    Modal.confirm({
        title: 'Confirm Category Deletion',
        content: 'Deleting this category will also remove all associated brands. This action is irreversible. Are you sure you want to proceed?',
        okText: 'Yes, Delete',
        okType: 'danger',
        cancelText: 'Cancel',
        onOk() {
            isLoading.value = true;
            form.delete(route('user.category.delete', { id: id }), {
                onFinish: () => {
                    isLoading.value = false; // ✅ Stop loading
                }
            });
        },
    });
};

const isEditModalVisible = ref(false);
const isbrandModalVisible = ref(false);

const openEditModal = (category: any) => {
    editForm.id = category.id;
    editForm.name = category.name;
    editForm.description = category.description;
    editForm.image = null; // Reset image when opening modal
    currentImage.value = category.image; // Store the current image path
    editImagePreview.value = ''; // Reset preview
    isEditModalVisible.value = true;
};
const openBrandModal = (record: any) => {
    selectedCategoryName.value = record.name;
    brandForm.category_id = record.id;
    isbrandModalVisible.value = true;
};
const saveBrand = () => {
    isLoading.value = true;
    brandForm.post(route('user.brand.store'), {
        onSuccess: () => {
            brandForm.reset();
            isbrandModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        }
    })
}

// Update category
const updateCategory = () => {
    isLoading.value = true;
    editForm.post(route('user.category.update', { id: editForm.id }), {
        onSuccess: () => {
            isEditModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

// Function to get original filename from storage path
const getOriginalFilename = (path: string) => {
    if (!path) return '';
    // Extract the filename from the path
    const parts = path.split('/');
    const filename = parts[parts.length - 1];
    return filename;
};

const openImagePreview = (imagePath: string) => {
    previewImageUrl.value = '/storage/' + imagePath;
    isImagePreviewModalVisible.value = true;
};
</script>

<template>
    <div v-if="isLoading" class="loading-overlay">
        <a-spin size="large" />
    </div>
    <AdminLayout>

        <Head title="Category" />

        <a-row>
            <a-col :xs="24">
                <div class="bg-white rounded-lg responsive-table p-4 shadow-md w-full">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold mb-4">Category List</h2>
                        <div>
                            <a-button @click="openAddCategoryModal()" type="default">Add Category</a-button>
                            <Link :href="route('user.category.log')">
                            <a-button type="default">Category Logs</a-button>
                            </Link>
                        </div>
                    </div>
                    <a-table :columns="columns" :data-source="categories?.data" rowKey="id" :scroll="{ x: 200 }">
                        <template #bodyCell="{ column, record, index }">
                            <template v-if="column.dataIndex === 'id'">
                                {{ index + 1 }}
                            </template>
                            <template v-if="column.dataIndex === 'image'">
                                <div class="flex flex-col items-center">
                                    <img
                                        v-if="record.image"
                                        :src="'/storage/' + record.image"
                                        alt="Category Image"
                                        class="w-12 h-12 object-cover rounded mb-1 cursor-pointer hover:opacity-80 transition-opacity"
                                        @click="openImagePreview(record.image)"
                                    />
                                    <span v-else class="text-gray-400 mb-1">No image</span>
                                </div>
                            </template>
                            <template v-if="column.dataIndex === 'name'">
                                <a>{{ record.name }}</a>
                            </template>
                            <template v-else-if="column.dataIndex === 'created_at'">
                                {{ formatDate(record.created_at) }}
                            </template>
                            <template v-else-if="column.dataIndex === 'description'">
                                {{ record.description ?? 'N/A' }}
                            </template>
                            <template v-else-if="column.dataIndex === 'action'">
                                <a-tooltip placement="top">
                                    <template #title>Edit</template>
                                    <a-button type="link" @click="openEditModal(record)"><i
                                            class="fa fa-pencil-square-o text-s text-green-500"
                                            aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>Delete</template>
                                    <a-button type="link" @click="deleteCategory(record.id)"><i
                                            class="fa fa-trash text-red-500" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>Add Brand</template>
                                    <a-button type="link" @click="openBrandModal(record)"><i
                                            class="fa fa-creative-commons" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>Brand List</template>
                                    <Link :href="route('user.related-brand-list', record.slug)"
                                        class="text-blue-500 hover:underline"><i class="fa fa-list text-slate-800"
                                        aria-hidden="true"></i></Link>
                                </a-tooltip>
                            </template>
                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>

        <!-- Add Category Modal -->
        <a-modal v-model:open="isAddCategoryModalVisible" title="Add Category"
            @cancel="isAddCategoryModalVisible = false" :footer="null">
            <form  @submit.prevent="saveCategory()" enctype="multipart/form-data">
                <div class="mb-4">
                    <label class="block">Name</label>
                    <a-input v-model:value="form.name" class="mt-2 w-full" placeholder="Enter Name" />
                    <div v-if="form.errors.name" class="text-red-500">{{ form.errors.name }}</div>
                </div>
                <div class="mb-4">
                    <label class="block">Description</label>
                    <a-textarea v-model:value="form.description" class="mt-2 w-full" placeholder="Description"
                        :auto-size="{ minRows: 2, maxRows: 5 }" />
                    <div v-if="form.errors.description" class="text-red-500">{{ form.errors.description }}</div>
                </div>
                <div class="mb-4">
                    <label class="block">Image</label>
                    <input type="file" @change="handleImageChange" accept="image/*"
                        class="mt-2 w-full p-2 border rounded" />
                    <div v-if="form.errors.image" class="text-red-500">{{ form.errors.image }}</div>
                    <!-- Image Preview -->
                    <div v-if="imagePreview" class="mt-2">
                        <p class="text-sm text-gray-600 mb-1">Preview:</p>
                        <img :src="imagePreview" alt="Image Preview" class="w-24 h-24 object-cover rounded border" />
                    </div>
                </div>
                <div class="text-right">
                    <a-button type="default" @click="isAddCategoryModalVisible = false">Cancel</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">Add</a-button>
                </div>
            </form>
        </a-modal>

        <!-- Edit Category Modal -->
        <a-modal v-model:open="isEditModalVisible" title="Edit Category" @cancel="isEditModalVisible = false"
            :footer="null">
            <form @submit.prevent="updateCategory()" enctype="multipart/form-data">
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
                <div class="mb-4">
                    <label class="block">Image</label>
                    <div v-if="currentImage" class="mb-2">
                        <p class="text-sm text-gray-600 mb-1">Current Image:</p>
                        <img :src="'/storage/' + currentImage" alt="Current Category Image"
                            class="w-24 h-24 object-cover rounded border" />
                        <p class="text-xs text-gray-500 mt-1">{{ getOriginalFilename(currentImage) }}</p>
                    </div>
                    <input type="file" @change="handleEditImageChange" accept="image/*"
                        class="mt-2 w-full p-2 border rounded" />
                    <div v-if="editForm.errors.image" class="text-red-500">{{ editForm.errors.image }}</div>
                    <div class="mt-2 text-sm text-gray-500">
                        Leave empty to keep the current image
                    </div>
                    <!-- New Image Preview -->
                    <div v-if="editImagePreview" class="mt-2">
                        <p class="text-sm text-gray-600 mb-1">New Image Preview:</p>
                        <img :src="editImagePreview" alt="New Image Preview"
                            class="w-24 h-24 object-cover rounded border" />
                    </div>
                </div>
                <div class="text-right">
                    <a-button type="default" @click="isEditModalVisible = false">Cancel</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">Update</a-button>
                </div>
            </form>
        </a-modal>
        <!-- brand Modal  -->
        <a-modal v-model:open="isbrandModalVisible" title="Add Brand  " @cancel="isbrandModalVisible = false"
            :footer="null">
            <h4 class="text-md"> Category ({{ selectedCategoryName }})</h4>
            <form @submit.prevent="saveBrand()">
                <a-input hidden v-model:value="brandForm.category_id" class="mt-2 w-full" placeholder="Enter Name" />
                <div class="mb-4">
                    <label class="block">Name</label>
                    <a-input v-model:value="brandForm.name" class="mt-2 w-full" placeholder="Enter Name" />
                    <div v-if="brandForm.errors.name" class="text-red-500">{{ brandForm.errors.name }}</div>
                </div>
                <div class="mb-4">
                    <label class="block">Description</label>
                    <a-textarea v-model:value="brandForm.description" class="mt-2 w-full" placeholder="Description"
                        :auto-size="{ minRows: 2, maxRows: 5 }" />
                    <div v-if="brandForm.errors.description" class="text-red-500">{{ brandForm.errors.description }}
                    </div>
                </div>
                <div class="text-right">
                    <a-button type="default" @click="isbrandModalVisible = false">Cancel</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">Save</a-button>
                </div>
            </form>
        </a-modal>

        <!-- Image Preview Modal -->
        <a-modal
            v-model:open="isImagePreviewModalVisible"
            title="Image Preview"
            @cancel="isImagePreviewModalVisible = false"
            :footer="null"
            width="600px"
        >
            <div class="flex justify-center p-4">
                <img
                    :src="previewImageUrl"
                    alt="Full Size Image"
                    class="max-w-full max-h-[500px] object-cover"
                />
            </div>
        </a-modal>
    </AdminLayout>
</template>
<style scoped></style>
