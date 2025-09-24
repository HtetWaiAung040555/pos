<script setup>
  import { useCollapseSidebar } from '@/stores/collapseSidebar';
import { usePermissionStore } from '@/stores/usePermissionStore';
  import { onMounted, ref } from 'vue';
  import { useRouter } from 'vue-router';

  const collapseSidebar = useCollapseSidebar();
  const router = useRouter();
  const usePermission = usePermissionStore();
  const openDropdown = ref(null);

  function toggleCollapse() {
      isCollapsed.value = !isCollapsed.value;
  }

  // menu items
  const menuItems = [
      { 
        name: 'Dashboard', 
        icon: 'fas fa-tachometer-alt',
        pathname: '/',
        permission: {name: 'Dashboard', action: "View"}
      },
      { 
        name: 'Sales', 
        icon: 'fas fa-chart-line',
        pathname: '/sales',
        permission: {name: 'Sales', action: 'View'}
      },
      { 
        name: 'Products', 
        icon: 'fas fa-box-open',
        pathname: "",
        permission: {name: 'Product', action: "View"}
      },
      { 
        name: 'Settings', 
        icon: 'fas fa-cogs',
        pathname: "",
        permission: {name: 'Setting', action: "View"}
      },
      { 
        name: 'User Role', 
        icon: 'fas fa-users',
        children: [
          { 
            name: 'User', 
            icon: 'fas fa-user-tie',
            pathname: '/user',
            permission: {name: 'User', action: "View"}
          },
          { 
            name: 'Role & Permission', 
            icon: 'fas fa-sitemap',
            pathname: "/role",
            permission: {name: 'Role', action: "View"}
          },
          { 
            name: 'Branch', 
            icon: 'fas fa-warehouse',
            pathname: '/branch',
            permission: { name: 'Branch', action: 'View' }
          },
          { 
            name: 'Counter', 
            icon: 'fas fa-computer',
            pathname: "/counter",
            permission: {name: 'Counter', action: "View"}
          }
        ],
        pathname: "",
        permission: {name: 'Role', action: "View"}
      },
      { 
        name: 'Receipt', 
        icon: 'fas fa-receipt',
        pathname: "/receipt",
        permission: {name: 'Receipt', action: "View"}
      }
  ];

  function toggleDropdown(itemName) {
    openDropdown.value = openDropdown.value === itemName ? null : itemName;
  }

  function changeRoute(name) {
    if (!name) return;
    router.push(name);
  }

  function canAccess(item) {
    if (!item.permissions) return false
    return usePermission.can(item.permission.name, item.permission.action);
  }

</script>

<template>
    <!-- Sidebar -->
    <div :class="[
        'sidebar-bg text-white transition-all duration-300 text-sm font-semibold pt-2 h-screen',
        collapseSidebar.isSidebarCollapsed ? 'w-16' : 'w-64',
        'md:group-hover:w-64',
      ]"
      class="relative group">

      <div 
        :class="collapseSidebar.isSidebarCollapsed? 'justify-center items-center': 'px-2 gap-2'" 
        class="flex items-center transition-all"
        @click="collapseSidebar.toggleSidebar"
      >
        <img src="../assets/images/logo.png" :class="collapseSidebar.isSidebarCollapsed? 'w-10 h-10' : 'w-10 h-10'" alt="Fusion Mart" />
        <div class="flex flex-col">
          <span v-show="!collapseSidebar.isSidebarCollapsed" class="text-white text-lg font-semibold transition-all duration-300 origin-left">Fusion Mart</span>
          <span v-show="!collapseSidebar.isSidebarCollapsed" class="text-gray-300 text-[11px] transition-all duration-300 origin-left">POS System</span>
        </div>
      </div>

      <!-- Menu -->
      <nav class="mt-4 flex flex-col gap-2">
        <template
          v-for="item in menuItems"
          :key="item.name"
        >
          <div
            class="items-center hover:bg-[#F8FAFC] hover:text-black transition-all cursor-pointer relative"
            v-if="usePermission.can(item.permission.name, item.permission.action)"
          >
            <div 
              class="flex justify-between items-center py-3 px-4 hover:bg-[#F8FAFC] hover:text-black transition-all cursor-pointer"
              @click="item.children ? toggleDropdown(item.name) : changeRoute(item.pathname)"
            >
              <div class="flex items-center gap-4">
                <i :class="item.icon" class="text-lg"></i>
                <span
                  v-show="!collapseSidebar.isSidebarCollapsed"
                  class="transition-all duration-300 origin-left"
                >
                  {{ item.name }}
                </span>
              </div>
              <!-- Chevron Icon for Dropdown -->
              <i
                v-if="item.children"
                class="fas fa-chevron-right transition-transform text-sm ml-2"
                :class="{ 'rotate-180': openDropdown === item.name }"
              ></i>
              <!-- If sidebar is collapsed, show floating dropdown -->
            </div>
            <div
              v-if="item.children && openDropdown === item.name"
              :class="[
                collapseSidebar.isSidebarCollapsed 
                  ? 'absolute left-full top-0 ml-2 bg-[#fff] text-black rounded shadow-lg z-50 w-48'
                  : 'bg-[#fff] text-black'
              ]"
            >
              <template
                v-for="sub in item.children"
                :key="sub.name"
              >
                <div
                  class="flex pl-6 items-center py-2 gap-3 hover:bg-[#F8FAFC] hover:text-black cursor-pointer transition-all"
                  @click="changeRoute(sub.pathname)"
                  v-if="usePermission.can(sub.permission.name, sub.permission.action)"
                >
                  <i :class="sub.icon" class="text-lg"></i>
                  <span class="whitespace-nowrap">
                    {{ sub.name }}
                  </span>
                </div>
              </template>
            </div>
          </div>
        </template>
      </nav>
    </div>
</template>

<style>
  .sidebar-bg {
    background-color: #206DBA;
  }
</style>