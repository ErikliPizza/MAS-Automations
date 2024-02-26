<script setup>
import { ref } from 'vue'
import {
    Dialog,
    DialogPanel,
    TransitionChild,
    TransitionRoot
} from '@headlessui/vue'
import {
    Bars3Icon,
    HomeIcon,
    MinusCircleIcon
} from '@heroicons/vue/24/outline'

import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { useFlashMessages } from "@/Services/useFlashMessages.js";

import SidebarMenu from "@/Components/Sidebar/SidebarMenu.vue";
import SidebarMenuYourTeams from "@/Components/Sidebar/SidebarMenuYourTeams.vue";
import SidebarMenuTeams from "@/Components/Sidebar/SidebarMenuTeams.vue";

const pages = [
    { name: 'Projects', href: '#', current: false },
    { name: 'Project Nero', href: '#', current: true },
]
useFlashMessages(); // Initialize flash messages functionality

const sidebarOpen = ref(false)
</script>

<template>
    <div>
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative z-50 lg:hidden" @close="sidebarOpen = false">
                <!-- Background overlay transition -->
                <TransitionChild
                    as="template"
                    enter="transition-opacity ease-linear duration-300"
                    enter-from="opacity-0"
                    enter-to="opacity-100"
                    leave="transition-opacity ease-linear duration-300"
                    leave-from="opacity-100"
                    leave-to="opacity-0"
                >
                    <div class="fixed inset-0 bg-gray-900/80" />
                </TransitionChild>

                <div class="fixed inset-0 flex">
                    <!-- Sidebar transition -->
                    <TransitionChild
                        as="template"
                        enter="transition ease-in-out duration-300 transform"
                        enter-from="-translate-x-full"
                        enter-to="translate-x-0"
                        leave="transition ease-in-out duration-300 transform"
                        leave-from="translate-x-0"
                        leave-to="-translate-x-full"
                    >
                        <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">

                            <!-- Sidebar content transition -->
                            <TransitionChild
                                as="template"
                                enter="ease-in-out duration-300"
                                enter-from="opacity-0"
                                enter-to="opacity-100"
                                leave="ease-in-out duration-300"
                                leave-from="opacity-100"
                                leave-to="opacity-0"
                            >
                                <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-2">
                                    <div class="flex h-16 shrink-0 items-center justify-between">
                                        <ApplicationLogo class="h-8 w-auto"/>
                                        <MinusCircleIcon class="h-6 w-6 text-black" aria-hidden="true" @click="sidebarOpen = false"/>
                                    </div>
                                    <nav class="flex flex-1 flex-col">
                                        <ul role="list" class="flex flex-1 flex-col gap-y-7">
                                            <li>
                                                <SidebarMenu @closeSidebar="sidebarOpen = false"/>
                                            </li>
                                            <li>
                                                <SidebarMenuYourTeams @closeSidebar="sidebarOpen = false"/>
                                                <SidebarMenuTeams @closeSidebar="sidebarOpen = false"/>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </TransitionChild>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </Dialog>
        </TransitionRoot>


        <!-- Static sidebar for desktop -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <!-- Sidebar component, swap this element with another sidebar if you like -->
            <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6">
                <div class="flex h-16 shrink-0 items-center">
                    <ApplicationLogo class="h-8 w-auto"/>
                </div>
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <li>
                            <SidebarMenu @closeSidebar="sidebarOpen = false"/>
                        </li>
                        <li>
                            <SidebarMenuYourTeams @closeSidebar="sidebarOpen = false"/>
                            <SidebarMenuTeams @closeSidebar="sidebarOpen = false"/>
                        </li>

                        <li class="-mx-6 mt-auto">
                            <a href="#" class="flex items-center gap-x-4 px-6 py-3 text-sm font-semibold leading-6 text-gray-900 hover:bg-gray-50">
                                <img class="h-8 w-8 rounded-full bg-gray-50" :src="$page.props.auth.user.gravatar" />
                                <span class="sr-only">Your profile</span>
                                <span aria-hidden="true">{{ $page.props.auth.user.name }}</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>

        <div class="top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-sm sm:px-6 lg:hidden">
            <button type="button" class="-m-2.5 p-2.5 text-gray-700 lg:hidden" @click="sidebarOpen = true">
                <span class="sr-only">Open sidebar</span>
                <Bars3Icon class="h-6 w-6" aria-hidden="true" />
            </button>
            <div class="flex-1 text-sm font-semibold leading-6 text-gray-900">Dashboard</div>
            <a href="#">
                <span class="sr-only">Your profile</span>
                <img class="h-8 w-8 rounded-full bg-gray-50" :src="$page.props.auth.user.gravatar"/>
            </a>
        </div>

        <main class="lg:pl-72">
            <div class="xl:pr-96">
                <nav class="p-6 -mb-10" aria-label="Breadcrumb">
                    <ol role="list" class="flex items-center space-x-4">
                        <li>
                            <div>
                                <a href="#" class="text-gray-400 hover:text-gray-500">
                                    <HomeIcon class="h-5 w-5 flex-shrink-0" aria-hidden="true" />
                                    <span class="sr-only">Home</span>
                                </a>
                            </div>
                        </li>
                        <li v-for="page in pages" :key="page.name">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M5.555 17.776l8-16 .894.448-8 16-.894-.448z" />
                                </svg>
                                <a :href="page.href" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700" :aria-current="page.current ? 'page' : undefined">{{ page.name }}</a>
                            </div>
                        </li>
                    </ol>
                </nav>
                <div class="px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </div>
        </main>

        <aside class="fixed inset-y-0 right-0 hidden w-96 overflow-y-auto border-l border-gray-200 px-4 py-6 sm:px-6 lg:px-8 xl:block">
            <span>hello</span>
        </aside>
    </div>
</template>
