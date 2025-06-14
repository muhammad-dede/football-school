<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import MainContent from "@/components/MainContent.vue";
import { Card, CardContent, CardFooter } from "@/components/ui/card/index";
import { Label } from "@/components/ui/label/index";
import { Input } from "@/components/ui/input/index";
import InputError from "@/components/InputError.vue";
import { LoaderCircle } from "lucide-vue-next";
import { Button, buttonVariants } from "@/components/ui/button/index";
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
    roles: Object,
    user: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Pengguna", href: "/user" },
    { title: "Ubah", href: "/user/edit" },
];

const form = useForm({
    name: props.user.name ?? "",
    email: props.user.email ?? "",
    password: "",
    password_confirmation: "",
    role: props.user.roles[0].name ?? "",
});

const submit = () => {
    form.patch(route("user.update", props.user.id));
};
</script>

<template>
    <Head title="Ubah Pengguna" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Ubah Pengguna"
                    description="Formulir untuk mengubah data pengguna yang telah terdaftar"
                />
            </HeadingGroup>
            <form @submit.prevent="submit">
                <Card>
                    <CardContent>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-x-2 gap-y-6 mb-4"
                        >
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
                                <Label for="email">Email</Label>
                                <Input
                                    id="email"
                                    type="email"
                                    name="email"
                                    placeholder="Input Email"
                                    autocomplete="off"
                                    v-model="form.email"
                                />
                                <InputError :message="form.errors.email" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="password">Password</Label>
                                <Input
                                    id="password"
                                    type="password"
                                    name="password"
                                    placeholder="Input Password"
                                    autocomplete="off"
                                    v-model="form.password"
                                />
                                <InputError :message="form.errors.password" />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="password_confirmation"
                                    >Konfirmasi Password</Label
                                >
                                <Input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    placeholder="Input Konfirmasi Password"
                                    autocomplete="off"
                                    v-model="form.password_confirmation"
                                />
                            </div>
                            <div class="w-full flex flex-col gap-2">
                                <Label for="role">Role</Label>
                                <Select v-model="form.role" name="role">
                                    <SelectTrigger id="role" class="w-full">
                                        <SelectValue placeholder="Pilih Role" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            <SelectItem
                                                v-for="role in roles"
                                                :key="role.id"
                                                :value="role.name"
                                            >
                                                {{ role.name }}
                                            </SelectItem>
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.role" />
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
                                :href="route('user.index')"
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
