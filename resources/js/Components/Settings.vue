<template>
    <div>
        <v-card>
            <v-form
                @submit.prevent="updateSettings(settings)"
                enctype="multipart/form-data"
            >
                <v-toolbar color="primary">
                    <v-toolbar-title color="danger" prepend-icon="mdi-cog"
                        >Settings</v-toolbar-title
                    >
                    <v-spacer></v-spacer>
                </v-toolbar>
                <v-list lines="one" subheader>
                    <v-list-subheader>General</v-list-subheader>
                    <v-list-item
                        :title="$t('message.fixedRate')"
                        :subtitle="$t('message.setting1')"
                    >
                        <template v-slot:prepend>
                            <v-checkbox
                                class="mt-4"
                                v-model="settings.is_rate_fixed"
                                :model-value="
                                    settings.is_rate_fixed == '1' ? true : false
                                "
                            ></v-checkbox>
                        </template>
                    </v-list-item>
                    <v-list-item>
                        <v-text-field
                            v-if="settings.is_rate_fixed"
                            v-model="settings.rate"
                            :label="$t('message.fixedRate')"
                        ></v-text-field>
                    </v-list-item>
                    <v-list-item>
                        <v-row>
                            <v-col cols="12" md="6" sm="6">
                                <v-text-field
                            v-model="settings.speciality"
                            :label="$t('message.speciality')"
                        ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="6" sm="6">
                                <v-text-field
                            v-model="settings.name"
                            :label="$t('message.name')"
                        ></v-text-field>
                            </v-col>
                            <v-col cols="12" md="12" sm="12">
                                <v-text-field
                            v-model="settings.address"
                            :label="$t('message.address')"
                        ></v-text-field>
                            </v-col>
                        
                        </v-row>
                    </v-list-item>

                  
                </v-list>
               
                <v-btn
                    size="small"
                    class="ml-5"
                    prepend-icon="mdi-account-lock"
                    @click.prevent="dialogApp = true"
                >
                    {{ $t('message.changePassword') }}
                </v-btn>
                <v-btn
                color="warning"
                class="ml-5"
                    size="small"
                    @click.prevent="dialog = true"
                    prepend-icon="mdi-account-lock"
                >
                   {{ $t('message.changePasswordRec') }}
                </v-btn>
                <v-card-actions>
                    <v-btn type="submit" variant="text" class="mt-4" color="teal-accent-4">
                        {{$t('message.save')}}
                    </v-btn>
                </v-card-actions>
            </v-form>
        </v-card>
        <v-dialog v-model="dialogApp">
            <v-form
                @submit.prevent="updatePassword(password)"
                enctype="multipart/form-data"
            >
                <v-card>
                    <v-card-text>
                        <v-container>
                            <v-text-field
                                v-model="password.currentPassword"
                                type="password"
                                color="primary"
                                :label="$t('message.currentPw')"
                                variant="underlined"
                            ></v-text-field>
                            <v-text-field
                                type="password"
                                v-model="password.newPassword"
                                color="primary"
                                :label="$t('message.newPw')"
                                variant="underlined"
                            ></v-text-field>
                            <v-text-field
                                type="password"
                                v-model="password.passwordConfirmation"
                                color="primary"
                                :rules="passwordConfirmationRules"
                                :label="$t('message.repeatNewPw')"
                                variant="underlined"
                            ></v-text-field>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="blue-darken-1"
                            variant="text"
                            @click="dialogApp = false"
                        >
                            {{ $t("message.close") }}
                        </v-btn>
                        <v-btn type="submit" color="primary" variant="text">
                            {{ $t("message.save") }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
        <v-dialog v-model="dialog">
            <v-form
                @submit.prevent="updateRecPassword(receptionistPassword)"
                enctype="multipart/form-data"
            >
                <v-card>
                    <v-card-text>
                        <v-container>
                            <v-text-field
                                v-model="receptionistPassword.currentPassword"
                                type="password"
                                color="primary"
                                :label="$t('message.currentPw')"
                                variant="underlined"
                            ></v-text-field>
                            <v-text-field
                                type="password"
                                v-model="receptionistPassword.newPassword"
                                color="primary"
                                :label="$t('message.newPw')"
                                variant="underlined"
                            ></v-text-field>
                            <v-text-field
                                type="password"
                                v-model="receptionistPassword.passwordConfirmation"
                                color="primary"
                                :label="$t('message.repeatNewPw')"
                                :rules="recPasswordConfirmationRules"
                                variant="underlined"
                            ></v-text-field>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="blue-darken-1"
                            variant="text"
                            @click="dialog = false"
                        >
                            {{ $t("message.close") }}
                        </v-btn>
                        <v-btn type="submit" color="primary" variant="text">
                            {{ $t("message.save") }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>
    </div>
</template>
<script>
import { reactive, onMounted, ref, computed } from "vue";
import useAuth from "../Composables/auth";
import useSettings from "../Composables/settings";

export default {
    data: () => ({
        dialog: false,
        
    }),
    setup() {
        const { getSettings, settings, updateSettings } = useSettings();
        const { updatePassword, updateRecPassword } = useAuth();
        const fixedRate = ref(false);
        const dialogApp = ref(false);
        const password = reactive({
            currentPassword: "",
            newPassword: "",
            passwordConfirmation: "",
        });

        const receptionistPassword = reactive({
            currentPassword: "",
            newPassword: "",
            passwordConfirmation: "",
        });

        onMounted(() => {
            getSettings();
        });
        const passwordConfirmationRules = computed(() => [
      (value) => {
        if (value !== password.newPassword) {
            return localStorage.getItem("userLanguage") == "en"
          ? "Password doesn't match"
          : "le mot de passe ne correspond pas";
        }
        return true;
      }
    ]);
    const recPasswordConfirmationRules = computed(() => [
      (value) => {
        if (value !== password.newPassword) {
            return localStorage.getItem("userLanguage") == "en"
          ? "Password doesn't match"
          : "le mot de passe ne correspond pas";
        }
        return true;
      }
    ]);

        return {
            getSettings,
            settings,
            fixedRate,
            updateSettings,
            passwordConfirmationRules,
            recPasswordConfirmationRules,
            dialogApp,
            password,
            receptionistPassword,
            updatePassword,
            updateRecPassword

        };
    },
};
</script>
