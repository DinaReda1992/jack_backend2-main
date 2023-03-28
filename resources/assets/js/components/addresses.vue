<template>
  <div class="container">
    <a href="/cart" v-if="all_addresses.length > 0" class="btn btn-lg fs-18 btn-secondary btn-block h-60 bg-hover-primary border-0">{{ $t('main.complete_order') }}</a>
    <div class="row " style="margin-top: 20px;">

      <div class="col-md-6">
        <div class="">
          <form @submit.prevent="store_address" id="addAddress" class="col-md-8 border p-4">
            <div class="form-group">
              <label>{{$t('main.Street name')}}</label>
              <input type="text" v-model="add_address.address" class="form-control">
            </div>
            <div class="form-group">
              <label>{{$t('main.Details')}}</label>
              <input type="text" v-model="add_address.details" class="form-control">
            </div>
            <div class="form-group mb-3 ">
              <label for="region_id"> <b>{{$t('main.Region')}}</b></label>
              <select @change="onChangeSelect('region')" class="form-control" v-model="add_address.region_id"
                name="region_id" id="region_id" required>
                <option value="">{{$t('main.select Region')}}</option>
                <option :value="item.id" v-for="(item, index) in regions" :key="index">
                  {{ item.name }}
                </option>
              </select>

            </div>

            <div class="form-group mb-3 ">
              <label for="state_id"> <b>{{$t('main.City')}}</b></label>
              <!--                js-example-basic-single-->
              <select v-model="add_address.state_id" class="form-control" name="state_id" id="state_id" required>
                <option value="">{{$t('main.select City')}}</option>
                <option :value="item.id" v-for="(item, index) in states" :key="index">
                  {{ item.name }}
                </option>
              </select>

            </div>
            <div class="form-group">
              <label>{{$t('main.Phone number 1')}}</label>
              <input type="text" v-model="add_address.phone1" class="form-control">
            </div>
            <div class="form-group">
              <label>{{$t('main.Phone number 2')}}</label>
              <input type="text" v-model="add_address.phone2" class="form-control">
            </div>
            <div class="form-group mb-2">
              <label>{{$t('main.E-mail Address')}}</label>
              <input type="email" v-model="add_address.email" class="form-control">
            </div>
            <div class="form-group mb-2">
              <label>{{$t('main.Search on map')}}</label>
              <div class="d-flex justify-content-between align-items-center p-0  form-control">
                <gmap-autocomplete :placeholder="$t('main.Search on map')" class="form-control w-100" @place_changed="setPlace">
                </gmap-autocomplete>
                <div><a href="javascript:void(0)" @click.prevent="usePlace"><i class="las la-search"></i></a></div>
              </div>
            </div>

            <div class="position-relative">

              <GmapMap :center="initMarker" :zoom="15" map-type-id="terrain" style="width: 100%; height: 400px"
                :options="{ fields: [] }">
                <Gmap-Marker ref="myMarker" :position="marker" :clickable="true" :draggable="true"
                  @drag="updateCoordinates"></Gmap-Marker>

              </GmapMap>
            </div>
            <button type="submit" class="btn btn-primary mt-2 w-100" :disabled="loading">{{$t('main.save')}}</button>
          </form>
        </div>
      </div>


      <div class="col-md-6 border-right">
        <div v-if="all_addresses.length > 0">
          <label>{{$t('main.Addressess')}}</label>
          <div v-for="(item, index) in all_addresses">
            <div class="border d-flex align-items-center position-relative mt-2 p-3">
              <div class="position-absolute toggle-drop">
                <a class="btn dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <img src="/images/options.svg" class="options">
                </a>
                <div class="dropdown-menu">
                  <a class="dropdown-item" href="javascript:void(0)" @click.prevent="edit_address(item)">{{$t('main.Edit')}}</a>
                  <a class="dropdown-item" href="javascript:void(0)" @click.prevent="delete_address(item.id)">{{$t('main.Delete')}}</a>
                </div>
              </div>
              <input type="radio" name="is_home[]" :id="'radioHome' + item.id" :checked="item.is_home == 1"
                @change="is_home(item.id)">
              <div class="px-3">
                <h5 class="mb-0">{{ item.address }}</h5>
                <p class="mb-0 f-15" v-if="item.details != ''">{{ item.details }}</p>
              </div>

            </div>
            <div v-if="updated_address.id == item.id">
              <form @submit.prevent="update_address()" id="EditAddress" class="border editform p-4">
                <div class="form-group">
                  <label>{{$t('main.Address')}}</label>
                  <input type="text" v-model="updated_address.address" class="form-control">
                </div>
                <div class="form-group">
                  <label>{{$t('main.Details')}}</label>
                  <input type="text" v-model="updated_address.details" class="form-control">
                </div>

                <div class="form-group mb-3">
                  <label for="region_id"> <b>{{$t('main.Region')}}</b></label>
                  <select @change="update_onChangeSelect($event, 'region')" class="form-control"
                    v-model="updated_address.region_id" name="region_id" required>
                    <option value="">{{$t('main.select Region')}}</option>
                    <option :value="item.id" v-for="(item, index) in update_regions" :key="index">
                      {{ item.name }}
                    </option>
                  </select>

                </div>

                <div class="form-group mb-3">
                  <label for="state_id"> <b>{{$t('main.City')}}</b></label>
                  <!--                js-example-basic-single-->
                  <select v-model="updated_address.state_id" class="form-control" name="state_id" required>
                    <option value="">{{$t('main.select City')}}</option>
                    <option :value="item.id" v-for="(item, index) in update_states" :key="index">
                      {{ item.name }}
                    </option>
                  </select>

                </div>
                <div class="form-group">
                  <label>{{$t('main.Phone number 1')}}</label>
                  <input type="text" v-model="updated_address.phone1" class="form-control">
                </div>
                <div class="form-group">
                  <label>{{$t('main.Phone number 2')}}</label>
                  <input type="text" v-model="updated_address.phone2" class="form-control">
                </div>
                <div class="form-group mb-2">
                  <label>{{$t('main.E-mail Address')}}</label>
                  <input type="email" v-model="updated_address.email" class="form-control">
                </div>


                <div class="position-relative">

                  <GmapMap :center="{
                    lat: parseFloat(updated_address.lat),
                    lng: parseFloat(updated_address.lng),
                  }" :zoom="15" map-type-id="terrain" style="width: 100%; height: 300px" :options="{ fields: [] }">
                    <Gmap-Marker ref="myMarker" :position="{
                      lat: parseFloat(updated_address.latitude),
                      lng: parseFloat(updated_address.longitude),
                    }" :clickable="true" :draggable="true" @drag="updateCoordinates2"></Gmap-Marker>

                  </GmapMap>
                </div>
                <div class="d-flex mt-2 justify-content-between">
                  <button type="submit" class="btn btn-primary  " :disabled="loading_update">{{$t('main.save')}}</button>
                  <button @click="clearAddress" class="btn btn-danger mt-2 ">{{$t('main.close')}}</button>
                </div>
              </form>

            </div>

          </div>
        </div>
        <div class="alert alert-info" v-else>
         {{$t('main.Add at least one address')}}
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
Vue.use(CxltToastr, toastrConfigs)
import * as VueGoogleMaps from 'vue2-google-maps'
Vue.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyA2fQwBCKX5iz-Cj5JznOvPB_v9w68s-Ls',
    libraries: 'places', // This is required if you use the Autocomplete plugin
    v: 3.38
  },
  installComponents: true
})

