<template>
  <ul class="navbar-nav flex-row justify-content-xl-center d-flex flex-wrap text-body py-0 navbar-right">
    <li v-if="user.id == 0" class="nav-item">
      <a class="nav-link pr-3 py-0" href="javascript:void(0)" data-toggle="modal" data-target="#sign-in">
        <svg class="icon icon-user-light">
          <use xlink:href="#icon-user-light"></use>
        </svg>
      </a>
    </li>
    <li v-else class="nav-item dropdown">
      <a class="nav-link dropdown-toggle pr-3 py-0" href="javascript:void(0)" id="navbarDropdown" role="button" data-bs-toggle="dropdown"
        aria-expanded="false">
        <svg class="icon icon-user-light">
          <use xlink:href="#icon-user-light"></use>
        </svg>
      </a>
      <ul class="dropdown-menu dropdown-menuhome" aria-labelledby="navbarDropdown">
        <li class="nav-item nav-itemmain">
          <a class="dropdown-item " href="/wallet">
            <div class="d-flex justify-content-between">
              <span>{{$t('main.My balance')}}</span>
              <span>{{ balance }} {{$t('main.SAR')}}</span>
            </div>
          </a>
        </li>
        <li class="nav-item nav-itemmain">
          <a class="dropdown-item " href="/my-orders">
            {{$t('main.My Orders')}}
          </a>
        </li>
        <li class="nav-item nav-itemmain">
          <a class="dropdown-item " href="/notifications">
            {{$t('main.Notifications')}}
          </a>
        </li>
        <li class="nav-item nav-itemdrop">
          <a class="dropdown-item " href="/addresses">
            {{$t('main.Addressess')}}
          </a>
        </li>
        <li class="nav-item nav-itemdropp">
          <a class="dropdown-item " href="/account">
            {{$t('main.Edit Profile')}}
          </a>
        </li>
        <li class="nav-item nav-itemdropp">
          <a class="dropdown-item " href="/logout">
            {{$t('main.Logout')}}
          </a>
        </li>
      </ul>
    </li>
    <li v-if="user.id == 0" class="nav-item">
      <a class="nav-link position-relative px-4 py-0" data-toggle="modal" data-target="#sign-in" href="javascript:void(0)">
        <svg class="icon icon-star-light">
          <use xlink:href="#icon-star-light"></use>
        </svg></a>
    </li>
    <li v-else class="nav-item">
      <a class="nav-link position-relative px-4 py-0" href="/wishlist">
        <svg class="icon icon-star-light">
          <use xlink:href="#icon-star-light"></use>
        </svg>
        <span class="position-absolute number">{{ wishlist_count }}</span></a>
    </li>
    <li v-if="user.id == 0" class="nav-item">
      <a class="nav-link position-relative px-4 menu-cart py-0 d-inline-flex align-items-center mr-n2" href="javascript:void(0)"
        data-toggle="modal" data-target="#sign-in">
        <svg class="icon icon-shopping-bag-open-light">
          <use xlink:href="#icon-shopping-bag-open-light"></use>
        </svg>
      </a>
    </li>
    <li v-else class="nav-item">
      <a class="nav-link position-relative px-4 menu-cart py-0 d-inline-flex align-items-center mr-n2" href="javascript:void(0)"
        data-canvas="true" data-canvas-options='{"container":".cart-canvas"}'>
        <!--<span class="mr-2 font-weight-bold fs-15">$0.00</span>-->
        <svg class="icon icon-shopping-bag-open-light">
          <use xlink:href="#icon-shopping-bag-open-light"></use>
        </svg>
        <span class="position-absolute number">{{ cart_count }}</span>
      </a>
    </li>
  </ul>
</template>

<script>
export default {
  props: {
    user: {
      type: Object,
      default: () => ({
        id: 0,
        activate: 0,
      }),
    },
    statistic_data: {
      type: Object,
      default: () => ({
        wishlist_count: 0,
        balance: 0.0,
        cart_count: 0,
      }),
    }
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      balance: this.statistic_data.balance,
      cart_count: this.statistic_data.cart_count,
      wishlist_count: this.statistic_data.wishlist_count,
    };
  },
  mounted() {
    this.$root.$on("getItem", (data) => {
      this.item = data;
      this.quantity = data.min_quantity;
    });
    this.$root.$on("updateWishlist", (data) => {
      this.wishlist_count = data;
    });
    this.$root.$on("updateCountItems", (data) => {
      this.cart_count = data;
    }); 
  },
  methods: {
    closeModel: function () {
      $("#addToCartBox").modal("hide");
    },
  },
};
</script>
