import { useToast } from 'vue-toastification';
import { watch, nextTick } from 'vue';
import { usePage } from "@inertiajs/vue3";

// Create a toast instance for displaying messages
const toast = useToast();

// Get the current page instance
const page = usePage();

// Custom service function to handle flash messages
export function useFlashMessages() {
    // Watch for changes in flash messages and trigger appropriate toasts, go to the middleware directory of laravel and check the inertia handler for more info
    watch(() => ({
        success: page.props.flash.success,
        info: page.props.flash.info,
        error: page.props.flash.error,
    }), (newValues) => {

        nextTick(() => {
            // Display success message as a toast
            if (newValues.success) {
                page.props.flash.success = null;
                toast.clear();
                toast.success(newValues.success);
            }
            // Display info message as a toast
            if (newValues.info) {
                page.props.flash.info = null;
                toast.clear();
                toast.info(newValues.info);
            }
            // Display error message as a toast
            if (newValues.error) {
                page.props.flash.error = null;
                toast.clear();
                toast.error(newValues.error);
            }
        });
    }, { deep: true, immediate: true }); // Watch deeply to capture changes in nested properties
}
