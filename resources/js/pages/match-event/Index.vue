<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { debounce } from "lodash";

import AppLayout from "@/layouts/AppLayout.vue";
import MainContent from "@/components/MainContent.vue";
import PaginationLinks from "@/components/PaginationLinks.vue";
import { Card, CardContent } from "@/components/ui/card/index";
import { buttonVariants } from "@/components/ui/button/index";
import {
    SquarePlus,
    CalendarDays,
    IdCard,
    UserCog,
    Timer,
    GitCompare,
    FileDigit,
} from "lucide-vue-next";

import { Avatar, AvatarFallback } from "@/components/ui/avatar";

import SearchInput from "@/components/SearchInput.vue";
import FilterControl from "@/components/FilterControl.vue";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import usePermissions from "@/composables/usePermissions";
import InfoItem from "@/components/InfoItem.vue";

const { can } = usePermissions();

const props = defineProps({
    match_events: Object,
    search_term: String,
    per_page_term: String,
    filter_term: String,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Pertandingan", href: "/match-event" },
];

const search = ref(props.search_term);
const perPage = ref(props.per_page_term);
const filter = ref(props.filter_term);

const dataControl = () => {
    router.get(
        route("match-event.index"),
        {
            search: search.value,
            per_page: perPage.value,
            filter: filter.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

watch(
    search,
    debounce(() => {
        dataControl();
    }, 1000)
);

watch([perPage, filter], () => {
    dataControl();
});

const formatTimestamp = (timestamp) => {
    if (!timestamp) return "-";

    const date = new Date(timestamp);

    const datePart = date.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "long",
        year: "numeric",
    });

    const timePart = date.toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
        hour12: false,
    });

    return `${datePart}, ${timePart}`;
};

const dateFormat = (date) => {
    if (!date) return "-";
    const options = { day: "numeric", month: "long", year: "numeric" };
    return new Date(date).toLocaleDateString("id-ID", options);
};
</script>

<template>
    <Head title="Pertandingan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Data Pertandingan"
                    description="Lihat dan kelola data pertandingan yang tersedia"
                />
                <Link
                    v-if="can('match-event-create')"
                    :href="route('match-event.create')"
                    :class="buttonVariants({ variant: 'default' })"
                >
                    <SquarePlus class="w-4 h-4" />Tambah
                </Link>
            </HeadingGroup>
            <div class="flex justify-between items-center gap-4 mb-4">
                <SearchInput v-model="search" />
                <FilterControl
                    :per-page="perPage"
                    :filter="filter"
                    @update:per-page="perPage = $event"
                    @update:filter="filter = $event"
                />
            </div>
            <div class="flex flex-col gap-4 mb-2">
                <template v-if="match_events.data.length > 0">
                    <Card
                        v-for="(item, index) in match_events.data"
                        :key="item.id"
                        class="py-0"
                    >
                        <CardContent class="px-4 grid divide-y divide-gray-100">
                            <div class="flex justify-between items-center">
                                <InfoItem
                                    :label="formatTimestamp(item.created_at)"
                                    :icon="CalendarDays"
                                />
                                <InfoItem
                                    :label="`${item.group_score}-${item.opponent_score}`"
                                    :icon="FileDigit"
                                />
                            </div>
                            <div class="flex relative overflow-hidden">
                                <div class="absolute top-3.5 left-0">
                                    <Avatar class="size-12 border">
                                        <AvatarFallback>
                                            {{ match_events.from + index }}
                                        </AvatarFallback>
                                    </Avatar>
                                </div>
                                <div
                                    class="pl-18 w-[85%] flex flex-col justify-between lg:flex-row lg:items-center lg:gap-4"
                                >
                                    <InfoItem
                                        :label="item.period?.name ?? '-'"
                                        :value="item.group?.name ?? '-'"
                                        :icon="IdCard"
                                    />
                                    <InfoItem
                                        reverse
                                        label="Pelatih"
                                        :value="item.coach?.name ?? '-'"
                                        :icon="UserCog"
                                    />
                                    <InfoItem
                                        reverse
                                        label="Melawan"
                                        :value="item.opponent ?? '-'"
                                        :icon="GitCompare"
                                    />
                                </div>
                                <div class="absolute top-5 right-0">
                                    <Link
                                        :href="
                                            route('match-event.show', item.id)
                                        "
                                        :class="
                                            buttonVariants({
                                                variant: 'secondary',
                                            })
                                        "
                                    >
                                        Kelola
                                    </Link>
                                </div>
                            </div>
                            <div
                                class="flex flex-col justify-between gap-x-4 lg:flex-row"
                            >
                                <InfoItem
                                    label="Tanggal Pertandingan"
                                    :value="dateFormat(item.match_date) ?? '-'"
                                    :icon="CalendarDays"
                                    background
                                />
                                <InfoItem
                                    label="Waktu Mulai"
                                    :value="item.start_time ?? '-'"
                                    :icon="Timer"
                                    background
                                />
                                <InfoItem
                                    label="Waktu Selesai"
                                    :value="item.end_time ?? '-'"
                                    :icon="Timer"
                                    background
                                />
                            </div>
                        </CardContent>
                    </Card>
                </template>
                <template v-else>
                    <div class="p-8 text-center flex flex-col gap-4">
                        <span class="text-base font-semibold">
                            Tidak ada data ditemukan
                        </span>
                        <div v-if="can('match-event-create')">
                            <Link
                                :href="route('match-event.create')"
                                :class="buttonVariants({ variant: 'default' })"
                                ><SquarePlus class="w-4 h-4" />Tambah
                                Pertandingan</Link
                            >
                        </div>
                    </div>
                </template>
            </div>
            <PaginationLinks :paginator="match_events" />
        </MainContent>
    </AppLayout>
</template>
