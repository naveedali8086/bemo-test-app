<template>

    <modal>

        <template v-slot:header>
            <h3>{{ card ? 'Edit Card' : 'Add Card' }}</h3>
        </template>

        <template v-slot:body>

            <Input v-model="form.fields.title" :err="form.errors.title">
                Title
            </Input>

            <TextArea v-model="form.fields.description" :err="form.errors.description">
                Description
            </TextArea>

        </template>

        <template v-slot:footer>
            <button class="btn btn-secondary me-2" type="button" :disabled="form_submitted" @click="$emit('close')">
                Cancel
            </button>
            <button class="btn btn-primary" type="button" :disabled="form_submitted" @click="saveCard">
                <span v-if="form_submitted" class="spinner-border spinner-border-sm" role="status"></span>
                Save
            </button>
        </template>

    </modal>

</template>

<script>
import Modal from "./common/Modal.vue";
import Input from "./formControls/Input.vue";
import TextArea from "./formControls/TextArea.vue";
import SendRequest from "../mixins/SendRequest";

export default {
    name: "Card",

    components: {Input, Modal, TextArea},

    mixins: [SendRequest],

    props: ['column_id', 'card'],

    data() {
        return {

            form_submitted: false,

            form: {

                fields: {
                    title: '',
                    description: '',
                    column_id: ''
                },

                errors: {
                    title: '',
                    description: '',
                    column_id: ''
                }
            }

        }
    },

    methods: {

        saveCard() {
            this.form_submitted = true
            let url = `${window.location.origin}/api/cards`
            if (this.card) {
                url += `/${this.card.id}`
            }
            this.sendRequest(url,
                this.card ? 'PUT' : 'POST',
                () => {
                    this.form_submitted = false
                }, () => {
                    this.$emit('close', {card_saved: true});
                })
        }

    },

    created() {
        this.form.fields.column_id = this.column_id
        if (this.card) {
            this.form.fields.title = this.card.title;
            this.form.fields.description = this.card.description;
        }
    },

}
</script>
