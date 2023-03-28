<template>
  <button
    class=" cart-button mr-auto d-md-flex align-items-center justify-content-center cursor-pointer text-secondary bg-white bg-hover-secondary w-40px h-40px mb-2"
    v-if="single == 0" @click.prevent="add_to_cart(true)" style="border-radius: 15px;" aria-label="add-to-cart"
    :style="this_item.is_carted == 1 ? 'border: 2px solid #82588d;background-color: #dc3545 !important;' : 'border: 2px solid #4e0161;'">
    <!-- <svg class="icon icon-shopping-bag-open-light fs-24">
      <use xlink:href="#icon-shopping-bag-open-light"></use>
    </svg> -->
    <i class="far fa-cart-plus fs-24 icon icon-shopping-bag-open-light"
      :style="this_item.is_carted == 1 ? 'color: #ffffff !important;' : ''"></i>
    <!-- <img src="/images/add_cart.png" class="icon icon-shopping-bag-open-light fs-24"  :style="this_item.is_carted == 1 ? 'filter: invert(1); !important;' : ''"> -->
  </button>
  <a href="javascript:void(0)" v-else-if="single == 1" @click.prevent="add_to_cart(true)"
    class="btn btn-secondary bg-hover-primary border-hover-primary px-sm-7 px-3">
    {{ $t('main.add to cart') }}
  </a>
  <form v-else-if="single == 2">
    <div class="row align-items-end no-gutters mx-n2">
      <div class="col-sm-6 form-group px-2 mb-5">
        <label class="text-secondary font-weight-600 mb-4" for="quantity">{{ $t('main.Quantity') }}: </label>
        <select name="size" class="form-control w-100 border-0" v-model="quantity">
          <option v-for="n in 100" :value="n">{{ n }}</option>
        </select>
      </div>
      <div class="col-12 px-2">
        <button type="submit" @click.prevent="addToCart"
          class="btn btn-lg fs-18 btn-secondary btn-block h-60 bg-hover-primary border-0">
          {{ $t('main.add to cart') }}
        </button>
      </div>
    </div>
  </form>
  <span v-else class="">نفذت</span>
</template>

<script>
export default {
  components: {},

  props: {
    item: {
      type: Object,
      default: "",
    },
    user: {
      type: Object,
      default: () => ({
        id: 0,
        activate: 0,
      }),
    },
    single: {
      type: Number,
      default: 1,
    },
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      this_item: this.item,
      quantity: this.item.min_quantity,
    };
  },
  mounted() {
    this.$root.$on("getItemQuick", (data) => {
      this.quantity = data.min_quantity;
    });
    this.$root.$on("deleteItem", (data) => {
      console.log(data);
      if (this.this_item.id == data.item_id) {
        this.this_item.is_carted = 0;
      }
    });
    this.$root.$on("CartUpdateItem", (data) => {
      if (this.this_item.id == data.id) {
        this.this_item.is_carted = 1;
      }
    });
  },

  methods: {
    add_to_cart: function (status) {
      if (this.user.id == 0) {
        let instance = Vue.$toast.warning(this.$t('main.login first'), {
          position: "top-left",
        });
        $("#sign-in").modal("show");
        return;
      }
      if (status) {
        $("#addToCartBox").modal("show");
        this.$root.$emit("getItem", this.this_item);
      } else {
        $("#addToCartBox").modal("hide");
      }
    },
    addToCart: async function () {
      try {
        if (this.user.id == 0) {
          Vue.$toast.warning(this.$t('main.login first'), {
            position: "top-left",
          });
          return;
        }
        if (this.item.min_quantity > this.quantity) {
          this.$toast.error(this.$t('main.The minimum quantity to buy is') + this.item.min_quantity);
          return;
        }
        var formData = new FormData();
        formData.set('id', this.item.id);
        formData.set('quantity', this.quantity);
        let res = await axios.post('/' + this.lang + '/cart/store', formData);

        if (res.status == 200) {
          this.this_item.is_carted = 1;
          this.$toast.success(res.data.message);
          this.$root.$emit('updateCart', res.data.items);
          this.$root.$emit('updateCountItems', res.data.count_items);
          this.$root.$emit('CartUpdateItem', this.item);
        } else {
          this.$toast.error(res.data.message)
        }
      } catch (res) {
        // console.log(res);
        // this.$toast.error(res.data.message);
      }
    },
  },
};
</script>
<style>
/* i {
  color: #4e0161 !important;
}

i:hover {
  color: #000000 !important;
} */

.cart-button:hover {
  color: #fff !important;
}

.cart-button:focus {
  color: #fff !important;
}

i:hover {
  color: #000 !important;
}
</style>