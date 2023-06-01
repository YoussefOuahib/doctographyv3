<template>
  <div>
    <v-autocomplete return-object :counter="25" clearable append-inner-icon="mdi-magnify"
      :label="$t('message.searchByName')" :items="patients.data" item-title="full_name" item-value="id"
      v-model="searching" @update:modelValue="searchSessions"></v-autocomplete>

    <v-btn color="warning" @click="dialogPayment = true" class="ml-2 mt-4 mr-auto" prepend-icon="mdi-calendar-plus">
      {{ $t("message.addPayment") }}
    </v-btn>

    <v-dialog v-model="dialogPayment">
      <v-form @submit.prevent="storePayment(payment)" enctype="multipart/form-data">
        <v-card>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12" sm="6" md="6">
                  <v-autocomplete v-model="payment.patient_id" :items="patients.data" label="Patient"
                    item-title="full_name" item-value="id" @update:modelValue="getPatient">
                  </v-autocomplete>
                </v-col>

                <v-col cols="12" sm="6" md="6">
                  <v-autocomplete v-model="payment.act" :items="conditions.data" label="Acte" item-title="name"
                    item-value="id">
                  </v-autocomplete>
                </v-col>
                <v-col cols="12" sm="12" md="12">
                  <v-text-field label="Amount" v-model="payment.amount">

                  </v-text-field>
                </v-col>



              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-btn color="blue-darken-1" variant="text" @click="dialogPayment = false">
              {{ $t("message.close") }}
            </v-btn>
            <v-btn color="blue-darken-1" variant="text" type="submit">
              {{ $t("message.save") }}
            </v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>

    <v-table>
      <thead>
        <tr>
          <th>Patient</th>
          <th>{{ $t("message.act") }}</th>
          <!-- <th>{{ $t("message.sessions") }}</th> -->
          <th>{{ $t("message.paidAmount") }}</th>
          <th>{{ $t("message.remainingAmount") }}</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="session in sessions.data" :key="session.id">
          <td>
            <v-chip :color="session.patient_gender == 'm' ? 'secondary-darken-1' : 'pink'" text-color="white"
              :prepend-icon="session.patient_gender == 'm' ? 'mdi-gender-male' : 'mdi-gender-female'
                " class="font-weight-medium">
              {{ session.patient_name }}
            </v-chip>
          </td>
          <td>
            <v-chip color="danger" class="font-weight-medium">
              {{ session.act }}
            </v-chip>
          </td>

          <td>
            <v-chip color="darkPrimary" text-color="white" prepend-icon="mdi-account-cash" class="font-weight-medium">
              {{ session.rate }} MAD
            </v-chip>
          </td>

          <td>
            <v-chip color="darkPrimary" text-color="white" prepend-icon="mdi-cash-multiple" class="font-weight-medium">
              {{ session.remaining_amount }} MAD
            </v-chip>
            <v-chip color="danger" text-color="white" prepend-icon="mdi-cash-lock" class="font-weight-medium"
              size="x-small">
              {{ session.total_amount }} MAD
            </v-chip>
          </td>

          <td>
            <v-chip color="darkPrimary" text-color="white" prepend-icon="mdi-calendar-range" class="font-weight-medium">
              {{ session.date }}
            </v-chip>
          </td>

          <td>
            <v-btn @mouseover="showAppointmentsSnack = true" @mouseleave="showAppointmentsSnack = false"
              @click.prevent="getInfo(session.id, session.patient_id)" color="warning" class="ml-2"
              icon="mdi-calendar-range" size="x-small">
            </v-btn>
            <v-btn color="secondary" @mouseover="editAppointmentSnack = true" @mouseleave="editAppointmentSnack = false"
              icon="mdi-calendar-edit" size="x-small" class="ml-2" @click.prevent="editSession(session)" rounded>
            </v-btn>
            <v-btn color="danger" @mouseover="deleteAppointmentSnack = true" @mouseleave="deleteAppointmentSnack = false"
              class="ml-2" icon="mdi-delete" size="x-small" @click="deleteSession(session.id)">
            </v-btn>
          </td>
        </tr>
      </tbody>
    </v-table>
    <v-dialog v-model="dialogSession">
      <v-form @submit.prevent="updateSession(mySession)" enctype="multipart/form-data">
        <v-card>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col v-if="can('appointments.update')" cols="12" sm="6" md="6">
                  <v-text-field v-model="mySession.medical_treatment" :label="$t('message.medicalTreatment')"
                    placeholder="Aspirin, ..." required density="compact"></v-text-field>
                </v-col>

                <v-col cols="12" sm="6" md="6">
                  <Datepicker :enableTimePicker="false" :highlightWeekDays="[0, 6]" :minDate="new Date()" no-today
                    placeholder="Select next examination date" v-model="mySession.next_examination_date"></Datepicker>
                </v-col>

                <v-col v-if="can('appointments.update')" cols="12" sm="6" md="6">
                  <v-text-field clearable v-model="mySession.note" :label="$t('message.additionalNote')"
                    density="compact"></v-text-field>
                </v-col>

                <v-col cols="12" sm="6" md="6">
                  <v-text-field :label="$t('message.rate')" density="compact" type="numeric" v-model="mySession.rate">
                  </v-text-field>
                </v-col>
              </v-row>
            </v-container>
          </v-card-text>
          <v-card-actions>
            <v-btn type="submit" color="darkPrimary">
              {{ $t("message.save") }}
            </v-btn>
            <v-btn color="danger" @click="dialogSession = false">
              {{ $t("message.close") }}</v-btn>
          </v-card-actions>
        </v-card>
      </v-form>
    </v-dialog>
    <v-pagination v-model="current_page" :length="last_page" color="primary"
      @update:modelValue="onPageChange"></v-pagination>
    <v-dialog v-model="dialogApp">
      <v-card>
        <v-timeline>
          <v-timeline-item v-for="app in myPatient.appointments" :key="app.id" size="large"
            :dot-color="app.type == 'session' ? 'primary' : 'warning'">
            <div>
              <v-chip color="darkPrimary" prepend-icon="mdi-calendar-range" class="font-weight-bold">{{ app.date }}
              </v-chip>
              <v-chip v-if="app.act" color="danger" prepend-icon="mdi-hospital-box" class="font-weight-medium">{{
                app.act }}</v-chip>
              <v-chip v-if="app.medical_treatment" class="font-weight-bold" prepend-icon="mdi-bottle-tonic-plus"
                color="warning">
                {{ app.medical_treatment }}
              </v-chip>
              <v-chip v-if="app.note" color="info" prepend-icon="mdi-alert" class="ml-2">
                {{ app.note }}</v-chip>

              <br />
              <v-chip color="darkPrimary" prepend-icon="mdi-bed" class="font-weight-medium ml-2" size="small"> {{ app.rate
              }} MAD</v-chip>
              <v-btn v-if="app.images" @click.stop="dialogImage[app.id] = true" class="ma-2" size="x-small" color="purple"
                icon="mdi-image-multiple"></v-btn>
              <br />
            </div>
            <v-dialog v-model="dialogImage[app.id]">
              <v-card>
                <div class="text-center">
                  <img v-for="item in app.images" :src="'/images/' + item.image" class="ml-2" :width="120" />
                </div>
              </v-card>
            </v-dialog>
          </v-timeline-item>
        </v-timeline>
        <v-card-actions>
          <v-btn color="darkPrimary" @click.stop="dialogApp = false">Close Dialog</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <!-- Start of snackbars -->
    <v-snackbar v-model="deleteAppointmentSnack" multi-line>
            {{ $t("message.deleteAppointmentSnack") }}


        </v-snackbar>
        <v-snackbar v-model="showAppointmentsSnack" multi-line>
            {{ $t("message.showAppointmentsSnack") }}


        </v-snackbar>
        <v-snackbar v-model="editAppointmentSnack" multi-line>
            {{ $t("message.editAppointmentSnack") }}


        </v-snackbar>

        <!-- End of Snackbars-->
  </div>
