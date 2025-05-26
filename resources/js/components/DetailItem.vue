<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: true,
    },
    icon: {
        type: [Object, Function],
        required: true,
    },
    iconColor: {
        type: String,
        default: "text-blue-600",
    },
    bgColor: {
        type: String,
        default: "bg-blue-100",
    },
});
</script>

<template>
    <div class="flex gap-2 items-start">
        <div
            class="rounded-xl flex items-center justify-center size-10 shrink-0"
            :class="[bgColor, 'p-2']"
        >
            <component :is="icon" class="size-4" :class="iconColor" />
        </div>
        <div class="grid gap-0.5">
            <p class="text-xs font-semibold text-gray-500">
                {{ label }}
            </p>
            <span class="text-sm font-bold">
                {{ value }}
            </span>
        </div>
    </div>
</template>
