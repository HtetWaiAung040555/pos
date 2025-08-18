<script setup>
  import Button from 'primevue/button';
  import { useCollapseSidebar } from '@/stores/collapseSidebar';
import { ref } from 'vue';
import BaseButton from './BaseButton.vue';
import { useRouter } from 'vue-router';

  const collapseSidebar = useCollapseSidebar();

  const openDropdown = ref(false);

  const router = useRouter();

  function toggleDropdown() {
    openDropdown.value = !openDropdown.value;
  }

  function logout() {
    localStorage.removeItem("auth");
    router.push('/login');
  }

</script>

<template>
  <div class="w-full h-16 shadow flex items-center px-6 justify-between bg-[#ffffff]">
    <!-- Collapse Button -->
    <div class="flex justify-end ml-[-20px]">
      <Button severity="contrast" variant="text" @click="collapseSidebar.toggleSidebar" icon="pi pi-bars" rounded />
    </div>
    <div class="flex items-center gap-x-2">
      <BaseButton class="w-10 h-10" severity="primary" variant="outlined" icon="fa fa-question" rounded />
      <BaseButton class="w-10 h-10" severity="primary" variant="solid" icon="pi pi-bell" rounded />
      <div class="relative overflow-visible">
        <div
        class="flex justify-between text-black items-center bg-[#F8FAFC] hover:bg-gray-200 rounded-xl py-2 px-3 cursor-pointer"
        @click="toggleDropdown"
        >
          <div class="flex items-center gap-x-2">
            <i  class="fa fa-user-circle text-2xl"></i>
            <span
              class="text-sm transition-all duration-300 origin-left"
            >
              Htet Wai Aung
            </span>
            <!-- <i :class="openDropdown? 'fa fa-chevron-up' : 'fa fa-chevron-down'" class="text-sm"></i> -->
          </div>
        </div>
        <Transition name="fade">
          <div
            v-if="openDropdown"
            class="absolute right-0 mt-2 w-40 bg-white border-1 border-gray-100 text-black rounded shadow-lg z-10"
          >
            <div
              class="flex px-2 items-center py-3 gap-4 hover:bg-blue-100 cursor-pointer transition-all"
            >
              <i class="fa fa-user-gear text-lg"></i>
              <span>
                Setting
              </span>
            </div>
            <div
              class="flex px-2 items-center py-3 gap-4 hover:bg-blue-100 cursor-pointer transition-all"
              @click="logout"
            >
              <i class="fa fa-right-to-bracket text-lg"></i>
              <span>
                Logout
              </span>
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </div>
</template>

<style>
  .nav-bg {
    background-color: #007FFF;
  }
</style>