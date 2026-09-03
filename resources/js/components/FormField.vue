<script setup>
defineProps({
    id: { type: String, required: true },
    label: { type: String, required: true },
    type: { type: String, default: 'text' },
    autocomplete: { type: String, default: 'off' },
    required: { type: Boolean, default: false },
    autofocus: { type: Boolean, default: false },
    modelValue: { type: String, default: '' },
    error: { type: String, default: '' },
});

defineEmits(['update:modelValue']);
</script>

<template>
    <div class="flex flex-col gap-1.5">
        <label :for="id" class="text-sm font-medium">{{ label }}</label>

        <input
            :id="id"
            :name="id"
            :type="type"
            :value="modelValue"
            :autocomplete="autocomplete"
            :required="required"
            :autofocus="autofocus"
            :aria-invalid="error ? 'true' : undefined"
            :aria-describedby="error ? `${id}-error` : undefined"
            class="rounded-sm border border-[#19140035] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC]"
            :class="error && 'border-red-500 dark:border-red-500'"
            @input="$emit('update:modelValue', $event.target.value)"
        >

        <p v-if="error" :id="`${id}-error`" class="text-sm text-red-600 dark:text-red-400">
            {{ error }}
        </p>
    </div>
</template>
