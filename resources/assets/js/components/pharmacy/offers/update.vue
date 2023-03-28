<template>
  <div>
    <div class="row d-flex flex-md-column">
      <div class="col-md-8">
        <div class="panel panel-white p-20">
          <p class=" f-18 mb--20">
            {{ $t("main.Offer Type") }}
          </p>

          <div class="type-group mt--10 " v-for="(type, index) in offer_types">
            <label class="custom-radio f-16" v-if="offer_type == type.id">
              <input
                type="radio"
                :checked="offer_type == type.id"
                :value="type.id"
              />
              <i></i>
              {{ this.lang == "ar" ? type.name_ar : type.name_en }}
            </label>
          </div>

          <div class=" mt-3" v-if="offer_type == 1">
            <div class="form-group">
              <label>{{ $t("main.Discount Value") }}</label>
              <input
                type="number"
                @change="setValueToZero('price_discount')"
                class="form-control"
                v-model="price_discount"
              />
            </div>
          </div>
          <div class="mt-3" v-if="offer_type == 2">
            <div class="form-group">
              <label>{{ $t("main.Discount Percentage") }}</label>
              <input
                type="number"
                @change="setValueToZero('percentage')"
                max="100"
                maxlength="3"
                class="form-control"
                v-model="percentage"
              />
            </div>
          </div>
        </div>

        <div class="panel panel-white p-20">
          <p class="f-18">
            {{ $t("main.Offer Date") }}
          </p>
          <div class="row mt--20">
            <div class="form-group col-md-6" id="date_start-group">
              <label class="control-label">
                {{ $t("main.Start Offer") }} <span class="text-danger">*</span>
              </label>
              <input type="date" class="form-control" v-model="start_date" />
            </div>

            <div class="form-group col-md-6" id="date_end-group">
              <label class="control-label">
                {{ $t("main.End Offer") }} <span class="text-danger">*</span>
              </label>
              <input type="date" class="form-control " v-model="end_date" />
            </div>
          </div>
        </div>

        <div class="panel panel-white p-20">
          <p class=" f-18 mb--20">
            {{ $t("main.Usage limits") }}
          </p>
          <div class="row">
            <div class="col-md-6">
              <div class="type-group  ">
                <label class="custom-radio f-15">
                  <input type="checkbox" value="0" v-model="usage_limit" />
                  <i></i>
                  {{ $t("main.Usage limit") }}
                </label>
              </div>
              <div v-if="!filter('0')">
                <div class="row">
                  <div class="col-md-8 mx-auto">
                    <div class="d-flex">
                      <button
                        type="button"
                        @click="change_number_of_users('decrement')"
                        class="btn btn-outline-secondary btn-number"
                        :disabled="number_of_users <= 1"
                      >
                        <span class="fa fa-minus"></span>
                      </button>
                      <input
                        @change="change_number_of_users('change')"
                        type="number"
                        v-model="number_of_users"
                        class="form-control input-number"
                        value="1"
                        min="1"
                        max="10"
                      />
                      <button
                        type="button"
                        @click="change_number_of_users('increment')"
                        class="btn btn-outline-secondary btn-number"
                      >
                        <span class="fa fa-plus"></span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="type-group  ">
                <label class="custom-radio f-15">
                  <input type="checkbox" value="1" v-model="usage_limit" />
                  <i></i>
                  {{ $t("main.Usage limit") }} {{ $t("main.To Client") }}
                </label>
              </div>
              <div v-if="!filter('1')">
                <div class="row">
                  <div class="col-md-8 mx-auto">
                    <div class="d-flex">
                      <button
                        type="button"
                        @click="change_one_user_use('decrement')"
                        class="btn btn-outline-secondary btn-number"
                        :disabled="one_user_use <= 1"
                      >
                        <span class="fa fa-minus"></span>
                      </button>
                      <input
                        @change="change_one_user_use('change')"
                        type="number"
                        v-model="one_user_use"
                        class="form-control input-number"
                        value="1"
                        min="1"
                        max="10"
                      />
                      <button
                        type="button"
                        @click="change_one_user_use('increment')"
                        class="btn btn-outline-secondary btn-number"
                      >
                        <span class="fa fa-plus"></span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="panel panel-white p-20">
          <div
            class="mt-3"
            v-if="offer_type == 3 || offer_type == 4 || offer_type == 5"
          >
            <div class="form-group">
              <label>{{
                $t("main.Quantity required to activate the offer")
              }}</label>
              <input
                type="number"
                @change="setValueToZero('quantity')"
                class="form-control"
                v-model="quantity"
              />
            </div>
          </div>

          <div>
            <label class="typo__label">{{ $t("main.Select Products") }}</label>
            <multiselect
              v-model="products"
              :options="all_products"
              :multiple="true"
              :loading="loading_products"
              :close-on-select="false"
              :clear-on-select="false"
              :preserve-search="false"
              :placeholder="$t('main.Select Products')"
              label="title"
              track-by="title"
              :preselect-first="false"
              selectLabel=""
              selectedLabel="مضاف"
              deselectLabel="اضغط لالغاء الاضافة"
              @open="search_products"
            >
              <template slot="option" slot-scope="props">
                <div class="option__desc">
                  <span class="option__title">{{ props.option.title }}</span>
                </div>
              </template>
            </multiselect>
            <div v-if="products_messages.length > 0" class="my-2">
              <ul>
                <li class="mb-2">
                  {{ $t("main.Products are on offer at the same time") }} :
                </li>
                <li
                  class="text-danger"
                  v-for="products_message in products_messages"
                >
                  {{ products_message }}
                </li>
              </ul>
            </div>
          </div>

          <div
            class="mt-5"
            v-if="offer_type == 3 || offer_type == 4 || offer_type == 5"
          >
            <div class="form-group">
              <label>{{
                $t("main.The quantity that the customer takes")
              }}</label>
              <input
                type="number"
                @change="setValueToZero('get_quantity')"
                class="form-control"
                v-model="get_quantity"
              />
            </div>
          </div>

          <div class="mt-3" v-if="offer_type == 4">
            <label class="typo__label">{{
              $t("main.Choose the products that the customer can take")
            }}</label>
            <multiselect
              v-model="get_products"
              :options="all_products"
              :multiple="true"
              :loading="loading_products"
              :close-on-select="false"
              :clear-on-select="false"
              :preserve-search="false"
              :placeholder="$t('main.Select Products')"
              label="title"
              track-by="title"
              :preselect-first="false"
              selectLabel=""
              selectedLabel="مضاف"
              deselectLabel="اضغط لالغاء الاضافة"
              @open="search_products"
            >
              <template slot="option" slot-scope="props">
                <div class="option__desc">
                  <span class="option__title">{{ props.option.title }}</span>
                </div>
              </template>
            </multiselect>
            <div v-if="get_products_messages.length > 0" class="my-2">
              <ul>
                <li class="mb-2">
                  {{ $t("main.Products are on offer at the same time") }} :
                </li>
                <li
                  class="text-danger"
                  v-for="products_message in get_products_messages"
                >
                  {{ products_message }}
                </li>
              </ul>
            </div>
          </div>

          <div
            class="mt-5 "
            v-if="offer_type == 3 || offer_type == 4 || offer_type == 5"
          >
            <label class="f18">{{ $t("main.Discount Type") }}</label>
            <div class="d-flex">
              <div class="type-group mt--10 mx-2 ">
                <label class="custom-radio f-14">
                  <input type="radio" v-model="offer_discount_type" value="1" />
                  <i></i>
                  {{ $t("main.Percentage") }}
                </label>
              </div>
              <div class="type-group mt--10 mx-2 ">
                <label class="custom-radio f-14">
                  <input type="radio" v-model="offer_discount_type" value="2" />
                  <i></i>
                  {{ $t("main.Free") }}
                </label>
              </div>
            </div>
          </div>
          <div
            class="mt-3"
            v-if="
              (offer_type == 3 || offer_type == 4 || this.offer_type == 5) &&
                offer_discount_type == 1
            "
          >
            <div class="form-group">
              <label>{{ $t("main.Discount Percentage") }}</label>
              <input
                type="number"
                @change="setValueToZero('percentage')"
                max="100"
                maxlength="3"
                class="form-control"
                v-model="percentage"
              />
            </div>
          </div>
        </div>

                <div class="panel panel-white p-20">
          <div class="form-group position-relative">
            <label class="control-label ">
              {{ $t('main.Select Clients') }}
            </label>
            <input type="checkbox" @change="changeIfClient" :checked="for_all_clients" />
          </div>
          <div v-if="for_all_clients">
            <label class="typo__label">{{ $t("main.Select Clients") }}</label>
            <multiselect
              v-model="clients"
              :options="all_clients"
              :multiple="true"
              :loading="loading_clients"
              :close-on-select="false"
              :clear-on-select="false"
              :preserve-search="false"
              :placeholder="$t('main.Select Clients')"
              label="username"
              track-by="id"
              :preselect-first="false"
              selectLabel=""
              selectedLabel="مضاف"
              deselectLabel="اضغط لالغاء الاضافة"
              @open="search_clients"
              noOptions="لا يوجد عملاء في القائمة"
            >
              <template slot="option" slot-scope="props">
                <div class="option__desc">
                  <span class="option__title">{{ props.option.username }}</span>
                </div>
              </template>
            </multiselect>
          </div>
        </div>

        <div class="text-right">
          <button
            class="btn btn-success"
            :disabled="loading_next"
            @click.prevent="create"
          >
            {{ $t("main.Save Changes") }}
          </button>
        </div>
      </div>
      <div class="col-md-4 order-md-1 summery-panel">
        <div class="panel stick panel-white p-20">
          <ul class="summary">
            <template
              v-if="offer_type == 3 || offer_type == 4 || this.offer_type == 5"
            >
              <li>
                {{ $t("main.Quantity required to activate the offer") }}
                {{ quantity }}
              </li>
              <li>
                {{ $t("main.The quantity that the customer takes") }}
                {{ get_quantity }}
              </li>
              <li v-if="percentage > 0 && offer_discount_type == 1">
                {{ $t("main.Discount") }} {{ percentage }} %
              </li>
              <li v-if="offer_discount_type == 2">
                {{ $t("main.Offer Free") }}
              </li>
            </template>

            <li v-if="price_discount > 0 && offer_type == 1">
              {{ $t("main.Discount") }} {{ price_discount }}
              {{ $t("main.SAR") }}
            </li>
            <li v-if="percentage > 0 && offer_type == 2">
              {{ $t("main.Discount") }} {{ percentage }} %
            </li>

            <li v-if="!this.filter('0')">
              {{ $t("main.Usage limit") }} {{ number_of_users }}
            </li>
            <li v-if="!this.filter('1')">
              {{ $t("main.Usage limit") }} {{ $t("main.To Client") }}
              {{ one_user_use }}
            </li>

            <li>{{ $t("main.Start Offer") }} {{ start_date }}</li>
            <li>{{ $t("main.End Offer") }} {{ end_date }}</li>
          </ul>
        </div>

        <div>
          <div class="panel panel-white p-20 mb-0">
            <div class="form-group">
              <div class="d-flex align-items-center">
                <label class="control-label ">
                  {{ $t("main.Offer Status") }}
                </label>
                <div
                  class="checkbox checkbox-switchery switchery-sm mx-5 switchery-double"
                >
                  <input
                    type="checkbox"
                    v-model="status"
                    :value="true"
                    class="switchery sweet_switch"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import CxltToastr from "cxlt-vue2-toastr";
