<template>
    <div>
        <v-text-field
            return-object
            :counter="25"
            clearable
            append-inner-icon="mdi-magnify"
            :label="$t('message.searchByName')"
            required
            v-model="searching"
        ></v-text-field>
        <v-btn color="primary" class="ml-2" prepend-icon="mdi-hospital-box">
            {{ $t("message.addAct") }}

            <v-dialog v-model="dialog" activator="parent">
                <v-form
                    @submit.prevent="storeConditions(condition)"
                    enctype="multipart/form-data"
                >
                    <v-card>
                        <v-card-text>
                            <v-container>
                                <v-row>
                                   
                                    <v-col cols="12" sm="12" md="12">
                                        <v-text-field
                                            :label="$t('message.addAct') + '*'"
                                            :counter="100"
                                            :rules="conditionRules"
                                            required
                                            v-model="condition.name"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
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
                            <v-btn
                                color="blue-darken-1"
                                variant="text"
                                @click="dialog = false"
                                type="submit"
                            >
                            {{ $t("message.save") }}
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-form>
            </v-dialog>
        </v-btn>
        <v-table>
            <thead>
                <tr>
                    <th>{{$t('message.act')}}</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="condition in conditions.data" :key="condition.id">
                    <td>
                        <v-chip
                            color="darkPrimary"
                            text-color="white"
                            prepend-icon="mdi-hospital-box"
                            class="font-weight-medium"
                        >
                            {{ condition.name }}
                        </v-chip>
                    </td>
            

                    <td>
                        <v-btn
                            color="secondary"
                            class="ml-2"
                            icon="mdi-clipboard-edit"
                            size="x-small"
                            @click.prevent="getCondition(condition.id)"
                        >
                        <v-icon> </v-icon>
                        <v-dialog
                                v-model="dialogCondition"
                                activator="parent"
                            >
                                <v-form
                                    @submit.prevent="updateCondition(myCondition)"
                                    enctype="multipart/form-data"
                                >
                                    <v-card>
                                        <v-card-text>
                                            <v-container>
                                                <v-row>
                                                    <v-col cols="12" sm="6" md="6">
                                        <v-text-field
                                            :label=" $t('message.act')"
                                            :counter="100"
                                            :rules="conditionRules"
                                            required
                                            v-model="myCondition.name"
                                        ></v-text-field>
                                    </v-col>
                                    
                                                </v-row>
                                            </v-container>
                                        </v-card-text>
                                        <v-card-actions>
                                            <v-btn
                                                type="submit"
                                                color="darkPrimary"
                                            >
                                            {{ $t("message.save") }}
                                            </v-btn>
                                            <v-btn
                                                color="danger"
                                                @click="dialogCondition = false"
                                                > {{ $t("message.close") }}</v-btn
                                            >
                                        </v-card-actions>
                                    </v-card>
                                </v-form>
                            </v-dialog>
                            </v-btn>
                        <v-btn
                            color="danger"
                            class="ml-2"
                            icon="mdi-delete"
                            @click="deleteCondition(condition.id)"
                            size="x-small"
                        >
                        </v-btn>
                    </td>
                </tr>
            </tbody>
        </v-table>

    </div>
</template>
<script>
import { reactive, onMounted, ref, watch } from "vue";
import useConditions from "../Composables/conditions";
export default {
    data: () => ({
        dialog: false,
        conditionRules: [
            value => {
                if (value) return true

                return localStorage.getItem('userLanguage') == 'en' ? 'Name is required' : "Nom de l'acte est requis"
            },
            value => {
                if (value?.length <= 100) return true

                return localStorage.getItem('userLanguage') == 'en' ? 'Act must be less than 100 characters.' : "L'acte médical doit contenir moins de 45 caractères."
            },
            value => {
                if (/^[a-zA-Z\s]+$/.test(value)) return true

                return localStorage.getItem('userLanguage') == 'en' ? 'Act can not contain digits or special characters' : "L'acte médicale ne peut pas contenir de chiffres ou de caractères spéciaux"
            },

        ],
    }),
    setup() {
        const searching = ref("");
        const dialogCondition = ref(false);

        const condition = reactive({
            name: "",
        });
 
        watch(searching, (current, previous) => {
            searchConditions(current);
        });

        const {
            conditions,
            getConditions,
            getCondition,
            updateCondition,
            myCondition,
            storeConditions,
            searchConditions,
            deleteCondition,
            patients,
            
        } = useConditions();
        onMounted(getConditions);
        return {
            condition,
            conditions,
            patients,
            storeConditions,
            updateCondition,
            searching,
            getCondition,
            myCondition,
            getConditions,
            searchConditions,
            deleteCondition,
            dialogCondition
        };
    },
};
</script>
