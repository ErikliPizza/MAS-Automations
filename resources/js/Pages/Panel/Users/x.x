<script setup>
import { useForm as useInertiaForm } from '@inertiajs/vue3';
import { useField, useForm as useVeeForm } from 'vee-validate';
import * as yup from 'yup';
import {computed, nextTick, ref, watch} from "vue";

const props = defineProps({
    initialModules: {
        required: true
    },
    userLimitCount: {
        type: Number
    },
    currentUserCount: {
        type: Number
    }
});

const stepOne = ['name', 'surname', 'email', 'password'];
const stepTwo = ['modules', 'role'];

// VeeValidate form setup
const {handleSubmit, handleReset, values} = useVeeForm({
    validationSchema: yup.object({
        name: yup.string().min(2).max(64).required(),
        surname: yup.string().min(2).max(64).required(),
        email: yup.string().email().required(),
        password: yup.string().required().min(8).max(64),
        phone: yup.string().nullable()
            .test('is-number', 'Must be a number', value => !value || /^\d+$/.test(value))
            .test('length-check', 'Must be between 3 & 15 digits', value => !value || (value.length >= 3 && value.length <= 15)),
        role: yup.string().required().matches(/^(additional|admin)$/, 'Choose a valid role'),
        modules: yup.array().required().min(1),
    })
});

const name = useField('name');
const surname = useField('surname');
const email = useField('email');
const password = useField('password');
const phone = useField('phone');
const role = useField('role');
role.value.value="additional";
const modules = useField('modules');

// Map of field names to their corresponding refs
const fieldRefs = {
    name,
    surname,
    email,
    password,
    phone,
    role,
    modules
};
const roleItems = ref([
    'additional',
    'admin',
])

// Inertia form setup
const form = useInertiaForm({
    name: '',
    surname: '',
    email: '',
    password: '',
    phone: '',
    role: '',
    modules: []
});

// Sync VeeValidate values with Inertia form
watch(values,(newValues) => {
    form.name = newValues.name ?? '';
    form.surname = newValues.surname ?? '';
    form.email = newValues.email ?? '';
    form.password = newValues.password ?? '';
    form.phone = newValues.phone ?? '';
    form.role = newValues.role ?? '';
    form.modules = newValues.modules ?? [];
    checkErrors();
});

const stepOneError = ref(false);
const stepTwoError = ref(false);

function checkErrors() {
    setTimeout(() => {
        stepOneError.value = stepOne.some(field => {
            const fieldInstance = fieldRefs[field];
            return fieldInstance.errorMessage.value || (form.errors[field] && form.errors[field].length > 0);
        });

        stepTwoError.value = stepTwo.some(field => {
            const fieldInstance = fieldRefs[field];
            return fieldInstance.errorMessage.value || (form.errors[field] && form.errors[field].length > 0);
        });
    }, 1000);

}

const submit = handleSubmit(() => {
    form.post(route('user.store'), {
        onSuccess: () => form.reset(),
        preserveState: (page) => Object.keys(page.props.errors).length,
    });
});

const getErrorMessage = (fieldName) => {
    if (form.errors[fieldName]) {
        return form.errors[fieldName];
    } else {
        const fieldRef = fieldRefs[fieldName];
        if (fieldRef) {
            return fieldRef.errorMessage.value;
        } else {
            return '';
        }
    }
};
</script>

<template>
    step one: {{ stepOneError }}, step two: {{ stepTwoError }}
    {{ form.modules }}
    <v-container>
        <v-stepper alt-labels editable>
            <v-stepper-header>
                <v-stepper-item
                    :rules="[() => !stepOneError]"
                    value="1"
                >
                    <template v-slot:title>
                        Primary
                    </template>
                </v-stepper-item>

                <v-divider></v-divider>

                <v-stepper-item
                    :rules="[() => !stepTwoError]"
                    value="2"
                >
                    <template v-slot:title>
                        Roles
                    </template>
                </v-stepper-item>

                <v-divider></v-divider>

                <v-stepper-item
                    value="3"
                >
                    <template v-slot:title>
                        Optional
                    </template>
                </v-stepper-item>
            </v-stepper-header>

            <v-stepper-window>
                <v-stepper-window-item value="1">
                    <v-card title="Required Fields" flat>
                        <v-container>
                            <v-row>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        v-model="name.value.value"
                                        :error-messages="getErrorMessage('name')"
                                        :counter="10"
                                        clearable
                                        label="Name"
                                        variant="solo-filled"
                                        density="compact"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        v-model="surname.value.value"
                                        :error-messages="getErrorMessage('surname')"
                                        :counter="10"
                                        clearable
                                        label="Surname"
                                        variant="solo-filled"
                                        density="compact"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        v-model="email.value.value"
                                        :error-messages="getErrorMessage('email')"
                                        clearable
                                        label="Email"
                                        variant="solo-filled"
                                        density="compact"
                                    ></v-text-field>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        v-model="password.value.value"
                                        :error-messages="getErrorMessage('password')"
                                        clearable
                                        label="Password"
                                        variant="solo-filled"
                                        density="compact"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card>
                </v-stepper-window-item>
                <v-stepper-window-item value="2">
                    <v-card title="Step Two" flat>
                        <v-container>
                            <v-row>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-select
                                        v-model="modules.value.value"
                                        :error-messages="getErrorMessage('modules')"
                                        :items="initialModules"
                                        item-value="id"
                                        item-title="name"
                                        label="Select Modules"
                                        multiple
                                        density="compact"
                                        variant="solo-filled"
                                    ></v-select>
                                </v-col>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-select
                                        v-model="role.value.value"
                                        :error-messages="getErrorMessage('role')"
                                        :items="roleItems"
                                        label="Role"
                                        variant="solo-filled"
                                        density="compact"
                                    ></v-select>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card>
                </v-stepper-window-item>
                <v-stepper-window-item value="3">
                    <v-card title="Step Three" flat>
                        <v-container>
                            <v-row>
                                <v-col
                                    cols="12"
                                    sm="6"
                                >
                                    <v-text-field
                                        v-model="phone.value.value"
                                        :error-messages="getErrorMessage('phone')"
                                        clearable
                                        label="Phone"
                                        variant="solo-filled"
                                        density="compact"
                                    ></v-text-field>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card>
                </v-stepper-window-item>
            </v-stepper-window>
        </v-stepper>
    </v-container>
</template>
