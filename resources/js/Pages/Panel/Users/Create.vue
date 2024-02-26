<script setup>
import MainFrame from "@/Components/Frames/MainFrame.vue";
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import ListDescription from "@/Components/ListDescription.vue";
import Combobox from "@/Components/Combobox.vue";
import { useSmoothScroll } from "@/Composables/useSmoothScroll.vue";
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
    password_confirmation: '',
    role: 'additional', // Added role
    modules: [], // Added modules as an array
});
const submit = () => {
    form.post(route('create-user'), {
        onSuccess: () => form.reset(),
        preserveState: (page) => Object.keys(page.props.errors).length,
    });
};
</script>

<template>
    <Head title="Register" />
    <MainFrame>
        <form @submit.prevent="submit" v-if="existingUsers < limit">
            <div class="flex justify-between items-center">
                <div>
                    {{ existingUsers }} / {{ limit }}
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
                    placeholder="John Doe"
                    required
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4" ref="scrollTarget">
                <InputLabel for="email" value="Email *" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    placeholder="example@mail.com"
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

            <div class="mt-4 flex space-x-1">
                <div>
                    <InputLabel for="password" value="Password *" />

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                    />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div>
                    <InputLabel for="password_confirmation" value="Password *" />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <div class="flex">
                    <InputError class="mt-2" :message="form.errors.password_confirmation" />
                </div>
            </div>

            <div class="mt-4" v-show="form.role === 'additional'">
                <InputLabel value="Modules *" />

                <Combobox v-model="form.modules" :item="modules" :limit="modules.length" placeholder="select modules"/>
                <InputError class="mt-2" :message="form.errors.modules" />
            </div>
            <div class="flex items-center justify-end mt-4">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
        <div v-else>
            <p class="text-gray-800 text-center">You've reached maximum user limit per organization.</p>
        </div>
    </MainFrame>
</template>
