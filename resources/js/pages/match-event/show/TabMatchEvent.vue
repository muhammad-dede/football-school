<script setup>
import InfoItem from "@/components/InfoItem.vue";
import { Card, CardContent } from "@/components/ui/card/index";
import {
    Calendar,
    Group,
    CalendarDays,
    UserCog,
    Timer,
    MapPin,
    NotebookPen,
} from "lucide-vue-next";

defineProps({
    match_event: Object,
});

const dateFormat = (date) => {
    if (!date) return "-";
    const options = { day: "numeric", month: "long", year: "numeric" };
    return new Date(date).toLocaleDateString("id-ID", options);
};
</script>

<template>
    <Card>
        <CardContent>
            <h5 class="text-sm font-bold text-gray-500 mb-4">
                Informasi Kepelatihan
            </h5>
            <div class="grid divide-y divide-gray-100">
                <InfoItem
                    label="Grup"
                    :value="match_event.group?.name ?? '-'"
                    :icon="Group"
                    background
                />
                <InfoItem
                    label="Periode"
                    :value="match_event.period?.name ?? '-'"
                    :icon="CalendarDays"
                    background
                />
                <InfoItem
                    label="Pelatih"
                    :value="match_event.coach?.name ?? '-'"
                    :icon="UserCog"
                    background
                />
                <InfoItem
                    label="Tanggal Pelatihan"
                    :value="dateFormat(match_event.match_date) ?? '-'"
                    :icon="Calendar"
                    background
                />
                <InfoItem
                    label="Waktu Pelatihan"
                    :value="
                        `${match_event.start_time} - ${match_event.end_time}` ??
                        '-'
                    "
                    :icon="Timer"
                    background
                />
                <InfoItem
                    label="Lokasi"
                    :value="match_event.location ?? '-'"
                    :icon="MapPin"
                    background
                />
                <InfoItem
                    label="Deskripsi"
                    :value="match_event.description ?? '-'"
                    :icon="NotebookPen"
                    background
                />
            </div>
        </CardContent>
    </Card>
</template>
