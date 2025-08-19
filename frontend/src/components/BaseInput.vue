<script setup>
import { computed } from "vue";

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: "",
  },
  label: {
    type: String,
    default: "",
  },
  placeholder: {
    type: String,
    default: "",
  },
  size: {
    type: String,
    default: "md", // sm, md, lg
  },
  type: {
    type: String,
    default: "text",
  },
  error: {
    type: String,
    default: "",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  readonly: {
    type: Boolean,
    default: false,
  },
  width: {
    type: String,
    default: "100%", // Can be '100%', 'auto', '300px', 'w-64', etc.
  },
  height: {
    type: String,
    default: "", // Can be '40px', 'h-12', etc.
  },
});

const emit = defineEmits(["update:modelValue"]);

const sizeClasses = computed(() => {
  switch (props.size) {
    case "sm":
      return "px-2 py-1 text-sm";
    case "lg":
      return "px-4 py-3 text-lg";
    default:
      return "px-3 py-2 text-base"; // md
  }
});

const borderClasses = computed(() => {
  if (props.error) {
    return "border-red-500";
  }
  return "border-gray-500";
});

const styleWidth = computed(() => {
  return props.width.includes("w-") ? "" : props.width;
});

const styleHeight = computed(() => {
  return props.height.includes("h-") ? "" : props.height;
});
</script>

<template>
  <div class="flex flex-col gap-1" :style="{ width: styleWidth }" :class="width.includes('w-') ? width : ''">

    <!-- Label -->
    <label v-if="label" class="text-sm font-medium text-black">
      {{ label }}
    </label>

    <!-- Input -->
    <input
      :type="type"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      :readonly="readonly"
      :style="{ height: styleHeight }"
      :class="`
        border rounded outline-none transition placeholder:text-[13px]
        ${sizeClasses} ${borderClasses}
        ${disabled ? 'bg-gray-100 cursor-not-allowed' : ''}
        ${height.includes('h-') ? height : ''}
      `"
      @input="$emit('update:modelValue', $event.target.value)"
    />

    <!-- Error Message -->
    <p v-if="error" class="text-sm text-red-500">
      {{ error }}
    </p>

  </div>
</template>
