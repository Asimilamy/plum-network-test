<script setup>
defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
});
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-[#FDFDFC] p-6 text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <div class="flex w-full max-w-sm flex-col gap-6">
            <header class="flex flex-col gap-2 text-center">
                <h1 class="text-xl font-medium">{{ title }}</h1>
                <p v-if="description" class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                    {{ description }}
                </p>
            </header>

            <main class="rounded-lg border border-[#e3e3e0] bg-white p-6 shadow-[0px_0px_1px_0px_rgba(0,0,0,0.03),0px_1px_2px_0px_rgba(0,0,0,0.06)] dark:border-[#3E3E3A] dark:bg-[#161615] dark:shadow-none">
                <slot />
            </main>

            <footer v-if="$slots.footer" class="text-center text-sm text-[#706f6c] dark:text-[#A1A09A]">
                <slot name="footer" />
            </footer>
        </div>
    </div>
</template>
