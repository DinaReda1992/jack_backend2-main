<template>
  <div id="myModalConfirmation" class="modal fade " tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalCenterTitle">اضافة إلي السلة</h5>
        </div>
        <div class="modal-body">
          <h5 class="text-center">{{ this_item.title }}</h5>
          <div class="form-group">
            <label>أختر الكمية</label>
            <select v-model="quantity" class="form-control">
              <template v-for="item in 500">
                <option v-if="item >= this_item.min_quantity">{{ item }}</option>
              </template>
            </select>
            <small class="form-text text-muted">اقل كمية متاحة للبيع هي {{ this_item.min_quantity }}</small>

            <!--            <label >اكتب الكمية</label>
            <input type="text" v-model="quantity" class="form-control" aria-describedby="emailHelp" placeholder="Enter email">
            <small id="emailHelp" class="form-text text-muted">اقل كمية متاحة للبيع هي {{this_item.min_quantity}}</small>-->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click.prevent="add_to_cart(false)"
            data-dismiss="modal">إغلاق</button>
          <button type="button" :disabled="loading" class="btn btn-primary" @click.prevent="store_item1"><i
              class="la la-shopping-cart"></i>اضافة إلي السلة</button>
        </div>
      </div>
    </div>
  </div>
</template>



<script>
export default {
  components: {
  },

  props: {

  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      this_item: {},
      quantity: 0

    }
  },
  mounted() {
    this.$root.$on('getItem', data => {
      this.this_item = data
      this.quantity = data.min_quantity
    });


  },

  methods: {
    add_to_cart: function (status) {

      if (status) {
        $('#myModalConfirmation').modal('show')
        this.$root.$emit('getItem', this.this_item)
      } else {
        $('#myModalConfirmation').modal('hide')

      }
    },
    store_item1: async function () {
      try {
        if (parseInt(this.this_item.min_quantity) > parseInt(this.quantity)) {
          this.$toast.error('اقل كمية للبيع هي' + this.this_item.min_quantity)
          return;
        }
        this.loading = true
        var formData = new FormData();
        formData.set('id', this.this_item.id);
        formData.set('quantity', this.quantity);
        let res = await axios.post('/' + this.lang + '/cart/store', formData);
        if (res.status == 200) {
          this.loading = false
          $('#myModalConfirmation').modal('hide')
          this.$toast.success(res.data.message);
          this.$root.$emit('updateCart', res.data.items)
        } else {
          this.loading = false
          this.$toast.error(res.data.message);
        }

      } catch (res) {
        console.log(res)
        this.$toast.error(res.data.message);
      }

    },



  },
};
</script>
<style>
.toast-icon {
  top: initial !important;
}

body {
  overflow-x: hidden;
}

.is_fav {
  color: #d18332 !important;
}
</style>
