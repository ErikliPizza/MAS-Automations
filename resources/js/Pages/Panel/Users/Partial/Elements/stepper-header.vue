<script setup>
import {defineProps} from 'vue';

const props = defineProps({
    steps: {
        type: Array,
        required: true,
    },
    stepErrors: {
        type: Object,
        required: true,
    }
});
</script>

<template>
    <v-stepper-header>
        <template v-for="(step, index) in steps" :key="index">
            <v-stepper-item :rules="[() => !stepErrors[step.name]]" :value="index">
                <template v-slot:title> {{ step.title }}</template>
            </v-stepper-item>
            <v-divider v-if="index < steps.length - 1"></v-divider>
        </template>
    </v-stepper-header>
</template>
