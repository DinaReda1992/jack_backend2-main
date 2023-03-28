<template>
  <div class="dropdown drop-cart">

    <a class="btn" type="button" id="cart" data-bs-toggle="dropdown" aria-expanded="false">
      <div>
        <i class="las la-shopping-cart"></i>
      </div>
      <span>
        السلة
        <span class="badge">{{ all_items.length }}</span>
      </span>
    </a>
    <div class="shopping-cart dropdown-menu" aria-labelledby="cart">
      <form>
        <ul class="shopping-cart-items">

          <li v-for="(item, index ) in all_items" class="clearfix">
            <img :src="item.product.photo" width="50" :alt="item.title" />
            <span class="item-name">{{ item.product.title }}</span>
            <span class="item-quantity">
              <div class="input-number-wrapper">
                <select @change="update_item(item)" v-model="item.quantity" class="w-100">
                  <template v-for="num in 500">
                    <option v-if="num >= item.product.min_quantity">{{ num }}</option>
                  </template>
                </select>



                <!--                  <a class="decrease btn"><i class="las la-minus"></i></a>-->
                <!--
                 <button class=" btn" :disabled="loading_cart"  @click.prevent="update_item(item)" >
                    <i class="las la-check" v-if="!loading_cart"></i>
                    <i class="las la-spinner" v-else></i>
                  </button>
                  <input type="number" @change="update_item(item)" :min="item.min_quantity" max="100" v-model="item.quantity"/>
-->

              </div>


            </span>
            <span style="direction: ltr" class="item-price">
              {{ item.product.price }}
              sar
            </span>
            <a href="javascript:void(0)" class="item-delete" @click.prevent="delete_item(item.id)">
              <div class="icons">
                <i class="la la-times"></i>
                <i class="las la-trash"></i>
              </div>
            </a>
          </li>
        </ul>
        <a href="/complete-order" class="button hvr-sweep-to-left">
          <span>
            استكمال الطلب
          </span>
          <span style="direction: ltr" class="total_price">
            {{ mathRound(total) }} sar
          </span>
        </a>
      </form>
    </div>
  </div>
</template>



<script>
import CxltToastr from 'cxlt-vue2-toastr'
var toastrConfigs = {
  position: 'top right',
  timeOut: 5000
}
Vue.use(CxltToastr, toastrConfigs)

export default {
  components: {

  },

  props: {
    items: {
      type: Array,
      default: ""
    },

  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      loading_cart: false,
      all_items: this.items

    }
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
        total.push(val.item_total) // the value of the current key.
      });

      return total.reduce(function (total, num) { return total + num }, 0);

    }
  },

  methods: {
    mathRound: function (num) {
      return Math.round(num * 100) / 100

    },
    updateCoordinates(location) {
      let marker = {
        lat: location.latLng.lat(),
        lng: location.latLng.lng(),
      }
      this.marker = marker
      this.add_address.latitude = marker.lat
      this.add_address.longitude = marker.lng
    },

    update_item: async function (item) {
      try {
        // this.$toast.removeAll()
        this.loading_cart = true
        var formData = new FormData();
        formData.set('id', item.product.id);
        formData.set('quantity', item.quantity);
        let res = await axios.post('/' + this.lang + '/cart/update', formData);
        if (res.status == 200) {
          this.loading_cart = false
          this.$root.$emit('updateCart', res.data.items)

          this.$toast.success(res.data.message)
        }
        else if (res.status == 202) {
          this.loading_cart = false
          let id = item.id
          const index = this.items.findIndex(item => item.id == id);
          // item.quantity=item.product.min_quantity
          item.quantity = res.data.min_quantity

          this.all_items[index] = item
          this.$toast.error(res.data.message);
        } else {
          this.loading_cart = false
          this.$toast.error(res.data.message);
        }

      } catch (res) {
        console.log(res)
        this.$toast.error(res.data.message);
      }

    },

    delete_item: async function (item) {
      try {
        var r = confirm("هل أنت متأكد من حذف المنتج؟")

        if (r == true) {
          // this.$toast.removeAll()
          this.loading = true
          let params = {
            id: item,
          };
          let res = await axios.post('/' + this.lang + '/cart/delete', params);
          if (res.status == 200) {
            this.all_items = res.data.items
            this.$root.$emit('updateCart', res.data.items)

            this.$toast.success(res.data.message)
          } else {
            let errors = res.data.errors
            var i;
            for (i = 0; i < errors.length; i++) {
              this.$toast.error(errors[i]);
            }
          }

        } else {
          /* alert('a')
           var __FOUND = this.all_addresses.find(function(item, index) {
             if(item.id == address)
               return true;
           });
           console.log(__FOUND)*/
        }


      } catch (res) {
        this.loading = false
        console.log(res)
        // this.handleError(this.fetchColor);
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

.shopping-cart-items select:focus {
  outline: none;
}
</style>
