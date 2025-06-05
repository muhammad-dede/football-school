<script setup>
import { ref, watchEffect } from "vue";
import { useForm } from "@inertiajs/vue3";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table/index";
import { Button } from "@/components/ui/button/index";
import { Input } from "@/components/ui/input/index";
import { CircleCheck, Circle, LoaderCircle } from "lucide-vue-next";

const props = defineProps({
    match_event: Object,
    attendances: Object,
});

const form = useForm({
    attendances: [],
});

watchEffect(() => {
    if (
        form.attendances.length === 0 &&
        props.match_event.participants?.length
    ) {
        form.attendances = props.match_event.participants.map(
            (participant) => ({
                student_id: participant.student_id,
                attendance: participant.attendance,
                notes: participant.notes,
            })
        );
    }
});

const isEditAttendance = ref(false);

const resetAttendance = () => {
    form.attendances = props.match_event.participants.map((participant) => ({
        student_id: participant.student_id,
        attendance: participant.attendance,
        notes: participant.notes,
    }));
};

const submit = () => {
    form.post(route("match-event.attendance", props.match_event.id), {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => {
            isEditAttendance.value = false;
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <div class="rounded-md border mb-4">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead class="w-[10px]">No</TableHead>
                        <TableHead>Pemain</TableHead>
                        <template
                            v-for="(item, index) in attendances"
                            :key="index"
                        >
                            <TableHead>{{ item.label }}</TableHead>
                        </template>
                        <TableHead>Keterangan</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="match_event.participants?.length > 0">
                        <TableRow
                            v-for="(
                                participant, index_participant
                            ) in match_event.participants"
                            :key="index_participant"
                        >
                            <TableCell class="font-medium">
                                {{ index_participant + 1 }}
                            </TableCell>
                            <TableCell>
                                {{ participant.student?.name ?? "-" }}
                            </TableCell>
                            <template
                                v-for="attendance in attendances"
                                :key="attendance.value"
                            >
                                <TableCell class="w-[5%]">
                                    <div
                                        :class="[
                                            'flex justify-center items-center',
                                            isEditAttendance
                                                ? 'cursor-pointer'
                                                : '',
                                        ]"
                                        @click="
                                            isEditAttendance
                                                ? (form.attendances[
                                                      index_participant
                                                  ].attendance =
                                                      attendance.value)
                                                : null
                                        "
                                    >
                                        <CircleCheck
                                            v-if="
                                                form.attendances[
                                                    index_participant
                                                ].attendance ===
                                                attendance.value
                                            "
                                            class="size-5 text-green-500"
                                        />
                                        <Circle
                                            v-else
                                            class="size-5 text-gray-400 hover:text-green-500 transition-colors"
                                        />
                                    </div>
                                </TableCell>
                            </template>
                            <TableCell class="w-[20%]">
                                <Input
                                    class="border-none shadow-none"
                                    type="text"
                                    :name="`attendance_${index_participant}_notes`"
                                    autocomplete="off"
                                    v-model="
                                        form.attendances[index_participant]
                                            .notes
                                    "
                                    :readonly="!isEditAttendance"
                                />
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell
                                :colspan="3 + attendances.length"
                                class="text-center py-6"
                            >
                                <strong>Belum ada data</strong>
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>
        <div
            v-if="match_event.participants?.length > 0"
            class="flex justify-end items-center"
        >
            <div v-if="isEditAttendance" class="space-x-2">
                <Button type="submit" :disabled="form.processing">
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Simpan Kehadiran
                </Button>
                <Button
                    type="button"
                    variant="outline"
                    @click="
                        () => {
                            isEditAttendance = false;
                            resetAttendance();
                        }
                    "
                >
                    Batal
                </Button>
            </div>
            <Button
                v-else
                type="button"
                variant="secondary"
                @click="isEditAttendance = true"
            >
                Ubah Kehadiran
            </Button>
        </div>
    </form>
</template>
