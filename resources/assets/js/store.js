
export default {

    state: {
        serviceItems: {},
        searchItems: {},
        cartItems: [],
        type: '',
        value: '',
        pageName: 'login',
        activation_code: '',
        password: '',
        // user:{
        //     id:0,
        //     approved:0
        // },
        user:typeof getUser !== 'undefined' ?getUser:{
            id:0,
            approved:0
        }

    },

    getters: {

        getCategoryFormGetters(state){ //take parameter state
            return state.category
        },
        getTypeFormGetters(state){ //take parameter state

            return state.type
        } ,
        getPageNameFormGetters(state){ //take parameter state
            return state.pageName
        },
    },

    actions: {
        goPage(context,data){
            context.commit("page_name",data) //
        },
        EditItem(context,data){
            // console.log(this.state.searchItems)
           // return;
            let id=data.id
            // console.log('id',id)
            if(this.state.searchItems.data){
                let index = this.state.searchItems.data.findIndex(item => item.id == id);
                // console.log('index',index)

                let searchItems=this.state.searchItems
                searchItems.data[index]=data
                context.commit("searchItems",searchItems) //
            }

        },
        EditCartItem(context,data){
            let id=data.id
            let index = this.state.cartItems.data.findIndex(item => item.id == id);
            let searchItems=this.state.cartItems
            searchItems[index]=data
            context.commit("cartItems",searchItems) //
        },
        RemoveCartItem(context,data){
            let id=data.id
            let index = this.state.cartItems.data.findIndex(item => item.id == id);

            let searchItems=this.state.cartItems
            searchItems.splice(index, 1);

            context.commit("cartItems",searchItems) //
        },

        fav: async function  (context,data) {
            try {
                if(this.state.user.id==0){
                    vm.$toast.warn({
                        title:'قم بتسجيل الدخول أولا'
                    })
                    return;
                }
                var formData = new FormData();
                if(data.is_liked==0){
                    formData.set('type', 'like');
                }else{
                    formData.set('type', 'unlike');
                }
                formData.set('item_id', data.id);
                let res = await axios.post('/like-product',formData);
                if(res.status==200){

                    if(data.is_liked==1){
                        data.is_liked=0
                    }else{
                        data.is_liked=1
                    }
                    // this.$store.dispatch("EditItem",data)

                    this.$toast.success({
                        title:res.data.message
                    })
                    // this.all_addresses=res.data.addresses

                }else{
                    this.$toast.error({
                        title:res.data.message
                    })
                }

            } catch (res) {
                console.log(res)
                this.$toast.error({
                    title:res.data.message
                })
            }

        },

    },

    mutations: {
        categories(state,data) {
            return state.category = data
        } ,
        type(state,data) {
            return state.type = data
        },
        value(state,data) {
            return state.value = data
        },
        pageName(state,data) {
            return state.pageName = data
        },
        activation_code(state,data) {
            return state.activation_code = data
        },
        searchItems(state,data) {
            return state.searchItems = data
        },
        serviceItems(state,data) {
            return state.serviceItems = data
        },
    }
}
