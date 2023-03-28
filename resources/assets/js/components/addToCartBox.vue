<template>
  <div class="modal fade" id="addToCartBox" tabindex="-1" aria-labelledby="addToCartBoxLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addToCartBoxLabel">{{ $t('main.add to cart') }}</h5>
          <button type="button" class="btn-close" ref="addToCartBox" data-bs-dismiss="modal" aria-label="Close"
            @click="closeModel"></button>
        </div>
        <div class="modal-body">
          <h3 class="text-center">{{ item.title }}</h3>
          <div class="add-to-cart-content">
            <form>
              <div class="form-group">
                <label>{{ $t('main.select Quantity') }}</label>
                <select class="form-control" v-model="quantity">
                  <option v-for="n in 100" :value="n">{{ n }}</option>
                </select>
              </div>
            </form>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            {{ $t('main.Close') }}
          </button>
          <button type="button" class="btn btn-primary" @click="addToCart">{{ $t('main.add to cart') }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      item: {},
      quantity: 0,
    };
  },
  mounted() {
    this.$root.$on("getItem", (data) => {
      this.item = data;
      this.quantity = data.min_quantity;
    });
  },
  methods: {
    addToCart: async function () {
      try {
        if (this.item.min_quantity > this.quantity) {
          this.$toast.error(this.$t('main.The minimum quantity to buy is') + this.item.min_quantity);
          return;
        }
        var formData = new FormData();
        formData.set('id', this.item.id);
        formData.set('quantity', this.quantity);
        let res = await axios.post('/' + this.lang + '/cart/store', formData);

        if (res.status == 200) {
          this.item.is_carted = 1;
          this.$toast.success(res.data.message);
          this.$root.$emit('updateCart', res.data.items);
          this.$root.$emit('updateCountItems', res.data.count_items);
          this.$root.$emit('CartUpdateItem', this.item);
          this.$refs["addToCartBox"].click();
        } else {
          this.$toast.error(res.data.message)
        }
      } catch (res) {
        // console.log(res);
        // this.$toast.error(res.data.message);
      }

    },
    closeModel: function () {
      $("#addToCartBox").modal("hide");
    },
  },
};
</script>
