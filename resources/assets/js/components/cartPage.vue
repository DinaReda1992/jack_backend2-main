<template>
  <section class="pb-11 pb-lg-13">
    <div class="container">
      <h2 class="text-center mt-9 mb-8">{{$t('main.Cart')}}</h2>
      <form class="table-responsive-md pb-8 pb-lg-10">
        <div>
          <ul style="list-style: arabic-indic;">
            <li v-for="(message, index) in messages" class="alert alert-warning mx-1 px-2">{{ message }}</li>
          </ul>
        </div>
        <table class=" table border shop table-bordered" id="cart-table">
          <thead style="background-color: #F5F5F5" class="text-center">
            <tr class="fs-15 letter-spacing-01 font-weight-600 text-uppercase text-secondary">
              <th scope="col" class="border-1x pl-7">{{$t('main.Product')}}</th>
              <th scope="col" class="border-1x">{{$t('main.Quantity')}}</th>
              <th colspan="2" class="border-1x">{{$t('main.Price')}}</th>
            </tr>
          </thead>
          <tbody>
            <tr class="position-relative" v-for="(item, index ) in all_items">
              <th scope="row" class="w-xl-695 pl-xl-5 py-4">
                <div class="media align-items-center">
                  <input class="checkbox-primary w-15px h-15px" type="checkbox" name="check-product" value="checkbox">
                  <div class="ml-3 mr-4">
                    <img :src="item.product.photo" :alt="item.product.title" class="mw-75px image-cart">
                  </div>
                  <div class="media-body w-128px">
                    <p class="font-weight-500 mb-1 text-secondary text-right">{{ item.product.title }}</p>
                    <p class="card-text font-weight-bold fs-14 mb-1 text-secondary" v-if="item.product.offer_price > 0">
                      <span class="fs-13 font-weight-500 text-decoration-through text-body pr-1">{{
                        item.product.price
                      }} {{$t('main.SAR')}}</span>
                      <span>{{ item.product.offer_price }} {{$t('main.SAR')}}</span>
                    </p>
                    <p class="card-text font-weight-bold fs-14 mb-1 text-secondary" v-else>
                      <span>{{ item.product.price }} {{$t('main.SAR')}}</span>
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
                <p class="mb-0 text-secondary font-weight-bold mr-xl-11" v-if="item.product.offer_price > 0">
                  {{ parseFloat(item.item_total).toFixed(2) }} {{$t('main.SAR')}}</p>
                <p class="mb-0 text-secondary font-weight-bold mr-xl-11" v-else>
                  {{ parseFloat(item.item_total).toFixed(2) }} {{$t('main.SAR')}}</p>
              </td>
              <td class="align-middle text-right pr-5"><a href="javascript:void(0)" class="d-block"
                  @click.prevent="deleteItem(item)"><i class="fal fa-times text-body"></i></a></td>
            </tr>
            <tr v-if="all_items.length == 0" class="text-center">
              <td colspan="4">
                <img src="/images/cart.png" alt="cart" style="height: 250px;" />
                <h4>{{$t('main.Not Found Products In Cart')}}</h4>
              </td>
            </tr>
          </tbody>
        </table>
        <table class=" table border mobile">
          <thead style="background-color: #F5F5F5">
            <tr class="fs-15 letter-spacing-01 font-weight-600 text-uppercase text-secondary">
              <th scope="col" class="border-1x pl-7">{{$t('main.Product')}}</th>
              <th scope="col" class="border-1x">{{$t('main.Quantity')}}</th>
            </tr>
          </thead>
          <tbody>
            <tr class="position-relative" v-for="(item, index ) in all_items">
              <th scope="row" class="w-xl-695 pl-xl-5 py-4">
                <div class="media align-items-center">
                  <input class="checkbox-primary w-15px h-15px" type="checkbox" name="check-product" value="checkbox">
                  <div class="ml-3 mr-4">
                    <img :src="item.product.photo" :alt="item.product.title" class="mw-75px">
                  </div>
                  <div class="media-body w-128px">
                    <p class="font-weight-500 mb-1 text-secondary">{{ item.product.title }}</p>
                    <p class="card-text font-weight-bold fs-14 mb-1 text-secondary" v-if="item.product.offer_price > 0">
                      <span class="fs-13 font-weight-500 text-decoration-through text-body pr-1">{{
                        item.product.price
                      }} {{$t('main.SAR')}}</span>
                      <span>{{ item.product.offer_price }} {{$t('main.SAR')}}</span>
                    </p>
                    <p class="card-text font-weight-bold fs-14 mb-1 text-secondary" v-else>
                      <span>{{ item.product.price }} {{$t('main.SAR')}}</span>
                    </p>
                  </div>
                </div>
              </th>
              <td class="align-middle">
                <div class="input-group position-relative">
                  <select style="text-align:right;" name="inputNumber" id="inputNumber" v-model="item.quantity"
                    @change="updateItem(item)"
                    class="form-control form-control-sm px-6 fs-16 text-center input-quality border-0 h-35px">
                    <option v-for="n in 100" :value="n">{{ n }}</option>
                  </select>
                  <a href="javascript:void(0)" class="d-block" @click.prevent="deleteItem(item)"><i
                      class="fal fa-times text-body"></i></a>
                </div>
              </td>

            </tr>
            <tr v-if="all_items.length == 0" class="text-center">
              <td colspan="2">
                <img src="/images/cart.png" alt="cart" style="height: 150px;" />
                <h3>{{$t('main.Not Found Products In Cart')}}</h3>
              </td>
            </tr>
          </tbody>
        </table>

      </form>
      <div class="row">
        <div class="col-lg-12 pt-lg-0 pt-11">
          <div class="card border-0" style="box-shadow: 0 0 10px 0 rgba(0,0,0,0.1)">
            <div class="card-footer bg-transparent px-0 pb-4 mx-6">
              <div class="d-flex align-items-center font-weight-bold mb-3">
                <span class="text-secondary">{{$t('main.Total')}}:</span>
                <span class="d-block ml-auto text-secondary fs-24 font-weight-bold">{{
                  parseFloat(total).toFixed(2)
                }}  {{$t('main.SAR')}}</span>
              </div>
              <form method="post" :action="'/'+lang+'/summary'">
                <input type="hidden" name="_token" :value="csrf">
                <button :disabled="loading || all_items.length == 0" type="submit"
                  class="btn btn-secondary btn-block bg-hover-primary border-hover-primary">
                  {{$t('main.buy')}}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import Vue from 'vue';

export default {
  props: {
    messages: {
      type: Array,
      default: []
    },
    items: {
      type: Array,
      default: ""
    }
  },
  data() {
    return {
      csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
        let res = await axios.post('/'+this.lang+'/cart/update', formData);
        if (res.status == 200) {
          this.loading = false;
          this.$root.$emit('updateCart', res.data.items);
          this.$toast.success(res.data.message)
        }
        else if (res.status == 202) {
          this.loading = false
          let id = item.id
          const index = this.items.findIndex(item => item.id == id);
          item.quantity = res.data.min_quantity;
          this.all_items[index] = item
          this.$toast.error(res.data.message);
        } else {
          this.loading = false
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
                  this.loading = false;
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
        this.loading = false;
        console.log(res)
      }
    },

  },
};
</script>
