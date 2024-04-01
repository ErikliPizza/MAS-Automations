import {router} from "@inertiajs/vue3";

export function meHavePosts() {

    window.addEventListener('popstate', (event) => {
        event.stopImmediatePropagation();
        router.reload({
            preserveState: false,
            preserveScroll: false,
            replace: true,
        });
    });
}
