<template>
  <div>
    <div class="card" v-if="!activation">
      <h2 class="title">تسجيل الدخول</h2>

      <form method="post" @submit.prevent="login">
        <div class="email-login">
          <label for="phone"> <b>ادخل رقم الجوال</b></label>
          <input type="number" id="phone" v-model="phone"
            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
            placeholder="05......." name="phone" maxlength="10">
        </div>
        <div class="form-group">
          <button type="submit" :disabled="loading" class="cta-btn">
            <span v-if="loading" class="">جاري الأرسال</span>
            <span v-else>أرسل كود التحقق</span>
          </button>
        </div>
        <!--          <input :disabled="loading"  class="cta-btn" value="دخول" type="submit">-->
      </form>

    </div>

    <section class="verify-section" v-if="!register_form && activation">
      <div id="wrapper">
        <div id="dialog">
          <h3>من فضلك ادخل الارقام المرسلة لكـ في رسالة نصية</h3>
          <span>(نأسف لذلك لكن يجب التأكد من صحة رقم الهاتف الذي ادخلة)</span>
          <!--        <br>
              {{activation_code2}}
          <br>-->
          <form @submit.prevent="activation_" id="form" method="post" action="/activate_phone_number"
            class="login-form activated">
            <input type="text" v-model="activation_code[0]" name="activation_code[]" class="verify-input" maxLength="1"
              size="1" min="0" max="9" pattern="[0-9]{1}" />
            <input type="text" v-model="activation_code[1]" name="activation_code[]" class="verify-input" maxLength="1"
              size="1" min="0" max="9" pattern="[0-9]{1}" />
            <input type="text" v-model="activation_code[2]" name="activation_code[]" class="verify-input" maxLength="1"
              size="1" min="0" max="9" pattern="[0-9]{1}" />
            <input type="text" v-model="activation_code[3]" name="activation_code[]" class="verify-input" maxLength="1"
              size="1" min="0" max="9" pattern="[0-9]{1}" />

            <button :disabled="loading_activation" class="btn btn-primary btn-embossed">
              <span v-if="loading_activation" class="">جاري التفعيل</span>
              <span v-else>تفعيل</span>
            </button>
            <!--          <button type="submit" :disabled="loading_activation"  class="btn btn-primary btn-embossed" value="تأكيد">تأكيد</button>-->
          </form>


          <div>
            <button v-on:click.prevent="login('resend')" :disabled="countDown < 300"
              class="text-primary btn NeoMediumArabic bg-transparent f-15">
              <span v-if="countDown < 300">
                <span class="text-black">إعادة ارسال الكود بعد</span>
                @{{ time }} </span>
              <span v-else>إعادة إرسال كود التفعيل</span>

            </button>
            <br>
            <a href="javascript:void(0)" v-on:click.prevent="change_activate" class="btn d-block btn-app" style="color: #d18332"
              type="submit">ارسال رقم التأكيد مرة اخرى</a>
            <a href="javascript:void(0)" v-on:click.prevent="change_activate">تغيير رقم الهاتف الذي تم ادخالة مسبقا</a>
          </div>
        </div>
      </div>
    </section>


    <section class="register-section" v-if="register_form && activation">
      <div class="card">
        <form @submit.prevent="register">
          <h2 class="title">تسجيل جديد</h2>
          <p class="subtitle"> تملك عضوية ؟ <a href="javascript:void(0)" @click="go_login"> تسجيل دخول</a></p>
          <form method="post" @submit.prevent="register" action="/register">

            <div class="email-login">
              <div class="row">
                <div class="col-12 col-md-6">
                  <div class="form-group py-3 ">
                    <label for="profile-pic" class="d-flex align-items-center">
                      <div class="d-flex align-items-center justify-content-center profile-pic-con">
                        <template>
                          <img v-if="photo == ''" src="/images/upload_image.svg" class="placeholder">
                          <img v-else :src="photo" class="">
                        </template>

                      </div>
                      <div class="px-3">
                        <span class="d-block text-dark f-14">صورة شخصية</span>
                        <span class="text-muted  f-14">قم برفع صورة شخصية واضحة لك</span>
                      </div>
                    </label>
                    <input id="profile-pic" accept="image/png, image/jpeg" ref="photo"
                      v-on:change="onImageChange($event, 'photo')" class="sr-only" type="file">
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="form-group py-3 ">
                    <label for="commercial_id" class="d-flex align-items-center">
                      <div class="d-flex align-items-center justify-content-center profile-pic-con">
                        <template>
                          <img v-if="commercial_id == ''" src="/images/upload_image.svg" class="placeholder">
                          <img v-else :src="commercial_id" class="">
                        </template>

                      </div>
                      <div class="px-3">
                        <span class="d-block text-dark f-14">صورة السجل التجاري</span>
                        <span class="text-muted  f-14">قم برفع صورة او ملف </span>
                      </div>
                    </label>
                    <input id="commercial_id" name="commercial_id" accept="image/png, image/jpeg,pdf"
                      ref="commercial_id" v-on:change="onImageChange($event, 'commercial_id')" class="sr-only"
                      type="file">
                  </div>
                </div>
                <!--              <div class="col-12" >
                <label for=""> <b>رقم الجوال</b></label>
                <input type="text" disabled  :value="phone" class="form-control" >
              </div>-->

                <div class="col-12 col-md-6 ">
                  <label for="username"> <b>الاسم</b></label>
                  <input type="text" v-model="username" id="username" name="username" required class="form-control"
                    placeholder="الاسم بالكامل">
                </div>
                <div class="col-12 col-md-6 ">
                  <label for="email"> <b>البريد الالكترونى</b></label>
                  <input v-model="email" type="email" id="email" value="" placeholder="البريد الالكترونى" name="email">
                </div>
                <div class="col-12 col-md-4 " style="display: none">
                  <label for="country_id"> <b>الدولة</b></label>
                  <select @change="onChangeSelect($event, 'country')" v-model="country_id" name="country_id"
                    id="country_id" required>
                    <option value="" disabled="">اختر الدولة</option>
                    <option :value="item.id" v-for="(item, index) in countries" :key="index">
                      {{ item.name }}
                    </option>
                  </select>

                </div>
                <div class="col-12 col-md-6 ">
                  <label for="region_id"> <b>المنطقة</b></label>
                  <select @change="onChangeSelect($event, 'region')" v-model="region_id" name="region_id" id="region_id"
                    required>
                    <option value="">اختر المنطقة</option>
                    <option :value="item.id" v-for="(item, index) in regions" :key="index">
                      {{ item.name }}
                    </option>
                  </select>

                </div>

                <div class="col-12 col-md-6 ">
                  <label for="state_id"> <b>المدينة</b></label>
                  <!--                js-example-basic-single-->
                  <select v-model="state_id" class="" name="state_id" id="state_id" required>
                    <option value="">اختر المدينة</option>
                    <option :value="item.id" v-for="(item, index) in states" :key="index">
                      {{ item.name }}
                    </option>
                  </select>

                </div>
                <div class="col-12 col-md-6 ">
                  <label for="client_type"> <b>نوع النشاط</b></label>
                  <select v-model="client_type" id="client_type" name="client_type" required>
                    <option value="" disabled="" selected>اختر نوع النشاط</option>
                    <option :value="item.id" v-for="(item, index) in clienttypes" :key="index">
                      {{ item.name }}
                    </option>
                  </select>
                </div>



                <div class="col-12 col-md-6 ">
                  <label for="commercial_no"> <b>رقم السجل التجارى</b></label>
                  <input v-model="commercial_no" type="number" id="commercial_no" value=""
                    placeholder="رقم السجل التجارى" name="commercial_no">

                </div>
                <div class="col-12 col-md-6 ">
                  <label for="commercial_no"> <b>الرقم الضريبي</b></label>
                  <input v-model="tax_number" type="number" id="tax_number" value="" placeholder="الرقم الضريبي"
                    name="tax_number">

                </div>

                <div class="col-12 col-md-6 ">
                  <label for=""> <b>تاريخ انتهاء السجل</b></label>
                  <date-picker v-model="commercial_end_date" format="YYYY-MM-DD" value-type="YYYY-MM-DD" type="date"
                    placeholder="اختر التاريخ" class="w-100"></date-picker>
                  <!--                <input v-model="commercial_end_date" type="text" id="commercial_end_date" value="" placeholder="تاريخ انتهاء السجل التجارى" name="commercial_end_date">-->

                </div>
                <div class="py-3 col-md-12 border-bottom">
                  <p class="ml-2 pr-3 d-flex align-items-center">
                    <input id="chacked" style="width:40px;height:30px" type="checkbox" :value="true" v-model="accept"
                      name="radio-group">
                    <label for="chacked" class="f-12 NeoMediumArabic mb-0 pr-3">
                      <a target="_blank" href="https://goldenroad.sa/page/conditions">
                        أقر بأنني قد قرأت الشروط المذكورة ووافقت عليها
                      </a>
                    </label>
                  </p>
                </div>


                <div class="col-12">
                  <button :disabled="loading_register" class="cta-btn">
                    <span v-if="loading_register" class="">جاري التسجيل</span>
                    <span v-else>تسجيل</span>
                  </button>
                  <!--                <button class="cta-btn" >تسجيل</button>-->
                </div>
              </div>
            </div>
          </form>
        </form>
      </div>
    </section>
  </div>
