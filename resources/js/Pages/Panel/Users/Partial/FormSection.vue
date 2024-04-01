<script setup>

import InputLabel from "@/Components/InputLabel.vue";
import {EnvelopeIcon, UsersIcon} from "@heroicons/vue/20/solid/index.js";
import TextInput from "@/Components/TextInput.vue";
import Combobox from "@/Components/Combobox.vue";
import InputError from "@/Components/InputError.vue";
import CustomInput from "@/Components/CustomInput.vue";
import {computed} from "vue";

defineProps({
    modelValue: {
        required: true
    },
    modules: {
        required: true
    },
    passwordRequired: {
        required: true,
        type: Boolean,
        default: false
    }
})

</script>

<template>
    <div class="mt-4">
        <InputLabel for="name" value="Name *" />

        <div class="mt-1 flex rounded-md shadow-sm">
            <div class="relative flex flex-grow items-stretch focus-within:z-10">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <UsersIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                </div>
                <CustomInput
                    @keyup.enter="$emit('enter')"
                    id="name"
                    type="text"
                    v-model="modelValue.name"
                    required
                    autocomplete="name"
                    placeholder="John Doe"
                />
            </div>
        </div>

        <InputError class="mt-2" :message="modelValue.errors.name" />
    </div>

    <div class="mt-4">
        <InputLabel for="email" value="Email *" />

        <div class="relative rounded-md shadow-sm mt-1">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <EnvelopeIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </div>
            <CustomInput
                @keyup.enter="$emit('enter')"
                id="email"
                type="email"
                v-model="modelValue.email"
                required
                autocomplete="email"
                placeholder="you@example.com"
            />
        </div>

        <InputError class="mt-2" :message="modelValue.errors.email" />
    </div>

    <div class="mt-4">
        <InputLabel for="phone" value="Phone" />

        <TextInput
            @keyup.enter="$emit('enter')"
            id="phone"
            type="text"
            class="mt-1 block w-full"
            v-model="modelValue.phone"
            autocomplete="phone"
        />

        <InputError class="mt-2" :message="modelValue.errors.phone" />
    </div>

    <div class="mt-4">
        <InputLabel for="password" value="Password *" v-if="passwordRequired"/>
        <InputLabel for="password" value="Password" v-else/>

        <TextInput
            :required="passwordRequired"
            @keyup.enter="$emit('enter')"
            id="password"
            type="password"
            class="mt-1 block w-full"
            v-model="modelValue.password"
            autocomplete="password"
        />

        <InputError class="mt-2" :message="modelValue.errors.password" />
    </div>

    <div class="mt-4" v-show="modelValue.role === 'additional'">
        <InputLabel value="Modules *" />
        <Combobox v-model="modelValue.modules" :item="modules" :limit="modules.length" placeholder="select modules"/>
        <InputError class="mt-2" :message="modelValue.errors.modules" />
    </div>
</template>

<style scoped>

</style>
