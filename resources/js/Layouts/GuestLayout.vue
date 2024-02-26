<script setup>
import { useFlashMessages } from "@/Services/useFlashMessages.js";
useFlashMessages(); // Initialize flash messages functionality

</script>

<template>
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 bg-white rounded-lg mt-2">
        <div class="flex min-h-full flex-1 items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
            <div class="w-full max-w-lg space-y-10">
                <slot />
            </div>
        </div>
    </div>
</template>
