<script setup>
import { Link } from "@inertiajs/vue3";
import { Card, CardContent } from "@/components/ui/card";
import InfoItem from "@/components/InfoItem.vue";
import { Badge } from "@/components/ui/badge";
import { Button, buttonVariants } from "@/components/ui/button/index";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import {
    CalendarDays,
    Banknote,
    LocateFixed,
    Locate,
    Shirt,
    CircleDollarSign,
    Dock,
} from "lucide-vue-next";
import usePermissions from "@/composables/usePermissions";

const { can } = usePermissions();

const props = defineProps({
    student: Object,
    billings: Object,
    dateFormat: Function,
});

const currency = (number) => {
    if (isNaN(number)) return "Rp0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Number(number));
};
</script>

<template>
    <template v-if="billings.length > 0">
        <Card v-for="(item, index) in billings" :key="item.id" class="py-0">
            <CardContent class="px-4 grid divide-y divide-gray-100">
                <div class="flex justify-between items-center">
                    <InfoItem
                        label="Jatuh Tempo"
                        :value="dateFormat(item.due_date)"
                        :icon="CalendarDays"
                        background
                    />
                    <Badge
                        :variant="
                            item.status === 'UNPAID' ? 'destructive' : 'default'
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
                                {{ index + 1 }}
                            </AvatarFallback>
                        </Avatar>
                    </div>
                    <div
                        class="pl-18 w-[85%] flex flex-col justify-between lg:flex-row lg:items-center lg:gap-4"
                    >
                        <InfoItem
                            :label="item.billing_type?.name"
                            :value="item.period?.name"
                            :icon="Banknote"
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
                                can('student-payment-create') && !item.payment
                            "
                            :href="route('student.payment.create', item.id)"
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
                <div class="flex flex-col justify-between gap-x-4 lg:flex-row">
                    <InfoItem
                        label="Sudah Bayar"
                        :value="currency(item.payment?.amount)"
                        :icon="CircleDollarSign"
                        background
                    />
                    <InfoItem
                        label="Tanggal Bayar"
                        :value="dateFormat(item.payment?.payment_date) ?? '-'"
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
</template>
