<script setup>
import { Head } from '@inertiajs/vue3';

</script>

<template>
    <p class="text-center">this is home screen!</p>
    <p class="text-center text-gray-600 text-lg mt-8">Wellcome {{ $page.props.auth.user.name }}</p>
</template>

<style>

</style>
