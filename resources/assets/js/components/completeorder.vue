<template>
  <div class="row">
    <div class="col-md-8 col-12">
      <div class="p-3">
        <span class="f-17">العنوان الأفتراضي:</span>
        <span class="f-15">{{ address.address }}</span>
        <a href="/addresses" class="px-2">تغيير العنوان</a>
      </div>
      <div>
        <ul>
          <li v-for="(message, index) in messages" class="alert alert-warning mx-1 px-2">{{ message }}</li>
        </ul>
      </div>
      <div class="basket">
        <div class="basket-labels">
          <ul>
            <li class="item item-heading">المنتج</li>
            <li class="price">السعر</li>
            <li class="quantity">الكمية</li>
            <li class="subtotal">مجمل السعر</li>
          </ul>
        </div>
        <div v-for="(item, index ) in all_items" class="basket-product">
          <div class="item">
            <div class="product-image">
              <img :src="item.product.photo" width="50" :alt="item.title" class="product-frame">
            </div>
            <div class="product-details">
              <h1><strong><span class="item-quantity">{{ item.quantity }}</span> {{ item.product.title }}</strong></h1>
              <!--              <p><strong>اسم التصنيف</strong></p>-->
            </div>
          </div>
          <div class="price">{{ item.price }}</div>
          <div class="quantity">
            <select @change="update_item(item)" v-model="item.quantity" class="w-100 quantity-field">
              <template v-for="num in 500">
                <option v-if="num >= item.product.min_quantity">{{ num }}</option>
              </template>
            </select>


            <!--            <input type="number" @change="update_item(item)" v-model="item.quantity" :min="item.min_quantity" class="quantity-field">-->
          </div>
          <div class="subtotal">{{ mathRound(item.price * item.quantity) }}</div>
          <div class="remove">
            <button @click.prevent="delete_item(item.id)">حذف</button>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4 col-12">
      <aside>
        <div class="summary">
          <div class="mb-3">
            <label for="promo-code">ادخل كود الخصم</label>
            <div class="form-group d-flex justify-content-between align-items-center">
              <input id="promo-code" class="m-0" v-model="cobon" type="text" name="promo-code">
              <button @click.prevent="check_cobon" :disabled="loading_cobon" class="btn m-0">تنفيذ</button>
            </div>
            <div class="mb-3 " v-if="cobon_erorr != ''">{{ cobon_erorr }}</div>
          </div>
          <!--          <div class="summary-total-items">مجموع المنتجات : <span class="total-items"></span>-->
          <div class="summary-total-items summary-total">
            <span class="">مجموع المنتجات :</span>
            <span class="">{{ all_items.length }}</span>
          </div>
          <div class="summary-subtotal   my-0">
            <div class="subtotal-title">سعر المنتجات</div>
            <div class="subtotal-value final-value">{{ mathRound(total) }}</div>
          </div>
          <div class="summary-subtotal  my-0">
            <div class="subtotal-title">سعر الشحن</div>
            <div class="subtotal-value final-value">{{ shipmentprice }}</div>
          </div>

          <div v-if="order.cobon_discount > 0" class="summary-subtotal   my-0">
            <div class="subtotal-title">خصم الكوبون</div>
            <div class=" final-value"> {{ mathRound(order.cobon_discount) }} SAR</div>
          </div>
          <div class="summary-subtotal  my-0">
            <div class="subtotal-title">نسبة الضرائب</div>
            <div class=" final-value"> {{ mathRound((total + shipmentprice - order.cobon_discount) * (taxs / 100)) +
            'SAR'}}
            </div>
          </div>
          <div class="summary-total  my-0">
            <div class="total-title">السعر الكلي</div>
            <div class="total-value final-value" id="basket-total">
              {{
            mathRound((total + shipmentprice - order.cobon_discount + ((total + shipmentprice - order.cobon_discount)
              * taxs / 100)))}}

            </div>
          </div>
          <div class="pay-type">
            <!--            <label>
              <input type="radio" name="radio" value="1" checked/>
              <span> دفع الكتروني </span>
            </label>-->
            <label>
              <input type="radio" v-model="payment_type" :checked="payment_type == 'bank'" value="bank" name="radio" />
              <span> تحويل بنكي </span>
            </label>
            <label>
              <input type="radio" v-model="payment_type" :checked="payment_type == 'balance'" value="balance"
                name="radio" />
              <span> دفع من الرصيد الشخصي </span>
            </label>
            <label v-if="pay_later">
              <input type="radio" v-model="payment_type" :checked="payment_type == 'later'" value="later"
                name="radio" />
              <span> الدفع لاحقا </span>
            </label>
            <label v-if="online_payment">
              <input type="radio" v-model="payment_type" :checked="payment_type == 'online'" value="online"
                name="radio" />
              <span> دفع الكتروني </span>
            </label>
          </div>
          <div class="summary-checkout">
            <button :disabled="loading" @click.prevent="add_order" class="checkout-cta">
              <template v-if="loading">
                <div class="spinner-border spinner-border-sm text-center text-white" role="status">
                  <span class="sr-only">جاري اكمال عملية الشراء ...</span>
                </div>
              </template>
              <template v-else>
                اكمل عملية الشراء
              </template>

            </button>
          </div>
        </div>
      </aside>
    </div>
  </div>


</template>



<script>
import CxltToastr from 'cxlt-vue2-toastr'
var toastrConfigs = {
  position: 'top right',
  timeOut: 5000
}
Vue.use(CxltToastr, toastrConfigs)

