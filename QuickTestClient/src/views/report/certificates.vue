<template>
<!-- certificate template -->
    <v-container class="px-0">
        <Message/>
        <v-data-table
            :headers="headersTemplate"
            :items="itemsTemplate"
            class="elevation-1"
        >
            <template v-slot:top>
                <v-toolbar
                    flat
                >         
                    <v-dialog
                        v-model="dialog"
                        max-width="900"
                    >
                        <template v-slot:activator="{ on, attrs }">
                            <v-btn
                                color="primary"
                                dark
                                class="mb-2"
                                v-bind="attrs"
                                v-on="on"               
                                absolute
                                right
                            >
                                New Item
                            </v-btn>
                        </template>
                        <v-card>
                            <v-card-title>
                                <span class="headline">{{ formTitle }}</span>
                            </v-card-title>
                            <v-card-text>
                                <v-row align="center" justify="center">
                                    <v-chip-group multiple>
                                        <v-chip id="[fullName]" @dragstart="drag($event)" draggable>Student Name</v-chip>
                                        <v-chip id="[email]" @dragstart="drag($event)" draggable>Email</v-chip>
                                        <v-chip id="[mobile]" @dragstart="drag($event)" draggable>Mobile</v-chip>
                                        <v-chip id="[address]" @dragstart="drag($event)" draggable>Address</v-chip>
                                        <v-chip id="[dateOfBirth]" @dragstart="drag($event)" draggable>Date of Birth</v-chip>
                                        <v-chip id="[quizTitle]" @dragstart="drag($event)" draggable>Title</v-chip>
                                        <v-chip id="[quizTime]" @dragstart="drag($event)" draggable>Time</v-chip>
                                        <v-chip id="[timeTaken]" @dragstart="drag($event)" draggable>Time Taken</v-chip>
                                        <v-chip id="[quizMark]" @dragstart="drag($event)" draggable>Marks</v-chip>
                                        <v-chip id="[userObtainedQuizMark]" @dragstart="drag($event)" draggable>Marks Obtained</v-chip>
                                        <v-chip id="[attemptCount]" @dragstart="drag($event)" draggable>Attempt Number</v-chip>
                                    </v-chip-group>
                                </v-row>
                            </v-card-text>

                            <v-card-text>
                                <v-container>
                                    <v-form ref="form">
                                        <v-row>
                                            <v-col 
                                                cols="12"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.title"
                                                    label="Certificate Title"
                                                    :rules="[rules.required]"                                                                                                    
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col 
                                                cols="12"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.heading"
                                                    label="Certificate Top Header"
                                                    :rules="[rules.required]"                                                                                                    
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>  
                                        <v-row>
                                            <v-col 
                                                cols="12"
                                            >
                                                <v-textarea
                                                    v-model="editedItem.mainText"
                                                    label="Certificate Main Text"                                                  
                                                    :rules="[rules.required]"
                                                    @drop="drop($event)"
                                                    @dragover="allowDrop($event)"
                                                    rows="5"                                                                                                    
                                                    clearable                       
                                                ></v-textarea>
                                            </v-col>
                                        </v-row>                                      
                                        <v-row>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-file-input
                                                    v-model="leftImageFile"
                                                    accept="image/*"
                                                    label="Top Left Logo"
                                                    prepend-icon="mdi-camera"
                                                    @change="onLeftImageSelected"                       
                                                    show-size
                                                >
                                                </v-file-input> 
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-img                       
                                                    :src="previewLeftImage"
                                                    max-height="100"
                                                    max-width="150"
                                                    contain                      
                                                >
                                                </v-img>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-file-input
                                                    v-model="rightImageFile"
                                                    accept="image/*"
                                                    label="Top Right Logo"
                                                    prepend-icon="mdi-camera"
                                                    @change="onRightImageSelected"                       
                                                    show-size
                                                >
                                                </v-file-input> 
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-img                       
                                                    :src="previewRightImage"
                                                    max-height="100"
                                                    max-width="150"
                                                    contain                      
                                                >
                                                </v-img>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-file-input
                                                    v-model="bottomImageFile"
                                                    accept="image/*"
                                                    label="Bottom Logo"
                                                    prepend-icon="mdi-camera"
                                                    @change="onBottomImageSelected"                       
                                                    show-size
                                                >
                                                </v-file-input> 
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-img                       
                                                    :src="previewBottomImage"
                                                    max-height="100"
                                                    max-width="150"
                                                    contain                      
                                                >
                                                </v-img>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-file-input
                                                    v-model="backGroundImageFile"
                                                    accept="image/*"
                                                    label="BackGround"
                                                    prepend-icon="mdi-camera"
                                                    @change="onBackGroundImageSelected"                       
                                                    show-size
                                                >
                                                </v-file-input> 
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-img                       
                                                    :src="previewBackGroundImage"
                                                    max-height="100"
                                                    max-width="150"
                                                    contain                      
                                                >
                                                </v-img>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-file-input
                                                    v-model="signatureLeftImage"
                                                    accept="image/*"
                                                    label="Signature Left"
                                                    prepend-icon="mdi-camera"
                                                    @change="onSignatureLeftSelected"                       
                                                    show-size
                                                >
                                                </v-file-input> 
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-img                       
                                                    :src="previewSignatureLeft"
                                                    max-height="100"
                                                    max-width="150"
                                                    contain                      
                                                >
                                                </v-img>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="6"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.leftSignatureText"
                                                    label="Signature Left Text"                                                                                                                                                      
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-file-input
                                                    v-model="signatureRightImage"
                                                    accept="image/*"
                                                    label="Signature Right"
                                                    prepend-icon="mdi-camera"
                                                    @change="onSignatureRightSelected"                       
                                                    show-size
                                                >
                                                </v-file-input> 
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="3"
                                            >
                                                <v-img                       
                                                    :src="previewSignatureRight"
                                                    max-height="100"
                                                    max-width="150"
                                                    contain                      
                                                >
                                                </v-img>
                                            </v-col>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                                md="6"
                                            >
                                                <v-text-field
                                                    v-model="editedItem.rightSignatureText"
                                                    label="Signature Right Text"                                                                                                                                                      
                                                    clearable                       
                                                ></v-text-field>
                                            </v-col>
                                        </v-row>
                                        <v-row>
                                            <v-col
                                                cols="12"
                                                sm="6"
                                            >
                                                <v-menu
                                                    v-model="publishMenu"
                                                    :close-on-content-click="false"
                                                    :nudge-right="40"
                                                    transition="scale-transition"
                                                    offset-y
                                                    min-width="auto"
                                                    >
                                                        <template v-slot:activator="{ on, attrs }">
                                                            <v-text-field
                                                                v-model="editedItem.publishDate"
                                                                label="Publish Date"
                                                                prepend-icon="mdi-calendar"
                                                                readonly
                                                                v-bind="attrs"
                                                                v-on="on"
                                                                clearable
                                                            ></v-text-field>
                                                        </template>
                                                        <v-date-picker
                                                            v-model="editedItem.publishDate"
                                                            @input="publishMenu = false"
                                                        ></v-date-picker>
                                                    </v-menu>
                                            </v-col>
                                        </v-row>                                                                          
                                    </v-form>                
                                </v-container>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn
                                    color="blue darken-1"
                                    text
                                    @click="close"
                                >
                                Cancel
                                </v-btn>
                                <v-btn
                                    color="blue darken-1"
                                    text
                                    @click="save"
                                >
                                Save
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <v-dialog v-model="dialogDelete" max-width="500px">
                        <v-card>
                            <v-card-title class="headline">Are you sure you want to delete this item?</v-card-title>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click="closeDelete">Cancel</v-btn>
                                <v-btn color="blue darken-1" text @click="deleteItemConfirm">OK</v-btn>
                                <v-spacer></v-spacer>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                </v-toolbar>
            </template>
            <template v-slot:[`item.actions`]="{item}">
                <v-icon
                    small
                    class="mr-2"
                    @click="viewCertificate(item)"
                >
                    visibility
                </v-icon>
                <v-icon
                    small
                    class="mr-2"
                    @click="editItem(item)"
                >
                    mdi-pencil
                </v-icon>
                <v-icon
                    small
                    class="mr-2"
                    @click="deleteItem(item)"
                >
                    mdi-delete
                </v-icon>
            </template>
        </v-data-table>
    </v-container>
