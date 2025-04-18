<script setup>
import { useContact } from "@/Composables/useContact.vue";
import { useWhatsapp } from "@/Composables/useWhatsapp.vue";
import { useClipboard } from "@/Composables/useClipboard.vue";

import {useDisplay} from "vuetify";

const { mobile } = useDisplay();
const { callNumber, addContact } = useContact();
const { sendWith, shareWith } = useWhatsapp();
const { copyToClipboard } = useClipboard();

const props = defineProps({
    modelValue: { required: true },
    user: { required: true}
});
const emit = defineEmits(['update:modelValue']);

const updateValue = (value) => {
    emit('update:modelValue', value);
};

</script>

<template>
    <v-menu
        v-model="props.modelValue"
        :close-on-content-click="true"
        @update:model-value="updateValue"
        location="end"
    >
        <template v-slot:activator="{ props }">
            <span class="pe-2" v-bind="props">
                {{ user.phone }}
            </span>
        </template>

        <v-card min-width="300">
            <v-list>
                <v-list-item
                    :prepend-avatar="user.gravatar"
                    :subtitle="user.title"
                    :title="user.name + ' ' + user.surname"
                >
                    <template v-slot:append>
                        <v-btn
                            v-if="mobile"
                            icon="mdi-plus-box-multiple"
                            variant="text"
                            @click="addContact(user.name, user.number, user.email)"
                        ></v-btn>
                        <v-btn
                            v-else
                            icon="mdi-share-variant"
                            variant="text"
                            @click="shareWith(user.name + ' ' + user.surname + ' - ' + user.phone + (user.title ? ' (' + user.title + ')' : ''))"
                        ></v-btn>
                    </template>
                </v-list-item>
            </v-list>

            <v-divider></v-divider>

            <v-list>
                <v-list-item title="WhatsApp" subtitle="send message via" :href="sendWith(user.phone)" target="_blank">
                    <template v-slot:prepend>
                        <v-icon color="success" icon="mdi-whatsapp"></v-icon>
                    </template>
                </v-list-item>

                <v-list-item title="Call" subtitle="dial this number" @click="callNumber(user.phone)">
                    <template v-slot:prepend>
                        <v-icon color="blue-grey-darken-2" icon="mdi-phone"></v-icon>
                    </template>
                </v-list-item>
            </v-list>

            <v-card-actions>
                <v-spacer></v-spacer>

                <v-btn variant="text">
                    Cancel
                </v-btn>
                <v-btn
                    prepend-icon="mdi-content-copy"
                    color="primary"
                    variant="tonal"
                    @click="copyToClipboard(user.phone, user.name + ' ' + user.surname)"
                >
                </v-btn>
                <v-btn
                    v-if="mobile"
                    @click="shareWith(user.name + ' ' + user.surname + ' - ' + user.phone + (user.title ? ' (' + user.title + ')' : ''))"
                    prepend-icon="mdi-share-variant"
                    color="secondary"
                    variant="tonal"
                >
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-menu>
</template>
