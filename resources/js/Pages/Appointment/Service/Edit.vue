<script setup>
import MainFrame from "@/Components/Frames/MainFrame.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import { computed, ref } from 'vue';
import {useForm} from "@inertiajs/vue3";
import Toggle from "@/Components/Toggle.vue";
import { useSmoothScroll } from "@/Composables/useSmoothScroll.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SimpleAlert from "@/Components/Modals/SimpleAlert.vue";
import {ClockIcon} from "@heroicons/vue/24/outline/index.js";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

const { scrollTarget } = useSmoothScroll();

const props = defineProps(
    {
        service: {
            required: true
        }
    }
)

const form = useForm({
    name: props.service.name,
    email: props.service.email,
    bio: props.service.bio,
    phone: props.service.phone,
    external: props.service.external,
    opening_time: props.service.opening_time,
    closing_time: props.service.closing_time,
    status: props.service.status,
    price: props.service.price
});

// Your Vue ref
const time = ref([{ "hours": 4, "minutes": 0}, { "hours": 5, "minutes": 0 }]);

// Function to parse backend time and update the ref
function updateTimeFromBackend(opening, closing) {
    const parseTime = (timeString) => {
        const [hours, minutes] = timeString.split(':').map(Number);
        return { hours, minutes };
    };

    time.value[0] = parseTime(opening); // Update opening time
    time.value[1] = parseTime(closing); // Update closing time
}

// Example usage
updateTimeFromBackend(props.service.opening_time, props.service.closing_time);

const booleanStatus = computed({
    get: () => props.service.status === 'active',
    set: (val) => {
        form.status = val ? 'active' : 'inactive';
    }
});
const formattedTimes = computed(() => {
    return {
        opening_time: `${time.value[0].hours.toString().padStart(2, '0')}:${time.value[0].minutes.toString().padStart(2, '0')}:00`,
        closing_time: `${time.value[1].hours.toString().padStart(2, '0')}:${time.value[1].minutes.toString().padStart(2, '0')}:00`
    };
});
const submit = () => {
    form.opening_time = formattedTimes.value.opening_time;
    form.closing_time = formattedTimes.value.closing_time;
    form.put(route('appointments.services.update', { service: props.service.id }), {
        preserveState: (page) => Object.keys(page.props.errors).length,
    });
};

const destroy = () => {
    form.delete(route('appointments.services.destroy', { service: props.service.id }), {
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
            <div class="flex items-center justify-between" ref="scrollTarget">
                <div>
                    <Toggle v-model="booleanStatus"/>
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

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
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
                <InputLabel for="external" value="external" />

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
        <SimpleAlert title="Are you sure?" cancel="Cancel" accept="Accept" @accepted="deleteAccepted(service.name)" :show="deleteModal" @close="closeDeleteModal">
            <div>
                <p>
                    Are you sure you want to delete this user? All of the data will be permanently removed from our servers forever. This action cannot be undone.
                </p>
                <div class="mt-4">
                    <InputLabel value="enter the user's name" />
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
