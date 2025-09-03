<script setup>
    import Button from 'primevue/button';
    let loginUser = JSON.parse(localStorage.getItem('user'));
</script>

<template>
    <div class="p-6">
        <p class="text-black">Home Page</p>
        <Button label="Submit" severity="success" raised rounded variant="outlined" icon="pi pi-user" />
    </div>
</template>