<script setup>
import { ref } from 'vue';
import { postJson } from '../lib/api';

const props = defineProps({
    shared: { type: Object, required: true },
});

const loggingOut = ref(false);

async function logout() {
    if (loggingOut.value) {
        return;
    }

    loggingOut.value = true;

    try {
        const { response, payload } = await postJson(props.shared.api.logout, {}, props.shared.csrfToken);

        window.location.assign(response.ok && payload.redirect ? payload.redirect : props.shared.routes.home);
    } catch {
        loggingOut.value = false;
    }
}
</script>

<template>
    <header
        class="fixed inset-x-0 top-0 z-50 border-b border-[#e3e3e0] bg-white/90 backdrop-blur dark:border-[#3E3E3A] dark:bg-[#0a0a0a]/90"
    >
        <nav class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between gap-4 px-6">
            <a :href="shared.routes.home" class="text-sm font-medium">
                {{ shared.appName }}
            </a>

            <div v-if="shared.user" class="flex items-center gap-3 text-sm">
                <a class="hidden underline-offset-4 hover:underline sm:inline" :href="shared.routes.dashboard">
                    Dashboard
                </a>

                <a
                    v-if="shared.user.isSuperAdmin"
                    class="hidden underline-offset-4 hover:underline sm:inline"
                    :href="shared.routes.users"
                >
                    Users
                </a>

                <span class="text-[#706f6c] dark:text-[#A1A09A]">
                    {{ shared.user.name }}
                </span>

                <button
                    class="rounded-sm border border-[#19140035] px-4 py-1.5 leading-normal transition hover:border-[#1915014a] disabled:cursor-not-allowed disabled:opacity-60 dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                    type="button"
                    :disabled="loggingOut"
                    @click="logout"
                >
                    {{ loggingOut ? 'Logging out…' : 'Log out' }}
                </button>
            </div>

            <div v-else class="flex items-center gap-3 text-sm">
                <a
                    class="rounded-sm border border-transparent px-4 py-1.5 leading-normal transition hover:border-[#19140035] dark:hover:border-[#3E3E3A]"
                    :href="shared.routes.login"
                >
                    Log in
                </a>

                <a
                    class="rounded-sm bg-[#1b1b18] px-4 py-1.5 font-medium leading-normal text-white transition hover:bg-black dark:bg-[#EDEDEC] dark:text-[#1C1C1A] dark:hover:bg-white"
                    :href="shared.routes.register"
                >
                    Register
                </a>
            </div>
        </nav>
    </header>
</template>

