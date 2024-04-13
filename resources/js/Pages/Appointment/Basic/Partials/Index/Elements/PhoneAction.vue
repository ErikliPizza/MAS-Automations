<script setup>
import SimpleModal from "@/Components/Modals/SimpleModal.vue";
import {ChatBubbleLeftRightIcon, PhoneIcon, UserPlusIcon} from "@heroicons/vue/24/solid/index.js";

let emit = defineEmits(['update:modelValue']);

const props = defineProps({
    modelValue: Boolean,
    appointment: {
        required: true,
        type: Object
    }
})
const closeActionModal = () => {
    emit('update:modelValue',  false);
}; // Function to close the map modal
</script>

<template>
    <SimpleModal :title="appointment.name" :show="modelValue" @close="closeActionModal" v-if="appointment">
        <div class="isolate flex justify-center rounded-md">
            <button type="button" class="relative inline-flex items-center gap-x-1.5 rounded-l-md bg-white px-3 py-2 text-xs font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                <PhoneIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                Call
            </button>
            <button type="button" class="relative inline-flex items-center gap-x-1.5 bg-white px-3 py-2 text-xs font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                <UserPlusIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                New Contact
            </button>
            <button type="button" class="relative inline-flex items-center gap-x-1.5 rounded-r-md bg-white px-3 py-2 text-xs font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                <ChatBubbleLeftRightIcon class="-ml-0.5 h-5 w-5 text-green-300" aria-hidden="true" />
                WhatsApp
            </button>
        </div>
    </SimpleModal>
</template>

<style scoped>

</style>
