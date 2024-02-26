<script setup>
import WideFrame from "@/Components/Frames/WideFrame.vue";
import { PhoneIcon, EnvelopeIcon } from "@heroicons/vue/24/solid/index.js";
import { useSmoothScroll } from "@/Composables/useSmoothScroll.vue";
const { scrollTarget } = useSmoothScroll();
defineProps({
    users: {
        required: true,
        type: Array
    }
})
</script>

<template>
    <WideFrame ref="scrollTarget">
        <ul role="list" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="user in users" :key="user.email" class="col-span-1 divide-y divide-gray-200 rounded-lg bg-white shadow">
                <Link :href="'/users/'+user.id" class="flex w-full items-center justify-between space-x-6 p-6 cursor-pointer">
                    <div class="flex-1 truncate">
                        <div class="flex items-center space-x-3">
                            <h3 class="truncate text-sm font-medium text-gray-900">{{ user.name }}</h3>
                            <span class="inline-flex flex-shrink-0 items-center rounded-full px-1.5 py-0.5 text-xs font-medium ring-1 ring-inset" :class="{'bg-green-50 text-green-700 ring-green-600/20': user.status === 'active', 'text-white bg-red-300 ring-red-900/20': user.status === 'inactive' }">{{ user.role }}</span>
                        </div>
                        <p class="mt-1 truncate text-sm text-gray-500">
                            <span v-for="(module, index) in user.modules">
                                <span v-if="(index+1) < user.modules.length">{{ module.name }}, </span>
                                <span v-else>{{ module.name }}</span>
                            </span>
                        </p>
                    </div>
                    <img class="h-10 w-10 flex-shrink-0 rounded-full bg-gray-300" :src="user.gravatar" alt="" />
                </Link>
                <div>
                    <div class="-mt-px flex divide-x divide-gray-200">
                        <div class="flex w-0 flex-1">
                            <a :href="`mailto:${user.email}`" class="relative -mr-px inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-bl-lg border border-transparent py-4 text-sm font-semibold text-gray-900">
                                <EnvelopeIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
                            </a>
                        </div>
                        <div class="-ml-px flex w-0 flex-1" v-show="user.phone">
                            <a :href="`tel:${user.phone}`" class="relative inline-flex w-0 flex-1 items-center justify-center gap-x-3 rounded-br-lg border border-transparent py-4 text-sm font-semibold text-gray-900">
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
