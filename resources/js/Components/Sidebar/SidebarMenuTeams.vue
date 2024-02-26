<script setup>
import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {Link, usePage} from "@inertiajs/vue3";
import {EllipsisVerticalIcon} from "@heroicons/vue/20/solid/index.js";
import { useCanIClick } from "@/Composables/useCanIClick.vue";

const canIClick = useCanIClick();

</script>

<template>
    <ul role="list" class="-mx-2 mt-2 space-y-1">
        <li v-for="item in $page.props.team.users" :key="item.id">
            <div class="group relative flex items-center py-4">
                <Link :disabled="!canIClick(item.role)" as="button" @click="$emit('closeSidebar')" :href="'/users/'+item.id" class="text-left -m-1 block flex-1 p-1">
                    <div class="absolute inset-0 group-hover:bg-gray-50" aria-hidden="true" />
                    <div class="relative flex min-w-0 flex-1 items-center">
                            <span class="relative inline-block flex-shrink-0">
                              <img class="h-10 w-10 rounded-full" :src="item.gravatar" alt="" />
                              <span
                                  :class="[
                                  item.status === 'active' ? 'bg-green-400' : 'bg-red-400',
                                  'absolute top-0 right-0 block h-2.5 w-2.5 rounded-full ring-2 ring-white'
                                ]"
                                  aria-hidden="true"
                              ></span>
                            </span>
                        <div class="ml-4 truncate">
                            <p class="truncate text-sm font-medium text-gray-900">{{ item.name }}</p>
                            <p class="truncate text-sm text-gray-500">{{ '@' + item.email }}</p>
                        </div>
                    </div>
                </Link>
                <Menu as="div" class="relative ml-2 inline-block flex-shrink-0 text-left">
                    <MenuButton
                        class="group relative inline-flex h-8 w-8 items-center justify-center rounded-full bg-white focus:outline-none focus:ring-2 focus:ring-gray-100 focus:ring-offset-2"
                    >
                        <span class="absolute -inset-1.5" />
                        <span class="sr-only">Open options menu</span>
                        <span class="flex h-full w-full items-center justify-center rounded-full">
                              <EllipsisVerticalIcon
                                  class="h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                  aria-hidden="true"
                              />
                            </span>
                    </MenuButton>
                    <transition
                        enter-active-class="transition ease-out duration-100"
                        enter-from-class="transform opacity-0 scale-95"
                        enter-to-class="transform opacity-100 scale-100"
                        leave-active-class="transition ease-in duration-75"
                        leave-from-class="transform opacity-100 scale-100"
                        leave-to-class="transform opacity-0 scale-95"
                    >
                        <MenuItems
                            class="absolute right-9 top-0 z-10 w-48 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                        >
                            <div class="py-1">
                                <MenuItem v-slot="{ active }">
                                    <a
                                        v-show="item.phone"
                                        :href="`https://api.whatsapp.com/send?phone=${item.phone}`"
                                        :class="[
                                      active ? 'bg-gray-100 text-gray-900' : 'text-green-500',
                                      'block px-4 py-2 text-sm'
                                    ]"
                                    >WhatsApp</a
                                    >
                                </MenuItem>

                                <MenuItem v-slot="{ active }">
                                    <a
                                        v-show="item.phone"
                                        :href="`tel:${item.phone}`"
                                        :class="[
                                      active ? 'bg-gray-100 text-gray-900' : 'text-black',
                                      'block px-4 py-2 text-sm'
                                    ]"
                                    >Call</a
                                    >
                                </MenuItem>

                                <MenuItem v-slot="{ active }">
                                    <a
                                        :href="`mailto:${item.email}`"
                                        :class="[
                                      active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                                      'block px-4 py-2 text-sm'
                                    ]"
                                    >Send email</a
                                    >
                                </MenuItem>
                            </div>
                        </MenuItems>
                    </transition>
                </Menu>
            </div>
        </li>
    </ul>
</template>

<style scoped>

</style>
