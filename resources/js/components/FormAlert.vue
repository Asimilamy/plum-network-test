<script setup>
defineProps({
    message: { type: String, default: '' },
    variant: { type: String, default: 'error' },
});
</script>

<template>
    <p
        v-if="message"
        class="mb-4 text-sm"
        :class="variant === 'success'
            ? 'text-green-600 dark:text-green-400'
            : 'text-red-600 dark:text-red-400'"
        role="alert"
    >
        {{ message }}
    </p>
</template>
