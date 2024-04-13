<script setup>
import SimpleModal from "@/Components/Modals/SimpleModal.vue";
import {dateTimeToTime} from "@/Services/useTimeHelpers.js";
import Quote from "@/Components/Svg/Quote.vue";
import SectionParagraph from "@/Pages/Appointment/Basic/Partials/Index/Elements/SectionParagraph.vue";
import {ref} from "vue";
import PhoneAction from "@/Pages/Appointment/Basic/Partials/Index/Elements/PhoneAction.vue";

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    appointment: {
        required: true,
        type: Object
    }
})

const openPhoneAction = ref(false);
let emit = defineEmits(['update:modelValue']);
const closeActionModal = () => {
    emit('update:modelValue',  false);
}; // Function to close the map modal
</script>

<template>
    <SimpleModal :title="appointment.name" :show="modelValue" @close="closeActionModal" v-if="appointment">
        <div class="isolate flex justify-center rounded-md">
            <section v-if="appointment">
                <div class="flex justify-between">
                    <div>
                        <div class="flex">
                            <p class="cursor-pointer text-xs text-blue-300" v-if="appointment.phone" @click="openPhoneAction = true">
                                @{{ appointment.phone }}
                            </p>
                        </div>
                        <div class="flex">
                            <a class="text-xs text-blue-300 flex" v-if="appointment.email" :href="`mailto:${appointment.email}`">
                                @{{ appointment.email }}
                            </a>
                        </div>
                        <div class="flex">
                            <p class="text-xs text-gray-600" v-if="appointment.phone" @click="openActionModal">
                                @{{ appointment.status }}
                            </p>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-xs text-gray-600">
                            {{ dateTimeToTime(appointment.start_time) }} - {{ dateTimeToTime(appointment.end_time) }}
                        </p>
                        <p class="font-semibold text-xs text-gray-600 text-center" v-if="appointment.price">
                            ₺{{ appointment.price }}
                        </p>
                    </div>

                </div>
                <SectionParagraph>
                    <quote/>
                    {{ appointment.notes }}
                </SectionParagraph>

                <div class="mt-5 sm:mt-6">
                    <Link
                        :href="'/appointments/edit-appointment/'+appointment.id"
                        type="button"
                        class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Edit this appointment
                    </Link>
                </div>
            </section>
        </div>
    </SimpleModal>
    <!-- modals -->
    <PhoneAction :appointment="appointment" v-model="openPhoneAction"/>
    <!-- modals -->
</template>

<style scoped>

</style>
