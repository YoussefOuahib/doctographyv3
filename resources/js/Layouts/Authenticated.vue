<template>
  <v-app id="inspire">
    <v-navigation-drawer v-model="drawer" app>
      <v-sheet class="pa-2 text-center">
        <v-img class="mx-auto mb-4 mt-4" max-width="228" src="/images/logo.png"></v-img>
        <div>Hi, {{ user.name }}</div>
        <v-progress-circular
          class="mt-4"
          :size="50"
          :width="5"
          indeterminate
          size="small"
          color="darkPrimary"
        >
          {{ waiting }}
        </v-progress-circular>

        <br />
        <v-btn
          size="x-small"
          class="mt-2 mr-2"
          icon="mdi-account-multiple-remove"
          color="danger"
          variant="text"
          v-if="can('waiting.decrement')"
          @click.prevent="decrement"
        ></v-btn>
        <v-btn
          size="x-small"
          class="mt-2"
          icon="mdi-refresh"
          variant="text"
          color="primary"
          v-if="can('waiting.refresh')"
          @click.prevent="show"
        ></v-btn>
        <v-btn
          size="x-small"
          class="mt-2"
          icon="mdi-power-cycle"
          variant="text"
          color="warning"
          @click.prevent="reset"
        ></v-btn>
        <v-btn
          size="x-small"
          class="mt-2"
          icon="mdi-account-multiple-plus"
          color="primary"
          variant="text"
          v-if="can('waiting.increment')"
          @click.prevent="increment"
        ></v-btn>
      </v-sheet>
      <v-spacer></v-spacer>

      <v-divider></v-divider>

      <v-list>
        <v-list-item v-if="can('settings.manage')" :to="{ name: 'home' }">
          <template v-slot:prepend>
            <v-icon color="darkPrimary" icon="mdi-home"></v-icon>
          </template>

          <v-list-item-title> {{ $t("message.home") }}</v-list-item-title>
        </v-list-item>
        <v-list-item :to="{ name: 'patients' }">
          <template v-slot:prepend>
            <v-icon color="darkPrimary" icon="mdi-account-injury"></v-icon>
          </template>

          <v-list-item-title>Patiens</v-list-item-title>
        </v-list-item>
        <!-- <v-list-item :to="{ name: 'list' }">
          <template v-slot:prepend>
            <v-icon color="darkPrimary" icon="mdi-account-group"></v-icon>
          </template>

          <v-list-item-title>Waiting list</v-list-item-title>
        </v-list-item> -->

        <v-list-item v-if="can('conditions.manage')" :to="{ name: 'conditions' }">
          <template v-slot:prepend>
            <v-icon color="darkPrimary" icon="mdi-hospital-box"></v-icon>
          </template>

          <v-list-item-title>{{ $t("message.acts") }}</v-list-item-title>
        </v-list-item>

        <v-list-item :to="{ name: 'appointments' }">
          <template v-slot:prepend>
            <v-icon color="darkPrimary" icon="mdi-clipboard-text-clock"></v-icon>
          </template>

          <v-list-item-title>{{ $t("message.appointments") }}</v-list-item-title>
        </v-list-item>

        <v-list-item :to="{ name: 'sessions' }">
          <template v-slot:prepend>
            <v-icon color="darkPrimary" icon="mdi-calendar-month"></v-icon>
          </template>

          <v-list-item-title>{{ $t("message.sessions") }}</v-list-item-title>
        </v-list-item>

        <v-list-item v-if="can('settings.manage')" :to="{ name: 'settings' }">
          <template v-slot:prepend>
            <v-icon color="darkPrimary" icon="mdi-cog"></v-icon>
          </template>

          <v-list-item-title>{{ $t("message.settings") }}</v-list-item-title>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <v-app-bar>
      <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

      <template v-slot:append>
        <v-select
          class="mt-3 mr-2"
          :items="$i18n.availableLocales"
          v-model="$i18n.locale"
          density="compact"
          @update:modelValue="changeLanguage"
          menu
        >
        </v-select>

        <v-btn
          class="my-auto mr-2"
          @click="toggleTheme"
          color="primary"
          icon="mdi-brightness-6"
          size="large"
        >
        </v-btn>

        <v-btn class="my-auto">
          <v-avatar
            id="menu-activator"
            icon="mdi-doctor"
            size="small"
            color="primary"
          ></v-avatar>
        </v-btn>
        <v-menu activator="#menu-activator">
          <v-list>
            <v-list-item>
              <v-list-item-title>
                <v-btn @click="logout">{{ $t("message.logOut") }}</v-btn>
              </v-list-item-title>
            </v-list-item>
          </v-list>
        </v-menu>
      </template>
    </v-app-bar>

    <v-main>
      <router-view></router-view>
    </v-main>
  </v-app>
</template>

<script>
import axios from "axios";
import i18n from "../messages";
import { onMounted, computed, ref, watch } from "vue";
import useWaiting from "../Composables/waiting";
import useAuth from "../Composables/auth";
import { useTheme } from "vuetify";
import { useAbility } from "@casl/vue";

export default {
  data: () => ({
    drawer: null,
  }),
  setup() {
    const theme = useTheme();
    const { user, logout } = useAuth();
    const { show, increment, decrement, waiting, reset } = useWaiting();
    const waitingValue = ref(0);
    const number = ref(0);

    const toggleTheme = () => {
      theme.global.name.value = theme.global.current.value.dark
        ? "myLightTheme"
        : "myDarkTheme";
      localStorage.setItem("userTheme", theme.global.name.value);
    };

    const changeLanguage = (value) => {
      localStorage.setItem("userLanguage", value);
      axios.post('/api/language', { locale: value })
        .then(response => {
          console.log('Language stored successfully')
        })
        .catch(error => {
          console.error('Error storing language:', error)
        })
    };

    // a computed ref
    // const increase = computed(() => {
    //   return waitingValue.value + 1;
    // });
    // function increment() {
    //   number.value++;
    // }
    onMounted(show);

    const { can } = useAbility();

    return {
      waiting,
      waitingValue,
      increment,
      decrement,
      show,
      reset,
      number,
      changeLanguage,
      user,
      logout,
      theme,
      toggleTheme,
      can,
    };
  },
  computed: {
    currentPageTitle() {
      return this.$route.meta.title;
    },
  },
};
</script>
