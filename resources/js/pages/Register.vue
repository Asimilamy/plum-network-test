<script setup>
import AuthLayout from '../layouts/Auth/main.vue';
import FormAlert from '../components/FormAlert.vue';
import FormField from '../components/FormField.vue';
import SubmitButton from '../components/SubmitButton.vue';
import { useForm } from '../lib/api';

const props = defineProps({
    shared: { type: Object, required: true },
});

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => form.submit(props.shared.api.register, props.shared.csrfToken);
</script>

<template>
    <AuthLayout :shared="shared" title="Create an account" description="Register to get started">
        <FormAlert :message="form.errorFor('form')" />

        <form class="flex flex-col gap-4" @submit.prevent="submit">
            <FormField
                id="name"
                v-model="form.data.name"
                label="Name"
                autocomplete="name"
                required
                autofocus
                :error="form.errorFor('name')"
            />

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
                label="Password"
                type="password"
                autocomplete="new-password"
                required
                :error="form.errorFor('password')"
            />

            <FormField
                id="password_confirmation"
                v-model="form.data.password_confirmation"
                label="Confirm password"
                type="password"
                autocomplete="new-password"
                required
                :error="form.errorFor('password_confirmation')"
            />

            <SubmitButton :processing="form.processing">
                {{ form.processing ? 'Creating account…' : 'Register' }}
            </SubmitButton>
        </form>

        <template #footer>
            Already registered?
            <a class="underline underline-offset-4" :href="shared.routes.login">Log in</a>
        </template>
    </AuthLayout>
</template>
