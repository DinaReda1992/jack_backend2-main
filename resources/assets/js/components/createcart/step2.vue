<template>
  <div>
    <div class="row">

      <div class=" col-md-6">
        <div class="">
          <h3>{{ $t("main.Select Address Client") }}</h3>
          <ul class="col-md-6">
            <li v-for="(address, index) in addresses">
              <div class="panel panel-default">
                <div class="panel-body">
                  <h5 class="card-title d-flex align-items-center">
                    <label class="mb-0 mx-2"><input type="radio" name="address" :value="address.id"
                        :checked="address.is_home == 1" v-model="address_id" /></label>
                    <span>{{ address.address }}</span>
                  </h5>
                  <h6 class="card-subtitle mb-2 text-muted">{{ address.details }}</h6>
                  <p class="card-text">{{ $t("main.Saudi Arabia") }} / {{ address.region.name }} / {{
                    address.state.name
                  }}</p>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
      <div class="col-md-4">
        <div>
          <h3>{{ $t("main.Client Information") }}</h3>
          <div class="panel panel-default">
            <div class="panel-body">
              <h5 class="card-title d-flex align-items-center">
                <span>{{ $t("main.Name") }} :</span>
                <span>{{ user.username }}</span>
              </h5>
              <h6 class="card-subtitle mb-2 ">
                <span>{{ $t("main.Phone") }} :</span>
                <span>{{ user.phone }}</span>
              </h6>
              <!-- <h6 class="card-subtitle mb-2 ">
                <span>العنوان :</span>
                <span>{{ user.adddress }}</span>
              </h6> -->

            </div>
          </div>
        </div>
      </div>

    </div>
    <div class="buttons-direction">
      <button class="btn btn-success" @click.prevent="back_btn">{{ $t("main.Previous") }}</button>
      <button class="btn btn-success" :disabled="loading" @click.prevent="select_address"> {{
        $t("main.Next")
      }}</button>
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
    order: {
      type: Object,
      default: []
    },
  },
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      activeItem: 'home',
      address_id: '',
      addresses: [],
      user: '',

    }
  },
  mounted() {

    this.$root.$on('updateUser', data => {
      this.addresses = data[0]
      this.user = data[1]
      if (data[0].length > 0) {
        this.address_id = data[0][0].id
      }
    });


  },
  computed: {

  },

  methods: {

    back_btn() {
      this.$root.$emit('updateTap', 'home')
    },

    select_address: async function () {
      try {
        //// this.$toast.removeAll()
        this.loading = true
        var formData = new FormData();
        formData.set('address_id', this.address_id);
        formData.set('order_id', this.order.id);
        let res = await axios.post("/" + this.lang + '/admin-panel/orders/select-address', formData);
        if (res.status == 200) {
          this.loading = false
          this.$toast.success(res.data.message)
          this.$root.$emit('updateTap', 'confirm')

        }
        else if (res.status == 202) {
          this.loading = false
          this.$toast.error(res.data.message);
        } else {
          this.loading = false
          this.$toast.error(res.data.message);
        }

      } catch (res) {
        console.log(res)
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
</style>
