<script setup>
import MainFrame from "@/Components/Frames/MainFrame.vue";
import HeadlessList from "@/Components/HeadlessList.vue";
import {onMounted, watch} from "vue";
import {router} from "@inertiajs/vue3";
import {useQueryParam} from "@/Services/useQueryParam.js";

const props = defineProps({
    previous: {
        type: Array
    },
    ongoing: {
        type: Array
    },
    next: {
        type: Array
    },
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
})
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


</script>

<template>
    {{ service.id }}
    <main-frame>
        <div class="mt-4">
            <div class="flex justify-center items-center mb-4 space-x-2">
                <HeadlessList :list="services" v-model="service.id"/>
            </div>
        </div>
        <div class="flex justify-center items-center space-x-6">
            <div class="text-gray-400">
                <span>previous appointment</span>
                <div v-if="previous">
                    {{ previous.name }}
                </div>
                <div v-else>
                    nothing found
                </div>
            </div>
            <div class="text-lime-400">
                <span>ongoing appointment</span>
                <div v-if="ongoing">
                    {{ ongoing.name }}
                </div>
                <div v-else>
                    nothing found
                </div>
            </div>
            <div class="text-green-600">
                <span>next appointment</span>
                <div v-if="next">
                    {{ next.name }}
                </div>
                <div v-else>
                    nothing found
                </div>
            </div>
        </div>
        <div v-for="appointment in appointments">
            {{ appointment.name }} - {{ appointment.start_time }}
        </div>
    </main-frame>
</template>

<style scoped>

</style>
