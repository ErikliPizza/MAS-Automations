<script setup>
import useDateTranslator from "@/Services/useDateTranslator.js";
import Column from "@/Pages/Panel/Users/Partial/Frames/column.vue";
import ExpansionPanel from "@/Pages/Panel/Users/Partial/Elements/expansion-panel.vue";
import {computed, ref} from "vue";
import { useMailto } from "@/Composables/useMailto.vue";
import UseHandleNumberClick from "@/Composables/useHandleNumberClick.vue";
import { useClipboard } from "@/Composables/useClipboard.vue";

const { copyToClipboard } = useClipboard();

const { toHumanReadable } = useDateTranslator();
const { mailto } = useMailto();

const handleNumberDialog = ref(false);
const props = defineProps({
    user: { required: true },

});

function calculateMonths(startDate, endDate = new Date()) {
    const start = new Date(startDate);
    const end = endDate ? new Date(endDate) : new Date();

    const yearsDifference = end.getFullYear() - start.getFullYear();
    const monthsDifference = end.getMonth() - start.getMonth();
    return (yearsDifference * 12) + monthsDifference;
}

const monthsOfWork = computed(() => {
    return calculateMonths(props.user.start_date_of_work, props.user.end_date_of_work);
});

const additional = computed(() => {
    return props.user.email !== null || props.user.phone !== null || props.user.bank_account !== null || props.user.id_number;
});

const staff = computed(() => {
    return props.user.start_date_of_work !== null || props.user.end_date_of_work !== null || props.user.reason_of_leaving !== null || props.user.salary;
});

const authorized = computed(() => {
    return props.user.modules.length > 0
});
</script>

<template>
    <v-expansion-panels class="pa-2 mt-6" variant="popout">
        <v-expansion-panel hide-actions>
            <v-expansion-panel-title>
                <v-row
                    align="center"
                    class="spacer"
                    no-gutters
                >
                    <column align="center">


                        <v-badge :color="user.status === 'active' ? 'success' : 'error'" dot>
                            <v-tooltip
                                activator="parent"
                                location="end"
                            >
                                This user status is <strong :class="[user.status === 'active' ? 'text-success' : 'text-error']">{{ user.status }}</strong>
                            </v-tooltip>
                            <v-avatar
                                size="72px"
                            >
                                <v-img
                                    alt="Avatar"
                                    :src="user.gravatar"
                                ></v-img>
                            </v-avatar>
                        </v-badge>

                        <v-list-item
                            :title="user.name + ' ' + user.surname"
                        >
                            <v-list-item-subtitle>
                                {{ user.email }}
                            </v-list-item-subtitle>
                            <VDivider class="my-2" width="30" v-if="user.birthday"></VDivider>
                            <v-list-item-subtitle v-if="user.birthday">
                                <v-icon style="margin-bottom: 7px;" size="small">mdi-cake-variant</v-icon>
                                {{ toHumanReadable(user.birthday, 'fullDate') }}
                            </v-list-item-subtitle>
                        </v-list-item>
                    </column>

                    <v-col
                        v-if="user.title"
                        class="text-medium-emphasis text-truncate hidden-sm-and-down text-capitalize"
                    >
                        &mdash;
                        {{ user.title }}
                    </v-col>
                </v-row>
            </v-expansion-panel-title>

            <v-expansion-panel-text>
                <v-card-text>
                    This user is <span class="font-weight-bold"> created at {{ toHumanReadable(user.created_at, 'fullDate') }} </span>
                    and
                    <span v-if="user.status === 'active'"> serving to this tenant actively </span>
                    <span v-else> served to this tenant </span>
                    as
                    <span class="font-weight-bold"> {{ user.role }} </span>
                    with
                    <span class="font-weight-bold">{{ user.modules.length }} active modules.</span>
                </v-card-text>
            </v-expansion-panel-text>
        </v-expansion-panel>

        <expansion-panel v-if="additional" title="Additional" icon="mdi-information-variant-box-outline" color="blue-grey-darken-1">
            <v-list density="compact" dense>
                <v-list-item prepend-icon="mdi-email-fast" :subtitle="user.email" :href="mailto(user.email)"/>
                <v-list-item v-if="user.phone" prepend-icon="mdi-phone" @click="handleNumberDialog = true">
                    <v-list-item-subtitle>
                        <use-handle-number-click :user="user" v-model="handleNumberDialog"/>
                    </v-list-item-subtitle>
                </v-list-item>
                <v-list-item v-if="user.bank_account" prepend-icon="mdi-bank-check" :subtitle="user.bank_account" @click="copyToClipboard(user.bank_account, 'IBAN')"/>
                <v-list-item v-if="user.id_number" prepend-icon="mdi-card-account-details-outline" :subtitle="user.id_number" class="font-italic" @click="copyToClipboard(user.id_number, 'ID Number')"/>
            </v-list>
        </expansion-panel>

        <expansion-panel v-if="staff" title="Staff" icon="mdi-briefcase-arrow-left-right" color="blue-grey-darken-1">
            <v-list density="compact" dense>
                <v-list-item v-if="user.start_date_of_work" prepend-icon="mdi-calendar-import" :subtitle="'Start Date: ' + toHumanReadable(user.start_date_of_work, 'fullDate')" />
                <v-list-item v-if="user.end_date_of_work" prepend-icon="mdi-calendar-export" :subtitle="'Dismissal Date: ' + toHumanReadable(user.end_date_of_work, 'fullDate')" />
                <v-list-item v-if="user.start_date_of_work || user.end_date_of_work">
                    <v-list-item-subtitle>
                        <VDivider class="my-2" width="30"></VDivider>
                    </v-list-item-subtitle>
                </v-list-item>
                <v-list-item v-if="user.start_date_of_work" prepend-icon="mdi-briefcase-clock-outline" :subtitle="'Months Worked: ' + monthsOfWork" />
                <v-list-item v-if="user.salary" prepend-icon="mdi-cash-multiple" :subtitle="user.salary" />
                <v-list-item v-if="user.salary && user.start_date_of_work" prepend-icon="mdi-account-cash" :subtitle="'Total ' + monthsOfWork*user.salary" />
            </v-list>
            <v-expansion-panel-text align="center" v-if="user.reason_of_leaving">
                <v-card-text>
                    <span class="text-medium-emphasis">reason of leaving</span>
                    <div>
                        <v-icon icon="mdi-format-quote-open"></v-icon>
                        &nbsp;
                        <span class="font-weight-bold">{{ user.reason_of_leaving }}</span>
                        &nbsp;
                        <v-icon icon="mdi-format-quote-close"></v-icon>
                    </div>
                </v-card-text>
            </v-expansion-panel-text>
        </expansion-panel>

        <expansion-panel v-if="authorized" title="Authorized" icon="mdi-view-module" color="blue-grey-darken-1" :count="user.modules.length" :subtitle="user.role">
            <v-card-text>
                <v-responsive class="overflow-y-auto" max-height="280">
                    <v-chip-group class="mt-3" column>
                        <v-chip
                            v-for="module in user.modules"
                            :key="module"
                            :text="module.name"
                        ></v-chip>
                    </v-chip-group>
                </v-responsive>
            </v-card-text>
        </expansion-panel>

    </v-expansion-panels>


</template>

<style scoped>
</style>
