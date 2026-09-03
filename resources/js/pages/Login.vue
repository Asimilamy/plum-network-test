<script setup>
import { ref } from 'vue';
import AuthLayout from '../layouts/Auth/main.vue';
import FormField from '../components/FormField.vue';
import SubmitButton from '../components/SubmitButton.vue';

const props = defineProps({
    shared: { type: Object, required: true },
});

const email = ref(props.shared.old?.email ?? '');
const password = ref('');

const errorFor = (field) => props.shared.errors[field]?.[0] ?? '';
</script>

<template>
    <AuthLayout title="Log in" description="Enter your credentials to continue">
        <p v-if="shared.status" class="mb-4 text-sm text-green-600 dark:text-green-400">
            {{ shared.status }}
        </p>

        <form class="flex flex-col gap-4" :action="shared.routes.login" method="POST">
            <input type="hidden" name="_token" :value="shared.csrfToken">

            <FormField
                id="email"
                v-model="email"
                label="Email"
                type="email"
                autocomplete="email"
                required
                autofocus
                :error="errorFor('email')"
            />

            <FormField
                id="password"
                v-model="password"
                label="Password"
                type="password"
                autocomplete="current-password"
                required
                :error="errorFor('password')"
            />

            <label class="flex items-center gap-2 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                <input name="remember" type="checkbox" value="1" class="rounded-sm border-[#19140035] dark:border-[#3E3E3A]">
                Remember me
            </label>

            <SubmitButton>Log in</SubmitButton>
        </form>

        <template #footer>
            <a class="underline underline-offset-4" :href="shared.routes.passwordRequest">Forgot your password?</a>
            <span class="mx-2">·</span>
            <a class="underline underline-offset-4" :href="shared.routes.register">Create an account</a>
        </template>
    </AuthLayout>
</template>
