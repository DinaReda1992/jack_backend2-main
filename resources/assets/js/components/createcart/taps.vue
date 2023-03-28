<template>

  <div>

    <ul class="nav nav-tabs text-center">
      <li :class="{ active: isActive('home') }"><a href="#home">{{ $t('main.Products and Client information') }}</a>
      </li>
      <li :class="{ active: isActive('address') }"><a href="#menu1">{{ $t('main.Address') }}</a></li>
      <li :class="{ active: isActive('confirm') }"><a href="#menu2" :class="{ active: isActive('confirm') }">
          {{ $t('main.Payment method and Confirm Order') }}</a></li>
    </ul>

    <div class="tab-content">
      <div id="home" class="tab-pane fade " style="opacity: 1"
        :class="{ 'active show  active-tap': activeItem == 'home' }">
        <h3>{{ $t('main.Products and Client information') }}</h3>
        <step1 :order="order" :cart="cart" />
      </div>
      <div id="menu1" class="tab-pane fade" :class="{ 'active show active-tap ': activeItem == 'address' }">
        <step2 :order="order" />
      </div>
      <div id="menu2" class="tab-pane fade" :class="{ 'active show active-tap': activeItem == 'confirm' }">
        <step3 :order="order" />

      </div>

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
      loading: false,
      loading_cart: false,
      all_products: [],
      all_users: this.users,
      activeItem: 'home',
      available_taps: 1

    }
  },
  mounted() {
    history.pushState({}, null, '/' + this.lang + '/admin-panel/orders/create/' + this.order.token);



    this.$root.$on('taps', data => {
      this.available_taps = data
    });
    this.$root.$on('updateTap', data => {
      console.log(data)
      this.activeItem = data
    });
    /* this.$root.$on('updateCart', data => {
      this.all_items=data
     });*/

  },
  computed: {
    total: function () {
      let total = [];

      Object.entries(this.all_products).forEach(([key, val]) => {
        total.push(val.price * val.quantity) // the value of the current key.
      });

      return total.reduce(function (total, num) { return total + num }, 0);
    }
  },

  methods: {
    isActive(menuItem) {
      return this.activeItem === menuItem
    },
    setActive(menuItem) {
      this.activeItem = menuItem
    },
    update_item: async function (item) {
      try {
        // this.$toast.removeAll()
        this.loading_cart = true
        var formData = new FormData();
        formData.set('id', item.id);
        formData.set('quantity', item.quantity);
        let res = await axios.post('/cart/update', formData);
        if (res.status == 200) {
          this.loading_cart = false
          this.$root.$emit('updateCart', res.data.items)

          this.$toast.success(res.data.message)
        }
        else if (res.status == 202) {
          this.loading_cart = false
          let id = item.id
          const index = this.items.findIndex(item => item.id == id);
          item.quantity = item.product.min_quantity
          this.all_items[index] = item
          this.$toast.error(res.data.message);
        } else {
          this.loading_cart = false
          this.$toast.error(res.data.message);
        }

      } catch (res) {
        console.log(res)
        this.$toast.error(res.data.message);
      }

    },

    delete_item: async function (item) {
      try {
        var r = confirm("هل أنت متأكد من حذف المنتج؟")

        if (r == true) {
          // this.$toast.removeAll()
          this.loading = true
          let params = {
            id: item,
          };
          let res = await axios.post('/cart/delete', params);
          if (res.status == 200) {
            this.all_items = res.data.items
            this.$root.$emit('updateCart', res.data.items)
            this.$toast.error(res.data.message);
          } else {
            let errors = res.data.errors
            var i;
            for (i = 0; i < errors.length; i++) {
              this.$toast.error(errors[i]);
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


  },
};
</script>
<style>
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
    content: '';
    display: none;
  }
}
</style>
