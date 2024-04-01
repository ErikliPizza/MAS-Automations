<script setup>
import MainFrame from "@/Components/Frames/MainFrame.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import ListDescription from "@/Components/ListDescription.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import {computed, ref, watch} from 'vue';
import {useForm} from "@inertiajs/vue3";
import Toggle from "@/Components/Toggle.vue";
import { useSmoothScroll } from "@/Composables/useSmoothScroll.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SimpleAlert from "@/Components/Modals/SimpleAlert.vue";
import FormSection from "@/Pages/Panel/Users/Partial/FormSection.vue";

const { scrollTarget } = useSmoothScroll();

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    modules: {
        type: Array,
        required: true
    }
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone,
    password: '',
    role: props.user.role,
    status: props.user.status,
    modules: props.user.modules.map(module => module.id),
});

watch(() => props.user, (newUser) => {
    form.name = newUser.name;
    form.email = newUser.email;
    form.phone = newUser.phone;
    form.role = newUser.role;
    form.status = newUser.status;
    form.modules = newUser.modules.map(module => module.id);
});

const booleanStatus = computed({
    get: () => props.user.status === 'active',
    set: (val) => {
        form.status = val ? 'active' : 'inactive';
    }
});
const submit = () => {
    form.put(route('users.update', { user: props.user.id }), {
        preserveState: true,
    });
};

const destroy = () => {
    form.delete(route('users.delete', { user: props.user.id }), {
        preserveState: false,
    });
};

const deleteModal = ref(false); // Ref to control the visibility of the map modal
const closeDeleteModal = () => { deleteModal.value = false; }; // Function to close the map modal
const openDeleteModal = () => { deleteModal.value = true; }; // Function to open the map modal

const confirmDelete = ref();


watch(
    () => form.status,
    (newValue, oldValue) => {
        if (newValue !== oldValue) {
            submit();
        }
    },
    { deep: true }
)

watch(
    () => form.role,
    (newValue, oldValue) => {
        if (newValue !== oldValue) {
            submit();
        }
    },
    { deep: true }
)
const deleteAccepted = (answer) => {
    if (confirmDelete.value === answer)
    {
        destroy();
    }
}
</script>

<template>
    <MainFrame>
        <div>
            <div class="flex items-center justify-between" v-show="user.role !== 'root' && $page.props.auth.user.id !== user.id" ref="scrollTarget">
                <div>
                    <Toggle v-model="booleanStatus" :disabled="form.processing"/>
                </div>
                <div>
                    <ListDescription v-model="form.role"/>

                    <InputError class="mt-2" :message="form.errors.role" />
                </div>
            </div>

            <form-section :modules="modules" v-model="form" @enter="submit" :passwordRequired="false" />

            <div class="flex items-center mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="submit">
                    Save
                </PrimaryButton>
            </div>
            <div class="flex items-center mt-4">
                <DangerButton class="w-full flex justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="openDeleteModal">
                    Delete
                </DangerButton>
            </div>
        </div>


        <SimpleAlert title="Are you sure?" cancel="Cancel" accept="Accept" @accepted="deleteAccepted(user.email)" :show="deleteModal" @close="closeDeleteModal">
            <div>
                <p>
                    Are you sure you want to delete this user? All of the data will be permanently removed from our servers forever. This action cannot be undone.
                </p>
                <div class="mt-4">
                    <InputLabel value="enter the user's email address" />
                    <TextInput
                        id="confirmDelete"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="confirmDelete"
                    />
                    <p class="text-center text-red-500 text-xs mt-2" v-show="user.email !== confirmDelete">
                        Confirmation input did not match. Try Again.
                    </p>
                </div>
            </div>
        </SimpleAlert>
    </MainFrame>

</template>

<style scoped>

</style>
