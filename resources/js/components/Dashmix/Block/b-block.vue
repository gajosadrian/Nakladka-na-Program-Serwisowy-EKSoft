<template>
    <div class="block" :class="{'block-rounded': !noround, 'block-themed': theme, 'block-fx-shadow': shadow, 'shadow-sm': !shadow}">
        <div v-if="title" class="block-header" :class="[theme, {'block-header-default': !theme, 'd-print-none': noprint}]">
            <h3 class="block-title">{{ title }}</h3>
            <div v-if="hasOptionsSlot" class="block-options">
                <slot name="options"></slot>
            </div>
        </div>
        <div class="block-content" :class="{'block-content-full': full}">
            <slot name="content"></slot>
        </div>
        <div v-if="hasFooterSlot" class="block-content block-content-full block-content-sm bg-body-light font-size-sm">
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        'noborder': Boolean,
        'noround': Boolean,
        'full': Boolean,
        'shadow': Boolean,
        'noprint': Boolean,
        'title': String,
        'theme': String
    },
    computed: {
        hasOptionsSlot() {
            return !!this.$slots.options;
        },
        hasFooterSlot() {
            return !!this.$slots.footer;
        }
    }
}
</script>
