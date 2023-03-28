/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
window.Popper = require("popper.js").default;
window.$ = window.jQuery = require("jquery");

require("./bootstrap");

window.Vue = require("vue");
import "cxlt-vue2-toastr/dist/css/cxlt-vue2-toastr.css";

let gelang = document.documentElement.lang;

window.lang = gelang ? gelang : "ar";
window.Vue = require("vue");
import i18n from "./components/i18n/i18n";

Vue.config.productionTip = false;
Vue.config.devtools = true;
window.axios.defaults.headers.common["lang"] = lang ? lang : "ar";
window.axios.defaults.headers.common["X-CSRF-TOKEN"] = document.querySelector(
  'meta[name="csrf-token"]'
).content;
window.axios.defaults.headers.post["Content-Type"] = "multipart/form-data";
const files = require.context("./", true, /\.vue$/i);
files.keys().map((key) =>
  Vue.component(
    key
      .split("/")
      .pop()
      .split(".")[0],
    files(key).default
  )
);
/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Vuex from 'vuex'

Vue.use(Vuex)
import storeData from "././store"

import VueConfirmDialog from 'vue-confirm-dialog'

const store = new Vuex.Store(
    storeData
)

import VueToast from "vue-toast-notification";
import "vue-toast-notification/dist/theme-sugar.css";

Vue.use(VueToast);

Vue.component("sign-in", require("./components/sign.vue"));
Vue.component("activation", require("./components/activation.vue"));
Vue.component("register", require("./components/register.vue"));
Vue.component("side-cart", require("./components/sidecart.vue"));
Vue.component("cart-page", require("./components/cartPage.vue"));
Vue.component("search-filter", require("./components/filter.vue"));
Vue.component("layout-labels", require("./components/Labels.vue"));
Vue.component("view-product", require("./components/viewproduct.vue"));
Vue.component('payment_methods', require('./components/payment_methods.vue'));
Vue.component("quick-view", require("./components/quickView.vue"));
Vue.component("checkout", require("./components/checkout.vue"));
Vue.component(
  "add-to-wishlist",
  require("./components/addProductToWishlist.vue")
);
Vue.component("cart-box", require("./components/addToCartBox.vue"));
Vue.component(
  "add-to-cart-action",
  require("./components/addToCartAction.vue")
);
Vue.component("view-product", require("./components/viewproduct.vue"));
Vue.component(
  "example-component",
  require("./components/ExampleComponent.vue")
);
Vue.component("login", require("./components/login.vue"));
Vue.component("addresses", require("./components/addresses.vue"));
Vue.component("addresses2", require("./components/addresses2.vue"));
Vue.component("cart", require("./components/cart.vue"));
// Vue.component("actions", require("./components/actions.vue"));
Vue.component("confirmationbox", require("./components/confirmationbox.vue"));
Vue.component("completeorder", require("./components/completeorder.vue"));
Vue.component("paymentbank", require("./components/payment_bank.vue"));
Vue.component("search", require("./components/search.vue"));
// Vue.component("providers", require("./components/providers.vue"));
Vue.component("products", require("./components/products.vue"));
Vue.component("becomeprovider", require("./components/becomeprovider.vue"));
Vue.component("ordertaps", require("./components/createcart/taps.vue"));
Vue.component("step1", require("./components/createcart/step1.vue"));
Vue.component("step2", require("./components/createcart/step2.vue"));
Vue.component("step3", require("./components/createcart/step3.vue"));

// Vue.component("create_purchase", require("./components/purchase/create.vue"));
Vue.component("createcategory", require("./components/create_category.vue"));
Vue.component("updatecategory", require("./components/edit_category.vue"));

Vue.component(
  "create_offer",
  require("./components/pharmacy/offers/create.vue")
);
Vue.component(
  "update_offer",
  require("./components/pharmacy/offers/update.vue")
);

// Vue.component(
//   "provider_ordertaps",
//   require("./components/provider_createcart/taps.vue")
// );
// Vue.component(
//   "provider_step1",
//   require("./components/provider_createcart/step1.vue")
// );
// Vue.component(
//   "provider_step2",
//   require("./components/provider_createcart/step2.vue")
// );
// Vue.component(
//   "provider_step3",
//   require("./components/provider_createcart/step3.vue")
// );
// Vue.component(
//   "create_user",
//   require("./components/provider_createcart/create_user.vue")
// );
// Vue.component("payment", require("./components/payment.vue"));

Vue.use(VueConfirmDialog)
Vue.component('vue-confirm-dialog', VueConfirmDialog.default)

Vue.component("pagination", require("laravel-vue-pagination"));
Vue.component("InfiniteLoading", require("vue-infinite-loading"));
Vue.component("VueSlickCarousel", require("vue-slick-carousel"));
const app = new Vue({
  el: "#app",
  i18n,
  store, //vuex
});
