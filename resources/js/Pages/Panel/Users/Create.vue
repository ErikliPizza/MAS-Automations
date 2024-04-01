<script setup>
import MainFrame from "@/Components/Frames/MainFrame.vue";
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import ListDescription from "@/Components/ListDescription.vue";
import { useSmoothScroll } from "@/Composables/useSmoothScroll.vue";
import {computed} from "vue";
import FormSection from "@/Pages/Panel/Users/Partial/FormSection.vue";

const { scrollTarget } = useSmoothScroll();

const props = defineProps(
    {
        modules: {
            required: true
        },
        limit: {
            type: Number
        },
        existingUsers: {
            type: Number
        }
    }
)
const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    role: 'additional', // Added role
    modules: [], // Added modules as an array
});

const isFormReady = computed(() => {
    if (form.role === 'admin') {
        return form.name.trim() !== '' && form.email.trim() !== '' && form.password.trim() !== '' && (form.role.trim() === 'additional' || form.role.trim() === 'admin');
    } else {
        return form.name.trim() !== '' && form.email.trim() !== '' && form.password.trim() !== '' && (form.role.trim() === 'additional' || form.role.trim() === 'admin') && form.modules.length > 0;
    }
});
const submit = () => {
    form.post(route('create-user'), {
        onSuccess: () => form.reset(),
        preserveState: (page) => Object.keys(page.props.errors).length,
    });
};
</script>

<template>
    <MainFrame>
        <form @submit.prevent="submit" v-if="existingUsers < limit">
            <div class="flex justify-between items-center" ref="scrollTarget">
                <div>
                    {{ existingUsers }} / {{ limit }}
                </div>
                <div>
                    <ListDescription v-model="form.role"/>

                    <InputError class="mt-2" :message="form.errors.role" />
                </div>
            </div>

            <form-section :modules="modules" v-model="form" :password-required="true"/>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton :class="{ 'bg-sky-100': form.processing || !isFormReady}" :disabled="form.processing || !isFormReady">
                    Register
                </PrimaryButton>
            </div>
        </form>
        <div v-else>
            <p class="text-gray-800 text-center">You've reached maximum user limit per organization.</p>
        </div>
    </MainFrame>
</template>
