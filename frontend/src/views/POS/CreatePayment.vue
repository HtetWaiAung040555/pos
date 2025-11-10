<script setup>
  import { useRoute, useRouter } from 'vue-router';
  import { ref, computed, onMounted } from 'vue';
  import moment from 'moment';

  import BaseInput from '@/components/BaseInput.vue';
  import BaseLabel from '@/components/BaseLabel.vue';
  import BaseTextarea from '@/components/BaseTextarea.vue';
  import BaseButton from '@/components/BaseButton.vue';
  import { useSaleStore } from '@/stores/useSalesStore';
import { useStatusStore } from '@/stores/useStatusStore';
import { usePaymentMethodStore } from '@/stores/usePaymentMethodStore';
import { useToast } from 'primevue';

  const router = useRouter();
  const route = useRoute();
  const toast = useToast();
  const useSales = useSaleStore();
  const useStatus = useStatusStore();
  const usePaymentMethod = usePaymentMethodStore();

  const salesData = ref({});
  const userData = ref({});

  const data = ref({
    // Payment Form
    payAmount: 0,
    note: "",
    date: moment().format("DD/MM/YY hh:mm"),
    currency: 'Ks. ',
    taxRate: 3,
    payment_id: 1,
    status_id: '',
  });

  onMounted(async() => {
    await useSales.fetchSales(route.query.id);
    salesData.value = useSales.salesList;
    userData.value = JSON.parse(localStorage.getItem('user'));
    data.value.payAmount = salesData.value.total_amount;
    await useStatus.fetchAllStatus();
    data.value.status_id = useStatus.statusList.find(el => el.name === 'Complete').id;
    await usePaymentMethod.fetchAllPaymentMethod();
  });

  const subtotal = computed(() => {
    return salesData.value.details?.reduce((sum, item) => sum + item.quantity * item.price, 0);
  });

  const tax = computed (() => {
    return (subtotal.value * data.value.taxRate) / 100;
  });

  const total = computed(() => {
    return
  });


  const changeReturn = computed(() => {
    const received = parseFloat(salesData.value.total_amount);
    const paying = parseFloat(data.value.payAmount);
    const change = paying - received;
    return change;
  });

  async function formSubmit() {
    const payload = {
      sale_date: data.value.date,
      payment_id: data.value.payment_id,
      paid_amount: parseFloat(data.value.payAmount),
      due_amount: changeReturn.value,
      remark: data.value.note,
      status_id: data.value.status_id,
      updated_by: userData.value.id
    }
    console.log(payload);
    await useSales.editSales(salesData.value.id, payload);
    if(useSales.error) {
      Object.values(useSales.error).forEach((err) => {
          err.forEach((msg) => {
              toast.add({ severity: 'error', summary: 'Error Message', detail: msg, life: 3000 });
          })
      })
      return
    }
    if (useSales.salesList) {
      toast.add({ severity: 'success', summary: 'Success Message', detail: 'Sales created successfully.', life: 3000 });
      router.push('/sales');
    }
  }

  async function formSubmitAndPrint() {
      console.log('Submit and print clicked');
      // First submit the form (persist payment) then print slip
      try {
        await formSubmit();
      } catch (err) {
        console.error('Error submitting before print', err);
      }
      printSlip();
  }

  async function formCancel() {
      console.log('Submit clicked');
  }

  function changePaymentMethod(e) {
    
  }

  // Print only the slip section between the markers
  function printSlip() {
    const slip = document.getElementById('slip-section');
    if (!slip) {
      alert('Slip section not found');
      return;
    }

    // Build minimal printable document
    const printWindow = window.open('', '', 'width=400,height=600')
    if (!printWindow) {
      alert('Unable to open print window. Please allow popups.');
      return;
    }

    const doc = printWindow.document;
    const html = `
      <!doctype html>
      <html>
        <head>
          <meta charset="utf-8" />
          <title>Receipt</title>
          <style>
            /* ============ PRINT STYLES FOR 80MM THERMAL RECEIPT ============ */
            @page {
              size: 384px auto;
              margin: 5mm;
            }

            body {
              width: 384px;
              font-family: 'Courier New', monospace;
              font-size: 11px;
              color: #000;
              margin: 0 auto;
              padding: 0;
              line-height: 1.3;
            }

            

            /* Hide anything extra in print */
            @media print {
              body {
                width: 80mm;
              }
            }
          </style>
        </head>
        <body>
          ${slip.innerHTML}
        </body>
      </html>
    `;

    doc.open();
    doc.write(html);
    doc.close();

    // Wait a short time to ensure images/fonts load
    printWindow.focus();
    setTimeout(() => {
      printWindow.print();
      // Optionally close window after printing
      // printWindow.close();
    }, 500);
  }
   
    
</script>

