<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { debounce } from "lodash";

import AppLayout from "@/layouts/AppLayout.vue";
import MainContent from "@/components/MainContent.vue";
import PaginationLinks from "@/components/PaginationLinks.vue";
import { Card, CardContent } from "@/components/ui/card/index";
import { Button, buttonVariants } from "@/components/ui/button/index";
import {
    SquarePlus,
    Mail,
    Phone,
    MoreHorizontal,
    UserCheck,
} from "lucide-vue-next";

import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from "@/components/ui/alert-dialog";

import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge/index";

import SearchInput from "@/components/SearchInput.vue";
import FilterControl from "@/components/FilterControl.vue";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import { useInitials } from "@/composables/useInitials";
import usePermissions from "@/composables/usePermissions";

const { getInitials } = useInitials();
const { can } = usePermissions();

const props = defineProps({
    students: Object,
    search_term: String,
    per_page_term: String,
    filter_term: String,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Siswa", href: "/student" },
];

const search = ref(props.search_term);
const perPage = ref(props.per_page_term);
const filter = ref(props.filter_term);
const studentToDelete = ref(null);

const dataControl = () => {
    router.get(
        route("student.index"),
        {
            search: search.value,
            per_page: perPage.value,
            filter: filter.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

watch(
    search,
    debounce(() => {
        dataControl();
    }, 1000)
);

watch([perPage, filter], () => {
    dataControl();
});

const confirmDelete = (student) => {
    studentToDelete.value = student;
};

const destroy = () => {
    if (!studentToDelete.value) return;
    const studentId = studentToDelete.value.id;
    router.delete(route("student.destroy", studentId), {
        preserveScroll: true,
        onFinish: () => {
            studentToDelete.value = null; // Memastikan lagi
        },
    });
};
</script>

<template>
    <Head title="Siswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Data Siswa"
                    description="Lihat dan kelola data siswa yang tersedia"
                />
                <Link
                    v-if="can('student-create')"
                    :href="route('student.create')"
                    :class="buttonVariants({ variant: 'default' })"
                >
                    <SquarePlus class="w-4 h-4" />Tambah
                </Link>
            </HeadingGroup>
            <div class="flex justify-between items-center gap-4 mb-4">
                <SearchInput v-model="search" />
                <FilterControl
                    :per-page="perPage"
                    :filter="filter"
                    @update:per-page="perPage = $event"
                    @update:filter="filter = $event"
                />
            </div>
            <div class="flex flex-col gap-4 mb-2">
                <template v-if="students.data.length > 0">
                    <Card
                        v-for="item in students.data"
                        :key="item.id"
                        class="py-4"
                    >
                        <CardContent class="px-4">
                            <div
                                class="flex lg:items-center gap-4 relative overflow-hidden items-start"
                            >
                                <Avatar class="size-12">
                                    <AvatarImage
                                        :src="item.photo_url"
                                        alt="photo"
                                    />
                                    <AvatarFallback>
                                        {{ getInitials(item.name) }}
                                    </AvatarFallback>
                                </Avatar>
                                <div
                                    class="flex flex-col items-start gap-2 lg:flex-row lg:items-center lg:gap-4"
                                >
                                    <div
                                        class="flex flex-col lg:min-w-xs lg:max-w-sm"
                                    >
                                        <h5 class="font-bold">
                                            {{ item.name }}
                                        </h5>
                                        <div
                                            class="flex items-center font-semibold gap-1 text-gray-500 text-sm"
                                        >
                                            <Mail class="size-4" />
                                            {{ item.user?.email }}
                                        </div>
                                    </div>
                                    <Badge
                                        variant="outline"
                                        class="p-2 rounded-full h-fit"
                                    >
                                        <Phone />
                                        {{ item.phone }}
                                    </Badge>
                                    <Badge
                                        :variant="
                                            item.enrollment?.is_active
                                                ? 'secondary'
                                                : 'destructive'
                                        "
                                        class="p-2 rounded-full h-fit"
                                    >
                                        <UserCheck />
                                        {{
                                            item.is_active
                                                ? "Aktif"
                                                : "Tidak Aktif"
                                        }}
                                    </Badge>
                                </div>
                                <div
                                    class="absolute top-0 right-0 flex items-start justify-center h-full lg:items-center"
                                >
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="outline"
                                                class="w-8 h-8 p-0"
                                            >
                                                <MoreHorizontal
                                                    class="w-4 h-4"
                                                />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent align="end">
                                            <DropdownMenuLabel>
                                                Aksi
                                            </DropdownMenuLabel>
                                            <DropdownMenuSeparator />
                                            <DropdownMenuItem
                                                v-if="can('student-edit')"
                                                asChild
                                            >
                                                <Link
                                                    :href="
                                                        route(
                                                            'student.edit',
                                                            item.id
                                                        )
                                                    "
                                                >
                                                    Ubah
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                v-if="can('student-show')"
                                                asChild
                                            >
                                                <Link
                                                    :href="
                                                        route(
                                                            'student.show',
                                                            item.id
                                                        )
                                                    "
                                                >
                                                    Detail
                                                </Link>
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                v-if="can('student-delete')"
                                                @select="
                                                    () => confirmDelete(item)
                                                "
                                            >
                                                Hapus
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
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
                        <div v-if="can('student-create')">
                            <Link
                                :href="route('student.create')"
                                :class="buttonVariants({ variant: 'default' })"
                                ><SquarePlus class="w-4 h-4" />Tambah
                                Siswa</Link
                            >
                        </div>
                    </div>
                </template>
            </div>
            <PaginationLinks :paginator="students" />
        </MainContent>
    </AppLayout>
    <AlertDialog :open="!!studentToDelete">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>
                    Apakah Anda benar-benar yakin?
                </AlertDialogTitle>
                <AlertDialogDescription>
                    Tindakan ini tidak dapat dibatalkan. Ini akan secara
                    permanen menghapus data terkait dari server kami.
                </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel @click="studentToDelete = null">
                    Batal
                </AlertDialogCancel>
                <AlertDialogAction @click="destroy">Hapus</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
