<script setup>
import { computed } from "vue";
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import MainContent from "@/components/MainContent.vue";
import { Card, CardContent, CardFooter } from "@/components/ui/card/index";
import { Label } from "@/components/ui/label/index";
import { Input } from "@/components/ui/input/index";
import { Button, buttonVariants } from "@/components/ui/button/index";
import InputError from "@/components/InputError.vue";
import { LoaderCircle } from "lucide-vue-next";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table/index";
import { Textarea } from "@/components/ui/textarea";
import { Separator } from "@/components/ui/separator/index";
import usePermissions from "@/composables/usePermissions";

const { can } = usePermissions();

const props = defineProps({
    groups: Object,
    periods: Object,
    coaches: Object,
    students: Object,
    match_event: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Pertandingan", href: "/match-event" },
    { title: "Ubah", href: "/match-event/edit" },
];

const toTimeOnly = (time) => time?.substring(0, 5) ?? "";

const form = useForm({
    group_code: props.match_event?.group_code ?? "",
    period_id: props.match_event?.period_id ?? "",
    coach_id: props.match_event?.coach_id ?? "",
    opponent: props.match_event?.opponent ?? "",
    match_date: props.match_event?.match_date ?? "",
    start_time: toTimeOnly(props.match_event?.start_time) ?? "",
    end_time: toTimeOnly(props.match_event?.end_time) ?? "",
    group_score: props.match_event?.group_score ?? "",
    opponent_score: props.match_event?.opponent_score ?? "",
    location: props.match_event?.location ?? "",
    description: props.match_event?.description ?? "",
    participants:
        props.match_event?.participants?.map((participant) => ({
            student_id: participant.student_id,
            attendance: participant.attendance,
            notes: participant.notes,
        })) ?? [],
});

const addParticipant = () => {
    form.participants.push({ student_id: "" });
};

const removeParticipant = (index) => {
    form.participants.splice(index, 1);
};

const availableStudents = (rowIndex) => {
    // id siswa yang SUDAH dipilih di baris lain
    const pickedIds = form.participants
        .filter((_, i) => i !== rowIndex) // kecuali baris ini sendiri
        .map((p) => p.student_id)
        .filter(Boolean); // buang string kosong

    return props.students.filter((student) => {
        const e = student.current_program;
        return (
            e &&
            e.period_id == form.period_id &&
            e.group_code == form.group_code &&
            !pickedIds.includes(student.id) // belum dipakai
        );
    });
};

const submit = () => {
    form.patch(route("match-event.update", props.match_event.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Ubah Pertandingan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Ubah Pertandingan"
                    description="Formulir untuk mengubah data pertandingan yang telah terdaftar"
                />
            </HeadingGroup>
            <form @submit.prevent="submit">
                <Card>
                    <CardContent>
                        <Heading title="Informasi Pertandingan" />
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-2 gap-y-6 my-4"
                        >
                            <div class="w-full flex flex-col gap-2">
                                <Label for="group_code">Grup</Label>
                                <Select
                                    v-model="form.group_code"
                                    name="group_code"
                                >
                                    <SelectTrigger
                                        id="group_code"
                                        class="w-full"
                                    >
                                        <SelectValue placeholder="Pilih Grup" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem
                                                v-for="(item, index) in groups"
                                                :key="index"
                                                :value="item.code"
                                            >
                                                {{ item.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.group_code" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="period_id">Periode</Label>
                                <Select
                                    v-model="form.period_id"
                                    name="period_id"
                                >
                                    <SelectTrigger
                                        id="period_id"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Pilih Periode"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem
                                                v-for="(item, index) in periods"
                                                :key="index"
                                                :value="item.id"
                                            >
                                                {{ item.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.period_id" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="coach_id">Pelatih</Label>
                                <Select v-model="form.coach_id" name="coach_id">
                                    <SelectTrigger id="coach_id" class="w-full">
                                        <SelectValue
                                            placeholder="Pilih Pelatih"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem
                                                v-for="(item, index) in coaches"
                                                :key="index"
                                                :value="item.id"
                                            >
                                                {{ item.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.coach_id" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="opponent">Nama Tim Lawan</Label>
                                <Input
                                    id="opponent"
                                    type="text"
                                    name="opponent"
                                    placeholder="Input Nama Tim Lawan"
                                    autocomplete="off"
                                    v-model="form.opponent"
                                />
                                <InputError :message="form.errors.opponent" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="match_date"
                                    >Tanggal Pertandingan</Label
                                >
                                <Input
                                    id="match_date"
                                    type="date"
                                    name="match_date"
                                    v-model="form.match_date"
                                />
                                <InputError :message="form.errors.match_date" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="start_time">Waktu Mulai</Label>
                                <Input
                                    id="start_time"
                                    type="time"
                                    name="start_time"
                                    v-model="form.start_time"
                                />
                                <InputError :message="form.errors.start_time" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="end_time">Waktu Selesai</Label>
                                <Input
                                    id="end_time"
                                    type="time"
                                    name="end_time"
                                    v-model="form.end_time"
                                />
                                <InputError :message="form.errors.end_time" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="group_score">Skor Tim</Label>
                                <Input
                                    id="group_score"
                                    type="number"
                                    name="group_score"
                                    placeholder="Input Skor Tim"
                                    v-model="form.group_score"
                                />
                                <InputError
                                    :message="form.errors.group_score"
                                />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="opponent_score"
                                    >Skor Tim Lawan</Label
                                >
                                <Input
                                    id="opponent_score"
                                    type="number"
                                    name="opponent_score"
                                    placeholder="Input Skor Tim Lawan"
                                    v-model="form.opponent_score"
                                />
                                <InputError
                                    :message="form.errors.opponent_score"
                                />
                            </div>
                            <div
                                class="w-full flex flex-col gap-2 md:col-span-2"
                            >
                                <Label for="location"
                                    >Lokasi Pertandingan</Label
                                >
                                <Input
                                    id="location"
                                    type="text"
                                    name="location"
                                    placeholder="Input Lokasi Pertandingan"
                                    autocomplete="off"
                                    v-model="form.location"
                                />
                                <InputError :message="form.errors.location" />
                            </div>
                            <div
                                class="w-full flex flex-col gap-2 md:col-span-2"
                            >
                                <Label for="description">Deskripsi</Label>
                                <Textarea
                                    id="description"
                                    name="description"
                                    placeholder="Input Deskripsi"
                                    autocomplete="off"
                                    v-model="form.description"
                                />
                                <InputError
                                    :message="form.errors.description"
                                />
                            </div>
                        </div>
                        <Separator class="mb-4 mt-5" />
                        <Heading title="Informasi Pemain" />
                        <div class="rounded-md border mb-4">
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Pemain</TableHead>
                                        <TableHead class="w-[15px]">
                                            <Button
                                                type="button"
                                                v-if="
                                                    form.period_id &&
                                                    form.group_code
                                                "
                                                variant="default"
                                                size="sm"
                                                @click.prevent="addParticipant"
                                            >
                                                Tambah
                                            </Button>
                                        </TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <template
                                        v-if="form.participants.length > 0"
                                    >
                                        <TableRow
                                            v-for="(
                                                participant, index_participant
                                            ) in form.participants"
                                            :key="index_participant"
                                        >
                                            <TableCell>
                                                <div
                                                    class="flex flex-col gap-2"
                                                >
                                                    <Select
                                                        v-model="
                                                            form.participants[
                                                                index_participant
                                                            ].student_id
                                                        "
                                                        :name="`student_id_${index_participant}`"
                                                    >
                                                        <SelectTrigger
                                                            class="w-full"
                                                        >
                                                            <SelectValue
                                                                placeholder="Pilih Pemain"
                                                            />
                                                        </SelectTrigger>
                                                        <SelectContent>
                                                            <SelectGroup>
                                                                <SelectItem
                                                                    v-for="(
                                                                        item,
                                                                        index_student
                                                                    ) in availableStudents(
                                                                        index_participant
                                                                    )"
                                                                    :key="
                                                                        index_student
                                                                    "
                                                                    :value="
                                                                        item.id
                                                                    "
                                                                >
                                                                    {{
                                                                        item.name
                                                                    }}
                                                                </SelectItem>
                                                            </SelectGroup>
                                                        </SelectContent>
                                                    </Select>
                                                    <InputError
                                                        :message="
                                                            form.errors[
                                                                `participants.${index_participant}.student_id`
                                                            ]
                                                        "
                                                    />
                                                </div>
                                            </TableCell>
                                            <TableCell>
                                                <Button
                                                    type="button"
                                                    variant="destructive"
                                                    size="sm"
                                                    @click.prevent="
                                                        removeParticipant(
                                                            index_participant
                                                        )
                                                    "
                                                >
                                                    Hapus
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                    <template v-else>
                                        <TableRow>
                                            <TableCell
                                                colspan="4"
                                                class="text-center py-6"
                                            >
                                                <strong>
                                                    Belum ada data
                                                </strong>
                                            </TableCell>
                                        </TableRow>
                                    </template>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                    <CardFooter>
                        <div class="space-x-2">
                            <Button type="submit" :disabled="form.processing">
                                <LoaderCircle
                                    v-if="form.processing"
                                    class="h-4 w-4 animate-spin"
                                />
                                Simpan
                            </Button>
                            <Link
                                v-if="can('match-event-index')"
                                :href="route('match-event.index')"
                                :class="buttonVariants({ variant: 'outline' })"
                                >Kembali</Link
                            >
                        </div>
                    </CardFooter>
                </Card>
            </form>
        </MainContent>
    </AppLayout>
</template>
