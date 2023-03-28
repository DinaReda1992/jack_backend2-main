<template>
    <div class="modal register" id="register" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header border-0 p-6">
                    <nav class="w-100">
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <a class="nav-link active" data-toggle="tab" href="#register" role="tab"
                                aria-controls="register" aria-selected="true">{{ $t('main.Create Account') }}</a>
                        </div>
                    </nav>
                    <button type="button" class="close opacity-10 fs-32 pt-1 position-absolute" ref="register"
                        data-dismiss="modal" aria-label="Close" style="left: 30px" @click="closeModel">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-9 pb-8">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="register">
                            <h4 class="fs-34 text-center mb-6">{{ $t('main.Create Account') }}</h4>
                            <form method="post" @submit.prevent="register" action="/register">
                                <div class="email-login">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-group py-3 ">
                                                <label for="profile-pic" class="d-flex align-items-center">
                                                    <div
                                                        class="d-flex align-items-center justify-content-center profile-pic-con">
                                                        <template>
                                                            <img v-if="photo == ''" src="/images/upload__image.png"
                                                                class="photo edit-img-one">
                                                            <img v-else :src="photo" class="photo">
                                                        </template>

                                                    </div>
                                                    <div class="px-3">
                                                        <span class="d-block text-dark f-14">{{ $t('main.personal image')
                                                        }}</span>
                                                        <span class="text-muted  f-14">{{ $t('main.Upload a clear personal photo of yourself') }}</span>
                                                    </div>
                                                </label>
                                                <input id="profile-pic" accept="image/png, image/jpeg" ref="photo"
                                                    v-on:change="onImageChange($event, 'photo')" class="sr-only"
                                                    type="file">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <div class="form-group py-3 ">
                                                <label for="commercial_id" class="d-flex align-items-center">
                                                    <div
                                                        class="d-flex align-items-center justify-content-center profile-pic-con">
                                                        <template>
                                                            <img v-if="commercial_id == ''" src="/images/upload__image.png"
                                                                class="photo edit-img-two">
                                                            <img v-else :src="commercial_id" class="photo">
                                                        </template>

                                                    </div>
                                                    <div class="px-3">
                                                        <span class="d-block text-dark f-14">{{ $t('main.commercial_file')
                                                        }}</span>
                                                        <span class="text-muted  f-14">{{ $t('main.Add a picture or file')
                                                        }} </span>
                                                    </div>
                                                </label>
                                                <input id="commercial_id" name="commercial_id"
                                                    accept="image/png, image/jpeg,pdf" ref="commercial_id"
                                                    v-on:change="onImageChange($event, 'commercial_id')" class="sr-only"
                                                    type="file">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 ">
                                            <label for="username"> <b>{{ $t('main.Name') }}</b></label>
                                            <input type="text" v-model="username" id="username" name="username" required
                                                class="form-control" :placeholder="$t('main.Name')">
                                        </div>
                                        <div class="col-12 col-md-6 ">
                                            <label for="email"> <b>{{ $t('main.Email') }}</b></label>
                                            <input v-model="email" type="email" id="email" class="form-control"
                                                :placeholder="$t('main.Email')" name="email">
                                        </div>
                                        <div class="col-12 col-md-4 " style="display: none">
                                            <label for="country_id"> <b>{{ $t('main.Select Country') }}</b></label>
                                            <select class="form-control" @change="onChangeSelect($event, 'country')"
                                                v-model="country_id" name="country_id" id="country_id" required>
                                                <option value="" disabled="">{{ $t('main.Select Country') }}</option>
                                                <option :value="item.id" v-for="(item, index) in countries" :key="index">
                                                    {{ item.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 ">
                                            <label for="region_id"> <b>{{ $t('main.select Region') }}</b></label>
                                            <select class="form-control" @change="onChangeSelect($event, 'region')"
                                                v-model="region_id" name="region_id" id="region_id" required>
                                                <option value="">{{ $t('main.select Region') }}</option>
                                                <option :value="item.id" v-for="(item, index) in regions" :key="index">
                                                    {{ item.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 ">
                                            <label for="state_id"> <b>{{ $t('main.select City') }}</b></label>
                                            <select class="form-control" v-model="state_id" name="state_id" id="state_id"
                                                required>
                                                <option value="">{{ $t('main.select City') }}</option>
                                                <option :value="item.id" v-for="(item, index) in states" :key="index">
                                                    {{ item.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 ">
                                            <label for="client_type"> <b>{{ $t('main.activity type') }}</b></label>
                                            <select v-model="client_type" id="client_type" class="form-control"
                                                name="client_type" required>
                                                <option value="" disabled="" selected>{{ $t('main.Select activity type') }}
                                                </option>
                                                <option :value="item.id" v-for="(item, index) in clienttypes" :key="index">
                                                    {{ item.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6 ">
                                            <label for="commercial_no"> <b>{{ $t('main.Commercial Registration No')}}</b></label>
                                            <input v-model="commercial_no" type="number" id="commercial_no"
                                                class="form-control" :placeholder="$t('main.Commercial Registration No')"
                                                name="commercial_no">

                                        </div>
                                        <div class="col-12 col-md-6 ">
                                            <label for="commercial_no"> <b>{{ $t('main.Tax Number') }}</b></label>
                                            <input v-model="tax_number" type="number" id="tax_number" class="form-control"
                                                :placeholder="$t('main.Tax Number')" name="tax_number">

                                        </div>

                                        <div class="col-12 col-md-6 ">
                                            <label for=""> <b>{{ $t('main.Registration end date') }}</b></label>
                                            <date-picker v-model="commercial_end_date" format="YYYY-MM-DD"
                                                value-type="YYYY-MM-DD" type="date"
                                                :placeholder="$t('main.Registration end date')" class="w-100"></date-picker>
                                        </div>
                                        <div class="py-3 col-md-12 border-bottom pb-2">
                                            <p class="ml-2 pr-3 d-flex align-items-center">
                                                <input id="chacked" style="width:20px;height:20px" type="checkbox"
                                                    :value="true" v-model="accept" name="radio-group">
                                                <label for="chacked" class="f-12 NeoMediumArabic mb-0 pr-3">
                                                    <a target="_blank" :href="'/' + lang + '/page/conditions'">
                                                        {{ $t('main.I declare that I have read and agree to the mentioned terms')}}
                                                    </a>
                                                </label>
                                            </p>
                                        </div>


                                        <div class="col-12 text-center">
                                            <button :disabled="loading" class="btn btn-primary">
                                                <span v-if="loading" class="">{{ $t('main.Submission In Progress') }}</span>
                                                <span v-else>{{ $t('main.Register') }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
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
    props: {
        countries: {
            type: Array,
            default: ""
        },
        clienttypes: {
            type: Array,
            default: ""
        },
    },
    data() {
        return {
            errors: [],
            lang: window.lang,
            loading: false,
            phone: "",
            phonecode: 966,
            country_id: 188,
            regions: [],
            states: [],
            photo: '',
            username: '',
            email: '',
            country_id: 188,
            region_id: '',
            state_id: '',
            client_type: '',
            commercial_no: '',
            commercial_end_date: '',
            commercial_id: '',
            tax_number: '',
            accept: false
        };
    },
    mounted() {
        this.$root.$on('LoadPhone', data => {
            this.username = '';
        this.email = '';
        this.region_id = '';
        this.state_id = '';
        this.client_type = '';
        this.commercial_no = '';
        this.commercial_end_date = '';
        this.commercial_id = '';
        this.tax_number = '';
            this.phone = data.phone;
            this.phonecode = data.phonecode;
        });
        let country_id = this.country_id
        var __FOUND = this.countries.find(function (item, index) {
            if (item.id == country_id)
                return true;
        });
        this.regions = __FOUND.get_regions
    },
    methods: {
        register: async function () {
            try {
                if (this.phone == '' &&
                    this.activation_code != '' &&
                    this.username != '' &&
                    this.country_id != '' &&
                    this.region_id != '' &&
                    this.state_id != '' &&
                    this.client_type != ''
                ) {
                    this.$toast.error(this.$t('main.Complete the information to complete the registration'));
                    return;
                }
                if (this.accept == false) {
                    this.$toast.error(this.$t('main.Confirm terms and conditions'));
                    return;
                }
                this.loading = true
                var formData = new FormData();
                formData.set('phone', this.phone);
                formData.set('username', this.username);
                formData.set('email', this.email);
                formData.set('country_id', this.country_id);
                formData.set('region_id', this.region_id);
                formData.set('state_id', this.state_id);
                formData.set('client_type', this.client_type);
                formData.set('commercial_no', this.commercial_no);
                formData.set('commercial_end_date', this.commercial_end_date);
                formData.set('tax_number', this.tax_number);
                if (this.photo != '') {
                    formData.append('photo', this.$refs['photo'].files[0]);
                }
                if (this.commercial_id != '') {
                    formData.append('commercial_id', this.$refs['photo'].files[0]);
                }
                let res = await axios.post('/register', formData);
                if (res.status == 200) {
                    if (res.data.status == 400 || res.data.status == 402) {
                        this.$toast.error(res.data.message);
                        this.loading = false;
                    } else {
                        this.$toast.success(res.data.message);
                        this.$refs["register"].click();
                        // this.$root.$emit('registerDone', res.data.message);
                        // window.location = '/addresses';
                    }
                }
            } catch (res) {
                this.loading = false;
                this.$toast.error(res.data.message);
            }
        },
        onImageChange(e, name) {
            let files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
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
        closeModel: function () {
            $('#register').modal('hide');
        },
    },

};
</script>
<style>.photo {
    width: 100px;
    height: 100px;
    border-radius: 56px;
}</style>