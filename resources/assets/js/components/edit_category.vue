<template>
  <div>
    <div class="form-group position-relative">
      <label>{{ $t('main.Name Ar') }}</label>
      <input class="form-control" v-model="name_ar" :placeholder="$t('main.Name Ar')" />
    </div>

    <div class="form-group position-relative">
      <label>{{ $t('main.Name En') }}</label>
      <input class="form-control" v-model="name_en" :placeholder="$t('main.Name En')" />
    </div>

    <div class="form-group position-relative">
      <label class="control-label ">
        {{ $t('main.Offers') }}
      </label>
      <input type="checkbox" @change="changeIfOffer" :checked="is_offer" />
    </div>

    <div class="form-group position-relative" v-if="!is_offer">
      <label>{{ $t('main.Search In Categories') }}</label>
      <select class="form-control" v-model="category_id" @change="changeCategory"
        :placeholder="$t('main.Search In Categories')">
        <option value="0">{{ $t('main.Select Category') }}</option>
        <option v-for="(category, index) in all_categories" :value="category.id">{{ category.name }}</option>
      </select>
    </div>

    <div class="form-group position-relative" v-if="!is_offer && category_id != 0">
      <label> {{ $t('main.Search In SubCategories') }}</label>
      <select class="form-control" v-model="sub_category_id" :placeholder="$t('main.Search In SubCategories')">
        <option value="0">{{ $t('main.Select SubCategory') }}</option>
        <option v-for="(category, index) in all_sub_categories" :value="category.id">{{ category.name }}</option>
      </select>
    </div>

    <div v-if="!is_offer && category_id == 0 && sub_category_id == 0">
      <div class="form-group position-relative">
        <label>{{ $t('main.Search products') }}</label>
        <input class="form-control" @focus="show_search_products" :placeholder="$t('main.Search products')" />

        <div class="modal fade in show " v-if="products_popup" id="exampleModalScrollable" tabindex="-1" role="dialog"
          aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalScrollableTitle">
                  {{ $t('main.Add products') }}
                </h5>
                <button @click.prevent="show_search_products" type="button" class="close" data-dismiss="modal"
                  aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <div class="form-group">
                  <input class="form-control" placeholder="$t('main.Search products')" @keydown="search_products_keydown"
                    v-model="search_product" />
                </div>
                <div class="_2d71edc9" id="infinite_list">
                  <div v-for="(product, index) in products" class="eb7d03bb">
                    <div class="_220ab7ca">
                      <input type="checkbox" v-model="items" :value="product.id" :id="'checkbox-' + product.id"
                        class="_13c2491b" />
                      <label :for="'checkbox-' + product.id" class="d953d938"></label>
                    </div>
                    <img :src="product.photo" />
                    <p class="_233f655c">{{ product.title }}</p>
                    <p class="_81592708">
                      <span class="">{{ product.quantity }}</span> &nbsp;{{ $t('main.In Warehouse') }}
                    </p>
                    <p class="b0c57c6a">SAR {{ product.original_price }}</p>
                  </div>

                  <infinite-loading :identifier="infiniteId" @infinite="search_products"
                    force-use-infinite-wrapper="._2d71edc9">
                    <div slot="no-results" style="display: none">
                      {{ $t('main.No more data found') }}
                    </div>
                    <div slot="no-more" style="display: none">
                      {{ $t('main.No more data found') }}
                    </div>
                  </infinite-loading>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click.prevent="show_search_products"
                  data-dismiss="modal">
                  {{ $t('main.Close') }}
                </button>
                <button type="button" :disabled="loading_cart" @click.prevent="store_item" class="btn btn-primary">
                  {{$t('main.Add products')}} ({{ items.length }})
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
      <table class="table table-bordered">
        <thead>
          <tr style="background: #e8e8e8">
            <th style="width: 60px;"></th>
            <th style="width: 250px;">{{$t('main.Product')}}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(product, index) in all_products">
            <td class="text-center">
              <a href="javascript:void(0)" @click.prevent="remove_item(index)"><i
                  class="fa text-danger f-15 fa-times-circle"></i></a>
            </td>
            <td>
              <img :src="product.photo" width="40" height="40" />
              {{ product.title }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <br />
    <div class="text-center">
      <button class="btn btn-success" :disabled="!is_offer && all_products.length == 0 && category_id == 0"
        @click.prevent="confirm_order">
        {{ $t('main.save') }}
      </button>
    </div>
</div>
</template>

<script>
import CxltToastr from "cxlt-vue2-toastr";

var toastrConfigs = {
  position: "top right",
  timeOut: 5000,
};
Vue.use(CxltToastr, toastrConfigs);

export default {
  components: {},
  props: {
    category: {
      type: Object,
      default: {},
    },
    categories: {
      type: Array,
      default: [],
    },
    sub_categories: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      loading_cart: false,
      all_products: this.category.products,
      purchases_items: [],
      payment_terms: 3,
      details: "",
      photo: "",
      file_type: "",
      delivery_date: "",
      products_popup: false,
      search_product: "",
      page: 1,
      infiniteId: Math.random(),
      items: [],
      products: [],
      name_ar: this.category.name_ar ? this.category.name_ar : "",
      name_en: this.category.name_en ? this.category.name_en : "",
      is_offer: this.category.is_offer ? this.category.is_offer : false,
      category_id: this.category.category_id ? this.category.category_id : 0,
      sub_category_id: this.category.sub_category_id ? this.category.sub_category_id : 0,
      all_sub_categories: this.sub_categories,
      all_categories: this.categories,
    };
  },
  created() { },
  computed: {},

  methods: {
    changeIfOffer() {
      this.is_offer = !this.is_offer;
    },
    show_search_products() {
      this.products_popup = !this.products_popup;
    },
    changeCategory: async function () {
      let res = await axios.get(
        '/' + this.lang + "/admin-panel/page-categories/get-sub_categories/" + this.category_id
      );
      this.all_sub_categories = res.data;
      console.log(res);
    },
    search_products_keyup() {
      // alert('up')
      // this.page=1
      // this.products=[]
      // this.infiniteId= Math.random()
    },
    search_products_keydown() {
      // alert('down')
      this.page = 1;
      this.products = [];
      this.infiniteId = Math.random();
    },
    remove_item: function (index) {
      var r = confirm("هل أنت متأكد من حذف المنتج؟");
      if (r == true) {
        this.all_products.splice(this.all_products.indexOf(index), 1);
      }
    },
    confirm_order: async function () {
      try {
        // this.$toast.removeAll();
        if (this.all_products.length == 0 && !this.is_offer && !this.category_id) {
          this.$toast.error("لا يوجد منتجات!",);
          return;
        }

        let products = [];

        Object.entries(this.all_products).forEach(([key, val]) => {
          let product = {
            id: val.id,
          };
          // if (val.qty > 0) {
          products.push(product);
          // }
        });
        this.loading = true;
        var formData = new FormData();
        formData.append("products", JSON.stringify(products));
        formData.append("name_ar", this.name_ar);
        formData.append("name_en", this.name_en);
        formData.append("is_offer", this.is_offer ? 1 : 0);
        formData.append("category_id", this.category_id);
        formData.append("sub_category_id", this.sub_category_id);
        formData.append("_method", 'put');
        let res = await axios.post(
          '/' + this.lang + "/admin-panel/page-categories/" + this.category.id,
          formData
        );
        if (res.status == 200) {
          // this.loading=false
          this.$toast.success(res.data.message,);
          setTimeout((window.location = res.data.url), 5000);
        } else if (res.status == 202) {
          this.loading = false;
          this.$toast.error(res.data.message);
        } else {
          this.loading = false;
          this.$toast.error(res.data.message);
        }
      } catch (res) {
        console.log(res);
        //this.$toast.error(
        //     res.data.message
        //)
      }
    },

    search_products: async function ($state) {
      try {
        // this.$toast.removeAll();
        this.loading_products = true;
        let params = {
          search: this.search_product,
          page: this.page,
          provider_id: this.user_id,
        };
        let res = await axios.get(
          '/' + this.lang + "/admin-panel/warehouse-purchases/orders/products",
          { params }
        );
        if (res.status == 200) {
          this.loading_products = false;
          if (res.data.data.data.length > 0) {
            this.page += 1;
            this.products = res.data.data.data;
            $state.loaded();
            // $state.complete();
          } else {
            // alert('a')
            // this.page=1
            $state.complete();
          }
        } else if (res.status == 202) {
          this.loading_products = false;
        } else {
          this.loading_products = false;
        }
      } catch (res) {
        console.log(res);
        this.$toast.error(res.data.message);
      }
    },

    store_item: async function () {
      try {
        // this.$toast.removeAll();
        this.loading_cart = true;

        let items = this.items;
        let products = this.products;
        let all_products = this.all_products;
        items.forEach((value, index) => {
          let filteredItems = all_products.filter((item) => item.id === value);
          console.log(filteredItems);
          if (filteredItems.length == 0) {
            let result = products.filter((obj) => {
              return obj.id === value;
            });
            all_products.push(result[0]);
            result = "";
          }
        });
        this.loading_cart = false;
        this.items = [];
        this.products = [];
        this.products_popup = false;
      } catch (res) {
        console.log(res);
        this.$toast.error(res.data.message);
      }
    },
  },
};
</script>
<style>
.f-15 {
  font-size: 15px;
}

table {
  table-layout: fixed;
  word-wrap: break-word;
}

.toast-icon {
  top: initial !important;
}

body {
  overflow-x: hidden;
}

.active-tap {
  opacity: 1;
}

@media (max-width: 768px) {
  .nav-tabs>li {
    display: inline-block;
  }

  .nav-tabs:before {
    content: "";
    display: none;
  }
}

.border {
  border: 1px solid #d2d6de;
}

.placeholder {
  width: 100%;
}

.read-only {
  opacity: 0.5;
  pointer-events: none;
}

.d-flex {
  display: flex;
  align-items: center;
}

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
  content: "";
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
  content: "";
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
  content: "";
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
