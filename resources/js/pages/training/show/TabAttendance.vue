<script setup>
import { watchEffect, ref } from "vue";
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
    training: Object,
    attendances: Object,
    students: Object,
});

const form = useForm({
    attendances: [],
});

watchEffect(() => {
    form.attendances = props.students.map((student) => {
        const existing = props.training.attendances?.find(
            (item) => item.student_id === student.id
        );
        return {
            student_id: student.id,
            notes: existing?.notes ?? "",
            attendance: existing?.attendance ?? "",
        };
    });
});

const isEditAttendance = ref(false);

const submit = () => {
    form.post(route("training.attendance", props.training.id), {
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
                        <TableHead>Siswa</TableHead>

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
                    <template v-if="students.length > 0">
                        <TableRow
                            v-for="(student, index_student) in students"
                            :key="index_student"
                        >
                            <TableCell class="font-medium">
                                {{ index_student + 1 }}
                            </TableCell>
                            <TableCell>
                                {{ student.name ?? "-" }}
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
                                                      index_student
                                                  ].attendance =
                                                      attendance.value)
                                                : null
                                        "
                                    >
                                        <CircleCheck
                                            v-if="
                                                form.attendances[index_student]
                                                    .attendance ===
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
                                    :name="`attendance_${index_student}_notes`"
                                    autocomplete="off"
                                    v-model="
                                        form.attendances[index_student].notes
                                    "
                                    :readonly="!isEditAttendance"
                                />
                            </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>
        <div class="flex justify-end items-center">
            <div v-if="isEditAttendance" class="space-x-2">
                <Button type="submit" :disabled="form.processing">
                    <LoaderCircle
                        v-if="form.processing"
                        class="h-4 w-4 animate-spin"
                    />
                    Simpan Kehadiran
                </Button>
                <Button variant="outline" @click="isEditAttendance = false">
                    Batal
                </Button>
            </div>
            <Button v-else variant="secondary" @click="isEditAttendance = true">
                Ubah Kehadiran
            </Button>
        </div>
    </form>
</template>
