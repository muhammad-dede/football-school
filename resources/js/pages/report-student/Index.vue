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
    MoreHorizontal,
    UserCheck,
    CalendarRange,
    Search,
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
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table/index";

import { Avatar, AvatarFallback } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge/index";

import SearchInput from "@/components/SearchInput.vue";
import FilterControl from "@/components/FilterControl.vue";
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
import { Input } from "@/components/ui/input/index";
import { useInitials } from "@/composables/useInitials";
import usePermissions from "@/composables/usePermissions";

const { can } = usePermissions();
const { getInitials } = useInitials();

const props = defineProps({
    periods: Object,
    groups: Object,
    search_term: String,
    group_code_term: String,
    period_id_term: String,
    student_programs: Object,
    report_students: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Nilai Siswa", href: "/report-student" },
];

const search = ref(props.search_term);
const group_code = ref(props.group_code_term);
const period_id = ref(props.period_id_term);

const dataControl = () => {
    router.get(
        route("report-student.index"),
        {
            search: search.value,
            group_code: group_code.value,
            period_id: period_id.value,
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

watch([group_code, period_id], () => {
    dataControl();
});
</script>

<template>
    <Head title="Nilai Siswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Nilai Siswa"
                    description="Lihat dan kelola data nilai siswa yang tersedia"
                />
            </HeadingGroup>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <Select v-model="period_id" name="period_id">
                    <SelectTrigger id="period_id" class="w-full">
                        <SelectValue placeholder="Pilih Periode" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem
                                v-for="(period, index) in periods"
                                :key="index"
                                :value="period.id"
                            >
                                {{ period.name }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
                <Select v-model="group_code" name="group_code">
                    <SelectTrigger id="group_code" class="w-full">
                        <SelectValue placeholder="Pilih Grup" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectItem
                                v-for="(group, index) in groups"
                                :key="index"
                                :value="group.code"
                            >
                                {{ group.name }}
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>
            <div class="flex justify-between items-center gap-4 mb-4">
                <SearchInput v-model="search" />
                <div>
                    <Button type="button">Edit</Button>
                </div>
            </div>
            <div class="rounded-md border mb-4">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Nama Siswa</TableHead>
                            <TableHead>Keterangan</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <template v-if="student_programs.length > 0">
                            <TableRow
                                v-for="(program, index) in student_programs"
                                :key="index"
                            >
                                <TableCell>
                                    {{ program.student?.name ?? "-" }}
                                </TableCell>
                                <TableCell>Coba</TableCell>
                            </TableRow>
                        </template>
                        <template v-else>
                            <TableRow>
                                <TableCell colspan="2" class="text-center py-6">
                                    <strong>Belum ada data</strong>
                                </TableCell>
                            </TableRow>
                        </template>
                    </TableBody>
                </Table>
            </div>
        </MainContent>
    </AppLayout>
</template>
