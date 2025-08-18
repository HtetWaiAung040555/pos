<script setup>

    import PageTitle from '@/components/PageTitle.vue';
    import BaseButton from '@/components/BaseButton.vue';
    import { useRoute, useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
import { useBranchStore } from '@/stores/useBranchStore';

    const router = useRouter();
    const route = useRoute();
    const useBranch = useBranchStore();

    const branch = ref({});

    function changeRoute(pathname) {
        router.push(pathname);
    }

    onMounted(async () => {
        await useBranch.fetchBranch(route.query.id);
        branch.value = useBranch.branchList
        console.log(branch.value.name);
    })

</script>

<template>
    <div class="p-4">
        <PageTitle title="Update Branch">
            <template #titleButtons>
                <div class="flex gap-x-2 items-center">
                    <BaseButton icon="fa fa-chevron-left" label="Back" severity="secondary" @click="changeRoute('/branch')"  />
                </div>
            </template>
        </PageTitle>
    </div>
</template>
