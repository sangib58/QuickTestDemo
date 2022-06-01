<template>
    <!-- snackbar for short time duration start -->
    <v-snackbar v-if="msgLong==false" v-model="snackbar" :timeout="timeout" top>
        <span>{{msg}}</span>
        <template v-slot:action="{ attrs }">
            <v-btn color="blue" text v-bind="attrs" @click="snackbar=false">close</v-btn>
        </template>
    </v-snackbar>
    <!-- snackbar for short time duration end -->

    <!-- snackbar for long time duration start -->
    <v-snackbar v-else-if="msgLong==true" v-model="snackbar" :timeout="timeout" top>
        <span>{{msg}}</span>
        <template v-slot:action="{ attrs }">
            <v-btn color="blue" text v-bind="attrs" @click="snackbar=false">close</v-btn>
        </template>
    </v-snackbar>
    <!-- snackbar for long time duration end -->
</template>

<script>
export default {
    name:'Message',
    data(){
        return{
            snackbar: false,
            msg: '',
            timeout: -1,
            msgLong:false
        }
    },
    mounted(){
        //message display for short time
        this.$root.$on('message_from_parent',(msg)=>{
            this.snackbar=true
            this.timeout=2000
            this.msg=msg
            this.msgLong=false
        })

        //message display for long time
        this.$root.$on('message_from_parent_long',(msg)=>{
            this.snackbar=true
            this.timeout=10000
            this.msg=msg
            this.msgLong=true
        })
    }
}
</script>