<script setup>

    import PageTitle from '@/components/PageTitle.vue';
    import DataTable from '@/components/DataTable.vue';

    const columns = [
        { key: 'name', label: 'Name' },
        { key: 'email', label: 'Email' },
        { key: 'role', label: 'Role' }
    ];

    const users = [
        { name: 'Htet Wai Aung', email: 'htet@example.com', role: 'Admin' },
        { name: 'John Doe', email: 'john@example.com', role: 'User' },
        { name: 'Jane Smith', email: 'jane@example.com', role: 'User' },
        { name: 'Michael Chan', email: 'michael@example.com', role: 'Manager' },
    ];

</script>

<template>
  <div class="p-4">
    <PageTitle title="Counter List" />
    <DataTable class="mt-3" :columns="columns" :rows="users" :pageSize="5">
      <template #filters>
        <div class="flex gap-2">
          <input type="date" class="border rounded px-2 py-1" />
          <input type="date" class="border rounded px-2 py-1" />
          <input
            type="text"
            v-model="search"
            placeholder="Search..."
            class="border rounded px-2 py-1"
          />
        </div>
      </template>
    </DataTable>
  </div>
</template>
