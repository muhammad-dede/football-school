<script setup>
import { Link } from "@inertiajs/vue3";
import { Card, CardContent } from "@/components/ui/card";
import InfoItem from "@/components/InfoItem.vue";
import { Badge } from "@/components/ui/badge";
import { Button, buttonVariants } from "@/components/ui/button/index";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import {
    CalendarDays,
    Group,
    LocateFixed,
    Locate,
    Shirt,
} from "lucide-vue-next";
import usePermissions from "@/composables/usePermissions";

const { can } = usePermissions();

const props = defineProps({
    student: Object,
    enrollments: Object,
    dateFormat: Function,
    enrollmentHasBilling: Function,
});
</script>

<template>
    <template v-if="enrollments.length > 0">
        <Card v-for="(item, index) in enrollments" :key="item.id" class="py-0">
            <CardContent class="px-4 grid divide-y divide-gray-100">
                <div class="flex justify-between items-center">
                    <InfoItem
                        label="Tanggal Bergabung"
                        :value="dateFormat(item.join_date)"
                        :icon="CalendarDays"
                        background
                    />
                    <Badge
                        :variant="item.is_active ? 'default' : 'destructive'"
                        class="px-3 py-2 rounded-full h-fit"
                    >
                        {{ item.is_active ? "Aktif" : "Tidak Aktif" }}
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
                            :label="item.group?.name"
                            :value="item.period?.name"
                            :icon="Group"
                        />
                        <InfoItem
                            reverse
                            label="Periode"
                            :value="`${dateFormat(
                                item.period?.start_date
                            )} - ${dateFormat(item.period?.end_date)}`"
                            :icon="CalendarDays"
                        />
                    </div>
                    <div class="absolute top-5 right-0">
                        <Link
                            href="#"
                            :class="
                                buttonVariants({
                                    variant: 'secondary',
                                })
                            "
                        >
                            {{ enrollmentHasBilling(item)?.status }}
                        </Link>
                    </div>
                </div>
                <div class="flex flex-col justify-between gap-x-4 lg:flex-row">
                    <InfoItem
                        label="Posisi"
                        :value="item.position?.name"
                        :icon="LocateFixed"
                        background
                    />
                    <InfoItem
                        label="Posisi Alternatif"
                        :value="item.alternative_position?.name"
                        :icon="Locate"
                        background
                    />
                    <InfoItem
                        label="Nomor Punggung"
                        :value="`${item.jersey_number ?? '-'}`"
                        :icon="Shirt"
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
            <div v-if="can('student-enrollment-create')">
                <Link
                    :href="route('student.enrollment.create', student.id)"
                    :class="buttonVariants({ variant: 'default' })"
                >
                    Tambah Team
                </Link>
            </div>
        </div>
    </template>
</template>
