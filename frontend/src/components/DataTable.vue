<script setup>
import { ref, computed } from 'vue';
import BaseButton from './BaseButton.vue';
import { useRouter } from 'vue-router';
import Loading from './Loading.vue';

const props = defineProps({
  columns: { type: Array, required: true }, // [{ key: 'name', label: 'Name' }]
  rows: { type: Array, required: true },    // your data array
  pageSize: { type: Number, default: 5 },
  isAction: {type: Boolean, default: true},
  editPath: {type: String, default: ""},
  deletePath: {type: String, default: ""},
  isLoading: {type: Boolean, default: false}
});

const router = useRouter();

const searchQuery = ref('');
const currentPage = ref(1);
const sortKey = ref(null);
const sortOrder = ref('asc'); // 'asc' or 'desc'

// Filtered rows by search
const filteredRows = computed(() => {
  if (!searchQuery.value) return props.rows;
  return props.rows.filter(row =>
    Object.values(row).some(val =>
      String(val).toLowerCase().includes(searchQuery.value.toLowerCase())
    )
  );
});

// Sorted rows
const sortedRows = computed(() => {
  if (!sortKey.value) return filteredRows.value;
  return [...filteredRows.value].sort((a, b) => {
    const aVal = a[sortKey.value];
    const bVal = b[sortKey.value];
    if (aVal < bVal) return sortOrder.value === 'asc' ? -1 : 1;
    if (aVal > bVal) return sortOrder.value === 'asc' ? 1 : -1;
    return 0;
  });
});

// Paginated rows
const paginatedRows = computed(() => {
  const start = (currentPage.value - 1) * props.pageSize;
  return sortedRows.value.slice(start, start + props.pageSize);
});

const totalPages = computed(() => Math.ceil(sortedRows.value.length / props.pageSize));

function changeSort(key) {
  if (sortKey.value === key) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortKey.value = key;
    sortOrder.value = 'asc';
  }
}

function changePage(page) {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
}

// Pagination Pages Array
const paginationPages = computed(() => {
  const pages = [];
  const total = totalPages.value;
  const current = currentPage.value;

  if (total <= 7) {
    // Show all pages if small
    for (let i = 1; i <= total; i++) pages.push(i);
  } else {
    // Always show first two pages
    pages.push(1);
    if (current > 3) pages.push('...');

    const startPage = Math.max(2, current - 1);
    const endPage = Math.min(total - 1, current + 1);

    for (let i = startPage; i <= endPage; i++) pages.push(i);

    if (current < total - 2) pages.push('...');
    pages.push(total);
  }
  return pages;
});

// function changeRoute(pathname, id) {
//   router.push({name: '/branch/update', params:{"branchId": id}});
// }

</script>

<template>
  <div class="bg-white text-black rounded-lg shadow p-4">
    <!-- Search -->
    <div class="mb-3">
      <slot name="filters">
        <!-- Default fallback: search input -->
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Search..."
          class="w-full border rounded px-3 py-2 text-sm"
        />
      </slot>
    </div>

    <div class="flex flex-col h-[250px] overflow-hidden">

      <!-- Table -->
      <div class="flex-1 overflow-y-auto">
        <table class="w-full">
          <thead class="sticky top-0 z-10">
            <tr class="bg-gray-100">
              <th
                v-for="col in columns"
                :key="col.key"
                class="px-4 py-2 cursor-pointer select-none text-black"
                @click="changeSort(col.key)"
              >
                {{ col.label }}
                <span class="text-[10px]" v-if="sortKey === col.key">
                  {{ sortOrder === 'asc' ? '▲' : '▼' }}
                </span>
              </th>
              <th 
                v-if="props.isAction"
                class="px-4 py-2 cursor-pointer select-none text-black"
              >
                Action
              </th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="paginatedRows.length === 0 && !isLoading">
              <td :colspan="columns.length" class="text-center py-4 text-gray-500">No data found</td>
            </tr>
            <tr v-if="isLoading">
              <td :colspan="columns.length" class="py-4 text-gray-500">
                <div class="flex items-center justify-center gap-2">
                  <Loading />
                </div>
              </td>
            </tr>
            <tr
              v-for="(row, idx) in paginatedRows"
              :key="idx"
              class="hover:bg-gray-50 text-[13px]"
            >
              <td
                v-for="col in columns"
                :key="col.key"
                class="p-2 text-center"
                v-html="col.formatter ? col.formatter(row) : row[col.key]"
              >
              </td>
              <td class="p-2 text-center w-[120px]" v-if="props.isAction">
                <router-link :to="{name: props.editPath, query: {id: row.id}}">
                  <BaseButton icon="fa fa-pencil" variant="text" severity="info" size="sm" />
                </router-link>
                <router-link :to="{name: props.deletePath, query: {id: row.id}}">
                  <BaseButton icon="fa fa-trash" variant="text" severity="danger" size="sm" />
                </router-link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="sticky bottom-0 bg-white border-t border-gray-200 p-3">
        <div class="flex items-center justify-end mt-3 gap-2">
          <BaseButton 
            icon="fa fa-chevron-left" 
            variant="text"
            severity="contrast"
            size="sm"
            class="w-6 h-6"
            rounded
            :disabled="currentPage === 1"
            @click="changePage(currentPage - 1)"
          />
          <!-- Page Numbers -->
          <template v-for="(page, index) in paginationPages" :key="index">
            <span
              v-if="page === '...'"
              class="px-2 text-gray-500 select-none"
            >
              ...
            </span>
            <BaseButton
              v-else
              :label="String(page)"
              :variant="page === currentPage ? 'solid' : 'text'"
              severity="contrast"
              size="sm"
              class="w-6 h-6"
              rounded
              @click="changePage(page)"
            />
          </template>
          <!-- <span class="px-2 py-1">{{ currentPage }} / {{ totalPages }}</span> -->
          <BaseButton 
            icon="fa fa-chevron-right" 
            variant="text"
            severity="contrast"
            size="sm"
            class="w-6 h-6"
            rounded
            :disabled="currentPage === totalPages"
            @click="changePage(currentPage + 1)"
          />
        </div>
      </div>
    </div>
  </div>
</template>
