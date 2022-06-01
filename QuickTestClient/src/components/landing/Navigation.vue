<template>
  <div>
  <!--landing page navigation for mobile device start -->
    <v-navigation-drawer
      v-model="drawer"
      app
      temporary
      dark
      src="@/assets/img/landing/bgMain2.jpg"
    >
      <v-list>
        <v-list-item>
          <v-list-item-avatar>
            <img src="@/assets/img/landing/logo-quick-test.png" alt="Logo" />
          </v-list-item-avatar>
          <v-list-item-content>
            <v-list-item-title class="title">Self</v-list-item-title>
            <v-list-item-subtitle>Assess</v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-divider />

      <v-list dense>
        <v-list-item
          v-for="([icon, text, link], i) in items"
          :key="i"
          link
          @click="$vuetify.goTo(link)"
        >
          <v-list-item-icon class="justify-center">
            <v-icon>{{ icon }}</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title class="subtitile-1">{{
              text
            }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>
  <!--landing page navigation for mobile device end -->

  <!--landing page app bar start -->
    <v-app-bar
      app
      dark
      :color="color"
      :flat="flat"
      :class="{ expand: flat }"
    >
      <v-toolbar-title>
        <v-img src="@/assets/img/landing/logo-quick-test.png" max-width="40px" />
      </v-toolbar-title>
      <v-spacer />
      <v-app-bar-nav-icon
        v-if="isXs"
        @click.stop="drawer = !drawer"
        class="mr-3"       
      />
      <div v-else>
        <v-btn text @click="$vuetify.goTo('#hero')">
          <span class="mr-2">Home</span>
        </v-btn>
        <v-btn text @click="$vuetify.goTo('#about')">
          <span class="mr-2">About</span>
        </v-btn>
        <v-btn text @click="$vuetify.goTo('#register')">
          <span class="mr-2">Register</span>
        </v-btn>
        <v-btn text @click="$vuetify.goTo('#feature')">
          <span class="mr-2">Features</span>
        </v-btn>
        <v-btn text @click="$vuetify.goTo('#contact')">
          <span class="mr-2">Contact Us</span>
        </v-btn>
        <v-btn rounded outlined text @click="switchSignIn">
          <span class="mr-2">Sign In</span>
        </v-btn>
      </div>
    </v-app-bar>
  <!--landing page app bar end -->
  </div>
</template>

<style scoped>
.v-toolbar {
  transition: 0.6s;
}

.expand {
  height: 80px !important;
  padding-top: 10px;
}
</style>

<script>
export default {
  data: () => ({
    drawer: null,
    isXs: false,
    items: [
      ["mdi-home-outline", "Home", "#hero"],
      ["mdi-information-outline", "About", "#about"],
      ["app_registration", "Register", "#register"],
      ["list", "Featers", "#feature"],
      ["mdi-email-outline", "Contact", "#contact"],
    ],
  }),
  props: {
    color: String,
    flat: Boolean,
  },
  methods: {
    //screen width check
    onResize() {
      this.isXs = window.innerWidth < 850;
    },
    //switch to sign in
    switchSignIn(){
      this.$router.push({name:'SignIn'})
    }
  },

  watch: {
    isXs(value) {
      if (!value) {
        if (this.drawer) {
          this.drawer = false;
        }
      }
    },
  },
  mounted() {
    this.onResize();
    window.addEventListener("resize", this.onResize, { passive: true });
  },
};
</script>
