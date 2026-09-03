<script setup>
defineProps({
    shared: { type: Object, required: true },
    title: { type: String, default: '' },
});
</script>

<template>
    <div class="flex min-h-screen flex-col bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC]">
        <header class="border-b border-[#e3e3e0] dark:border-[#3E3E3A]">
            <nav class="mx-auto flex w-full max-w-6xl items-center justify-between gap-4 px-6 py-4">
                <a :href="shared.routes.dashboard" class="text-sm font-medium">Dashboard</a>

                <div class="flex items-center gap-4">
                    <span v-if="shared.user" class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                        {{ shared.user.name }}
                    </span>

                    <form :action="shared.routes.logout" method="POST">
                        <input type="hidden" name="_token" :value="shared.csrfToken">
                        <button
                            class="rounded-sm border border-[#19140035] px-4 py-1.5 text-sm leading-normal transition hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                            type="submit"
                        >
                            Log out
                        </button>
                    </form>
                </div>
            </nav>
        </header>

        <main class="mx-auto flex w-full max-w-6xl flex-1 flex-col gap-6 px-6 py-8">
            <h1 v-if="title" class="text-xl font-medium">{{ title }}</h1>
            <slot />
        </main>
    </div>
</template>
