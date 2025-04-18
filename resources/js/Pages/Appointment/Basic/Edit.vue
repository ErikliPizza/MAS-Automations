<script setup>
import {computed, onMounted, ref, watch} from "vue";
import MainFrame from "@/Components/Frames/MainFrame.vue";
import HeadlessList from "@/Components/HeadlessList.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import {router, useForm} from "@inertiajs/vue3";
import { useTimePicker } from "@/Composables/useTimePicker.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import Checkbox from "@/Components/Checkbox.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import StatusDescription from "@/Components/StatusDescription.vue";
import ListDescription from "@/Components/ListDescription.vue";
import Toggle from "@/Components/Toggle.vue";
import EditAppointmentStepper from "@/Pages/Appointment/Basic/Partials/Edit/EditAppointmentStepper.vue";

const props = defineProps({
    services: {
        type: Array,
    },
    service: {
        type: Array
    },
    appointments: {
        type: Array
    },
    appointment: {
        type: Array
    }
})
const minutesToHHMMSS = (minutes) => {
    const hours = Math.floor(minutes / 60);
    const mins = minutes % 60;
    return `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:00`;
};
const dateTimeToMinutes = (dateTime) => {
    const timePart = dateTime.split(' ')[1];
    return timeToMinutes(timePart.substring(0, 5));
};
const timeToMinutes = (time) => {
    const [hours, minutes] = time.split(':').map(Number);
    return hours * 60 + minutes;
};

const appointments = computed(() => props.appointments);
const openingTime = computed(() => {
    // Get the opening time from props
    const openingTimeString = props.service.opening_time;
    const [openingHours, openingMinutes] = openingTimeString.split(':').map(Number);

    // Get the current time
    const now = new Date();
    const currentHours = now.getHours();
    const currentMinutes = now.getMinutes();

    // Function to round up to the next 15-minute multiple
    const roundUpToNext15Min = (hours, minutes) => {
        const minutesToNextQuarter = 15 - (minutes % 15);
        const totalMinutes = minutes + minutesToNextQuarter;
        if (totalMinutes >= 60) {
            hours += 1;
            minutes = totalMinutes % 60; // Reset minutes to 0 if it's exactly 60
        } else {
            minutes = totalMinutes;
        }
        return { hours, minutes };
    };

    let adjustedHours = openingHours;
    let adjustedMinutes = openingMinutes;

    // Check if current time is past the opening time
    if (currentHours > openingHours || (currentHours === openingHours && currentMinutes > openingMinutes)) {
        // Adjust to the current time
        adjustedHours = currentHours;
        adjustedMinutes = currentMinutes;
        // Then round up to the next 15 minutes if needed
        const roundedTime = roundUpToNext15Min(adjustedHours, adjustedMinutes);
        adjustedHours = roundedTime.hours;
        adjustedMinutes = roundedTime.minutes;
    }

    // Ensure hours and minutes are always two digits
    const formattedHours = adjustedHours.toString().padStart(2, '0');
    const formattedMinutes = adjustedMinutes.toString().padStart(2, '0');

    return { hours: parseInt(formattedHours), minutes: parseInt(formattedMinutes) };
});

const closingTime = computed(() => {
    const closingTimeString = props.service.closing_time;
    const [hours, minutes] = closingTimeString.split(':');
    return { hours: parseInt(hours), minutes: parseInt(minutes) };
});

const { selectedStartTime, selectedEndTime, timeSlots, selectTime, isSelected, isInRange } = useTimePicker(appointments, openingTime, closingTime);
onMounted(() => {
    if (window.location.search === '') {
        selectedStartTime.value = dateTimeToMinutes(props.appointment.start_time);
        selectedEndTime.value = dateTimeToMinutes(props.appointment.end_time);
    }
})
const form = useForm({
    name: props.appointment.name ?? null,
    email: props.appointment.email ?? null,
    phone: props.appointment.phone ?? null,
    service_id: props.service.id,
    start_time: selectedStartTime.value,
    end_time: selectedEndTime.value,
    status: props.appointment.status,
    notes: props.appointment.notes ?? null,
    price: props.appointment.price ?? null,
    day: ref(props.appointment.start_time),
    send_email: false,
    send_message: false
});

watch(selectedStartTime, (newValue) => {
    form.start_time = newValue ? minutesToHHMMSS(newValue) : '';
}, { immediate: true });
watch(selectedEndTime, (newValue) => {
    form.end_time = newValue ? minutesToHHMMSS(newValue) : '';
}, { immediate: true });

