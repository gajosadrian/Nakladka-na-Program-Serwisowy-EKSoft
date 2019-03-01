<template>
    <div>
        <b-button size="sm" variant="light" @click="changeStatus"><i :class="[icon, color_formatted]" class="mr-1"></i> {{ name }}</b-button>
    </div>
</template>

<script>
export default {
    props: {
        zlecenie_id: String,
        status_id: String,
        name: String,
        icon: String,
        color: String,
    },

    computed: {
        color_formatted() {
            return 'text-' + this.color;
        }
    },

    methods: {
        changeStatus() {
            axios.post(route('zlecenia.api.change_status', {
                id: this.zlecenie_id,
                status_id: this.status_id,
            })).then(response => {
                location.reload();
            });
        }
    },
}
</script>
