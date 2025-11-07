<script setup>
    import { useRouter } from 'vue-router';
    import { ref, computed, onMounted } from 'vue';
    import moment from 'moment';

    import BaseInput from '@/components/BaseInput.vue';
    import BaseLabel from '@/components/BaseLabel.vue';
    import BaseTextarea from '@/components/BaseTextarea.vue';
    import BaseButton from '@/components/BaseButton.vue';

    const router = useRouter();

    const data = ref({
      // Payment Form
      receivedAmount: "15000",
      payingAmount: "10000",
      changeReturn: "",
      note: "",

      //  Slip
      store: {
        address: '53 Street, Between 36 & 37 ST (MA-68/2), Ye Mon Taung Quater, Mandalay',
        phone: '+959740010055',
      },
      receiptNo: '20250922043768',
      cashier: 'Ma Su Latt',
      counter: 'C-001',
      date: moment().format("DD/MM/YY hh:mm"),
      currency: 'Ks. ',
      taxRate: 3,
      items: [
          { id: 1, name: 'UltraComfort Ergonomic Mesh Office Chair for Home Theater & Outdoor Use ', qty: 2000, price: 1000000 },
          { id: 2, name: 'SmartSync Ultra 4K Wi-Fi Projector for Home Theater & Outdoor Use', qty: 100, price: 20000 },
          { id: 3, name: 'RadiantGlow Vitamin C Brightening Serum for Home Theater & Outdoor Use', qty: 500, price: 4500 },
          { id: 4, name: 'ProBlend 900-Watt High-Speed Countertop for Home Theater & Outdoor Use', qty: 200, price: 250000 },
      ],
    });

    const subtotal = computed(() => {
      return data.value.items.reduce((sum, item) => sum + item.qty * item.price, 0);
    });

    const tax = computed (() => {
      return (subtotal.value * data.value.taxRate) / 100;
    });

    const total = computed(() => {
      return
    });


    const changeReturn = computed(() => {
        const received = parseFloat(data.value.receivedAmount);
        const paying = parseFloat(data.value.payingAmount);
        const change = received - paying;
        return change > 0 ? change.toFixed(2) : '0.00';
    });


    async function formSubmit() {
        console.log('Submit clicked');
    }

    async function formSubmitAndPrint() {
        console.log('Submit and print clicked');
    }

    async function formCancel() {
        console.log('Submit clicked');
    }
   
    
</script>

<template>
  <div class="p-4">
    <h3 class="text-black text-xl font-bold border-b pb-2 mb-4">Make Payment</h3>

    <div class="flex gap-4 items-start">
      <!-- PAYMENT FORM -->
      <form
        class="flex-[1.2] grid grid-cols-2 gap-4 bg-white p-6 rounded-sm border border-gray-300 shadow-sm"
      >
        <div class="flex flex-col">
          <BaseLabel label="Received Amount :" />
          <BaseInput
            size="sm"
            v-model="data.receivedAmount"
            width="350px"
            height="h-[35px]"
          />
        </div>

        <div class="flex flex-col">
          <BaseLabel label="Paying Amount:" />
          <BaseInput
            size="sm"
            v-model="data.payingAmount"
            width="350px"
            height="h-[35px]"
            disabled
          />
        </div>

        <div class="flex flex-col">
          <BaseLabel label="Change Return :" />
          <BaseInput size="sm" 
            :model-value="changeReturn" 
            width="350px" 
            height="h-[35px]" 
            disabled 
          />
        </div>

        <div class="flex flex-col">
          <BaseLabel label="Payment Type:" />
          <select
            class="text-md border border-gray-500 rounded-sm p-2 text-black w-[350px] h-[35px]"
          >
            <option>Cash</option>
            <option>Cheque</option>
            <option>Bank Transfer</option>
            <option>Other</option>
            <option>Kpay</option>
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
          >
            <option selected>Paid</option>
            <option>Unpaid</option>
          </select>
        </div>


        <div class="flex gap-3 mt-5">
            <BaseButton label="Submit" @click="formSubmit" />
            <BaseButton label="Submit & Print" @click="formSubmitAndPrint" />
            <BaseButton label="Cancel" severity="danger" @click="formCancel" />
        </div>

      </form>

      
     


      <!-- SLIP -->
      <div class="flex-[1.8] max-w-md w-full mx-auto p-6 bg-white shadow-lg border border-gray-300 rounded-sm text-sm font-mono text-black">
        <!-- Header -->
        <header class="text-center pb-3 mb-3 border-b">
          <h1 class="text-lg font-bold">FUSION MART</h1>
          <p>{{ data.store.address }}</p>
          <p>Tel: {{ data.store.phone }}</p>
        </header>

        <!-- Receipt Info -->
        <div class="flex justify-between text-xs mb-4 border-b border-dashed pb-2">
          <div>
            <p><span class="font-semibold">Receipt:</span> {{ data.receiptNo }}</p>
            <p><span class="font-semibold">Counter:</span> {{ data.counter }}</p>
          </div>
          <div class="text-left">
            <p><span class="font-semibold">Cashier:</span> {{ data.cashier }}</p>
            <p><span class="font-semibold">Date:</span> {{ data.date }}</p>
          </div>
        </div>

        <!-- Items Table -->
        <table class="w-full text-xs border-b border-gray-300 mb-4">
          <thead>
            <tr class="font-semibold text-left border-b border-dashed">
              <th class="py-1">Description</th>
              <th class="py-1 text-center">Qty</th>
              <th class="py-1 text-right">Price</th>
              <th class="py-1 text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="item in data.items"
              :key="item.id"
              class="border-t border-gray-100"
            >
              <td class="py-1 w-[150px]">
                <span class="line-clamp-2">
                  {{ item.name }}
                </span>
              </td>
              <td class="py-1 text-center">{{ item.qty }}</td>
              <td class="py-1 text-right">{{ item.price.toLocaleString() }}</td>
              <td class="py-1 text-right">
                {{ (item.qty * item.price).toLocaleString() }}
              </td>
            </tr>
          </tbody>
        </table>

        <!-- Totals -->
        <div class="text-right space-y-1 mb-4">
          <div class="flex justify-between">
            <span>SUBTOTAL</span>
            <span>{{ data.currency + subtotal.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between">
            <span>TAX ({{ data.taxRate }}%)</span>
            <span>{{ data.currency + tax.toLocaleString() }}</span>
          </div>
          <div class="flex justify-between text-lg font-bold border-t pt-1">
            <span>TOTAL</span>
            <span>{{ data.currency + (subtotal + tax).toLocaleString() }}</span>
          </div>
        </div>

        <!-- Footer -->
        <footer class="text-center border-t border-dashed pt-2 text-xs">
          <p>Thanks for shopping with us!</p>
          <p>Keep this receipt for your records</p>
        </footer>
      </div>
    </div>

  </div>
</template>




