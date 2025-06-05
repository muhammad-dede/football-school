<script setup>
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

const props = defineProps({
    periods: Object,
    groups: Object,
    positions: Object,
    student: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Siswa", href: "/student" },
    { title: "Tambah Program", href: "/student/program/create" },
];

const form = useForm({
    period_id: "",
    group_code: "",
    position_code: "",
    alternative_position_code: "",
    jersey_number: "",
    join_date: "",
});

const submit = () => {
    form.post(route("student.program.store", props.student?.id), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Tambah Program" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Tambah Program"
                    description="Formulir untuk menambahkan data program baru"
                />
            </HeadingGroup>
            <form @submit.prevent="submit">
                <Card>
                    <CardContent>
                        <Heading title="Informasi Program" />
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-2 gap-y-6 my-4"
                        >
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
                                <Label for="position_code">Posisi</Label>
                                <Select
                                    v-model="form.position_code"
                                    name="position_code"
                                >
                                    <SelectTrigger
                                        id="position_code"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Pilih Posisi"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem
                                                v-for="(
                                                    item, index
                                                ) in positions"
                                                :key="index"
                                                :value="item.code"
                                            >
                                                {{ item.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="form.errors.position_code"
                                />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="alternative_position_code"
                                    >Posisi Alternatif</Label
                                >
                                <Select
                                    v-model="form.alternative_position_code"
                                    name="alternative_position_code"
                                >
                                    <SelectTrigger
                                        id="alternative_position_code"
                                        class="w-full"
                                    >
                                        <SelectValue
                                            placeholder="Pilih Posisi Alternatif"
                                        />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem
                                                v-for="(
                                                    item, index
                                                ) in positions"
                                                :key="index"
                                                :value="item.code"
                                            >
                                                {{ item.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError
                                    :message="
                                        form.errors.alternative_position_code
                                    "
                                />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="jersey_number"
                                    >Nomor Punggung</Label
                                >
                                <Input
                                    id="jersey_number"
                                    type="number"
                                    name="jersey_number"
                                    placeholder="Input Nomor Punggung"
                                    autocomplete="off"
                                    v-model="form.jersey_number"
                                />
                                <InputError
                                    :message="form.errors.jersey_number"
                                />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="join_date">Tanggal Bergabung</Label>
                                <Input
                                    id="join_date"
                                    type="date"
                                    name="join_date"
                                    v-model="form.join_date"
                                />
                                <InputError :message="form.errors.join_date" />
                            </div>
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
                                :href="route('student.show', student.id)"
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