</template>

<script>
import Message from '../../components/common/Message'
import config from '../../config'

export default {
    name:'Certificates',
    components:{
      Message
    },
    data(){
        return{
            publishMenu:false,
            leftImageFile:null,
            previewLeftImage:null,
            selectedLeftImage:null,
            rightImageFile:null,
            previewRightImage:null,
            selectedRightImage:null,
            bottomImageFile:null,
            previewBottomImage:null,
            selectedBottomImage:null,
            backGroundImageFile:null,
            previewBackGroundImage:null,
            selectedBackGroundImage:null,
            signatureLeftImage:null,
            previewSignatureLeft:null,
            selectedSignatureLeftImage:null,
            signatureRightImage:null,
            previewSignatureRight:null,
            selectedSignatureRightImage:null,
            dialog:false,
            dialogDelete:false,
            rules:{
                required:value=>!!value||'Required'
            },
            headersTemplate:[
                {text:'Title',value:'title'},
                {text:'Header',value:'heading'},
                {text:'Main Text',value:'mainText'},
                {text: 'Actions', value: 'actions', sortable: false},
            ],
            itemsTemplate:[],
            editedIndex:-1,
            editedItem:{
                certificateTemplateId:null,
                title:'',
                heading:'',
                mainText:'',
                publishDate:'',
                topLeftImagePath:'',
                topRightImagePath:'',
                bottomMiddleImagePath:'',                  
                backgroundImagePath:'',
                leftSignatureImagePath:'',
                leftSignatureText:'',
                rightSignatureImagePath:'',
                rightSignatureText:'',
            },
            defaultItem:{
                certificateTemplateId:null,
                title:'',
                heading:'',
                mainText:'',
                publishDate:'',
                topLeftImagePath:'',
                topRightImagePath:'',
                bottomMiddleImagePath:'',
                backgroundImagePath:'',
                leftSignatureImagePath:'',
                leftSignatureText:'',
                rightSignatureImagePath:'',
                rightSignatureText:'',              
            },
        }
    },
    methods:{
        //certificate left image upload
        onLeftImageSelected(e){
            if(e!=null){
                this.selectedLeftImage=e
                const reader=new FileReader()
                reader.readAsDataURL(this.selectedLeftImage)
                reader.onload=e=>{
                    this.previewLeftImage=e.target.result
                }
                let fd=new FormData()
                fd.append('image',this.selectedLeftImage)
                this.$store.dispatch('quiz/uploadCertificateImage',fd)
                .then(response=>{                
                    if(response.status==200){
                        this.editedItem.topLeftImagePath=response.data                       
                    }
                })
            }else{               
                this.selectedLeftImage=null
                this.previewLeftImage=null
                this.editedItem.topLeftImagePath=''
            }                      
        },
        //certificate right image upload
        onRightImageSelected(e){
            if(e!=null){
                this.selectedRightImage=e
                const reader=new FileReader()
                reader.readAsDataURL(this.selectedRightImage)
                reader.onload=e=>{
                    this.previewRightImage=e.target.result
                }
                let fd=new FormData()
                fd.append('image',this.selectedRightImage)
                this.$store.dispatch('quiz/uploadCertificateImage',fd)
                .then(response=>{
                    if(response.status==200){
                        this.editedItem.topRightImagePath=response.data                           
                    }
                })
            }else{               
                this.selectedRightImage=null
                this.previewRightImage=null
                this.editedItem.topRightImagePath=''
            }                      
        },
        //certificate bottom image upload
        onBottomImageSelected(e){
            if(e!=null){
                this.selectedBottomImage=e
                const reader=new FileReader()
                reader.readAsDataURL(this.selectedBottomImage)
                reader.onload=e=>{
                    this.previewBottomImage=e.target.result
                }
                let fd=new FormData()
                fd.append('image',this.selectedBottomImage)
                this.$store.dispatch('quiz/uploadCertificateImage',fd)
                .then(response=>{
                    if(response.status==200){
                        this.editedItem.bottomMiddleImagePath=response.data                          
                    }
                })
            }else{               
                this.selectedBottomImage=null
                this.previewBottomImage=null
                this.editedItem.bottomMiddleImagePath=''
            }                      
        },
        //certificate background image upload
        onBackGroundImageSelected(e){
            if(e!=null){
                this.selectedBackGroundImage=e
                const reader=new FileReader()
                reader.readAsDataURL(this.selectedBackGroundImage)
                reader.onload=e=>{
                    this.previewBackGroundImage=e.target.result
                }
                let fd=new FormData()
                fd.append('image',this.selectedBackGroundImage)
                this.$store.dispatch('quiz/uploadCertificateImage',fd)
                .then(response=>{
                    if(response.status==200){
                        this.editedItem.backgroundImagePath=response.data                           
                    }
                })
            }else{               
                this.selectedBackGroundImage=null
                this.previewBackGroundImage=null
                this.editedItem.backgroundImagePath=''
            }                      
        },
        //certificate left signature image upload
        onSignatureLeftSelected(e){
            if(e!=null){
                this.selectedSignatureLeftImage=e
                const reader=new FileReader()
                reader.readAsDataURL(this.selectedSignatureLeftImage)
                reader.onload=e=>{
                    this.previewSignatureLeft=e.target.result
                }
                let fd=new FormData()
                fd.append('image',this.selectedSignatureLeftImage)
                this.$store.dispatch('quiz/uploadCertificateImage',fd)
                .then(response=>{
                    if(response.status==200){
                        this.editedItem.leftSignatureImagePath=response.data                                               
                    }
                })
            }else{               
                this.selectedSignatureLeftImage=null
                this.previewSignatureLeft=null
                this.editedItem.leftSignatureImagePath=''
            }                      
        },
        //certificate right signature image upload
        onSignatureRightSelected(e){
            if(e!=null){
                this.selectedSignatureRightImage=e
                const reader=new FileReader()
                reader.readAsDataURL(this.selectedSignatureRightImage)
                reader.onload=e=>{
                    this.previewSignatureRight=e.target.result
                }
                let fd=new FormData()
                fd.append('image',this.selectedSignatureRightImage)
                this.$store.dispatch('quiz/uploadCertificateImage',fd)
                .then(response=>{
                    if(response.status==200){
                        this.editedItem.rightSignatureImagePath=response.data                          
                    }
                })
            }else{               
                this.selectedSignatureRightImage=null
                this.previewSignatureRight=null
                this.editedItem.rightSignatureImagePath=''
            }                      
        },
        //get certificate data
        initialize(){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('quiz/fetchTemplates')
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsTemplate=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //edit view
        editItem(item){
            item.publishDate=item.publishDate==null?null:item.publishDate.substr(0,10)
            this.previewLeftImage=item.topLeftImagePath!=null?config.hostname+item.topLeftImagePath:null
            this.previewRightImage=item.topRightImagePath!=null?config.hostname+item.topRightImagePath:null
            this.previewBottomImage=item.bottomMiddleImagePath!=null?config.hostname+item.bottomMiddleImagePath:null
            this.previewBackGroundImage=item.backgroundImagePath!=null?config.hostname+item.backgroundImagePath:null
            this.previewSignatureLeft=item.leftSignatureImagePath!=null?config.hostname+item.leftSignatureImagePath:null
            this.previewSignatureRight=item.rightSignatureImagePath!=null?config.hostname+item.rightSignatureImagePath:null
            this.editedIndex=this.itemsTemplate.indexOf(item)           
            this.editedItem=Object.assign({},item)
            this.dialog=true
        },
        //view certificate on designed template
        viewCertificate(item){
            this.$store.dispatch('quiz/storeSingleTemplate',item)
            this.$router.push({name:'Template'})
        },
        //delete dialog
        deleteItem(item){
            this.editedItem = Object.assign({}, item)
            this.dialogDelete=true
        },
        //delete dialog confirm
        deleteItemConfirm () {       
            this.$store.dispatch('quiz/deleteTemplate',this.editedItem.certificateTemplateId)
            .then(response=>{
                if(response.status==200){
                    this.$root.$emit('message_from_parent',response.data.responseMsg)
                    this.$store.dispatch('quiz/fetchTemplates')
                    .then((response)=>{
                        this.itemsTemplate=response.data                  
                    })
                    .catch((err)=>{
                        console.log(err)
                    })
                }else if(response.status==202){
                    this.$root.$emit('message_from_parent_long',response.data.responseMsg)
                }
            })
            .catch(err=>{
                console.log(err)
            })
            this.closeDelete()
        },
        //drag & drop functions to design a template start
        drag(event) {
            event.dataTransfer.setData("text", event.target.id)
        },
        drop(event) {
            event.target.value += event.dataTransfer.getData("text")
            this.editedItem.mainText=event.target.value
        },
        allowDrop(event) {
            event.preventDefault()
        },
        //drag & drop functions to design a template end

        //close dialog for save & edit
        close(){
            this.dialog=false
            this.leftImageFile=null
            this.selectedLeftImage=null
            this.previewLeftImage=null
            this.rightImageFile=null
            this.selectedRightImage=null
            this.previewRightImage=null
            this.bottomImageFile=null
            this.selectedBottomImage=null
            this.previewBottomImage=null
            this.backGroundImageFile=null
            this.selectedBackGroundImage=null
            this.previewBackGroundImage=null
            this.signatureLeftImage=null
            this.selectedSignatureLeftImage=null
            this.previewSignatureLeft=null
            this.signatureRightImage=null
            this.selectedSignatureRightImage=null
            this.previewSignatureRight=null
            this.$nextTick(()=>{       
                this.editedItem=Object.assign({},this.defaultItem)
                this.editedIndex=-1
            })
        },
        //close dialog delete
        closeDelete(){
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
        },
        //create & edit
        save(){
            if(this.$refs.form.validate()){
                const objCertificate={
                    title:this.editedItem.title.trim(),
                    heading:this.editedItem.heading.trim(),
                    mainText:this.editedItem.mainText.trim(),
                    publishDate:this.editedItem.publishDate==null?'':this.editedItem.publishDate.substr(0,10),
                    topLeftImagePath:this.editedItem.topLeftImagePath,
                    topRightImagePath:this.editedItem.topRightImagePath,
                    bottomMiddleImagePath:this.editedItem.bottomMiddleImagePath,
                    backgroundImagePath:this.editedItem.backgroundImagePath,
                    leftSignatureImagePath:this.editedItem.leftSignatureImagePath,
                    leftSignatureText:this.editedItem.leftSignatureText!=null?this.editedItem.leftSignatureText.trim():null,
                    rightSignatureImagePath:this.editedItem.rightSignatureImagePath,
                    rightSignatureText:this.editedItem.rightSignatureText!=null?this.editedItem.rightSignatureText.trim():null,
                    addedBy:parseInt(localStorage.getItem('loggedUserId')),
                }
                if (this.editedIndex > -1){
                    objCertificate.certificateTemplateId=this.editedItem.certificateTemplateId
                    objCertificate.lastUpdatedBy=parseInt(localStorage.getItem('loggedUserId'))

                    this.$store.dispatch('quiz/updateTemplate',objCertificate)
                    .then(response=>{
                    if(response.status==200){
                        this.$root.$emit('message_from_parent',response.data.responseMsg)
                        this.$store.dispatch('quiz/fetchTemplates')
                        .then((response)=>{                              
                            this.close()
                            this.itemsTemplate=response.data
                        })
                        .catch((err)=>{
                            console.log(err)
                        })
                    }else if(response.status==202){
                            this.$root.$emit('message_from_parent',response.data.responseMsg)
                        }
                    })
                    .catch(err=>{
                        console.log(err)
                    })

                }else{
                    this.$store.dispatch('quiz/createTemplate',objCertificate)
                    .then(response=>{
                    if(response.status==200){
                        this.$root.$emit('message_from_parent','Successfully saved')
                        this.$store.dispatch('quiz/fetchTemplates')
                        .then((response)=>{                              
                            this.close()
                            this.itemsTemplate=response.data
                        })
                        .catch((err)=>{
                            console.log(err)
                        })
                    }else if(response.status==202){
                            this.$root.$emit('message_from_parent',response.data.responseMsg)
                        }
                    })
                    .catch(err=>{
                        console.log(err)
                    })
                }
            }
        }
    },
    computed:{
        formTitle(){
            return this.editedIndex===-1?'New Template':'Edit Template'
        },
    },
    watch:{
        dialog (val) {         
            val || this.close()
        },
        dialogDelete (val) {
            val || this.closeDelete()
        },
    },
    created(){
        this.initialize()
    }
}
</script>