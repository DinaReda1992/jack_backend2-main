<template>
  <section style="overflow:auto;" class="pb-lg-13 pb-11">
    <div class="container">
      <h2 class="text-center my-9">{{$t('main.complete_order')}}</h2>
      <form>
        <div class="row">
          <div class="col-lg-4 pb-lg-0 pb-11 order-lg-last">
            <div class="card border-0" style="box-shadow: 0 0 10px 0 rgba(0,0,0,0.1)">
              <div class="card-header px-0 mx-6 bg-transparent py-5">
                <h4 class="fs-24 mb-5"> {{$t('main.enter-promo-code')}}</h4>
                <div class="d-flex align-items-center mb-2">
                  <input id="promo-code" v-model="coupon" type="text" name="promo-code" class="form-control">
                  <button class="btn btn-secondary px-3 border-0" @click.prevent="check_cobon">{{$t('main.apply')}}</button>
                </div>
              </div>
              <div class="card-body px-6 pt-5">
                <div class="d-flex align-items-center mb-2">
                  <span> {{$t('main.Count Products')}}:</span>
                  <span class="d-block ml-auto text-secondary font-weight-bold">{{ items.length }}</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                  <span> {{$t('main.Price Products')}}:</span>
                  <span class="d-block ml-auto text-secondary font-weight-bold">{{
                    parseFloat(summary_sub_total).toFixed(2)
                  }} {{ $t('main.SAR') }}</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                  <span>{{$t('main.Shipping price')}}:</span>
                  <span class="d-block ml-auto text-secondary font-weight-bold">{{ shipmentprice }}
                    {{ $t('main.SAR') }}</span>
                </div>
                <div class="d-flex align-items-center mb-2" v-if="discount_price > 0">
                  <span>{{$t('main.Discount price')}}:</span>
                  <span class="d-block ml-auto text-secondary font-weight-bold">{{ -
                  parseFloat(discount_price).toFixed(2) }} {{ $t('main.SAR') }}</span>
                </div>
                <div class="d-flex align-items-center mb-2" v-if="coupon_discount > 0">
                  <span>{{ $t('main.Promotion') }} </span>
                  <span class="d-block ml-auto text-secondary font-weight-bold">- {{ coupon_discount }}
                    {{ $t('main.SAR') }}</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                  <span> {{$t('main.tax')}} (15%): </span>
                  <span class="d-block ml-auto text-secondary font-weight-bold"> {{ parseFloat(price_vat).toFixed(2) }}
                    {{ $t('main.SAR') }}</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                  <span>{{ $t('main.Total') }}:</span>
                  <span class="d-block ml-auto text-secondary font-weight-bold">{{ parseFloat(shipmentprice+ total -
                  coupon_discount).toFixed(2) }}
                    {{ $t('main.SAR') }}</span>
                </div>

              </div>
              <div class="card-footer bg-transparent px-0 pb-1 mx-6">
                <div class="d-flex align-items-center font-weight-bold mb-3">
                  <span class="text-secondary">{{ $t('main.Total') }}:</span>
                  <span class="d-block ml-auto text-secondary fs-24 font-weight-bold"> {{
                  parseFloat(shipmentprice+ total - coupon_discount).toFixed(2) }} {{ $t('main.SAR') }}</span>
                </div>
                <div class="summary-checkout">
                  <form method="post" :action="'/'+lang+'/choose-payment'">
                    <input type="hidden" name="_token" :value="csrf">
                    <button id="addPaymentMethod" type="submit" class="btn btn-secondary px-3 border-0 w-100 pb-3">{{
                      $t('main.Payment')
                    }}</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-8" style="overflow:auto;">
            <div class="address-box">
              <div class="card-lst-item-title">
                <h5> {{$t('main.Shipping Address')}} </h5>
              </div>
              <div class="p-addresses-list">
                <div class="adrs-item">
                  <h5> {{ address.address }} </h5>
                  <div class="adrs-no">
                    <h5> test2 - {{ address.address }} - {{ address.region.name }} </h5>
                    <p> +966 {{ address.phone1 }} </p>
                  </div>
                  <div class="btns-opt"><a type="button" href="/addresses" class="btn edit_button">{{$t('main.Change Address')}}</a>
                  </div>
                </div>
              </div>
            </div>
            <table class=" table border table-bordered">
              <thead style="background-color: #F5F5F5">
                <tr class="fs-15 letter-spacing-01 font-weight-600 text-uppercase text-secondary text-center">
                  <th scope="col" class="border-1x pl-7">{{ $t('main.product') }}</th>
                  <th scope="col" class="border-1x">{{$t('main.quantity')}}</th>
                  <th colspan="2" class="border-1x">{{$t('main.price')}}</th>
                </tr>
              </thead>
              <tbody>
                <tr class="position-relative" v-for="(item, index ) in all_items">
                  <th scope="row" class="w-xl-695 pl-xl-5 py-4">
                    <div class="media align-items-center">
                      <input class="checkbox-primary w-15px h-15px" type="checkbox" name="check-product"
                        value="checkbox">
                      <div class="ml-3 mr-4">
                        <img :src="item.product.photo" :alt="item.product.title" class="mw-75px image-cart">
                      </div>
                      <div class="media-body w-128px">
                        <p class="font-weight-500 mb-1 text-secondary">{{ item.product.title }}</p>
                        <p class="card-text font-weight-bold fs-14 mb-1 text-secondary"
                          v-if="item.product.offer_price > 0">
                          <span class="fs-13 font-weight-500 text-decoration-through text-body pr-1">{{
                            item.product.price
                          }} {{ $t('main.SAR') }}</span>
                          <span>{{ item.product.offer_price }} {{ $t('main.SAR') }}</span>
                        </p>
                        <p class="card-text font-weight-bold fs-14 mb-1 text-secondary" v-else>
                          <span>{{ item.product.price }} {{ $t('main.SAR') }}</span>
                        </p>
                      </div>
                    </div>
                  </th>
                  <td class="align-middle">
                    <div class="input-group position-relative w-128px">
                      <select style="text-align:right;" name="inputNumber" id="inputNumber" v-model="item.quantity"
                        @change="updateItem(item)"
                        class="form-control form-control-sm px-6 fs-16 text-center input-quality border-0 h-35px">
                        <option v-for="n in 100" :value="n">{{ n }}</option>
                      </select>
                    </div>
                  </td>
                  <td class="align-middle">
                    <p class="mb-0 text-secondary font-weight-bold mr-xl-11" v-if="item.product.offer_price > 0">{{
                      parseFloat(item.item_total).toFixed(2)
                    }} {{ $t('main.SAR') }}</p>
                    <p class="mb-0 text-secondary font-weight-bold mr-xl-11" v-else>{{
                      parseFloat(item.item_total).toFixed(2)
                    }} {{ $t('main.SAR') }}</p>
                  </td>
                  <td class="align-middle text-right pr-5"><a href="javascript:void(0)" class="d-block"
                      @click.prevent="deleteItem(item)"><i class="fal fa-times text-body"></i></a></td>
                </tr>
                <tr v-if="all_items.length == 0" class="text-center">
                  <td colspan="4">
                    <img src="/images/cart.png" alt="cart" style="height: 250px;" />
                    <h4>{{ $t('main.Not Found Products In Cart') }}</h4>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </form>
    </div>
  </section>
