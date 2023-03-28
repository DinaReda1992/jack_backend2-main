<template>
  <div class="modal activation" id="activation" tabindex="-1" aria-labelledby="activation" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header border-0 p-6">
          <nav class="w-100">
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
              <a class="nav-link active" data-toggle="tab" href="#activation" role="tab" aria-controls="activation"
                aria-selected="true">{{ $t('main.activation') }}</a>
            </div>
          </nav>
          <button type="button" class="close opacity-10 fs-32 pt-1 position-absolute" ref="activation-code"
            id="activation-code" data-dismiss="modal" aria-label="Close" style="left: 30px" @click="closeModel">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body px-9 pb-8">
          <div class="tab-content" id="nav-tabContent">
            <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="activation">
              <h4 class="fs-34 text-center mb-6">{{ $t('main.activation') }}</h4>
              <form>
                <label class="sverfspan">{{ $t('main.Enter the 4-digit code') }}</label>
                <div class="verify-section text-center pb-3" dir="ltr">
                  <input type="number" v-model="activation_code[0]" name="activation_code[]" class="verify-input"
                    maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                  <input type="number" v-model="activation_code[1]" name="activation_code[]" class="verify-input"
                    maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                  <input type="number" v-model="activation_code[2]" name="activation_code[]" class="verify-input"
                    maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                  <input type="number" v-model="activation_code[3]" name="activation_code[]" class="verify-input"
                    maxLength="1" size="1" min="0" max="9" pattern="[0-9]{1}" />
                </div>
                <button class="sendto btn btn-secondary btn-block bg-hover-primary border-hover-primary" type="submit"
                  @click.prevent="activation">
                  <span v-if="loading" disabled class="">{{ $t('main.Activation is in progress') }}</span>
                  <span v-else>{{ $t('main.Activate') }}</span>
                </button>
              </form>
              <p v-if="activation_code2">{{ activation_code2 }} </p>
              <a href="javascript:void(0);" v-on:click.prevent="login('resend')" :disabled="countDown < 300"
                class="lastspan pt-3">
                <span v-if="countDown < 300"> {{ $t("main.Didnt receive the code") }} {{ time }} </span>
                <span v-else> {{ $t('main.Resend') }}</span>
              </a>
              <br>
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
      phone: '',
      phonecode: 966,
      countDown: 60 * 5,
      activation_code: [],
      activation_code2: '',
    };
  },
  mounted() {
    this.$root.$on('LoadPhone', data => {
      this.phone = data.phone;
      this.phonecode = data.phonecode;
    });
    this.$root.$on('LoadActivationCode', data => {
      this.activation_code = [];
      this.activation_code2 = data.activation_code;
    });
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
    closeModel: function () {
      $('#activation').modal('hide');
    },

    activation: function () {
      try {
        let activation_code = this.activation_code[0] + this.activation_code[1] + this.activation_code[2] + this.activation_code[3]
        if ((this.phone == '' && activation_code != '') || activation_code == 'NaN') {
          this.$toast.error(this.$t('main.activation code required'));
          return;
        }
        this.loading = true;
        var formData = new FormData();
        formData.set('phone', this.phone);
        formData.append('phonecode', this.phonecode);
        formData.append('activation_code', parseInt(activation_code));
        axios.post('/activate_phone_number', formData)
          .then((res) => {
            if (res.status == 200) {
              if (res.data.status == 400) { //400
                this.loading = false;
                this.activation_code = [];
                this.$toast.error(res.data.message);
              } else if (res.data.status == 202) {//to register form 202
                this.loading = false;
                this.$root.$emit('LoadPhone', { 'phone': this.phone, 'phonecode': this.phonecode });
                this.$toast.success(this.$t('main.Complete the information to complete the registration'));
                this.$refs["activation-code"].click();
                $("#register").modal("show");
              }
              else {//login successfully 200
                this.loading = false;
                this.$refs["activation-code"].click();
                this.$toast.success(this.$t('main.Login Successfully'));
                window.location = '/'
              }
            }
          })
          .catch((error) => {
          }).finally(() => {
          });
      } catch (res) {
        this.loading = false
      }

    },
    login: async function (type) {
      try {
        if (this.phone == '') {
          // this.$toast.removeAll()
          this.$toast.error(this.$t('main.phone number required'));
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
        let res = await axios.post('/' + this.lang + '/login', formData);
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
  },
};
</script>
<style>
.verify-section input {
  margin: 0 5px;
  text-align: center;
  line-height: 80px;
  font-size: 50px;
  border: solid 1px #ccc;
  box-shadow: 0 0 5px #ccc inset;
  outline: 0;
  width: 20%;
  transition: all .2s ease-in-out;
  border-radius: 0;
}
</style>