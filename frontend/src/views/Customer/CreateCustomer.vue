<script setup>

import PageTitle from '@/components/PageTitle.vue';
import BaseButton from '@/components/BaseButton.vue';
import BaseCard from '@/components/BaseCard.vue';
import SubTitle from '@/components/SubTitle.vue';
import { useRouter } from 'vue-router';
import BaseInput from '@/components/BaseInput.vue';
import BaseTextarea from '@/components/BaseTextarea.vue';
import { onMounted, ref, watch } from 'vue';
import QRCode from 'qrcode';
import { useToast } from 'primevue/usetoast';
import BaseSwitch from '@/components/BaseSwitch.vue';
import BaseLabel from '@/components/BaseLabel.vue';
import { errMsgList } from '@/utils/const';
import { useCustomerStore } from '@/stores/useCustomerStore';

const router = useRouter();
const toast = useToast();
const useCustomer = useCustomerStore();

const formData = ref(
    {
        id: "",
        name: "",
        phone: "",
        address: "",
        status_id: "1",
        is_default: false,
        created_by: "1",
        updated_by: ""
    }
)
const qrDataUrl = ref('');
const customerStatus = ref(true);
const userData = ref({});
const errorMsg = ref({
    name: "",
});

// Change route function
function changeRoute(pathname) {
    router.push(pathname);
}

onMounted(async () => {
    userData.value = JSON.parse(localStorage.getItem('user'));
    await useCustomer.fetchLastCustomerId();
    console.log(useCustomer.lastId);
    // generate initial auto code
    generateCustomerCode();
});

// Watch code field and generate QR whenever it changes
watch(() => formData.value.id, async (newVal) => {
    if (!newVal) {
        qrDataUrl.value = '';
        return;
    }
    try {
        qrDataUrl.value = await QRCode.toDataURL(newVal, { width: 300 });
    } catch (err) {
        console.error('Failed to generate QR', err);
        qrDataUrl.value = '';
    }
});

// 1) Auto-generate customer code (e.g., FMC-0001)
function generateCustomerCode() {
    // Default values when there is no lastId
    const defaultPrefix = 'KBAM';
    const defaultSep = '-';

    const lastRaw = useCustomer.lastId;
    console.log('Last ID:', lastRaw);
    let lastSerial = 0;
    let pad = 4; // default serial padding
    let prefix = defaultPrefix;
    let sep = defaultSep;

    if (lastRaw) {
        // Try to extract trailing digits and use their length as padding
        const m = lastRaw.match(/(\d+)$/);
        if (m) {
            lastSerial = parseInt(m[1], 10) || 0;
            pad = m[1].length;
            // prefix part is everything before the digits
            let p = lastRaw.slice(0, lastRaw.length - m[1].length).trim();
            if (p.endsWith('-') || p.endsWith('_')) {
                sep = p.slice(-1);
                prefix = p.slice(0, -1);
            } else if (p.length > 0) {
                // No explicit separator, keep p as prefix and use default sep
                prefix = p;
            }
        } else if (!isNaN(Number(lastRaw))) {
            // lastRaw is numeric string like "123"
            lastSerial = Number(lastRaw);
            pad = Math.max(4, String(lastSerial).length);
        }
    }

    const next = lastSerial + 1;
    const serial = String(next).padStart(pad, '0');
    formData.value.id = `${prefix}${sep}${serial}`;
}

