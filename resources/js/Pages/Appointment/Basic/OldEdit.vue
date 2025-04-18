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


</script>

<template>
    <div>
        <main-frame>
            <p class="bg-red-500 font-semibold">Status: {{ appointment.status }}</p>

            <div class="mt-4 flex justify-between space-x-2">
                <div>
                    <VueDatePicker
                        locale="tr"
                        v-model="form.day"
                        @update:model-value="handleDate"
                        :is-24="true"
                        :enable-time-picker="false"
                        ignore-time-validation
                    />
                    <InputError class="mt-2" :message="form.errors.day" />
                </div>
                <StatusDescription v-model="form.status" class="z-50"/>
            </div>

            <div class="mt-4">
                <div class="flex justify-center items-center space-x-2 mb-4">
                    <HeadlessList :list="services" v-model="form.service_id"/>
                </div>
            </div>
        </main-frame>

        <div class="relative mx-auto max-w-4xl px-4 sm:px-6 xl:px-8 bg-white mt-16">
            <InputError class="mt-2" :message="form.errors.start_time" />
            <InputError class="mt-2" :message="form.errors.end_time" />

            <div class="isolate grid grid-cols-4 gap-px rounded-lg bg-white p-1 text-sm shadow ring-1 ring-gray-200 sm:grid-cols-8 lg:grid-cols-12">

                <button v-for="slot in timeSlots" :key="slot.display" type="button" @click="handleSlotClick(slot)" @dblclick="handleSlotDoubleClick(slot)"
                        :class="[
                  'py-3 px-1 hover:bg-gray-100 focus:z-10 relative bg-white text-gray-900 border border-transparent rounded-lg',
                  isInRange(slot) ? 'line-through' : '',
                  slot.selectable ? '' : 'cursor-help',
                ]">

                    <time class="mx-auto flex h-10 w-10 items-center justify-center rounded-full relative"
                          :class="{
    'bg-indigo-600 font-semibold text-white': isSelected(slot) === 'start',
    'bg-green-600 font-semibold text-white': isSelected(slot) === 'end',
    'bg-green-100 text-gray-500': slot.exclusiveEnd && isSelected(slot) !== 'end' && slot.selectable,
    'bg-indigo-100 text-gray-500': slot.exclusiveStart && isSelected(slot) !== 'start' && slot.selectable,
    'bg-gray-200 text-gray-500 line-through': !slot.selectable,
    'border-2 border-white border-dashed': (slot.exclusiveEnd || slot.exclusiveStart) && isSelected(slot) !== 'end' && isSelected(slot) !== 'start' && slot.selectable,
  }"
                    >
                        {{ slot.display }}
                        <template v-if="true">
                            <!-- Condition for a single image -->
                            <template v-if="isSelected(slot) && slot.images.length === 0" class="absolute inset-0">
                                <span class="absolute top-0 block h-2 w-2 rounded-full ring-2 ring-white bg-black"/>
                            </template>



                            <!-- Condition for a single image -->
                            <template v-if="slot.images && slot.images.length === 1 && !slot.exclusiveStart && !slot.exclusiveEnd" class="absolute inset-0">
                                <span class="absolute top-0 block h-2 w-2 rounded-full ring-2 ring-white" :class="clrs[slot.images[0]]"/>
                            </template>

                            <!-- Exclusive End Condition -->
                            <template v-else-if="slot.exclusiveEnd && !slot.exclusiveStart">
                                <span class="absolute left-1 top-0 block h-2 w-2 rounded-full ring-2 ring-white bg-black" v-if="isSelected(slot)"/>
                                <span class="absolute right-1 top-0 block h-2 w-2 rounded-full ring-2 ring-white" :class="clrs[slot.images[0]]"/>
                            </template>

                            <!-- Exclusive Start Condition -->
                            <template v-else-if="slot.exclusiveStart && !slot.exclusiveEnd">
                                <span class="absolute right-1 top-0 block h-2 w-2 rounded-full ring-2 ring-white bg-black" v-if="isSelected(slot)"/>
                                <span class="absolute left-1 top-0 block h-2 w-2 rounded-full ring-2 ring-white" :class="clrs[slot.images[0]]"/>
                            </template>

                            <!-- Conditions for two images -->
                            <template v-else-if="slot.images && slot.images.length >= 2">
                                <span class="absolute left-1 top-0 block h-2 w-2 rounded-full ring-2 ring-white" :class="clrs[slot.images[0]]"/>
                                <span class="absolute right-1 top-0 block h-2 w-2 rounded-full ring-2 ring-white" :class="clrs[slot.images[1]]"/>
                            </template>
                        </template>
                    </time>
                </button>
            </div>
        </div>

        <main-frame>
            <div class="mt-4">
                <InputLabel for="name" value="Name *" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    :placeholder="appointment.name ?? 'John Doe'"
                    required
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4" ref="scrollTarget">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    :placeholder="appointment.email ?? 'example@mail.com'"
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
                    :placeholder="appointment.phone ?? '05431122333'"
                    autocomplete="phone"
                />

                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <div class="mt-4">
                <div>
                    <label for="price" class="block text-sm font-medium leading-6 text-gray-900">Price</label>
                    <div class="relative mt-2 rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">₺</span>
                        </div>
                        <input type="text" name="price" id="price" v-model="form.price" class="block w-full rounded-md border-0 py-1.5 pl-7 pr-12 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" :placeholder="appointment.price ?? '125.00'" aria-describedby="price-currency" />
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-gray-500 sm:text-sm" id="price-currency">TL</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="notes" value="Notes " />

                <textarea v-model="form.notes" rows="3" name="notes" id="notes" class="block w-full resize-none border-0 border-b border-transparent p-0 pb-2 text-gray-900 placeholder:text-gray-400 focus:border-indigo-600 focus:ring-0 sm:text-sm sm:leading-6" placeholder="Add a note here..." />

                <InputError class="mt-2" :message="form.errors.notes" />
            </div>

            <fieldset v-show="(form.email || form.phone) && form.status === 'booked'">
                <legend class="text-sm font-semibold leading-6 text-gray-900">Notify user via:</legend>
                <div class="mt-6 space-y-6">
                    <div class="relative flex gap-x-3" v-show="form.phone">
                        <div class="flex h-6 items-center">
                            <input v-model="form.send_message" id="comments" name="comments" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                        </div>
                        <div class="text-sm leading-6">
                            <label for="comments" class="font-medium text-gray-900">Message</label>
                            <p class="text-gray-500">Get notified via WhatsApp</p>
                        </div>
                    </div>
                    <div class="relative flex gap-x-3" v-show="form.email">
                        <div class="flex h-6 items-center">
                            <input v-model="form.send_email" id="candidates" name="candidates" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600" />
                        </div>
                        <div class="text-sm leading-6">
                            <label for="candidates" class="font-medium text-gray-900">E-mail</label>
                            <p class="text-gray-500">Get notified via e-mail</p>
                        </div>
                    </div>
                </div>
            </fieldset>
            <primary-button @click="createAppointment">Create</primary-button>
        </main-frame>
    </div>
</template>

<style scoped>

</style>
