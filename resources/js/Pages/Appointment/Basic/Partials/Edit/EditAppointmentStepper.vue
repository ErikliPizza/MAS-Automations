<template>
    <main-frame>
        <div class="w-full max-w-md lg:px-2 py-16 sm:px-0">
            <TabGroup>
                <TabList class="flex space-x-2 rounded-xl bg-blue-900/20 p-1 overflow-x-auto no-scrollbar" v-dragscroll.x>


                    <Tab
                        class="p-2"
                        as="template"
                        :key="'View'"
                        v-slot="{ selected }"
                    >
                        <button
                            :class="[
              'w-full rounded-lg py-2.5 text-sm font-medium leading-5',
              'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
              selected
                ? 'bg-white text-blue-700 shadow'
                : 'text-blue-100 hover:bg-white/[0.12] hover:text-white',
            ]"
                        >
                            View
                        </button>
                    </Tab>
                    <Tab
                        class="p-2"
                        as="template"
                        :key="'Service'"
                        v-slot="{ selected }"
                    >
                        <button
                            :class="[
              'w-full rounded-lg py-2.5 text-sm font-medium leading-5',
              'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
              selected
                ? 'bg-white text-blue-700 shadow'
                : 'text-blue-100 hover:bg-white/[0.12] hover:text-white',
            ]"
                        >
                            Service
                        </button>
                    </Tab>
                    <Tab
                        class="p-2"
                        as="template"
                        :key="'Time'"
                        v-slot="{ selected }"
                    >
                        <button
                            :class="[
              'w-full rounded-lg py-2.5 text-sm font-medium leading-5',
              'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
              selected
                ? 'bg-white text-blue-700 shadow'
                : 'text-blue-100 hover:bg-white/[0.12] hover:text-white',
                form.errors.start_time || form.errors.end_time ? 'text-red-400 blink' : ''

            ]"
                        >
                            Time
                        </button>
                    </Tab>
                    <Tab
                        class="p-2"
                        as="template"
                        :key="'Information'"
                        v-slot="{ selected }"
                    >
                        <button
                            :class="[
              'w-full rounded-lg py-2.5 text-sm font-medium leading-5',
              'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
              selected
                ? 'bg-white text-blue-700 shadow'
                : 'text-blue-100 hover:bg-white/[0.12] hover:text-white',
            ]"
                        >
                            Information
                        </button>
                    </Tab>
                    <Tab
                        class="p-2"
                        as="template"
                        :key="'Preview'"
                        v-slot="{ selected }"
                    >
                        <button
                            :class="[
              'w-full rounded-lg py-2.5 text-sm font-medium leading-5',
              'ring-white/60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2',
              selected
                ? 'bg-white text-blue-700 shadow'
                : 'text-blue-100 hover:bg-white/[0.12] hover:text-white',
            ]"
                        >
                            Preview
                        </button>
                    </Tab>
                </TabList>

                <TabPanels class="mt-2">
                    <TabPanel>
                        <div class="bg-white shadow-md rounded-lg">
                            <div class="p-4">
                                <div class="flex justify-between">
                                    <h2 class="text-xl font-semibold text-gray-800 mb-2">{{appointment.name}}</h2>
                                    <StatusDescription v-model="form.status" class="z-50"/>
                                </div>
                                <div class="mb-4" v-show="appointment.email">
                                    <span class="text-sm text-gray-900">
                                        Email:
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ appointment.email }}
                                    </span>
                                </div>
                                <div class="mb-4" v-show="appointment.phone">
                                    <span class="text-sm text-gray-900">
                                        Phone:
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ appointment.phone }}
                                    </span>
                                </div>
                                <div class="mb-4">
                                    <span class="text-sm text-gray-900">
                                        Time:
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ dateTimeToTime(appointment.start_time) }} - {{ dateTimeToTime(appointment.end_time) }}
                                    </span>
                                </div>
                                <div class="mb-4">
                                    <span class="text-sm text-gray-900">
                                        Status:
                                    </span>
                                    <span class="text-sm" :class="getStatusColorAsText(appointment.status)">
                                        {{ appointment.status }}
                                    </span>
                                </div>
                                <div class="mb-4" v-show="appointment.notes">
                                    <span class="text-sm text-gray-900">
                                        Note:
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ appointment.notes }}
                                    </span>
                                </div>
                                <div class="mb-4" v-show="appointment.price">
                                    <span class="text-sm text-gray-900">
                                        Price:
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ appointment.price }}₺
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-sm text-gray-900">
                                        Service:
                                    </span>
                                    <span class="text-sm text-gray-600 capitalize">
                                        {{ appointment.service.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <div class="p-4">
                            <div class="flex justify-center items-center space-x-2 mb-4">
                                <HeadlessList :list="services" v-model="form.service_id"/>
                            </div>
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <div class="bg-white shadow-md rounded-lg">
                            <div class="p-4">
                                <VueDatePicker
                                    locale="tr"
                                    v-model="form.day"
                                    @update:model-value="handleDate"
                                    :is-24="true"
                                    :enable-time-picker="false"
                                    ignore-time-validation
                                />
                            </div>

                            <div class="relative mx-auto max-w-4xl px-4 bg-white">
                                <InputError class="mt-2" :message="form.errors.start_time" />
                                <InputError class="mt-2" :message="form.errors.end_time" />

                                <div class="isolate grid grid-cols-4 gap-px text-sm sm:grid-cols-8 lg:grid-cols-6">

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
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <div class="bg-white shadow-md rounded-lg overflow-hidden">
                            <div class="p-4">
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
                            </div>
                        </div>
                    </TabPanel>
                    <TabPanel>
                        <div class="bg-white shadow-md rounded-lg overflow-hidden">
                            <div class="p-4">
                                <ol class="mb-4 list-disc px-8" v-show="Object.keys($page.props.errors).length > 0">
                                    <li class="list-none text-center text-xs text-gray-600">
                                        your request did not proceed due to these reasons:
                                    </li>
                                    <li class="text-red-400 text-sm" v-for="error in form.errors" :key="error">
                                        {{ error }}
                                    </li>
                                </ol>
                                <div class="flex justify-between">
                                    <h2 class="text-xl font-semibold text-gray-800 mb-2" v-if="form.name">{{form.name}}</h2>
                                    <h4 class="text-xl font-semibold text-red-400 underline mb-2" v-else>Name field must be filled</h4>
                                </div>
                                <div class="mb-4" v-show="form.email">
                                    <span class="text-sm text-gray-900">
                                        Email:
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ form.email }}
                                    </span>
                                </div>
                                <div class="mb-4" v-show="form.phone">
                                    <span class="text-sm text-gray-900">
                                        Phone:
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ form.phone }}
                                    </span>
                                </div>
                                <div class="mb-4">
                                    <span class="text-sm text-gray-900">
                                        Time:
                                    </span>
                                    <span class="text-sm text-gray-600" v-if="form.start_time && form.end_time">
                                        {{ timestampToHHMM(form.start_time) }} - {{ timestampToHHMM(form.end_time) }}
                                    </span>
                                    <span class="text-sm text-red-400 underline" v-else>
                                        Pick an appointment date from the date tab.
                                    </span>
                                </div>
                                <div class="mb-4">
                                    <span class="text-sm text-gray-900">
                                        Status:
                                    </span>
                                    <span class="text-sm" :class="getStatusColorAsText(form.status)">
                                        {{ form.status }}
                                    </span>
                                </div>
                                <div class="mb-4" v-show="form.notes">
                                    <span class="text-sm text-gray-900">
                                        Note:
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ form.notes }}
                                    </span>
                                </div>
                                <div class="mb-4" v-show="form.price">
                                    <span class="text-sm text-gray-900">
                                        Price:
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ form.price }}₺
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <span class="text-sm text-gray-900">
                                        Service:
                                    </span>
                                    <span class="text-sm text-gray-600 capitalize">
                                        {{ service.name }}
                                    </span>
                                </div>
                                <fieldset v-show="(form.email || form.phone)">
                                    <legend class="text-sm font-semibold leading-6 text-gray-900">Notify user about this change via:</legend>
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
                                <primary-button @click="createAppointment" class="mt-2">Create</primary-button>
                            </div>
                        </div>
                    </TabPanel>
                </TabPanels>
            </TabGroup>
        </div>
    </main-frame>

