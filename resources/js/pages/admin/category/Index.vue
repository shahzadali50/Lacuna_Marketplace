<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import {  Modal } from 'ant-design-vue';
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import dayjs from "dayjs";
const isLoading = ref(false);
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};
const columns = [
    { title: 'Sr.', dataIndex: 'id', key: 'id' },
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
    id:null
})
const editForm = useForm({
    id: null,
    name: '',
    description: '',
});
const brandForm = useForm({
    id: null,
    name: '',
    description: '',
    category_id: null,
});
const saveCategory = () => {
    isLoading.value = true;
    form.post(route('user.category.store'), {
        onSuccess: () => {
            form.reset();
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
            form.delete(route('user.category.delete', id), {
                onFinish: () => {
            isLoading.value = false; // ✅ Stop loading
        }
            });
        },
    });
};

const isEditModalVisible = ref(false);
const isbrandModalVisible = ref(false);
const selectedCategoryName = ref('');

const openEditModal = (category: any) => {
    editForm.id = category.id;
    editForm.name = category.name;
    editForm.description = category.description;
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
    editForm.put(route('user.category.update', editForm.id), {
        onSuccess: () => {
            isEditModalVisible.value = false;
        },
        onFinish: () => {
            isLoading.value = false;
        }
    });
};
</script>

<template>
      <div v-if="isLoading" class="loading-overlay" >
        <a-spin size="large" />
    </div>
    <AdminLayout>
    <Head title="Category" />

        <a-row class="justify-between">
            <a-col :lg="8" :xs="24">
                <div class="bg-white rounded-lg p-4 shadow-md ">

                    <h2 class="text-lg font-semibold mb-4">Create Category</h2>
                    <form @submit.prevent="saveCategory()">
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
                        <div>
                            <a-button type="primary" html-type="submit" :disabled="isLoading">
                                Save
                            </a-button>
                        </div>
                    </form>
                </div>
            </a-col>
            <a-col :lg="15" :xs="24">

                <div class="bg-white rounded-lg responsive-table p-4 shadow-md  mt-4 lg:mt-0 w-full">

                    <div  class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold mb-4">Category List</h2>
                        <Link :href="route('user.category.log')" >
                            <a-button type="default">Category Logs</a-button>
                        </Link>

                    </div>
                    <a-table :columns="columns" :data-source="categories.data" rowKey="id" :scroll="{ x: 200 }" >
                        <template #bodyCell="{ column, record,index }">
                            <template v-if="column.dataIndex === 'id'">
                                {{ index+1 }}
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
                                    <a-button type="link" @click="openEditModal(record)"><i class="fa fa-pencil-square-o text-s text-green-500" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>Delete</template>
                                    <a-button type="link"  @click="deleteCategory(record.id)"><i class="fa fa-trash text-red-500" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>Add Brand</template>
                                    <a-button type="link" @click="openBrandModal(record)"><i class="fa fa-creative-commons" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>Brand List</template>
                                    <Link :href="route('user.related-brand-list',record.slug)" class="text-blue-500 hover:underline"><i class="fa fa-list text-slate-800" aria-hidden="true"></i></Link>
                                </a-tooltip>
                            </template>
                        </template>
                    </a-table>

                </div>
            </a-col>


        </a-row>
        <!-- Edit Category Modal -->
        <a-modal v-model:open="isEditModalVisible" title="Edit Category" @cancel="isEditModalVisible = false"
            :footer="null">
            <form @submit.prevent="updateCategory()">
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
        <!-- brand Modal  -->
        <a-modal v-model:open="isbrandModalVisible" title="Add Brand  "  @cancel="isbrandModalVisible = false"
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
                    <div v-if="brandForm.errors.description" class="text-red-500">{{ brandForm.errors.description }}</div>
                </div>
                <div class="text-right">
                    <a-button type="default" @click="isbrandModalVisible = false">Cancel</a-button>
                    <a-button type="primary" html-type="submit" class="ml-2">Save</a-button>
                </div>
            </form>
        </a-modal>
    </AdminLayout>
</template>
<style scoped>


</style>
