<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { debounce } from "lodash";

import AppLayout from "@/layouts/AppLayout.vue";
import MainContent from "@/components/MainContent.vue";
import PaginationLinks from "@/components/PaginationLinks.vue";
import { Card, CardContent } from "@/components/ui/card/index";
import { Button, buttonVariants } from "@/components/ui/button/index";
import {
    SquarePlus,
    MoreHorizontal,
    UserCheck,
    CalendarRange,
} from "lucide-vue-next";

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";

import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge/index";

import SearchInput from "@/components/SearchInput.vue";
import FilterControl from "@/components/FilterControl.vue";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import { useInitials } from "@/composables/useInitials";
import usePermissions from "@/composables/usePermissions";

const { can } = usePermissions();
const { getInitials } = useInitials();

const props = defineProps({
    periods: Object,
    search_term: String,
    per_page_term: String,
    filter_term: String,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Periode", href: "/period" },
];

const search = ref(props.search_term);
const perPage = ref(props.per_page_term);
const filter = ref(props.filter_term);
const periodToStatus = ref(null);

const dataControl = () => {
    router.get(
        route("period.index"),
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

const confirmStatus = (period) => {
    periodToStatus.value = period;
};

const changeStatus = () => {
    if (!periodToStatus.value) return;
    const periodId = periodToStatus.value.id;
    periodToStatus.value = null;
    router.post(route("period.status", periodId), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Periode" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Data Periode"
                    description="Lihat dan kelola data periode yang tersedia"
                />
                <Link
                    v-if="can('period-create')"
                    :href="route('period.create')"
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
                <template v-if="periods.data.length > 0">
                    <Card
                        v-for="item in periods.data"
                        :key="item.id"
                        class="py-4"
                    >
                        <CardContent class="px-4">
                            <div
                                class="flex lg:items-center gap-4 relative overflow-hidden items-start"
                            >
                                <Avatar class="size-12">
                                    <AvatarFallback>
                                        {{ getInitials(item.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <div
                                    class="flex flex-col items-start gap-2 lg:flex-row lg:items-center lg:gap-4"
                                >
                                    <div
                                        class="flex flex-col lg:min-w-xs lg:max-w-sm"
                                    >
                                        <h5 class="font-bold">
                                            {{ item.name }}
                                        </h5>
                                        <div
                                            class="flex items-center font-semibold gap-1 text-gray-500 text-sm"
                                        >
                                            <CalendarRange class="size-4" />
                                            {{ item.start_date }} s/d
                                            {{ item.end_date }}
                                        </div>
                                    </div>
                                    <Badge
                                        :variant="
                                            item.is_active
                                                ? 'secondary'
                                                : 'destructive'
                                        "
                                        class="p-2 rounded-full h-fit cursor-pointer"
                                        @click="confirmStatus(item)"
                                    >
                                        <UserCheck />
                                        {{
                                            item.is_active
                                                ? "Aktif"
                                                : "Tidak Aktif"
                                        }}
                                    </Badge>
                                </div>
                                <div
                                    class="absolute top-0 right-0 flex items-start justify-center h-full lg:items-center"
                                >
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="outline"
                                                class="w-8 h-8 p-0"
                                            >
                                                <MoreHorizontal
                                                    class="w-4 h-4"
                                                />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuLabel>
                                                Aksi
                                            </DropdownMenuLabel>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                v-if="can('period-edit')"
                                                asChild
                                            >
                                                <Link
                                                    :href="
                                                        route(
                                                            'period.edit',
                                                            item.id
                                                        )
                                                    "
                                                >
                                                    Ubah
                                                </Link>
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </template>
                <template v-else>
                    <div class="p-8 text-center flex flex-col gap-4">
                        <span class="text-base font-semibold">
                            Tidak ada data ditemukan
                        </span>
                        <div>
                            <Link
                                :href="route('period.create')"
                                :class="buttonVariants({ variant: 'default' })"
                                ><SquarePlus class="w-4 h-4" />Tambah
                                Periode</Link
                            >
                        </div>
                    </div>
                </template>
            </div>
            <PaginationLinks :paginator="periods" />
        </MainContent>
    </AppLayout>
    <AlertDialog :open="!!periodToStatus">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>
                    Apakah Anda benar-benar yakin?
                </AlertDialogTitle>
                <AlertDialogDescription>
                    Anda akan mengubah status periode, periode aktif saat ini
                    akan digantikan.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="periodToStatus = null">
                    Batal
                </AlertDialogCancel>
                <AlertDialogAction @click="changeStatus"
                    >Ubah Status</AlertDialogAction
                >
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
