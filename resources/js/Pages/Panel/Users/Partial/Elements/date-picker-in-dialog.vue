<script setup>
import {computed, ref} from "vue";
import {useDate} from 'vuetify'

const props = defineProps({
    modelValue: { required: true },
    label: { required: true, type: String },
    icon: { required: false, type: String, default: '' },
});

const adapter = useDate()
const modal = ref(false)

const formattedDate = computed(() => {
    if (props.modelValue) {
        return adapter.format(new Date(props.modelValue), 'fullDate');
    }
    return '';
});
const emit = defineEmits(['update:modelValue']);

const updateValue = (value) => {
    emit('update:modelValue', value);
};
const cancel = () => {
    emit('update:modelValue', null);
    modal.value = false;
}

const confirm = () => {
    modal.value = false;
}
// two-way model binding. Props are writable
</script>

<template>
    <v-text-field
        v-model="formattedDate"
        @click:prepend="modal = true"
        :label="label"
        :prepend-icon="icon"
        variant="solo-filled"
        density="compact"
        readonly
    >
        <v-dialog v-model="modal" activator="parent" width="auto" transition="dialog-bottom-transition">
            <v-card>
                <v-card-text class="pa-0">
                    <v-row justify="center">
                        <v-date-picker
                            width="340"
                            :title="label"
                            :model-value="props.modelValue"
                            @update:model-value="updateValue"
                            color="primary"
                        ></v-date-picker>
                    </v-row>
                </v-card-text>

                <v-divider></v-divider>

                <v-card-actions>
                    <v-spacer></v-spacer>

                    <v-btn
                        text="Clear"
                        variant="plain"
                        @click="cancel"
                    ></v-btn>

                    <v-btn
                        color="primary"
                        text="Save"
                        variant="tonal"
                        :disabled="!props.modelValue"
                        @click="confirm"
                    ></v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-text-field>
</template>
