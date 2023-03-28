<template>
  <div class="modal sign-in" id="sign-in" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header border-0 p-6">
          <nav class="w-100">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <a class="nav-link active" data-toggle="tab" href="#sign-in" role="tab" aria-controls="sign-in"
                aria-selected="true">{{$t('main.Sign In')}}</a>
            </div>
          </nav>
          <button type="button" class="close opacity-10 fs-32 pt-1 position-absolute" ref="sign-in" data-dismiss="modal"
            aria-label="Close" style="left: 30px" @click="closeModel">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body px-9 pb-8">
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="sign-in">
              <h4 class="fs-34 text-center mb-6">{{$t('main.Sign In')}}</h4>
              <form>
                <input name="phone" v-model="phone" type="number" class="form-control border-0 mb-3"
                  :placeholder="$t('main.Phone') " required
                  oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                  maxlength="10" min="000000001" />
                <button type="button" class="btn btn-secondary btn-block bg-hover-primary border-hover-primary"
                  @click="login">
                  <span v-if="loading" disabled class="">{{$t('main.sending')}}</span>
                  <span v-else>{{$t('main.Send verification code')}}</span>
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
export default {
  data() {
    return {
      errors: [],
      lang: window.lang,
      loading: false,
      phone: "",
      phonecode: 966,
      show_modal: true,
    };
  },
  mounted() { },
  methods: {
    login: function () {
      if (this.phone == "") {
        this.$toast.error(this.$t('main.phone number required'));
        return;
      }


      if (![9,10].includes(this.phone.length)) {
        this.$toast.error(this.$t('main.phone number wrong'));
        return;
      }

      var formData = new FormData();
      this.message = "";
      formData.set("phone", this.phone);
      this.loading = true;
      axios.post("/login", formData)
        .then((res) => {
          this.loading = false;
          this.$root.$emit('LoadPhone', { 'phone': this.phone, 'phonecode': this.phonecode })
          this.$toast.success(res.data.message);
          this.$refs["sign-in"].click();
          this.$root.$emit('LoadActivationCode', { 'activation_code': res.data.activation_code});
          $("#activation").modal("show");
        })
        .catch((error) => {
          this.$toast.error(error.response.data.message);
          this.$refs["sign-in"].click();
        }).finally(() => {
        });
    },
    register: function () { },
    closeModel: function () {
      $('#sign-in').modal('hide');
    },
  },
};
</script>
