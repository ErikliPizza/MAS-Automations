<script setup>
import SidebarMenuCreateUser from "@/Components/Sidebar/SidebarMenuCreateUser.vue";
import SidebarMenuFrame from "@/Components/Sidebar/SidebarMenuFrame.vue";
import SidebarMenuModules from "@/Components/Sidebar/SidebarMenuModules.vue";
import SidebarMenuLogout from "@/Components/Sidebar/SidebarMenuLogout.vue";
import SidebarMenuSettings from "@/Components/Sidebar/SidebarMenuSettings.vue";
</script>

<template>
    <SidebarMenuFrame>
        <SidebarMenuModules @closeSidebar="$emit('closeSidebar')"/>
        <SidebarMenuCreateUser @closeSidebar="$emit('closeSidebar')" v-if="$page.props.auth.user.role === 'admin' || $page.props.auth.user.role === 'root' "/>
        <SidebarMenuSettings @closeSidebar="$emit('closeSidebar')"/>
        <SidebarMenuLogout />
    </SidebarMenuFrame>
</template>

<style scoped>

</style>
