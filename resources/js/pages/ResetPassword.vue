<script setup>
import AuthLayout from '../layouts/Auth/main.vue';
import FormAlert from '../components/FormAlert.vue';
import FormField from '../components/FormField.vue';
import SubmitButton from '../components/SubmitButton.vue';
import { useForm } from '../lib/api';

const props = defineProps({
    shared: { type: Object, required: true },
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => form.submit(props.shared.api.passwordUpdate, props.shared.csrfToken);
</script>

<template>
    <AuthLayout :shared="shared" title="Reset password" description="Choose a new password for your account">
        <FormAlert :message="form.errorFor('form')" />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <FormField
                id="email"
                v-model="form.data.email"
                label="Email"
                type="email"
                autocomplete="email"
                required
                :error="form.errorFor('email')"
            />

            <FormField
                id="password"
                v-model="form.data.password"
                label="New password"
                type="password"
                autocomplete="new-password"
                required
                autofocus
                :error="form.errorFor('password')"
            />

            <FormField
                id="password_confirmation"
                v-model="form.data.password_confirmation"
                label="Confirm new password"
                type="password"
                autocomplete="new-password"
                required
                :error="form.errorFor('password_confirmation')"
            />

            <SubmitButton :processing="form.processing">
                {{ form.processing ? 'Resetting…' : 'Reset password' }}
            </SubmitButton>
        </form>

        <template #footer>
            <a class="underline underline-offset-4" :href="shared.routes.login">Back to log in</a>
        </template>
    </AuthLayout>
</template>
