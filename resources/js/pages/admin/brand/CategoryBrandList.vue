<script setup lang="ts">
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link , useForm} from '@inertiajs/vue3';
import {  Modal } from 'ant-design-vue';
import dayjs from "dayjs";
import { ref,  } from 'vue';
const isLoading = ref(false);
// Format date function
const formatDate = (date: string) => {
    return date ? dayjs(date).format("DD-MM-YYYY hh:mm A") : "N/A";
};

// Define table columns
const columns = [
    { title: 'ID', dataIndex: 'id', key: 'id' },
    { title: 'Name', dataIndex: 'name', key: 'name' },
    { title: 'Description', dataIndex: 'description', key: 'description' },
    { title: 'Category', dataIndex: 'category', key: 'category' },
    { title: 'Created At', dataIndex: 'created_at', key: 'created_at' },
    { title: 'Action', dataIndex: 'action', key: 'action' },
];

// Receive brands as a prop from Inertia
defineProps({
    brands: Object,
    category: Object,
});
const form = useForm({});
const deleteBrand = (id: number) => {
    Modal.confirm({
        title: 'Are you sure you want to delete this Brand?',
        content: 'This action cannot be undone.',
        okText: 'Yes, Delete',
        okType: 'danger',
        cancelText: 'Cancel',
        onOk() {
            isLoading.value = true;
            form.delete(route('user.brand.delete', id), {
                onSuccess: () => {
                },
                onFinish: () => {
           isLoading.value = false;
        }
            });
        },
    });
};
const isEditModalVisible = ref(false);
const editForm = useForm({
    id: null,
    name: '',
    description: '',
});
const openEditModal =(brands:any)=>{
    editForm.id = brands.id;
    editForm.name = brands.name;
    editForm.description = brands.description;
    isEditModalVisible.value = true;
}
// Update brand
const updateBrand = () => {
    isLoading.value = true;
    editForm.put(route('user.brand.update', editForm.id), {
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
    <AdminLayout>
        <Head title="Brand List" />
        <a-row>
            <a-col :span="24">
                <div class="bg-white rounded-lg p-4 shadow-md">
                    <div class="mb-4 flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Brand List - {{ category.name }}</h2>

                        <div>
                            <Link :href="route('user.categories')">
                            <a-button class="mx-2" type="default">Back</a-button>
                        </Link>
                        <Link :href="route('user.brand-log')">
                            <a-button class="mx-2" type="default">Brand Logs</a-button>
                        </Link>
                        <Link :href="route('user.brands')" >
                            <a-button class="mx-2" type="default">All Brand List</a-button>
                        </Link>
                        </div>
                    </div>

                    <!-- Display table -->
                    <a-table v-if="brands" :columns="columns" :data-source="brands.data" rowKey="id" bordered>
                        <template #bodyCell="{ column, record, index }">
                            <template v-if="column.dataIndex === 'id'">
                                {{ index + 1 }}
                            </template>
                            <template v-if="column.dataIndex === 'name'">
                                {{ record.name}}
                            </template>
                            <template v-if="column.dataIndex === 'description'">
                                {{ record.description ? record.description : 'N/A'}}
                            </template>
                            <template v-if="column.dataIndex === 'category'">
                                {{ record.category.slug}}
                            </template>

                            <template v-else-if="column.dataIndex === 'created_at'">
                                {{ formatDate(record.created_at) }}
                            </template>
                            <template v-else-if="column.dataIndex === 'action'">
                            <a-tooltip placement="top">
                                    <template #title>Delete</template>
                                    <a-button type="link"  @click="deleteBrand(record.id)"><i class="fa fa-trash text-red-500" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                                <a-tooltip placement="top">
                                    <template #title>Edit</template>
                                    <a-button type="link" @click="openEditModal(record)"><i class="fa fa-pencil-square-o text-s text-green-500" aria-hidden="true"></i></a-button>
                                </a-tooltip>
                            </template>

                        </template>
                    </a-table>
                </div>
            </a-col>
        </a-row>
          <!-- Edit Category Modal -->
          <a-modal v-model:open="isEditModalVisible" title="Edit Brand" @cancel="isEditModalVisible = false"
            :footer="null">
            <form @submit.prevent="updateBrand()">
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
    </AdminLayout>
</template>