export default {
  components: {

  },

  props: {
    items: {
      type: Array,
      default: ""
    },
    messages: {
      type: Array,
      default: []
    },
    user: {
      type: Object,
      default: ""
    },
    address: {
      type: Object,
      default: ""
    },

    taxs: {
      type: Number,
      default: 0
    },
    shipmentprice: {
      type: Number,
      default: 0
    },
    online_payment: {
      type: Number,
      default: 0
    },
    pay_later: {
      type: Number,
      default: 0
    },


  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      message: '',
      loading: false,
      loading_cobon: false,
      all_items: this.items,
      loading_cart: false,
      order: {
        delivery_price: this.shipmentprice,
        order_price: this.total,
        address_id: this.address.id,
        taxes: this.total * (this.taxs / 100),
        final_price: this.shipmentprice + this.total + (this.total + this.shipmentprice * (this.taxs / 100)),
        cobon: '',
        cobon_discount: 0,
      },
      cobon_erorr: '',
      cobon: '',
      payment_type: 'bank',//balance
    }
  },
  mounted() {
    this.$root.$on('updateCart', data => {
      this.all_items = data
      if (this.order.cobon_discount > 0) {
        this.$toast.success('قم باضافة الكوبون بعد الانتهاء من اضافة المنتجات')
        this.cobon = ''
        this.order.cobon_discount = 0
      }

    });

  },
  computed: {
    total: function () {
      let total = [];

      Object.entries(this.all_items).forEach(([key, val]) => {
        total.push(val.price * val.quantity) // the value of the current key.
      });

      return total.reduce(function (total, num) { return total + num }, 0);

    },


  },
  methods: {
    mathRound: function (num) {
      return Math.round(num * 100) / 100

    },
    add_order: async function () {
      try {
        // this.$toast.removeAll()
        if (isNaN(this.total)) {
          this.$toast.error('قم بادخال الكميات بشكل صحيح')
          return;
        }
        this.loading = true
        var formData = new FormData();
        formData.set('delivery_price', this.shipmentprice);
        formData.set('order_price', this.total);
        formData.set('final_price', (this.shipmentprice + this.total + (this.total + this.shipmentprice * (this.taxs / 100))) - this.order.cobon_discount);
        formData.set('address_id', this.address.id);
        formData.set('taxes', this.total * (this.taxs / 100));
        formData.set('cobon', this.order.cobon);
        if (this.payment_type == 'later') {
          formData.set('payment_type', 5);
        } else if (this.payment_type == 'bank') {
          formData.set('payment_type', 4);
        } else if (this.payment_type == 'online') {
          formData.set('payment_type', 2);
        }
        else {//balance
          formData.set('payment_type', 3);
        }
        formData.set('cobon_discount', this.order.cobon_discount);
        let res = await axios.post('/add-order', formData);
        if (res.status == 200) {
          if (res.data.status == 200) {
            this.loading = false
            this.$toast.success(res.data.message);
            if (this.payment_type == 'bank') {
              setTimeout(window.location = '/checkout/' + res.data.order_id, 5000)
            } else if (this.payment_type == 'online') {
              setTimeout(window.location = res.data.url, 5000)
            }
            else {
              setTimeout(window.location = '/my-orders', 5000)
            }

          } else {
            this.loading = false
            this.$toast.error(res.data.message);
          }
          // this.all_addresses=res.data.addresses

        } else {
          this.loading = false
          this.$toast.error(res.data.message);
        }

      } catch (res) {
        console.log(res)
        /*this.$toast.error(
          res.data.message
        )*/
      }

    },

    check_cobon: async function () {
      try {
        // this.$toast.removeAll()
        this.loading_cobon = true
        var formData = new FormData();
        formData.set('code', this.cobon);
        let res = await axios.post('/check-cobon', formData);
        if (res.status == 200) {
          if (res.data.status == 200) {
            this.loading_cobon = false
            this.$toast.success(res.data.message + ' قيمة الخصم ' + res.data.money);
            this.order.cobon = this.cobon
            this.order.cobon_discount = res.data.money
          } else {
            this.loading_cobon = false
            this.cobon_erorr = res.data.message
            this.$toast.error(res.data.message)
          }

        } else {

          this.loading_cobon = false
          this.$toast.error(res.data.message);
        }

      } catch (res) {
        console.log(res)
        /*this.$toast.error(
          res.data.message
        })*/
      }

    },

    update_item: async function (item) {
      try {
        // this.$toast.removeAll()
        var formData = new FormData();
        formData.set('id', item.id);
        formData.set('quantity', item.quantity);
        let res = await axios.post('/' + this.lang + '/cart/update', formData);
        if (res.status == 200) {

          this.$toast.success(res.data.message);

          this.$root.$emit('updateCart', res.data.items)

          // this.all_addresses=res.data.addresses

        }
        else if (res.status == 202) {
          let id = item.id
          const index = this.items.findIndex(item => item.id == id);
          item.quantity = res.data.min_quantity
          // console.log(item)
          this.all_items[index] = item
          console.log(item)
          this.$toast.error(res.data.message);
        }
        else {

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
          let res = await axios.post('/' + this.lang + '/cart/delete', params);
          if (res.status == 200) {
            this.loading = false
            this.all_items = res.data.items
            this.$toast.success(res.data.message);
            this.$root.$emit('updateCart', res.data.items)
          } else {
            this.loading = false
            let errors = res.data.errors
            var i;
            for (i = 0; i < errors.length; i++) {
              this.$toast.error(errors[i]);
            }
          }

        } else {
          this.loading = false
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

.is_fav {
  color: #d18332 !important;
}

.f-15 {
  font-size: 15px;
  letter-spacing: 0;
  color: #000;
  font-weight: 700;
}

.f-17 {
  font-size: 17px;
  letter-spacing: 0;
  color: #000;
  font-weight: 700;
}
</style>
