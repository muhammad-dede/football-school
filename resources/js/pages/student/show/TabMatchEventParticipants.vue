<script setup>
import { Card, CardContent } from "@/components/ui/card";
import InfoItem from "@/components/InfoItem.vue";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import {
    IdCard,
    UserCog,
    MapPin,
    GitCompare,
    FileDigit,
    Timer,
} from "lucide-vue-next";
import { Badge } from "@/components/ui/badge/index";

const props = defineProps({
    student: Object,
    match_event_participants: Object,
    attendances: Object,
    dateFormat: Function,
});

const getAttendance = (matchEventParticipantId) => {
    return (
        props.match_event_participants?.find(
            (p) => p.id === matchEventParticipantId
        )?.attendance ?? null
    );
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
    <template v-if="match_event_participants.length > 0">
        <Card
            v-for="(item, index) in match_event_participants"
            :key="item.id"
            class="py-0"
        >
            <CardContent class="px-4 grid divide-y divide-gray-100">
                <div class="flex justify-between items-center">
                    <InfoItem
                        :label="`${item.match_event?.group_score}-${item.match_event?.opponent_score}`"
                        :icon="FileDigit"
                    />
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
                            :label="item.match_event?.period?.name ?? '-'"
                            :value="item.match_event?.group?.name ?? '-'"
                            :icon="IdCard"
                        />
                        <InfoItem
                            reverse
                            label="Pelatih"
                            :value="item.match_event?.coach?.name ?? '-'"
                            :icon="UserCog"
                        />
                        <InfoItem
                            reverse
                            label="Melawan"
                            :value="item.match_event?.opponent ?? '-'"
                            :icon="GitCompare"
                        />
                    </div>
                </div>
                <div class="flex flex-col justify-between gap-x-4 lg:flex-row">
                    <InfoItem
                        label="Lokasi"
                        :value="item.match_event?.location ?? '-'"
                        :icon="MapPin"
                        background
                    />
                    <InfoItem
                        label="Waktu Pertandingan"
                        :value="`${dateFormat(item.match_event?.match_date)}, ${
                            item.match_event?.start_time
                        } - ${item.match_event?.end_time}`"
                        :icon="Timer"
                        background
                    />
                    <div class="flex justify-center items-center">
                        <Badge
                            class="px-3 py-2 rounded-3xl"
                            :variant="
                                getAttendanceVariant(getAttendance(item.id))
                            "
                        >
                            {{ getAttendanceLabel(getAttendance(item.id)) }}
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
