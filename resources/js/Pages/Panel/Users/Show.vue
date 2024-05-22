<script setup>
const props = defineProps({
    user: {
        type: Object,
        required: true
    }
});

</script>

<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <v-card>
                    <v-card-item
                        :title="user.name + '' + user.surname"
                        :subtitle="user.email"
                    >
                        <template v-slot:prepend>
                            <v-avatar size="x-large">
                                <v-img :src="user.gravatar" />
                            </v-avatar>
                        </template>
                        <template v-slot:append>
                            <v-avatar size="x-large">
                                <v-img :src="user.gravatar" />
                            </v-avatar>
                        </template>
                    </v-card-item>
                </v-card>
            </v-col>

        </v-row>

        <v-row>
            <v-col cols="12">

            </v-col>
        </v-row>
    </v-container>
</template>

<style scoped>

</style>
