<template>
<!-- footer start-->
    <v-footer padless light :color="getFooterColor">
        <v-col class="text-right font-weight-regular">
            {{footerText}}
        </v-col>
    </v-footer>
<!-- footer end-->
</template>

<script>
export default {
    name:'Footer',
    computed:{
        //footer color from settings
        getFooterColor:function(){
            return this.$store.getters['settings/footerColor']
        },
        //footer text from settings
        footerText:function(){
            return this.$store.getters['settings/footerText']
        }
    },
}
</script>