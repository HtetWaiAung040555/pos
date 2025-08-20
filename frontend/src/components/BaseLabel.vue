<script setup>
    defineProps({
        label: {type: String, default: ''}
    })
</script>

<template>
    <span class="text-sm font-medium text-black">
        {{ label }}
    </span>
</template>