</template>



<script>
import CxltToastr from 'cxlt-vue2-toastr'
var toastrConfigs = {
  position: 'top right',
  timeOut: 5000
}
Vue.use(CxltToastr, toastrConfigs)
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

/*
 this.$toast.success(res.data.message)
* */
export default {
  components: {
    DatePicker
  },

  props: {

    route: {
      type: String,
      default: ""
    },
    countries: {
      type: Array,
      default: ""
    },
    clienttypes: {
      type: Array,
      default: ""
    },


  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      loading_activation: false,
      loading_register: false,
      register_form: false,
      activation: false,
      phone: '',//123456789999999
      message: '',
      activation_code: [],//1234,
      activation_code2: '',//1234,
      phonecode: '966',

      countDown: 60 * 5,
      time: '',
      regions: [],
      states: [],

      photo: '',
      username: '',
      email: '',
      country_id: 188,
      region_id: '',
      state_id: '',
      client_type: '',
      commercial_no: '',
      commercial_end_date: '',
      password: '',
      password_confirmation: '',
      commercial_id: '',
      tax_number: '',
      accept: false
    }
  },
  mounted() {
    let country_id = this.country_id
    var __FOUND = this.countries.find(function (item, index) {
      if (item.id == country_id)
        return true;
    });
    console.log(__FOUND)
    this.regions = __FOUND.get_regions

  },
  methods: {
    countDownTimer: function () {
      if (this.countDown > 0) {
        setTimeout(() => {
          this.countDown -= 1
          let minutes = Math.floor(this.countDown / 60)
          let seconds = this.countDown - minutes * 60

          this.time = minutes + ':' + seconds
          this.countDownTimer()
        }, 1000)
      } else if (this.countDown == 0) {
        this.countDown = 60 * 5
      }
    },

    routeFixed(route, id) {
      return route.replace('_id', id);
    },
    onChangeSelect(e, type) {
      if (type == 'country') {
        let country_id = this.country_id
        var __FOUND = this.countries.find(function (item, index) {
          if (item.id == country_id)
            return true;
        });
        console.log(__FOUND)
        this.regions = __FOUND.get_regions
      } else {
        let region_id = this.region_id
        var __FOUND = this.regions.find(function (item, index) {
          if (item.id == region_id)
            return true;
        });
        this.states = __FOUND.get_states
      }
    },
    go_login() {
      this.phone = ''
      this.activation = !this.activation
    },
    change_activate: function () {
      this.activation = !this.activation
    },
    login: async function (type) {
      try {
        if (this.phone == '') {
          // this.$toast.removeAll()
          this.$toast.error(this.$t('main.phone number required'))
          return;
        }

        //// this.$toast.removeAll()
        if (type != 'resend') {
          this.loading = true
        }
        var formData = new FormData();
        this.message = ''

        formData.set('phone', this.phone);
        formData.append('phonecode', this.phonecode);
        // formData.set('password', this.password);
        // let route=this.route
        let res = await axios.post('/login', formData);
        if (res.status == 200) {
          this.activation_code2 = res.data.activation_code
          if (type != 'resend') {
            this.activation = true
            this.loading = false
          }
          this.$toast.success(res.data.message)
          // this.activation_code=res.data.activation_code.activation_code
          if (type == 'resend') {
            this.countDownTimer()
          }
        } else {

        }

      } catch (res) {
        console.log(res)
        this.loading = false
        this.$toast.error(res.data.message);
        // this.handleError(this.fetchColor);
      }

    },
    activation_: async function () {
      try {
        // this.$toast.removeAll()
        let activation_code = this.activation_code[0] + this.activation_code[1] + this.activation_code[2] + this.activation_code[3]
        if ((this.phone == '' && activation_code != '') || activation_code == 'NaN') {
          // this.$toast.removeAll()
          this.$toast.error(this.$t('main.activation code required'))
          return;
        }

        //// this.$toast.removeAll()
        this.loading_activation = true
        var formData = new FormData();
        formData.set('phone', this.phone);
        formData.append('phonecode', this.phonecode);
        formData.append('activation_code', parseInt(activation_code));
        // formData.set('password', this.password);
        // let route=this.route
        let res = await axios.post('/activate_phone_number', formData);
        if (res.status == 200) {
          if (res.data.status == 400) { //400
            this.loading_activation = false
            this.$toast.error(res.data.message)
          } else if (res.data.status == 202) {//to register form 202
            this.register_form = true
            this.loading_activation = false
            this.$toast.success(this.$t('main.Complete the information to complete the registration'))
          }
          else {//login successfully 200
            this.$toast.success(this.$t('main.Login Successfully'))

            window.location = '/'
          }
        }


      } catch (res) {
        console.log(res)
        this.loading = false
        this.$toast.error(res.data.message);
        // this.handleError(this.fetchColor);
      }

    },






    /**/
    onImageChange(e, name) {
      let files = e.target.files || e.dataTransfer.files;
      if (!files.length)
        return;
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
    /**/
    register: async function () {
      try {
        if (this.phone == '' &&
          this.activation_code != '' &&
          this.username != '' &&
          this.country_id != '' &&
          this.region_id != '' &&
          this.state_id != '' &&
          this.client_type != ''
        ) {
          // this.$toast.removeAll()
          this.$toast.error(this.$t('main.Complete the information to complete the registration'))
          return;
        }
        if (this.accept == false) {
          // this.$toast.removeAll()
          this.$toast.error(this.$t('main.Confirm terms and conditions'))
          return;
        }

        //// this.$toast.removeAll()
        this.loading_register = true
        var formData = new FormData();
        formData.set('phone', this.phone);
        formData.set('username', this.username);
        formData.set('email', this.email);
        formData.set('country_id', this.country_id);
        formData.set('region_id', this.region_id);
        formData.set('state_id', this.state_id);
        formData.set('client_type', this.client_type);
        formData.set('commercial_no', this.commercial_no);
        formData.set('commercial_end_date', this.commercial_end_date);
        formData.set('tax_number', this.tax_number);
        if (this.photo != '') {
          formData.append('photo', this.$refs['photo'].files[0]);
        }
        if (this.commercial_id != '') {
          formData.append('commercial_id', this.$refs['photo'].files[0]);
        }
        let res = await axios.post('/register', formData);
        if (res.status == 200) {
          if (res.data.status == 400 || res.data.status == 402) {
            this.$toast.error(res.data.message); this.loading_register = false

          } else {
            this.$toast.success(res.data.message)
            // this.activation=false
            // this.register_form=true
            // this.loading_activation=false
            this.$root.$emit('registerDone', res.data.message)

            window.location = '/addresses'
          }

        } else {

        }

      } catch (res) {
        console.log(res)
        this.loading = false
        this.$toast.error(res.data.message);                  // this.handleError(this.fetchColor);
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

.mx-input {
  min-height: initial !important;
  height: initial !important;
  font-size: 17px;
  border-radius: 0;
  padding-right: 36px !important;
}

.profile-pic-con {
  background: #0000001F;
  width: 100px;
  height: 100px;
  border-radius: 50%;
  overflow: hidden;
}

.profile-pic-con img.placeholder {
  width: 20px;
  height: 20px;
}

.profile-pic-con img:not(.placeholder) {
  width: 100%;
  height: 100%;
}
</style>
