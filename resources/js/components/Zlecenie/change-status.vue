<template>
    <div>
        <b-button size="sm" variant="light" @click="changeStatus"><i :class="[icon, color_formatted]" class="mr-1"></i> {{ name_formatted }}</b-button>
    </div>
</template>

<script>
export default {
    props: {
        zlecenie_id: String,
        status_id: String,
        second_status_id: String,
        remove_termin: Number,
        dont_change_color: Number,
        name: String,
        icon: String,
        color: String,
    },

    computed: {
        color_formatted() {
            return 'text-' + this.color;
        },
        name_formatted() {
            return JSON.parse('"' + this.name + '"');
        },
    },

    methods: {
        changeStatus() {
            let status_color = this.dont_change_color && '0' || '12897956';

            axios.post(route('zlecenia.api.change_status', {
                id: this.zlecenie_id,
                status_id: this.status_id,
                remove_termin: this.remove_termin,
                terminarz_status_id: status_color,
            })).then(response => {
                if (this.second_status_id) {
                    axios.post(route('zlecenia.api.change_status', {
                        id: this.zlecenie_id,
                        status_id: this.second_status_id,
                        add_seconds: 1,
                    })).then(response => {
                        location.reload();
                    });
                } else {
                    location.reload();
                }
            });
        }
    },
}
</script>
