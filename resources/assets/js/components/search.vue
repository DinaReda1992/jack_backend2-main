<template>
  <div class="row">
    <div class="col-md-3 col-sm-4 col-12">
      <div class="services-side">
        <form @submit.prevent="search_method(1,'first_search')">
          <div class="services-head border-bottom-0 mb-0">
<!--            <h4>
              بحث المدينة
              <i class="la la-minus"></i>
            </h4>
            <select class="form-control">
              <option>الرياض</option>
              <option>جدة</option>
              <option>مكه</option>
              <option>المدينة</option>
            </select>-->
            <div class="form-group search">
              <input type="text" v-model="search" class="form-control mb-0" placeholder="ادخل كلمة البحث هنا">
              <i class="la la-search"></i>
            </div>

          </div>

          <div class="services-head" >
            <h4>
              الأقسام
              <i class="la la-minus"></i>
            </h4>
            <div style="height: 300px;overflow: overlay">
              <div v-for="(category , index) in categories" class="checkbox" >
                <label class="f-17">
                  <input type="checkbox" :value="category.id" v-model="category_id"> {{ category.name }}
                </label>
              </div>
            </div>




          </div>
          <div class="my-3 text-center">
            <button type="submit" class="send px-3 py-2 d-block w-100 " :disabled="loading">بحث</button>
          </div>
        </form>
      </div>
      <div class="clear-fix"></div>
      <div class="ad-img">
        <a href="javascript:void(0)">
          <img :src="settings" alt="img" />
        </a>
      </div>
    </div>
    <div class="col-md-9 col-sm-8 col-12">
      <div class="services-con1">
        <div class="sh-slider">
          <div class="row">
            <div  v-for="(item,index) in items.data" class="col-md-4 col-sm-12 col-6">
              <div class="item">
                <div class="product-card">
                  <div class="badge" v-if="item.is_trend==1">Hot</div>
                  <div class="product-tumb">
                    <img :src="item.photo" alt="">
                  </div>
                  <div class="product-details">
                    <div class="product-top-details">
                      <h4>
                        <a :href="'/product/'+item.id">{{ item.title }}</a>
                      </h4>
                      <span  v-if="user.id!=0"  class="product-catagory">السعر  شامل الضريبة</span>
                      <div v-if="user.id!=0" style="direction: ltr;display: inline-block" class="product-price">
<!--                        <small> {{item.price}} SAR </small>-->
                        {{item.price}} SAR
                      </div>
                      <div class="product-rate">
                        <input class="rating rating-loading input-id" data-min="0" data-max="5" data-step="1" value="3" data-size="xxs" disabled>
                      </div>
                    </div>
                    <div class="product-bottom-details">
                      <actions :user="user" :item="item" :single="0"/>
<!--                      <div class="product-links">
                        <a href="javascript:void(0)"><i class="las la-share-alt"></i></a>
                        <a href="javascript:void(0)"><i class="la la-heart"></i></a>
                        <a href="javascript:void(0)"><i class="la la-shopping-cart"></i></a>
                      </div>-->
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="message!=''" class="alert alert-info text-center">
              لا يوجد نتائج
            </div>

            <div class="col-md-12 text-center mt-2 mt-md-5 d-flex justify-content-center">
              <nav aria-label="Page navigation example">
                <pagination :limit="1" :show-disabled="true" :data="items" @pagination-change-page="search_method"></pagination>
              </nav>
            </div>

          </div>
        </div>
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
    sort: {
      type: String,
      default: ""
    },
    settings: {
      type: String,
      default: ""
    },
    categories: {
      type: Array,
      default: ""
    },
    user: {
      type: Object,
      default: () => ({
        id: 0,
        activate: 0,
      }),
    },


  },
  data() {
    return {
      errors:[],
      lang:window.lang,
      loading:false,
      message:'',
      all_products:[],
      search:'',
      category_id:[],
      items: {},
      sort_pram:this.sort
    }
  },
  mounted(){
    this.initSearch()


  },


  methods: {
    /**/

    initSearch(){
      let current_url=window.location.href
      if(current_url.indexOf('?')!=-1){
        if(current_url.indexOf('search')!=-1){
          let url = new URL(current_url);
          let val = url.searchParams.get("search");
          this.search=val
        }

        if(current_url.indexOf('category_id')!=-1){
            let url = new URL(current_url);
            let val = url.searchParams.get("category_id");
            val=JSON.parse(val)
            this.category_id=val
        }

        this.search_method(1,'first_search');

      }else{
        this.search_method(1,'first_search');
      }

    },

    search_method: async function (page,index=null) {
      try {

        this.message=''
        this.loading=true
        this.items= {}

        if (typeof page === 'undefined') {
          page = 1;
        }
        let params ={
          page:page
        };

        if(this.category_id.length!=0){
          params.category_id=JSON.stringify(this.category_id)
        }
        if(this.search!=''){
          params.search=this.search
        }
        params.ajax=1
        this.all_params=params

        let res = await axios.get('/search', {params});
        if(res.status == 200){

          this.loading=false
          // history.replaceState(null, null, this.res.data.url); // replace the existing URL without history
          history.pushState({}, null, '?'+res.data.url);
          // $('html,body').stop().animate({
          //   scrollTop: 350
          // }, 'slow', 'swing');
          if(res.data.status==200){
            this.items=res.data.data
            if(res.data.data.data.length==0){
              this.message='no data'
            }
            // window.scrollTo(0,0);
          }else {
            this.items={}
            this.message=res.data.message
          }


        }
      } catch (res) {
        console.log(res)
        // this.handleError(this.fetchColor);
      }
    },



  },
};
</script>
<style>
.toast-icon{
  top:initial!important;
}
body{
  overflow-x: hidden;
}
.is_fav{
  color:#d18332!important;
}
.f-17{
  font-size: 17px;
}
</style>
