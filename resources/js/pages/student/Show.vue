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
    FileDigit,
    Footprints,
    Ruler,
    Weight,
    Group,
    LandPlot,
} from "lucide-vue-next";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import HeadingSmall from "@/components/HeadingSmall.vue";
import DetailItem from "@/components/DetailItem.vue";
import { ref } from "vue";
import usePermissions from "@/composables/usePermissions";
import Lightbox from "@/components/Lightbox.vue";

const { can } = usePermissions();

const props = defineProps({
    student: Object,
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Siswa", href: "/student" },
    { title: "Detail", href: "/student/show" },
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
    <Head title="Detail Siswa" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Detail Siswa"
                    description="Informasi lengkap mengenai siswa yang terdaftar"
                />
                <Link
                    :href="route('student.index')"
                    :class="buttonVariants({ variant: 'outline' })"
                >
                    Kembali
                </Link>
            </HeadingGroup>
            <div class="flex flex-col lg:flex-row gap-4">
                <div
                    class="flex flex-col gap-2 p-4 border rounded-lg w-fit h-fit shrink-0 mx-auto lg:mx-0"
                >
                    <template v-if="student.photo_url">
                        <img
                            :src="student.photo_url"
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
                        v-if="can('student-edit')"
                        :href="route('student.edit', student.id)"
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
                            <TabsTrigger value="enrollment"
                                >Kelompok</TabsTrigger
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
                                            :value="student.name"
                                            :icon="Tag"
                                            icon-color="text-blue-600"
                                            bg-color="bg-blue-100"
                                        />
                                        <DetailItem
                                            label="Tempat, Tanggal Lahir"
                                            :value="
                                                `${student.place_of_birth}` +
                                                ', ' +
                                                `${dateFormat(
                                                    student.date_of_birth
                                                )}`
                                            "
                                            :icon="Calendar"
                                            icon-color="text-yellow-600"
                                            bg-color="bg-yellow-100"
                                        />
                                        <DetailItem
                                            label="Jenis Kelamin"
                                            :value="
                                                student.gender === 'L'
                                                    ? 'Laki-laki'
                                                    : 'Perempuan'
                                            "
                                            :icon="Mars"
                                            icon-color="text-gray-600"
                                            bg-color="bg-gray-100"
                                        />
                                        <DetailItem
                                            label="Alamat"
                                            :value="student.address"
                                            :icon="MapPinCheck"
                                            icon-color="text-red-600"
                                            bg-color="bg-red-100"
                                        />
                                        <DetailItem
                                            label="Telepon"
                                            :value="student.phone"
                                            :icon="Phone"
                                            icon-color="text-green-600"
                                            bg-color="bg-green-100"
                                        />
                                        <DetailItem
                                            label="No. Identitas"
                                            :value="student.national_id_number"
                                            :icon="IdCard"
                                            icon-color="text-teal-600"
                                            bg-color="bg-teal-100"
                                        />
                                        <DetailItem
                                            label="Kaki Dominan"
                                            :value="student.dominant_foot"
                                            :icon="Footprints"
                                            icon-color="text-lime-600"
                                            bg-color="bg-lime-100"
                                        />
                                        <DetailItem
                                            label="Tinggi Badan"
                                            :value="`${
                                                student.height_cm ?? '-'
                                            } Centimeter`"
                                            :icon="Ruler"
                                            icon-color="text-purple-600"
                                            bg-color="bg-purple-100"
                                        />
                                        <DetailItem
                                            label="Berat Badan"
                                            :value="`${
                                                student.weight_kg ?? '-'
                                            } Kg`"
                                            :icon="Weight"
                                            icon-color="text-rose-600"
                                            bg-color="bg-rose-100"
                                        />
                                    </div>
                                </CardContent>
                            </Card>
                        </TabsContent>
                        <TabsContent value="enrollment">
                            <Card>
                                <CardContent>
                                    <Heading title="Informasi Kelompok" />
                                    <div
                                        v-for="(
                                            enrollment, index
                                        ) in student.enrollments"
                                        :key="index"
                                        class="border rounded-lg p-4"
                                    >
                                        <HeadingSmall
                                            :title="enrollment.period?.name"
                                        />
                                        <div
                                            class="grid xl:grid-cols-2 gap-6 mt-4"
                                        >
                                            <DetailItem
                                                label="Grup"
                                                :value="enrollment.group?.name"
                                                :icon="Group"
                                                icon-color="text-pink-600"
                                                bg-color="bg-pink-100"
                                            />
                                            <DetailItem
                                                label="Posisi Bermain"
                                                :value="
                                                    enrollment.position?.name
                                                "
                                                :icon="LandPlot"
                                                icon-color="text-orange-600"
                                                bg-color="bg-orange-100"
                                            />
                                            <DetailItem
                                                label="Alternatif Posisi"
                                                :value="
                                                    enrollment
                                                        .alternative_position
                                                        ?.name
                                                "
                                                :icon="LandPlot"
                                                icon-color="text-teal-600"
                                                bg-color="bg-teal-100"
                                            />
                                            <DetailItem
                                                label="Nomor Punggung"
                                                :value="`${
                                                    enrollment.jersey_number ??
                                                    ''
                                                }`"
                                                :icon="FileDigit"
                                                icon-color="text-sky-600"
                                                bg-color="bg-sky-100"
                                            />
                                            <DetailItem
                                                label="Tanggal Bergabung"
                                                :value="
                                                    dateFormat(
                                                        enrollment.join_date
                                                    )
                                                "
                                                :icon="Calendar"
                                                icon-color="text-fuchsia-600"
                                                bg-color="bg-fuchsia-100"
                                            />
                                            <DetailItem
                                                label="Status"
                                                :value="
                                                    enrollment.is_active
                                                        ? 'Aktif'
                                                        : 'Tidak Aktif'
                                                "
                                                :icon="UserCheck"
                                                icon-color="text-cyan-600"
                                                bg-color="bg-cyan-100"
                                            />
                                        </div>
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
                                            :value="student.user?.email"
                                            :icon="Mail"
                                            icon-color="text-pink-600"
                                            bg-color="bg-pink-100"
                                        />
                                        <DetailItem
                                            label="Status"
                                            :value="
                                                student.user?.is_active
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
        :image-url="student.photo_url"
        @close="togglePhoto"
    />
</template>
