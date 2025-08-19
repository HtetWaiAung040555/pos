<script setup>

    import PageTitle from '@/components/PageTitle.vue';
    import BaseButton from '@/components/BaseButton.vue';
    import BaseCard from '@/components/BaseCard.vue';
    import SubTitle from '@/components/SubTitle.vue';
    import { useRouter } from 'vue-router';
    import BaseInput from '@/components/BaseInput.vue';
    import BaseTextarea from '@/components/BaseTextarea.vue';
    import { ref } from 'vue';
    import { useBranchStore } from '@/stores/useBranchStore';
    import { useToast } from 'primevue/usetoast';
    import BaseSwitch from '@/components/BaseSwitch.vue';
    import BaseLabel from '@/components/BaseLabel.vue';
    

    const router = useRouter();

    const toast = useToast();

    const useBranch = useBranchStore();

    const formData = ref(
      {
        name: "",
        phone: "",
        location: "",
        status_id: "1",
        created_by: "1",
        updated_by: ""
      }
    )

    const branchStatus = ref(true);

    function changeRoute(pathname) {
        router.push(pathname);
    }

    async function formSubmit() {
        console.log(formData.value);
        console.log(branchStatus.value);
        formData.value = {
            ...formData.value,
            status_id: branchStatus.value? '1' : '2'
        };
        console.log("After:" + formData.value)
        await useBranch.addBranch(formData.value);
        if(useBranch.error) {
            console.log("Api Error:" + JSON.stringify(useBranch.error));
            Object.values(useBranch.error).forEach((err) => {
                err.forEach((msg) => {
                    toast.add({ severity: 'error', summary: 'Error Message', detail: msg, life: 3000 });
                })
            })
            return
        }
        if (useBranch.branchList) {
            toast.add({ severity: 'success', summary: 'Success Message', detail: 'Branch created successfully.', life: 3000 });
            router.push('/branch');
        }
        

    }

</script>

<template>
    <div class="p-4">
        <PageTitle title="Create Branch">
            <template #titleButtons>
                <div class="flex gap-x-2 items-center">
                    <BaseButton icon="fa fa-chevron-left" label="Back" severity="secondary" @click="changeRoute('/branch')"  />
                </div>
            </template>
        </PageTitle>
        <BaseCard class="mt-3">
            <template #cardElements>
                <SubTitle label="Basic Info" />
                <div class="flex gap-x-4 mt-6">
                    <BaseInput
                        size="sm"
                        v-model="formData.name"
                        label="Name"
                        placeholder="Name"
                        width="300px"
                        height="h-[35px]"
                    />
                    <div class="flex flex-col gap-y-1 w-[200px]">
                        <BaseLabel label="Status" />
                        <BaseSwitch v-model="branchStatus" />

                    </div>
                </div>
                <div class="flex gap-x-4 mt-4">
                    <BaseInput
                        size="sm"
                        v-model="formData.phone"
                        label="Phone Number"
                        placeholder="Phone"
                        width="300px"
                        height="h-[35px]"
                    />
                </div>
                <div class="flex gap-x-4 mt-4">
                    <BaseTextarea
                        v-model="formData.location"
                        label="Location"
                        placeholder="Address"
                        autoResize
                    />
                </div>
                <div class="flex justify-end mt-4">
                    <BaseButton label="Save" :isLoading="useBranch.loading" :icon="useBranch.loading? 'fa fa-spinner' : 'fa fa-floppy-disk'" severity="primary" @click="formSubmit" :disabled="useBranch.loading"  />
                </div>
            </template>
        </BaseCard>
    </div>
</template>
