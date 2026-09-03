<script setup>
import DashboardLayout from '../layouts/Dashboard/main.vue';

defineProps({
    shared: { type: Object, required: true },
});
</script>

<template>
    <DashboardLayout :shared="shared" title="Dashboard">
        <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
            You're logged in as {{ shared.user?.email }}.
        </p>
    </DashboardLayout>
</template>
