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
    Phone,
    CalendarDays,
    IdCard,
    Timer,
    CreditCard,
    FileDigit,
    Landmark,
} from "lucide-vue-next";

import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge/index";

import SearchInput from "@/components/SearchInput.vue";
import FilterControl from "@/components/FilterControl.vue";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import { useInitials } from "@/composables/useInitials";
import usePermissions from "@/composables/usePermissions";
import InfoItem from "@/components/InfoItem.vue";

const { getInitials } = useInitials();
const { can } = usePermissions();

const props = defineProps({
    coaches: Object,
    search_term: String,
    per_page_term: String,
    filter_term: String,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Pelatih", href: "/coach" },
];

const search = ref(props.search_term);
const perPage = ref(props.per_page_term);
const filter = ref(props.filter_term);

const dataControl = () => {
    router.get(
        route("coach.index"),
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

const calculateAge = (birthDate) => {
    if (!birthDate) return "-";

    const today = new Date();
    const birth = new Date(birthDate);

    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();

    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
        age--;
    }

    return age;
};
</script>

<template>
    <Head title="Pelatih" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Data Pelatih"
                    description="Lihat dan kelola data pelatih yang tersedia"
                />
                <Link
                    v-if="can('coach-create')"
                    :href="route('coach.create')"
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
                <template v-if="coaches.data.length > 0">
                    <Card
                        v-for="item in coaches.data"
                        :key="item.id"
                        class="py-0"
                    >
                        <CardContent class="px-4 grid divide-y divide-gray-100">
                            <div class="flex justify-between items-center py-4">
                                <InfoItem
                                    :label="formatTimestamp(item.created_at)"
                                    :icon="CalendarDays"
                                />
                                <Badge
                                    :variant="
                                        item.is_active
                                            ? 'default'
                                            : 'destructive'
                                    "
                                    class="px-3 py-2 rounded-full h-fit"
                                >
                                    {{
                                        item.is_active ? "Aktif" : "Tidak Aktif"
                                    }}
                                </Badge>
                            </div>
                            <div class="flex relative overflow-hidden">
                                <div class="absolute top-2.5 left-0">
                                    <Avatar class="size-14 border">
                                        <AvatarImage
                                            :src="item.photo_url"
                                            alt="photo"
                                        />
                                        <AvatarFallback>
                                            {{ getInitials(item.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                </div>
                                <div
                                    class="pl-18 w-[85%] flex flex-col justify-between lg:flex-row lg:items-center lg:gap-4"
                                >
                                    <InfoItem
                                        :label="item.national_id_number"
                                        :value="item.name"
                                        :icon="IdCard"
                                    />
                                    <InfoItem
                                        reverse
                                        label="Telepon"
                                        :value="item.phone"
                                        :icon="Phone"
                                    />
                                    <InfoItem
                                        reverse
                                        label="Usia"
                                        :value="`${calculateAge(
                                            item.date_of_birth
                                        )} Tahun`"
                                        :icon="Timer"
                                    />
                                </div>
                                <div class="absolute top-5 right-0">
                                    <Link
                                        :href="route('coach.show', item.id)"
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
                                    label="Lisensi Kepelatihan"
                                    :value="item.coaching_license ?? '-'"
                                    :icon="CreditCard"
                                    color="lime"
                                    withColor
                                />
                                <InfoItem
                                    label="Nomor Lisensi"
                                    :value="item.license_number ?? '-'"
                                    :icon="FileDigit"
                                    color="purple"
                                    withColor
                                />
                                <InfoItem
                                    label="Lembaga Kepelatihan"
                                    :value="item.license_issuer ?? '-'"
                                    :icon="Landmark"
                                    color="sky"
                                    withColor
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
                        <div v-if="can('coach-create')">
                            <Link
                                :href="route('coach.create')"
                                :class="buttonVariants({ variant: 'default' })"
                                ><SquarePlus class="w-4 h-4" />Tambah
                                Pelatih</Link
                            >
                        </div>
                    </div>
                </template>
            </div>
            <PaginationLinks :paginator="coaches" />
        </MainContent>
    </AppLayout>
</template>
