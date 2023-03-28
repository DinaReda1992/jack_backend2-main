<template>
 <div style="display:inline-block;">
   <a style="color: white;" id="addBalanceModal_btn"
      data-toggle="modal" data-target="#addBalanceModal"
      onclick="return false;" class="btn btn-primary">
     {{$t('main.Recharge')}}
   </a>
   <div class="modal fade" id="addBalanceModal" tabindex="-1" role="dialog"
        aria-labelledby="myModalLabel">
     <div class="modal-dialog" role="document">
       <div class="modal-content">
         <div class="modal-header">
           <button type="button" class="close" data-dismiss="modal"
                   aria-label="Close"><span aria-hidden="true">&times;</span>
           </button>
           <h4 class="modal-title" id="myModalLabel">{{$t('main.Choose Payment Method')}}</h4>
         </div>
         <div class="modal-body">
           <div class="row">
             <form @submit.prevent="create" method="get" action="/pharmacy-panel/add-balance">
               <div class="form-group">
                  <label>{{$t('main.Enter the amount in riyals')}}</label>
                 <input v-model="value" required type="number" class="form-control " style="width: 50%"
                        name="value" :placeholder="$t('main.Enter the amount in riyals')"
                 >


               </div>
               <div class="form-group">
                 <label for="checkbox">
                   <input required type="radio" id="checkbox"
                          name="payment_type" checked
                          value="visa">
                   {{$t('main.Visa / Master Card')}}
                 </label>
                 <label for="checkbox2">
                   <input v-model="payment_type" required type="radio" id="checkbox2"
                          name="payment_type"
                          value="mada">
                                      {{$t('main.MADA')}}
                 </label>
                 <label for="checkbox3">
                   <input v-model="payment_type" required type="radio" id="checkbox3"
                          name="payment_type"
                          value="bank">
                   {{$t('main.bank transfer')}}
                 </label>
               </div>

               <div class="form-group" style="float: left;">
                 <button type="submit" class="btn btn-primary">
                   {{$t('main.Payment')}}
                 </button>

                 <button ref="myBtn" type="button" class="btn btn-default"
                         data-dismiss="modal">
                   {{$t('main.close')}}
                 </button>
               </div>

             </form>
           </div>
         </div>
         <div class="modal-footer">
         </div>
       </div>
     </div>
   </div>
 </div>







</template>



<script>
import CxltToastr from 'cxlt-vue2-toastr'
Vue.component('multiselect', Multiselect)
import Multiselect from 'vue-multiselect'
var toastrConfigs = {
  position: 'top right',
  timeOut: 5000
}
Vue.use(CxltToastr,toastrConfigs)

export default {
  components: {
    Multiselect
  },

  props: {

    current_balance: {
      type: Number,
      default: 0
    },



  },
  data() {
    return {
      errors:[],
      lang:window.lang,
      loading:false,
      value:'',
      payment_type:'visa',

      /**/

      /**/

      url_panel:window.location.pathname.split('/')[1],


    }
  },
  mounted(){

    /* this.$root.$on('updateCart', data => {
      this.all_items=data
     });*/

  },
  computed: {

  },
  watch: {
    value: function () {
      if(this.value>100){
      }else if(this.value<1){
        return this.value=''
      }
    },

  },

  methods: {

    create:  function  () {
      if(this.payment_type=='bank'){
        window.location='/'+this.url_panel+'/add-balance/bank'
      }else{
        var w = window.open('/'+this.url_panel+'/add-balance?value='+this.value+'&payment_type='+this.payment_type,'Popup_Window','toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=700,height=600,left = 312,top = 150');
        this.target = 'Popup_Window';
        this.$refs.myBtn.click()
      }

    },

    get_balance: async function  () {
      try {
        // this.$toast.removeAll()
        this.loading=true
        let res = await axios.get('/'+this.url_panel+'/get-balance');
        if(res.status==200){
          // setInterval(this.get_balance, 5000)

        }
        else if(res.status==202){

        }else{

        }

      } catch (res) {
        console.log(res)
this.$toast.error(res.data.message);                }

    },

  },
};
</script>
<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style>
.toast-icon{
  top:10px
}
table img.product_img {
  width: 38px;
  height: 38px;
  border-radius: 7px;
  box-shadow: 0 2px 5px rgb(92 152 195 / 30%);
}
td {
  background: #fff;
  border: 1px solid #ddd!important;
}
.price-div{
  display: flex;
  align-items: center;
  min-width: 200px;

}
.input-qty{
  display: inline-block;
  height: calc(1.5em + 0.5rem + 3px);
  padding: 0.25rem 0.5rem;
  font-size: 0.975rem;
  line-height: 1.5;
  max-width: 50px;
  color: #45a787;
  font-weight: 600;
  text-align: center;
}

#users{
  position: absolute;
  top: 100%;
  right: 0;
  min-width: 100%;
  background: #fff;
  border: 1px solid #d2d6de;
  /*box-shadow: 0 3px 5px rgb(0 0 0);*/
  border-radius: 0 0 4px 4px;
  max-height: 220px;
  overflow: hidden auto;
  padding: 4px 0;
  z-index: 2;
}
#users ul{
  margin: 0;
  padding: 0;
  list-style: none;
}
#users li{
  display: block;
  padding: 4px 24px;
  cursor: pointer;
}
.rounded-circle{
  border-radius: 50%;
}

