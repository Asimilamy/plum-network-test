<script setup>
import { ref } from 'vue';
import AuthLayout from '../layouts/Auth/main.vue';
import FormField from '../components/FormField.vue';
import SubmitButton from '../components/SubmitButton.vue';

const props = defineProps({
    shared: { type: Object, required: true },
});

const email = ref(props.shared.old?.email ?? '');

const errorFor = (field) => props.shared.errors[field]?.[0] ?? '';
</script>

<template>
    <AuthLayout
        title="Forgot password"
        description="Enter your email and we'll send you a password reset link"
    >
        <p v-if="shared.status" class="mb-4 text-sm text-green-600 dark:text-green-400">
            {{ shared.status }}
        </p>

        <form class="flex flex-col gap-4" :action="shared.routes.passwordEmail" method="POST">
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

            <SubmitButton>Email password reset link</SubmitButton>
        </form>

        <template #footer>
            <a class="underline underline-offset-4" :href="shared.routes.login">Back to log in</a>
        </template>
    </AuthLayout>
</template>
