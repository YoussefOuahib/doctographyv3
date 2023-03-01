<template>
  
    <form @submit.prevent="submitLogin">
    <div class="py-4">
    <v-img
      class="mx-auto mb-4"
      max-width="228"
      src="/images/logo.png"
    ></v-img>

    <v-card
      class="mx-auto pa-12 pb-8"
      elevation="8"
      max-width="448"
      rounded="lg"
    >

      <div class="text-subtitle-1 text-medium-emphasis">Email</div>

      <v-text-field
        density="compact"
        placeholder="Email address"
        prepend-inner-icon="mdi-email-outline"
        variant="outlined"
        v-model="loginForm.email"
      ></v-text-field>

      <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
        Password

      </div>

      <v-text-field
        :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
        :type="visible ? 'text' : 'password'"
        density="compact"
        v-model="loginForm.password"
        placeholder="Enter your password"
        prepend-inner-icon="mdi-lock-outline"
        variant="outlined"
        @click:append-inner="visible = !visible"
      ></v-text-field>

      <v-card
        color="surface-variant"
        variant="tonal"
      >
      
      </v-card>
      <v-checkbox label="Remember me" v-model="loginForm.remember"></v-checkbox>

      <v-btn
        block
        class="mb-4"
        color="primary"
        size="large"
        type="submit"
        variant="tonal"
      >
        Log In
      </v-btn>
    </v-card>
    </div>
    </form>
</template>
<script>
import useAuth from "../../Composables/auth";
import { onMounted, ref } from "vue";

export default {
    data: () => ({
      visible: false,
    }),
    setup() {
        const { loginForm, submitLogin } = useAuth();
        onMounted(() => {
          if(JSON.parse(localStorage.getItem('loggedIn'))) {
        next('/patients');
    }
        });
        return { loginForm, submitLogin };
    },
};
</script>
