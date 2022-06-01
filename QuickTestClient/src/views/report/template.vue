<template>
<!-- certificate template -->
    <v-container class="px-0">
        <div ref="quizResult">
            <v-card flat id="certificate">
                <v-img
                    :src="bgImage"              
                >
                    <v-card-text class="mt-5 pb-0 mb-0" v-if="this.templateObj.topLeftImagePath!='' || this.templateObj.topRightImagePath!=''">
                        <v-row justify="center">
                            <v-col cols="2" class="ml-10"><v-img max-height="200" max-width="350" :src="leftLogo"></v-img></v-col>
                            <v-col cols="6"></v-col>
                            <v-col cols="2"><v-img max-height="200" max-width="350" :src="rightLogo"></v-img></v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-text class="mt-16 pt-16" v-else></v-card-text>
                    <v-card-text>
                        <v-row justify="center" style="font-size:25px">
                            <h1>{{this.templateObj.title}}</h1>         
                        </v-row>
                        <v-row justify="center" class="mt-10" style="font-size:20px">
                            <h3>{{this.templateObj.heading}}</h3>                   
                        </v-row>
                        <v-row justify="center" class="mx-14 mt-5" style="font-size:22px">
                            <span class="font-weight-medium">{{this.templateObj.mainText}}</span>
                        </v-row>
                    </v-card-text>
                    <v-card-text>
                        <v-row justify="start" v-if="this.templateObj.leftSignatureImagePath!='' || this.templateObj.leftSignatureText!=''">
                            <v-col cols="5">
                                <v-row justify="center"><v-img max-height="100" max-width="250" :src="leftSignatureImage"></v-img></v-row>
                                <hr>
                                <v-row justify="center" style="font-size:20px">{{leftSignatureText}}</v-row>
                            </v-col>

                            <v-col cols="2"><v-img max-height="200" max-width="350" :src="bottomMiddileLogo"></v-img></v-col>

                            <v-col cols="5" class="pr-14" v-if="this.templateObj.rightSignatureImagePath!='' || this.templateObj.rightSignatureText!=''">
                                <v-row justify="center"><v-img max-height="100" max-width="250" :src="rightSignatureImage"></v-img></v-row>
                                <hr>
                                <v-row justify="center" style="font-size:20px">{{rightSignatureText}}</v-row>
                            </v-col>
                        </v-row>
                        <v-row justify="end" v-else-if="this.templateObj.rightSignatureImagePath!='' || this.templateObj.rightSignatureText!=''">
                            <v-col cols="5" class="pl-14" v-if="this.templateObj.leftSignatureImagePath!='' || this.templateObj.leftSignatureText!=''">
                                <v-row justify="center"><v-img max-height="100" max-width="250" :src="leftSignatureImage"></v-img></v-row>
                                <hr>
                                <v-row justify="center" style="font-size:20px">{{leftSignatureText}}</v-row>
                            </v-col>

                            <v-col cols="2"><v-img max-height="200" max-width="350" :src="bottomMiddileLogo"></v-img></v-col>

                            <v-col cols="5" class="pr-14">
                                <v-row justify="center"><v-img max-height="100" max-width="250" :src="rightSignatureImage"></v-img></v-row>
                                <hr>
                                <v-row justify="center" style="font-size:20px">{{rightSignatureText}}</v-row>
                            </v-col>
                        </v-row>
                        <v-row v-else justify="center">
                            <v-col cols="2"><v-img max-height="200" max-width="350" :src="bottomMiddileLogo"></v-img></v-col>
                        </v-row>
                    </v-card-text>
                    <v-card-text v-if="this.templateObj.publishDate!='' && this.templateObj.publishDate!=null" class="font-weight-black pr-16 pt-16">
                        <v-row justify="end">Published on {{this.templateObj.publishDate}}</v-row>
                    </v-card-text>
                </v-img>
            </v-card>
        </div>
    </v-container>
</template>

<script>
import jsPDF from 'jspdf'
import html2canvas from 'html2canvas'
import config from '../../config'

