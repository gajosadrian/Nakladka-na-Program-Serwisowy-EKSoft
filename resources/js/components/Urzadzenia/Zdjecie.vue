<template>
    <div>
        <b-img
            @click="rotateRight()"
            :src="url"
            :style="{ transform: `rotate(${rotation}deg)` }"
            fluid
        />
    </div>
</template>

<script>
export default {
    props: {
        url: String,
    },
    data() {
        return {
            rotation: 0,
        }
    },
    methods: {
        rotateRight() {
            this.rotation += 90
        }
    },
}
</script>