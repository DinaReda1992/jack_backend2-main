<template>
  <div class="canvas-sidebar cart-canvas">
    <div class="canvas-overlay"></div>
    <div class="card border-0 pt-4 pb-7 h-100">
      <div class="px-6 text-right">
        <span class="canvas-close d-inline-block fs-24 mb-1 ml-auto lh-1 text-primary"><i class="fal fa-times"></i></span>
      </div>
      <div class="card-header bg-transparent p-0 mx-6">
        <h3 class="fs-24 mb-5">
          {{ $t('main.Cart') }}
        </h3>
      </div>
      <div class="card-body px-6 pt-7 overflow-y-auto">
        <div class="mb-4 d-flex" v-for="(item, index ) in all_items">
          <a href="javascript:void(0)" class="d-flex align-items-center mr-2 text-muted" @click="deleteItem(item)"><i
              class="fal fa-times"></i></a>
          <div class="media w-100">
            <div class="w-60px mr-3">
              <img :src="item.product.photo" :alt="item.product.title" class="image-cart" />
            </div>
            <div class="media-body d-flex">
              <div class="cart-price pr-6">
                <p class="fs-14 font-weight-bold text-secondary mb-1" v-if="item.product.offer_price > 0">
                  <span class="font-weight-500 fs-13 text-line-through text-body mr-1 m-3">{{
                    item.product.price
                  }}
                    {{ $t('main.SAR') }}</span>{{ item.product.offer_price }} {{ $t('main.SAR') }}
                </p>
                <p class="fs-14 font-weight-bold text-secondary mb-1" v-else>
                  {{ item.product.price }} {{ $t('main.SAR') }}
                </p>
                <a href="javascript:void(0)" class="text-secondary">{{ item.product.title }}</a>
              </div>
              <div class="position-relative ml-auto">
                <div class="input-group" :disabled="loading">
                  <a href="javascript:void(0)" class="position-absolute pos-fixed-left-center pl-2"
                    @click.prevent="updateItem(item, -1)"><i class="far fa-minus"></i></a>
                  <input type="number" v-model="item.quantity"
                    class="number-cart w-90px px-6 text-center h-40px bg-input border-0" min="1" step="1" />
                  <a href="javascript:void(0)" class="position-absolute pos-fixed-right-center pr-2"
                    @click.prevent="updateItem(item, +1)"><i class="far fa-plus"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="text-center" v-if="all_items.length == 0">
          <img src="/images/cart.png" alt="cart" style="height: 150px;" />
          <h5>{{ $t('main.Not Found Products In Cart') }}</h5>
        </div>
      </div>
      <div class="card-footer mt-auto border-0 bg-transparent px-6 pb-0 pt-5">
        <hr>
        <div class="d-flex align-items-center mb-2">
          <span class="text-secondary fs-15">{{ $t('main.Total') }} :</span>
          <span class="d-block ml-auto fs-24 font-weight-bold text-secondary">{{ mathRound(total.toFixed(2)) }}
            {{ $t('main.SAR') }}</span>
        </div>
        <hr>
        <a :href="'/' + lang + '/cart'" class="btn btn-outline-secondary btn-block">{{ $t('main.Cart') }}
        </a>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    items: {
      type: Array,
      default: "",
    },
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      all_items: this.items,
    };
  },
  mounted() {
    this.$root.$on('updateCart', data => {
      this.all_items = data
    });
  },
  computed: {
    total: function () {
      let total = [];
      Object.entries(this.all_items).forEach(([key, val]) => {
        total.push(val.item_total)
      });
      return total.reduce(function (total, num) { return total + num }, 0);
    }
  },
  methods: {
    mathRound: function (num) {
      return Math.round(num * 100) / 100
    },
    updateItem: async function (item, type = 0) {
      try {
        this.loading = true
        var formData = new FormData();
        var newQuantity = item.quantity + type;
        formData.set('id', item.product.id);
        formData.set('quantity', newQuantity);
        let res = await axios.post('/' + this.lang + '/cart/update', formData);
        if (res.status == 200) {
          this.loading = false;
          this.$root.$emit('updateCart', res.data.items);
          this.$root.$emit('updateCountItems', res.data.count_items);
          this.$toast.success(res.data.message, { position: "top-left" })
        }
        else if (res.status == 202) {
          this.loading = false
          let id = item.id
          const index = this.items.findIndex(item => item.id == id);
          this.all_items[index] = item;
          this.$root.$emit('updateCart', res.data.items);
          this.$root.$emit('updateCountItems', res.data.count_items);
          this.$toast.error(res.data.message, { position: "top-left" });
        } else {
          this.loading = false
          this.$toast.error(res.data.message, { position: "top-left" });
        }

      } catch (res) {
        // console.log(res)
        this.$toast.error(res.data.message, { position: "top-left" });
      }

    },
    deleteItem: async function (item) {
      try {
        Vue.$confirm(
          {
            title: this.$t('main.Delete a product from the cart'),
            message: this.$t('main.Do you want to remove the product from the cart?'),
            button: {
              no: this.$t('main.No'),
              yes: this.$t('main.Yes'),
            },
            callback: async confirm => {
              if (confirm) {
                this.loading = true
                let params = {
                  id: item,
                };
                let res = await axios.post('/' + this.lang + '/cart/delete', params);
                if (res.status == 200) {
                  this.all_items = res.data.items
                  this.$root.$emit('updateCart', res.data.items);
                  this.$root.$emit('updateCountItems', res.data.count_items);
                  this.$root.$emit('deleteItem', params.id);
                  this.$toast.success(res.data.message)
                } else {
                  let errors = res.data.errors
                  var i;
                  for (i = 0; i < errors.length; i++) {
                    this.$toast.error(errors[i]);
                  }
                }
              }
            }
          }
        )
      } catch (res) {
        this.loading = false
        // console.log(res)
      }
    },

  },
};
</script>
