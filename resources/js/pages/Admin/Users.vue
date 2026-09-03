<script setup>
import { onMounted, ref } from 'vue';
import DashboardLayout from '../../layouts/Dashboard/main.vue';
import FormAlert from '../../components/FormAlert.vue';
import FormField from '../../components/FormField.vue';
import SubmitButton from '../../components/SubmitButton.vue';
import { getJson, useForm } from '../../lib/api';

const props = defineProps({
    shared: { type: Object, required: true },
    roles: { type: Array, required: true },
    perPage: { type: Number, required: true },
});

const users = ref([]);
const meta = ref({ current_page: 1, last_page: 1, total: 0 });
const loading = ref(false);
const listError = ref('');
const created = ref('');

async function fetchUsers(page = 1) {
    loading.value = true;
    listError.value = '';

    try {
        const { response, payload } = await getJson(props.shared.api.usersIndex, {
            page,
            per_page: props.perPage,
        });

        if (! response.ok) {
            listError.value = payload.message ?? 'Could not load users.';

            return;
        }

        users.value = payload.data ?? [];
        meta.value = payload.meta ?? meta.value;
    } catch {
        listError.value = 'Could not reach the server. Please try again.';
    } finally {
        loading.value = false;
    }
}

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: props.roles[0]?.value ?? '',
});

async function submit() {
    created.value = '';

    const payload = await form.submit(props.shared.api.usersStore, props.shared.csrfToken);

    if (! payload?.user) {
        return;
    }

    created.value = `${payload.user.name} was added as a ${payload.user.roleLabel.toLowerCase()}.`;

    form.data.name = '';
    form.data.email = '';
    form.data.password = '';
    form.data.password_confirmation = '';
    form.data.role = props.roles[0]?.value ?? '';

    // The newest user sorts first, so jump back to the start of the list.
    await fetchUsers(1);
}

function goToPage(page) {
    if (loading.value || page < 1 || page > meta.value.last_page) {
        return;
    }

    fetchUsers(page);
}

onMounted(() => fetchUsers(1));
</script>

<template>
    <DashboardLayout :shared="shared" title="User management">
        <div class="grid gap-8 lg:grid-cols-[20rem_1fr]">
            <section class="flex flex-col gap-4 rounded-lg border border-[#e3e3e0] bg-white p-6 dark:border-[#3E3E3A] dark:bg-[#161615]">
                <h2 class="text-sm font-medium">Create a user</h2>

                <FormAlert :message="created" variant="success" />
                <FormAlert :message="form.errorFor('form')" />

                <form class="flex flex-col gap-4" @submit.prevent="submit">
                    <FormField
                        id="name"
                        v-model="form.data.name"
                        label="Name"
                        autocomplete="off"
                        required
                        :error="form.errorFor('name')"
                    />

                    <FormField
                        id="email"
                        v-model="form.data.email"
                        label="Email"
                        type="email"
                        autocomplete="off"
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

                    <div class="flex flex-col gap-1.5">
                        <label for="role" class="text-sm font-medium">Role</label>

                        <select
                            id="role"
                            v-model="form.data.role"
                            class="rounded-sm border border-[#19140035] bg-white px-3 py-2 text-sm outline-none transition focus:border-[#1b1b18] dark:border-[#3E3E3A] dark:bg-[#0a0a0a] dark:focus:border-[#EDEDEC]"
                            :class="form.errorFor('role') && 'border-red-500 dark:border-red-500'"
                        >
                            <option v-for="role in roles" :key="role.value" :value="role.value">
                                {{ role.label }}
                            </option>
                        </select>

                        <p v-if="form.errorFor('role')" class="text-sm text-red-600 dark:text-red-400">
                            {{ form.errorFor('role') }}
                        </p>
                    </div>

                    <SubmitButton :processing="form.processing">
                        {{ form.processing ? 'Creating…' : 'Create user' }}
                    </SubmitButton>
                </form>
            </section>

            <section class="flex flex-col gap-4">
                <h2 class="text-sm font-medium">
                    All users
                    <span class="text-[#706f6c] dark:text-[#A1A09A]">({{ meta.total }})</span>
                </h2>

                <FormAlert :message="listError" />

                <div class="overflow-x-auto rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    <table class="w-full min-w-lg border-collapse text-left text-sm">
                        <thead class="border-b border-[#e3e3e0] text-[#706f6c] dark:border-[#3E3E3A] dark:text-[#A1A09A]">
                            <tr>
                                <th class="px-4 py-3 font-medium">Name</th>
                                <th class="px-4 py-3 font-medium">Email</th>
                                <th class="px-4 py-3 font-medium">Role</th>
                                <th class="px-4 py-3 font-medium">Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading || users.length === 0">
                                <td class="px-4 py-6 text-center text-[#706f6c] dark:text-[#A1A09A]" colspan="4">
                                    {{ loading ? 'Loading users…' : 'No users to show.' }}
                                </td>
                            </tr>

                            <template v-else>
                                <tr
                                    v-for="user in users"
                                    :key="user.id"
                                    class="border-b border-[#e3e3e0] last:border-b-0 dark:border-[#3E3E3A]"
                                >
                                    <td class="px-4 py-3">{{ user.name }}</td>
                                    <td class="px-4 py-3 text-[#706f6c] dark:text-[#A1A09A]">{{ user.email }}</td>
                                    <td class="px-4 py-3">{{ user.roleLabel }}</td>
                                    <td class="px-4 py-3 text-[#706f6c] dark:text-[#A1A09A]">{{ user.createdAt }}</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between gap-4 text-sm">
                    <span class="text-[#706f6c] dark:text-[#A1A09A]">
                        Page {{ meta.current_page }} of {{ meta.last_page }}
                    </span>

                    <div class="flex items-center gap-2">
                        <button
                            class="rounded-sm border border-[#19140035] px-4 py-1.5 leading-normal transition hover:border-[#1915014a] disabled:cursor-not-allowed disabled:opacity-40 dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                            type="button"
                            :disabled="loading || meta.current_page <= 1"
                            @click="goToPage(meta.current_page - 1)"
                        >
                            Previous
                        </button>

                        <button
                            class="rounded-sm border border-[#19140035] px-4 py-1.5 leading-normal transition hover:border-[#1915014a] disabled:cursor-not-allowed disabled:opacity-40 dark:border-[#3E3E3A] dark:hover:border-[#62605b]"
                            type="button"
                            :disabled="loading || meta.current_page >= meta.last_page"
                            @click="goToPage(meta.current_page + 1)"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </section>
        </div>
    </DashboardLayout>
</template>
