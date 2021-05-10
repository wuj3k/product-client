<template>
  <b-container class="py-3">
    <crud-table
        :endpoint="serverAddress"
        :columns="fields"
        :form-fields="{
               name: '',
               amount: 0,
           }"
        @form-error="handleError"
        @form-clear="clearForm"
    >
      <template v-slot:input-fields="{formdata}">
        <b-form-group id="input-group-2" label="Nazwa produktu" label-for="input-2">
          <b-form-input
              id="input-2"
              v-model="formdata.name"
              required
              :state="nameFieldState"
              placeholder="Wprowadz nazwę produktu"
              class="my-2"
          ></b-form-input>
          <div class="alert-danger px-2">{{ nameFieldMessage }}</div>
          <b-form-input
              id="input-1"
              class="my-2"
              v-model="formdata.amount"
              required
              type="number"
              :state="amountFieldState"
              aria-invalid="true"
              placeholder="Wprowadz ilość produktu"
          ></b-form-input>
          <div class="alert-danger px-2 my-2">{{ amountFieldMessage }}</div>
        </b-form-group>
      </template>
    </crud-table>
  </b-container>
</template>

<script>
import CrudTable from '../pages/crud-table';

export default {
  components: {CrudTable},
  data() {
    return {
      nameFieldState: null,
      amountFieldState: null,
      nameFieldMessage: null,
      amountFieldMessage: null,
      fields: [
        {
          key: 'name',
          label: 'Nazwa produktu',
        },{
          key: 'amount',
          label: 'Ilość na stanie',
        },
        {
          key: 'action',
          label: 'Akcja',
        },
      ]
    }
  },
  computed: {
    serverAddress() {
      return window.serverAddress + '/products';
    },
  },
  methods: {
    handleError(response) {
      response.map(this.addError);
    },
    addError(response) {
      if (response.field === 'amount') {
        this.amountFieldState = false;
        this.amountFieldMessage = response.message;
      }
      if (response.field === 'name') {
        this.nameFieldState = false;
        this.nameFieldMessage = response.message;
      }
    },
    clearForm() {
      this.nameFieldState = null;
      this.amountFieldState = null;
      this.nameFieldMessage = null;
      this.amountFieldMessage = null;
    },
  },
};
</script>