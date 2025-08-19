<script setup>

    import PageTitle from '@/components/PageTitle.vue';
    import DataTable from '@/components/DataTable.vue';
    import BaseButton from '@/components/BaseButton.vue';
    import { useRouter } from 'vue-router';
    import { useBranchStore } from '@/stores/useBranchStore';
    import { onMounted, ref } from 'vue';

    const router = useRouter();

    const useBranch = useBranchStore();

    let branchList = ref([]);

    onMounted(async () => {
      await useBranch.fetchAllBranch();
      branchList.value = useBranch.branchList
      
    });

    console.log(branchList.value);

    const columns = [
        { key: 'id', label: 'ID' },
        { key: 'name', label: 'Name' },
        { key: 'location', label: 'Location' },
        { key: 'status', label: 'Status', formatter: (row) => {
            const color = row.status.name === 'Active' ? 'bg-green-500 text-white rounded-md py-1 px-2' : 'bg-red-500 text-white rounded-md py-1 px-2';
            return `<span class="text-white px-2 py-1 rounded ${color}">${row.status.name}</span>`;
        } },
        { key: 'created_by.name', label: 'Created By', formatter: (row) => row.created_by.name },
        { key: 'updated_by.name', label: 'Updated By', formatter: (row) => row.updated_by.name },
    ];

    console.log(branchList);

    function changeRoute(pathname) {
        router.push(pathname);
    }

</script>

<template>
    <div class="p-4">
        <PageTitle title="Branch List">
            <template #titleButtons>
                <div class="flex gap-x-2 items-center">
                    <BaseButton icon="fa fa-circle-plus" label="Create" severity="primary" @click="changeRoute('/branch/create')"  />
                </div>
            </template>
        </PageTitle>
        <DataTable class="mt-3" :columns="columns" :rows="branchList" :pageSize="5" :editPath="'Update Branch'" :isLoading="useBranch.loading">
            <!-- <template #filters>
                <div class="flex gap-2">
                <input type="date" class="border rounded px-2 py-1" />
                <input type="date" class="border rounded px-2 py-1" />
                <input
                    type="text"
                    v-model="searchQuery"
                    placeholder="Search..."
                    class="border rounded px-2 py-1"
                />
                </div>
            </template> -->
        </DataTable>
    </div>
</template>
