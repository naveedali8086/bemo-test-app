<template>

    <div class="mb-3">

        <label class="form-label" v-if="!!$slots['default']">
            <slot></slot>
        </label>

        <textarea class="form-control" :class="{'is-invalid':err}" v-bind="$attrs" :value="value"
                  @input="$emit('input', $event.target.value)"></textarea>

        <div v-if="err" class="invalid-feedback">{{ err }}</div>
    </div>

</template>

<script>
export default {
    name: "TextArea",
    props: ['value', 'err'],
    emits: ['input'],
}
</script>
