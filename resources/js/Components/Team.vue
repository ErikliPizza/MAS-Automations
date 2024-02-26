<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="relative z-10" @close="closeDialog">
            <div class="fixed inset-0" />
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                        <TransitionChild
                            as="template"
                            enter="transform transition ease-in-out duration-100 sm:duration-300"
                            enter-from="translate-x-full"
                            enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-100 sm:duration-300"
                            leave-from="translate-x-0"
                            leave-to="translate-x-full"
                        >
                            <DialogPanel class="pointer-events-auto w-screen max-w-md">
                                <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                                    <div class="p-6">
                                        <div class="flex items-start justify-between">
                                            <Link href="/users" class="text-base font-semibold leading-6 text-gray-900" @click="closeDialog">
                                                Team Dashboard
                                            </Link>
                                            <div class="ml-3 flex h-7 items-center">
                                                <button
                                                    type="button"
                                                    class="relative rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500"
                                                    @click="closeDialog"
                                                >
                                                    <span class="absolute -inset-2.5" />
                                                    <span class="sr-only">Close panel</span>
                                                    <XMarkIcon class="h-6 w-6" aria-hidden="true" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-b border-gray-200">
                                        <div class="px-6">
                                            <nav class="-mb-px flex space-x-6">
                                                <a
                                                    v-for="tab in tabs"
                                                    :key="tab.name"
                                                    @click.prevent="setTab(tab.name)"
                                                    :class="[
                            currentTab === tab.name
                              ? 'border-indigo-500 text-indigo-600'
                              : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                            'whitespace-nowrap border-b-2 px-1 pb-4 text-sm font-medium cursor-pointer'
                          ]"
                                                >{{ tab.name }}</a
                                                >
                                            </nav>
                                        </div>
                                    </div>
                                    <ul role="list" class="flex-1 divide-y divide-gray-200 overflow-y-auto">
                                        <li v-for="item in filteredItems" :key="item.id">
                                            <div class="group relative flex items-center px-5 py-6">
                                                <Link :href="'/users/'+item.id" class="-m-1 block flex-1 p-1" @click="closeDialog">
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
                                                                        :href="`tel:${item.phone}`"
                                                                        :class="[
                                      active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
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
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script setup>
import { ref, computed, watchEffect } from 'vue';
import {
    Dialog,
    DialogPanel,
    DialogTitle,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue';
import { XMarkIcon } from '@heroicons/vue/24/outline';
import { EllipsisVerticalIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
        modelValue: Boolean,
        items: Array,
    });

const open = ref(props.modelValue);
watchEffect(() => {
    open.value = props.modelValue;
});
const emit = defineEmits(['update:modelValue']);
// Update to close the dialog and emit the event to parent
const closeDialog = () => {
    open.value = false;
    emit('update:modelValue', false);
};

const tabs = [
    { name: 'All', href: '#' },
    { name: 'Active', href: '#' },
    { name: 'Inactive', href: '#' },
];

const currentTab = ref('All');

const filteredItems = computed(() => {
    if (currentTab.value === 'All') return props.items;
    return props.items.filter((person) => person.status === currentTab.value.toLowerCase());
});

const setTab = (tabName) => {
    currentTab.value = tabName;
};
</script>

