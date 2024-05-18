<script setup>
import SimpleModal from "@/Components/Modals/SimpleModal.vue";
import {ChatBubbleLeftRightIcon, PhoneIcon, UserPlusIcon} from "@heroicons/vue/24/solid/index.js";
import {useContact} from "@/Composables/useContact.vue";
import {useWhatsapp} from "@/Composables/useWhatsapp.vue";

const { callNumber, addContact } = useContact();
const { sendWith } = useWhatsapp();
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
            <button @click="callNumber(appointment.phone)" type="button" class="relative inline-flex items-center gap-x-1.5 rounded-l-md bg-white px-3 py-2 text-xs font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                <PhoneIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                Call
            </button>
            <button @click="addContact(appointment.name, appointment.phone, appointment.email ?? null)" type="button" class="relative inline-flex items-center gap-x-1.5 bg-white px-3 py-2 text-xs font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                <UserPlusIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true" />
                New Contact
            </button>
            <a :href="sendWith(appointment.phone)" target="_blank" type="button" class="relative inline-flex items-center gap-x-1.5 rounded-r-md bg-white px-3 py-2 text-xs font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                <ChatBubbleLeftRightIcon class="-ml-0.5 h-5 w-5 text-green-300" aria-hidden="true" />
                WhatsApp
            </a>
        </div>
    </SimpleModal>
</template>

<style scoped>

</style>
