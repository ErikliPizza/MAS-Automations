<script setup>
import { ref } from 'vue';
import {meHavePosts} from "@/Services/postScrollCleaner.js";
import { useFlashMessages } from "@/Services/useFlashMessages.js";
import {useContact} from "@/Composables/useContact.vue";
import {useWhatsapp} from "@/Composables/useWhatsapp.vue";
import {useMailto} from "@/Composables/useMailto.vue";
meHavePosts();
useFlashMessages(); // Initialize flash messages functionality

const { callNumber, addContact } = useContact();
const { sendWith } = useWhatsapp();
const { mailto } = useMailto();
const drawer = ref(null);
const openUserList = ref(null);
</script>

<template>
    <v-layout class="rounded rounded-md">
        <v-navigation-drawer
            location="left"
            v-model="drawer"
        >
            <v-list-item
                prepend-avatar="https://randomuser.me/api/portraits/men/78.jpg"
                title="John Leider"
            ></v-list-item>

            <v-divider></v-divider>

            <v-list v-model:opened="openUserList" density="compact" nav>
                <v-list-item prepend-icon="mdi-view-dashboard" title="Home" value="home" @click="$inertia.get(route('home'))" :active="route().current('home')"></v-list-item>
                <v-list-item prepend-icon="mdi-forum" title="About" value="about"></v-list-item>

                <v-list-subheader>Users</v-list-subheader>
                <v-list-item v-for="item in $page.props.team.users" :key="item.id"
                             @click="$inertia.get(`${route('users.index')}/${item.id}`)"
                             :prepend-avatar="item.gravatar"
                             :title="item.name"
                             :subtitle="item.email"
                >
                    <template v-slot:append>
                        <v-speed-dial
                            location="right center"
                            transition="fade-transition"
                        >
                            <template v-slot:activator="{ props: activatorProps }">
                                <v-btn
                                    color="grey-lighten-1"
                                    icon="mdi-dots-horizontal"
                                    variant="text"
                                    v-bind="activatorProps"
                                ></v-btn>
                            </template>

                            <v-btn key="1" icon="mdi-phone" color="grey-darken-3" @click="callNumber(item.phone)"/>
                            <v-btn key="2" icon="mdi-whatsapp" :href="sendWith(item.phone)" color="green-lighten-1"/>
                            <v-btn key="3" icon="mdi-email" color="blue-lighten-1" :href="mailto(item.email)"/>
                        </v-speed-dial>
                    </template>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar :elevation="2">
            <template v-slot:prepend>
                <v-app-bar-nav-icon
                    @click.stop="drawer = !drawer"
                ></v-app-bar-nav-icon>
            </template>
            <v-app-bar-title>Application Bar</v-app-bar-title>
        </v-app-bar>

        <v-navigation-drawer location="right">
            <v-list>
                <v-list-item title="Drawer right"></v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-main class="d-flex align-center justify-center" style="min-height: 300px;">
            <slot />
        </v-main>

        <v-bottom-navigation grow class="hidden-md-and-up">
            <v-btn value="recent">
                <v-icon>mdi-history</v-icon>

                <span>Recent</span>
            </v-btn>

            <v-btn value="favorites">
                <v-icon>mdi-heart</v-icon>

                <span>Favorites</span>
            </v-btn>

            <v-btn value="nearby">
                <v-icon>mdi-map-marker</v-icon>

                <span>Nearby</span>
            </v-btn>
        </v-bottom-navigation>
    </v-layout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
