<script setup>
import MainFrame from "@/Components/Frames/MainFrame.vue";
import HeadlessList from "@/Components/HeadlessList.vue";
import {ArrowTopRightOnSquareIcon, CheckIcon, ExclamationCircleIcon, XCircleIcon, ArrowsPointingOutIcon, ArrowsPointingInIcon, PhoneIcon, ChatBubbleLeftRightIcon, UserPlusIcon} from "@heroicons/vue/24/solid/index.js";
import {computed, onMounted, ref, watch} from "vue";
import {router} from "@inertiajs/vue3";
import {useQueryParam} from "@/Services/useQueryParam.js";
import WideFrame from "@/Components/Frames/WideFrame.vue";
import {dateTimeToTime, timestampToHHMM} from "@/Services/useTimeHelpers.js";
import AppointmentType from "@/Pages/Appointment/Basic/Partials/Index/Frames/AppointmentType.vue";
import Quote from "@/Components/Svg/Quote.vue";
import SectionParagraph from "@/Pages/Appointment/Basic/Partials/Index/Elements/SectionParagraph.vue";
import SimpleModal from "@/Components/Modals/SimpleModal.vue";


const container = ref(null);
const appointmentsRange = ref(1);
const expand = ref(false);
const actionModal = ref(false); // Ref to control the visibility of the map modal
const closeActionModal = () => { actionModal.value = false; }; // Function to close the map modal
const openActionModal = () => { actionModal.value = true; }; // Function to open the map modal

const props = defineProps({
    services: {
        type: Array,
    },
    service: {
        type: Array
    },
    appointments: {
        type: Array
    }
})
onMounted(() => {
    if (useQueryParam('service')) {
        props.service.id = useQueryParam('service');
    }
    setAppointmentRange();
})
function setAppointmentRange () {
    appointmentsRange.value = 1;
    setTimeout( () => {
        if (hasOngoingAppointment.value) {
            appointmentsRange.value = findOngoingAppointmentIndex.value+1;
        } else if (hasNextAppointment.value) {
            appointmentsRange.value = findNextAppointmentIndex.value+1;
        } else {
            appointmentsRange.value = 1;
        }
    }, 200)

}
watch(() => props.service.id, (newValue, oldValue) => {
    if (newValue && newValue !== oldValue) {
        router.reload({
            only: ['service', 'appointments', 'previous', 'ongoing', 'next'],
            data: {
                'service': newValue
            },
            replace: true
        })
    }
});


watch(() => appointmentsRange.value, (newValue, oldValue) => {
    let x = document.getElementById('appointment-'+(newValue-1));
    if (x) {
        let containerRect = container.value.getBoundingClientRect();
        let elementRect = x.getBoundingClientRect();
        let scrollTopOffset = (elementRect.top - 6) - containerRect.top + container.value.scrollTop;

        // Smooth scroll behavior using CSS transition
        container.value.style.scrollBehavior = 'smooth';
        container.value.scrollTop = scrollTopOffset;

        // Reset scroll behavior to default after scrolling
        container.value.addEventListener('scroll', () => {
            container.value.style.scrollBehavior = 'auto';
        }, { once: true });
    }
});

const getStatusColor = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-green-500'; // purple for completed
        case 'missed':
            return 'bg-orange-500'; // Orange for missed
        case 'cancelled':
            return 'bg-red-500'; // Red for cancelled
        default:
            return 'bg-gray-300'; // A default color if none of the statuses match
    }
}
const getStatusColorAsText = (status) => {
    switch (status) {
        case 'previous':
            return 'text-gray-300'; // purple for completed
        case 'ongoing':
            return 'text-indigo-300'; // Orange for missed
        case 'next':
            return 'text-sky-300'; // Red for cancelled
        default:
            return 'text-black-300'; // A default color if none of the statuses match
    }
}

const hasOngoingAppointment = computed(() => {
    return props.appointments.find(appointment => appointment.type === "ongoing") || null;
});

const findOngoingAppointmentIndex = computed(() => {
    return props.appointments.findIndex(appointment => appointment.type === "ongoing");
});

const hasNextAppointment = computed(() => {
    return props.appointments.find(appointment => appointment.type === "next") || null;
});

const findNextAppointmentIndex = computed(() => {
    return props.appointments.findIndex(appointment => appointment.type === "next");
});

const heroAppointment = computed(()=> {
    if (hasOngoingAppointment.value) {
        return hasOngoingAppointment.value;
    } else if (hasNextAppointment.value) {
        return hasNextAppointment.value;
    } else {
        return false
    }
})
</script>