</template>
<script>
import { reactive, onMounted, ref, watch } from "vue";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import useAppointments from "../Composables/appointments";
import useSessions from "../Composables/sessions";
import usePatients from "../Composables/patients";
import useConditions from "../Composables/conditions";
import { useAbility } from "@casl/vue";

export default {
  components: { Datepicker },
  data: () => ({
    dialogImage: {},
    dialogPayment: false,

    //start of snackbars //
    showAppointmentsSnack: false,
    editAppointmentSnack: false,
    deleteAppointmentSnack: false,

    // end of snackbars //
    
  }),
  setup() {
    const searching = ref("");
    const dialogApp = ref(false);
    const dialogSession = ref(false);

    const payment = reactive({
      patient_id: "",
      act: "",
      amount: "",
    });
    const {
      getSessions,
      sessions,
      getSession,
      mySession,
      updateSession,
      deleteSession,
      searchSessions,
      current_page,
      last_page,
      storePayment
    } = useSessions();
    const { getPatients, patients, myPatient, getPatient } = usePatients();

    const { getConditions, conditions } = useConditions();

    onMounted(() => {
      getConditions();
      getPatients();
      getSessions();
    });

    const getInfo = (sessionId, patientId) => {
      dialogApp.value = true;
      getSession(sessionId);
      getPatient(patientId);
    };
    const editSession = async (session) => {
      getSession(session.id);
      dialogSession.value = true;
    };
    const { can } = useAbility();
    // watch(searching, (current, previous) => {
    //   searchSessions(current);
    // });
    const onPageChange = (current_page) => {
      getSessions(current_page);
    };

    return {
      storePayment,
      onPageChange,
      current_page,
      last_page,
      searching,
      getConditions,
      conditions,
      getSessions,
      getInfo,
      deleteSession,
      getSession,
      searchSessions,
      updateSession,
      editSession,
      mySession,
      sessions,
      getPatients,
      myPatient,
      patients,
      getPatient,
      dialogApp,
      dialogSession,
      can,
      payment,
    };
  },
};
</script>
