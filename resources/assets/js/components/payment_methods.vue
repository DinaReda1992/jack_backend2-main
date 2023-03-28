<template>
    <section class="payment_method text-center">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="title">
                        <h3 class="section-title pb-4">{{ $t('main.Payment Method') }}</h3>
                    </div>
                </div>
                <div class="col-4" style="text-align: right;padding: 20px;">
                    <div class="form-check" v-if="!with_balance"><input type="checkbox" name="is_schedul"
                            v-model="is_schedul" id="is_schedul" class="form-check-input">
                        <label for="is_schedul" class="form-check-label">
                            {{ $t('main.Do you want to schedule the Order ?') }}
                        </label>
                    </div>
                    <input type="date" name="scheduling_date" class="form-control" style="margin-top: 10px;"
                        v-model="scheduling_date" v-if="is_schedul" :min="new Date().toISOString().substr(0, 10)" id="">
                    <hr>
                    <div class="form-check" v-if="!is_schedul && balance > 0"><input type="checkbox" name="with_balance"
                            v-model="with_balance" id="with_balance" class="form-check-input">
                        <label for="with_balance" class="form-check-label">
                            {{ $t('main.Do you want to use partial balance ?') }}
                        </label>
                    </div>

                </div>
                <div class="col-8">
                </div>
                <!-- <div class="col-md-6 col-sm-6 mb-3">
                    <a href="javascript:void(0)" :class="{ 'disabled': loading_payment || loading_balance || loading_hand_delivery }"
                        @click.prevent="addOrder('payment')">
                        <div class="card payment-card">
                            <div class="card-block text-center" id="electorinc">
                                <i class="las la-credit-card fa-3x"></i>
                                <p class="card-title" style="color: white;">{{ $t('main.E-Payment') }}</p>
                                <span v-if="loading_payment" class="spinner spinner-border"></span>
                            </div>
                        </div>
                    </a>
                </div> -->
                <div class="col-md-4 col-sm-6 mb-3">
                    <a href="javascript:void(0)"
                        :class="{ 'disabled': loading_bank || loading_balance || loading_hand_delivery }"
                        @click.prevent="addOrder('bank')">
                        <div class="card payment-card">
                            <div class="card-block text-center" id="bank">
                                <i class="las la-credit-card fa-3x"></i>
                                <p class="card-title" style="color: white;">{{$t('main.Bank Transfer')}}</p>
                                <span v-if="loading_bank" class="spinner spinner-border"></span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 col-sm-6 mb-3" v-if="payment_balance">
                    <a href="javascript:void(0)"
                        :class="{ 'disabled': balance == 0 || loading_payment || loading_balance || loading_hand_delivery || is_schedul || with_balance }"
                        @click.prevent="addOrder('balance')">
                        <div class="card payment-card">
                            <div class="card-block text-center" id="cashmyBalance">
                                <i class="las la-wallet fa-3x"></i>
                                <p class="card-title" style="color: white;">
                                    {{ $t('main.My balance') }} ( {{ balance.toFixed(2) }} {{ $t('main.SAR') }} )
                                </p>
                                <span v-if="loading_balance" class="spinner spinner-border"></span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 col-sm-6 mb-3" v-if="payment_hand_delivery">
                    <a href="javascript:void(0)"
                        :class="{ 'disabled': loading_payment || loading_balance || loading_hand_delivery }"
                        @click.prevent="addOrder('hand_delivery')">
                        <div class="card payment-card">
                            <div class="card-block text-center" id="cash">
                                <i class="las la-money-bill fa-3x"></i>
                                <p class="card-title" style="color: white;">{{ $t('main.Cash on Deleivery') }}</p>
                                <p v-if="hand_delivery_cost > 0" class="">{{ $t('main.add amount') }}
                                    {{ hand_delivery_cost }} {{ $t('main.SAR') }}</p>
                                <span v-if="loading_hand_delivery" class="spinner spinner-border"></span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            <!--End Row-->
        </div>


        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">

                <div class="modal-content">
                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-4 col-sm-4 mb-3">
                                <a href="javascript:void(0)"
                                    :class="{ 'disabled': loading_mada || loading_visa || loading_tabby }"
                                    @click.prevent="payment('mada')">
                                    <div class="card payment-card">
                                        <div class="card-block">
                                            <!--                      <i class="las la-wallet fa-3x"></i>-->
                                            <img src="/images/mada.svg" class="card-style" width="50">
                                            <h4 class="card-title">
                                                {{ $t('main.MADA') }}
                                            </h4>
                                            <span v-if="loading_mada" class="spinner spinner-border"></span>

                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-md-4 col-sm-4 mb-3">
                                <a href="javascript:void(0)"
                                    :class="{ 'disabled': loading_mada || loading_visa || loading_tabby }"
                                    @click.prevent="payment('visa')">
                                    <div class="card payment-card">
                                        <div class="card-block">
                                            <img src="/images/mc_symbol.svg" class="card-style" width="50">
                                            <img src="/images/visa.svg" class="card-style" width="50">
                                            <!--                      <i class="las la-money-bill fa-3x"></i>-->
                                            <h4 class="card-title">
                                                {{ $t('main.Visa / Master Card') }}

                                            </h4>
                                            <span v-if="loading_visa" class="spinner spinner-border"></span>

                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-md-4 col-sm-4 mb-3">
                                <a href="javascript:void(0)"
                                    :class="{ 'disabled': loading_mada || loading_visa || loading_tabby }"
                                    @click.prevent="paymentTabby('tabby')">
                                    <div class="card payment-card">
                                        <div class="card-block">
                                            <img src="/images/tabby.png" class="card-style" width="50">
                                            <!--                                            <h4 class="card-title">-->
                                            <!--                                                {{$t('main.TABBY')}}-->
                                            <!--                                            </h4>-->
                                            <p>{{ $t('main.4 interest-free payments') }}</p>
                                            <span v-if="loading_tabby" class="spinner spinner-border"></span>

                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        <div class="modal fade" id="myModalBank" tabindex="-1" role="dialog" aria-labelledby="myModalBank">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <form @submit.prevent="paymentBank" class="row">
                            <h4>{{$t('main.Bank Transfer')}}</h4>
                            <div class="form-group col-6">
                                <label for="expire_kind">{{$t('main.Choose the bank to transfer from')}}</label>
                                <select v-model="from_bank_id" class=" form-control" id="" name="expire_kind">
                                    <option value="" disabled="" selected hidden>{{$t('main.Choose the bank to transfer from')}}</option>
                                    <option v-for="(bank, index) in banks" :value="bank.id">{{ bank.name }}</option>
                                </select>
                            </div>
                            <div class="form-group col-6">
                                <label for="money">{{$t('main.Transferred amount')}}</label>
                                <input id="money" v-model="money_transfered" class="form-control" type="text">
                            </div>
                            <div class="form-group">
                                <label for="expire_kind">{{$t('main.Choose the bank to transfer to')}}</label>
                                <select @change="setBank()" v-model="bank_id" class=" form-control" id="expire_kind"
                                    name="expire_kind">
                                    <option value="" disabled="" selected hidden>{{$t('main.Choose the bank to transfer to')}}</option>
                                    <option v-for="(bank, index) in appbanks" :value="bank.id">{{ bank.account_name }}
                                    </option>
                                </select>
                            </div>
                            <div class="form-group" v-if="bank_details != null">
                                <table class="table text-dark table-bordered table-striped ">
                                    <tbody>
                                        <tr>
                                            <td>{{$t('main.bank name')}}</td>
                                            <td>{{ bank_details.bank_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{$t('main.account name')}}</td>
                                            <td>{{ bank_details.account_name }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{$t('main.account number')}}</td>
                                            <td>{{ bank_details.account_number }}</td>
                                        </tr>
                                        <tr>
                                            <td>{{$t('main.iban number')}}</td>
                                            <td>{{ bank_details.account_ipan }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group">
                                <label for="name"> {{$t('main.bank transfer picture')}}</label>
                                <div class="col-ting">
                                    <div class="control-group file-upload form-control" id="file-upload1"
                                        style="height: 300px;" @click="uploadPhoto">
                                        <div class="image-box text-center">
                                            <p> {{$t('main.Add a picture or file')}}</p>

                                            <img v-if="photo != '' && file_type != 'pdf'" :src="photo"
                                                style="height: 200px;">
                                            <span v-if="photo != '' && file_type == 'pdf'">{{$t('main.file uploaded')}}</span>
                                        </div>
                                        <div class="controls" style="display: none;">
                                            <input ref="photo" v-on:change="onImageChange($event, 'photo')" type="file"
                                                name="contact_image_1" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-button">
                                <button type="submit" :disabled="loading_bank" style="width: 100%" id="PayButton"
                                    class="btn btn-block btn-success submit-button">
                                    <span class="align-middle">{{$t('main.Payment')}}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


</template>


<script>
import CxltToastr from 'cxlt-vue2-toastr'

var toastrConfigs = {
    position: 'top right',
    timeOut: 5000
}
Vue.use(CxltToastr, toastrConfigs)

export default {
    components: {},

    props: {
        order: {
            type: Object,
            default: () => {
            }
        },
        balance: {
            type: Number,
            default: 0
        },

        hand_delivery_cost: {
            type: Number,
            default: 0
        },
        banks: {
            type: Array,
            default: ""
        },
        appbanks: {
            type: Array,
            default: ""
        },
        payment_hand_delivery:{
            type:Number,
            default:0,
        },
        payment_balance:{
            type:Number,
            default:0,
        }
    },
    data() {
        return {
            errors: [],
            gifts: [],
            get_gifts: [],
            lang: window.lang,
            loading_payment: false,
            loading_hand_delivery: false,
            loading_balance: false,
            loading_bank: false,
            loading_mada: false,
            loading_visa: false,
            loading_tabby: false,
            user: this.$store.state.user,
            code: localStorage.getItem('code'),
            shipmentsCount: localStorage.getItem('shipments'),
            account_name: '',
            account_number: '',
            bank_id: '',
            from_bank_id: '',
            money_transfered: '',
            photo: '',
            file_type: '',
            is_schedul: false,
            scheduling_date: '',
            with_balance: false,
            bank_details: null,
        }
    },
    computed: {},
    mounted() {


    },

    methods: {
        uploadPhoto() {
            this.$refs['photo'].click();
        },

        send_order: async function (type) {
            this['loading_' + type] = true
            var formData = new FormData();
            if (this.is_schedul) {
                formData.set('is_schedul', this.is_schedul ? 1 : 0);
                formData.set('scheduling_date', this.scheduling_date);
            }
            if (this.with_balance) {
                formData.set('with_balance', this.with_balance ? 1 : 0);
            }
            formData.set('address_id', this.order.address_id);
            formData.set('order_id', this.order.id);
            formData.set('payment_type', type);
            if (localStorage.getItem('code') != '') {
                formData.set('code', localStorage.getItem('code'));
            }
            let res = await axios.post('/'+this.lang+'/send-order', formData);
            if (res.status == 200) {

                if (res.data.status == 200) {
                    this.$toast.success(res.data.message)
                    setTimeout(function () {
                        window.location = '/'+this.lang+'/thank-you?id=' + res.data.order_id
                    }, 300);
                } else {
                    this['loading_' + type] = false
                    this.$toast.error(res.data.message)
                }
                // this.all_addresses=res.data.addresses
            } else {
                this[type] = false

            }

        },
        addOrder: async function (type) {
            console.log(this.$store.state);
            try {
                // if (this.user.id == 0) {
                //     this.$toast.warning(this.$t('main.login first'))
                //     return;
                // }
                if (this.is_schedul && this.scheduling_date == '') {
                    this.$toast.error(this.$t('main.Scheduling date must be added'));
                    return;
                }

                if (type == 'payment') {
                    $('#myModal').modal('show')
                    // window.location='/payment/'+this.order.id
                    return;
                }

                if (type == 'bank') {
                    $('#myModalBank').modal('show')
                    return;
                }
                this.$confirm(
                    {
                        message: this.$t('main.Are you sure?'),
                        button: {
                            no: this.$t('main.No'),
                            yes: this.$t('main.Yes'),
                        },
                        /**
                         * Callback Function
                         * @param {Boolean} confirm
                         */
                        callback: confirm => {
                            if (confirm) {
                                this.send_order(type)
                            }
                        }
                    }
                )

            } catch (res) {
                console.log(res);
            }

        },
        payment: async function (type) {
            try {
                if (this.user.id == 0) {
                    this.$toast.warn({
                        title: this.$t('main.login first')
                    })
                    return;
                }
                let order_id = this.order.id
                this['loading_' + type] = true
                var formData = new FormData();
                if (this.is_schedul) {
                    formData.set('is_schedul', this.is_schedul ? 1 : 0);
                    formData.set('scheduling_date', this.scheduling_date);
                }
                if (this.with_balance) {
                    formData.set('with_balance', this.with_balance ? 1 : 0);
                }
                formData.set('address_id', this.order.address_id);
                formData.set('order_id', this.order.id);
                formData.set('type', type);
                if (localStorage.getItem('code') != '') {
                    formData.set('code', localStorage.getItem('code'));
                }
                let res = await axios.post('/'+this.lang+'/checkout', formData);
                if (res.status == 200) {
                    if (res.data.status == 200) {
                        this.$toast.success({
                            title: res.data.message
                        })
                        localStorage.setItem('total_cost', res.data.total_cost)
                        setTimeout(function () {
                            window.location = '/'+this.lang+'/checkout-' + type + '/' + res.data.checkoutId + '?code=' + localStorage.getItem('code') + '&order_id=' + order_id
                        }, 300);

                    } else {
                        this['loading_' + type] = false
                        this.$toast.error({
                            title: res.data.message
                        })
                    }
                    // this.all_addresses=res.data.addresses
                } else {
                    this['loading_' + type] = false

                }

            } catch (res) {
                console.log(res)
                this.$toast.error({
                    title: res.data.message
                })
            }
        },
        paymentTabby: async function (type) {
            try {
                if (this.user.id == 0) {
                    this.$toast.warn({
                        title: this.$t('main.login first')
                    })
                    return;
                }
                let order_id = this.order.id
                this['loading_' + type] = true
                var formData = new FormData();
                formData.set('order_id', this.order.id);
                formData.set('address_id', this.order.address_id);
                formData.set('type', type);
                if (localStorage.getItem('code') != '') {
                    formData.set('code', localStorage.getItem('code'));
                }
                let res = await axios.post('/'+this.lang+'/checkout-tabby', formData);
                if (res.status == 200) {
                    if (res.data.status == 200) {
                        this.$toast.success({
                            title: res.data.message
                        })
                        setTimeout(function () {
                            window.location = res.data.data.url;
                        }, 200);

                    } else {
                        this['loading_' + type] = false
                        this.$toast.error({
                            title: res.data.message
                        })
                    }
                    // this.all_addresses=res.data.addresses
                } else {
                    this['loading_' + type] = false

                }

            } catch (res) {
                console.log(res)
                this.$toast.error({
                    title: res.data.message
                })
            }
        },
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
        paymentBank: async function () {
            try {
                if (this.photo == '') {
                    this.$toast.error(
                        this.$t('main.Upload the payment receipt')
                    )
                    return;
                }
                if (this.from_bank_id == '' || this.bank_id == '' || this.money_transfered == '') {
                    this.$toast.error(
                        this.$t('main.Please complete the data')
                    )
                    return;
                }
                this.loading_bank = true;

                var formData = new FormData();
                if (this.is_schedul) {
                    formData.set('is_schedul', this.is_schedul ? 1 : 0);
                    formData.set('scheduling_date', this.scheduling_date);
                }
                if (this.with_balance) {
                    formData.set('with_balance', this.with_balance ? 1 : 0);
                }
                formData.set('address_id', this.order.address_id);
                formData.set('account_name', this.account_name);
                formData.set('account_number', this.account_number);
                formData.set('bank_id', this.bank_id);
                formData.set('money_transfered', this.money_transfered);
                formData.set('payment_type', 'bank');
                formData.set('order_id', this.order.id);
                formData.set('from_bank_id', this.from_bank_id);
                if (this.photo != '') {
                    formData.append('photo', this.$refs['photo'].files[0]);
                }
                let res = await axios.post('/'+this.lang+'/send-order', formData);
                if (res.status == 200) {
                    this.loading_bank = false
                    this.$toast.success(res.data.message)
                    setTimeout(function () {
                        window.location = '/'+this.lang+'/thank-you?id=' + res.data.order_id
                    }, 300);
                } else {
                    this.loading_bank = false
                    this.$toast.error(res.data.message);
                }

            } catch (res) {
                this.loading_bank = false
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

.payment_method {
    padding-bottom: 3em;
    background-size: cover;
}

.is_fav {
    color: #d18332 !important;
}

a.disabled {
    pointer-events: none;
    color: #ccc;
}

.card-style {
    background: #fff;
    border-radius: 31px;
}

.payment_method .payment-card {
    background: #4e0161;
    border: 0;
    text-align: center;
    color: #fff;
    min-height: 256px;
    border-radius: 16px;
    color: #fff;
}

.payment_method .card {
    transition: .3s;
}

.card,
.prepare_appointments .days-list .day-appointments .appointment-time .form-group input {
    border-radius: 10px;
}

.card {
    overflow: hidden;
}

.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid rgba(0, 0, 0, .125);
    border-radius: 0.25rem;
}

.payment_method .card-block {
    padding: 4rem 0;
    text-align: center;
    color: #fff;
}

.la,
.las {
    font-family: 'Line Awesome Free';
    font-weight: 900;
}

.la,
.las,
.lar,
.lal,
.lad,
.lab {
    -moz-osx-font-smoothing: grayscale;
    -webkit-font-smoothing: antialiased;
    display: inline-block;
    font-style: normal;
    font-variant: normal;
    text-rendering: auto;
    line-height: 1;
}

.payment-card p {
    margin-top: 0;
    margin-bottom: 0.5rem;
    font-weight: 500;
    line-height: 1.2;
    font-size: 23px;
}

.payment_method .payment-card:hover {
    background: var(--yellow);
    color: #000 !important;
    box-shadow: 2px 4px 8px 0 rgb(46 61 73 / 20%);
}

.payment_method .payment-card {
    background: #4e0161;
    border: 0;
    text-align: center;
    min-height: 256px;
    border-radius: 16px;
    color: #fff !important;
}

h4 {
    color: #fff !important;
}

i {
    color: #fff !important;
}

.card-title,
.card-text {
    text-align: center;
}

.bank-payment .file-upload:hover {
    background: rgba(3, 3, 3, .3);
}

.bank-payment .file-upload {
    cursor: pointer;
    transition: .3s;
    border-radius: 0;
    background: rgba(211, 211, 211, .16);
}

.bank-payment img {
    width: 100%;
}

img,
svg {
    vertical-align: middle;
}

.form-check {
    margin: 10px 0;
    position: relative;
}

.form-check {
    display: block;
    min-height: 1.5rem;
    padding-left: 0;
    padding-right: 1.5em;
    margin-bottom: 0.125rem;
    cursor: pointer;
}

.form-check .form-check-input {
    width: 20px;
    height: 20px;
    margin-left: 10px;
    cursor: pointer;
}

.form-check .form-check-input {
    float: right;
    margin-left: auto;
    margin-right: -1.5em;
}

.form-check-input[type=checkbox] {
    border-radius: 0.25em;
}

.form-check .form-check-input {
    float: left;
    margin-left: -1.5em;
}

input[type=checkbox],
input[type=radio] {
    box-sizing: border-box;
    padding: 0;
}

.form-check-input:checked {
    background-color: #46eb57;
    border-color: #46eb57;
}

.form-check label {
    cursor: pointer;
}

.form-check label {
    color: #000;
    font-size: 14px;
    font-weight: 600;
}

.form-check-label {
    margin-right: 10px;
}
</style>
