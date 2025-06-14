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
import { Textarea } from "@/components/ui/textarea";
import usePermissions from "@/composables/usePermissions";

const { can } = usePermissions();

const props = defineProps({
    groups: Object,
    periods: Object,
    coaches: Object,
    training: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Pelatihan", href: "/training" },
    { title: "Ubah", href: "/training/edit" },
];

const toTimeOnly = (time) => time?.substring(0, 5) ?? "";

const form = useForm({
    group_code: props.training.group_code ?? "",
    period_id: props.training.period_id ?? "",
    coach_id: props.training.coach_id ?? "",
    training_date: props.training.training_date ?? "",
    start_time: toTimeOnly(props.training.start_time),
    end_time: toTimeOnly(props.training.end_time),
    location: props.training.location ?? "",
    description: props.training.description ?? "",
});

const submit = () => {
    form.patch(route("training.update", props.training?.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Ubah Pelatihan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Ubah Pelatihan"
                    description="Formulir untuk mengubah data pelatihan yang telah terdaftar"
                />
            </HeadingGroup>
            <form @submit.prevent="submit">
                <Card>
                    <CardContent>
                        <Heading title="Informasi Pelatihan" />
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-2 gap-y-6 my-4"
                        >
                            <div class="w-full flex flex-col gap-2">
                                <Label for="group_code">Grup</Label>
                                <Select
                                    v-model="form.group_code"
                                    name="group_code"
                                    disabled
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
                                    disabled
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
                                <Label for="training_date"
                                    >Tanggal Pelatihan</Label
                                >
                                <Input
                                    id="training_date"
                                    type="date"
                                    name="training_date"
                                    v-model="form.training_date"
                                />
                                <InputError
                                    :message="form.errors.training_date"
                                />
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
                            <div
                                class="w-full flex flex-col gap-2 md:col-span-2"
                            >
                                <Label for="location">Lokasi Pelatihan</Label>
                                <Input
                                    id="location"
                                    type="text"
                                    name="location"
                                    placeholder="Input Lokasi Pelatihan"
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
                                v-if="can('training-show')"
                                :href="route('training.show', training.id)"
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