</template>

<script setup>
import {computed, onMounted, ref, watch} from 'vue'
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue'
import MainFrame from "@/Components/Frames/MainFrame.vue";
import {dateTimeToTime, timestampToHHMM} from "@/Services/useTimeHelpers.js";
import {router, useForm} from "@inertiajs/vue3";
import {useTimePicker} from "@/Composables/useTimePicker.vue";
import HeadlessList from "@/Components/HeadlessList.vue";
import VueDatePicker from "@vuepic/vue-datepicker";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import StatusMenu from "@/Pages/Appointment/Basic/Partials/Index/Elements/StatusMenu.vue";
import StatusDescription from "@/Components/StatusDescription.vue";

const props = defineProps({
    appointment: {
        type: Object,
        required: true
    },
    appointments: {
        type: Object,
        required: true
    },
    service: {
        type: Object,
        required: true
    },
    services: {
        type: Object,
        required: true
    }
})

const categories = ref({
    View: [],
    Service:[],
    Time:[],
    Information:[],
    Preview: []
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
    const openingTimeString = props.service.opening_time;
    const [hours, minutes] = openingTimeString.split(':');
    return { hours: parseInt(hours), minutes: parseInt(minutes) };
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
    name: props.appointment.name,
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

const getStatusColorAsText = (status) => {
    switch (status) {
        case 'completed':
            return 'text-green-600'; // purple for completed
        case 'missed':
            return 'text-orange-600'; // Orange for missed
        case 'cancelled':
            return 'text-red-600'; // Red for cancelled
        default:
            return 'text-gray-600'; // A default color if none of the statuses match
    }
}
</script>

<style scoped>
/* Define the blinking animation */

/* Define the blinking animation */
@keyframes blink {
    0%, 100% {
        scale: 100%;
        opacity: 1;
    }
    50% {
        scale: 80%;
        opacity: 0.8;
    }
}



.blink {
    animation: blink 2s infinite;
}

</style>
