<script setup>
defineProps({
    shared: { type: Object, required: true },
});
</script>

<template>
    <div class="flex min-h-screen flex-col items-center justify-center gap-6 bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <h1 class="text-2xl font-medium">Vue is running in Laravel</h1>

        <nav class="flex items-center gap-4 text-sm">
            <a
                v-if="shared.user"
                class="rounded-sm border border-[#19140035] px-5 py-1.5 leading-normal transition hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                :href="shared.routes.dashboard"
            >
                Dashboard
            </a>

            <template v-else>
                <a
                    class="rounded-sm border border-[#19140035] px-5 py-1.5 leading-normal transition hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                    :href="shared.routes.login"
                >
                    Log in
                </a>
                <a
                    class="rounded-sm border border-transparent px-5 py-1.5 leading-normal transition hover:border-[#19140035] dark:hover:border-[#3E3E3A]"
                    :href="shared.routes.register"
                >
                    Register
                </a>
            </template>
        </nav>
    </div>
</template>
