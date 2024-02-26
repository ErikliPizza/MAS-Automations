<script setup>
import {computed, ref} from "vue";
import MainFrame from "@/Components/Frames/MainFrame.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import {ClockIcon} from "@heroicons/vue/24/outline/index.js";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import {useForm} from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const time = ref([{ "hours": 10, "minutes": 0}, { "hours": 18, "minutes": 0 }]);

// Assuming you're using Composition API
const formattedTimes = computed(() => {
    return {
        opening_time: `${time.value[0].hours.toString().padStart(2, '0')}:${time.value[0].minutes.toString().padStart(2, '0')}:00`,
        closing_time: `${time.value[1].hours.toString().padStart(2, '0')}:${time.value[1].minutes.toString().padStart(2, '0')}:00`
    };
});

const form = useForm({
    name: '',
    bio: '',
    email: '',
    phone: '',
    external: '',
    opening_time: '',
    closing_time: '',
    price: ''
});
const submit = () => {
    form.opening_time = formattedTimes.value.opening_time;
    form.closing_time = formattedTimes.value.closing_time;
    form.post(route('appointments.services.create'), {
        onSuccess: () => form.reset(),
        preserveState: (page) => Object.keys(page.props.errors).length,
    });
};
</script>
<template>
    <main-frame>
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

        <div>
            <InputLabel value="Working Hours *"  class="flex justify-center"/>

            <VueDatePicker
                v-model="time"
                :is-24="true"
                time-picker
                range
                minutes-increment="15"
                no-minutes-overlay
                no-hours-overlay
                placeholder="Work Hours"
            >
                <template #input-icon>
                    <clock-icon class="ml-2 w-5 h-5 text-black"/>
                </template>
            </VueDatePicker>

            <div class="flex space-x-2">
                <InputError class="mt-2" :message="form.errors.opening_time" />
                <InputError class="mt-2" :message="form.errors.closing_time" />
            </div>
        </div>

        <div class="mt-4" ref="scrollTarget">
            <InputLabel for="email" value="Email" />

            <TextInput
                id="email"
                type="email"
                class="mt-1 block w-full"
                v-model="form.email"
                placeholder="example@mail.com"
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
            <InputLabel for="external" value="External" />

            <TextInput
                id="external"
                type="text"
                class="mt-1 block w-full"
                v-model="form.external"
            />

            <InputError class="mt-2" :message="form.errors.external" />
        </div>

        <div class="mt-4">
            <div>
                <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Price</label>
                <div class="relative mt-2 rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-500 sm:text-sm">₺</span>
                    </div>
                    <input type="text" name="price" id="price" v-model="form.price" class="block w-full rounded-md border-0 py-1.5 pl-7 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="0.00" aria-describedby="price-currency" />
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <span class="text-gray-500 sm:text-sm" id="price-currency">TL</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <InputLabel for="bio" value="Bio" />

            <textarea v-model="form.bio" rows="3" name="bio" id="bio" class="block w-full resize-none border-0 border-b border-transparent p-0 pb-2 text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-0 sm:text-sm sm:leading-6" placeholder="Add your bio..." />

            <InputError class="mt-2" :message="form.errors.bio" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <PrimaryButton @click="submit" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                Create Service
            </PrimaryButton>
        </div>
    </main-frame>
</template>

<style scoped>
/* Add any additional Tailwind CSS styles or custom styles here */
</style>
