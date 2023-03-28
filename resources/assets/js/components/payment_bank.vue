<template>
  <form @submit.prevent="payment">
    <h1>فاتورة الدفع</h1>
    <div style="display: flex;justify-content: space-between;" class="form-group">
      <label>قيمة الدفع</label>
      <div style="display: inline-block" class="amount-placeholder">
        <span>SAR</span>
        <span>{{ order.final_price }}</span>
      </div>
    </div>
    <hr>
    <div class="form-group">
      <label for="expire_kind">البنك المحول منه</label>
      <select v-model="from_bank_id" class=" form-control" id="" name="expire_kind">
        <option value="" disabled="" selected hidden>اختر البنك</option>
        <option v-for="(bank, index) in banks" :value="bank.id">{{ bank.name }}</option>
      </select>
    </div>
    <!--          <div class="form-group">
            <label for="name">اسم صاحب الحساب</label>
            <input id="name" v-model="account_name" class="form-control" type="text" maxlength="255">
          </div>
          <div class="form-group">
            <label for="number_account">رقم الحساب المحول منه</label>
            <input id="number_account" v-model="account_number" class="form-control" type="text">
          </div>-->
    <div class="form-group">
      <label for="money">المبلغ المحول</label>
      <input id="money" v-model="money_transfered" class="form-control" type="text">
    </div>
    <div class="form-group">
      <label for="expire_kind">البنك المحول إليه</label>
      <select @change="setBank()" v-model="bank_id" class=" form-control" id="expire_kind" name="expire_kind">
        <option value="" disabled="" selected hidden>اختر البنك</option>
        <option v-for="(bank, index) in appbanks" :value="bank.id">{{ bank.account_name }}</option>
      </select>
    </div>
    <div class="form-group" v-if="bank_details != null">
      <table class="table text-dark table-bordered table-striped ">
        <tbody>
          <tr>
            <td>اسم البنك</td>
            <td>{{ bank_details.bank_name }}</td>
          </tr>
          <tr>
            <td>اسم الحساب</td>
            <td>{{ bank_details.account_name }}</td>
          </tr>
          <tr>
            <td>رقم الحساب</td>
            <td>{{ bank_details.account_number }}</td>
          </tr>
          <tr>
            <td>الأيبان</td>
            <td>{{ bank_details.account_ipan }}</td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="form-group">
      <label for="name"> صورة التحويل البنكي</label>
      <div class="col-ting">
        <div class="control-group file-upload form-control" id="file-upload1">
          <div class="image-box text-center">
            <p> اضافة صورة او ملف</p>

            <img v-if="photo != '' && file_type != 'pdf'" :src="photo" class="placeholder">
            <span v-if="photo != '' && file_type == 'pdf'">تم رفع الملف</span>
          </div>
          <div class="controls" style="display: none;">
            <input ref="photo" v-on:change="onImageChange($event, 'photo')" type="file" name="contact_image_1" />
          </div>
        </div>
      </div>
    </div>
    <div class="card-button">
      <button type="submit" :disabled="loading" style="width: 100%" id="PayButton"
        class="btn btn-block btn-success submit-button">
        <span class="align-middle">دفع SAR {{ order.final_price }}</span>
      </button>
    </div>
  </form>
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
    order: {
      type: Object,
      default: ""
    },
    banks: {
      type: Array,
      default: ""
    },
    appbanks: {
      type: Array,
      default: ""
    },


  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,

      account_name: '',
      account_number: '',
      bank_id: '',
      from_bank_id: '',
      money_transfered: '',
      photo: '',
      file_type: '',
      bank_details: null
    }
  },
  mounted() {


  },


  methods: {
    /**/

    setBank() {
      let bank = this.bank_id
      const index = this.appbanks.findIndex(item => item.id == bank);
      this.bank_details = this.appbanks[index]
    },
    onImageChange(e, name) {
      let files = e.target.files || e.dataTransfer.files;
      if (!files.length) {
        return;
      }
      if (files[0].type == "application/pdf") {
        this.file_type = 'pdf'
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
    /**/
    payment: async function () {
      try {
        if (this.photo == '') {
          this.$toast.error(
            'من فضلك قم برفع صورة او ملف التحويل'
          )
          return;
        }
        if (this.from_bank_id == '' || this.bank_id == '' || this.money_transfered == '') {
          this.$toast.error(
            'من فضلك قم باستكمال البيانات'
          )
          return;
        }
        this.loading = true
        /*
       account_name:'',
      account_number:'',
      bank_id:'',
      money_transfered:'',
      photo:'',
        * */
        var formData = new FormData();
        formData.set('account_name', this.account_name);
        formData.set('account_number', this.account_number);
        formData.set('bank_id', this.bank_id);
        formData.set('money_transfered', this.money_transfered);
        formData.set('order_id', this.order.id);
        formData.set('from_bank_id', this.from_bank_id);
        if (this.photo != '') {
          formData.append('photo', this.$refs['photo'].files[0]);
        }
        let res = await axios.post('/payment-bank', formData);
        if (res.status == 200) {
          this.loading = false
          this.$toast.success(res.data.message)
          setTimeout(window.location = '/my-orders', 5000)

          // this.all_addresses=res.data.addresses

        } else {
          this.loading = false
          this.$toast.error(res.data.message);
        }

      } catch (res) {
        this.loading = false
        console.log(res)

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
