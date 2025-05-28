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
    Code,
    MarsStroke,
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
    groups: Object,
    search_term: String,
    per_page_term: String,
    filter_term: String,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Grup", href: "/group" },
];

const search = ref(props.search_term);
const perPage = ref(props.per_page_term);
const filter = ref(props.filter_term);
const groupToStatus = ref(null);

const dataControl = () => {
    router.get(
        route("group.index"),
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

const confirmStatus = (group) => {
    groupToStatus.value = group;
};

const changeStatus = () => {
    if (!groupToStatus.value) return;
    const groupId = groupToStatus.value.id;
    groupToStatus.value = null;
    router.post(route("group.status", groupId), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Grup" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Data Grup"
                    description="Lihat dan kelola data grup yang tersedia"
                />
                <Link
                    v-if="can('group-create')"
                    :href="route('group.create')"
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
                <template v-if="groups.data.length > 0">
                    <Card
                        v-for="item in groups.data"
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
                                            <Code class="size-4" />
                                            {{ item.code }}
                                        </div>
                                    </div>
                                    <Badge
                                        variant="outline"
                                        class="p-2 rounded-full h-fit"
                                    >
                                        <MarsStroke />
                                        {{ item.age_min }} -
                                        {{ item.age_max }} Tahun
                                    </Badge>
                                    <Badge
                                        :variant="
                                            item.is_active
                                                ? 'secondary'
                                                : 'destructive'
                                        "
                                        :class="[
                                            'p-2 rounded-full h-fit',
                                            can('group-status')
                                                ? 'cursor-pointer'
                                                : '',
                                        ]"
                                        @click="
                                            can('group-status')
                                                ? confirmStatus(item)
                                                : null
                                        "
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
                                                v-if="can('group-edit')"
                                                asChild
                                            >
                                                <Link
                                                    :href="
                                                        route(
                                                            'group.edit',
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
                        <div v-if="can('group-create')">
                            <Link
                                :href="route('group.create')"
                                :class="buttonVariants({ variant: 'default' })"
                                ><SquarePlus class="w-4 h-4" />Tambah Grup</Link
                            >
                        </div>
                    </div>
                </template>
            </div>
            <PaginationLinks :paginator="groups" />
        </MainContent>
    </AppLayout>
    <AlertDialog :open="!!groupToStatus">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>
                    Apakah Anda benar-benar yakin?
                </AlertDialogTitle>
                <AlertDialogDescription>
                    Anda akan mengubah status grup.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="groupToStatus = null">
                    Batal
                </AlertDialogCancel>
                <AlertDialogAction @click="changeStatus"
                    >Ubah Status</AlertDialogAction
                >
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
