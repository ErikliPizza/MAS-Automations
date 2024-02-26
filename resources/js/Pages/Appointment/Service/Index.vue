<script setup>
import WideFrame from "@/Components/Frames/WideFrame.vue";
import { PhoneIcon, EnvelopeIcon } from "@heroicons/vue/24/solid/index.js";
import { useSmoothScroll } from "@/Composables/useSmoothScroll.vue";
const { scrollTarget } = useSmoothScroll();
defineProps({
    services: {
        required: true,
        type: Array
    }
})
</script>

<template>
    <WideFrame ref="scrollTarget">
        <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="service in services" :key="service.id" class="col-span-1 divide-y divide-gray-200 rounded-lg bg-white shadow">
                <Link :href="'/appointments/services/edit/'+service.id" class="flex w-full items-center justify-between space-x-6 p-6 cursor-pointer">
                    <div class="flex-1 truncate">
                        <div class="flex items-center space-x-3">
                            <h3 class="truncate text-sm font-medium text-gray-900">{{ service.name }}</h3>
                            <span class="inline-flex flex-shrink-0 items-center rounded-full px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset" :class="{'bg-green-50 text-green-700 ring-green-600/20': service.status === 'active', 'text-white bg-red-300 ring-red-900/20': service.status === 'inactive' }">{{ service.status }}</span>
                        </div>
                        <p class="mt-1 truncate text-sm text-gray-500">
                            {{ service.opening_time }} - {{ service.closing_time }}
                        </p>
                    </div>
                    <img class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-300" :src="service.gravatar" />
                </Link>
                <div>
                    <div class="-mt-px flex divide-x divide-gray-200">
                        <div class="flex w-0 flex-1" v-show="service.email">
                            <a :href="`mailto:${service.email}`" class="relative -mr-px inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-bl-lg border border-transparent py-4 text-sm font-semibold text-gray-900">
                                <EnvelopeIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            </a>
                        </div>
                        <div class="-ml-px flex w-0 flex-1" v-show="service.phone">
                            <a :href="`tel:${service.phone}`" class="relative inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-br-lg border border-transparent py-4 text-sm font-semibold text-gray-900">
                                <PhoneIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            </a>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </WideFrame>
</template>

<style scoped>

</style>
