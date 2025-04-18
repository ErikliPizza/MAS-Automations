export function useSyncFormErrors(form, fields) {
    const getErrorMessage = (fieldName) => {
        return form.errors[fieldName] || fields[fieldName]?.errorMessage.value || '';
    };

    return { getErrorMessage };
}
