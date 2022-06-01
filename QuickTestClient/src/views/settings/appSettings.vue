<template>
    <v-container>
        <Message/>
        <v-form ref="form">
            <!-- general settings start -->
            <v-row>
                <v-col cols="12" sm="4"><h3>General Settings</h3></v-col>
            </v-row>
            <v-row>
                <v-col cols="12" sm="4">
                    <v-text-field label="Site Title" v-model="title" :rules="[rules.required]" v-on:keyup="setTitle"></v-text-field>
                </v-col>
                <v-col cols="12" sm="8">
                    <v-text-field label="Welcome Message" v-model="description" :rules="[rules.required]" v-on:keyup="setDescription"></v-text-field>
                </v-col>
            </v-row>
            <v-row>     
                <v-col cols="12" sm="4">
                    <v-text-field label="Copyright Text" v-model="footerText" :rules="[rules.required]" v-on:keyup="setFooterText"></v-text-field>
                </v-col>                        
                <v-col cols="12" sm="4">
                    <v-checkbox label="Allow Welcome Email" v-model="allowEmail"></v-checkbox>
                </v-col>
                <v-col cols="12" sm="4">
                    <v-checkbox label="Allow FAQ" v-model="allowFaq"></v-checkbox>
                </v-col>
            </v-row>
            <v-row>
                <v-col cols="12" sm="3">
                    <v-file-input accept="image/*" label="Site Logo" prepend-icon="mdi-camera" @change="onLogoSelected" show-size></v-file-input>                    
                </v-col>
                <v-col cols="12" sm="3">                   
                    <v-img :src="previewLogo" max-height="100" max-width="150" contain></v-img>
                </v-col>
                <v-col cols="12" sm="3">
                    <v-file-input accept="image/*" label="Site Favicon" prepend-icon="mdi-camera" @change="onFaviconSelected" show-size></v-file-input>                    
                </v-col>
                <v-col cols="12" sm="3">                   
                    <v-img :src="previewFavicon" max-height="100" max-width="150" contain></v-img>
                </v-col>
            </v-row>
            <!-- general settings end -->

            <!-- exam settings start -->
            <v-row>
                <v-col cols="12" sm="4"><h3>Exam Settings</h3></v-col>
            </v-row>
            <v-row>
                <v-col cols="12" sm="4">
                    <v-checkbox label="Browser refresh end the assessment" v-model="endExam"></v-checkbox>
                </v-col>
                <v-col cols="12" sm="4">
                    <v-checkbox label="Site logo on assessment page" v-model="examPageLogo"></v-checkbox>
                </v-col>
            </v-row>
            <!-- exam settings end -->

            <!-- payment settings start -->
            <v-row>
                <v-col cols="12" sm="4"><h3>Payment Settings</h3></v-col>
            </v-row>
            <v-row>
                <v-col cols="12" sm="4"><h4>Stripe</h4></v-col>
            </v-row>
            <v-row>
                <v-col cols="12" sm="4">
                    <v-checkbox label="Allow paid registration" v-model="paidRegistration"></v-checkbox>
                </v-col> 
                <v-col cols="12" sm="4">
                    <v-text-field label="Currency" v-model="currency" hint="USD,EUR,CAD,GBP,SAR,AUD,BZD,NZD,
                    HKD,SGD,JPY,AED,BGN,BRL,CHF,CZK,DKK,HUF,INR,BDT,MXN,MYR,NOK,PLN,RON,SEK,SGD & more"></v-text-field>
                </v-col>
                <v-col cols="12" sm="4">
                    <v-text-field label="Currency Symbol" v-model="currencySymbol"></v-text-field>
                </v-col>             
            </v-row>
            <v-row>                 
                <v-col cols="12" sm="4">
                    <v-text-field label="Registration price" v-model="registrationPrice" type="number" :rules="[rules.minimumVal]"></v-text-field>
                </v-col>
                <v-col cols="12" sm="4">
                    <v-text-field label="Stripe Secret key" v-model="stripeSecretKey"></v-text-field>
                </v-col> 
                <v-col cols="12" sm="4">
                    <v-text-field label="Stripe Publishable key" v-model="stripePubKey"></v-text-field>
                </v-col>            
            </v-row>
            <!-- payment settings end -->

            <!-- color settings start -->
            <v-row>
                <v-col cols="12" sm="4"><h3>Color Settings</h3></v-col>
            </v-row>
            <v-row >
                <v-col cols="12" sm="4">
                    <v-select
                        v-model="appBarColorSelect"
                        :items="colors"
                        label="App Bar"
                        dense
                        clearable                      
                        v-on:change="setAppBarColor"
                    >
                    </v-select>
                </v-col>
                <v-col cols="12" sm="4">
                    <v-select
                        v-model="footerColorSelect"
                        :items="colors"
                        label="Footer"
                        dense
                        clearable                     
                        v-on:change="setFooterColor"
                    >
                    </v-select>
                </v-col>
                <v-col cols="12" sm="4">
                    <v-select
                        v-model="bodyColorSelect"
                        :items="bodyColors"
                        label="Body"
                        dense
                        clearable                     
                        v-on:change="setBodyColor"
                    >
                    </v-select>
                </v-col>
            </v-row>
            <!-- color settings end -->
            <v-row>
                <v-col cols="12" sm="4" md="4">
                    <v-btn color="primary" @click="save">save</v-btn>
                </v-col>
            </v-row>
        </v-form>
    </v-container>
