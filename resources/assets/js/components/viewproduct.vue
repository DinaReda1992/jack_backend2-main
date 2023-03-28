<template>
  <div class="modal fade quick-view" id="quick-view" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header border-0 py-0">
          <button type="button" class="close fs-32" data-dismiss="modal" aria-label="Close" @click="closeModel">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 pr-xl-5 mb-8 mb-md-0 pl-xl-8">
              <div class="galleries-product product galleries-product-02 position-relative">
                <div class="view-slider-for mx-0">
                  <div class="box px-0" style="height:400px;" id="slider-box0">
                    <div class="card p-0 rounded-0 border-0">
                      <a :href="photo" class="card-img">
                        <img :src="photo" :alt="item.photo">
                      </a>
                    </div>
                  </div>
                </div>
                <div class=" mx-n1" style="display: flex;">
                  <div class="box py-4 px-1 cursor-pointer">
                    <img :src="item.photo" :alt="item.title" style="height: 108px;width: 85px;" @click.prevent="changePhoto(item.photo)">
                  </div>
                  <div class="box py-4 px-1 cursor-pointer" v-for="(photo, index) in item.photos" :key="index + 1">
                    <img :src="photo.photo" :alt="item.title" style="height: 108px;width: 85px;" @click.prevent="changePhoto(photo.photo)">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 pl-xl-6 pr-xl-8">
              <h2 class="fs-24 mb-2"> {{ item.title }}</h2>
              <p class="d-flex align-items-center mb-3">
                <span class="text-line-through" v-if="item.offer_price > 0">{{ item.price }} {{$t('main.SAR')}}</span>
                &nbsp; &nbsp;
                <span class="fs-18 text-secondary font-weight-bold ml-3" v-if="item.offer_price > 0"> {{
                  item.offer_price
                }} {{$t('main.SAR')}}</span>
                <span class="fs-18 text-secondary font-weight-bold ml-3" v-if="item.offer_id == 0"> {{
                  item.price
                }}
                  {{$t('main.SAR')}}</span>
                <span class="badge badge-primary fs-16 ml-4 font-weight-600 px-3" v-if="item.offer_type != ''">{{
                  item.offer_type
                }}</span>
              </p>
              <p class="mb-3">
                {{ item.description }}
              </p>
              <add-to-cart-action :item="item" :single="2" :user="user"></add-to-cart-action>
              <div class="d-flex align-items-center flex-wrap mt-4 mb-4">
                <add-to-wishlist :item="item" :single="1" :user="user"></add-to-wishlist>
                <hr>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VueSlickCarousel from 'vue-slick-carousel'
import 'vue-slick-carousel/dist/vue-slick-carousel.css'
// optional style for arrows & dots
import 'vue-slick-carousel/dist/vue-slick-carousel-theme.css'

export default {
  props: {
    components: { VueSlickCarousel },
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
      item: {},
      quantity: 0,
      photo: '',
      settings: {
        "dots": true,
        "dotsClass": "slick-dots custom-dot-class",
        "edgeFriction": 0.35,
        "infinite": false,
        "slidesToShow": 1,
        "slidesToScroll": 1,
        "autoplay": true,
        "speed": 2000,
        "autoplaySpeed": 2000,
      }
    };
  },
  mounted() {
    this.$root.$on('getItemQuick', data => {
      this.item = data;
      this.photo=data.photo;
      this.quantity = data.min_quantity
    });
  },
  methods: {
    changePhoto: function (photo) {
      this.photo = photo;
    },
    closeModel: function () {
      $('#quick-view').modal('hide');
    },
  },
};
</script>
<style>
.slick-next::before {
  color: black;
}

.slick-prev::before {
  color: black;
}
</style>

