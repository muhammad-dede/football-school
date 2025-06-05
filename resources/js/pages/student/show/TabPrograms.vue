<script setup>
import { Link, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { Card, CardContent } from "@/components/ui/card";
import InfoItem from "@/components/InfoItem.vue";
import { Badge } from "@/components/ui/badge";
import { Button, buttonVariants } from "@/components/ui/button/index";
import { Avatar, AvatarFallback } from "@/components/ui/avatar";
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
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
    CalendarDays,
    Group,
    LocateFixed,
    Locate,
    Shirt,
    MoreHorizontal,
} from "lucide-vue-next";
import usePermissions from "@/composables/usePermissions";

const { can } = usePermissions();

const props = defineProps({
    student: Object,
    programs: Object,
    dateFormat: Function,
});

const programToDelete = ref(null);
const confirmDelete = (program) => {
    programToDelete.value = program;
};
const destroy = () => {
    if (!programToDelete.value) return;
    const programId = programToDelete.value.id;
    router.delete(route("student.program.destroy", programId), {
        preserveScroll: true,
        onFinish: () => {
            programToDelete.value = null;
        },
    });
};
</script>

<template>
    <template v-if="programs.length > 0">
        <Card v-for="(item, index) in programs" :key="item.id" class="py-0">
            <CardContent class="px-4 grid divide-y divide-gray-100">
                <div class="flex justify-between items-center">
                    <InfoItem
                        label="Tanggal Bergabung"
                        :value="dateFormat(item.join_date)"
                        :icon="CalendarDays"
                        background
                    />
                    <Badge
                        :variant="item.is_active ? 'default' : 'destructive'"
                        class="px-3 py-2 rounded-full h-fit"
                    >
                        {{ item.is_active ? "Aktif" : "Tidak Aktif" }}
                    </Badge>
                </div>
                <div class="flex relative overflow-hidden">
                    <div class="absolute top-3.5 left-0">
                        <Avatar class="size-12 border">
                            <AvatarFallback>
                                {{ index + 1 }}
                            </AvatarFallback>
                        </Avatar>
                    </div>
                    <div
                        class="pl-18 w-[85%] flex flex-col justify-between lg:flex-row lg:items-center lg:gap-4"
                    >
                        <InfoItem
                            :label="item.group?.name"
                            :value="item.period?.name"
                            :icon="Group"
                        />
                        <InfoItem
                            reverse
                            label="Periode"
                            :value="`${dateFormat(
                                item.period?.start_date
                            )} - ${dateFormat(item.period?.end_date)}`"
                            :icon="CalendarDays"
                        />
                    </div>
                    <div class="absolute top-5.5 right-0">
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline" class="w-8 h-8 p-0">
                                    <MoreHorizontal class="w-4 h-4" />
                                </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuLabel> Aksi </DropdownMenuLabel>
                                <DropdownMenuSeparator />
                                <DropdownMenuItem
                                    asChild
                                    v-if="can('student-program-edit')"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'student.program.edit',
                                                item.id
                                            )
                                        "
                                    >
                                        Ubah
                                    </Link>
                                </DropdownMenuItem>
                                <DropdownMenuItem
                                    v-if="can('student-program-delete')"
                                    @select="() => confirmDelete(item)"
                                >
                                    Hapus
                                </DropdownMenuItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
                <div class="flex flex-col justify-between gap-x-4 lg:flex-row">
                    <InfoItem
                        label="Posisi"
                        :value="item.position?.name"
                        :icon="LocateFixed"
                        background
                    />
                    <InfoItem
                        label="Posisi Alternatif"
                        :value="item.alternative_position?.name"
                        :icon="Locate"
                        background
                    />
                    <InfoItem
                        label="Nomor Punggung"
                        :value="`${item.jersey_number ?? '-'}`"
                        :icon="Shirt"
                        background
                    />
                </div>
            </CardContent>
        </Card>
    </template>
    <template v-else>
        <div class="p-8 text-center flex flex-col gap-4">
            <span class="text-base font-semibold">
                Tidak ada data ditemukan
            </span>
            <div v-if="can('student-program-create')">
                <Link
                    :href="route('student.program.create', student.id)"
                    :class="buttonVariants({ variant: 'default' })"
                >
                    Tambah Program
                </Link>
            </div>
        </div>
    </template>
    <AlertDialog :open="!!programToDelete">
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
                <AlertDialogCancel @click="programToDelete = null">
                    Batal
                </AlertDialogCancel>
                <AlertDialogAction @click="destroy">Hapus</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
