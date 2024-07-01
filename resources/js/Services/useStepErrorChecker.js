import {computed, ref, watch} from 'vue';

export function useStepErrorChecker(fields, form, steps) {
    const stepErrors = ref({});
    function checkErrors() {
        stepErrors.value = {};
        steps.forEach(step => {
            stepErrors.value[step.name] = step.fields.some(field => {
                const fieldInstance = fields[field];
                return fieldInstance.errorMessage.value || form.errors[field]?.length;
            });
        });
    }

    // Watcher for field errors
    const fieldErrorMessages = computed(() => {
        return Object.keys(fields).reduce((acc, key) => {
            acc[key] = fields[key].errorMessage.value;
            return acc;
        }, {});
    });

    // Watch form.errors and fieldErrorMessages to trigger checkErrors
    watch(() => [form.errors, fieldErrorMessages], () => {
        checkErrors();
    }, { deep: true });

    return {
        stepErrors
    };
}


/*
import { ref, watch } from 'vue';

export function useStepErrorChecker(fields, form, stepFields) {
    const stepErrors = ref({
        stepOne: false,
        stepTwo: false,
        stepThree: false,
    });

    function checkErrors() {
        setTimeout(() => {
            stepErrors.value.stepOne = stepFields.stepOne.some(field => {
                const fieldInstance = fields[field];
                return fieldInstance.errorMessage.value || form.errors[field]?.length;
            });

            stepErrors.value.stepTwo = stepFields.stepTwo.some(field => {
                const fieldInstance = fields[field];
                return fieldInstance.errorMessage.value || form.errors[field]?.length;
            });

            stepErrors.value.stepThree = stepFields.stepThree.some(field => {
                const fieldInstance = fields[field];
                return fieldInstance.errorMessage.value || form.errors[field]?.length;
            });
        }, 200);
    }

    return {
        stepErrors,
        checkErrors
    };
}
* */
