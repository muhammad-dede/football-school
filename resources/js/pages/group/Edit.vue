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
    group: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Grup", href: "/group" },
    { title: "Ubah", href: "/group/edit" },
];

const form = useForm({
    code: props.group?.code ?? "",
    name: props.group?.name ?? "",
    age_min: props.group?.age_min ?? "",
    age_max: props.group?.age_max ?? "",
    description: props.group?.description ?? "",
});

const submit = () => {
    form.patch(route("group.update", props.group?.id));
};
</script>

<template>
    <Head title="Ubah Grup" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Ubah Grup"
                    description="Formulir untuk mengubah data grup yang telah terdaftar"
                />
            </HeadingGroup>
            <form @submit.prevent="submit">
                <Card>
                    <CardContent>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-2 gap-y-6 mb-4"
                        >
                            <div class="w-full flex flex-col gap-2">
                                <Label for="code">Kode</Label>
                                <Input
                                    id="code"
                                    type="text"
                                    name="code"
                                    placeholder="Input Kode"
                                    autocomplete="off"
                                    v-model="form.code"
                                />
                                <InputError :message="form.errors.code" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
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
                                <Label for="age_min">Minimal Usia</Label>
                                <Input
                                    id="age_min"
                                    type="number"
                                    name="age_min"
                                    placeholder="Input Minimal Usia"
                                    autocomplete="off"
                                    v-model="form.age_min"
                                />
                                <InputError :message="form.errors.age_min" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="age_max">Maksimal Usia</Label>
                                <Input
                                    id="age_max"
                                    type="number"
                                    name="age_max"
                                    placeholder="Input Maksimal Usia"
                                    autocomplete="off"
                                    v-model="form.age_max"
                                />
                                <InputError :message="form.errors.age_max" />
                            </div>
                            <div
                                class="w-full flex flex-col gap-2 md:col-span-2"
                            >
                                <Label for="description">Deskripsi</Label>
                                <Input
                                    id="description"
                                    type="text"
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
                                :href="route('group.index')"
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
