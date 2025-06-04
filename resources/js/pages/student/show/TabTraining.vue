<script setup>
import { Card, CardContent } from "@/components/ui/card";
import InfoItem from "@/components/InfoItem.vue";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import {
    CalendarDays,
    IdCard,
    UserCog,
    MapPin,
    NotebookPen,
} from "lucide-vue-next";
import { Badge } from "@/components/ui/badge/index";

const props = defineProps({
    student: Object,
    trainings: Object,
    attendances: Object,
    dateFormat: Function,
});

const getAttendance = (trainingId, studentId) => {
    const attendance = props.trainings
        ?.find((t) => t.id === trainingId)
        ?.attendances?.find((a) => a.student_id === studentId);
    return attendance;
};

const getAttendanceLabel = (attendance) => {
    if (!attendance) return "BELUM DIISI";
    const found = props.attendances?.find((item) => item.value === attendance);
    return found?.label?.toUpperCase() ?? "BELUM DIISI";
};

const getAttendanceVariant = (attendance) => {
    if (!attendance) return "outline";
    switch (attendance) {
        case "PRESENT":
            return "default";
        case "ABSENT":
            return "destructive";
        case "EXCUSED":
            return "secondary";
        default:
            return "default";
    }
};
</script>

<template>
    <template v-if="trainings.length > 0">
        <Card v-for="(item, index) in trainings" :key="item.id" class="py-0">
            <CardContent class="px-4 grid divide-y divide-gray-100">
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
                            label="Jadwal Pelatihan"
                            :value="`${dateFormat(item.training_date)}, ${
                                item.start_time
                            }-${item.end_time}`"
                            :icon="CalendarDays"
                        />
                    </div>
                </div>
                <div class="flex flex-col justify-between gap-x-4 lg:flex-row">
                    <InfoItem
                        label="Lokasi"
                        :value="item.location ?? '-'"
                        :icon="MapPin"
                        background
                    />
                    <InfoItem
                        label="Deskripsi"
                        :value="item.description ?? '-'"
                        :icon="NotebookPen"
                        background
                    />
                    <div class="flex justify-center items-center">
                        <Badge
                            class="px-3 py-2 rounded-3xl"
                            :variant="
                                getAttendanceVariant(
                                    getAttendance(item.id, student.id)
                                )
                            "
                        >
                            {{
                                getAttendanceLabel(
                                    getAttendance(item.id, student.id)
                                        ?.attendance
                                )
                            }}
                        </Badge>
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
        </div>
    </template>
</template>
