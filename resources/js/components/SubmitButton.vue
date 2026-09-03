<script setup>
defineProps({
    processing: { type: Boolean, default: false },
});
</script>

<template>
    <button
        class="rounded-sm bg-[#1b1b18] px-4 py-2 text-sm font-medium text-white transition hover:bg-black disabled:cursor-not-allowed disabled:opacity-60 dark:bg-[#EDEDEC] dark:text-[#1C1C1A] dark:hover:bg-white"
        type="submit"
        :disabled="processing"
    >
        <slot />
    </button>
</template>
