<template>
<!-- all users browsing data -->
    <v-container class="px-0">
        <v-btn @click="generatePdf" small outlined>PDF</v-btn>
        <vue-excel-xlsx class="btnExcel" :data="itemsBrowse" :columns="excelColumnsBrowser" :filename="'browse-table'" :sheetname="'browse'">EXCEL</vue-excel-xlsx>
        <v-data-table
            :headers="headersBrowse"
            :items="itemsBrowse"
            class="elevation-1"
        >
        <template v-slot:[`item.logInTime`]="{item}">
            <span>{{formatLoginTime(item)}}</span> 
        </template>
        <template v-slot:[`item.logOutTime`]="{item}">
            <span>{{formatLogoutTime(item)}}</span> 
        </template>
        </v-data-table>
    </v-container>
</template>

<script>
import jsPDF from 'jspdf'
import 'jspdf-autotable'

export default {
    name:'BrowseList',
    data(){
        return{
            headersBrowse:[
                {text:'Name',value:'fullName'},
                {text:'Email',value:'email'},
                {text:'LogIn Time',value:'logInTime'},
                {text:'LogOut Time',value:'logOutTime'},
                {text:'IP',value:'ip'},
                {text:'Browser',value:'browser'},
                {text:'Browser Version',value:'browserVersion'},
                {text:'OS',value:'platform'}
            ],
            itemsBrowse:[],
            excelColumnsBrowser : [
                {label:'Name',field:'fullName'},
                {label:'Email',field:'email'},
                {label:'LogIn Time',field:'logInTime'},
                {label:'LogOut Time',field:'logOutTime'},
                {label:'IP',field:'ip'},
                {label:'Browser',field:'browser'},
                {label:'Browser Version',field:'browserVersion'},
                {label:'OS',field:'platform'}           
           ],
        }
    },
    methods:{
        //export pdf
        generatePdf(){
            const doc = new jsPDF()
            doc.autoTable({
                margin: { top: 10,left:2,right:1 },
                body: this.itemsBrowse,
                columns: [
                    {header:'Name',dataKey:'fullName'},
                    {header:'Email',dataKey:'email'},
                    {header:'LogIn',dataKey:'logInTime'},
                    {header:'LogOut',dataKey:'logOutTime'},
                    {header:'IP',dataKey:'ip'},
                    {header:'Browser',dataKey:'browser'},
                    {header:'Version',dataKey:'browserVersion'},
                    {header:'OS',dataKey:'platform'}
                ],
            })
            doc.save('browse-table.pdf')
        },
        //get all
        initialize(){
            this.$store.dispatch('dashboard/applyLoading')
            this.$store.dispatch('user/fetchBrowseList')
            .then((response)=>{
                this.$store.dispatch('dashboard/cancelLoading')
                this.itemsBrowse=response.data
            })
            .catch((err)=>{
                console.log(err)
            })
        },
        //formatting datetime
        formatLoginTime(item){
            return item.logInTime.replace('T',' ').substring(0,19)
        },
        //formatting datetime
        formatLogoutTime(item){
            return item.logOutTime!=null?item.logOutTime.replace('T',' ').substring(0,19):''
        }
    },
    created(){
        this.initialize()
    }
}
</script>