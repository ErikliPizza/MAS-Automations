<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import MainFrame from "@/Components/Frames/MainFrame.vue";
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";

defineOptions({
    layout: GuestLayout,
})
defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

</script>

<template>
    <GuestLayout>
    <Head title="Log in" />
    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
        {{ status }}
    </div>

    <div>
        <ApplicationLogo class="mx-auto h-10 w-auto"/>
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">Sign in to your account</h2>
    </div>
    <form @submit.prevent="submit" class="space-y-6">
        <div class="relative -space-y-px rounded-md shadow-sm">
            <div class="pointer-events-none absolute inset-0 z-10 rounded-md ring-1 ring-inset ring-gray-300" />
            <div>
                <input
                    class="relative block w-full rounded-t-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-100 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="Email"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div>
                <input
                    class="relative block w-full rounded-b-md border-0 py-1.5 text-gray-900 ring-1 ring-inset ring-gray-100 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="Password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                </label>
            </div>

            <div class="text-sm leading-6">
                <Link
                    class="font-semibold text-indigo-600 hover:text-indigo-500"
                    v-if="canResetPassword"
                    :href="route('password.request')"
                >
                    Forgot password?
                </Link>
            </div>
        </div>

        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Log in
        </PrimaryButton>
    </form>

    <p class="text-center text-sm leading-6 text-gray-500">
        Not a member?
        {{ ' ' }}
        <a href="#" class="font-semibold text-indigo-600 hover:text-indigo-500">Start a 14-day free trial</a>
    </p>
    </GuestLayout>
</template>