watch(() => form.service_id, (newValue, oldValue) => {
    if (newValue && newValue !== oldValue) {
        // Use Inertia.visit to make a partial reload to the current page
        // with the selected service as a query parameter
        router.reload({
            only: ['service', 'appointments'],
            data: {
                'service': newValue
            },
            replace: true
        })
        refreshTimePicker();
    }
});


// Parse the URL query parameters
const queryParams = new URLSearchParams(window.location.search);

// Check if the 'day' query parameter exists
if (queryParams.has('day')) {
    // Get the value of the 'day' query parameter and parse it into a Date object
    const dayQueryParam = queryParams.get('day');
    const dayDate = new Date(dayQueryParam);

    // Check if the parsed date is valid
    if (!isNaN(dayDate.getTime())) {
        // Set the 'date' variable to the parsed date
        form.day = dayDate;
    } else {
        console.error('Invalid date format in query parameter "day"');
    }
}



// Method to handle slot click, differentiating between selectable and displaying info
const handleSlotClick = (slot) => {
    console.log(slot);
    if (slot.selectable) {
        selectTime(slot);
    } else {
        // Display modal or tooltip here. For simplicity, using alert
        showAppointment(slot);
    }
}

// Method to handle slot click, differentiating between selectable and displaying info
const handleSlotDoubleClick = (slot) => {
    if(selectedStartTime.value == null) {
        showAppointment(slot);
    }
}

const showAppointment = (slot) => {
    if (slot.inUseInfos) {
        slot.inUseInfos.forEach(item => {
            // Construct the message to be alerted
            let message = `Name: ${item.name}\nPhone: ${item.phone}\nType: ${item.type}`;

            // Alert the message
            alert(message);
        });
    }
}
const handleDate = (modelData) => {
    router.reload({
        only: ['service', 'appointments'],
        data: {
            'day': modelData.toISOString().split('T')[0]
        },
        replace: true
    })
    refreshTimePicker();
}

const refreshTimePicker = () => {
    selectedStartTime.value = null;
    selectedEndTime.value = null;
}

const clrs = [
    "bg-red-200", "bg-red-300", "bg-red-400", "bg-red-600", "bg-red-700", "bg-red-800",
    "bg-orange-200", "bg-orange-300", "bg-orange-400", "bg-orange-600", "bg-orange-700", "bg-orange-800",
    "bg-yellow-200", "bg-yellow-300", "bg-yellow-400", "bg-yellow-600", "bg-yellow-700", "bg-yellow-800",
    "bg-lime-200", "bg-lime-300", "bg-lime-400", "bg-lime-600", "bg-lime-700", "bg-lime-800",
    "bg-emerald-200", "bg-emerald-300", "bg-emerald-400", "bg-emerald-600", "bg-emerald-700", "bg-emerald-800",
    "bg-teal-200", "bg-teal-300", "bg-teal-400", "bg-teal-600", "bg-teal-700", "bg-teal-800",
    "bg-cyan-200", "bg-cyan-300", "bg-cyan-400", "bg-cyan-600", "bg-cyan-700", "bg-cyan-800",
    "bg-blue-200", "bg-blue-300", "bg-blue-400", "bg-blue-600", "bg-blue-700", "bg-blue-800",
    "bg-violet-200", "bg-violet-300", "bg-violet-400", "bg-violet-600", "bg-violet-700", "bg-violet-800",
    "bg-purple-200", "bg-purple-300", "bg-purple-400", "bg-purple-600", "bg-purple-700", "bg-purple-800",
    "bg-pink-200", "bg-pink-300", "bg-pink-400", "bg-pink-600", "bg-pink-700", "bg-pink-800",
    "bg-fuchsia-200", "bg-fuchsia-300", "bg-fuchsia-400", "bg-fuchsia-600", "bg-fuchsia-700", "bg-fuchsia-800",
    "bg-red-500", "bg-orange-500", "bg-yellow-500", "bg-lime-500", "bg-emerald-500", "bg-teal-500",
    "bg-cyan-500", "bg-blue-500", "bg-violet-500", "bg-purple-500", "bg-pink-500", "bg-fuchsia-500"
];

const createAppointment = () => {
    form.put(route('appointments.update', { appointment: props.appointment.id }),{
        preserveState: (page) => Object.keys(page.props.errors).length,
        replace: true
    });
}

// USE APPOINTMENT.SERVICE.X INSTEAD OF SERVICE AS ADDITIONAL PROP!
</script>

<template>
    <div>
        <EditAppointmentStepper :appointments="appointments" :appointment="appointment" :services="services" :service="service" />
    </div>
</template>

<style scoped>

</style>
