<script setup>
import { Card, CardContent } from "@/components/ui/card";
import InfoItem from "@/components/InfoItem.vue";
import { Badge } from "@/components/ui/badge";
import {
    Phone,
    Calendar,
    Mail,
    Mars,
    MapPinCheck,
    IdCard,
    Footprints,
    Ruler,
    Weight,
} from "lucide-vue-next";

const props = defineProps({
    student: Object,
    dateFormat: Function,
    togglePhoto: Function,
});
</script>

<template>
    <div class="flex flex-col lg:flex-row gap-4">
        <Card class="h-fit w-full lg:w-[50%] xl:w-[60%] py-3">
            <CardContent>
                <h5 class="text-sm font-bold text-gray-500 mb-4">
                    Informasi Biodata
                </h5>
                <div class="flex justify-between">
                    <template v-if="student.photo_url">
                        <img
                            :src="student.photo_url"
                            alt="Preview"
                            class="h-24 w-24 object-cover rounded-lg border cursor-pointer"
                            @click="togglePhoto"
                        />
                    </template>
                    <template v-else>
                        <div
                            class="h-24 w-24 flex items-center justify-center border rounded-lg text-gray-500 text-xs"
                        >
                            Belum ada gambar
                        </div>
                    </template>
                    <Badge
                        :variant="
                            student.current_program ? 'default' : 'destructive'
                        "
                        class="py-2 px-3 rounded-full h-fit"
                    >
                        {{ student.current_program ? "Aktif" : "Tidak Aktif" }}
                    </Badge>
                </div>
                <div class="grid divide-y divide-gray-100">
                    <InfoItem
                        :label="student.national_id_number"
                        :value="student.name"
                        :icon="IdCard"
                    />
                    <InfoItem
                        label="Tempat Lahir"
                        :value="student.place_of_birth"
                        :icon="MapPinCheck"
                        background
                    />
                    <InfoItem
                        label="Tanggal Lahir"
                        :value="dateFormat(student.date_of_birth)"
                        :icon="Calendar"
                        background
                    />
                    <InfoItem
                        label="Jenis Kelamin"
                        :value="
                            student.gender === 'MALE'
                                ? 'Laki-laki'
                                : 'Perempuan'
                        "
                        :icon="Mars"
                        background
                    />
                    <InfoItem
                        label="Alamat"
                        :value="student.address"
                        :icon="MapPinCheck"
                        background
                    />
                    <InfoItem
                        label="Telepon"
                        :value="student.phone"
                        :icon="Phone"
                        background
                    />
                    <InfoItem
                        label="Email"
                        :value="student.user?.email"
                        :icon="Mail"
                        background
                    />
                </div>
            </CardContent>
        </Card>
        <Card class="h-fit w-full lg:w-[50%] xl:w-[40%] py-3">
            <CardContent>
                <h5 class="text-sm font-bold text-gray-500 mb-4">
                    Informasi Fisik
                </h5>
                <div class="grid divide-y divide-gray-100">
                    <InfoItem
                        label="Kaki Dominan"
                        :value="student.dominant_foot"
                        :icon="Footprints"
                        background
                    />
                    <InfoItem
                        label="Tinggi Badan"
                        :value="`${student.height_cm ?? '-'} Centimeter`"
                        :icon="Ruler"
                        background
                    />
                    <InfoItem
                        label="Berat Badan"
                        :value="`${student.weight_kg ?? '-'} Kg`"
                        :icon="Weight"
                        background
                    />
                </div>
            </CardContent>
        </Card>
    </div>
</template>
