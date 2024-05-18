<script setup>
import MainFrame from "@/Components/Frames/MainFrame.vue";
import HeadlessList from "@/Components/HeadlessList.vue";
import {CheckIcon, ExclamationCircleIcon, XCircleIcon, ArrowsPointingOutIcon, ArrowsPointingInIcon} from "@heroicons/vue/24/solid/index.js";
import {ClockIcon, RectangleGroupIcon, ArrowUpCircleIcon, ArrowDownCircleIcon} from "@heroicons/vue/24/outline/index.js";
import {onMounted, ref, watch} from "vue";
import {router} from "@inertiajs/vue3";
import {useQueryParam} from "@/Services/useQueryParam.js";
import WideFrame from "@/Components/Frames/WideFrame.vue";
import {dateTimeToTime, timestampToHHMM} from "@/Services/useTimeHelpers.js";
import AppointmentType from "@/Pages/Appointment/Basic/Partials/Index/Frames/AppointmentType.vue";
import Quote from "@/Components/Svg/Quote.vue";
import SectionParagraph from "@/Pages/Appointment/Basic/Partials/Index/Elements/SectionParagraph.vue";
import StatusMenu from "@/Pages/Appointment/Basic/Partials/Index/Elements/StatusMenu.vue";
import Card from "@/Pages/Appointment/Basic/Partials/Index/Elements/Card.vue";
import PhoneAction from "@/Pages/Appointment/Basic/Partials/Index/Elements/PhoneAction.vue";

const container = ref(null);
const openCard = ref(false);
const selectedAppointment = ref(null);
const openCardModal = (appointment) => {
    selectedAppointment.value = appointment;
    openCard.value = true;
};
const openPhoneAction = ref(false);

const appointmentsRange = ref(1);
const expand = ref(false);
const props = defineProps({
    services: {
        type: Object,
    },
    service: {
        type: Object
    },
    appointments: {
        type: Object
    },
    heroAppointment: {
        type: Object
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
        if (props.heroAppointment) {
            appointmentsRange.value = props.appointments.findIndex(appointment => appointment.selected)+1;
        }
    }, 200)

}

// Function to handle mouse wheel scrolling
const handleScroll = (event) => {
    // Increase or decrease the value based on scroll direction
    if (event.deltaY > 0) {
        appointmentsRange.value += 1; // Decrease value
    } else {
        appointmentsRange.value -= 1; // Increase value
    }

    // Ensure the value stays within the range of 0 to 100
    appointmentsRange.value = Math.min(Math.max(appointmentsRange.value, 1), props.appointments.length);
};

watch(() => props.service.id, (newValue, oldValue) => {
    if (newValue && newValue !== oldValue) {
        router.reload({
            only: ['service', 'appointments', 'heroAppointment'],
            data: {
                'service': newValue
            },
            replace: true
        }, {
            onFinish: setAppointmentRange(),
        })
    }
});


watch(() => appointmentsRange.value, (newValue) => {
    let x = document.getElementById('appointment-'+(newValue-2)) ?? document.getElementById('appointment-0');
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
        case 'completed':
            return 'text-green-300'; // purple for completed
        case 'missed':
            return 'text-orange-300'; // Orange for missed
        case 'cancelled':
            return 'text-red-300'; // Red for cancelled
        default:
            return 'text-indigo-300'; // A default color if none of the statuses match
    }
}
</script>

