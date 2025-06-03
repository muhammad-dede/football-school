<script setup>
import AppLayout from "@/layouts/AppLayout.vue";
import { Head, useForm, Link } from "@inertiajs/vue3";
import MainContent from "@/components/MainContent.vue";
import { Card, CardContent, CardFooter } from "@/components/ui/card/index";
import { Label } from "@/components/ui/label/index";
import { Input } from "@/components/ui/input/index";
import { Button, buttonVariants } from "@/components/ui/button/index";
import InputError from "@/components/InputError.vue";
import {
    LoaderCircle,
    IdCard,
    Calendar,
    CalendarDays,
    Banknote,
    CircleDollarSign,
    Landmark,
} from "lucide-vue-next";
import HeadingGroup from "@/components/HeadingGroup.vue";
import Heading from "@/components/Heading.vue";
import { Separator } from "@/components/ui/separator";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import LabelSpan from "@/components/LabelSpan.vue";
import { Textarea } from "@/components/ui/textarea";
import { onUnmounted, ref } from "vue";
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import InfoItem from "@/components/InfoItem.vue";

const props = defineProps({
    billing: Object,
    bank_accounts: Object,
});

const form = useForm({
    amount: props.billing.amount ?? 0,
    payment_date: "",
    method: "",
    receiver_id: "",
    sender_bank_name: "",
    sender_account_number: "",
    sender_account_holder_name: "",
    proof_file: "",
    reference_number: "",
    notes: "",
});

const breadcrumbs = [
    { title: "Dashboard", href: "/dashboard" },
    { title: "Siswa", href: "/student" },
    { title: "Pembayaran Tagihan", href: "/student/payment/create" },
];

const paymentMethods = [
    { value: "CASH", label: "Tunai" },
    { value: "TRANSFER", label: "Transfer" },
];

const currency = (number) => {
    if (isNaN(number)) return "Rp0";
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Number(number));
};

const dateFormat = (date) => {
    if (!date) return "-";
    const options = { day: "numeric", month: "long", year: "numeric" };
    return new Date(date).toLocaleDateString("id-ID", options);
};

function handleFileChange(event) {
    form.proof_file = event.target.files[0];
}

