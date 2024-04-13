<script setup>
import {EllipsisHorizontalIcon, CalendarIcon, CheckIcon, ExclamationCircleIcon, XCircleIcon} from "@heroicons/vue/24/solid/index.js";
import SimpleModal from "@/Components/Modals/SimpleModal.vue";
import {ref} from "vue";
import {router} from "@inertiajs/vue3";
const props = defineProps({
    appointment: {
        required: true,
        type: Object
    }
})

const actionModal = ref(false); // Ref to control the visibility of the map modal
const closeActionModal = () => { actionModal.value = false; }; // Function to close the map modal
const openActionModal = () => { actionModal.value = true; }; // Function to open the map modal
const updateStatus = (status) => {
    if (status === props.appointment.status) {
        closeActionModal();
        return;
    }
    router.put(route('appointments.updateStatus', { appointment: props.appointment.id}), {
        status: status,
        only: ['appointments'],
        replace: true,
    }, {
        onFinish: () => closeActionModal(),
        preserveScroll: true
    });
};
</script>

<template>
    <EllipsisHorizontalIcon
        @click="openActionModal"
        class="h-4 w-4 rounded-full text-gray-400 group-hover:text-gray-500 bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-100 focus:ring-offset-1 cursor-pointer"
        aria-hidden="true"
    />

    <SimpleModal title="Select an Action" :show="actionModal" @close="closeActionModal">
        <div class="isolate flex justify-center rounded-md">
            <button @click="updateStatus('booked')" type="button" class="relative flex flex-col justify-center items-center gap-x-1.5 rounded-l-md bg-white px-3 py-2 text-xs font-semibold text-gray-900 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                <CalendarIcon class="h-6 w-6 text-gray-500" aria-hidden="true" />
                Booked
            </button>
            <button @click="updateStatus('completed')" type="button" class="relative flex flex-col justify-center items-center gap-x-1.5 bg-white px-3 py-2 text-xs font-semibold text-gray-900  ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                <div class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full bg-green-500 group-hover:bg-green-600">
                    <CheckIcon class="h-4 w-4 text-white" aria-hidden="true" />
                </div>
                Completed
            </button>
            <button @click="updateStatus('missed')" type="button" class="relative flex flex-col justify-center items-center gap-x-1.5 bg-white px-3 py-2 text-xs font-semibold text-gray-900  ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                <div class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full bg-orange-500 group-hover:bg-orange-600">
                    <ExclamationCircleIcon class="h-4 w-4 text-white" aria-hidden="true" />
                </div>
                Missed
            </button>
            <button @click="updateStatus('cancelled')" type="button" class="relative flex flex-col justify-center items-center gap-x-1.5 rounded-r-md bg-white px-3 py-2 text-xs font-semibold text-gray-900  ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                <div class="relative z-10 flex h-6 w-6 items-center justify-center rounded-full bg-red-500 group-hover:bg-orange-600">
                    <XCircleIcon class="h-4 w-4 text-white" aria-hidden="true" />
                </div>
                Cancelled
            </button>
        </div>
    </SimpleModal>
</template>

