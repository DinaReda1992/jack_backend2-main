<template>
  <div>
    <section>
      <div class="container container-xl">
        <h2 class="text-center mt-9 mb-8">{{ $t('main.Search List') }}</h2>
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <div class="w-50 d-flex">
            <div class="d-flex align-items-center h-100">
              <div class="srchinput input-group position-relative mw-270 mr-auto" style="width: 293px;">
                <input type="text" class=" form-control form-control bg-transparent" style="border-radius: 56px;"
                  :placeholder="$t('main.search')" v-model="keyword">
                <div class="input-group-append position-absolute pos-fixed-right-center">
                  <button class="input-group-text bg-transparent border-0 px-0 fs-28 pr-3" type="submit" aria-label="Search" >
                    <i class="fal fa-search fs-20 font-weight-normal"></i>
                  </button>
                </div>
              </div>
            </div>
            &nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;
            <div class="d-flex align-items-center">
              <div class="switch-layout d-lg-flex align-items-center d-none">
                <h5>
                  {{ $t('main.Offers') }} :
                </h5>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <input type="checkbox" hidden="hidden" aria-label="filter" id="filter" :checked="is_offer" @change="ChangeIsOffer"><label
                  class="switch" for="filter"></label>
              </div>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <div class="d-flex align-items-center">
              <div class="switch-layout d-lg-flex align-items-center d-none">
                <a href="#" class="pr-5" title="Grid View" @click.prevent="changeView(1)">
                  <svg class="icon icon-squares-four fs-32 hover-secondary" :class="view == 1 ? 'active' : ''">
                    <use xlink:href="#icon-squares-four"></use>
                  </svg>
                </a>
                <a href="#" title="List View" @click.prevent="changeView(0)">
                  <svg class="icon icon-list fs-32 hover-secondary" :class="view == 0 ? 'active' : ''">
                    <use xlink:href="#icon-list"></use>
                  </svg>
                </a>
              </div>

            </div>
            <div class="dropdown show lh-1 rounded ml-lg-5 ml-0" style="background-color:#f5f5f5">
              <select v-model="sort_pram" @change="changeSort"
                class="dropdown-toggle custom-dropdown-toggle text-decoration-none text-secondary mw-210 position-relative d-block">
                <option class="dropdown-item text-center" value="sort">{{ $t('main.Sort') }}</option>
                <option class="dropdown-item text-center" value="price_high_to_low">{{ $t('main.Price High to Low') }}
                </option>
                <option class="dropdown-item text-center" value="price_low_to_high">{{ $t('main.Price Low to High') }}
                </option>
                <option class="dropdown-item text-center" value="random">{{ $t('main.Random') }}</option>
              </select>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="mt-7 pb-11 pb-lg-13">
      <div class="container container-xl">
        <div class="row">
          <div class="col-lg-3 primary-sidebar sidebar-sticky pr-lg-8 d-lg-block d-none" id="sidebar">
            <div class="primary-sidebar-inner">
              <div class="card border-0">
                <div class="card-header bg-transparent border-0 p-0">
                  <h2 class="card-title fs-25 mb-3">
                    {{ $t('main.Filter Category') }}</h2>
                </div>
                <div class="card-body pb-6">
                  <div class="trams-conditions" v-for="(category, index) in categories" :key="index" :id="category.sub_categories.length==0?'single-category':''">
                    <div class="custom-control custom-checkbox">
                      <input type='checkbox' class="custom-control-input" :id="'check-all' + category.id"
                        @change="checkAllSubs(category)" v-model="category_id" :value="category.id">
                      <label class="custom-control-label fs-16 fw-bold text-body" :for="'check-all' + category.id">{{
                        category.name
                      }}
                      </label>
                    </div>
                    <a href="javascript:void(0)" id="toggle" class="plus-icon" v-if="category.sub_categories.length > 0"
                      @click="showSubCategories(category.id)"></a>
                    <div class="trams-con" :class="'category-' + category.id" v-if="category.sub_categories.length > 0">
                      <div class="custom-control custom-checkbox" v-for="(subcategory, index) in category.sub_categories">
                        <input name="stay-signed-in" type="checkbox" class="custom-control-input individual"
                          :id="'subcategory-' + subcategory.id" v-model="subcategory_id" :value="subcategory.id">
                        <label class="custom-control-label text-body" :for="'subcategory-' + subcategory.id"> {{
                          subcategory.name
                        }} </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <a href="javascript:void(0)" class="srchbtnn" @click="search">{{ $t('main.search') }}</a>
            </div>
            <div class="myswitch mobile switch-layout d-lg-flex align-items-center d-none">
              <h5>
                {{ $t('main.Offers') }} :
              </h5>
              &nbsp;&nbsp;&nbsp;&nbsp;
              <input class="switchmobile" aria-label="filter" type="checkbox" hidden="hidden" id="filter2" :checked="is_offer"
                @change="ChangeIsOffer">
              <label class="switch" for="filter2"></label>
            </div>
            <div class="dropdown show lh-1 rounded  ml-0 mobile" style="background-color:#f5f5f5; margin-right: 35px;">
              <select v-model="sort_pram" @change="changeSort"
                class="dropdown-toggle custom-dropdown-toggle text-decoration-none text-secondary mw-210 position-relative d-block">
                <option class="dropdown-item text-center" value="sort">{{ $t('main.Sort') }}</option>
                <option class="dropdown-item text-center" value="price_high_to_low">{{ $t('main.Price High to Low') }}
                </option>
                <option class="dropdown-item text-center" value="price_low_to_high">{{ $t('main.Price Low to High') }}
                </option>
                <option class="dropdown-item text-center" value="random">{{ $t('main.Random') }}</option>
              </select>
            </div>
          </div>
          <div class="col-lg-9">
            <div id="spinner-containerr" v-if="loading">
              <div id="loading-spinnerr"></div>
            </div>
            <div class="row" v-if="view == 1">
              <div class="col-xl-3 col-lg-4 col-md-6" v-for="(item, index) in items.data">
                <div class="card border-0 product mb-6 fadeInUp animated" data-animate="fadeInUp">
                  <div class="position-relative">
                    <img :src="item.photo" :alt="item.title">
                    <div class="card-img-overlay d-flex p-3">
                      <div v-if="item.offer_type != ''">
                        <span class="badge badge-primary" style="position: absolute;direction: ltr;">{{
                          item.offer_type
                        }}</span>
                      </div>
                      <div class="my-auto w-100 content-change-vertical">
                        <a :href="'/product/' + item.id" data-toggle="tooltip" data-placement="left" title=""
                          class="add-to-cart ml-auto d-flex align-items-center justify-content-center text-secondary bg-white hover-white bg-hover-secondary w-48px h-48px rounded-circle mb-2"
                          data-original-title="اظهر المنتجات">
                          <svg class="icon icon-shopping-bag-open-light fs-24">
                            <use xlink:href="#icon-shopping-bag-open-light"></use>
                          </svg>
                        </a>
                        <add-to-wishlist :item="item" :single="0" :user="user"></add-to-wishlist>
                        <quick-view :item="item" :single="0"> </quick-view>
                      </div>
                    </div>
                  </div>
                  <div class="card-body pt-4 text-center px-0">
                    <h2 class="card-title fs-15 font-weight-500 mb-2"><a :href="'/product/' + item.id">{{ item.title
                    }}</a>
                    </h2>
                    <div style="margin-top:1.5em; direction:ltr;"
                      class="d-flex align-items-center justify-content-center flex-wrap">
                      <p class="card-text font-weight-bold fs-16 mb-1 text-secondary" dir="rtl">
                        <span class="fs-15 font-weight-500 text-decoration-through text-body pr-1"
                          v-if="item.offer_price > 0">{{ item.price }}</span>
                        <span class="fs-18 font-weight-bold" v-if="item.offer_price > 0">{{ item.offer_price }}
                          {{ $t('main.SAR') }}</span>
                        <span class="fs-18 font-weight-bold" v-if="item.offer_price <= 0">{{ item.price }}
                          {{ $t('main.SAR') }}</span>
                      </p>
                      <add-to-cart-action :item="item" :single="0" :user="user" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else class="media flex-column flex-md-row mb-7 fadeInUp animated" data-animate="fadeInUp"
              v-for="(item, index) in items.data">
              <a href="#" class="position-relative mw-md-258">
                <img :src="item.photo" :alt="item.title" />
                <div class="card-img-overlay p-3" v-if="item.offer_type != ''">
                  <span class="badge badge-primary">{{ item.offer_type }}</span>
                </div>
              </a>
              <div class="media-body ml-md-6 mt-4">

                <h2 class="card-title fs-24 mb-2"><a href="#">{{ item.title }} </a></h2>
                <p class="mb-7 mr-xl-8">
                  {{ item.description }}
                </p>
                <div class="d-flex align-items-center justify-content-between flex-row">
                  <div class="mr-sm-4">
                    <add-to-cart-action :item="item" :single="1" :user="user" />
                  </div>
                  <div class="d-flex">
                    <add-to-wishlist :item="item" :single="0" :user="user"></add-to-wishlist>
                    <quick-view :item="item" :single="0"> </quick-view>
                  </div>
                  <div>
                    <p class="card-text font-weight-bold fs-16 mb-1 text-secondary">
                      <span class="fs-15 font-weight-500 text-decoration-through text-body pr-1"
                        v-if="item.offer_price > 0">{{ item.price }} {{ $t('main.SAR') }}</span>
                      <span class="fs-18 font-weight-bold" v-if="item.offer_price > 0">{{ item.offer_price }}
                        {{ $t('main.SAR') }}</span>
                      <span class="fs-18 font-weight-bold" v-if="item.offer_price <= 0">{{ item.price }}
                        {{ $t('main.SAR') }}</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="items.total == 0">
              <img src="/images/no_data.png" class='offers-img'
                style="height: 300px;width: 100%;object-fit: scale-down;" />
            </div>
            <nav class="pt-3">
              <pagination :limit="1" :show-disabled="true" :data="items" @pagination-change-page="search_method">
              </pagination>
            </nav>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  props: {
    sort: {
      type: String,
      default: "",
    },
    settings: {
      type: String,
      default: "",
    },
    categories: {
      type: Array,
      default: "",
    },
    user: {
      type: Object,
      default: () => ({
        id: 0,
        activate: 0,
      }),
    },
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      message: "",
      all_products: [],
      // subcategory_id: 0,
      items: { data: [] },
      sort_pram: this.sort ? this.sort : 'sort',
      is_offer: false,
      keyword: '',
      category_id: [],
      product_id: '',
      subcategory_id: [],
      view: 1,
    };
  },
  mounted() {
    this.initSearch();
  },
  methods: {
    showSubCategories(item) {
      let element = $('.category-' + item);
      console.log(element);
      if (element.hasClass('blockDisplay')) {
        element.removeClass('blockDisplay');
      } else {
        element.addClass('blockDisplay')
      }
    },

    checkAllSubs(category) {
      let ids = this.subcategory_id;
      if (!this.category_id.includes(category.id)) {
        Object.entries(category.sub_categories).forEach(([key, val]) => {
          const index = ids.indexOf(val.id);
          ids.splice(index, 1);
        });
        this.subcategory_id = ids
        return;
      }

      Object.entries(category.sub_categories).forEach(([key, val]) => {
        ids.push(val.id) // the value of the current key.
        console.log(val.id);
      });

      var unique = ids.filter(function (elem, index, self) {
        return index === self.indexOf(elem);
      })
      this.subcategory_id = unique

    },
    search() {
      this.search_method(1, "first_search");
    },
    ChangeIsOffer() {
      this.is_offer = !this.is_offer;
      this.search_method(1, "first_search");
    },
    changeView(view) {
      this.view = view;
    },
    initSearch() {
      let current_url = window.location.href;
      if (current_url.indexOf("?") != -1) {
        if (current_url.indexOf("keyword") != -1) {
          let url = new URL(current_url);
          let val = url.searchParams.get("keyword");
          this.keyword = val;
        }

        if (current_url.indexOf('category_id') != -1) {
          let url = new URL(current_url);
          let val = url.searchParams.get("category_id");
          val = JSON.parse(val)
          if (val == null) {
            val = []
          }
          this.category_id = val
        }

        if (current_url.indexOf('subcategory_id') != -1) {
          let url = new URL(current_url);
          let val = url.searchParams.get("subcategory_id");
          val = JSON.parse(val)
          if (val == null) {
            val = []
          }
          this.subcategory_id = val
        }

        this.search_method(1, "first_search");
      } else {
        this.search_method(1, "first_search");
      }
    },
    changeCategory(category_id) {
      this.subcategory_id = category_id;
      this.search_method(1, "first_search");
    },
    changeSort() {
      this.search_method(1, "first_search");
    },
    search_method: async function (page, index = null) {
      try {
        this.message = "";
        this.loading = true;
        this.items = { data: [] };

        if (typeof page === "undefined") {
          page = 1;
        }
        let params = {
          page: page,
        };

        if (this.sort != 'sort') {
          params.sort = this.sort_pram;
        }
        if (this.keyword != "") {
          params.keyword = this.keyword;
        }
        if (this.is_offer == true) {
          params.is_offer = true;
        }
        if (this.category_id.length != 0 || this.category_id == null) {
          params.category_id = JSON.stringify(this.category_id)
        }

        if (this.subcategory_id.length != 0) {
          params.subcategory_id = JSON.stringify(this.subcategory_id)
        }

        params.ajax = 1;
        this.all_params = params;

        let res = await axios.get('/' + this.lang + '/search', { params });
        if (res.status == 200) {
          this.loading = false;
          history.pushState({}, null, "?" + res.data.url);
          if (res.data.status == 200) {
            this.items = res.data.data;
            if (res.data.data.data.length == 0) {
              this.message = "no data";
            }
          } else {
            this.items = [];
            this.message = res.data.message;
          }
        }
      } catch (res) {
        console.log(res);
        // this.handleError(this.fetchColor);
      }
    },
  },
};
</script>
<style>
.pagination {
  justify-content: center !important;
  align-items: center !important;
  font-weight: 600 !important;
  font-size: 16px !important;
  margin-bottom: 0 !important;
  display: flex;
  padding-left: 0;
  list-style: none;
  border-radius: 3px;
}

.pagination>li {
  font-size: 18px !important;
  display: block !important;
}

.pagination>li>a {
  align-items: center !important;
  border-radius: 50% !important;
  position: relative;
  line-height: 1.25;
  border: 0 solid #dee2e6;
  margin-left: 0;
  width: 40px;
  height: 40px;
  padding: 15px !important;
  align-items: center !important;
  display: flex !important;
}
</style>