const submit = () => {
    form.post(route("student.payment.store", props.billing?.id), {
        forceFormData: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Pembayaran Tagihan" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <MainContent>
            <HeadingGroup>
                <Heading
                    title="Pembayaran Tagihan"
                    description="Formulir untuk pembayaran tagihan siswa"
                />
            </HeadingGroup>
            <form @submit.prevent="submit">
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="w-full lg:w-[60%]">
                        <Card>
                            <CardContent>
                                <h5
                                    class="text-sm font-bold text-gray-500 mb-4"
                                >
                                    Informasi Tagihan
                                </h5>
                                <div class="grid divide-y divide-gray-100">
                                    <InfoItem
                                        :label="
                                            billing.student?.national_id_number
                                        "
                                        :value="billing.student?.name"
                                        :icon="IdCard"
                                    />
                                    <InfoItem
                                        label="Periode"
                                        :value="billing.period?.name"
                                        :icon="CalendarDays"
                                        background
                                    />
                                    <InfoItem
                                        label="Tagihan"
                                        :value="billing.billing_type?.name"
                                        :icon="Banknote"
                                        background
                                    />
                                    <InfoItem
                                        label="Jumlah Yang Harus Dibayar"
                                        :value="currency(billing.amount)"
                                        :icon="CircleDollarSign"
                                        background
                                    />
                                    <InfoItem
                                        label="Jatuh Tempo"
                                        :value="dateFormat(billing.due_date)"
                                        :icon="Calendar"
                                        background
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                    <div class="w-full lg:w-[40%]">
                        <Card>
                            <CardContent>
                                <h5
                                    class="text-sm font-bold text-gray-500 mb-4"
                                >
                                    Informasi Pembayaran
                                </h5>
                                <div class="grid divide-y divide-gray-100">
                                    <div
                                        class="w-full flex flex-col gap-2 pb-4"
                                    >
                                        <Label for="amount">Total Bayar</Label>
                                        <Input
                                            id="amount"
                                            type="number"
                                            name="amount"
                                            placeholder="Input Total Bayar"
                                            autocomplete="off"
                                            v-model="form.amount"
                                            readonly
                                        />
                                        <InputError
                                            :message="form.errors.amount"
                                        />
                                    </div>
                                    <div
                                        class="w-full flex flex-col gap-2 py-4"
                                    >
                                        <Label for="payment_date"
                                            >Tanggal Bayar</Label
                                        >
                                        <Input
                                            id="payment_date"
                                            type="date"
                                            name="payment_date"
                                            v-model="form.payment_date"
                                        />
                                        <InputError
                                            :message="form.errors.payment_date"
                                        />
                                    </div>
                                    <div
                                        class="w-full flex flex-col gap-2 py-4"
                                    >
                                        <LabelSpan label="Metode Pembayaran" />
                                        <RadioGroup
                                            :orientation="'vertical'"
                                            v-model="form.method"
                                        >
                                            <div
                                                class="flex items-center space-x-2"
                                                v-for="(
                                                    method, index
                                                ) in paymentMethods"
                                                :key="index"
                                            >
                                                <RadioGroupItem
                                                    :id="`method-${index}`"
                                                    :value="method.value"
                                                />
                                                <Label :for="`method-${index}`">
                                                    {{ method.label }}
                                                </Label>
                                            </div>
                                        </RadioGroup>
                                        <InputError
                                            :message="form.errors.method"
                                        />
                                    </div>
                                    <template v-if="form.method === 'TRANSFER'">
                                        <div
                                            class="w-full flex flex-col gap-2 py-4"
                                        >
                                            <Label for="receiver_id"
                                                >Bank Tujuan</Label
                                            >
                                            <Select
                                                v-model="form.receiver_id"
                                                name="receiver_id"
                                                :key="form.method"
                                            >
                                                <SelectTrigger
                                                    id="receiver_id"
                                                    class="w-full"
                                                >
                                                    <SelectValue
                                                        placeholder="Pilih Bank"
                                                    />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectGroup>
                                                        <SelectItem
                                                            v-for="(
                                                                item, index
                                                            ) in bank_accounts"
                                                            :key="index"
                                                            :value="item.id"
                                                        >
                                                            {{ item.bank_name }}
                                                            -
                                                            {{
                                                                item.account_number
                                                            }}
                                                            a.n
                                                            {{
                                                                item.account_holder_name
                                                            }}
                                                        </SelectItem>
                                                    </SelectGroup>
                                                </SelectContent>
                                            </Select>
                                            <InputError
                                                :message="
                                                    form.errors.receiver_id
                                                "
                                            />
                                        </div>
                                        <div
                                            class="w-full flex flex-col gap-2 py-4"
                                        >
                                            <Label for="sender_bank_name"
                                                >Nama Bank Pengirim</Label
                                            >
                                            <Input
                                                id="sender_bank_name"
                                                type="text"
                                                name="sender_bank_name"
                                                placeholder="Input Nama Bank Pengirim"
                                                autocomplete="off"
                                                v-model="form.sender_bank_name"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.sender_bank_name
                                                "
                                            />
                                        </div>
                                        <div
                                            class="w-full flex flex-col gap-2 py-4"
                                        >
                                            <Label for="sender_account_number"
                                                >No Rekening Pengirim</Label
                                            >
                                            <Input
                                                id="sender_account_number"
                                                type="text"
                                                name="sender_account_number"
                                                placeholder="Input No Rekening Pengirim"
                                                autocomplete="off"
                                                v-model="
                                                    form.sender_account_number
                                                "
                                            />
                                            <InputError
                                                :message="
                                                    form.errors
                                                        .sender_account_number
                                                "
                                            />
                                        </div>
                                        <div
                                            class="w-full flex flex-col gap-2 py-4"
                                        >
                                            <Label
                                                for="sender_account_holder_name"
                                                >Atas Nama Pengirim</Label
                                            >
                                            <Input
                                                id="sender_account_holder_name"
                                                type="text"
                                                name="sender_account_holder_name"
                                                placeholder="Input Atas Nama Pengirim"
                                                autocomplete="off"
                                                v-model="
                                                    form.sender_account_holder_name
                                                "
                                            />
                                            <InputError
                                                :message="
                                                    form.errors
                                                        .sender_account_holder_name
                                                "
                                            />
                                        </div>
                                        <div
                                            class="w-full flex flex-col gap-2 py-4"
                                        >
                                            <Label for="proof_file"
                                                >Bukti Transfer</Label
                                            >
                                            <Input
                                                id="proof_file"
                                                type="file"
                                                name="proof_file"
                                                accept="image/*,.pdf"
                                                @change="handleFileChange"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.proof_file
                                                "
                                            />
                                        </div>
                                        <div
                                            class="w-full flex flex-col gap-2 py-4"
                                        >
                                            <Label for="reference_number"
                                                >No Referensi</Label
                                            >
                                            <Input
                                                id="reference_number"
                                                type="text"
                                                name="reference_number"
                                                placeholder="Input No Referensi"
                                                autocomplete="off"
                                                v-model="form.reference_number"
                                            />
                                            <InputError
                                                :message="
                                                    form.errors.reference_number
                                                "
                                            />
                                        </div>
                                    </template>
                                    <div
                                        class="w-full flex flex-col gap-2 py-4"
                                    >
                                        <Label for="notes">Catatan</Label>
                                        <Input
                                            id="notes"
                                            type="text"
                                            name="notes"
                                            placeholder="Input Catatan"
                                            autocomplete="off"
                                            v-model="form.notes"
                                        />
                                        <InputError
                                            :message="form.errors.notes"
                                        />
                                    </div>
                                </div>
                            </CardContent>
                            <CardFooter>
                                <div class="space-x-2">
                                    <Button
                                        type="submit"
                                        :disabled="form.processing"
                                    >
                                        <LoaderCircle
                                            v-if="form.processing"
                                            class="h-4 w-4 animate-spin"
                                        />
                                        Bayar
                                    </Button>
                                    <Link
                                        :href="
                                            route(
                                                'student.show',
                                                billing.student_id
                                            )
                                        "
                                        :class="
                                            buttonVariants({
                                                variant: 'outline',
                                            })
                                        "
                                        >Kembali</Link
                                    >
                                </div>
                            </CardFooter>
                        </Card>
                    </div>
                </div>
            </form>
        </MainContent>
    </AppLayout>
</template>
