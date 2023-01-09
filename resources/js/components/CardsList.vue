<template>

    <div class="shadow-sm py-2">

        <div class="d-flex mb-3">

            <div class="me-auto p-2">
                {{ column.title }}
            </div>

            <div class="p-2">
                <button type="button" class="btn btn-sm btn-danger" @click="deleteColumn" :disabled="deleting_column">
                    <span v-if="deleting_column" class="spinner-border spinner-border-sm" role="status"></span>
                    Delete
                </button>
            </div>

            <div class="p-2">
                <button type="button" class="btn btn-sm btn-primary" @click="addCard">
                    Add Card
                </button>
            </div>

        </div>

        <card v-if="adding_card" :column_id="column.id" :card="editable_card" @close="hideCardPopup"/>

        <draggable v-model="cards" @end="dragFinished">
            <div v-for="card in cards" :key="card.id" @click="setEditableCard(card)"
                 class="bg-light bg-white mb-3 mx-2 p-3 shadow-sm col-card">
                {{ card.title }}
            </div>
        </draggable>

    </div>

</template>

<script>
import Card from "./Card.vue";
import column from "./Column.vue";
import SendRequest from "../mixins/SendRequest";
import draggable from 'vuedraggable'
import {getParamValFromUrl} from "../mixins/helpers";

export default {
    name: "CardsList",
    components: {Card, draggable,},
    mixins: [SendRequest],
    props: ['column'],

    data() {
        return {
            cards: [],
            adding_card: false,
            deleting_column: false,
            editable_card: null
        }
    },

    methods: {

        getCards() {

            axios.get('/api/cards', {
                params: {
                    column_id: this.column.id,
                    api_token: getParamValFromUrl('token'),
                    status: 1
                }
            })
                .then((response) => {
                    this.cards = response.data.data
                })
                .catch((error) => {
                    console.log(error)
                })
        },

        addCard() {
            this.editable_card = null
            this.adding_card = true
        },

        hideCardPopup(event_data) {
            if (event_data?.card_saved) {
                this.getCards()
            }
            this.adding_card = false
            this.editable_card = null
        },

        setEditableCard(card) {
            this.editable_card = card
            this.adding_card = true
        },

        deleteColumn() {
            if (confirm('Are you sure, you want to delete a column?')) {
                this.deleting_column = true
                let url = `${window.location.origin}/api/columns/${this.column.id}`
                this.sendRequest(url,
                    'DELETE',
                    () => {
                        this.deleting_column = false
                    }, () => {
                        this.$emit('columnDeleted');
                    })
            }
        },

        dragFinished(event) {
            let form_data = new FormData;
            let order = this.cards.length
            for (const index in this.cards) {
                form_data.append(`cards[${index}][id]`, this.cards[index].id);
                form_data.append(`cards[${index}][order]`, order);
                order--
            }
            form_data.append('api_token', getParamValFromUrl('token'))

            axios.post(`${window.location.origin}/api/reset-cards-order`, form_data)
                .then((response) => {
                    alert(response.data.message);
                })
                .catch((error) => {
                    console.log(error)
                });
        }

    },

    mounted() {
        this.getCards()
    }
}
</script>

<style scoped>
.col-card:hover {
    cursor: pointer;
}
</style>
