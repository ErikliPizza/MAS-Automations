<script setup>
import {useField, useForm as useVeeForm} from 'vee-validate';
import {computed, ref, watch} from 'vue';
import WindowItem from '@/Pages/Panel/Users/Partial/Frames/window-item.vue';
import Column from "@/Pages/Panel/Users/Partial/Frames/column.vue";
import StepperHeader from "@/Pages/Panel/Users/Partial/Elements/stepper-header.vue";
import {useStepErrorChecker} from '@/Services/useStepErrorChecker';
import {useSyncFormValues} from "@/Services/useSyncFormValues.js";
import {useSyncFormErrors} from "@/Services/useSyncFormErrors.js";
import {userValidation} from "@/Services/userValidation.js";
import DatePickerInDialog from "@/Pages/Panel/Users/Partial/Elements/date-picker-in-dialog.vue";


const props = defineProps({
    modelValue: { required: true },
    initialModules: { required: true },
    userLimitCount: Number,
    currentUserCount: Number,
});

const roleItems = ref(['additional', 'admin']);

const steps = [
    {
        name: 'stepOne',
        title: 'Primary',
        fields: ['name', 'surname', 'email', 'password']
    },
    {
        name: 'stepTwo',
        title: 'Roles',
        fields: ['modules', 'role']
    },
    {
        name: 'stepThree',
        title: 'Optional',
        fields: ['phone', 'title', 'birthday', 'id_number', 'bank_account', 'salary', 'start_date_of_work', 'end_date_of_work', 'reason_of_leaving']
    }
];


// VeeValidate form setup
const { handleSubmit, values } = useVeeForm({
    validationSchema: userValidation
});
const fields = {
    name: useField('name'),
    surname: useField('surname'),
    email: useField('email'),
    password: useField('password'),
    phone: useField('phone'),

    role: useField('role'),
    modules: useField('modules'),

    title: useField('title'),
    birthday: useField('birthday'),
    id_number: useField('id_number'),
    bank_account: useField('bank_account'),
    salary: useField('salary'),

    start_date_of_work: useField('start_date_of_work'),
    end_date_of_work: useField('end_date_of_work'),
    reason_of_leaving: useField('reason_of_leaving')
};
fields.role.value.value = 'additional';

useSyncFormValues(values, fields, props.modelValue);
const { getErrorMessage } = useSyncFormErrors(props.modelValue, fields);
const { stepErrors } = useStepErrorChecker(fields, props.modelValue, steps);

const emit = defineEmits(['update:modelValue', 'submitForm']);

const submit = handleSubmit(() => {
    emit('submitForm');
});

watch(() => fields.end_date_of_work.value.value, (newValue) => {
    if (!newValue) {
        fields.reason_of_leaving.value.value = null;
    }
});
const stepperController = ref(0)
const stepperDisabled = computed(() => {
    return stepperController.value === 0 ? 'prev' : stepperController.value === steps.value ? 'next' : undefined
})

// one-way model binding. Props are read only.
</script>

