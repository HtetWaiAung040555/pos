<script setup>

    import PageTitle from '@/components/PageTitle.vue';
    import BaseButton from '@/components/BaseButton.vue';
    import BaseCard from '@/components/BaseCard.vue';
    import SubTitle from '@/components/SubTitle.vue';
    import { useRouter } from 'vue-router';
    import BaseInput from '@/components/BaseInput.vue';
    import BaseTextarea from '@/components/BaseTextarea.vue';
    import { ref } from 'vue';

    const router = useRouter();

    const formData = ref(
      {
        name: "",
        status_id: "1",
        location: "",
        created_by: "",
        updated_by: ""
      }
    )

    function changeRoute(pathname) {
        router.push(pathname);
    }

    function formSubmit() {
        console.log(formData.value);
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
                    <BaseInput
                        size="sm"
                        v-model="formData.name"
                        label="Name"
                        placeholder="Name"
                        width="300px"
                        height="h-[35px]"
                    />
                </div>
                <div class="flex gap-x-4 mt-4">
                    <BaseTextarea
                        v-model="formData.location"
                        label="Address"
                        placeholder="Address"
                        height="50px"
                    />
                </div>
                <div class="flex justify-end mt-4">
                    <BaseButton label="Save" icon="fa fa-user" severity="primary" @click="formSubmit" />
                </div>
            </template>
        </BaseCard>
    </div>
</template>
