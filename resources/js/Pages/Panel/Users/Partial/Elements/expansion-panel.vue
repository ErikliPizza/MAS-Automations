<script setup>
defineProps({
    color: { required: true },
    icon: { required: true },
    title: { required: true },
    subtitle: { required: false, default: null },
    count: { required: false, default: null }
});
</script>

<template>
    <v-expansion-panel hide-actions>
        <v-expansion-panel-title>
            <v-row
                align="center"
                class="spacer"
                no-gutters
            >
                <v-col
                    cols="4"
                    md="1"
                    sm="2"
                >
                    <v-icon
                        :color="color"
                        :icon="icon"
                    ></v-icon>
                </v-col>

                <v-col
                    class="hidden-xs-only text-left ms-2"
                    md="3"
                    sm="5"
                >
                    <strong>{{ title }}</strong>
                    <span class="text-grey" v-if="count">&nbsp;({{ count }})</span>
                </v-col>
                <v-col
                    v-if="subtitle"
                    class="text-medium-emphasis text-truncate hidden-sm-and-down text-capitalize"
                >
                    &mdash;
                    {{ subtitle }}
                </v-col>
            </v-row>
        </v-expansion-panel-title>

        <v-expansion-panel-text>
            <slot />
        </v-expansion-panel-text>
    </v-expansion-panel>
</template>
