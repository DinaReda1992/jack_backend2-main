<template>
  <div>
    <div class="row">
      <div class="products col-md-10">
        <div class="form-group position-relative">
          <label>{{ $t('main.Search products') }}</label>
          <input class="form-control" @focus="show_search_products" :placeholder="$t('main.Search products')" />

          <div class="modal fade in show " v-if="products_popup" id="exampleModalScrollable" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalScrollableTitle">{{ $t('main.Add Products') }}</h5>
                  <button @click.prevent="show_search_products" type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <input class="form-control" :placeholder="$t('main.Search products')" @keyup="search_products_keyup"
                      v-model="search_product" />
                  </div>
                  <div class="_2d71edc9" id="infinite_list">
                    <div v-for="(product, index) in products" class="eb7d03bb">
                      <div class="_220ab7ca">
                        <input type="checkbox" v-model="items" :value="product.id" :id="'checkbox-' + product.id"
                          class="_13c2491b">
                        <label :for="'checkbox-' + product.id" class="d953d938"></label>
                      </div>
                      <img :src="product.photo">
                      <p class="_233f655c">{{ product.title }}</p>
                      <p class="_81592708"><span class="">{{ product.quantity }}</span>
                        &nbsp;{{ $t('main.In WareHouse') }}</p>
                      <p class="b0c57c6a">SAR {{ product.price }}</p>
                    </div>

                    <infinite-loading :identifier="infiniteId" @infinite="search_products"
                      force-use-infinite-wrapper="._2d71edc9">
                      <div slot="no-results" style="display: none">{{ $t('main.Not Found Result') }}</div>
                      <div slot="no-more" style="display: none">{{ $t('main.Not Found Result') }} </div>
                    </infinite-loading>
                  </div>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" @click.prevent="show_search_products"
                    data-dismiss="modal">{{ $t('main.Close') }}</button>
                  <button type="button" :disabled="loading_cart" @click.prevent="store_item" class="btn btn-primary">
                    {{ $t('main.Add Products') }} ({{ items.length }})
                  </button>
                </div>
              </div>
            </div>
          </div>


        </div>

        <div v-if="all_items.length > 0">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td>{{ $t('main.Product') }}</td>
                <td></td>
                <td>{{ $t('main.Price') }} X {{ $t('main.Quantity') }}</td>
                <td> {{ $t('main.Price') }}</td>
                <td></td>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in all_items">
                <td>
                  <img class="product_img" :src="item.product.photo" />
                </td>
                <td>{{ item.product.title }}</td>
                <td>
                  <div class="price-div" dir="ltr">
                    <input type="number" @change="update_item(item)" :disabled="loading_update"
                      class="input-qty form-control" v-model="item.quantity" :min="item.product.min_quantity">
                    <span class="mx-2">X</span>
                    <span class="">{{ item.product.price }} SAR</span>
                  </div>
                </td>
                <td>
                  {{ (item.product.price * item.quantity).toFixed(2)}} SAR
                </td>
                <td>
                  <a href="#" @click.prevent="delete_item(item.id)" class="d-block text-center fs-4">
                    <span class="fa fa-trash text-danger"></span>
                  </a>
                </td>
              </tr>
              <tr>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $t('main.Total') }}</td>
                <td>{{ total.toFixed(2) }} SAR</td>

              </tr>
            </tbody>
          </table>
        </div>

      </div>
      <div class="col-md-6 mt-15">
        <div v-if="user_id != null">
          <h3>{{ $t('main.Client Information') }}</h3>
          <div class="panel panel-default">
            <div class="panel-body">
              <div class="text-center">
                <img v-if="user.photo != null" :src="user.photo" width="70" height="70" class="rounded-circle" />
              </div>
              <h5 class="card-title d-flex align-items-center">
                <span>{{ $t('main.Name') }} :</span>
                <span>{{ user.username }}</span>
              </h5>
              <h6 class="card-subtitle mb-2 ">
                <span>{{ $t('main.Phone') }} :</span>
                <span>{{ user.phone }}</span>
              </h6>

            </div>
          </div>
        </div>

        <div class="form-group position-relative">
          <label>{{ $t('main.Select Client') }}</label>
          <input class="form-control" @keyup="search_users" @focus="show_search" v-model="search_user"
            :placeholder="$t('main.Select Client')" />
          <div id="users" v-if="users_popup && all_users.length > 0">
            <ul>
              <li @click="select_user(user)" v-for="(user, index) in all_users" :key="index"> {{ user.username }}</li>
            </ul>
          </div>

        </div>

      </div>
    </div>
    <div class="text-right">
      <button class="btn btn-success" :disabled="loading_next || next" @click.prevent="store_user">
        {{$t('main.Next')}}</button>
    </div>
  </div>

</template>



