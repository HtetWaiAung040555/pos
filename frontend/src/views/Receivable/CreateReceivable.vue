<script setup>

    import PageTitle from '@/components/PageTitle.vue';
    import BaseButton from '@/components/BaseButton.vue';
    import BaseCard from '@/components/BaseCard.vue';
    import SubTitle from '@/components/SubTitle.vue';
    import { useRouter } from 'vue-router';
    import BaseInput from '@/components/BaseInput.vue';
    import BaseTextarea from '@/components/BaseTextarea.vue';
    import { onMounted, ref } from 'vue';
    import { useToast } from 'primevue/usetoast';
    import BaseSwitch from '@/components/BaseSwitch.vue';
    import BaseLabel from '@/components/BaseLabel.vue';
    import { errMsgList } from '@/utils/const';
    import { Select } from 'primevue';
    import BaseErrorLabel from '@/components/BaseErrorLabel.vue';

    import { useReceivableStore } from '@/stores/useReceivableStore';
    import { usePaymentMethodStore } from '@/stores/usePaymentMethodStore';
    import { useCustomerStore } from '@/stores/useCustomerStore';

    
    const router = useRouter();
    const toast = useToast();
    const useReceivable = useReceivableStore();
    const usePaymentMethod = usePaymentMethodStore();
    const useCustomer = useCustomerStore();

    const formData = ref(
      {
        customer_id: "",
        amount: "",
        remark: "",
        payment_id: "",
        pay_date: new Date().toISOString().slice(0, 10),
        created_by: "",
        updated_by: ""
      }
    )
    
    const selectedPaymentMethod = ref('');
    const selectedCustomer = ref('');

    const userData = ref({});
    const errorMsg = ref({
        paymentMethod: "",
        customer: ""
    });

    // Change route function
    function changeRoute(pathname) {
        router.push(pathname);
    }

    onMounted(async () => {
        userData.value = JSON.parse(localStorage.getItem('user'));
        await usePaymentMethod.fetchAllPaymentMethod();
        await useCustomer.fetchAllCustomer();
    });

    // Create function
    async function formSubmit() {
        if (!selectedPaymentMethod.value) {
            errorMsg.value = {
                paymentMethod: errMsgList.paymentMethod,
                customer: ""
            }
            return
        } else if ("") {}


        formData.value = {
            ...formData.value,
            amount: Number(formData.value.amount),
            payment_id: selectedPaymentMethod.value.id,
            customer_id: selectedCustomer.value.id,
            created_by: userData.value.id
        };

        await useReceivable.addReceivable(formData.value);

        if(useReceivable.error) {
            Object.values(useReceivable.error).forEach((err) => {
                err.forEach((msg) => {
                    toast.add({ severity: 'error', summary: 'Error Message', detail: msg, life: 3000 });
                })
            })
            return
        }
        if (useReceivable.receivableList) {
            toast.add({ severity: 'success', summary: 'Success Message', detail: 'Receivable created successfully.', life: 3000 });
            router.push('/receivable');
        }
    }

</script>

<template>
    <div class="p-4">
        <!-- Page Title -->
        <PageTitle title="Pay Credit">
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
                    <!-- Customer Input --> 
                    <div class="flex flex-col gap-y-1">
                        <BaseLabel 
                            label="Customer"
                            :isRequire="true"
                        />
                        <Select 
                            v-model="selectedCustomer" 
                            :options="useCustomer.customerList" 
                            showClear
                            filter
                            optionLabel="name"
                            placeholder="Select a customer"
                            class="w-[300px] h-[35px] items-center" 
                        />
                    </div>
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
                            optionLabel="name"
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
                    <!-- Save Button -->
                    <BaseButton 
                        label="Save" 
                        :isLoading="useReceivable.loading" :icon="useReceivable.loading? 'fa fa-spinner' : 'fa fa-floppy-disk'" 
                        severity="primary" 
                        @click="formSubmit" 
                        :disabled="useReceivable.loading"  
                    />
                </div>
            </template>
        </BaseCard>
    </div>
</template>
