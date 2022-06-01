<template>
<!-- app sign in -->
    <div class="login-page">  
        <Message/>    
        <v-card elevation="10" class="login-box" shaped>
            <!-- logo and tile start -->
            <v-row v-if="this.logoImg!=''" justify="center" class="pt-2"><v-img :src="this.logoImg" max-height="50" max-width="100" contain></v-img></v-row>
            <v-card-text class="text-center">
                <h2 class="black--text">{{this.title}}</h2>
                <p class="grey--text">{{this.description}}</p>         
            </v-card-text>
            <!-- logo and tile end -->

            <!-- sign in form start -->
            <v-card-text>
                <v-form ref="form">
                    <v-text-field 
                        label="Email" 
                        v-model="emailLogin" 
                        append-icon="email"
                        :rules="[rules.required,rules.emailRules]"
                    >
                    </v-text-field>
                    <v-text-field 
                        label="Password" 
                        v-model="passwordLogin" 
                        :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                        :rules="[rules.required, rules.min]"
                        :type="show ? 'text' : 'password'"                            
                        hint="At least 8 characters"                                      
                        @click:append="show = !show"
                    >
                    </v-text-field>
                    <v-spacer></v-spacer>
                    <v-btn :loading="loading" @click="submit" rounded dark>Sign In</v-btn> 
                    <div class="mt-4 text-center font-weight-bold">
                        <a class="text-decoration-none text-uppercase black--text" @click="adminCred">Admin</a> 
                        <a class="text-decoration-none text-uppercase black--text pl-6" @click="userCred">Candidate</a>
                    </div>   
                </v-form>
            </v-card-text>
            <v-card-actions>
                <form v-bind:action="stripeGetUrl" method="GET" v-if="chkPaidRegistration==true">
                    <input type="hidden" name="location" v-bind:value="getWindowLocation">
                    <v-btn :loading="payLoading" text color="black" class="text-capitalize px-0" type="submit">{{this.registrationAmountWithText}}</v-btn>
                </form>
                <v-btn :loading="payLoading" v-else text color="black" class="text-capitalize" @click="register = true">Register</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="black" class="text-capitalize" @click="forget = true">Forgot Password</v-btn>
            </v-card-actions>
            <!-- sign in form end -->

            <!-- forget password start -->
            <v-expand-transition>
                <v-card v-if="forget" class="transition-fast-in-fast-out v-card--reveal" style="height: 100%;">
                    <v-row v-if="this.logoImg!=''" justify="center" class="pt-2"><v-avatar tile><v-img :src="this.logoImg"  max-height="50" max-width="100" contain></v-img></v-avatar></v-row>
                    <v-card-text class="text-center">
                        <h2 class="black--text">Forgot Password</h2>                              
                    </v-card-text>
                    <v-card-text class="pb-0">
                        <v-form ref="formForget">
                            <v-text-field
                              v-model="emailForget"
                              label="Email"
                              :rules="[rules.required,rules.emailRules]"
                              clearable                       
                            ></v-text-field>                                                                               
                        </v-form>
                    </v-card-text>
                    <v-card-actions class="pt-0">
                        <v-btn @click="forget = false" text color="grey darken-4">Close</v-btn>
                        <v-spacer></v-spacer>
                        <v-btn @click="sendPassword" text color="grey darken-4">Sent</v-btn>                       
                    </v-card-actions>
                </v-card>
            </v-expand-transition>
            <!-- forget password end -->

            <!-- reset password start -->
            <v-expand-transition>
                <v-card v-if="reset" class="transition-fast-in-fast-out v-card--reveal" style="height: 100%;">
                    <v-row v-if="this.logoImg!=''" justify="center" class="pt-2"><v-avatar tile><v-img :src="this.logoImg"  max-height="50" max-width="100" contain></v-img></v-avatar></v-row>
                    <v-card-text class="text-center">
                        <h2 class="black--text">Reset Password</h2>                              
                    </v-card-text>
                    <v-card-text class="pb-0">
                        <v-form ref="formReset">   
                            <v-text-field
                                v-model="newPassword"
                                label="New Password"
                                :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                                :rules="[rules.required, rules.min]"
                                :type="show ? 'text' : 'password'"
                                hint="At least 8 characters"
                                @click:append="show = !show"
                                clearable
                            >
                            </v-text-field>
                            <v-text-field
                                v-model="confirmPassword"
                                label="Confirm New Password"
                                :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                                :rules="[rules.required, rules.min]"
                                :type="show ? 'text' : 'password'"
                                hint="At least 8 characters"
                                @click:append="show = !show"   
                                clearable                   
                            >
                            </v-text-field>                                                                           
                        </v-form>
                    </v-card-text>
                    <v-card-actions class="pt-0">
                        <v-btn @click="reset = false" text color="grey darken-4">Close</v-btn>
                        <v-spacer></v-spacer>
                        <v-btn @click="resetPassword" text color="grey darken-4">Reset</v-btn>                       
                    </v-card-actions>
                </v-card>
            </v-expand-transition>
            <!-- forget password end -->

            <!-- registration start -->
            <v-expand-transition>
                <v-card v-if="register" class="transition-fast-in-fast-out v-card--reveal" style="height: 100%;">
                    <v-row v-if="this.logoImg!=''" justify="center" class="pt-2"><v-avatar tile><v-img :src="this.logoImg"  max-height="50" max-width="100" contain></v-img></v-avatar></v-row>
                    <v-card-text class="text-center">
                        <h2 class="black--text">Registration</h2>                              
                    </v-card-text>
                    <v-card-text class="pb-0">
                        <v-form ref="formRegister">
                            <v-text-field 
                                label="Name" 
                                v-model="fullNameRegister"                               
                                :rules="[rules.required]"
                                clearable
                            >
                            </v-text-field>
                            <v-text-field
                              v-model="emailRegister"
                              label="Email"
                              :rules="[rules.required,rules.emailRules]"
                              clearable                       
                            ></v-text-field>
                            <v-text-field 
                                label="Password" 
                                v-model="passwordRegister" 
                                :append-icon="show ? 'mdi-eye' : 'mdi-eye-off'"
                                :rules="[rules.required, rules.min]"
                                :type="show ? 'text' : 'password'"                            
                                hint="At least 8 characters"                                      
                                @click:append="show = !show"
                            >
                            </v-text-field>                                                                                
                        </v-form>
                    </v-card-text>
                    <v-card-actions class="pt-0">
                        <v-btn @click="register = false" text color="grey darken-4">Close</v-btn>
                        <v-spacer></v-spacer>
                        <v-btn @click="registration" text color="grey darken-4">Register</v-btn>                       
                    </v-card-actions>
                </v-card>
            </v-expand-transition>
            <!-- registration end -->
        </v-card>      
    </div>               