<script>
export default {
  props: {
    order: {
      type: Object,
      default: []
    },
    cart: {
      type: Array,
      default: []
    },
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
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
      all_items: this.cart,
      products: [],
      user_id: null,
      search_user: '',
      search_product: '',
      page: 1,
      infiniteId: Math.random(),
      items: [],
      user: this.order.user != null ? this.order.user : '',

    }
  },
  mounted() {
    if (this.order.user != null) {
      this.search_user = this.order.user.username
      this.user_id = this.order.user.id
    }
    /* this.$root.$on('updateCart', data => {
      this.all_items=data
     });*/

  },
  computed: {
    total: function () {
      let total = [];

      Object.entries(this.all_items).forEach(([key, val]) => {
        total.push(val.price * val.quantity) // the value of the current key.
      });

      return total.reduce(function (total, num) { return total + num }, 0);
    },
    next: function () {
      if (this.user_id == null || this.all_items.length == 0) {
        return true
      }
      return false
    }
  },

  methods: {

    select_user(user) {
      this.user = user
      this.user_id = user.id
      this.users_popup = false
      this.search_user = user.username
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
    search_users: async function () {
      try {
        //// this.$toast.removeAll()
        this.loading_users = true
        let params = {
          search: this.search_user
        }
        let res = await axios.get('/' + this.lang + '/admin-panel/search/users', { params });
        if (res.status == 200) {
          this.loading_users = false
          this.all_users = res.data.data
        }
        else if (res.status == 202) {
          this.loading_users = false

          this.$toast.error(
            res.data.message
          )
        } else {
          this.loading_users = false
          this.$toast.error(
            res.data.message
          )
        }

      } catch (res) {
        console.log(res)
        this.$toast.error(
          res.data.message
        )
      }

    },

    search_products: async function ($state) {
      try {
        // console.log($state)
        //// this.$toast.removeAll()
        this.loading_products = true
        let params = {
          search: this.search_product,
          page: this.page
        }
        let res = await axios.get('/' + this.lang + '/admin-panel/search/products', { params });
        if (res.status == 200) {
          this.loading_products = false
          if (res.data.data.data.length > 0) {
            this.page += 1;
            this.products.push(...res.data.data.data);
            $state.loaded();
            // $state.complete();

          } else {
            // alert('a')
            // this.page=1
            $state.complete();
          }


        }
        else if (res.status == 202) {
          this.loading_products = false

          // this.$toast.error(
          //   res.data.message
          // )
        } else {
          this.loading_products = false
          // this.$toast.error(
          //   res.data.message
          // )
        }

      } catch (res) {
        // console.log(res)
        this.$toast.error(
          res.data.message
        )
      }

    },

    store_item: async function () {
      try {
        //// this.$toast.removeAll()
        this.loading_cart = true
        var formData = new FormData();
        formData.set('ids', JSON.stringify(this.items));
        formData.set('order_id', this.order.id);
        let res = await axios.post('/' + this.lang + '/admin-panel/orders/store-items', formData);
        if (res.status == 200) {
          this.loading_cart = false
          this.all_items = res.data.items
          this.items = []
          this.products_popup = false
          this.$toast.success(
            res.data.message
          )
        }
        else if (res.status == 202) {
          this.loading_cart = false

          this.$toast.error(
            res.data.message
          )
        } else {
          this.loading_cart = false
          this.$toast.error(
            res.data.message
          )
        }

      } catch (res) {
        console.log(res)
        this.$toast.error(
          res.data.message
        )
      }

    },


    update_item: async function (item) {
      try {
        //// this.$toast.removeAll()
        this.loading_update = true
        var formData = new FormData();
        formData.set('id', item.id);
        formData.set('quantity', item.quantity);
        let res = await axios.post('/' + this.lang + '/admin-panel/orders/update-item', formData);
        if (res.status == 200) {
          this.loading_update = false
          this.all_items = res.data.items
          this.$toast.success(
            res.data.message
          )
        }
        else if (res.status == 202) {
          this.loading_update = false
          let id = item.id
          const index = this.all_items.findIndex(item => item.id == id);
          item.quantity = item.product.min_quantity
          this.all_items[index] = item
          this.$toast.error(
            res.data.message
          )
        }
        else {

          this.$toast.error(
            res.data.message
          )
        }

      } catch (res) {
        console.log(res)
        this.$toast.error(
          res.data.message
        )
      }

    },

    delete_item: async function (item) {
      try {
        var r = confirm("هل أنت متأكد من حذف المنتج؟")
        if (r == true) {
          //// this.$toast.removeAll()
          this.loading = true
          let params = {
            id: item,
          };
          let res = await axios.post('/' + this.lang + '/admin-panel/orders/delete-item', params);
          if (res.status == 200) {
            this.all_items = res.data.items

            this.$toast.success(
              res.data.message
            )
          } else {
            let errors = res.data.errors
            var i;
            for (i = 0; i < errors.length; i++) {
              this.$toast.error(
                errors[i]
              )
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

    store_user: async function () {
      try {
        //// this.$toast.removeAll()
        this.loading_next = true
        var formData = new FormData();
        formData.set('order_id', this.order.id);
        formData.set('user_id', this.user_id);
        let res = await axios.post('/' + this.lang + '/admin-panel/orders/select-user', formData);
        if (res.status == 200) {
          this.loading_next = false
          this.$toast.success(res.data.message)
          this.$root.$emit('updateUser', [res.data.data, res.data.user])
          this.$root.$emit('updateTap', 'address')
        }
        else if (res.loading_next == 202) {
          this.loading_next = false
          this.$toast.error(res.data.message);
        } else {
          this.loading_next = false
          this.$toast.error(res.data.message)
        }

      } catch (res) {
        console.log(res)
        this.$toast.error(res.data.message)
      }

    },



  },
};
</script>
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

.multiselect__content-wrapper {
  position: static !important;
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
  border-radius: 2px;
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
</style>
