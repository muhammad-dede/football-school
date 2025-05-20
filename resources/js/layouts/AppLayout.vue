<script setup>
import { usePage } from "@inertiajs/vue3";
import AppSidebar from "@/components/AppSidebar.vue";
import AppContent from "@/components/AppContent.vue";
import { SidebarProvider } from "@/components/ui/sidebar/index";
import AppSidebarHeader from "@/components/AppSidebarHeader.vue";
import FlashMessage from "@/components/FlashMessage.vue";

const props = defineProps({
    breadcrumbs: {
        type: Array,
        default: () => [],
    },
});
const isOpen = usePage().props.sidebarOpen;
</script>

<template>
    <SidebarProvider :default-open="isOpen">
        <AppSidebar />
        <AppContent>
            <AppSidebarHeader :breadcrumbs="breadcrumbs" />
            <slot />
        </AppContent>
    </SidebarProvider>
    <FlashMessage
        v-if="$page.props.flash.success || $page.props.flash.failed"
        :type="$page.props.flash.success ? 'success' : 'failed'"
        :message="$page.props.flash.success || $page.props.flash.failed"
    />
</template>
