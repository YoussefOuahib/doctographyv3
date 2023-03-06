<template>
  <div>
    <v-autocomplete return-object :counter="25" clearable append-inner-icon="mdi-magnify"
      :label="$t('message.searchByName')" :items="allPatients.data"
      item-title="full_name" item-value="id" v-model="searching" @update:modelValue="searchPatients"></v-autocomplete>
    <v-btn color="primary" class="ml-2" prepend-icon="mdi-account-plus">
      {{ $t("message.addPatient") }}

      <v-dialog v-model="dialog" activator="parent">
        <v-form @submit.prevent="storePatients(patient)" enctype="multipart/form-data">
          <v-card>
            <v-card-text>
              <v-container>
                <v-row>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field
                      :counter="8"
                      density="compact"
                      label="CIN"
                      hint="EE82XXXX"
                      required
                      v-model="patient.cin"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field
                      required
                      density="compact"
                      :label="$t('message.fullName') + '*'"
                      v-model="patient.full_name"
                      :rules="nameRules"
                      :counter="45"
                      hint="Hakim Ziyech"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-radio-group density="compact" v-model="patient.gender" inline>
                      <v-radio color="primary" label="M" value="m"></v-radio>
                      <v-radio label="F" value="f" color="primary"></v-radio>
                    </v-radio-group>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-text-field
                      density="compact"
                      :counter="10"
                      :label="$t('message.phone') + '*'"
                      hint="066125XXXX"
                      :rules="phoneRules"
                      v-model="patient.phone"
                      required
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <Datepicker
                      density="compact"
                      v-model="patient.birth_date"
                      :enableTimePicker="false"
                      :flow="flow"
                      :max-date="new Date()"
                      :placeholder="$t('message.selectBirthDate')"
                    ></Datepicker>
                  </v-col>
                  <v-col cols="12" sm="6" md="4">
                    <v-select
                      clearable
                      :items="insurances"
                      v-model="patient.insurance"
                      :label="$t(`message.insurance`)"
                      density="compact"
                    ></v-select>
                  </v-col>
                </v-row>
              </v-container>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="blue-darken-1" variant="text" @click="dialog = false">
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
    <v-btn
      color="darkPrimary"
      class="ml-2"
      @click="dialogInvoice = true"
      prepend-icon="mdi-printer"
      v-if="can('invoice.generate')"
      >{{ $t("message.generateInvoice") }}</v-btn
    >

    <v-btn
      v-if="can('prescription.generate')"
      color="secondary"
      @click="dialogPrescription = true"
      class="ml-2"
      prepend-icon="mdi-note"
      >{{ $t("message.generatePrescription") }}</v-btn
    >
    <v-table>
      <thead>
        <tr>
          <th>{{ $t("message.name") }}</th>
          <th>CIN</th>
          <th>Age</th>
          <th>{{ $t("message.phone") }}</th>
          <th>{{ $t("message.firstAppointment") }}</th>
          <th>{{ $t("message.insurance") }}</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="patient in patients.data" :key="patient.id">
          <td>
            <v-chip
              :color="patient.gender == 'm' ? 'secondary-darken-1' : 'pink'"
              text-color="white"
              :prepend-icon="
                patient.gender == 'm' ? 'mdi-gender-male' : 'mdi-gender-female'
              "
              class="font-weight-medium"
            >
              {{ patient.full_name }}
            </v-chip>
          </td>
          <td>
            <v-chip color="darkPrimary" prepend-icon="mdi-account-details">{{
              patient.cin == "" ? "aucun" : patient.cin
            }}</v-chip>
          </td>
          <td>
            <v-chip
              color="darkPrimary"
              text-color="white"
              prepend-icon="mdi-cake-variant"
              class="font-weight-medium"
            >
              {{ patient.age }} {{ $t("message.yearsOld") }}
            </v-chip>
          </td>

          <td>
            <v-chip
              color="darkPrimary"
              text-color="white"
              prepend-icon="mdi-cellphone"
              class="font-weight-medium"
            >
              {{ patient.phone }}
            </v-chip>
          </td>

          <td>
            <v-chip
              color="darkPrimary"
              prepend-icon="mdi-calendar-range"
              class="font-weight-medium"
            >
              {{ patient.first_app }}
            </v-chip>
          </td>
          <td>
            <v-chip
              color="darkPrimary"
              prepend-icon="mdi-hospital-building"
              class="font-weight-medium"
            >
              {{ patient.insurance }}
            </v-chip>
          </td>

          <td>
            <v-btn
              @click.stop="dialogApp[patient.id] = true"
              color="warning"
              class="ml-2"
              icon="mdi-calendar-range"
              size="x-small"
            >
            </v-btn>
            <v-btn
              color="secondary"
              icon="mdi-calendar-edit"
              size="x-small"
              class="ml-2"
              @click.prevent="getPatient(patient.id)"
              rounded
            >
              <v-icon> </v-icon>
              <v-dialog v-model="dialogPatient" activator="parent">
                <v-form
                  @submit.prevent="updatePatient(myPatient)"
                  enctype="multipart/form-data"
                >
                  <v-card>
                    <v-card-text>
                      <v-container>
                        <v-row>
                          <v-col cols="12" sm="6" md="4">
                            <v-text-field
                              density="compact"
                              :rules="cinRules"
                              label="CIN*"
                              hint="EE82XXXX"
                              required
                              v-model="myPatient.cin"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="6" md="4">
                            <v-text-field
                              density="compact"
                              :rules="nameRules"
                              :label="$t('message.fullName') + '*'"
                              v-model="myPatient.full_name"
                              hint="Hakim Ziyech"
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="6" md="4">
                            <v-radio-group
                              density="compact"
                              v-model="myPatient.gender"
                              inline
                            >
                              <v-radio color="primary" label="M" value="m"></v-radio>
                              <v-radio label="F" value="f" color="primary"></v-radio>
                            </v-radio-group>
                          </v-col>
                          <v-col cols="12" sm="6" md="4">
                            <v-text-field
                              :rules="phoneRules"
                              density="compact"
                              :label="$t('message.phone') + '*'"
                              hint="066125XXXX"
                              v-model="myPatient.phone"
                              required
                            ></v-text-field>
                          </v-col>
                          <v-col cols="12" sm="6" md="4">
                            <v-select
                              :items="insurances"
                              v-model="myPatient.insurance"
                              :label="$t(`message.insurance`)"
                              density="compact"
                            ></v-select>
                          </v-col>
                          <v-col cols="12" sm="6" md="4">
                            <Datepicker
                              density="compact"
                              v-model="myPatient.birth_date"
                              :enableTimePicker="false"
                              :flow="flow"
                              :placeholder="$t('message.selectBirthDate')"
                            >
                            </Datepicker>
                          </v-col>
                        </v-row>
                      </v-container>
                    </v-card-text>
                    <v-card-actions>
                      <v-btn type="submit" color="darkPrimary">
                        {{ $t("message.save") }}
                      </v-btn>
                      <v-btn color="danger" @click="dialogPatient = false">
                        {{ $t("message.close") }}</v-btn
                      >
                    </v-card-actions>
                  </v-card>
                </v-form>
              </v-dialog>
            </v-btn>
            <v-btn
              color="danger"
              class="ml-2"
              icon="mdi-account-minus"
              size="x-small"
              v-if="can('patients.delete')"
              @click.prevent="deletePatient(patient.id)"
            >
            </v-btn>
          </td>
          <v-dialog v-model="dialogApp[patient.id]" :key="patient.id">
            <v-card>
              <v-timeline>
                <v-timeline-item
                  v-for="app in patient.appointments"
                  :key="app.id"
                  size="large"
                  dot-color="primary"
                >
                  <div>
                    <v-chip
                      color="darkPrimary"
                      prepend-icon="mdi-calendar-range"
                      class="p-2 font-weight-bold"
                      size="normal"
                      >{{ app.date }}
                    </v-chip>
                    <v-chip
                      v-if="app.act"
                      color="danger"
                      prepend-icon="mdi-hospital-box"
                      class="font-weight-medium"
                      >{{ app.act }}</v-chip
                    >
                    <v-chip
                      v-if="app.medical_treatment"
                      class="font-weight-bold"
                      prepend-icon="mdi-bottle-tonic-plus"
                      color="warning"
                    >
                      {{ app.medical_treatment }}
                    </v-chip>
                    <v-chip
                      color="info"
                      prepend-icon="mdi-alert"
                      class="ml-2"
                      v-if="app.note"
                    >
                      {{ app.note }}</v-chip
                    >
                    <v-btn
                      v-if="app.images"
                      @click.stop="dialogImage[app.id] = true"
                      class="ma-2"
                      size="x-small"
                      color="purple"
                      icon="mdi-image-multiple"
                    ></v-btn>

                    <br />
                    <v-chip
                      color="darkPrimary"
                      prepend-icon="mdi-bed"
                      class="font-weight-medium ml-2"
                      size="small"
                      >{{ app.rate }} MAD</v-chip
                    >
                    <br />
                  </div>
                  <v-dialog v-model="dialogImage[app.id]">
                    <v-card>
                      <div class="text-center">
                        <img
                          v-for="item in app.images"
                          :src="'/images/' + item.image"
                          class="ml-2"
                          :width="120"
                        />
                      </div>
                    </v-card>
                  </v-dialog>
                </v-timeline-item>
              </v-timeline>
              <v-card-actions>
                <v-btn
                  color="darkPrimary"
                  block
                  @click.stop="dialogApp[patient.id] = false"
                >
                  {{ $t("message.close") }}</v-btn
                >
              </v-card-actions>
            </v-card>
          </v-dialog>
        </tr>
      </tbody>
    </v-table>
    <v-pagination
      v-model="current_page"
      :length="last_page"
      color="primary"
      @update:modelValue="onPageChange"
    ></v-pagination>
    <v-dialog v-model="dialogInvoice">
      <v-card>
        <v-card-text>
          <v-container>
            <v-row>
              <v-col cols="12" sm="6" md="6">
                <v-autocomplete
                  v-model="invoice.patient_id"
                  :items="patients.data"
                  label="Patient"
                  item-title="full_name"
                  item-value="id"
                  @update:modelValue="getPatient"
                >
                </v-autocomplete>
              </v-col>
              <v-col cols="12" sm="6" md="6">
                <v-text-field :label="$t('message.billTo')" v-model="invoice.billTo">
                </v-text-field>
              </v-col>
              <v-col cols="12" sm="12" md="12">
                <v-text-field :label="$t(`message.address`)" v-model="invoice.address">
                </v-text-field>
              </v-col>
              <v-col cols="12" sm="12" md="12">
                <v-table>
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Patient</th>
                      <th>Act</th>
                      <th>Rate</th>
                      <th>Date</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="app in myPatient.appointments" :key="app.id">
                      <td>
                        <v-checkbox
                          v-model="selectedAppointments"
                          :value="app"
                          class="mt-5"
                        ></v-checkbox>
                      </td>
                      <td>
                        {{ app.patient_name }}
                      </td>
                      <td>
                        <v-chip color="danger">
                          {{ app.act }}
                        </v-chip>
                      </td>
                      <td>{{ app.rate }} MAD</td>
                      <td>
                        {{ app.date }}
                      </td>
                    </tr>
                  </tbody>
                </v-table>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            @click="
              exportToPDF(
                myPatient.full_name,
                myPatient.id,
                settings,
                selectedAppointments
              )
            "
            color="darkPrimary"
          >
            Print
          </v-btn>
          <v-btn color="blue-darken-1" variant="text" @click="dialogInvoice = false">
            {{ $t("message.close") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-dialog v-model="dialogPrescription">
      <v-card>
        <v-card-text>
          <v-row>
            <v-col cols="12" sm="12" md="12">
              <v-autocomplete
                v-model="prescription.patient"
                :items="patients.data"
                label="Patient"
                item-title="full_name"
                item-value="full_name"
                @update:modelValue="count == 0 ? count++ : null"
              >
              </v-autocomplete>
            </v-col>

            <v-col
              v-if="prescription.patient"
              v-for="key in count"
              :key="key"
              cols="12"
              sm="10"
              md="10"
            >
              <v-text-field
                :prepend-icon="count > 0 ? 'mdi-file-document-remove' : null"
                @click:prepend="count > 0 ? count-- : null"
                append-icon="mdi-file-document-plus"
                @click:append="count++"
                v-model="prescription.medication[key]"
              >
              </v-text-field>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="exportPrescription(prescription, settings)" color="darkPrimary">
            Print
          </v-btn>
          <v-btn color="blue-darken-1" variant="text" @click="dialogPrescription = false">
            {{ $t("message.close") }}
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script>
import { reactive, onMounted, ref, watch } from "vue";
import usePatients from "../Composables/patients";
import useConditions from "../Composables/conditions";
import useSettings from "../Composables/settings";
import { useAbility } from "@casl/vue";
import Datepicker from "@vuepic/vue-datepicker";
import pdfMake from "pdfmake/build/pdfmake";
import * as vfsFonts from 'pdfmake/build/vfs_fonts';

export default {
  components: { Datepicker },
  data: () => ({
    count: 0,
    dialog: false,
    dialogPatient: false,
    dialogInvoice: false,
    dialogPrescription: false,
    dialogApp: {},
    dialogImage: {},
    nameRules: [
      (value) => {
        if (value) return true;

        return localStorage.getItem("userLanguage") == "en"
          ? "Name is required"
          : "Le nom est requis";
      },
      (value) => {
        if (value?.length <= 45) return true;

        return localStorage.getItem("userLanguage") == "en"
          ? "Name must be less than 45 characters."
          : "Le nom doit contenir moins de 45 caractères.";
      },
      (value) => {
        if (/^[a-zA-Z\s]+$/.test(value)) return true;

        return localStorage.getItem("userLanguage") == "en"
          ? "Name can not contain digits or special characters"
          : "Le nom ne peut pas contenir de chiffres ou de caractères spéciaux";
      },
    ],
    cinRules: [
      (value) => {
        if (value) return true;

        return localStorage.getItem("userLanguage") == "en"
          ? "CIN is required"
          : "CIN est requis";
      },
      (value) => {
        if (value?.length <= 8) return true;

        return localStorage.getItem("userLanguage") == "en"
          ? "CIN must be less than 8 characters."
          : "CIN doit comporter moins de 8 caractères.";
      },
      (value) => {
        if (/^[a-zA-Z0-9]+$/.test(value)) return true;

        return localStorage.getItem("userLanguage") == "en"
          ? "CIN cannot contains special characters"
          : "CIN ne peut pas contenir de caractères spéciaux";
      },
    ],
    phoneRules: [
      (value) => {
        if (value) return true;

        return localStorage.getItem("userLanguage") == "en"
          ? "Phone Number is required."
          : "Le numéro de téléphone est requis";
      },
      (value) => {
        if (value?.length == 10) return true;

        return localStorage.getItem("userLanguage") == "en"
          ? "Phone must have 10 digits"
          : "Le téléphone doit avoir 10 chiffres";
      },
      (value) => {
        if (/^[0-9]+$/.test(value)) return true;

        return localStorage.getItem("userLanguage") == "en"
          ? "Phone cannot contains special characters or letters"
          : "Le téléphone ne peut pas contenir de caractères spéciaux ou de lettres";
      },
    ],
  }),
  setup() {
    const searching = ref("");
    const { getSettings, settings } = useSettings();
    const selectedAppointments = ref([]);
    const patient = reactive({
      full_name: "",
      phone: "",
      cin: "",
      gender: "m",
      birth_date: new Date(),
      insurance: "",
    });
    const insurances = ref([
      'Aucune',
      "CNSS",
      "CNOPS",
      "OCP",
      "FAR",
      localStorage.getItem("userLanguage") == "en" ? "Other" : "Autre",
    ]);
    const invoice = reactive({
      patient_id: "",
      billTo: "",
      address: "",
    });
    const prescription = reactive({
      patient: "",
      medication: [],
    });

    const { can } = useAbility();
    const flow = ref(["year", "month", "calendar"]);
    const { conditions, getConditions } = useConditions();

    const {
      allPatients,
      patients,
      myPatient,
      getPatients,
      getPatient,
      updatePatient,
      storePatients,
      searchPatients,
      deletePatient,
      current_page,
      last_page,
    } = usePatients();

    const exportToPDF = (name, id, settings, apps) => {
      let data = [];
      let rows = [["Examen", "Montant", "Date"]];
      let prices = [];
      let total = 0;
      const current = new Date();
      const currentDate = `${current.getDate()}/${
        current.getMonth() + 1
      }/${current.getFullYear()}`;

      apps.forEach(function (value, key) {
        data.push({
          act: value.act,
          rate: value.rate,
          date: value.date,
        });
        prices.push(value.rate);
      });
      total = prices.reduce(function (previousValue, currentValue) {
        return previousValue + currentValue;
      });

      data.forEach(function (value, key) {
        rows.push([value.act, value.rate + " MAD", value.date]);
      });

      const documentDefinition = {
        content: [
       
          {
            alignment: "right",
            text: "Le: " + currentDate,
            fontSize: 10,
            margin: [0, 2],
          },
          {
            alignment: "left",
            text: settings.name,
            fontSize: 12,
            bold: true,
            margin: [0, 4],
          },
          {
            alignment: "left",
            text: settings.speciality,
            fontSize: 12,
            margin: [0, 4],
          },
          {
            alignment: "left",
            text: settings.diplome,
            fontSize: 12,
            margin: [0, 4],
          },
          {
            alignment: "left",
            text: "INP: " + settings.inp,
            fontSize: 10,
            margin: [0, 2],
          },
          {
            alignment: "left",
            text: "Patente: " + settings.patente,
            fontSize: 12,
            margin: [0, 4],
          },
          {
            alignment: "left",
            text: "IF: " + settings.if,
            fontSize: 12,
            margin: [0, 4],
          },
          {
            alignment: "left",
            text: "CNSS: " + settings.cnss,
            fontSize: 12,
            margin: [0, 4],
          },
         
          {
            alignment: "left",
            text: "ICE: " + settings.ice,
            fontSize: 10,
            margin: [0, 2],
          },

          {
            alignment: "right",
            text: name,
            fontSize: 14,
            bold: true,
            margin: [0, 10],
          },
          {
            alignment: "right",
            text: invoice.billTo,
            fontSize: 12,
            margin: [0, 2],
          },
          {
            alignment: "right",
            text: invoice.address,
            fontSize: 12,
            margin: [0, 2, 2, 25],
          },
          {
            alignment: "center",
            text: "Facture: " + id + Math.floor(Math.random() * 100 + 1),
            fontSize: 14,
            bold: true,
            margin: [0, 50],
          },
          {
            table: {
              margin: [50, 15, 15, 25],
              widths: ["50%", "25%", "25%"],
              heights: [25, 25],

              body: rows,
            },
          },
          {
            alignment: "left",
            text: "Total: " + total + " MAD",
            fontSize: 12,
            margin: [5, 15, 2, 10],
          },
        ],
      };
      pdfMake.fonts = {
        Roboto: {
          normal: "Roboto-Regular.ttf",
          bold: "Roboto-Medium.ttf",
          italics: "Roboto-Italic.ttf",
          bolditalics: "Roboto-Italic.ttf",
        },
      };
      pdfMake.createPdf(documentDefinition).open();
    };
    const exportPrescription = (prescription, settings) => {
      let data = [];
      let rows = [["Examen", "Montant", "Date"]];
      let prices = [];
      let total = 0;
      const current = new Date();
      const currentDate = `${current.getDate()}/${
        current.getMonth() + 1
      }/${current.getFullYear()}`;

      prescription.medication.forEach(function (value, key) {
        data.push(value + "\n \n");
      });
      // total = prices.reduce(function (previousValue, currentValue) {
      //     return previousValue + currentValue;
      // });

      // data.forEach(function (value, key) {
      //     rows.push([value.condition, value.rate + " MAD", value.date]);
      // });

      const documentDefinition = {
        content: [
          {
            alignment: "left",
            text: settings.name,
            fontSize: 14,
            margin: [0, 70, 0, 20],
          },
          {
            alignment: "left",
            text: settings.speciality,
            fontSize: 14,
            margin: [0, 4],
          },
          {
            alignment: "left",
            text: settings.address,
            fontSize: 12,
            margin: [0, 10, 0, 25],
          },

          {
            alignment: "center",
            text: "Marrakech le: " + currentDate,
            fontSize: 12,
            margin: [0, 20],
          },

          {
            alignment: "center",
            text: prescription.patient,
            fontSize: 12,
            margin: [0, 20],
          },

          {
            margin: [50, 15, 15, 30],
            fontSize: 15,
            alignment: "left",
            text: data,
          },
        ],
      };
      pdfMake.fonts = {
        Roboto: {
          normal: "Roboto-Regular.ttf",
          bold: "Roboto-Medium.ttf",
          italics: "Roboto-Italic.ttf",
          bolditalics: "Roboto-Italic.ttf",
        },
      };
      pdfMake.createPdf(documentDefinition).open();
    };
    onMounted(() => {
      getSettings();
      getPatients();
    });

    const onPageChange = (current_page) => {
      getPatients(current_page);
    };


    return {
      allPatients,
      searching,
      myPatient,
      exportToPDF,
      patient,
      patients,
      getPatient,
      storePatients,
      updatePatient,
      deletePatient,
      searchPatients,
      getPatients,
      conditions,
      getConditions,
      current_page,
      last_page,
      onPageChange,
      insurances,
      selectedAppointments,
      invoice,
      settings,
      flow,
      can,
      prescription,
      exportPrescription,
    };
  },
};
</script>
