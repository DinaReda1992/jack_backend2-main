<template>
  <a href="#" v-if="single == 0" @click.prevent="add_to_wishlist(true)" data-toggle="tooltip" data-placement="left"
    :title="$t('main.Add To Wishlist')"
    class="add-to-wishlist ml-auto d-flex align-items-center justify-content-center w-48px h-48px rounded-circle mb-2 bg-hover-primary border-hover-primary"
    :class="is_fav ? 'liked' : 'unlike'">
    <svg class="icon icon-star-light fs-24">
      <use xlink:href="#icon-star-light" v-if="!is_fav"></use>
      <use xlink:href="#icon-heart" v-if="is_fav"></use>
    </svg>
  </a>
  <a href="#" v-else @click.prevent="add_to_wishlist(true)"
    class="text-decoration-none font-weight-bold fs-16 d-flex align-items-center">
    <svg class="icon icon-star-light fs-20">
      <use xlink:href="#icon-star-light" v-if="!is_fav"></use>
      <use xlink:href="#icon-heart" v-if="is_fav"></use>
    </svg>
    <span class="ml-2" v-if="!is_fav">{{ $t('main.Add To Wishlist') }}</span>
    <span class="ml-2" v-if="is_fav">{{ $t('main.Delete From Wishlist') }}</span>
  </a>
</template>

<script>
export default {
  components: {},

  props: {
    item: {
      type: Object,
      default: "",
    },
    user: {
      type: Object,
      default: () => ({
        id: 0,
        activate: 0,
      }),
    },
    single: {
      type: Number,
      default: 1,
    },
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      is_fav: this.item.is_fav,
    };
  },
  mounted() {
    this.$root.$on("getItemQuick", (data) => {
      // this.item = data;
      this.is_fav = data.is_fav;
    });
    this.$root.$on("updateItem", (data) => {
      if (this.item.id == data.id) {
        this.is_fav = data.is_fav;
        this.item.is_fav = data.is_fav;
      }
    });

  },

  methods: {
    add_to_wishlist: async function (status) {
      try {
        if (this.user.id == 0) {
          Vue.$toast.warning(this.$t('main.login first'), { position: "top-left" });
          $("#sign-in").modal("show");
          return;
        }
        var formData = new FormData();
        formData.set("id", this.item.id);
        let res = await axios.post('/' + this.lang + "/add-to-fav", formData);
        if (res.status == 200) {
          if (this.item.is_fav == 1) {
            this.item.is_fav = 0;
            this.is_fav = 0;
          } else {
            this.item.is_fav = 1;
            this.is_fav = 1;
          }
          this.$root.$emit('updateWishlist', res.data.wishlist_count);
          this.$root.$emit('updateItem', this.item);
          Vue.$toast.success(res.data.message, { position: "top-left" });
        } else {
          Vue.$toast.error(res.data.message, { position: "top-left" });
        }
      } catch (res) {
        this.$toast.error(res.data.message);
      }
    },
  },
};
</script>
<style>
.liked {
  color: #fff !important;
  background: #4e0161 !important;
}

a.liked:focus {
  background-color: #4e0161 !important;
  color: white !important;
}

a.unlike:focus {
  background: #fff !important;
  color: #4e0161 !important;
}

a.unlike:hover {
  background-color: #4e0161 !important;
  color: white !important;
}

.unlike {
  background: #fff !important;
  color: #4e0161 !important;
}
</style>