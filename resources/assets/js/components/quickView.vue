<template>
  <a
    @click.prevent="view_product(true)"
    data-toggle="tooltip"
    data-placement="left"
    :title="$t('main.Quick View')"
    class="preview ml-auto d-md-flex align-items-center justify-content-center cursor-pointer text-secondary bg-white hover-white bg-hover-secondary w-48px h-48px rounded-circle mb-2 d-none"
  >
    <span>
      <svg class="icon icon-eye-light fs-24">
        <use xlink:href="#icon-eye-light"></use>
      </svg>
    </span>
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
      this_item: this.item,
    };
  },
  mounted() {},

  methods: {
    view_product: function(status) {
      if (status) {
        $("#quick-view").modal("show");
        this.$root.$emit("getItemQuick", this.this_item);
      } else {
        $("#quick-view").modal("hide");
      }
    },
  },
};
</script>