</template>

<script>
import Message from '../../components/common/Message'
import config from '../../config'

export default {
    name:'Signin',
    components:{
        Message
    },
    data(){
        return{
            rules:{
                required:value=>!!value||'Required',
                min: v => v.length >= 8 || 'Min 8 characters',
                emailRules:v => !v || /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail must be valid'
            },
            register: false,
            forget:false,
            reset:false,
            emailReset:'',
            newPassword:'',
            confirmPassword:'',
            hassPassword:'',
            emailForget:'',
            fullNameRegister:'',
            emailRegister:'',
            passwordRegister:'',
            emailLogin:'',
            passwordLogin:'',
            show: false,
            loading:false,
            payLoading:true,
            userInfo:null,
            msg:'',
            userId:null,
            logoImg:'',
            title:'',
            description:'',
            allowPaidRegistration:false,
            registrationAmountWithText:'',
            stripeSessionId:''
        }
    },
    methods:{
        //password reset info sent to email
        adminCred(){
            this.emailLogin='admin@exam-systems.com'
            this.passwordLogin='12345678'
        },
        userCred(){
            this.emailLogin='candidate@exam-systems.com'
            this.passwordLogin='12345678'
        },
        sendPassword(){
            if(this.$refs.formForget.validate()){
                this.$store.dispatch('user/fetchUserInfo',this.emailForget)
                .then((response)=>{
                    if(response.status==200){
                        if(response.data!=''){
                            var linkReset=window.location.origin+'/signIn?email='+this.emailForget+'&hash='+response.data.password
                            const objEmail={
                                toEmail:this.emailForget,
                                logoPath:config.hostname+(this.$store.getters['settings/logoPath']==null?'':this.$store.getters['settings/logoPath'].replace(/\\/g, '/')),
                                siteUrl:window.location.origin,
                                body:linkReset
                            }
                            this.$store.dispatch('settings/passwordEmailSent',objEmail)
                            .then(response=>{
                                if(response.status==202){
                                    this.$root.$emit('message_from_parent_long',response.data.responseMsg)                    
                                }else if(response.status==200){
                                    this.$root.$emit('message_from_parent_long',response.data.responseMsg)
                                }
                            })
                            .catch(err=>{
                                console.log(err)
                            })
                        }else{
                            this.$root.$emit('message_from_parent_long','Email not registered yet!')
                        }
                    }else if(response.status==202){
                        this.$root.$emit('message_from_parent',response.data.responseMsg)
                    }
                })
            } 
        },
        //reset password
        resetPassword(){
            if(this.$refs.formReset.validate()){
                if(this.newPassword!=this.confirmPassword){
                    this.$root.$emit('message_from_parent_long','New and Confirm password not matched!')
                }else{
                    const objReset={
                        email:this.emailReset,
                        hassPassword:this.hassPassword,
                        newPassword:this.newPassword
                    }
                    this.$store.dispatch('user/resetPassword',objReset)
                    .then(response=>{
                        if(response.status==200){
                            this.$root.$emit('message_from_parent',response.data.responseMsg)                    
                        }else if(response.status==202){
                            this.$root.$emit('message_from_parent_long',response.data.responseMsg)
                        }
                    })
                    .catch(err=>{
                        console.log(err)
                    })
                }
            }
        },
        //user registration
        registration(){
            if(this.$refs.formRegister.validate()){
                const objUser={          
                    fullName:this.fullNameRegister,
                    email:this.emailRegister,
                    password:this.passwordRegister,
                    stripeSessionId:this.stripeSessionId           
                }             
                this.$store.dispatch('user/createRegistration',objUser)
                .then(response=>{
                    if(response.status==200){
                        const credential={
                            email:this.emailRegister,
                            password:this.passwordRegister
                        }
                        this.logIn(credential)
                        if(this.$store.getters['settings/allSettings'].allowWelcomeEmail==true){
                            const objEmail={
                                toEmail:this.emailRegister,
                                name:this.fullNameRegister,
                                logoPath:config.hostname+this.$store.getters['settings/logoPath'].replace(/\\/g, '/'),
                                siteUrl:window.location.origin,
                                body:'We are happy to have you with us. As a registed member of '+this.title+' from now you can access to '+this.title+' through this Email: '+this.emailRegister+' and Password: '+this.passwordRegister
                            }
                            this.$store.dispatch('settings/welcomeEmailSent',objEmail)
                        }                                       
                    }else if(response.status==202){
                        this.$root.$emit('message_from_parent',response.data.responseMsg)
                    }
                })
                .catch(err=>{
                    console.log(err)
                })               
            }
        },
        submit(){
            if(this.$refs.form.validate()){
                this.loading=true
                const credential={
                    email:this.emailLogin,
                    password:this.passwordLogin
                }
                this.logIn(credential)
            }
        },
        //sign in to app
        logIn(credential){
            this.$store.dispatch('dashboard/fetchSigninInfo',credential)
            .then(response=>{                                             
                    if(response.status==200){
                        this.userId=parseInt(localStorage.getItem('loggedUserId'))                                                     
                        const objLogHistory={
                            userId:this.userId,
                            token:localStorage.getItem('token'),
                            ip:this.$store.getters['dashboard/clientInfo'].userIp,
                            browser:this.$store.getters['dashboard/clientInfo'].browserName==null?'others':this.$store.getters['dashboard/clientInfo'].browserName,
                            browserVersion:this.$store.getters['dashboard/clientInfo'].browserVersion,
                            platform:this.$store.getters['dashboard/clientInfo'].platform                             
                        }
                        this.$store.dispatch('dashboard/createLogHistory',objLogHistory)
                        .then((response)=>{
                            if(response.status==200){
                                this.$router.push({name:'Dashboard'})                                                                      
                            }
                        })                                                                              
                    }else if(response.status==204){
                        this.loading=false
                        this.msg='Incorrect Email/Password!'
                        this.$root.$emit('message_from_parent',this.msg)
                        localStorage.removeItem('token')
                        localStorage.removeItem('loggedUserId')
                        localStorage.removeItem('logCode')
                    }                      
                })
            .catch(err => {                   
                if(this.$store.getters['dashboard/authStatus']==='error'){
                    this.loading=false                      
                    this.msg='Error established in network connection!'
                    this.$root.$emit('message_from_parent',this.msg)
                }
                console.log(err)
            }) 
        },
        //fetch settings
        getSettings(){
            this.$store.dispatch('settings/fetchSiteSettings')
            .then(response=>{
                this.payLoading=false
                if(response.status==200){
                    this.logoImg=response.data.logoPath==''?'':config.hostname+response.data.logoPath
                    this.title=response.data.siteTitle==''?'Self Assess':response.data.siteTitle
                    this.description=response.data.welComeMessage==''?'Hello there,Sign in to start your task!':response.data.welComeMessage
                    
                    if(response.data.paidRegistration==true && response.data.registrationPrice>0 && response.data.currency!=null && response.data.currencySymbol!=null && response.data.stripeSecretKey!=null && response.data.stripePubKey!=null){
                        this.allowPaidRegistration=true
                        this.registrationAmountWithText='Pay('+response.data.currencySymbol+response.data.registrationPrice+') & Register'
                    }
                    const favicon = document.getElementById('favicon')
                    favicon.href = config.hostname+response.data.faviconPath
                }
            })
            .catch(err=>{
                console.log(err)
            })
        }, 
        //reset password url verify   
        getResetPasswordInfo(){
            var url = new URL(window.location.href)
            this.emailReset = url.searchParams.get('email')
            this.hassPassword=url.searchParams.get('hash')
            if(this.emailReset && this.hassPassword){
                this.reset=true
            }
        },  
        //update host url to db
        updateClientUrl(){
            const obj={
                displayName:window.location.origin
            }
            this.$store.dispatch('settings/updateClientUrl',obj)
        }
    },
    computed:{
        //check registration status
        chkPaidRegistration(){
            return this.allowPaidRegistration
        },
        //payment url
        stripeGetUrl(){
            return config.hostname+'/registration'
        },
        //host url
        getWindowLocation(){
            return window.location.href;
        }
    },
    created(){
        this.getResetPasswordInfo()
        if(localStorage.getItem('logCode')!=null){
            this.$store.dispatch('dashboard/updateLogHistory',localStorage.getItem('logCode'))
        }
        this.$store.dispatch('dashboard/signOut')
        this.$store.dispatch('dashboard/fetchClientInfo')
        this.$store.dispatch('settings/resetRefreshCount')
        this.getSettings() 
        this.updateClientUrl()
    }
}
</script>

<style scoped>
    .login-page {
        align-items: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        min-height: 85vh;
    }    
    .login-box {
        width: 380px;
    }
    .v-card--reveal {
        bottom: 0;
        opacity: 1 !important;
        position: absolute;
        width: 100%;
    }
</style>