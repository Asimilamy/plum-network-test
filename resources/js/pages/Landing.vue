<script setup>
import PublicLayout from '../layouts/Public/main.vue';

defineProps({
    shared: { type: Object, required: true },
});
</script>

<template>
    <PublicLayout :shared="shared" />
</template>