</template>

<script>
import Vue from 'vue';

export default {
  props: {
    items: {
      type: Array,
      default: "",
    },
    user: {
      type: Object,
      default: ""
    },
    address: {
      type: Object,
      default: ""
    },
    shipmentprice: {
      type: Number,
      default: 0
    },
  },
  data() {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      errors: [],
      lang: window.lang,
      loading: false,
      all_items: this.items,
      loading_cobon: false,
      coupon_discount: 0,
      coupon: '',
      loading_cart: false,
      cobon_erorr: '',
      cobon: '',
    };
  },
  mounted() {
    localStorage.setItem('code', '')
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
    },
    discount_price: function () {
      let discount_price = [];
      Object.entries(this.all_items).forEach(([key, val]) => {
        discount_price.push(val.discount_price)
      });
      return discount_price.reduce(function (discount_price, num) { return discount_price + num }, 0);
    },
    price_vat: function () {
      let price_vat = [];
      Object.entries(this.all_items).forEach(([key, val]) => {
        price_vat.push(val.price_vat)
      });
      return price_vat.reduce(function (price_vat, num) { return price_vat + num }, 0);
    },
    summary_sub_total: function () {
      let summary_sub_total = [];
      Object.entries(this.all_items).forEach(([key, val]) => {
        summary_sub_total.push(val.summary_sub_total)
      });
      return summary_sub_total.reduce(function (summary_sub_total, num) { return summary_sub_total + num }, 0);
    }

  },
  methods: {
    mathRound: function (num) {
      return Math.round(num * 100) / 100
    },
    updateItem: async function (item, type = 0) {
      try {
        this.loading_cart = true
        var formData = new FormData();
        var newQuantity = item.quantity + type;
        formData.set('id', item.product.id);
        formData.set('quantity', newQuantity);
        let res = await axios.post('/'+this.lang+'/cart/update', formData);
        if (res.status == 200) {
          this.loading_cart = false
          this.$root.$emit('updateCart', res.data.items)
          this.$toast.success(res.data.message)
        }
        else if (res.status == 202) {
          this.loading_cart = false
          let id = item.id
          const index = this.items.findIndex(item => item.id == id);
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
    deleteItem: async function (item) {
      try {
        var r = false;
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
                let res = await axios.post('/'+this.lang+'/cart/delete', params);
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
              }
            }
          }
        )
      } catch (res) {
        this.loading = false
        console.log(res)
      }
    },

    check_cobon: async function () {
      try {
        this.loading_cobon = true;
        var formData = new FormData();
        formData.set('code', this.coupon);
        let res = await axios.post('/'+this.lang+'/check-coupon', formData);
        if (res.status == 200) {
          if (res.data.status == 200) {
            this.loading_cobon = false;
            this.$toast.success(res.data.message)
            localStorage.setItem('code', this.coupon)
            this.coupon_discount = res.data.money
          } else {
            this.loading_cobon = false
            this.cobon_erorr = res.data.message
            this.$toast.error(res.data.message)
          }

        } else {

          this.loading_cobon = false
          this.$toast.error({
            title: res.data.message
          })
        }

      } catch (res) {
        console.log(res)
        /*this.$toast.error({
          title:res.data.message
        })*/
      }

    },

  },
};
</script>
<style>
.adrs-item {
  background: #fff;
  padding: 3px 15px;
  margin-bottom: 10px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  position: relative;
  background: #F8F8F8;
  border: 1px dashed #818181;
  border-radius: 4px;
}

.adrs-no {
  display: flex;
  align-items: center;
}

.btns-opt {
  line-height: initial;
}

.adrs-no h5,
.adrs-no p {
  font-size: 14px;
  color: #000;
  margin-left: 25px;
  line-height: 26px;
}

.phn-no {
  direction: ltr;
}
</style>