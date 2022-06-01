<template>
    <nav>
        <!-- app bar start -->
        <v-app-bar app light :color="getAppBarColor">
            <v-app-bar-nav-icon @click="drawer=!drawer"></v-app-bar-nav-icon>
            <v-spacer></v-spacer>
            <v-btn @click="applyLock" icon>
                <v-icon>lock</v-icon>
            </v-btn>
            <v-btn @click="toggle" icon>
                <v-icon>fullscreen</v-icon>
            </v-btn>
            <v-menu offset-y>
                <template v-slot:activator="{on}">
                    <v-btn text v-on="on" color="grey">
                        <v-icon left>expand_more</v-icon>
                        <span class="text-capitalize">Options</span>
                    </v-btn>
                </template>
                <v-list v-if="getUserRole=='Admin'">
                    <v-list-item v-for="link in linksAdmin" :key="link.text" :to="link.route">
                        <v-list-item-title>{{link.text}}</v-list-item-title>
                    </v-list-item>                  
                </v-list> 
                <v-list v-else>
                    <v-list-item v-for="link in linksOthers" :key="link.text" :to="link.route">
                        <v-list-item-title>{{link.text}}</v-list-item-title>
                    </v-list-item>                  
                </v-list>
            </v-menu>          
            <v-btn @click.stop="dialog=true" icon>
                <v-icon>logout</v-icon>
            </v-btn>
        </v-app-bar>
        <!-- app bar end -->

        <!-- sign out dialog start -->
        <v-dialog v-model="dialog" max-width="290" dark persistent>
            <v-card>
                <v-card-title class="headline">Want to leave?</v-card-title>
                <v-card-text>Press Signout to leave</v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="green darken-1" text @click="dialog=false">Stay here</v-btn>
                    <v-btn color="dark" text @click="signOut">Signout</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <!-- sign out dialog end -->

        <!-- navigation drawer start -->
        <v-navigation-drawer 
            v-model="drawer"
            app
            dark
            :color="getAppBarColor">
             
            <v-list>
                <v-list-item two-line>
                    <v-list-item-avatar>
                        <img :src=getSrc>
                    </v-list-item-avatar>

                    <v-list-item-content>
                        <v-list-item-title class="text-capitalize">{{getFullName}}</v-list-item-title>
                        <v-list-item-subtitle><span class="grey--text">{{getUserRole}}</span></v-list-item-subtitle>
                    </v-list-item-content>
                </v-list-item>
                <v-divider></v-divider>
                <v-list-item v-for="item in menus" :key="item.appMenuId" :to="item.url">
                    <v-list-item-icon><v-icon>{{item.iconClass}}</v-icon></v-list-item-icon>
                    <v-list-item-content v-text="item.menuTitle"></v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>
        <!-- navigation drawer end -->
    </nav>
</template>

<script>
import config from '../../config'

export default {
    name:'Navbar',
    data(){
        return{
            fullscreen:false,
            val:'',
            drawer:true,
            linksAdmin: [
                { text: 'Profile', route: '/user/profile' },
                { text: 'Password', route: '/user/password' },
                { text: 'FAQ', route: '/settings/faq' },
                { text: 'Browsing', route: '/user/browseList' },
            ],
            linksOthers: [
                { text: 'Profile', route: '/user/profile' },
                { text: 'Password', route: '/user/password' },
            ],
            dialog:false,
            userInfo:null,
            roleId:null,
            token:null,
            menus:[]
        }
    },
    methods:{
        //app lock
        applyLock(){
            this.$store.dispatch('dashboard/applyOverlay')
        },
        //toggle full screen
        toggle () {
            this.$fullscreen.toggle()
        },
        //app sign out
        signOut:function(){
            this.$store.dispatch('dashboard/signOut')
            .then(()=>{
                this.$router.push('/')
            })
            .catch((err)=>{
                console.log(err)
                this.$router.push('/')
            })
        },
        //app menus
        getAppMenu(){
            const info={
                roleId:this.$store.getters['dashboard/userInfo'].userRoleId
            }
            this.$store.dispatch('dashboard/fetchMenu',info)
            .then((response)=>{
                if(response.status==200){
                    this.menus=response.data
                }
            })
            .catch((err)=>console.log(err))
        },
    },
    computed:{
        //image source for app bar photo
        getSrc:function(){
            return this.$store.getters['dashboard/userInfo'].imagePath==''?'':config.hostname+this.$store.getters['dashboard/userInfo'].imagePath
        },
        //logged in user full name
        getFullName:function(){
            return this.$store.getters['dashboard/userInfo'].fullName
        },
        //app bar & navigation color from settings
        getAppBarColor:function(){
            return this.$store.getters['settings/appBarColor']
        },
        //role display name
        getUserRole:function(){
            return this.$store.getters['dashboard/userInfo'].displayName
        }
    },
    created(){
        if(this.$store.getters['settings/allSettings'].allowFaq==true){
            this.linksOthers.push({ text: 'FAQ', route: '/settings/faq' })
        }
        this.getAppMenu()
    }
}
</script>