<template>
    <v-container>
        <v-stepper alt-labels editable show-actions v-model="stepperController">
            <stepper-header :steps="steps" :stepErrors="stepErrors" />

            <v-stepper-window>
                <window-item :title="steps[0].title" :value="0">
                    <v-row>
                        <column>
                            <v-text-field
                                prepend-inner-icon="mdi-account-circle"
                                v-model="fields.name.value.value"
                                :error-messages="getErrorMessage('name')"
                                :counter="10"
                                clearable
                                label="Name"
                                variant="solo-filled"
                                density="compact"
                            ></v-text-field>
                        </column>

                        <column>
                            <v-text-field
                                prepend-inner-icon="mdi-account-circle"
                                v-model="fields.surname.value.value"
                                :error-messages="getErrorMessage('surname')"
                                :counter="10"
                                clearable
                                label="Surname"
                                variant="solo-filled"
                                density="compact"
                            ></v-text-field>
                        </column>

                        <column>
                            <v-text-field
                                prepend-inner-icon="mdi-email-fast"
                                v-model="fields.email.value.value"
                                :error-messages="getErrorMessage('email')"
                                clearable
                                label="Email"
                                variant="solo-filled"
                                density="compact"
                            ></v-text-field>
                        </column>

                        <column>
                            <v-text-field
                                prepend-inner-icon="mdi-key"
                                v-model="fields.password.value.value"
                                :error-messages="getErrorMessage('password')"
                                clearable
                                label="Password"
                                variant="solo-filled"
                                density="compact"
                            ></v-text-field>
                        </column>
                    </v-row>
                </window-item>

                <window-item :title="steps[1].title" :value="1">
                    <v-row>
                        <column>
                            <v-select
                                prepend-inner-icon="mdi-view-module"
                                v-model="fields.modules.value.value"
                                :error-messages="getErrorMessage('modules')"
                                :items="initialModules"
                                item-value="id"
                                item-title="name"
                                label="Select Modules"
                                multiple
                                density="compact"
                                variant="solo-filled"
                            ></v-select>
                        </column>
                        <column>
                            <v-select
                                prepend-inner-icon="mdi-account-group-outline"
                                v-model="fields.role.value.value"
                                :error-messages="getErrorMessage('role')"
                                :items="roleItems"
                                label="Role"
                                variant="solo-filled"
                                density="compact"
                            ></v-select>
                        </column>
                    </v-row>
                </window-item>

                <window-item :title="steps[2].title" :value="2">
                    <v-row>
                        <column>
                            <v-text-field
                                prepend-inner-icon="mdi-briefcase-account"
                                v-model="fields.title.value.value"
                                :error-messages="getErrorMessage('title')"
                                clearable
                                label="Title"
                                variant="solo-filled"
                                density="compact"
                            ></v-text-field>
                        </column>

                        <column>
                            <date-picker-in-dialog
                                prepend-inner-icon="mdi-cake-variant"
                                :error-messages="getErrorMessage('birthday')"
                                v-model="fields.birthday.value.value" label="Birthday"/>
                        </column>

                        <column>
                            <v-text-field
                                prepend-inner-icon="mdi-card-account-details"
                                v-model="fields.id_number.value.value"
                                :error-messages="getErrorMessage('id_number')"
                                clearable
                                label="ID Number"
                                variant="solo-filled"
                                density="compact"
                            ></v-text-field>
                        </column>

                        <column>
                            <v-text-field
                                prepend-inner-icon="mdi-bank-check"
                                v-model="fields.bank_account.value.value"
                                :error-messages="getErrorMessage('bank_account')"
                                clearable
                                label="IBAN"
                                variant="solo-filled"
                                density="compact"
                            ></v-text-field>
                        </column>

                        <column>
                            <v-text-field
                                prepend-inner-icon="mdi-cash-multiple"
                                v-model="fields.salary.value.value"
                                :error-messages="getErrorMessage('salary')"
                                clearable
                                label="Salary"
                                variant="solo-filled"
                                density="compact"
                            ></v-text-field>
                        </column>

                        <column>
                            <v-text-field
                                prepend-inner-icon="mdi-phone"
                                v-model="fields.phone.value.value"
                                :error-messages="getErrorMessage('phone')"
                                clearable
                                label="Phone"
                                variant="solo-filled"
                                density="compact"
                            ></v-text-field>
                        </column>

                        <v-col
                            cols="12"
                            sm="12"
                            v-show="fields.end_date_of_work.value.value"
                        >
                            <v-textarea
                                :counter="500"
                                v-model="fields.reason_of_leaving.value.value"
                                :error-messages="getErrorMessage('reason_of_leaving')"
                                clearable
                                label="Reason of leaving"
                                variant="solo-filled"
                                density="compact"
                            />
                        </v-col>

                        <column>
                            <date-picker-in-dialog
                                prepend-inner-icon="mdi-calendar-import"
                                :error-messages="getErrorMessage('start_date_of_work')"
                                v-model="fields.start_date_of_work.value.value" label="Start Date of Work"/>
                        </column>

                        <column>
                            <date-picker-in-dialog
                                prepend-inner-icon="mdi-calendar-export"
                                :error-messages="getErrorMessage('end_date_of_work')"
                                v-model="fields.end_date_of_work.value.value" label="End Date of Work"/>
                        </column>
                    </v-row>
                </window-item>
            </v-stepper-window>

            <v-row class="ma-12" align="center" justify="center">
                <v-btn append-icon="mdi-account-child" @click="submit" block>
                    Create User
                </v-btn>
            </v-row>

            <v-stepper-actions
                :disabled="stepperDisabled"
                @click:next="stepperController++"
                @click:prev="stepperController--"
            ></v-stepper-actions>



        </v-stepper>
    </v-container>
</template>
