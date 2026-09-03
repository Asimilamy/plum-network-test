<script setup>
import AuthLayout from '../layouts/Auth/main.vue';
import FormAlert from '../components/FormAlert.vue';
import FormField from '../components/FormField.vue';
import SubmitButton from '../components/SubmitButton.vue';
import { useForm } from '../lib/api';

const props = defineProps({
    shared: { type: Object, required: true },
});

const form = useForm({ email: '' });

const submit = () => form.submit(props.shared.api.passwordEmail, props.shared.csrfToken);
</script>

<template>
    <AuthLayout
        :shared="shared"
        title="Forgot password"
        description="Enter your email and we'll send you a password reset link"
    >
        <FormAlert :message="form.status" variant="success" />
        <FormAlert :message="form.errorFor('form')" />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <FormField
                id="email"
                v-model="form.data.email"
                label="Email"
                type="email"
                autocomplete="email"
                required
                autofocus
                :error="form.errorFor('email')"
            />

            <SubmitButton :processing="form.processing">
                {{ form.processing ? 'Sending…' : 'Email password reset link' }}
            </SubmitButton>
        </form>

        <template #footer>
            <a class="underline underline-offset-4" :href="shared.routes.login">Back to log in</a>
        </template>
    </AuthLayout>
</template>