// 2) Print QR code image (opens printable window)
function printQr() {
    if (!qrDataUrl.value) return;
    const printWindow = window.open('', '_blank', 'width=400,height=600');
    if (!printWindow) {
        alert('Unable to open print window. Please allow popups.');
        return;
    }
    printWindow.document.write(`
        <!doctype html>
        <html>
            <head>
                <title>Print QR</title>
                <style>
                    body{
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        height: 100vh;
                        margin: 0;
                        }
                    img{
                        max-width: 100%;
                    }
                    .print-container {
                        display: flex;
                        flex-direction: column;
                        align-item: center;
                        justify-content: center;
                    }
                    .print-container p {
                        margin: 0;
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class='print-container'>
                    <img src="${qrDataUrl.value}" alt="QR"/>
                    <p>${formData.value.id}</p>
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    // Give the image a moment to load then print
    printWindow.focus();
    setTimeout(() => {
        printWindow.print();
        // Optionally close after print
        // printWindow.close();
    }, 500);
}

// Create branch function
async function formSubmit() {
    if (formData.value.name === "") {
        errorMsg.value = {
            name: errMsgList.name,
        }
        return
    }
    formData.value = {
        ...formData.value,
        created_by: userData.value.id,
        status_id: customerStatus.value ? '1' : '2'
    };
    // Ensure QR is generated for the code before submit (if needed server-side)
    if (!qrDataUrl.value && formData.value.id) {
        try {
            qrDataUrl.value = await QRCode.toDataURL(formData.value.id, { width: 200 });
        } catch (err) {
            console.error('Failed to generate QR before submit', err);
        }
    }
    await useCustomer.addCustomer(formData.value);
    if (useCustomer.error) {
        Object.values(useCustomer.error).forEach((err) => {
            err.forEach((msg) => {
                toast.add({ severity: 'error', summary: 'Error Message', detail: msg, life: 3000 });
            })
        })
        return
    }
    if (useCustomer.customerList) {
        toast.add({ severity: 'success', summary: 'Success Message', detail: 'Customer created successfully.', life: 3000 });
        router.push('/customer');
    }
}

</script>

<template>
    <div class="p-4">
        <!-- Page Title -->
        <PageTitle title="Create Customer">
            <template #titleButtons>
                <div class="flex gap-x-2 items-center">
                    <BaseButton icon="fa fa-chevron-left" label="Back" severity="secondary"
                        @click="changeRoute('/customer')" />
                </div>
            </template>
        </PageTitle>
        <!-- Form Section -->
        <BaseCard class="mt-3">
            <template #cardElements>
                <!-- Form section subtitle -->
                <SubTitle label="Basic Info" />
                <div class="flex gap-x-4 mt-6">
                    <!-- <BaseInput
                        size="sm"
                        v-model="formData.id"
                        label="Code"
                        placeholder="Code"
                        width="300px"
                        height="h-[35px]"
                        :isRequire="true"
                        :error="errorMsg.name"
                    /> -->
                    <!-- QR Display and Print -->
                    <div class="flex items-center gap-x-3 ml-4">
                        <div class="flex flex-col gap-y-2 items-center justify-center">
                            <div class="w-[140px] h-[140px] bg-white flex items-center justify-center border">
                                <img v-if="qrDataUrl" :src="qrDataUrl" alt="QR Code" class="max-w-full max-h-full" />
                                <div v-else class="text-xs text-gray-400">QR will appear here</div>
                            </div>
                            <BaseLabel v-if="formData.id" :label="formData.id" class="text-sm" />
                        </div>
                        <div class="flex flex-col items-center gap-y-2">
                            <BaseButton label="Generate" severity="secondary" @click="generateCustomerCode" />
                            <BaseButton label="Print QR" severity="primary" @click="printQr" :disabled="!qrDataUrl" />
                        </div>
                    </div>
                </div>
                <div class="flex gap-x-4 mt-6">
                    <!-- Customer Name Input -->
                    <BaseInput size="sm" v-model="formData.name" label="Name" placeholder="Name" width="300px"
                        height="h-[35px]" :isRequire="true" :error="errorMsg.name" />
                    <!-- Customer Status -->
                    <div class="flex flex-col gap-y-1 w-[200px]">
                        <BaseLabel label="Status" />
                        <BaseSwitch v-model="customerStatus" />
                    </div>
                </div>
                <div class="flex gap-x-4 mt-4">
                    <!-- Phone number input -->
                    <BaseInput size="sm" v-model="formData.phone" label="Phone Number" placeholder="Phone" width="300px"
                        height="h-[35px]" />
                    <!-- Default Status -->
                    <div class="flex flex-col gap-y-1 w-[200px]">
                        <BaseLabel label="Default" />
                        <BaseSwitch v-model="formData.is_default" />
                    </div>
                </div>
                <div class="flex gap-x-4 mt-4">
                    <!-- Address input -->
                    <BaseTextarea v-model="formData.address" label="Location" placeholder="Address" autoResize />
                </div>
                <div class="flex justify-end mt-4">
                    <!-- Save Button -->
                    <BaseButton label="Save" :isLoading="useCustomer.loading"
                        :icon="useCustomer.loading ? 'fa fa-spinner' : 'fa fa-floppy-disk'" severity="primary"
                        @click="formSubmit" :disabled="useCustomer.loading" />
                </div>
            </template>
        </BaseCard>
    </div>
</template>
