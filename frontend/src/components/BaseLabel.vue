<script setup>
    defineProps({
        label: {type: String, default: ''}
    })
</script>

<template>
    <span class="text-sm text-black">
        {{ label }}
    </span>
</template>