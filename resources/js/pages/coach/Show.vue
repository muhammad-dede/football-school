<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import MainContent from "@/components/MainContent.vue";
import { buttonVariants } from "@/components/ui/button/index";
import { Tag, Phone, Calendar, Gem, UserCheck, Mail } from "lucide-vue-next";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import DetailItem from "@/components/DetailItem.vue";

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
            <div class="grid lg:grid-cols-2 gap-6 my-4">
                <DetailItem
                    label="Nama Lengkap"
                    :value="coach.name"
                    :icon="Tag"
                    icon-color="text-blue-600"
                    bg-color="bg-blue-100"
                />
                <DetailItem
                    label="Tanggal Lahir"
                    :value="dateFormat(coach.birth_date)"
                    :icon="Calendar"
                    icon-color="text-yellow-600"
                    bg-color="bg-yellow-100"
                />
                <DetailItem
                    label="Telepon"
                    :value="coach.phone"
                    :icon="Phone"
                    icon-color="text-green-600"
                    bg-color="bg-green-100"
                />
                <DetailItem
                    label="Spesialisasi"
                    :value="coach.specialty"
                    :icon="Gem"
                    icon-color="text-gray-600"
                    bg-color="bg-gray-100"
                />
                <DetailItem
                    label="Status"
                    :value="coach.is_active ? 'Aktif' : 'Tidak Aktif'"
                    :icon="UserCheck"
                    icon-color="text-cyan-600"
                    bg-color="bg-cyan-100"
                />
                <DetailItem
                    label="Email"
                    :value="coach.user?.email"
                    :icon="Mail"
                    icon-color="text-pink-600"
                    bg-color="bg-pink-100"
                />
            </div>
        </MainContent>
    </AppLayout>
</template>
