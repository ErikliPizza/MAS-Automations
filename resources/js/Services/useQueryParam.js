import { ref } from 'vue';
import { usePage } from "@inertiajs/vue3";

export function useQueryParam(paramName) {
    const paramValue = ref(false);

    const page = usePage();
    const currentUrl = new URL(page.url, window.location.origin);
    paramValue.value = currentUrl.searchParams.get(paramName) || false;

    return paramValue.value;
}
