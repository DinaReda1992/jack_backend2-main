<template>
  <div :class="{ 'read-only': loading_all }">
    <div class="row flex">

      <div v-if="!confirmed" class=" col-md-8">

        <div class="">
          <div class="">
            <div class="d-flex ">
              <h3>{{$t('main.recharge_the_balance_by_bank_transfer')}}</h3>
            </div>

          </div>
          <ul>
            <li >
              <div class="panel panel-default">
                <div class="panel-body">
                  <h5 class="card-title d-flex align-items-center">
                  </h5>
                  <div>
                    <div class="form-group">
                      <label>{{$t('main.Choose the bank to transfer from')}}</label>
                      <div class="form-group">
                        <select v-model="from_bank_id"  class="form-control">
                          <option v-for="(bank,index) in from_banks" :value="bank.id">
                            {{bank.bank_name + ' - ' +bank.account_name+ ' - ' +bank.account_number}}
                          </option>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>{{$t('main.Choose the bank to transfer to')}}</label>
                      <div class="form-group">
                        <select v-model="bank_id" @change="getBankDetails" class="form-control">
                          <option v-for="(bank,index) in banks" :value="bank.id">{{bank.bank_name}}</option>
                        </select>
                      </div>
                      <div class="table-responsive" v-if="bank_details!=''">
                        <table class="table text-dark table-bordered ">
                          <thead>
                          <tr>
                            <th>{{$t('main.bank name')}}</th>
                            <th>{{$t('main.account name')}}</th>
                            <th>{{$t('main.account number')}}</th>
                            <th>{{$t('main.iban number')}}</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr>
                            <td>{{bank_details.bank_name}}</td>
                            <td>{{bank_details.account_name}}</td>
                            <td>{{bank_details.account_number}}</td>
                            <td>{{bank_details.account_ipan}}</td>
                          </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                    <div class="form-group">
                      <label>{{$t('main.Amount')}}</label>
                      <div class="form-group">
                        <input class="form-control" v-model="money_transfered" />
                      </div>
                    </div>
                    <div class="form-group"  >
                      <label> {{$t('main.bank transfer picture')}}</label>
                      <div class="col-ting">
                        <div class="control-group file-upload text-center" id="file-upload1">
                          <label class="image-box text-center d-block border">
                            <p> {{$t('main.Add a picture or file')}}</p>
                            <img  v-if="photo==''" src="/images/upload_image.svg" class="">
                            <img v-if="photo!='' && file_type!='pdf'" :src="photo"  class="placeholder">
                            <span v-if="photo!='' && file_type=='pdf'">{{$t('main.file uploaded')}}</span>
                            <input style="display: none" ref="photo" v-on:change="onImageChange($event,'photo')" type="file" name="contact_image_1"/>
                          </label>
                          <a v-if="transfer_photo!=''" :href="transfer_photo">{{$t('main.See the receipt')}}</a>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </li>

          </ul>
        </div>



        <div class="text-right">
          <button class="btn btn-success" :disabled="loading" @click.prevent="confirm_order">{{$t('main.send request')}}</button>
        </div>
      </div>
      <div v-else>
        <div  class="alert alert-success text-center">{{$t('main.The request has been sent successfully')}}</div>
      </div>
    </div>

  </div>
</template>



<script>
import CxltToastr from 'cxlt-vue2-toastr'

var toastrConfigs = {
  position: 'top right',
  timeOut: 5000
}
Vue.use(CxltToastr,toastrConfigs)

export default {
  components: {
  },

  props: {
    banks: {
      type: Array,
      default: []
    },
    from_banks: {
      type: Array,
      default: []
    },


    balance: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      errors:[],
      lang:window.lang,
      loading:false,
      loading_all:false,
      confirmed:false,
      payment_id:'',
      user:'',
      file_type:'',
      photo:'',
      photo2:'',
      short_code:'',
      transfer_photo:'',
      from_bank_id:'',
      bank_id:'',
      money_transfered:'',
      bank_details:'',
      url_panel:window.location.pathname.split('/')[1],
    }
  },
  mounted(){
     console.log(this.lang);
  },
  computed: {

  },

  methods: {
    onImageChange(e,name) {
      let files = e.target.files || e.dataTransfer.files;
      if (!files.length){
        return;
      }
      if(files[0].type == "application/pdf"){
        this.file_type='pdf'
      }else {
        this.file_type=''
      }
      this.createImage(files[0],name);
    },
    createImage(file,name) {
      let reader = new FileReader();
      let vm = this;
      reader.onload = (e) => {
        vm[name] = e.target.result;
      };
      reader.readAsDataURL(file);
    },
    select_payment_method() {

    },
    getBankDetails() {
      let bank_id=this.bank_id
       var __FOUND = this.banks.find(function(type, index) {
           if(type.id == bank_id)
               return true;
       });
      console.log(__FOUND)
      this.bank_details=__FOUND
    },

    confirm_order: async function  (confirm=true) {
      try {
        // this.$toast.removeAll()
        if(this.photo=='' && this.transfer_photo==''){
          this.$toast.error(
            'قم برفع إيصال الدفع'
          )
          return ;
        }

        if( this.money_transfered=='' || this.from_bank_id=='' || this.bank_id==''){
          this.$toast.error(
            'قم باستكمال بيانات الدفع'
          )
          return ;
        }
        this.loading=true
        this.loading_all=true
        var formData = new FormData();

        formData.append('from_bank_id', this.from_bank_id);
        formData.append('bank_id', this.bank_id);
        formData.append('money_transfered', this.money_transfered);
        formData.append('photo', this.$refs['photo'].files[0]);

        let res = await axios.post('/'+this.url_panel+'/add-balance/bank',formData);
        if(res.status==200){
          if(res.data.status==200){
            this.loading=false
            this.loading_all=false
            this.confirmed=true

            this.$toast.success(res.data.message)
          }else{
            this.loading=false
            this.loading_all=false
this.$toast.error(res.data.message);                    }


        }
        else if(res.status==202){
          this.loading=false
          this.loading_all=false
 this.$toast.error(res.data.message);        }else{
          this.loading=false
          this.loading_all=false
 this.$toast.error(res.data.message);        }

      } catch (res) {
        console.log(res)
this.$toast.error(res.data.message);                }

    },





  },
};
</script>
<style>

ul{
  margin: 0;
  padding: 0;
  list-style: none;
}
.buttons-direction{
  display: flex;
  justify-content:space-between;
}
.d-block{
  display: block;
  padding: 10px;
}
.border{
  border:1px solid
}
.placeholder{
  width: 100%;
}
.read-only{
  opacity: .5;
  pointer-events: none;
}
.d-flex{
  display: flex;
  align-items: center;
}
</style>
