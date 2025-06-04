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
    CalendarDays,
    IdCard,
    CreditCard,
    Receipt,
    CircleDollarSign,
    Dock,
} from "lucide-vue-next";

import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge/index";

import SearchInput from "@/components/SearchInput.vue";
import FilterControl from "@/components/FilterControl.vue";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import usePermissions from "@/composables/usePermissions";
import InfoItem from "@/components/InfoItem.vue";

const { can } = usePermissions();

const props = defineProps({
    billings: Object,
    search_term: String,
    per_page_term: String,
    filter_term: String,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Tagihan", href: "/billing" },
];

const search = ref(props.search_term);
const perPage = ref(props.per_page_term);
const filter = ref(props.filter_term);

const dataControl = () => {
    router.get(
        route("billing.index"),
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

const currency = (number) => {
    if (isNaN(number)) return "Rp0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Number(number));
};

const dateFormat = (date) => {
    if (!date) return "-";
    const options = { day: "numeric", month: "long", year: "numeric" };
    return new Date(date).toLocaleDateString("id-ID", options);
};
</script>

<template>
    <Head title="Tagihan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Data Tagihan"
                    description="Lihat dan kelola data tagihan yang tersedia"
                />
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
                <template v-if="billings.data.length > 0">
                    <Card
                        v-for="(item, index) in billings.data"
                        :key="item.id"
                        class="py-0"
                    >
                        <CardContent class="px-4 grid divide-y divide-gray-100">
                            <div class="flex justify-between items-center">
                                <InfoItem
                                    :label="formatTimestamp(item.created_at)"
                                    :icon="CalendarDays"
                                />
                                <Badge
                                    :variant="
                                        item.status === 'PAID'
                                            ? 'default'
                                            : 'destructive'
                                    "
                                    class="px-3 py-2 rounded-full h-fit"
                                >
                                    {{ item.status }}
                                </Badge>
                            </div>
                            <div class="flex relative overflow-hidden">
                                <div class="absolute top-3.5 left-0">
                                    <Avatar class="size-12 border">
                                        <AvatarFallback>
                                            {{ billings.from + index }}
                                        </AvatarFallback>
                                    </Avatar>
                                </div>
                                <div
                                    class="pl-18 w-[85%] flex flex-col justify-between lg:flex-row lg:items-center lg:gap-4"
                                >
                                    <InfoItem
                                        :label="item.period?.name"
                                        :value="item.student?.name"
                                        :icon="IdCard"
                                    />
                                    <InfoItem
                                        reverse
                                        label="Pembayaran"
                                        :value="item.billing_type?.name"
                                        :icon="Receipt"
                                    />
                                    <InfoItem
                                        reverse
                                        label="Total Pembayaran"
                                        :value="currency(item.amount)"
                                        :icon="CircleDollarSign"
                                    />
                                </div>
                                <div class="absolute top-5 right-0">
                                    <Link
                                        v-if="
                                            can('billing-payment-create') &&
                                            item.status === 'UNPAID'
                                        "
                                        :href="
                                            route(
                                                'billing.payment.create',
                                                item.id
                                            )
                                        "
                                        :class="
                                            buttonVariants({
                                                variant: 'secondary',
                                            })
                                        "
                                    >
                                        Bayar
                                    </Link>
                                </div>
                            </div>
                            <div
                                class="flex flex-col justify-between gap-x-4 lg:flex-row"
                            >
                                <InfoItem
                                    label="Sudah Bayar"
                                    :value="
                                        currency(item.payment?.amount) ?? '-'
                                    "
                                    :icon="CreditCard"
                                    background
                                />
                                <InfoItem
                                    label="Tanggal Bayar"
                                    :value="
                                        dateFormat(
                                            item.payment?.payment_date
                                        ) ?? '-'
                                    "
                                    :icon="CalendarDays"
                                    background
                                />
                                <InfoItem
                                    label="Metode Pembayaran"
                                    :value="item.payment?.method ?? '-'"
                                    :icon="Dock"
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
                    </div>
                </template>
            </div>
            <PaginationLinks :paginator="billings" />
        </MainContent>
    </AppLayout>
</template>
