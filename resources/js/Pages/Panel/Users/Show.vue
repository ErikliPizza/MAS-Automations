<script setup>
import MainFrame from "@/Components/Frames/MainFrame.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import ListDescription from "@/Components/ListDescription.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { computed, ref } from 'vue';
import {useForm} from "@inertiajs/vue3";
import Toggle from "@/Components/Toggle.vue";
import Combobox from "@/Components/Combobox.vue";
import { useSmoothScroll } from "@/Composables/useSmoothScroll.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SimpleAlert from "@/Components/SimpleAlert.vue";

const { scrollTarget } = useSmoothScroll();

const props = defineProps(
    {
        user: {
            required: true
        },
        modules: {
            required: true
        }
    }
)

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone,
    password: '',
    role: props.user.role, // Added role
    status: props.user.status,
    modules: props.user.modules.map(module => module.id), // Added modules as an array
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
const deleteAccepted = (answer) => {
    if (confirmDelete.value === answer)
    {
        destroy();
    } else {
        alert('does not match');
    }
}
</script>

<template>

    <MainFrame>
        <form @submit.prevent="submit">
            <div class="flex items-center justify-between" v-show="user.role !== 'root' && $page.props.auth.user.id !== user.id" ref="scrollTarget">
                <div>
                    <Toggle v-model="booleanStatus"/>
                </div>
                <div>
                    <ListDescription v-model="form.role"/>

                    <InputError class="mt-2" :message="form.errors.role" />
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="name" value="Name *" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email *" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="email"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="phone" value="Phone" />

                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    autocomplete="phone"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4" v-show="form.role === 'additional'">
                <InputLabel value="Modules *" />

                <Combobox v-model="form.modules" :item="modules" :limit="modules.length" placeholder="select modules"/>
                <InputError class="mt-2" :message="form.errors.modules" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Save
                </PrimaryButton>
            </div>
        </form>
        <div class="flex justify-center items-center mt-4">
            <DangerButton class="w-1/2 flex justify-center" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="openDeleteModal">
                Delete
            </DangerButton>
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
                </div>
            </div>
        </SimpleAlert>
    </MainFrame>

</template>

<style scoped>

</style>
