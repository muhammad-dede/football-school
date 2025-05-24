<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { debounce } from "lodash";

import AppLayout from "@/layouts/AppLayout.vue";
import MainContent from "@/components/MainContent.vue";
import PaginationLinks from "@/components/PaginationLinks.vue";
import { Card, CardContent } from "@/components/ui/card/index";
import { Button, buttonVariants } from "@/components/ui/button/index";
import { SquarePlus, Mail, ShieldCheck, MoreHorizontal } from "lucide-vue-next";

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge/index";

import SearchInput from "@/components/SearchInput.vue";
import FilterControl from "@/components/FilterControl.vue";
import MainContentHeader from "@/components/MainContentHeader.vue";
import MainContentHeaderTitle from "@/components/MainContentHeaderTitle.vue";
import { useInitials } from "@/composables/useInitials";
import usePermissions from "@/composables/usePermissions";

const { can } = usePermissions();

const props = defineProps({
    users: Object,
    search_term: String,
    per_page_term: String,
    filter_term: String,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Pengguna", href: "/user" },
];

const search = ref(props.search_term);
const perPage = ref(props.per_page_term);
const filter = ref(props.filter_term);

const dataControl = () => {
    router.get(
        route("user.index"),
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

const { getInitials } = useInitials();
</script>

<template>
    <Head title="Pengguna" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <MainContentHeader>
                <MainContentHeaderTitle title="Data Pengguna" />
                <Link
                    v-if="can('user-create')"
                    :href="route('user.create')"
                    :class="buttonVariants({ variant: 'default' })"
                >
                    <SquarePlus class="w-4 h-4" />Tambah
                </Link>
            </MainContentHeader>
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
                <template v-if="users.data.length > 0">
                    <Card
                        v-for="item in users.data"
                        :key="item.id"
                        class="py-4"
                    >
                        <CardContent class="px-4">
                            <div class="flex items-center gap-4">
                                <Avatar class="size-12">
                                    <AvatarFallback>
                                        {{ getInitials(item.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <div class="flex flex-col w-sm">
                                    <h5 class="font-bold">{{ item.name }}</h5>
                                    <div
                                        class="flex items-center font-semibold gap-1 text-gray-500 text-sm"
                                    >
                                        <Mail class="size-4" />
                                        {{ item.email }}
                                    </div>
                                </div>
                                <Badge
                                    variant="outline"
                                    class="p-2 rounded-full"
                                >
                                    <ShieldCheck />
                                    {{ item.roles[0].name }}
                                </Badge>
                                <div class="w-full flex justify-end">
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
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
                                                v-if="can('user-edit')"
                                                asChild
                                            >
                                                <Link
                                                    :href="
                                                        route(
                                                            'user.edit',
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
                                :href="route('user.create')"
                                :class="buttonVariants({ variant: 'default' })"
                                ><SquarePlus class="w-4 h-4" />Tambah
                                Pengguna</Link
                            >
                        </div>
                    </div>
                </template>
            </div>
            <PaginationLinks :paginator="users" />
        </MainContent>
    </AppLayout>
</template>