export default {
    name:'Template',
    data(){
        return{
            templateObj:{},  
            userInfo:{},
            certificateData:{}        
        }
    },
    methods:{
        //get certificate on pdf
        generateResultPdf(){
            var pdfRef = this.$refs.quizResult;
            html2canvas(pdfRef).then(canvas => {
                var imgData = canvas.toDataURL('image/png');
                var doc = new jsPDF({
                    orientation:'l'
                });
                var imgWidth = 250;  
                var imgHeight = canvas.height * imgWidth / canvas.width;
                doc.addImage(imgData, 'PNG', 20, 10, imgWidth, imgHeight);
                doc.save('Certificate.pdf');
            })
        },
        //replace template text by actual data
        replaceText(text){
            text=text.replaceAll('[fullName]',this.certificateData.fullName)
            text=text.replaceAll('[email]',this.certificateData.email)
            text=text.replaceAll('[mobile]',this.certificateData.mobile==null?'':this.certificateData.mobile)
            text=text.replaceAll('[address]',this.certificateData.address==null?'':this.certificateData.address)
            text=text.replaceAll('[dateOfBirth]',this.certificateData.dateOfBirth==null?'':this.certificateData.dateOfBirth.substr(0,10))
            text=text.replaceAll('[quizTitle]',this.certificateData.quizTitle)
            text=text.replaceAll('[quizTime]',this.certificateData.quizTime)
            text=text.replaceAll('[timeTaken]',this.certificateData.timeTaken)
            text=text.replaceAll('[quizMark]',this.certificateData.quizMark)
            text=text.replaceAll('[userObtainedQuizMark]',this.certificateData.userObtainedQuizMark)
            text=text.replaceAll('[attemptCount]',this.certificateData.attemptCount)           
            return text
        }
    },
    //images from source
    computed:{
        bgImage(){
            return this.templateObj.backgroundImagePath==''?'':config.hostname+this.templateObj.backgroundImagePath
        },
        leftLogo(){
            return this.templateObj.topLeftImagePath==''?'':config.hostname+this.templateObj.topLeftImagePath
        },
        rightLogo(){
            return this.templateObj.topRightImagePath==''?'':config.hostname+this.templateObj.topRightImagePath
        },
        leftSignatureText(){
            return this.templateObj.leftSignatureText==''?'':this.templateObj.leftSignatureText
        },
        leftSignatureImage(){
            return this.templateObj.leftSignatureImagePath==''?'':config.hostname+this.templateObj.leftSignatureImagePath
        },
        rightSignatureText(){
            return this.templateObj.rightSignatureText==''?'':this.templateObj.rightSignatureText
        },
        rightSignatureImage(){
            return this.templateObj.rightSignatureImagePath==''?'':config.hostname+this.templateObj.rightSignatureImagePath
        },
        bottomMiddileLogo(){
            return this.templateObj.bottomMiddleImagePath==''?'':config.hostname+this.templateObj.bottomMiddleImagePath
        },
        downloadVisible(){
            return window.innerWidth>=960?true:false
        }
    },
    created(){
        this.userInfo=this.$store.getters['dashboard/userInfo']      
        if(this.userInfo.roleName=='Student'){
            this.$store.dispatch('dashboard/applyLoading')
            this.certificateData=this.$store.getters['report/certificateInfo']
            this.$store.dispatch('report/fetchCertificateData',this.certificateData.certificateTemplateId)
            .then(response=>{
                this.$store.dispatch('dashboard/cancelLoading')
                if(response.status==200){
                    this.templateObj=response.data
                    this.templateObj.title=this.replaceText(this.templateObj.title)
                    this.templateObj.heading=this.replaceText(this.templateObj.heading)
                    this.templateObj.mainText=this.replaceText(this.templateObj.mainText)
                }
            })
            .catch(err=>{
                console.log(err)
            })
        }else{
            this.templateObj=this.$store.getters['quiz/singleTemplate']
        }
    }
}
</script>

<style scoped>
    @import url('https://fonts.googleapis.com/css2?family=Sofia&display=swap');
    #certificate{
        font-family:'Sofia',sans-serif;
    }
</style>