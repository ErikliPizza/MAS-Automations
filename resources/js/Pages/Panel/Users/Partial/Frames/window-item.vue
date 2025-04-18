<script setup>

defineProps({
    title: {
        required: true,
        type: String
    },
    value: {
        required: true,
        type: Number
    }
});
</script>

<template>
    <v-stepper-window-item :value="value">
        <v-card :title="title" flat>
            <v-container>
                <slot/>
            </v-container>
        </v-card>
    </v-stepper-window-item>
</template>

<style scoped>

</style>
