<script setup>
import {EnvelopeIcon} from "@heroicons/vue/20/solid/index.js";
</script>

<template>
    <div>
        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
        <div class="relative rounded-md shadow-sm">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <EnvelopeIcon class="h-5 w-5 text-gray-400" aria-hidden="true" />
            </div>
            <input type="email" name="email" id="email" class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" placeholder="you@example.com" />
        </div>
    </div>
</template>

<style scoped>

</style>
