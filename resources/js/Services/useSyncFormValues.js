import { watch } from 'vue';

export function useSyncFormValues(values, fields, form) {
    watch(values, newValues => {
        Object.keys(fields).forEach(key => {
            form[key] = newValues[key] ?? '';
        });
    });
}