ul{
  margin: 0;
  padding: 0;
  list-style: none;
}
[dir=rtl] .multiselect__select {
  right: auto;
  left: 25px;
  top: 8px;
}
.multiselect{
  padding-right: 0;

}
.multiselect__content{
  background: #fff;
}
.multiselect{
  overflow: visible;
}
ul.summary {
  list-style: disc;
  list-style-position: inside;
}
.summary li{
  margin: 10px 0;
}
@media (max-width: 769px){
  .summery-panel{
    order: -1;
  }
  .flex-md-column{
    flex-direction: column;

  }
}
@media (min-width: 769px){
  .summery-panel div.stick{
    position: sticky;
    top: 20px;
    z-index: 998;
  }

}
[dir=rtl] .multiselect__spinner {
  right: auto;
  left: 31px;
}
.d-flex{
  display: flex!important;
}

table img.product_img {
  width: 38px;
  height: 38px;
  border-radius: 7px;
  box-shadow: 0 2px 5px rgb(92 152 195 / 30%);
}
td {
  background: #fff;
  border: 1px solid #ddd!important;
}
.price-div{
  display: flex;
  align-items: center;
  min-width: 200px;

}
.input-qty{
  display: inline-block;
  height: calc(1.5em + 0.5rem + 3px);
  padding: 0.25rem 0.5rem;
  font-size: 0.975rem;
  line-height: 1.5;
  max-width: 50px;
  color: #45a787;
  font-weight: 600;
  text-align: center;
}

#users{
  position: absolute;
  top: 100%;
  right: 0;
  min-width: 100%;
  background: #fff;
  border: 1px solid #d2d6de;
  /*box-shadow: 0 3px 5px rgb(0 0 0);*/
  border-radius: 0 0 4px 4px;
  max-height: 220px;
  overflow: hidden auto;
  padding: 4px 0;
  z-index: 2;
}
#users ul{
  margin: 0;
  padding: 0;
  list-style: none;
}
#users li{
  display: block;
  padding: 4px 24px;
  cursor: pointer;
}
.rounded-circle{
  border-radius: 50%;
}
._2d71edc9 {
  height: 336px;
  overflow-y: scroll;
}

.eb7d03bb {
  padding: 20px 30px;
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  border-bottom: 1px solid #e0e0e0;
  justify-content: space-between;
}
._220ab7ca {
  position: relative;
  display: inline-block;
  width: 16px;
  height: 16px;
}
.eb7d03bb img {
  width: 50px;
  height: 50px;
  border-radius: 7px;
  box-shadow:0 2px 5px rgb(92 152 195 / 30%);
  margin: 0 30px;
}
.eb7d03bb p {
  margin-bottom: 0;
  font-size: 16px;
}
._233f655c {
  color: #45a787;
  flex-basis: 20%;
  display: inline-block;
}
.eb7d03bb p {
  margin-bottom: 0;
  font-size: 16px;
}
._81592708 {
  margin: 0 auto;
  flex-basis: 20%;
  text-align: center;
}
.eb7d03bb p {
  margin-bottom: 0;
  font-size: 16px;
}
@media (min-width: 769px){
  .modal-dialog {
    width: 54%;
    margin: 30px auto;
  }
}
.d953d938 {
  cursor: pointer;
  line-height: 16px;
  font-size: 16px;
  font-weight: 500;
  margin: 0;
  padding: 0;
}
.d953d938:not(._4ff9577b)::before {
  left: 0;
}
.d953d938::before {
  content: '';
  display: inline-block;
  width: 16px;
  height: 16px;
  background: #fff;
  border: 2px solid #c5bacb;
  /*border-radius: 50%;*/
  position: absolute;
  top: 0;
  transition: background-color 0.15s ease;
}
.d953d938:not(._4ff9577b)::after {
  left: 5px;
}
.d953d938::after {
  content: '';
  display: block;
  position: absolute;
  top: 2px;
  width: 6px;
  height: 9px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(
      45deg
  );
  opacity: 0;
  transition: opacity 0.15s ease;
}
._13c2491b {
  opacity: 0;
  width: 0;
  height: 0;
}
._13c2491b:checked+.d953d938::before {
  background: #45a787;
  border-color: #45a787;
}
.d953d938::after {
  content: '';
  display: block;
  position: absolute;
  top: 2px;
  width: 6px;
  height: 9px;
  border: solid #fff;
  border-width: 0 2px 2px 0;
  transform: rotate(
      45deg
  );
  opacity: 0;
  transition: opacity 0.15s ease;
}
._13c2491b:checked+.d953d938::after {
  opacity: 1;
}

ul{
  margin: 0;
  padding: 0;
  list-style: none;
}
[dir=rtl] .multiselect__select {
  right: auto;
  left: 25px;
  top: 8px;
}
.multiselect{
  padding-right: 0;

}
.multiselect__content{
  background: #fff;
}
.multiselect{
  overflow: visible;
}
</style>
