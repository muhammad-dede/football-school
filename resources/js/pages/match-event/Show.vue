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
import TabMatchEvent from "./show/TabMatchEvent.vue";
import TabParticipant from "./show/TabParticipant.vue";

const { can } = usePermissions();

const props = defineProps({
    match_event: Object,
    attendances: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Pertandingan", href: "/match-event" },
    { title: "Detail", href: "/match-event/show" },
];

const showConfirmDelete = ref(false);
const destroy = () => {
    showConfirmDelete.value = false;
    router.delete(route("match-event.destroy", props.match_event.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Detail Pertandingan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Detail Pertandingan"
                    description="Informasi lengkap mengenai pertandingan yang terdaftar"
                />
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button variant="outline" class="w-8 h-8 p-0">
                            <MoreHorizontal class="w-4 h-4" />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem
                            v-if="can('match-event-index')"
                            asChild
                        >
                            <Link :href="route('match-event.index')">
                                <Undo2 class="text-yellow-500" />
                                Kembali
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            v-if="can('match-event-edit')"
                            asChild
                        >
                            <Link
                                :href="
                                    route('match-event.edit', match_event.id)
                                "
                            >
                                <Pencil class="text-green-500" />
                                Ubah Data
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem
                            v-if="can('match-event-delete')"
                            @select="showConfirmDelete = true"
                        >
                            <Trash2 class="text-red-500" />
                            Hapus Data
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </HeadingGroup>
            <Tabs default-value="match_event" class="w-full">
                <TabsList class="grid w-full grid-cols-2">
                    <TabsTrigger value="match_event">Pertandingan</TabsTrigger>
                    <TabsTrigger value="participant">Pemain</TabsTrigger>
                </TabsList>
                <TabsContent value="match_event">
                    <TabMatchEvent :match_event="match_event" />
                </TabsContent>
                <TabsContent value="participant">
                    <TabParticipant
                        :match_event="match_event"
                        :attendances="attendances"
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