<template>
  <div class="p-4">
    <h3 class="text-black text-xl font-bold border-b pb-2 mb-4">Make Payment</h3>

    <div class="flex gap-4 items-start">
      <!-- PAYMENT FORM -->
      <div
        class="flex-[1.2] grid grid-cols-2 gap-4 bg-white p-6 rounded-sm border border-gray-300 shadow-sm"
      >
        <div class="flex flex-col">
          <BaseLabel label="Received Amount :" />
          <BaseInput
            size="sm"
            v-model="data.payAmount"
            type="number"
            width="350px"
            height="h-[35px]"
            
          />
        </div>

        <div class="flex flex-col">
          <BaseLabel label="Paying Amount:" />
          <BaseInput
            size="sm"
            v-model="salesData.total_amount"
            width="350px"
            height="h-[35px]"
            disabled
          />
        </div>

        <div class="flex flex-col">
          <BaseLabel label="Change Return :" />
          <BaseInput size="sm" 
            v-model="changeReturn" 
            width="350px" 
            height="h-[35px]"
            disabled 
          />
        </div>

        <div class="flex flex-col">
          <BaseLabel label="Payment Type:" />
          <select
            class="text-md border border-gray-500 rounded-sm p-2 text-black w-[350px] h-[35px]"
            v-model="data.payment_id"
            @change="changePaymentMethod"
          >
            <option v-for="pm in usePaymentMethod.paymentMethodList" :value="pm.id">
              {{ pm.name }}
            </option>
          </select>
        </div>

        <div class="flex flex-col col-span-2">
          <BaseLabel label="Note:" />
          <BaseTextarea
            v-model="data.note"
            placeholder="Enter Note"
            autoResize
            class="w-full text-black"
          />
        </div>

        <div class="flex flex-col col-span-2">
          <BaseLabel label="Payment Status:" />
          <select
            class="text-md border border-gray-500 rounded-sm p-2 text-black w-full h-[35px]"
            v-model="data.status_id"
          >
            <option 
              v-for="status in useStatus.statusList.filter(el => el.name === 'Complete' || el.name === 'Unpaid')"
              :value="status.id"
            >
              {{ status.name === 'Complete'? 'Paid' : status.name }}
            </option>
          </select>
        </div>


        <div class="flex gap-3 mt-5">
            <BaseButton label="Submit" @click="formSubmit" />
            <BaseButton label="Submit & Print" @click="formSubmitAndPrint" />
            <BaseButton label="Cancel" severity="danger" @click="formCancel" />
        </div>

      </div>

    <!-- Start of Slip Section-->
      <div class="flex-[1.8] max-w-md w-full mx-auto p-6 bg-white shadow-lg border border-gray-300 rounded-sm text-sm font-mono text-black" id="slip-section" >
        <!-- Header -->
        <header 
          style="
            text-align: center;
            padding-bottom: 6px;
            margin-bottom: 6px;
            border-bottom: 1px solid black;
          "
        >
          <h1 class="text-lg font-bold">FUSION MART</h1>
          <div>53 Street, Between 36 & 37 ST (MA-68/2), Ye Mon Taung Quater, Mandalay</div>
          <div>Tel: +959740010055</div>
        </header>

        <!-- Receipt Info -->
        <div
          style="
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px dashed black;
          "
        >
          <div>
            <div>
              <span style="font-weight: bold;">Receipt:</span> {{ salesData.id }}
            </div>
            <div><span style="font-weight: bold;">Counter:</span> {{ userData.counter?.name }}</div>
          </div>
          <div style="text-align: left;">
            <div><span style="font-weight: bold;">Cashier:</span> {{ userData.name }}</div>
            <div><span style="font-weight: bold;">Date:</span> {{ data.date }}</div>
          </div>
        </div>

        <!-- Items Table -->
        <table
          style="
            width: 100%;
            font-size: 12px;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 8px;
          "
        >
          <thead>
            <tr 
              style="
                font-weight: bold;
                text-align: left;
              "
            >
              <th style="padding: 2px 0;">Description</th>
              <th style="padding: 2px 0; text-align: center;">Qty</th>
              <th style="padding: 2px 0; text-align: right;">Price</th>
              <th style="padding: 2px 0; text-align: right;">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in salesData.details"
              :key="item.id"
              style="border-top: 1px solid #dee2e6;"
            >
              <td style="padding: 2px 0; width: 150px;">
                <span
                 style="
                  overflow: hidden;
                  display: -webkit-box;
                  -webkit-box-orient: vertical;
                  -webkit-line-clamp: 2;
                 "
                >
                  {{ item.product.name }}
                </span>
              </td>
              <td style="padding: 2px 0; text-align: center;">{{ item.quantity }}</td>
              <td style="padding: 2px 0; text-align: right;">{{ Number(item.price).toLocaleString() }}</td>
              <td style="padding: 2px 0; text-align: right;">
                {{ (item.quantity * item.price).toLocaleString() }}
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Totals -->
        <div style="text-align: right; margin-bottom: 16px;">
          <div
            style="
              display: flex;
              justify-content: space-between;
              margin-bottom: 4px;
            "
          >
            <span>SUBTOTAL</span>
            <span>{{ data.currency + Number(subtotal).toLocaleString() }}</span>
          </div>
          <!-- <div class="flex justify-between">
            <span>TAX ({{ data.taxRate }}%)</span>
            <span>{{ data.currency + tax.toLocaleString() }}</span>
          </div> -->
          <div
            style="
              display: flex;
              justify-content: space-between;
              font-size: large;
              font-weight: bold;
              border-top: 1px solid black;
              padding-top: 4px;
            "
          >
            <span>TOTAL</span>
            <span>{{ data.currency + Number(subtotal).toLocaleString() }}</span>
            <!-- <span>{{ data.currency + (subtotal + tax).toLocaleString() }}</span> -->
          </div>
        </div>

        <!-- Footer -->
        <footer
          style="
            text-align: center;
            border-top: 1px dashed black;
            padding-top: 8px;
            font-size: 12px;
          "
        >
          <div>Thanks for shopping with us!</div>
          <div>Keep this receipt for your records</div>
        </footer>
      </div>

    <!-- End of Slip Section -->
    </div>

  </div>
</template>




