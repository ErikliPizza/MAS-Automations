<script setup>
import { useForm } from '@inertiajs/vue3';
import Main from "@/Pages/Panel/Users/Partial/Form/main.vue";
import useDateTranslator from "@/Services/useDateTranslator.js";

const { toYMD } = useDateTranslator();

const props = defineProps({
    initialModules: { required: true },
    userLimitCount: Number,
    currentUserCount: Number
});


// Inertia form setup
const form = useForm({
    name: '',
    surname: '',
    email: '',
    password: '',
    phone: '',
    role: '',
    modules: [],
    title: '',
    birthday: '',
    id_number: '',
    bank_account: '',
    salary: '',
    start_date_of_work: '',
    end_date_of_work: '',
    reason_of_leaving: ''
});

const submit = () => {
    form.birthday && (form.birthday = toYMD(form.birthday));
    form.start_date_of_work && (form.start_date_of_work = toYMD(form.start_date_of_work));
    form.end_date_of_work && (form.end_date_of_work = toYMD(form.end_date_of_work));

    form.post(route('user.store'), {
        onSuccess: () => form.reset(),
        preserveState: page => Object.keys(page.props.errors).length > 0,
    });
}

</script>

<template>
    <Main v-model="form" :initial-modules="initialModules" :user-limit-count="userLimitCount" :current-user-count="currentUserCount" @submitForm="submit"/>
</template>
