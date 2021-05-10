<template>
  <div>
    <b-alert
        :show="dismissCountDown"
        fade
        variant="success"
        @dismissed="dismissCountDown=0"
        @dismiss-count-down="countDownChanged"
    >
      {{ alertMessage }}
    </b-alert>
    <b-button @click="createItem" class="mb-2" variant="primary" size="sm">Dodaj nowy produkt</b-button>
    <b-form-select @change="search" v-model="selected" :options="options"></b-form-select>
    <b-table striped hover :items="tableData" :fields="columns">
      <template #cell(action)="data">
        <b-button @click="editItem(data.item)" variant="primary" size="sm">Edytuj</b-button>
        <b-button @click="deleteItem(data.item)" v-b-modal="'edit-modal'" variant="danger" size="sm">Usuń</b-button>
      </template>
    </b-table>

    <b-modal v-model="modalShow" :title="formTitle" hide-footer>
      <b-form @submit.prevent="save">
        <slot :formdata="editedItem" name="input-fields">
        </slot>

        <b-button size="sm" variant="danger" @click="close">
          Anuluj
        </b-button>

        <b-button type="submit" size="sm" variant="success">
          Zapisz
        </b-button>
      </b-form>

    </b-modal>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  props: ['endpoint', 'columns', 'formFields'],
  data() {
    return {
      editedItem: this.formFields,
      modalShow: false,
      editedIndex: -1,
      tableData: [],
      dismissSecs: 2,
      dismissCountDown: 0,
      showDismissibleAlert: false,
      alertMessage: '',
      selected: null,
      options: [
        {value: null, text: 'Wszystkie produktu'},
        {value: 1, text: 'Znajdują się na składzie'},
        {value: 2, text: 'Nie znajdują się na składzie'},
        {value: 3, text: 'Znajdują się na składzie w ilości większej niż 5'},
      ]
    }
  },
  computed: {
    formTitle() {
      return this.editedIndex === -1 ? 'Nowy produkt' : 'Edycja produktu';
    },
  },
  methods: {
    createItem() {
      this.modalShow = true;
      this.editedItem = Object.assign({}, this.formFields);
      this.editedIndex = -1;
    },

    editItem(item) {
      this.modalShow = true;
      this.editedIndex = this.tableData.indexOf(item);
      this.editedItem = Object.assign({}, item);
    },

    deleteItem(item) {
      confirm('Czy jestes pewny?');
      axios.delete(this.endpoint + '/' + item.id).then((response) => this.finish(response));
    },

    close() {
      this.modalShow = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.formFields);
        this.editedIndex = -1;
      }, 300);
    },

    save() {
      this.$emit('form-clear');
      if (this.editedIndex > -1) {
        axios.put(this.endpoint + '/' + this.editedItem.id, this.editedItem).then((response) => this.finish(response)).catch(err => this.handleError(err.response));
      } else {
        axios.post(this.endpoint, this.editedItem).then((response) => this.finish(response)).catch(err => this.handleError(err.response));
      }
    },

    get() {
      axios.get(this.endpoint).then(response => this.tableData = response.data);
    },
    search() {
      let query = {};
      switch (this.selected) {
        case 1: {
          query.type = 3;
          query.amount = 1;
          break;
        }
        case 2: {
          query.type = 1;
          query.amount = 0;
          break;
        }
        case 3: {
          query.type = 3;
          query.amount = 5;
          break;
        }
      }
      axios.get(this.endpoint, {params: query}).then(response => this.tableData = response.data);
    },

    handleError(response) {
      if (response.status === 422) {
        this.$emit('form-error', response.data);
      }
    },
    finish(response) {
      if (response.data.message) {
        this.alertMessage = response.data.message;
        this.showAlert();
      }
      this.get();
      this.close();
    },
    countDownChanged(dismissCountDown) {
      this.dismissCountDown = dismissCountDown
    },
    showAlert() {
      this.dismissCountDown = this.dismissSecs
    }
  },
  created() {
    this.get();
  },
}
</script>