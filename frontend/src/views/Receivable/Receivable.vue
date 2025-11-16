<script setup>

    import PageTitle from '@/components/PageTitle.vue';
    import DataTable from '@/components/DataTable.vue';
    import BaseButton from '@/components/BaseButton.vue';
    import { useRouter } from 'vue-router';
    import { onMounted, ref, computed } from 'vue';
    import { useToast } from 'primevue';
    import moment from 'moment'
    import { useFilterStore } from '@/stores/filterStore';
    import { usePermissionStore } from '@/stores/usePermissionStore';
    import BaseInput from '@/components/BaseInput.vue';
    import { useReceivableStore } from '@/stores/useReceivableStore';

    const router = useRouter();
    const toast = useToast();
    const filter = useFilterStore();
    const usePermission = usePermissionStore();

    const useReceivable = useReceivableStore();

    const searchValue = ref('');
    const startDate = ref('');
    const endDate = ref('');
    const receivableList = ref([]);

    onMounted(async () => {
        await useReceivable.fetchAllReceivable();
        receivableList.value = useReceivable.receivableList;
    });

    // Table headers
    const columns = [
        { key: 'id', label: 'ID' },
        { key: 'customer.name', label: 'Name' },
        { key: 'type', label: 'Type' },
        { key: 'amount', label: 'Amount' },
        { key: 'payment_method.name', label: 'Payment Method', formatter: (row) => row.payment_method.name },
       
        { key: 'created_by.name', label: 'Created By', formatter: (row) => row.created_by.name },
        { key: 'created_at', label: 'Created At', formatter: (row) => moment(row.created_at).format('DD-MM-YY hh:mm') },
        { key: 'updated_by.name', label: 'Updated By', formatter: (row) => row.updated_by.name },
        { key: 'updated_at', label: 'Updated At', formatter: (row) => moment(row.updated_at).format('DD-MM-YY hh:mm') },
    ];

    // Route change function: need to pass route path.
    function changeRoute(pathname) {
        router.push(pathname);
    }

    // Filter Function
    const filteredRows = computed(() => {
        const searchedData = filter.searchFunction(receivableList.value, searchValue.value, [
            "customer.name",
            "type",
            "payment_method.name"
        ]);
        return filter.dateRangeFilter(searchedData, { dateField: 'created_at', startDate: startDate.value, endDate: endDate.value })
    });

    //  Delete function
    async function deleteHandle(id) {
        await useReceivable.deleteReceivable(id);
        if(useReceivable.error) {
            toast.add({ severity: 'error', summary: 'Error Message', detail: useReceivable.error, life: 3000 });
            return
        }
        if (useReceivable.data.status === 200) {
            toast.add({ severity: 'success', summary: 'Success Message', detail: 'Receivable deleted successfully.', life: 3000 });
            await useReceivable.fetchAllReceivable();
            receivableList.value = useReceivable.receivableList;
        }
    }

</script>

<template>
    <div class="p-4">
        <!-- Page Title -->
        <PageTitle title="Receivable List">
            <template #titleButtons>
                <div class="flex gap-x-2 items-center">
                    <BaseButton 
                        v-if="usePermission.can('Receivable', 'Create')"
                        icon="fa fa-circle-plus" 
                        label="Create" 
                        severity="primary" 
                        @click="changeRoute('/receivable/create')"  
                    />
                </div>
            </template>
        </PageTitle>
        <!-- DataTable -->
        <DataTable
            :columns="columns"
            :rows="filteredRows"
            :pageSize="5"
            :editPath="'Update Receivable'"
            :isLoading="useReceivable.loading"
            @delete="deleteHandle"
            :defaultSort="{key: 'created_at', order: 'desc'}"
            :isEdit="!usePermission.can('Receivable', 'Update')"
            :isDelete="!usePermission.can('Receivable', 'Delete')"
        >
            <!-- Filter Section -->
            <template #filters>
                <div class="flex gap-2">
                    <BaseInput
                        size="sm"
                        type="date"
                        v-model="startDate"
                        placeholder="Search"
                        width="200px"
                        height="h-[35px]"
                    />
                    <BaseInput
                        size="sm"
                        type="date"
                        v-model="endDate"
                        placeholder="Search"
                        width="200px"
                        height="h-[35px]"
                    />
                    <BaseInput
                        size="sm"
                        v-model="searchValue"
                        placeholder="Search"
                        width="200px"
                        height="h-[35px]"
                        icon="pi pi-search"
                    />
                </div>
            </template>
        </DataTable>
    </div>
</template>