<template>
    <wide-frame>
        <div class="flex justify-center items-center mb-4 space-x-2">
            <HeadlessList :list="services" v-model="service.id" class="w-full"/>
        </div>
        <main-frame>
            <section v-if="heroAppointment">
                <div class="flex justify-between">
                    <div>
                        <p>{{ heroAppointment.name }}</p>
                        <div class="text-xs text-gray-600 flex">
                            <span class="cursor-pointer" v-if="heroAppointment.phone" @click="openActionModal">
                                @{{ heroAppointment.phone }}
                            </span>
                            <a class="text-xs text-gray-600 flex" v-else-if="heroAppointment.email" :href="`mailto:${heroAppointment.email}`">
                                @{{ heroAppointment.email }}
                            </a>
                            <ArrowTopRightOnSquareIcon class="w-2.5 h-2.5"/>
                        </div>
                    </div>
                    <div>
                        <p class="font-semibold text-xs text-gray-600">{{ dateTimeToTime(heroAppointment.start_time) }} - {{ dateTimeToTime(heroAppointment.end_time) }}</p>
                    </div>
                </div>
                <SectionParagraph>
                    <quote/>
                    {{ heroAppointment.notes }}
                </SectionParagraph>
            </section>
            <section v-else>
                <SectionParagraph>
                    <quote/>
                    You have no future appointment for today!
                </SectionParagraph>
            </section>

            <div class="relative" v-show="appointments.length > 2">
                <div class="flex justify-end">
                    <ArrowsPointingOutIcon class="h-6 w-6 text-gray-600 cursor-pointer p-0.5" v-if="!expand" @click="expand=true"/>
                    <ArrowsPointingInIcon class="h-6 w-6 text-gray-600 cursor-pointer p-0.5" v-else @click="() => {expand=false; setAppointmentRange();}"/>
                </div>
                <div v-show="!expand">
                    <input v-model="appointmentsRange" name="appointments-range-input" id="appointments-range-input" type="range" min="1" :max="appointments.length" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700" list="appointment-indicators">
                    <span class="text-sm text-gray-500 dark:text-gray-400 absolute start-0 -bottom-6">{{ timestampToHHMM(service.opening_time) }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 absolute end-0 -bottom-6">{{ timestampToHHMM(service.closing_time) }}</span>
                    <datalist id="appointment-indicators" class="w-full mt-2">
                        <option v-for="(appointment, index) in appointments" :value="index + 1" @click="appointmentsRange = (index + 1)">
                            <div :class="getStatusColorAsText(appointment.type)" class="text-2xl font-bold">-</div>
                        </option>
                    </datalist>
                </div>
            </div>

            <ol role="list" class="overflow-hidden" :class="[expand ? '' : 'h-32']" ref="container">
                <li v-for="(appointment, appointmentIdx) in appointments" :key="appointmentIdx" :id="'appointment-'+appointmentIdx" :class="[appointmentIdx !== appointments.length - 1 ? 'pb-10' : '', 'relative']">

                    <div
                        v-if="appointmentIdx !== appointments.length - 1"
                        class="absolute left-4 top-4 -ml-px mt-0.5 h-full w-0.5"
                        :class="getStatusColor(appointment.status)"
                        aria-hidden="true" />

                    <div class="group relative flex items-start">

                        <AppointmentType v-if="appointment.type === 'previous'">
                            <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-green-500 group-hover:bg-green-600" v-if="appointment.status === 'completed'">
                                <CheckIcon class="h-5 w-5 text-white" aria-hidden="true" />
                            </div>
                            <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-orange-500 group-hover:bg-orange-600" v-else-if="appointment.status === 'missed'">
                                <ExclamationCircleIcon class="h-5 w-5 text-white" aria-hidden="true" />
                            </div>
                            <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 group-hover:bg-red-600" v-else>
                                <XCircleIcon class="h-5 w-5 text-white" aria-hidden="true" />
                            </div>
                        </AppointmentType>

                        <AppointmentType v-else-if="appointment.type === 'ongoing'" ref="specificSection">
                            <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-indigo-600 bg-white">
                                <div class="h-2.5 w-2.5 rounded-full bg-indigo-600 blinking-dot" />
                            </div>
                        </AppointmentType>

                        <AppointmentType v-else-if="appointment.type === 'next'">
                            <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-300 bg-white">
                                <div class="flex" v-show="hasOngoingAppointment === null">
                                    <div class="dot bg-sky-300"></div>
                                    <div class="dot bg-sky-300"></div>
                                    <div class="dot bg-sky-300"></div>
                                </div>
                            </div>
                        </AppointmentType>


                        <AppointmentType v-else>
                            <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-300 bg-white group-hover:border-gray-400">
                                <div class="h-2.5 w-2.5 rounded-full bg-transparent group-hover:bg-gray-300" />
                            </div>
                        </AppointmentType>

                        <div class="ml-4 flex min-w-0 flex-col">
                            <div class="flex">
                                <div class="text-sm font-medium" :class="{'text-indigo-600' : appointment.type === 'ongoing'}">
                                    {{ appointment.name }}
                                </div>
                            </div>
                            <span class="text-sm text-gray-500">{{ dateTimeToTime(appointment.start_time) }} - {{ dateTimeToTime(appointment.end_time) }}</span>
                        </div>
                    </div>
                </li>
            </ol>
        </main-frame>
    </wide-frame>

    <SimpleModal title="Select an Action" :show="actionModal" @close="closeActionModal">
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
datalist {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    writing-mode: vertical-lr;
}

option {
    padding: 0;
}

input[type="range"] {
    margin: 0;
}

/* Define the blinking animation */
@keyframes blink {
    0%, 100% {
        scale: 125%;
        opacity: 1;
    }
    50% {
        scale: 50%;
        opacity: 0;
    }
}

/* Apply the animation to the blinking dot */
.blinking-dot {
    animation: blink 3s linear infinite;
}

.dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    animation: move 1.5s infinite ease-in-out;
}

.dot:nth-child(1) {
    left: 0;
    animation-delay: 0s;
}

.dot:nth-child(2) {
    left: 30px;
    animation-delay: 0.5s;
}

.dot:nth-child(3) {
    left: 60px;
    animation-delay: 1s;
}

@keyframes move {
    0%, 80%, 100% {
        transform: scale(0);
    }
    40% {
        transform: scale(1);
    }
}
</style>
