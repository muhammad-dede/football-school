<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import MainContent from "@/components/MainContent.vue";
import { Button } from "@/components/ui/button/index";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { MoreHorizontal, Trash2, Pencil, Undo2 } from "lucide-vue-next";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import { ref } from "vue";
import usePermissions from "@/composables/usePermissions";
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
import TabTraining from "./show/TabTraining.vue";
import TabAttendance from "./show/TabAttendance.vue";

const { can } = usePermissions();

const props = defineProps({
    training: Object,
    attendances: Object,
    students: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Pelatihan", href: "/training" },
    { title: "Detail", href: "/training/show" },
];

const showConfirmDelete = ref(false);
const destroy = () => {
    showConfirmDelete.value = false;
    router.delete(route("training.destroy", props.training.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Detail Pelatihan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Detail Pelatihan"
                    description="Informasi lengkap mengenai pelatihan yang terdaftar"
                />
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline" class="w-8 h-8 p-0">
                            <MoreHorizontal class="w-4 h-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem v-if="can('training-index')" asChild>
                            <Link :href="route('training.index')">
                                <Undo2 class="text-yellow-500" />
                                Kembali
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem v-if="can('training-edit')" asChild>
                            <Link :href="route('training.edit', training.id)">
                                <Pencil class="text-green-500" />
                                Ubah Data
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            v-if="can('training-delete')"
                            @select="showConfirmDelete = true"
                        >
                            <Trash2 class="text-red-500" />
                            Hapus Data
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </HeadingGroup>
            <Tabs default-value="training" class="w-full">
                <TabsList class="grid w-full grid-cols-2">
                    <TabsTrigger value="training">Pelatihan</TabsTrigger>
                    <TabsTrigger value="attendance">
                        Kehadiran Siswa
                    </TabsTrigger>
                </TabsList>
                <TabsContent value="training">
                    <TabTraining :training="training" />
                </TabsContent>
                <TabsContent value="attendance">
                    <TabAttendance
                        :training="training"
                        :attendances="attendances"
                        :students="students"
                    />
                </TabsContent>
            </Tabs>
        </MainContent>
    </AppLayout>

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