Vue.component("multiselect", Multiselect);
import Multiselect from "vue-multiselect";
var toastrConfigs = {
  position: "top right",
  timeOut: 5000,
};
Vue.use(CxltToastr, toastrConfigs);

export default {
  components: {
    Multiselect,
  },

  props: {
    offer_types: {
      type: Array,
      default: "",
    },
    data: {
      type: Object,
      default: "",
    },
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      done: false,

      loading: false,
      loading_products: false,
      loading_clients: false,
      loading_cart: false,
      loading_next: false,

      /**/
      offer_type: this.data.type_id,
      offer_discount_type: this.data.is_free == 1 ? 2 : 1,
      price_discount: this.data.price_discount,
      status: this.data.status == 1 ? true : false,
      percentage: this.data.percentage,
      quantity: this.data.quantity,
      get_quantity: this.data.get_quantity,
      usage_limit: [],
      number_of_users: this.data.number_of_users,
      one_user_use: this.data.one_user_use,
      start_date: this.data.start_date,
      end_date: this.data.end_date,
      // start_date : new Date().toISOString().slice(0,10),
      // end_date : new Date().toISOString().slice(0,10),
      /**/
      products_messages: [],
      get_products_messages: [],
      all_products: [],
      all_clients: [],
      clients: this.data.users,
      products: this.data.items,
      for_all_clients: false,
      get_products: this.data.get_items,
      url_panel: window.location.pathname.split("/")[1] === 'en' ?
              window.location.pathname.split("/")[2] : window.location.pathname.split("/")[1]
    };
  },
  mounted() {
    if (this.data.number_of_users != null) {
      this.usage_limit.push("0");
    }
    if (this.data.one_user_use != null) {
      this.usage_limit.push("1");
    }
    /* this.$root.$on('updateCart', data => {
      this.all_items=data
     });*/
     if(this.clients.length!=0)
     {
        this.for_all_clients= true;
     }
  },
  computed: {},

  methods: {
      changeIfClient() {
      this.for_all_clients = !this.for_all_clients;
    },
    change_number_of_users(type) {
      // if(this.number_of_users>1000){
      //   this.number_of_users=1000
      //   return;
      // }
      if (type == "increment") {
        this.number_of_users = this.number_of_users + 1;
      } else if (type == "decrement") {
        if (this.number_of_users <= 1) {
          this.number_of_users = 1;
          return;
        }
        this.number_of_users = this.number_of_users - 1;
      } else {
        if (this.number_of_users <= 1) {
          this.number_of_users = 1;
          return;
        }
      }
    },

    change_one_user_use(type) {
      if (type == "increment") {
        this.one_user_use = this.one_user_use + 1;
      } else if (type == "decrement") {
        if (this.one_user_use <= 1) {
          this.one_user_use = 1;
          return;
        }
        this.one_user_use = this.one_user_use - 1;
      } else {
        if (this.number_of_users <= 1) {
          this.number_of_users = 1;
          return;
        }
      }
    },
    filter(value) {
      // We can't find 'Taiwan' in nationalityArr
      return this.usage_limit.filter((n) => n === value).length === 0
        ? true
        : false; // false
    },
    setValueToZero(key) {
      if (key == "percentage") {
        if (this.percentage > 100) {
          this.percentage = 100;
        } else if (this.percentage <= 1) {
          this.percentage = 1;
        }
      }
      if (key == "price_discount") {
        if (this.price_discount <= 1) {
          this.price_discount = 1;
        }
      }
      if (key == "quantity") {
        if (this.quantity <= 1) {
          this.quantity = 1;
        }
      }
      if (key == "get_quantity") {
        if (this.get_quantity <= 1) {
          this.get_quantity = 1;
        }
      }
    },

    search_products: async function() {
      try {
        if (this.all_products.length > 0) return;
        // this.$toast.removeAll();
        this.loading_products = true;
        let params = {
          start_date: this.start_date,
          end_date: this.end_date,
          offer_id: this.data.id,
        };

        let res = await axios.get(
          "/" + this.lang+'/admin-panel' + "/do/offers/search-products",
          { params }
        );
        if (res.status == 200) {
          this.loading_products = false;
          this.all_products = res.data ? res.data.data : [];
        } else if (res.status == 202) {
          this.loading_products = false;

          // this.$toast.error(
          //   res.data.message
          // )
        } else {
          this.loading_products = false;
          // this.$toast.error(
          //   res.data.message
          // )
        }
      } catch (res) {
        console.log(res);
this.$toast.error(res.data.message);                }
    },

     search_clients: async function($state) {
      try {
        // this.$toast.removeAll();
        this.loading_clients = true;

        let res = await axios.get(
          "/" + this.lang+'/admin-panel' + "/do/offers/search-users");
        if (res.status == 200) {
          this.loading_clients = false;
          this.all_clients = res.data.data;
        } else if (res.status == 202) {
          this.loading_clients = false;
        } else {
          this.loading_clients = false;
        }
      } catch (res) {
        console.log(res);
this.$toast.error(res.data.message);                }
    },

    create: async function() {
      try {
        // this.$toast.removeAll();
        this.products_messages = [];
        this.get_products_messages = [];

        this.loading_next = true;
        var formData = new FormData();
        formData.set("offer_type", this.offer_type);
        formData.set("products", JSON.stringify(this.products));
        formData.set("clients",JSON.stringify(this.clients));
        formData.set("start_date", this.start_date);
        formData.set("end_date", this.end_date);
        formData.set("_method", "patch");
        if (this.status) {
          formData.set("status", this.status);
        }
        if (!this.filter("0")) {
          formData.set("number_of_users", this.number_of_users);
        }
        if (!this.filter("1")) {
          formData.set("one_user_use", this.one_user_use);
        }
        if (this.offer_type == 1) {
          formData.set("price_discount", this.price_discount);
        }
        if (this.offer_type == 2) {
          formData.set("percentage", this.percentage);
        }
        if (this.offer_type == 3) {
          formData.set("quantity", this.quantity);
          formData.set("get_quantity", this.get_quantity);
          formData.set("offer_discount_type", this.offer_discount_type);
          if (this.offer_discount_type == 1) {
            formData.set("percentage", this.percentage);
          }
        }
        if (this.offer_type == 4) {
          formData.set("get_products", JSON.stringify(this.get_products));
          formData.set("quantity", this.quantity);
          formData.set("get_quantity", this.get_quantity);
          formData.set("offer_discount_type", this.offer_discount_type);
          if (this.offer_discount_type == 1) {
            formData.set("percentage", this.percentage);
          }
        }
        if (this.offer_type == 5) {
          formData.set("quantity", this.quantity);
        }

        let res = await axios.post(
          "/" + this.lang+'/admin-panel' + "/offers/" + this.data.id,
          formData
        );
        if (res.status == 200) {
          this.loading_next = false;

          this.$toast.success(res.data.message);
          /*    this.products=[]
          this.get_products=[]
          this.number_of_users=1
          this.one_user_use=1
          this.offer_type=1
          this.price_discount=''
          this.percentage=''*/
        } else if (res.status == 202) {
          this.loading_next = false;
          if (res.data.data) {
            this.products_messages = res.data.data;
            this.get_products_messages = res.data.data_get_products;
          }
         this.$toast.error(res.data.message);          
        } else {
          this.loading_next = false;
         this.$toast.error(res.data.message);          
        }
      } catch (res) {
        console.log(res);
this.$toast.error(res.data.message);                }
    },
  },
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>
.toast-icon {
  top: 10px;
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
.d-flex {
  display: flex;
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

ul {
  margin: 0;
  padding: 0;
  list-style: none;
}
[dir="rtl"] .multiselect__select {
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
ul.summary {
  list-style: disc;
  list-style-position: inside;
}
.summary li {
  margin: 10px 0;
}
@media (max-width: 769px) {
  .summery-panel {
    order: -1;
  }
  .flex-md-column {
    flex-direction: column;
  }
}
@media (min-width: 769px) {
  .summery-panel div.stick {
    position: sticky;
    top: 20px;
    z-index: 998;
  }
}
[dir="rtl"] .multiselect__spinner {
  right: auto;
  left: 31px;
}
</style>