</template>

<script>
import Message from '../../components/common/Message'
import config from '../../config'

export default {
    name:'AppSettings',
    components:{
        Message
    },
    data(){
        return{
            rules:{
                required:value=>!!value||'Required',
                emailRules:v => !v || /^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,3})+$/.test(v) || 'E-mail must be valid',
                minimumVal:value=>value>=0,             
            },
            colors:['blue-grey darken-1','blue-grey darken-2','blue-grey darken-3','blue-grey darken-4','grey darken-3'],
            bodyColors:['grey lighten-1','grey lighten-2','grey lighten-3','grey lighten-4','grey lighten-5'],
            appBarColorSelect:null,
            footerColorSelect:null,
            bodyColorSelect:null,
            title:'',
            description:'',
            footerText:'',
            endExam:false,
            examPageLogo:false,
            paidRegistration:false,
            email:'',
            emailPassword:'',
            emailPort:'',
            emailHost:'',
            currency:'',
            currencySymbol:'',
            registrationPrice:0,
            stripePubKey:'',
            stripeSecretKey:'',
            allowEmail:null,
            allowFaq:null,
            previewLogo:null,
            selectedLogoFile:null,
            logoPath:'',
            previewFavicon:null,
            selectedFaviconFile:null,
            faviconPath:'',
            settings:{}
        }
    },
    methods:{
        //logo preview & save
        onLogoSelected(e){           
            if(e!=null){
                this.selectedLogoFile=e
                const reader=new FileReader()
                reader.readAsDataURL(this.selectedLogoFile)
                reader.onload=e=>{
                    this.previewLogo=e.target.result
                }

                let fd=new FormData()
                fd.append('image',this.selectedLogoFile)
                this.$store.dispatch('settings/uploadLogo',fd)
                .then(response=>{
                    if(response.status==200){ 
                        this.logoPath=response.data                                                                      
                    }
                })
            }else{               
                this.selectedLogoFile=null
                this.previewLogo=null
                this.logoPath=''
            }                      
        },
        //favicon preview & save
        onFaviconSelected(e){           
            if(e!=null){
                this.selectedFaviconFile=e
                const reader=new FileReader()
                reader.readAsDataURL(this.selectedFaviconFile)
                reader.onload=e=>{
                    this.previewFavicon=e.target.result
                }

                let fd=new FormData()
                fd.append('image',this.selectedFaviconFile)
                this.$store.dispatch('settings/uploadFavicon',fd)
                .then(response=>{
                    if(response.status==200){ 
                        this.faviconPath=response.data                                                                      
                    }
                })
            }else{               
                this.selectedFaviconFile=null
                this.previewFavicon=null
                this.faviconPath=''
            }                      
        },
        //app bar and navigation color set
        setAppBarColor(val){
            this.$store.dispatch('settings/changeAppBarColor',val) 
        },
        //set footer color
        setFooterColor(val){
            this.$store.dispatch('settings/changeFooterColor',val) 
        },
        //set body color
        setBodyColor(val){          
            if(val==null){
                val='grey lighten-3'
            }
            this.$store.dispatch('settings/changeBgColor',val) 
        },
        setTitle(){            
            this.$store.dispatch('settings/changeSiteTitle',this.title) 
        },
        setDescription(){            
            this.$store.dispatch('settings/changeSiteDescription',this.description) 
        },
        setFooterText(){            
            this.$store.dispatch('settings/changeFooterText',this.footerText) 
        },
        //get all settings data
        initialize(){
            this.$store.dispatch('dashboard/applyLoading')       
            this.$store.dispatch('settings/fetchSiteSettings')
            .then(response=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.settings=response.data
                this.title=this.settings.siteTitle
                this.description=this.settings.welComeMessage
                this.footerText=this.settings.copyRightText
                this.endExam=this.settings.endExam
                this.examPageLogo=this.settings.logoOnExamPage
                this.paidRegistration=this.settings.paidRegistration
                this.registrationPrice=this.settings.registrationPrice
                this.currency=this.settings.currency
                this.currencySymbol=this.settings.currencySymbol
                this.stripePubKey=this.settings.stripePubKey
                this.stripeSecretKey=this.settings.stripeSecretKey
                this.email=this.settings.defaultEmail
                this.emailPassword=this.settings.password
                this.emailPort=this.settings.port
                this.emailHost=this.settings.host
                this.allowEmail=this.settings.allowWelcomeEmail
                this.allowFaq=this.settings.allowFaq
                this.previewLogo=this.settings.logoPath!=''?config.hostname+this.settings.logoPath:''
                this.logoPath=this.settings.logoPath!=''?this.settings.logoPath:''
                this.previewFavicon=this.settings.faviconPath!=''?config.hostname+this.settings.faviconPath:''
                this.faviconPath=this.settings.faviconPath!=''?this.settings.faviconPath:''
                this.appBarColorSelect=this.settings.appBarColor
                this.footerColorSelect=this.settings.footerColor
                this.bodyColorSelect=this.settings.bodyColor
            })
            .catch(err=>{
                console.log(err)
            })
        },
        //update settings
        updateSettings(){
            const objSettings={
                siteSettingsId:this.settings.siteSettingsId,
                siteTitle:this.title,
                welComeMessage:this.description,
                copyRightText:this.footerText,
                endExam:this.endExam,
                logoOnExamPage:this.examPageLogo,
                paidRegistration:this.paidRegistration,
                registrationPrice:this.registrationPrice==''?0:this.registrationPrice,
                currency:this.currency,
                currencySymbol:this.currencySymbol,
                stripePubKey:this.stripePubKey,
                stripeSecretKey:this.stripeSecretKey,
                defaultEmail:this.email,
                password:this.emailPassword,
                host:this.emailHost,
                port:this.emailPort==''?0:this.emailPort,
                logoPath:this.logoPath,
                faviconPath:this.faviconPath,
                appBarColor:this.appBarColorSelect,
                footerColor:this.footerColorSelect,
                bodyColor:this.bodyColorSelect,
                allowWelcomeEmail:this.allowEmail,
                allowFaq:this.allowFaq,
                lastUpdatedBy:parseInt(localStorage.getItem('loggedUserId')),
            }
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('settings/updateSettings',objSettings)
            .then(response=>{
                this.$store.dispatch('dashboard/cancelLoading')
                if(response.status==200){
                    this.$root.$emit('message_from_parent','Successfully updated')
                }
            })
            .catch(err=>{
                console.log(err)
            })
        },
        save(){
            if(this.$refs.form.validate()){
                this.updateSettings()              
            }
        },
    },
    created(){
        this.initialize()
    }
}
</script>
<style scoped>
</style>