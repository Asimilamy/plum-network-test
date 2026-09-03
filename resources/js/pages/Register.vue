<script setup>
import { ref } from 'vue';
import AuthLayout from '../layouts/Auth/main.vue';
import FormField from '../components/FormField.vue';
import SubmitButton from '../components/SubmitButton.vue';

const props = defineProps({
    shared: { type: Object, required: true },
});

const name = ref(props.shared.old?.name ?? '');
const email = ref(props.shared.old?.email ?? '');
const password = ref('');
const passwordConfirmation = ref('');

const errorFor = (field) => props.shared.errors[field]?.[0] ?? '';
</script>

<template>
    <AuthLayout title="Create an account" description="Register to get started">
        <form class="flex flex-col gap-4" :action="shared.routes.register" method="POST">
            <input type="hidden" name="_token" :value="shared.csrfToken">

            <FormField
                id="name"
                v-model="name"
                label="Name"
                autocomplete="name"
                required
                autofocus
                :error="errorFor('name')"
            />

            <FormField
                id="email"
                v-model="email"
                label="Email"
                type="email"
                autocomplete="email"
                required
                :error="errorFor('email')"
            />

            <FormField
                id="password"
                v-model="password"
                label="Password"
                type="password"
                autocomplete="new-password"
                required
                :error="errorFor('password')"
            />

            <FormField
                id="password_confirmation"
                v-model="passwordConfirmation"
                label="Confirm password"
                type="password"
                autocomplete="new-password"
                required
                :error="errorFor('password_confirmation')"
            />

            <SubmitButton>Register</SubmitButton>
        </form>

        <template #footer>
            Already registered?
            <a class="underline underline-offset-4" :href="shared.routes.login">Log in</a>
        </template>
    </AuthLayout>
</template>
