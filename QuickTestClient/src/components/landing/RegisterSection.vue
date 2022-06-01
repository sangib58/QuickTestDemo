<template>
<!--landing page registration start -->
  <section id="register">
    <v-container fluid>
      <v-row align="center" justify="center">
        <v-col cols="10">
          <v-row align="center" justify="center">
            <v-col sm="4" class="hidden-xs-only">
              <v-img src="@/assets/img/landing/ill2.svg" class="d-block ml-auto mr-auto" max-width="350px" />
            </v-col>
            <v-col cols="12" sm="8" class="white--text text-left">
              <h1 class="font-weight-light display-2 mb-2">Get Registered</h1>
              <h1 class="font-weight-light">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Iste ex
                animi quod laboriosam vel blanditiis labore alias, aliquid,
                tempora repellendus non.
              </h1>
              <v-btn rounded outlined large color="white" class="mt-4" @click="switchSignIn">
                <v-icon class="mr-2">
                  app_registration
                </v-icon>
                Register
              </v-btn>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-container>
  </section>
<!--landing page registration end -->
</template>

<style scoped>
#register {
  background-image: url("~@/assets/img/landing/bgDownload.jpg");
  background-attachment: fixed;
  background-repeat: no-repeat;
  background-size: cover;
  background-position: center;
  height: 500px;
}

#register .container,
#register .row {
  height: 100%;
}
</style>

<script>
export default {
  methods:{
    //switch to sign in
    switchSignIn(){
      this.$router.push({name:'SignIn'})
    }
  }
}
</script>