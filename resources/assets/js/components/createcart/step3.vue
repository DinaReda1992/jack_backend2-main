<template>
  <div :class="{ 'read-only': loading_all }">
    <div class="row">
      <div class="col-md-12 col-12 cart-page">
        <aside>
          <div class="summary">
            <div class="mb-3" style="width:300px">
              <label for="promo-code">{{$t('main.Enter Discount Coupon')}}</label>
              <div class="form-group d-flex justify-content-between align-items-center">
                <input id="promo-code" class="m-0 form-control" v-model="cobon" type="text" name="promo-code" />
                <button @click.prevent="check_cobon" :disabled="loading_cobon" class="btn btn-primary m-0">
                  {{$t('main.Enter')}}
                </button>
              </div>
              <div class="mb-3 " v-if="cobon_erorr != ''">
                {{ cobon_erorr }}
              </div>
            </div>
            <div class="summary-total-items summary-total" style="font-size: 14px;" v-if="order.count_items > 0">
              <span class="">{{$t('main.Count Products')}} :</span>
              <span class="">{{ order.count_items }}</span>
            </div>
            <div class="summary-subtotal   my-0" v-if="order.order_price > 0">
              <div class="subtotal-title">{{$t('main.Products Price')}}</div>
              <div class="subtotal-value final-value">
                {{ mathRound(order.order_price) }}
              </div>
            </div>
            <div class="summary-subtotal  my-0" v-if="order.delivery_price > 0">
              <div class="subtotal-title">{{$t('main.Delivery Price')}}</div>
              <div class="subtotal-value final-value">
                {{ order.delivery_price }}
              </div>
            </div>

            <div v-if="order.cobon_discount > 0" class="summary-subtotal   my-0">
              <div class="subtotal-title">{{$t('main.Coupon Discount')}}</div>
              <div class=" final-value">
                {{ mathRound(order.cobon_discount) }} {{$t('main.SAR')}}
              </div>
            </div>
            <div class="summary-subtotal  my-0" v-if="order.taxes > 0">
              <div class="subtotal-title">{{$t('main.Tax')}}</div>
              <div class=" final-value">
                {{ mathRound(order.taxes)}} {{$t('main.SAR')}}
              </div>
            </div>
            <div class="summary-total  my-0" v-if="order.final_price > 0">
              <div class="total-title">{{$t('main.Total')}}</div>
              <div class="total-value final-value" id="basket-total">
                {{ mathRound(order.final_price)}} {{$t('main.SAR')}}
              </div>
            </div>
          </div>
        </aside>
      </div>
      <div v-if="!confirmed" class=" col-md-6">
        <div class="">
          <div class="">
            <div class="d-flex ">
              <h3>{{$t('main.Choose Payment Method')}}</h3>
              <a class="btn btn-success mr-2" target="_blank" :href="'/cart-invoice/' + this.order.short_code">{{$t('main.See Invoice')}}
                </a>
            </div>
          </div>
          <ul class="col-md-6">
            <li>
              <div class="panel panel-default">
                <div class="panel-body">
                  <h5 class="card-title d-flex align-items-center">
                    <label class="mb-0 mx-2"><input @change="select_payment_method" type="radio" name="payment"
                        value="1" v-model="payment_id" /></label>
                    <span>{{$t('main.Paid')}}</span>
                  </h5>
                </div>
              </div>
            </li>
            <li>
              <div class="panel panel-default">
                <div class="panel-body">
                  <h5 class="card-title d-flex align-items-center">
                    <label class="mb-0 mx-2"><input type="radio" @change="select_payment_method" name="payment"
                        value="2" v-model="payment_id" /></label>
                    <span>{{$t('main.Bank Transfer')}}</span>
                  </h5>
                  <div class="form-group" v-if="payment_id == 2">
                    <label> {{$t('main.Bank Transfer Photo')}}</label>
                    <div class="col-ting">
                      <div class="control-group file-upload text-center" id="file-upload1">
                        <label class="image-box text-center d-block border">
                          <p>{{$t('main.Add a picture or file')}}</p>
                          <img v-if="photo == ''" src="/images/upload_image.svg" class="" />
                          <img v-if="photo != '' && file_type != 'pdf'" :src="photo" class="placeholder" />
                          <span v-if="photo != '' && file_type == 'pdf'">{{$t('main.file uploaded')}}</span>
                          <input style="display: none" ref="photo" v-on:change="onImageChange($event, 'photo')"
                            type="file" name="contact_image_1" />
                        </label>
                        <a v-if="transfer_photo != ''" :href="'/uploads/' + transfer_photo">{{$t('main.See The Receipt')}}</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </li>
            <li>
              <div class="panel panel-default">
                <div class="panel-body">
                  <h5 class="card-title d-flex align-items-center">
                    <label class="mb-0 mx-2"><input @change="select_payment_method" type="radio" name="payment"
                        value="3" v-model="payment_id" /></label>
                    <span>{{$t('main.Pay Later')}}</span>
                  </h5>
                  <div class="form-group" v-if="payment_id == 3">
                    <label>{{$t('main.Invoice Photo')}}({{$t('main.Optional')}})</label>
                    <div class="col-ting">
                      <div class="control-group file-upload text-center" id="file-upload2">
                        <label class="image-box text-center d-block border">
                          <p>{{$t('main.Add a picture or file')}}</p>
                          <img v-if="photo2 == ''" src="/images/upload_image.svg" class="" />
                          <img v-if="photo2 != '' && file_type != 'pdf'" :src="photo2" class="placeholder" />
                          <span v-if="photo2 != '' && file_type == 'pdf'">{{$t('main.file uploaded')}}</span>
                          <input style="display: none" ref="photo2" v-on:change="onImageChange($event, 'photo2')"
                            type="file" name="contact_image_1" />
                        </label>
                        <a v-if="transfer_photo != ''" :href="'/uploads/' + transfer_photo">{{$t('main.See The Receipt')}}</a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <div v-else>
        <div v-if="confirm" class="alert alert-success text-center">
          تم تأكيد الطلب بنجاح
        </div>
        <div v-else class="alert alert-success text-center">
          تم حفظ الطلب في المسودات بنجاح
        </div>
        <div class="text-center">
          <a class="btn btn-primary" href="/admin-panel/orders/create">{{$t('main.Prepare New Cart')}}</a>
          <a class="btn btn-success" target="_blank" :href="'/admin-panel/orders/' + order.id + '/edit'">{{$t('main.See Invoice')}}
            </a>
        </div>
      </div>
    </div>
    <div class="buttons-direction" v-if="!confirmed">
      <button class="btn btn-success" @click.prevent="back_btn">{{$t('main.Previous')}}</button>
      <div>
        <button class="btn btn-primary" :disabled="loading" @click.prevent="confirm_order(false)">
          {{$t('main.Save In Draft')}}
        </button>
        <button class="btn btn-success" :disabled="loading" @click.prevent="confirm_order">
          {{$t('main.Accept Order')}}
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  components: {},

  props: {
    order: {
      type: Object,
      default: [],
    },
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      loading_all: false,
      confirmed: false,
      activeItem: "home",
      payment_id:
        this.order.cart_payment_method == 0
          ? 1
          : this.order.cart_payment_method == 5
            ? 1
            : this.order.cart_payment_method == 4
              ? 2
              : 1,
      addresses: [],
      user: "",
      file_type: "",
      photo: "",
      photo2: "",
      short_code: "",
      confirm: true,
      transfer_photo: this.order.transfer_photo
        ? this.order.transfer_photo.photo
        : "",
      cobon_erorr: "",
      cobon: "",
      loading_cobon: false,
    };
  },
  mounted() {
    this.getOrder();
    this.$root.$on("updateUser", (data) => {
      // console.log(data[0][0])
      this.addresses = data[0];
      this.user = data[1];
      if (data[0].length > 0) {
        this.address_id = data[0][0].id;
      }
    });
  },
  computed: {},

  methods: {
    mathRound: function (num) {
      return Math.round(num * 100) / 100;
    },
    onImageChange(e, name) {
      let files = e.target.files || e.dataTransfer.files;
      if (!files.length) {
        return;
      }
      if (files[0].type == "application/pdf") {
        this.file_type = "pdf";
      }
      this.createImage(files[0], name);
    },
    createImage(file, name) {
      let reader = new FileReader();
      let vm = this;
      reader.onload = (e) => {
        vm[name] = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    back_btn() {
      this.$root.$emit("updateTap", "address");
    },

    confirm_order: async function (confirm = true) {
      try {
        if (
          this.payment_id == 2 &&
          this.photo == "" &&
          this.transfer_photo == ""
        ) {
          this.$toast.error("قم برفع إيصال الدفع");
          return;
        }
        // this.$toast.removeAll();
        this.loading = true;
        var formData = new FormData();
        formData.append("order_id", this.order.id);
        formData.append("payment_id", this.payment_id);
        if (confirm) {
          formData.append("done", true);
        }
        if (this.payment_id == 2) {
          formData.append("photo", this.$refs["photo"].files[0]);
        }
        if (this.payment_id == 3) {
          if (this.photo2 != "") {
            formData.append("photo", this.$refs["photo2"].files[0]);
          }
        }
        let res = await axios.post(
          '/'+this.lang+"/admin-panel/orders/confirm-order",
          formData
        );
        if (res.status == 200) {
          this.loading = false;
          this.confirmed = true;

          if (!confirm) {
            this.confirm = false;
            this.$toast.success( "تم حفظ الطلب فى المسودات",);
          } else {
            this.$toast.success(res.data.message);
          }
        } else if (res.status == 202) {
          this.loading = false;
          this.$toast.error(res.data.message);
        } else {
          this.loading = false;
          this.$toast.error(res.data.message);
        }
      } catch (res) {
        console.log(res);
        this.$toast.error(res.data.message);
      }
    },

    check_cobon: async function () {
      try {
        // this.$toast.removeAll();
        this.loading_cobon = true;
        var formData = new FormData();
        formData.set("code", this.cobon);
        formData.set("order_id", this.order.id);
        let res = await axios.post('/'+this.lang+"/admin-panel/check-cobon", formData);
        if (res.status == 200) {
          if (res.data.status == 200) {
            this.loading_cobon = false;
            this.order = res.data.order;
            this.$toast.success(res.data.message + " قيمة الخصم " + res.data.money);
          } else {
            this.loading_cobon = false;
            this.cobon_erorr = res.data.message;
            this.$toast.error(res.data.message);
          }
        } else {
          this.loading_cobon = false;
          this.$toast.error(res.data.message);
        }
      } catch (res) {
        console.log(res);
        /*this.$toast.error(
          res.data.message
        })*/
      }
    },
    getOrder() {
      let res = axios.get('/'+this.lang+"/admin-panel/get-order/" + this.order.id);
      if (res.status == 200) {
        this.order = res.data.order;
      }
    },
    select_payment_method: async function () {
      try {
        // this.$toast.removeAll();
        this.loading_all = true;
        var formData = new FormData();
        formData.append("order_id", this.order.id);
        formData.append("payment_id", this.payment_id);

        let res = await axios.post(
          '/'+this.lang+"/admin-panel/orders/select-payment-method",
          formData
        );
        if (res.status == 200) {
          this.short_code = this.order.short_code;
          this.loading_all = false;
          /* this.confirmed=true
          this.$toast.success(res.data.message)*/
        } else if (res.status == 202) {
          this.loading_all = false;
          this.$toast.error(res.data.message);
        } else {
          this.loading_all = false;
          this.$toast.error(res.data.message);
        }
      } catch (res) {
        console.log(res);
        this.$toast.error(res.data.message);
      }
    },
  },
};
</script>
<style>
ul {
  margin: 0;
  padding: 0;
  list-style: none;
}

.buttons-direction {
  display: flex;
  justify-content: space-between;
}

.d-block {
  display: block;
  padding: 10px;
}

.border {
  border: 1px solid;
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

.cart-page .quantity-field {
  background-color: rgba(209, 131, 50, 0.34);
  border: 1px solid #d18332;
  border-radius: 0;
  font-size: 0.625rem;
  padding: 5px;
  margin: 0 5px;
  width: 100%;
  text-align: center;
}

.cart-page aside {
  position: relative;
  width: 100%;
}

.cart-page .summary {
  background-color: rgba(209, 131, 50, 0.34);
  border: 1px solid #d18332;
  padding: 1rem;
  position: relative;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
}

.cart-page .summary-total-items {
  color: #000;
  font-size: 0.875rem;
  text-align: center;
}

.cart-page .summary-subtotal,
.cart-page .summary-total {
  /*border-top: 1px solid #000;*/
  border-bottom: 1px solid #000;
  clear: both;
  margin: 1rem 0;
  overflow: hidden;
  padding: 0.5rem 0;
}

.cart-page .subtotal-title,
.cart-page .subtotal-value,
.cart-page .total-title,
.cart-page .total-value {
  color: #000;
  float: right;
  width: 50%;
}

.cart-page .total-title {
  font-weight: 700;
  text-transform: uppercase;
}

.cart-page .summary-checkout {
  display: block;
}

.cart-page .checkout-cta {
  display: block;
  float: none;
  font-size: 0.75rem;
  text-align: center;
  text-transform: uppercase;
  padding: 0.625rem 0;
  width: 100%;
}
</style>
