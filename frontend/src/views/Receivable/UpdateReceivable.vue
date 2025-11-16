<script setup>

    import PageTitle from '@/components/PageTitle.vue';
    import BaseButton from '@/components/BaseButton.vue';
    import { useRoute, useRouter } from 'vue-router';
    import { onMounted, ref } from 'vue';
    import BaseCard from '@/components/BaseCard.vue';
    import BaseInput from '@/components/BaseInput.vue';
    import BaseTextarea from '@/components/BaseTextarea.vue';
    import BaseLabel from '@/components/BaseLabel.vue';
    import BaseSwitch from '@/components/BaseSwitch.vue';
    import SubTitle from '@/components/SubTitle.vue';
    import { Select, useToast } from 'primevue';
    import { errMsgList } from '@/utils/const';
    import BaseErrorLabel from '@/components/BaseErrorLabel.vue';

    import { useReceivableStore } from '@/stores/useReceivableStore';
    import { usePaymentMethodStore } from '@/stores/usePaymentMethodStore';

    const router = useRouter();
    const route = useRoute();
    const toast = useToast();
    const useReceivable = useReceivableStore();
    const usePaymentMethod = usePaymentMethodStore();

    const formData = ref({});
    const userData = ref({});
    const selectedPaymentMethod = ref('');

    const errorMsg = ref({
        paymentMethod: ""
    });

    // Change route function
    function changeRoute(pathname) {
        router.push(pathname);
    }

    onMounted(async () => {
        await useReceivable.fetchReceivable(route.query.id);
        formData.value = useReceivable.receivableList;
        userData.value = JSON.parse(localStorage.getItem('user'));
    
        await usePaymentMethod.fetchAllPaymentMethod();
        selectedPaymentMethod.value = usePaymentMethod.paymentMethodList.filter(el => el.id === formData.value.paymentMethod.id)[0];
    });

    // Update function
    async function formSubmit() {
        if (!selectedPaymentMethod.value) {
            errorMsg.value = {
                paymentMethod: errMsgList.paymentMethod,
                customer: ""
            }
            return
        } else if ("") {}

        let updatedData = {
            type: formData.value.type,
            amount: formData.value.amount,
            payment_id: selectedPaymentMethod.value.id,
            remark: formData.value.remark,
            pay_date: formData.value.pay_date,
            updated_by: userData.value.id
        }
        await useReceivable.editReceivable(updatedData, route.query.id);

        if(useReceivable.error) {
            Object.values(useReceivable.error).forEach((err) => {
                err.forEach((msg) => {
                    toast.add({ severity: 'error', summary: 'Error Message', detail: msg, life: 3000 });
                })
            })
            return
        }

        if (useReceivable.receivableList) {
            toast.add({ severity: 'success', summary: 'Success Message', detail: 'Receivable updated successfully.', life: 3000 });
            router.push('/receivable');
        }
    }

</script>

<template>
    <div class="p-4">
        <!-- Page Title -->
        <PageTitle title="Update Receivable">
            <template #titleButtons>
                <div class="flex gap-x-2 items-center">
                    <BaseButton icon="fa fa-chevron-left" label="Back" severity="secondary" @click="changeRoute('/receivable')"  />
                </div>
            </template>
        </PageTitle>
        <!-- Form Section -->
        <BaseCard class="mt-3">
            <template #cardElements>
                <!-- Form section subtitle -->
                <SubTitle label="Basic Info" />
                <div class="flex gap-x-4 mt-6">
                    <!-- Receivable Type Input -->
                    <BaseInput
                        size="sm"
                        v-model="formData.type"
                        label="Type"
                        placeholder="Type"
                        width="300px"
                        height="h-[35px]"                   
                    />
                    <!-- Pay date Input -->
                    <BaseInput
                        size="sm"
                        v-model="formData.pay_date"
                        label="Pay Date"
                        placeholder="Pay Date"
                        width="300px"
                        height="h-[35px]"
                        type="date"   
                    />

                </div>
                <div class="flex gap-x-4 mt-4">
                     <!-- Amount input -->
                    <BaseInput
                        size="sm"
                        v-model="formData.amount"
                        label="Amount"
                        placeholder="Amount"
                        width="300px"
                        height="h-[35px]"
                    />
                    <!-- Payment Method Select -->
                    <div class="flex flex-col gap-y-1">
                        <BaseLabel 
                            label="Payment Method"
                            :isRequire="true"
                        />
                        <Select 
                            v-model="selectedPaymentMethod" 
                            :options="usePaymentMethod.paymentMethodList" 
                            showClear
                            filter
                            optionLabel="Payment Method"
                            placeholder="Select a payment method"
                            class="w-[300px] h-[35px] items-center" 
                        />
                        <BaseErrorLabel v-if="errorMsg.paymentMethod" :label="errorMsg.paymentMethod" />
                    </div>

                </div>
                <div class="flex gap-x-4 mt-4">
                     <!-- Remark input -->
                    <BaseTextarea
                        v-model="formData.remark"
                        label="Remark"
                        placeholder="Remark"
                        autoResize
                    />

                </div>
                <div class="flex justify-end mt-4">
                    <!-- Save button -->
                    <BaseButton 
                        label="Update" 
                        :isLoading="useReceivable.loading" 
                        :icon="useReceivable.loading? 'fa fa-spinner' : 'fa fa-floppy-disk'" 
                        severity="primary" 
                        @click="formSubmit" 
                        :disabled="useReceivable.loading"  
                    />
                </div>
            </template>
        </BaseCard>
    </div>
</template>
