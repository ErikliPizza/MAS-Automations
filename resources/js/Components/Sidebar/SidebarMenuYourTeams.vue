<script setup>
import {Link} from "@inertiajs/vue3";

</script>

<template>
    <Link @click="$emit('closeSidebar')" href="/users" class="text-xs font-semibold leading-6 text-gray-400">Your teams</Link>
</template>

<style scoped>

</style>
