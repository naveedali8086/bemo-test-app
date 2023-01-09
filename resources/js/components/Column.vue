<template>

    <modal>

        <template v-slot:header>
            <h3>Add Column</h3>
        </template>

        <template v-slot:body>

            <Input v-model="form.fields.title" :err="form.errors.title">
                Title
            </Input>

        </template>

        <template v-slot:footer>
            <button class="btn btn-secondary me-2" type="button" :disabled="form_submitted" @click="$emit('close')">
                Cancel
            </button>
            <button class="btn btn-primary" type="button" :disabled="form_submitted" @click="createColumn">
                <span v-if="form_submitted" class="spinner-border spinner-border-sm" role="status"></span>
                Save
            </button>
        </template>

    </modal>

</template>

<script>
import Modal from "./common/Modal.vue";
import Input from "./formControls/Input.vue";
import SendRequest from "../mixins/SendRequest";

export default {
    name: "Column",

    components: {Input, Modal},

    mixins: [SendRequest],

    props: ['board_id'],

    data() {
        return {

            form_submitted: false,

            form: {

                fields: {
                    title: '',
                    board_id: ''
                },

                errors: {
                    title: '',
                    board_id: ''
                }
            }

        }
    },

    methods: {

        createColumn() {
            this.form_submitted = true
            this.sendRequest(`${window.location.origin}/api/columns`, 'POST', () => {
                this.form_submitted = false
            }, () => {
                this.$emit('close', {column_added: true});
            })
        }

    },

    created() {
        this.form.fields.board_id = this.board_id
    }


}
</script>

<style scoped>

</style>
