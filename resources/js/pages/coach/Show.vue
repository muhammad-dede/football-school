<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import MainContent from "@/components/MainContent.vue";
import { Card, CardContent } from "@/components/ui/card/index";
import { buttonVariants } from "@/components/ui/button/index";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
    Tag,
    Phone,
    Calendar,
    UserCheck,
    Mail,
    Mars,
    MapPinCheck,
    IdCard,
    CreditCard,
    FileDigit,
    Landmark,
} from "lucide-vue-next";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import DetailItem from "@/components/DetailItem.vue";
import { ref } from "vue";
import Lightbox from "@/components/Lightbox.vue";
import usePermissions from "@/composables/usePermissions";

const { can } = usePermissions();

const props = defineProps({
    coach: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Pelatih", href: "/coach" },
    { title: "Detail", href: "/coach/show" },
];

function dateFormat(date) {
    if (!date) return "-";

    const options = { day: "numeric", month: "long", year: "numeric" };
    return new Date(date).toLocaleDateString("id-ID", options);
}

const showPhoto = ref(false);
const togglePhoto = () => {
    showPhoto.value = !showPhoto.value;
};
</script>

<template>
    <Head title="Detail Pelatih" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Detail Pelatih"
                    description="Informasi lengkap mengenai pelatih yang terdaftar"
                />
                <Link
                    :href="route('coach.index')"
                    :class="buttonVariants({ variant: 'outline' })"
                >
                    Kembali
                </Link>
            </HeadingGroup>
            <div class="flex flex-col lg:flex-row gap-4">
                <div
                    class="flex flex-col gap-2 p-4 border rounded-lg w-fit h-fit shrink-0 mx-auto lg:mx-0"
                >
                    <template v-if="coach.photo_url">
                        <img
                            :src="coach.photo_url"
                            alt="Preview"
                            class="h-48 w-48 object-cover rounded-lg border cursor-pointer"
                            @click="togglePhoto"
                        />
                    </template>
                    <template v-else>
                        <div
                            class="h-48 w-48 flex items-center justify-center border rounded-lg text-gray-500 text-xs"
                        >
                            Belum ada gambar
                        </div>
                    </template>
                    <Link
                        v-if="can('coach-edit')"
                        :href="route('coach.edit', coach.id)"
                        :class="[
                            buttonVariants({
                                variant: 'secondary',
                                size: 'sm',
                            }),
                            'w-full',
                        ]"
                        >Ubah</Link
                    >
                </div>
                <div class="w-full">
                    <Tabs default-value="biodata" class="w-full">
                        <TabsList class="grid w-full grid-cols-3">
                            <TabsTrigger value="biodata">Pribadi</TabsTrigger>
                            <TabsTrigger value="license"
                                >Kepelatihan</TabsTrigger
                            >
                            <TabsTrigger value="account">Akun</TabsTrigger>
                        </TabsList>
                        <TabsContent value="biodata">
                            <Card>
                                <CardContent>
                                    <Heading title="Informasi Pribadi" />
                                    <div class="grid gap-6 my-4">
                                        <DetailItem
                                            label="Nama Lengkap"
                                            :value="coach.name"
                                            :icon="Tag"
                                            icon-color="text-blue-600"
                                            bg-color="bg-blue-100"
                                        />
                                        <DetailItem
                                            label="Tempat, Tanggal Lahir"
                                            :value="
                                                `${coach.place_of_birth}` +
                                                ', ' +
                                                `${dateFormat(
                                                    coach.date_of_birth
                                                )}`
                                            "
                                            :icon="Calendar"
                                            icon-color="text-yellow-600"
                                            bg-color="bg-yellow-100"
                                        />
                                        <DetailItem
                                            label="Jenis Kelamin"
                                            :value="
                                                coach.gender === 'L'
                                                    ? 'Laki-laki'
                                                    : 'Perempuan'
                                            "
                                            :icon="Mars"
                                            icon-color="text-gray-600"
                                            bg-color="bg-gray-100"
                                        />
                                        <DetailItem
                                            label="Alamat"
                                            :value="coach.address"
                                            :icon="MapPinCheck"
                                            icon-color="text-red-600"
                                            bg-color="bg-red-100"
                                        />
                                        <DetailItem
                                            label="Telepon"
                                            :value="coach.phone"
                                            :icon="Phone"
                                            icon-color="text-green-600"
                                            bg-color="bg-green-100"
                                        />
                                        <DetailItem
                                            label="No. Identitas"
                                            :value="coach.national_id_number"
                                            :icon="IdCard"
                                            icon-color="text-teal-600"
                                            bg-color="bg-teal-100"
                                        />
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>
                        <TabsContent value="license">
                            <Card>
                                <CardContent>
                                    <Heading title="Informasi Kepelatihan" />
                                    <div class="grid gap-6 my-4">
                                        <DetailItem
                                            label="Lisensi Kepelatihan"
                                            :value="
                                                coach.coaching_license ?? '-'
                                            "
                                            :icon="CreditCard"
                                            icon-color="text-lime-600"
                                            bg-color="bg-lime-100"
                                        />
                                        <DetailItem
                                            label="Nomor Lisensi"
                                            :value="coach.license_number ?? '-'"
                                            :icon="FileDigit"
                                            icon-color="text-purple-600"
                                            bg-color="bg-purple-100"
                                        />
                                        <DetailItem
                                            label="Tanggal Terbit"
                                            :value="
                                                dateFormat(
                                                    coach.license_issued_at
                                                ) ?? '-'
                                            "
                                            :icon="Calendar"
                                            icon-color="text-rose-600"
                                            bg-color="bg-rose-100"
                                        />
                                        <DetailItem
                                            label="Tanggal Berakhir"
                                            :value="
                                                dateFormat(
                                                    coach.license_expired_at
                                                ) ?? '-'
                                            "
                                            :icon="Calendar"
                                            icon-color="text-stone-600"
                                            bg-color="bg-stone-100"
                                        />
                                        <DetailItem
                                            label="Lembaga Kepelatihan"
                                            :value="coach.license_issuer ?? '-'"
                                            :icon="Landmark"
                                            icon-color="text-sky-600"
                                            bg-color="bg-sky-100"
                                        />
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>
                        <TabsContent value="account">
                            <Card>
                                <CardContent>
                                    <Heading title="Informasi Akun" />
                                    <div class="grid gap-6 my-4">
                                        <DetailItem
                                            label="Email"
                                            :value="coach.user?.email"
                                            :icon="Mail"
                                            icon-color="text-pink-600"
                                            bg-color="bg-pink-100"
                                        />
                                        <DetailItem
                                            label="Status"
                                            :value="
                                                coach.is_active
                                                    ? 'Aktif'
                                                    : 'Tidak Aktif'
                                            "
                                            :icon="UserCheck"
                                            icon-color="text-cyan-600"
                                            bg-color="bg-cyan-100"
                                        />
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>
                    </Tabs>
                </div>
            </div>
        </MainContent>
    </AppLayout>

    <Lightbox
        :show="showPhoto"
        :image-url="coach.photo_url"
        @close="togglePhoto"
    />
</template>
