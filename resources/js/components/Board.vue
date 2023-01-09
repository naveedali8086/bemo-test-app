<template>
    <div>

        <!-- Board Header -->
        <div class="row">
            <div class="d-flex">
                <div class="me-2">
                    <select id="boards" class="form-select" v-model="selected_board">
                        <option value="">Select Board</option>
                        <option v-for="board in boards" :value="board.id" :key="board.id">{{ board.title }}</option>
                    </select>
                </div>

                <div>
                    <button type="button" class="btn btn-primary" @click="addColumn">Add Column</button>
                </div>
            </div>
        </div>

        <!-- "Add New Column" Popup -->
        <Column v-if="adding_column" @close="hideColumnPopup" :board_id="selected_board"/>

        <!-- Columns List -->
        <div class="scrolling-wrapper row flex-row flex-nowrap mt-4 pb-4 pt-2">
            <div v-for="column in columns" class="col-3" :key="column.id">
                <cards-list :column="column" @columnDeleted="getColumns"/>
            </div>
        </div>

    </div>

</template>

<script>
import Column from "./Column.vue"
import CardsList from "./CardsList.vue"
import {getParamValFromUrl} from "../mixins/helpers";

export default {
    name: "Board",
    components: {CardsList, Column},

    data() {
        return {
            selected_board: '',
            boards: [],

            adding_column: false,
            columns: []
        }
    },

    methods: {

        getBoards() {

            axios.get('/api/boards', {params: {api_token: getParamValFromUrl('token')}})
                .then((response) => {
                    this.boards = response.data.data.boards
                })
                .catch((error) => {
                    console.log(error)
                })
        },

        addColumn() {
            if (!this.selected_board) {
                alert('Please select board first')
                return false
            }
            this.adding_column = true
        },

        getColumns() {
            this.columns = [];
            axios.get('/api/columns', {params: {board_id: this.selected_board, api_token: getParamValFromUrl('token')}})
                .then((response) => {
                    this.columns = response.data.data
                })
                .catch((error) => {
                    console.log(error)
                })
        },

        hideColumnPopup(event_data) {
            if (event_data?.column_added) {
                this.getColumns()
            }
            this.adding_column = false
        },

    },

    watch: {
        selected_board: function (newVal) {
            if (newVal) {
                this.getColumns()
            } else {
                this.columns = [];
            }
        }
    },

    mounted() {
        this.getBoards()
    }

}
</script>

<style scoped>
.scrolling-wrapper {
    overflow-x: auto
}
</style>
