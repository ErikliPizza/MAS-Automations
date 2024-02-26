<script>
import { ref, computed } from 'vue';
import {usePage} from "@inertiajs/vue3";

// Create a composable function
export function useCanIClick() {
    const page = usePage();

    const canIClick = ref(() => {
        return (userRole) => {
            const authRole = page.props.auth.user.role;

            // Check conditions
            if (authRole === 'additional') {
                return false;
            } else if (authRole === 'root' && (userRole === 'additional' || userRole === 'admin')) {
                return true;
            } else if (authRole === 'admin' && userRole === 'additional') {
                return true;
            }

            return false; // Default case
        };
    });

    return computed(() => canIClick.value());
}
</script>
