<script setup>
  import { useRoute } from 'vue-router';
  import Sidebar from './components/Sidebar.vue';
  import Navbar from './components/Navbar.vue';


  const route = useRoute();
  

</script>

<template>
  <div class="flex h-screen text-sm">
    <Sidebar v-if="route.path !== '/login'" />
    <!-- Main content -->
    <div class="w-full p-0 main-bg">
      <Navbar v-if="route.path !== '/login'" />
      <router-view></router-view>
    </div>
  </div>
  <!-- <router-link to="/">Home</router-link>
  <router-link to="/sales">Sales</router-link> -->
  
</template>

<style>
  .main.bg {
    background-color: #F8FAFC;
  }
</style>
