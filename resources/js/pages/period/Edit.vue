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

const props = defineProps({
    period: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Periode", href: "/period" },
    { title: "Ubah", href: "/period/edit" },
];

const form = useForm({
    name: props.period?.name ?? "",
    start_date: props.period?.start_date ?? "",
    end_date: props.period?.end_date ?? "",
});

const submit = () => {
    form.patch(route("period.update", props.period?.id));
};
</script>

<template>
    <Head title="Ubah Periode" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Ubah Periode"
                    description="Formulir untuk mengubah data periode yang telah terdaftar"
                />
            </HeadingGroup>
            <form @submit.prevent="submit">
                <Card>
                    <CardContent>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-2 gap-y-6 mb-4"
                        >
                            <div
                                class="w-full flex flex-col gap-2 md:col-span-2"
                            >
                                <Label for="name">Nama</Label>
                                <Input
                                    id="name"
                                    type="text"
                                    name="name"
                                    placeholder="Input Nama"
                                    autocomplete="off"
                                    v-model="form.name"
                                />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="start_date">Tanggal Mulai</Label>
                                <Input
                                    id="start_date"
                                    type="date"
                                    name="start_date"
                                    autocomplete="off"
                                    v-model="form.start_date"
                                />
                                <InputError :message="form.errors.start_date" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="end_date">Tanggal Berakhir</Label>
                                <Input
                                    id="end_date"
                                    type="date"
                                    name="end_date"
                                    autocomplete="off"
                                    v-model="form.end_date"
                                />
                                <InputError :message="form.errors.end_date" />
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
                                :href="route('period.index')"
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
