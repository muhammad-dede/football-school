<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import MainContent from "@/components/MainContent.vue";
import { Card, CardContent } from "@/components/ui/card/index";
import { Button, buttonVariants } from "@/components/ui/button/index";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
    MoreHorizontal,
    Trash2,
    Pencil,
    Undo2,
    AlertCircle,
} from "lucide-vue-next";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import { computed, ref } from "vue";
import usePermissions from "@/composables/usePermissions";
import Lightbox from "@/components/Lightbox.vue";
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
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import TabStudent from "./show/TabStudent.vue";
import TabPrograms from "./show/TabPrograms.vue";
import TabBillings from "./show/TabBillings.vue";
import TabTrainings from "./show/TabTrainings.vue";
import TabMatchEventParticipants from "./show/TabMatchEventParticipants.vue";

const { can } = usePermissions();

const page = usePage();
const flash = page.props.flash;

const props = defineProps({
    attendances: Object,
    student: Object,
    programs: Object,
    billings: Object,
    trainings: Object,
    match_event_participants: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Siswa", href: "/student" },
    { title: "Detail", href: "/student/show" },
];

const dateFormat = (date) => {
    if (!date) return "-";
    const options = { day: "numeric", month: "long", year: "numeric" };
    return new Date(date).toLocaleDateString("id-ID", options);
};

const showPhoto = ref(false);
const togglePhoto = () => {
    showPhoto.value = !showPhoto.value;
};

const showConfirmDelete = ref(false);
const destroy = () => {
    showConfirmDelete.value = false;
    router.delete(route("student.destroy", props.student.id), {
        preserveScroll: true,
    });
};

const hasNoProgram = computed(() => {
    return props.programs.length === 0;
});
const unpaidBilling = computed(() => {
    return props.billings.find((billing) => billing.status === "UNPAID");
});
</script>

<template>
    <Head title="Detail Siswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    :title="`Detail Siswa \&quot;${student.name}\&quot;`"
                    description="Informasi lengkap mengenai siswa yang terdaftar"
                />
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline" class="w-8 h-8 p-0">
                            <MoreHorizontal class="w-4 h-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem v-if="can('student-index')" asChild>
                            <Link :href="route('student.index')">
                                <Undo2 class="text-yellow-500" />
                                Kembali
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem v-if="can('student-edit')" asChild>
                            <Link :href="route('student.edit', student.id)">
                                <Pencil class="text-green-500" />
                                Ubah Data
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            v-if="can('student-delete')"
                            @select="showConfirmDelete = true"
                        >
                            <Trash2 class="text-red-500" />
                            Hapus Data
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </HeadingGroup>
            <Alert
                v-if="hasNoProgram"
                variant="destructive"
                class="mb-4 border-red-300 flex items-center justify-between"
            >
                <div class="flex items-start space-x-4">
                    <AlertCircle class="w-4 h-4 mt-1 text-red-600" />
                    <div>
                        <AlertTitle>Belum Ada Program</AlertTitle>
                        <AlertDescription class="text-red-600">
                            Siswa belum terdaftar di program manapun. Klik
                            Tambah untuk menambahkan program.
                        </AlertDescription>
                    </div>
                </div>
                <Link
                    v-if="can('student-program-create')"
                    :href="route('student.program.create', student.id)"
                    :class="[
                        buttonVariants({ variant: 'default', size: 'sm' }),
                    ]"
                >
                    Tambah
                </Link>
            </Alert>
            <Alert
                v-if="unpaidBilling"
                variant="destructive"
                class="mb-4 border-red-300 flex items-center justify-between"
            >
                <div class="flex items-start space-x-4">
                    <AlertCircle class="w-4 h-4 mt-1 text-red-600" />
                    <div>
                        <AlertTitle>Tagihan Belum Dibayar</AlertTitle>
                        <AlertDescription class="text-red-600">
                            Siswa memiliki tagihan yang belum dibayar.
                        </AlertDescription>
                    </div>
                </div>
                <Link
                    v-if="can('student-payment-create')"
                    :href="route('student.payment.create', unpaidBilling.id)"
                    :class="[
                        buttonVariants({ variant: 'default', size: 'sm' }),
                    ]"
                >
                    Bayar
                </Link>
            </Alert>
            <Tabs :default-value="flash.page ?? 'student'" class="w-full">
                <TabsList class="grid w-full grid-cols-5">
                    <TabsTrigger value="student">Biodata</TabsTrigger>
                    <TabsTrigger value="programs">Program</TabsTrigger>
                    <TabsTrigger value="billings">Tagihan</TabsTrigger>
                    <TabsTrigger value="trainings">Pelatihan</TabsTrigger>
                    <TabsTrigger value="match_events">Pertandingan</TabsTrigger>
                </TabsList>
                <TabsContent value="student">
                    <TabStudent
                        :student="student"
                        :dateFormat="dateFormat"
                        :togglePhoto="togglePhoto"
                    />
                </TabsContent>
                <TabsContent value="programs">
                    <TabPrograms
                        :student="student"
                        :programs="programs"
                        :dateFormat="dateFormat"
                    />
                </TabsContent>
                <TabsContent value="billings">
                    <TabBillings
                        :student="student"
                        :billings="billings"
                        :dateFormat="dateFormat"
                    />
                </TabsContent>
                <TabsContent value="trainings">
                    <TabTrainings
                        :student="student"
                        :trainings="trainings"
                        :attendances="attendances"
                        :dateFormat="dateFormat"
                    />
                </TabsContent>
                <TabsContent value="match_events">
                    <TabMatchEventParticipants
                        :student="student"
                        :match_event_participants="match_event_participants"
                        :attendances="attendances"
                        :dateFormat="dateFormat"
                    />
                </TabsContent>
            </Tabs>
        </MainContent>
    </AppLayout>

    <Lightbox
        :show="showPhoto"
        :image-url="student.photo_url"
        @close="togglePhoto"
    />

    <AlertDialog :open="!!showConfirmDelete">
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
                <AlertDialogCancel @click="showConfirmDelete = false">
                    Batal
                </AlertDialogCancel>
                <AlertDialogAction @click="destroy">Hapus</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
