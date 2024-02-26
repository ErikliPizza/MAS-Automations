<script setup>
import {Link} from "@inertiajs/vue3";
</script>

<template>
    <li v-for="item in $page.props.modules" :key="item.name">
        <Link @click="$emit('closeSidebar')" :href="'/module/'+item.endpoint" class="text-gray-700 hover:text-indigo-600 hover:bg-gray-50 group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
            <div v-html="item.icon" class="text-gray-400 group-hover:text-indigo-600 h-6 w-6 shrink-0" aria-hidden="true"></div>
            {{ item.name }}
        </Link>
    </li>
</template>

