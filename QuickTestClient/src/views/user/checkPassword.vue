<template>
<!-- lock toggle view -->
    <v-container class="px-0">
        <Message/>
        <v-overlay :value="isUnlock" :opacity="opacity">
            <v-form ref="form">
                <v-container class="text-center">
                    <v-row no-gutters>
                        <v-col cols="12"><img :src=getImg></v-col>
                    </v-row>
                    <v-row no-gutters>
                        <v-col cols="12"><v-text-field :rules="[rules.required]" v-model="password" label="Type password" outlined></v-text-field></v-col>
                    </v-row>
                    <v-row no-gutters>
                        <v-col cols="12"><v-btn outlined @click="cancelOverlay"><v-icon x-large>vpn_key</v-icon></v-btn></v-col>
                    </v-row>
                </v-container>         
            </v-form>
        </v-overlay>
    </v-container>
</template>

<script>
import Message from '../../components/common/Message'
import config from '../../config'

export default {
    name:'CheckPassword',
    components:{
        Message
    },
    data(){
        return{
            opacity:0.95,
            password:'',
            rules: {
                required: value => !!value || 'Required.'
            }
        }
    },
    methods:{
        //switch to app after given password
        cancelOverlay(){
            if(this.$refs.form.validate()){
                const objPassword={
                    hashedPassword:this.$store.getters['dashboard/userInfo'].password,
                    password:this.password
                }
                this.$store.dispatch('user/unLockUser',objPassword)
                    .then(response=>{
                        if(response.status==200){
                            this.$store.dispatch('dashboard/clearOverlay')
                            this.password=''
                        }else{
                            this.$root.$emit('message_from_parent','Wrong password!')
                        }
                    })
                    .catch(err=>{
                        console.log(err)
                    })
            }
        }
    },
    computed:{
        //get lock status
        isUnlock:function(){
            return this.$store.getters['dashboard/overlay']
        },
        //get user image
        getImg:function(){
            return this.$store.getters['dashboard/userInfo'].imagePath==null?'':config.hostname+this.$store.getters['dashboard/userInfo'].imagePath
        },
    }

}
</script>