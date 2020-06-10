<template>
    <div class="border">
        <div :onclick="zlecenie.popup_link" class="bg-light p-2" style="cursor:pointer;">
            <span class="font-w700">{{ zlecenie.nr }}</span>
            <span class="float-right text-muted">» pokaż zlecenie «</span>
        </div>
        <div class="p-2">
            Technik: <span class="font-w600">{{ technik.imie }} {{ technik.nazwisko }}</span>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        zlecenie: Object,
    },
    computed: {
        technik() {
            return this.zlecenie.technik
        },
        urzadzenie() {
            return this.zlecenie.urzadzenie
        },
    },
}
</script>