<template>
<!-- app loader start-->
    <v-overlay :value="isLoading" :opacity="opacity">
        <v-progress-circular indeterminate></v-progress-circular>
    </v-overlay>
<!-- app loader end-->
</template>

<script>

export default {
    name:'Loading',
    data(){
        return{
            opacity:0.6,          
        }
    },
    computed:{
        //check loader state
        isLoading:function(){
            return this.$store.getters['dashboard/loading']
        }
    }

}
</script>