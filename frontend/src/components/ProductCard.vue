<script setup>
const props = defineProps({
  name: { type: String, default: "" },
  price: { type: String, default: "0" },
  imageUrl: { type: String, default: "" },
});

console.log(props.imageUrl);
</script>


<template>
  <div class="flex flex-col gap-y-2 p-2 rounded-md shadow-md bg-white">
    <!-- 1:1 image ratio -->
    <div class="w-full aspect-square overflow-hidden rounded-md">
      <img
        :src="props.imageUrl"
        alt="Product"
        class="w-full h-full object-cover"
      />
    </div>

    <!-- Name: max 2 lines with ellipsis -->
    <span
      class="mt-2 text-[12px] font-medium text-black leading-tight line-clamp-2"
    >
      {{ props.name }}
    </span>

    <!-- Price -->
    <span class="text-base font-semibold text-gray-700">
      {{ props.price }}
    </span>
  </div>
</template>