export default {
  components: {

  },

  props: {
    addresses: {
      type: Array,
      default: ""
    },
    user: {
      type: Object,
      default: ""
    },
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
      loading_update: false,
      all_addresses: this.addresses,
      regions: [],
      states: [],
      update_regions: [],
      update_states: [],
      add_address: {
        address: '',
        details: '',
        latitude: 24.774265,
        longitude: 46.738586,
        is_home: 0,
        // country_id:'',
        region_id: '',
        state_id: '',
        phone1: '',
        phone2: '',
        email: '',
      },
      updated_address: {
        id: '',
        address: '',
        details: '',
        latitude: '',
        longitude: '',
        lat: '',
        lng: '',
        is_home: '',
        // country_id:'',
        region_id: '',
        state_id: '',
        phone1: '',
        phone2: '',
        email: '',
      },
      marker: {
        lat: 24.774265,
        lng: 46.738586,
      },
      initMarker: {
        lat: 24.774265,
        lng: 46.738586,
      },

      country_id: this.user.country_id,
      region_id: '',
      state_id: '',
      place: null,

    }
  },
  created() {
    this.onChangeSelect('country')
    this.geolocation();

  },
  mounted() {
    this.$root.$on('registerDone', data => {
      this.$toast.success({
        data
      })
    });

  },
  methods: {
    setPlace(place) {
      this.place = place
      this.usePlace(place)
    },
    usePlace(place) {
      if (this.place) {
        let marker = {
          lat: this.place.geometry.location.lat(),
          lng: this.place.geometry.location.lng(),
        }
        this.marker = marker
        this.initMarker = marker
        this.add_address.latitude = marker.lat
        this.add_address.longitude = marker.lng
        this.place = null;
      }
    },
    geolocation: function (init = true) {
      navigator.geolocation.getCurrentPosition((position) => {
        this.marker = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        this.initMarker = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        this.add_address.latitude = position.coords.latitude
        this.add_address.longitude = position.coords.longitude
      });
    },
    onChangeSelect(type) {
      if (type == 'country') {
        let country_id = this.country_id
        var __FOUND = this.countries.find(function (item, index) {
          if (item.id == country_id)
            return true;
        });
        console.log(__FOUND)
        this.regions = __FOUND.get_regions
      } else {
        let region_id = this.add_address.region_id
        var __FOUND = this.regions.find(function (item, index) {
          if (item.id == region_id)
            return true;
        });
        this.states = __FOUND.get_states
      }
    },
    update_onChangeSelect(type) {
      if (type == 'country') {
        let country_id = this.country_id
        var __FOUND = this.countries.find(function (item, index) {
          if (item.id == country_id)
            return true;
        });
        console.log(__FOUND)
        this.update_regions = __FOUND.get_regions
      } else {
        let region_id = this.updated_address.region_id
        var __FOUND = this.update_regions.find(function (item, index) {
          if (item.id == region_id)
            return true;
        });
        this.update_states = __FOUND.get_states
      }
    },
    updateCoordinates(location) {
      let marker = {
        lat: location.latLng.lat(),
        lng: location.latLng.lng(),
      }
      this.marker = marker
      this.add_address.latitude = marker.lat
      this.add_address.longitude = marker.lng
    },
    updateCoordinates2(location) {
      let marker = {
        lat: location.latLng.lat(),
        lng: location.latLng.lng(),
      }
      this.updated_address.latitude = marker.lat
      this.updated_address.longitude = marker.lng
    },
    edit_address(address) {
      this.updated_address = {
        id: address.id,
        address: address.address,
        details: address.details,
        latitude: address.latitude,
        longitude: address.longitude,
        lat: address.lat,
        lng: address.lng,
        is_home: address.is_home,
        // country_id:address.country_id,
        region_id: address.region_id,
        state_id: address.state_id,
        phone1: address.phone1,
        phone2: address.phone2,
        email: address.email,
      }
      this.update_onChangeSelect('country')
      this.update_onChangeSelect('region')

    },
    clearAddress() {
      this.updated_address = {
        id: '',
        address: '',
        details: '',
        latitude: '',
        longitude: '',
        lat: '',
        lng: '',
        is_home: '',
        country_id: '',
        region_id: '',
        state_id: '',
        phone1: '',
        phone2: '',
        email: '',
      }
    },
    store_address: async function () {
      try {
        if (this.add_address.address == '' || this.add_address.latitude == '' || this.add_address.phone1 == '' || this.add_address.state_id == '') {
          // this.$toast.removeAll()
          this.$toast.error('اكمل البيانات');
          return;
        }
        this.loading = true
        var formData = new FormData();

        formData.set('address', this.add_address.address);
        formData.set('details', this.add_address.details);
        formData.set('latitude', this.add_address.latitude);
        formData.set('longitude', this.add_address.longitude);
        // formData.set('country_id', this.add_address.country_id);
        formData.set('region_id', this.add_address.region_id);
        formData.set('state_id', this.add_address.state_id);
        formData.set('phone1', this.add_address.phone1);
        formData.set('phone2', this.add_address.phone2);
        if (this.add_address.email != '') {
          formData.set('email', this.add_address.email);
        }
        let res = await axios.post('/' + this.lang + '/addresses/store', formData);
        if (res.status == 200) {
          this.loading = false
          this.$toast.success(res.data.message)
          this.all_addresses = res.data.addresses
          this.add_address = {
            address: '',
            details: '',
            is_home: 0,
          }
        } else {
          this.loading = false
          this.$toast.error(res.data.message);
        }

      } catch (res) {
        console.log(res)
        this.loading = false
        this.$toast.error(res.data.message);
      }

    },
    update_address: async function () {
      try {

        if (this.updated_address.address == '' ||
          this.updated_address.latitude == '' ||
          this.updated_address.phone1 == '' ||
          this.updated_address.state_id == '') {

          // this.$toast.removeAll()
          this.$toast.error('اكمل البيانات');
          return;
        }
        this.loading_update = true
        var formData = new FormData();

        formData.set('address', this.updated_address.address);
        formData.set('details', this.updated_address.details);
        formData.set('latitude', this.updated_address.latitude);
        formData.set('longitude', this.updated_address.longitude);
        // formData.set('country_id', this.updated_address.country_id);
        formData.set('region_id', this.updated_address.region_id);
        formData.set('state_id', this.updated_address.state_id);
        formData.set('phone1', this.updated_address.phone1);
        formData.set('phone2', this.updated_address.phone2);
        if (this.add_address.email != '') {
          formData.set('email', this.updated_address.email);
        }
        let res = await axios.post('/' + this.lang + '/addresses/update/' + this.updated_address.id, formData);
        if (res.status == 200) {
          this.loading_update = false
          this.$toast.success(res.data.message);
          this.all_addresses = res.data.addresses

        } else {
          this.loading_update = false
          this.$toast.error(res.data.message);
        }

      } catch (res) {
        console.log(res)
        this.loading_update = false
        this.$toast.error(res.data.message);
      }

    },

    delete_address: async function (address) {
      try {
        if (this.all_addresses.length == 1) {
          this.$toast.error(this.$t('main.There must be at least one address'))
          return;
        }
        var r = confirm(this.$t('main.Are you sure to delete the address?'))

        if (r == true) {
          // this.$toast.removeAll()
          this.loading = true
          let params = {
            id: address,
          };
          let res = await axios.post('/' + this.lang + '/addresses/delete', params);
          if (res.status == 200) {
            this.loading = false
            this.all_addresses = res.data.addresses
            this.$toast.success(res.data.message)
          } else {
            this.loading = false
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
    is_home: async function (id) {
      try {

        var r = confirm(this.$t('main.Are you sure to change this address to home?'))
        if (r == true) {
          // this.$toast.removeAll()
          this.loading = true
          let params = {
            id: id,
          };
          let res = await axios.post('/' + this.lang + '/addresses/home', params);
          if (res.status == 200) {
            this.all_addresses = res.data.addresses
            this.$toast.success(res.data.message)
          } else {
            this.loading = false
            let errors = res.data.errors
            var i;
            for (i = 0; i < errors.length; i++) {
              this.$toast.error(errors[i]);
            }
          }

        } else {
          $('#radioHome' + id).prop('checked', false)
          /*var __FOUND = this.all_addresses.find(function(item, index) {
            if(item.id == id)
              return true;
          });
          __FOUND.is_home=0
          this.all_addresses=__FOUND*/
          // console.log(__FOUND)
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

.toggle-drop {
  left: 0;
}

.options {
  width: 15px;
  transform: rotate(90deg);
}

#addAddress {
  background: #0000000A 0 0 no-repeat padding-box;
  border-radius: 10px;
}

.editform {
  background: #0000000A 0 0 no-repeat padding-box;
  border-radius: 10px;
}

.pac-target-input {
  width: 100%;
}

.cart-page input:active,
.login-section input:focus {
  box-shadow: none !important;
  border: none !important;
}
</style>
