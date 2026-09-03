<script setup>
import AppHeader from '../../components/AppHeader.vue';

defineProps({
    shared: { type: Object, required: true },
});
</script>

<template>
    <div class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <AppHeader :shared="shared" />

        <main class="mx-auto w-full max-w-6xl px-6 pt-16">
            <slot />
        </main>
    </div>
</template>
