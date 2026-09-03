import { reactive, ref } from 'vue';

/**
 * POST JSON to an API route as the current session.
 *
 * Sanctum's stateful middleware authenticates same-origin requests with the
 * session cookie, so the CSRF token has to travel with every write.
 */
export async function postJson(url, body, csrfToken) {
    const response = await fetch(url, {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(body),
    });

    const payload = await response.json().catch(() => ({}));

    return { response, payload };
}

/**
 * GET JSON from an API route as the current session.
 */
export async function getJson(url, params = {}) {
    const target = new URL(url, window.location.origin);

    Object.entries(params).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
            target.searchParams.set(key, value);
        }
    });

    const response = await fetch(target, {
        method: 'GET',
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    });

    const payload = await response.json().catch(() => ({}));

    return { response, payload };
}

/**
 * Track the state of a single API-backed form: values, validation errors,
 * an in-flight flag, and the status message returned on success.
 */
export function useForm(initial) {
    const data = reactive({ ...initial });
    const errors = ref({});
    const processing = ref(false);
    const status = ref('');

    const errorFor = (field) => errors.value[field]?.[0] ?? '';

    async function submit(url, csrfToken) {
        if (processing.value) {
            return null;
        }

        processing.value = true;
        errors.value = {};
        status.value = '';

        try {
            const { response, payload } = await postJson(url, { ...data }, csrfToken);

            if (response.status === 422) {
                errors.value = payload.errors ?? {};

                return null;
            }

            if (! response.ok) {
                errors.value = { form: [payload.message ?? 'Something went wrong. Please try again.'] };

                return null;
            }

            if (payload.redirect) {
                window.location.assign(payload.redirect);

                return payload;
            }

            status.value = payload.status ?? '';

            return payload;
        } catch {
            errors.value = { form: ['Could not reach the server. Please try again.'] };

            return null;
        } finally {
            processing.value = false;
        }
    }

    // `reactive` unwraps the refs, so templates read `form.processing`, not `.value`.
    return reactive({ data, errors, errorFor, processing, status, submit });
}