<template>
    <div>
        <wide-frame>
            <div class="flex justify-center items-center mb-4 space-x-2">
                <HeadlessList :list="services" v-model="service.id" class="w-full"/>
            </div>
            <main-frame>
                <section v-if="heroAppointment">
                    <div class="flex justify-between">
                        <div>
                            <p class="text-gray-900 font-semibold">{{ heroAppointment.name }}</p>
                            <div class="flex" v-if="heroAppointment.phone">
                                <p class="cursor-pointer text-xs text-blue-300" @click="openPhoneAction = true">
                                    @{{ heroAppointment.phone }}
                                </p>
                            </div>
                            <div class="flex" v-else-if="heroAppointment.email">
                                <a class="text-xs text-blue-300 flex" :href="`mailto:${heroAppointment.email}`">
                                    @{{ heroAppointment.email }}
                                </a>
                            </div>
                        </div>
                        <div>
                            <p class="font-semibold text-xs text-gray-600">
                                {{ dateTimeToTime(heroAppointment.start_time) }} - {{ dateTimeToTime(heroAppointment.end_time) }}
                            </p>
                            <div class="flex justify-center" v-if="heroAppointment.type === 'next'">
                                <div class="flex">
                                    <div class="dot bg-sky-300"></div>
                                    <div class="dot bg-sky-300"></div>
                                    <div class="dot bg-sky-300"></div>
                                </div>
                            </div>
                            <div class="flex justify-center" v-else>
                                <div class="h-2.5 w-2.5 rounded-full bg-indigo-600 blinking-dot" />
                            </div>
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

                <div class="relative" v-show="appointments.length > 3">
                    <div v-show="!expand">
                        <input v-show="false" v-model="appointmentsRange" name="appointments-range-input" id="appointments-range-input" type="range" min="1" :max="appointments.length" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700" list="appointment-indicators">
                        <datalist id="appointment-indicators" class="w-full">
                            <option v-for="(appointment, index) in appointments" :value="index + 1" @click="appointmentsRange = (index + 1)" class="cursor-pointer w-10 h-6 flex justify-center items-center">
                                <RectangleGroupIcon class="w-4 h-4" :class="[appointment.selected ? 'text-indigo-500 blink' : getStatusColorAsText(appointment.status) ]"/>
                            </option>
                        </datalist>
                    </div>
                </div>

                <div class="relative">
                    <div class="flex justify-end items-center">
                        <ArrowsPointingOutIcon class="h-8 w-8 text-gray-600 cursor-pointer p-0.5 opacity-10 hover:scale-150 hover:opacity-100 duration-150" v-if="!expand" @click="expand=true"/>
                        <ArrowsPointingInIcon class="h-8 w-8 text-gray-600 cursor-pointer p-0.5 opacity-10 hover:scale-150 hover:opacity-100 duration-150" v-else @click="() => {expand=false; setAppointmentRange();}"/>
                    </div>
                    <!-- Top button -->
                    <button @click="appointmentsRange -= 2" :disabled="appointmentsRange <= 2" v-show="!expand && appointments.length > 2">
                        <ArrowUpCircleIcon class="absolute top-8 right-0 z-10 w-8 h-8 cursor-pointer text-gray-400 hover:text-gray-600"/>
                    </button>

                    <!-- Your existing list -->
                    <ol role="list" class="overflow-hidden" :class="[expand ? '' : 'h-52']" ref="container" @wheel="handleScroll">
                        <li v-for="(appointment, appointmentIdx) in appointments" :key="appointmentIdx" :id="'appointment-'+appointmentIdx" :class="[appointmentIdx !== appointments.length - 1 ? 'pb-10' : '', 'relative']">

                            <div
                                v-if="appointmentIdx !== appointments.length - 1"
                                class="absolute left-4 top-4 -ml-px mt-0.5 h-full w-0.5"
                                :class="getStatusColor(appointment.status)"
                                aria-hidden="true" />

                            <div class="group relative flex items-start">

                                <AppointmentType v-if="appointment.status !== 'booked'">
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-green-500 group-hover:bg-green-600" v-if="appointment.status === 'completed'">
                                        <CheckIcon class="h-5 w-5 text-white" aria-hidden="true" />
                                    </div>
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-orange-500 group-hover:bg-orange-600" v-else-if="appointment.status === 'missed'">
                                        <ExclamationCircleIcon class="h-5 w-5 text-white" aria-hidden="true" />
                                    </div>
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full bg-red-500 group-hover:bg-red-600" v-else-if="appointment.status === 'cancelled'">
                                        <XCircleIcon class="h-5 w-5 text-white" aria-hidden="true" />
                                    </div>
                                </AppointmentType>

                                <AppointmentType v-else-if="appointment.selected">
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-indigo-600 bg-white" v-if="appointment.type === 'ongoing'">
                                        <div class="h-2.5 w-2.5 rounded-full bg-indigo-600 blinking-dot" />
                                    </div>
                                    <div class="relative z-10 flex h-8 w-8 items-center justify-center rounded-full border-2 border-gray-300 bg-white" v-else>
                                        <div class="flex">
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
                                        <div class="flex justify-between items-center">
                                            <div
                                                @click="openCardModal(appointment)"
                                                class="text-sm font-medium cursor-pointer" :class="{'text-indigo-600' : appointment.type === 'ongoing'}">
                                                {{ appointment.name }}
                                            </div>
                                            <div class="ms-2">
                                                <StatusMenu :appointment="appointment" />
                                            </div>
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ dateTimeToTime(appointment.start_time) }} - {{ dateTimeToTime(appointment.end_time) }}</span>
                                </div>
                            </div>
                        </li>
                    </ol>
                    <!-- Bottom button -->
                    <button @click="appointmentsRange += 2" :disabled="appointmentsRange > (appointments.length-2)" v-show="!expand && appointments.length > 2">
                        <ArrowDownCircleIcon class="absolute bottom-8 right-0 z-10 w-8 h-8 cursor-pointer text-gray-400 hover:text-gray-600" />
                    </button>
                </div>
            </main-frame>
        </wide-frame>
        <!-- modals -->
        <Card :appointment="selectedAppointment ?? null" v-model="openCard"/>
        <PhoneAction :appointment="heroAppointment" v-model="openPhoneAction"/>
        <!-- modals -->
    </div>
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
input[type=range]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    width: 20px;
    height: 20px;
    background: #f60202;
    cursor: pointer;
    border-radius: 50%;
    margin-top: -5px;
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

.blink {
    animation: blink 2s infinite;
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
