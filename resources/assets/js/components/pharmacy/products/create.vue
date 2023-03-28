<template>
  <div>
    <div>
      <div class="row">


        <div class="products col-md-10">
          <div class="form-group position-relative">
            <label>{{ $t('main.Search products') }}</label>
            <input class="form-control" @focus="show_search_products" :placeholder="$t('main.Search products')" />

            <div class="modal fade in show " v-if="products_popup" id="exampleModalScrollable" tabindex="-1"
              role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
              <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">{{ $t('main.Add products') }}</h5>
                    <button @click.prevent="show_search_products" type="button" class="close" data-dismiss="modal"
                      aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <input class="form-control" :placeholder="$t('main.Search products')"
                        @keyup="search_products_keyup" v-model="search_product" />
                    </div>
                    <div class="row">
                      <div class="form-group col-md-6">
                        <multiselect v-model="category_id" :options="categories" :multiple="false" :selectLabel="''"
                          :selectedLabel="''" :deselectLabel="''" :close-on-select="true" :clear-on-select="false"
                          :searchable="true" @select="getSubCategories" @remove="reset_search(false)"
                          :placeholder="$t('main.Select Main Category')" label="name" track-by="name"
                          :preselect-first="false">
                        </multiselect>
                      </div>
                      <div class="form-group col-md-6">
                        <multiselect v-model="sub_category_id" :options="sub_categories" :multiple="false"
                          :selectLabel="''" :selectedLabel="''" :deselectLabel="''" :close-on-select="true"
                          :clear-on-select="false" :searchable="false" @select="reset_search"
                          :placeholder="$t('main.Select Sub Category')" label="name" track-by="name"
                          :preselect-first="false">
                        </multiselect>
                      </div>
                    </div>
                    <div class="_2d71edc9" id="infinite_list">
                      <div v-for="(product, index) in products" class="eb7d03bb">
                        <div class="_220ab7ca">
                          <input type="radio" v-model="items" :value="product.id" :id="'checkbox-' + product.id"
                            class="_13c2491b">
                          <label :for="'checkbox-' + product.id" class="d953d938"></label>
                        </div>
                        <img :src="product.photo">
                        <p class="_233f655c">{{ product.title }}</p>
                        <p class="_81592708"><span class="">{{ product.quantity }}</span> &nbsp;{{
                          $t('main.In Store')
                        }}
                        </p>
                        <p class="b0c57c6a">SAR {{ product.price }}</p>
                      </div>

                      <infinite-loading :identifier="infiniteId" @infinite="search_products"
                        force-use-infinite-wrapper="._2d71edc9">
                        <div slot="no-results"></div>
                        <div slot="no-more"></div>
                      </infinite-loading>



                    </div>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click.prevent="show_search_products"
                      data-dismiss="modal">{{ $t('main.Close') }}</button>
                    <button type="button" :disabled="loading_cart" @click.prevent="store_item" class="btn btn-primary">
                      {{ $t('main.Add products') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>


          </div>

          <div v-if="product != ''">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <td>{{ $t('main.Product') }}</td>
                  <td>{{ $t('main.Supply Price') }}</td>
                  <td>{{ $t('main.Client Price') }}</td>
                  <td>{{ $t('main.Brand') }}</td>
                  <td> {{ $t('main.Company') }}</td>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <img class="product_img d-inline-block" :src="product.photo" />
                    <span class="d-inline-block">{{ product.title }}</span>
                  </td>
                  <td>
                    {{ product.price }} SAR
                  </td>
                  <td>
                    {{ product.client_price }} SAR
                  </td>
                  <td>
                    {{ product.brand ? product.brand.name : '' }}
                  </td>
                  <td>
                    <img class="product_img d-inline-block" :src="product.user.photo" />
                    <span class="d-inline-block">{{ product.user.username }}</span>
                  </td>

                </tr>
              </tbody>
            </table>

            <div class="form-group row mt-4 ">
              <label class="col-md-3">{{ $t('main.Quantity In Store') }}</label>
              <div class="col-md-9">
                <input v-model="quantity" type="number" class="form-control" />
              </div>
            </div>
            <div class="form-group row ">
              <label class="col-md-3">{{ $t('main.Minimum Quantity In Store') }}</label>
              <div class="col-md-9">
                <input v-model="min_quantity" type="number" class="form-control" />
              </div>
            </div>

            <div class="form-group row ">
              <label class="col-md-3">{{ $t('main.Gift') }}</label>
              <div class="col-md-9">
                <input v-model="is_gift" type="checkbox" />
              </div>
            </div>
          </div>

        </div>
      </div>


      <div class="text-right">
        <button class="btn btn-success" :disabled="loading_next" @click.prevent="create">{{$t('main.Add product')}}</button>
      </div>
    </div>





  </div>

</template>



<script>
import CxltToastr from 'cxlt-vue2-toastr'
Vue.component('multiselect', Multiselect)
import Multiselect from 'vue-multiselect'
var toastrConfigs = {
  position: 'top right',
  timeOut: 5000
}
Vue.use(CxltToastr, toastrConfigs)

export default {
  components: {
    Multiselect
  },

  props: {

    categories: {
      type: Array,
      default: ""
    },
    base_url: {
      type: String,
      default: 'pharmacy-panel'
    },
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      done: false,
      users_popup: false,
      loading_products: false,
      loading_update: false,
      products_popup: false,
      loading: false,
      loading_users: false,
      loading_cart: false,
      loading_next: false,
      activeItem: 'home',
      all_users: [],
      products: [],
      user_id: null,
      search_user: '',
      search_product: '',
      address_id: '',
      page: 1,
      order_id: '',
      infiniteId: Math.random(),
      items: '',
      all_items: [],
      category_id: '',
      sub_category_id: '',
      sub_categories: [],
      product: '',
      quantity: '',
      min_quantity: '',
      is_gift: false
    }
  },
  mounted() {

    /* this.$root.$on('updateCart', data => {
      this.all_items=data
     });*/

  },
  computed: {


  },

  methods: {

    select_user(user) {
      this.user = user
      this.user_id = user.id
      this.users_popup = false
      this.search_user = user.username
    },
    getSubCategories(value, id) {
      let type_id = value.category_id
      /* var __FOUND = this.aqar_types.find(function(type, index) {
           if(type.id == type_id)
               return true;
       });*/
      this.sub_category_id = ''
      this.sub_categories = value.sub_categories
      this.page = 1
      this.products = []
      this.infiniteId = Math.random()
    },
    show_search() {
      this.users_popup = true
    },
    show_search_products() {
      this.products_popup = !this.products_popup
    },
    search_products_keyup() {
      this.page = 1
      this.products = []
      this.infiniteId = Math.random()
    },
    reset_search(clear = true) {
      if (!clear) {
        this.sub_category_id = null
        this.sub_categories = []
      }
      this.page = 1
      this.products = []
      this.infiniteId = Math.random()
    },

    search_products: async function ($state) {
      try {
        // this.$toast.removeAll()
        this.loading_products = true
        let params = {
          search: this.search_product,
          page: this.page
        }
        if (this.category_id != '' && this.category_id != null) {
          params.category_id = this.category_id.id
        }
        if (this.sub_category_id != '' && this.sub_category_id != null) {
          params.sub_category_id = this.sub_category_id.id
        }
        let res = await axios.get('/' + this.base_url + '/products/search-products', { params });
        if (res.status == 200) {
          this.loading_products = false
          if (res.data.data.data.length > 0) {
            this.page += 1;
            if (this.products.length > 0) {
              let exist = false;
              let productObject = null;
              this.products.forEach((product) => {
                exist = false;
                res.data.data.data.forEach((productObj) => {
                  productObject = productObj;
                  console.log(productObj.id, product.id);
                  if (parseInt(productObj.id) === parseInt(product.id)) {
                    exist = true;
                  }
                });
                if (exist === false) {
                  this.products.push(productObject);
                }
              });
              this.products = this.products.sort().filter(function (item, pos, ary) {
                return !pos || item != ary[pos - 1];
              });
            } else {
              this.products.push(...res.data.data.data);
            }
            // let old_products=this.products
            // let ids = old_products.map(o => o.id)
            // let filtered = old_products.filter(({id}, index) => !ids.includes(id, index + 1))
            // this.products=filtered

            $state.loaded();
            // $state.complete();

          } else {
            // alert('a')
            this.page = 1
            $state.complete();
          }


        }
        else if (res.status == 202) {
          this.loading_products = false

          // this.$toast.error(
          //   title:res.data.message
          // })
        } else {
          this.loading_products = false
          // this.$toast.error(
          //   title:res.data.message
          // })
        }

      } catch (res) {
        console.log(res)
        this.$toast.error(res.data.message);
      }

    },

    store_item: async function () {
      try {
        // this.$toast.removeAll()
        this.loading_cart = true

        let items = this.items
        let products = this.products

        let result = products.filter(obj => {
          return obj.id === items
        })
        // console.log(result[0])
        this.product = result[0]
        this.loading_cart = false
        this.products_popup = false

      } catch (res) {
        console.log(res)
        this.$toast.error(res.data.message);
      }

    },




    create: async function () {
      try {
        // this.$toast.removeAll()
        if (this.product == '') {
          this.$toast.error(
            'قم باختيار منتج أولا'
          )
          return;
        }
        if (this.quantity == '' || this.min_quantity == '') {
          this.$toast.error(
            'قم باستكمال البيانات'
          )
          return;
        }
        this.loading_next = true
        var formData = new FormData();
        formData.set('product_id', this.product.id);
        formData.set('quantity', this.quantity);
        formData.set('min_quantity', this.min_quantity);
        formData.set('is_gift', this.is_gift ? 1 : 0);

        let res = await axios.post('/' + this.base_url + '/products', formData);
        if (res.status == 200) {
          this.reset_search()
          this.loading_next = false
          this.$toast.success(res.data.message)
          this.items = ''
          this.product = ''
          this.quantity = ''
          this.min_quantity = ''
          this.is_gift = false;
          // this.done=true
        }
        else if (res.status == 202) {
          this.loading_next = false

          this.$toast.error(res.data.message);
        } else {
          this.loading_next = false
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
<style src="vue-multiselect/dist/vue-multiselect.min.css">

</style>

<style>
table img.product_img {
  width: 38px;
  height: 38px;
  border-radius: 7px;
  box-shadow: 0 2px 5px rgb(92 152 195 / 30%);
}

td {
  background: #fff;
  border: 1px solid #ddd !important;
}

.price-div {
  display: flex;
  align-items: center;
  min-width: 200px;

}

.input-qty {
  display: inline-block;
  height: calc(1.5em + 0.5rem + 3px);
  padding: 0.25rem 0.5rem;
  font-size: 0.975rem;
  line-height: 1.5;
  max-width: 50px;
  color: #45a787;
  font-weight: 600;
  text-align: center;
}

#users {
  position: absolute;
  top: 100%;
  right: 0;
  min-width: 100%;
  background: #fff;
  border: 1px solid #d2d6de;
  /*box-shadow: 0 3px 5px rgb(0 0 0);*/
  border-radius: 0 0 4px 4px;
  max-height: 220px;
  overflow: hidden auto;
  padding: 4px 0;
  z-index: 2;
}

#users ul {
  margin: 0;
  padding: 0;
  list-style: none;
}

#users li {
  display: block;
  padding: 4px 24px;
  cursor: pointer;
}

.rounded-circle {
  border-radius: 50%;
}

._2d71edc9 {
  height: 336px;
  overflow-y: scroll;
}

.eb7d03bb {
  padding: 20px 30px;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  border-bottom: 1px solid #e0e0e0;
}

._220ab7ca {
  position: relative;
  display: inline-block;
  width: 16px;
  height: 16px;
}

.eb7d03bb img {
  width: 50px;
  height: 50px;
  border-radius: 7px;
  box-shadow: 0 2px 5px rgb(92 152 195 / 30%);
  margin: 0 30px;
}

.eb7d03bb p {
  margin-bottom: 0;
  font-size: 16px;
}

._233f655c {
  color: #45a787;
  flex-basis: 20%;
}

.eb7d03bb p {
  margin-bottom: 0;
  font-size: 16px;
}

._81592708 {
  margin: 0 auto;
  flex-basis: 20%;
  text-align: center;
}

.eb7d03bb p {
  margin-bottom: 0;
  font-size: 16px;
}

@media (min-width: 769px) {
  .modal-dialog {
    width: 54%;
    margin: 30px auto;
  }
}

.d953d938 {
  cursor: pointer;
  line-height: 16px;
  font-size: 16px;
  font-weight: 500;
  margin: 0;
  padding: 0;
}

.d953d938:not(._4ff9577b)::before {
  left: 0;
}

.d953d938::before {
  content: '';
  display: inline-block;
  width: 16px;
  height: 16px;
  background: #fff;
  border: 2px solid #c5bacb;
  border-radius: 50%;
  position: absolute;
  top: 0;
  transition: background-color 0.15s ease;
}

.d953d938:not(._4ff9577b)::after {
  left: 5px;
}

.d953d938::after {
  content: '';
  display: block;
  position: absolute;
  top: 2px;
  width: 6px;
  height: 9px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
  opacity: 0;
  transition: opacity 0.15s ease;
}

._13c2491b {
  opacity: 0;
  width: 0;
  height: 0;
}

._13c2491b:checked+.d953d938::before {
  background: #45a787;
  border-color: #45a787;
}

.d953d938::after {
  content: '';
  display: block;
  position: absolute;
  top: 2px;
  width: 6px;
  height: 9px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(45deg);
  opacity: 0;
  transition: opacity 0.15s ease;
}

._13c2491b:checked+.d953d938::after {
  opacity: 1;
}

ul {
  margin: 0;
  padding: 0;
  list-style: none;
}

[dir=rtl] .multiselect__select {
  right: auto;
  left: 25px;
  top: 8px;
}

.multiselect {
  padding-right: 0;

}

.multiselect__content {
  background: #fff;
}

.multiselect {
  overflow: visible;
}
</style>
