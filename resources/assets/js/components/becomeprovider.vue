<template>
  <div class="register-section">
    <div class=" ">
      <form @submit.prevent="create">
        <div class="d-flex align-items-center flex-column justify-content-center">

          <div class="col-md-6 w-sm-100 mb-2">
            <label for="phone"> <b>الجوال</b></label>
            <input type="text" v-model="phone" id="phone" name="phone" required class="form-control"
              placeholder="الجوال ">
          </div>
          <div class="col-md-6 w-sm-100 mb-2">
            <label for="name"> <b>الاسم</b></label>
            <input type="text" v-model="name" id="name" name="name" required class="form-control"
              placeholder="الاسم بالكامل">
          </div>

          <div class="col-md-6 w-sm-100 mb-2">
            <label for="country_id"> <b>الدولة</b></label>
            <select @change="onChangeSelect($event, 'country')" v-model="country_id" name="country_id" id="country_id"
              required>
              <option value="" disabled="">اختر الدولة</option>
              <option :value="item.id" v-for="(item, index) in countries" :key="index">
                {{ item.name }}
              </option>
            </select>

          </div>
          <div class="col-md-6 w-sm-100 mb-2">
            <label for="region_id"> <b>المنطقة</b></label>
            <select @change="onChangeSelect($event, 'region')" v-model="region_id" name="region_id" id="region_id"
              required>
              <option value="">اختر المنطقة</option>
              <option :value="item.id" v-for="(item, index) in regions" :key="index">
                {{ item.name }}
              </option>
            </select>

          </div>

          <div class="col-md-6 w-sm-100 mb-2">
            <label for="state_id"> <b>المدينة</b></label>
            <!--                js-example-basic-single-->
            <select v-model="state_id" class="" name="state_id" id="state_id" required>
              <option value="">اختر المدينة</option>
              <option :value="item.id" v-for="(item, index) in states" :key="index">
                {{ item.name }}
              </option>
            </select>

          </div>
          <div class="card-button">
            <button type="submit" :disable="loading_register" style="width: 100%" id="PayButton"
              class="btn btn-block btn-success submit-button">
              <span class="align-middle">ارسال</span>
            </button>
          </div>
        </div>

      </form>
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
    countries: {
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
      message: '',
      phonecode: '966',

      regions: [],
      states: [],

      phone: '',//123456789999999
      name: '',
      country_id: '',
      region_id: '',
      state_id: '',
    }
  },
  mounted() {


  },


  methods: {
    /**/


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

    /**/
    create: async function () {
      try {
        if (this.phone == '' &&
          this.name != '' &&
          this.country_id != '' &&
          this.region_id != '' &&
          this.state_id != ''
        ) {
          // this.$toast.removeAll()
          this.$toast.error(
            'من فضلك قم بإستكمال كل البيانات'
          )
          return;
        }
        //// this.$toast.removeAll()
        this.loading_register = true
        var formData = new FormData();
        formData.set('phone', this.phone);
        formData.set('name', this.name);
        formData.set('country_id', this.country_id);
        formData.set('region_id', this.region_id);
        formData.set('state_id', this.state_id);
        let res = await axios.post('/become-provider', formData);
        if (res.status == 200) {
          if (res.data.status == 400 || res.data.status == 402) {
            this.$toast.error(res.data.message); this.loading_register = false

          } else {
            this.$toast.success(res.data.message)
            this.loading_register = false
            this.phone = ''
            this.username = ''
            this.country_id = ''
            this.region_id = ''
            this.state_id = ''
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

.is_fav {
  color: #d18332 !important;
}
</style>
