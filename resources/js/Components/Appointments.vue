<template>
    <div>
        <v-dialog v-model="dialog" fullscreen scrollable>
            <template v-slot:activator="{ props }">
                <div class="mb-6">
                    <v-btn color="primary" class="ml-2 mt-4 mr-auto" @click="dialog = true" prepend-icon="mdi-calendar-plus"
                        v-if="can('appointments.store')">
                        {{ $t("message.addAppointment") }}
                    </v-btn>
                    <v-btn color="warning" v-if="can('upcomings.store')" class="ml-2 mt-4 mr-auto" @click="dialogApp = true"
                        prepend-icon="mdi-calendar-plus">
                        {{ $t("message.addAppointment") }}
                    </v-btn>
                </div>
            </template>
            <br />

            <v-form @submit.prevent="storeAppointments(appointment, calendar)" enctype="multipart/form-data">
                <v-card>
                    <v-card-text>
                        <v-container>
                            <v-row>

                                <v-col cols="10" sm="12" md="12">

                                    <v-autocomplete density="compact" hide-no-data persistent-hint label="Patient"
                                        v-model="appointment.patient_id" name="patient_id" :items="patients.data"
                                        item-title="full_name" item-value="id">
                                    </v-autocomplete>
                                </v-col>

                                <v-divider></v-divider>
                                <v-col cols="12" sm="6" md="6">
                                    <v-select :label="$t('message.act')" clearable item-value="id" item-title="name"
                                        single-line chips v-model="appointment.act"
                                        @update:modelValue="checkIfExists(appointment.patient_id, appointment.act)"
                                        density="compact" :items="conditions.data">
                                    </v-select>
                                </v-col>


                                <v-col cols="12" sm="6" md="6">
                                    <v-text-field v-model="appointment.medical_treatment"
                                        :label="$t('message.medicalTreatment')" placeholder="Aspirin, ..." required
                                        density="compact"></v-text-field>
                                </v-col>


                                <v-col cols="12" v-if="checked == false" sm="6" md="6">
                                    <v-text-field density="compact" :label="$t('message.totalAmountTreatment')"
                                        v-model="appointment.total_amount"></v-text-field>
                                </v-col>
                                <v-col cols="12" sm="6" md="6">

                                    <Datepicker density="compact" v-model="appointment.next_examination_date" 
                                    text-input inline-with-input :enableTimePicker="false" auto-apply no-today
                                        :highlightWeekDays="[0, 6]" :placeholder="$t('message.nextExaminationDate')">
                                    </Datepicker>
                                </v-col>
                                <v-col cols="12" sm="6" md="6">
                                    <v-file-input :label="$t('message.uploadImage')" v-model="appointment.gallery" multiple
                                        chips density="compact"></v-file-input>
                                </v-col>

                                <v-col cols="12" sm="6" md="6">
                                    <v-text-field clearable v-model="appointment.note" :label="$t('message.additionalNote')"
                                        density="compact"></v-text-field>
                                </v-col>




                                <v-col cols="12" sm="6" md="6">
                                    <v-text-field :label="$t('message.rate')" density="compact" v-model="appointment.rate">
                                    </v-text-field>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="blue-darken-1" variant="text" @click="dialog = false">
                            {{ $t("message.close") }}
                        </v-btn>
                        <v-btn color="blue-darken-1" variant="text" @click="submitForm" type="submit">
                            {{ $t("message.save") }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>

        <v-dialog v-model="dialogP">
            <v-card class="mx-auto">
                <v-card-text>
                    <v-table>
                        <thead>
                            <tr>
                                <th>{{ $t("message.name") }}</th>
                                <th> {{ $t("message.phone") }}</th>
                                <th>{{ $t("message.act") }}</th>
                                <th>Date</th>
                                <th v-if="myAppointment.next_examination_date">
                                    {{ $t("message.nextExaminationDate") }}
                                </th>
                                <th>{{ $t("message.paidAmount") }}</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <v-chip color="darkPrimary" text-color="white" prepend-icon="mdi-account-circle"
                                        class="font-weight-medium">
                                        {{ myPatient.full_name }}
                                    </v-chip>
                                </td>
                                <td>
                                    <v-chip color="darkPrimary" text-color="white" prepend-icon="mdi-account-circle"
                                        class="font-weight-medium">
                                        {{ myPatient.phone }}
                                    </v-chip>
                                </td>
                                <td>
                                    <v-chip color="danger" class="font-weight-medium">
                                        {{ myAppointment.act }}
                                    </v-chip>
                                </td>
                                <td>
                                    <v-chip color="darkPrimary" text-color="white" prepend-icon="mdi-calendar-range"
                                        class="font-weight-medium">
                                        {{ myAppointment.date }}
                                    </v-chip>
                                </td>
                                <td v-if="myAppointment.next_examination_date">
                                    <v-chip color="darkPrimary" text-color="white" prepend-icon="mdi-clipboard-text-clock"
                                        class="font-weight-medium">
                                        {{
                                            myAppointment.next_examination_date
                                        }}
                                    </v-chip>
                                </td>

                                <td>
                                    <v-chip color="success" class="font-weight-medium">
                                        {{ myAppointment.rate }} MAD
                                    </v-chip>
                                </td>
                                <td>
                                <td>
                                    <v-btn class="ml-2" color="secondary" icon="mdi-calendar-edit" size="x-small" rounded>
                                        <v-icon> </v-icon>
                                        <v-dialog v-model="dialogAppointment" activator="parent">
                                            <v-form @submit.prevent="
                                                updateAppointment(
                                                    myAppointment,
                                                    calendar
                                                )
                                            " enctype="multipart/form-data">
                                                <v-card>
                                                    <v-card-text>
                                                        <v-container>
                                                            <v-row>
                                                                <v-col v-if="
                                                                    can(
                                                                        'appointments.update'
                                                                    )
                                                                " cols="12" sm="6" md="6">
                                                                    <v-text-field v-model="
                                                                        myAppointment.medical_treatment
                                                                    " :label="$t('message.medicalTreatment')"
                                                                        placeholder="Aspirin, ..." required
                                                                        density="compact"></v-text-field>
                                                                </v-col>
                                                                <v-col cols="12" sm="6" md="6">
                                                                
                                                                    <Datepicker density="compact"
                                                                        v-model="myAppointment.next_examination_date"
                                                                        text-input inline-with-input
                                                                        :enableTimePicker="false" no-today auto-apply
                                                                        :highlightWeekDays="[0, 6]"
                                                                        :placeholder="$t('message.nextExaminationDate')">
                                                                    </Datepicker>
                                                                </v-col>

                                                                <v-col v-if="
                                                                    can(
                                                                        'appointments.update'
                                                                    )
                                                                " cols="12" sm="12" md="12">
                                                                    <v-text-field clearable v-model="
                                                                        myAppointment.note
                                                                    " :label="$t('message.additionalNote')"
                                                                        density="compact"></v-text-field>
                                                                </v-col>

                                                                <v-col cols="12" sm="6" md="6">
                                                                    <v-text-field :label="$t('message.rate')"
                                                                        density="compact" type="numeric" v-model="
                                                                            myAppointment.rate
                                                                        ">
                                                                    </v-text-field>
                                                                </v-col>
                                                            </v-row>
                                                        </v-container>
                                                    </v-card-text>
                                                    <v-card-actions>
                                                        <v-btn type="submit" color="darkPrimary">
                                                            {{
                                                                $t(
                                                                    "message.save"
                                                                )
                                                            }}
                                                        </v-btn>
                                                        <v-btn color="danger" @click="
                                                            dialogAppointment = false
                                                        ">
                                                            {{
                                                                $t(
                                                                    "message.close"
                                                                )
                                                            }}</v-btn>
                                                    </v-card-actions>
                                                </v-card>
                                            </v-form>
                                        </v-dialog>
                                    </v-btn>
                                    <v-btn v-if="myAppointment.next_examination_date" class="ml-2" color="error"
                                        icon="mdi-calendar-remove" size="x-small"
                                        @click="deleteNextDate(myAppointment.id, calendar)">
                                    </v-btn>

                                    <v-btn @click.prevent="
                                        deleteAppointment(myAppointment.id, calendar)" color="danger" class="ml-2"
                                        icon="mdi-delete" size="x-small">
                                    </v-btn>
                                </td>
                                </td>

                            </tr>
                        </tbody>
                    </v-table>
                    <div class="text-center">
                        <img v-for="item in myAppointment.images" :src="'/images/' + item.image" class="ml-2" :width="120">
                    </div>
                    <v-timeline>
                        <v-timeline-item v-for="app in myPatient.appointments" :key="app.id" size="large"
                            dot-color="primary">
                            <div>
                                <v-chip color="darkPrimary" prepend-icon="mdi-calendar-range" class="p-2 font-weight-bold"
                                    size="normal">{{ app.date }}
                                </v-chip>
                                <v-chip v-if="app.act" color="danger" prepend-icon="mdi-hospital-box"
                                    class="font-weight-medium">{{
                                        app.act
                                    }}</v-chip>
                                <v-chip v-if="app.medical_treatment" class="font-weight-bold"
                                    prepend-icon="mdi-bottle-tonic-plus" color="warning">
                                    {{ app.medical_treatment }}
                                </v-chip>
                                <v-chip v-if="app.note" color="info" prepend-icon="mdi-alert" class="ml-2">
                                    {{ app.note }}</v-chip>
                                <v-btn v-if="app.images" @click.stop="dialogImage[app.id] = true" class="ma-2"
                                    size="x-small" color="purple" icon="mdi-image-multiple"></v-btn>

                                <br />

                            </div>
                            <v-dialog v-model="dialogImage[app.id]">
                                <v-card>
                                    <div class="text-center">
                                        <img v-for="item in app.images" :src="'/images/' + item.image" class="ml-2"
                                            :width="120">
                                    </div>
                                </v-card>
                            </v-dialog>
                        </v-timeline-item>
                    </v-timeline>
                </v-card-text>
                <v-card-actions>
                    <v-btn variant="text" color="teal-accent-4" @click="dialogP = false">
                        Close
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <v-dialog v-model="dialogApp">
            <v-form @submit.prevent="storeUpcomingAppointment(upcomingAppointment, calendar)" enctype="multipart/form-data">
                <v-card>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12" sm="6" md="6">
                                    <v-text-field v-model="upcomingAppointment.name" :label="$t('message.name')"
                                        density="compact">

                                    </v-text-field>
                                </v-col>

                                <v-col cols="12" sm="6" md="6">
                                    <datepicker :enableTimePicker="false" :highlightWeekDays="[0, 6]" :minDate="new Date()"
                                        no-today placeholder="Date" v-model="
                                            upcomingAppointment.date
                                        "></datepicker>
                                </v-col>




                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="blue-darken-1" variant="text" @click="dialogApp = false">
                            {{ $t("message.close") }}
                        </v-btn>
                        <v-btn color="blue-darken-1" variant="text" @click="submitForm" type="submit">
                            {{ $t("message.save") }}
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-form>
        </v-dialog>

        <div class="ml-3">
            <v-chip variant="elevated" color="danger">
                {{ $t("message.uncofirmed") }}</v-chip>
            <v-chip variant="elevated" color="darkPrimary">
                {{ $t("message.paid") }}</v-chip>
            <v-chip variant="elevated" color="#f7a048">
                {{ $t("message.session") }}</v-chip>
            <v-chip variant="elevated" color="#2196F3">
                {{ $t("message.upcomingAppointments") }}</v-chip>
        </div>

        <v-container>
            <full-calendar ref="calendar" :options="calendarOptions" />
        </v-container>
    </div>
</template>
<script>
import axios from "axios";
import { reactive, onMounted, ref, watch } from "vue";
import useAppointments from "../Composables/appointments";
import useUpcoming from "../Composables/upcoming";
import useSettings from "../Composables/settings";
import Datepicker from "@vuepic/vue-datepicker";
import "@vuepic/vue-datepicker/dist/main.css";
import usePatients from "../Composables/patients";
import "@fullcalendar/core/vdom"; // solves problem with Vite
import FullCalendar from "@fullcalendar/vue3";
import timeGridPlugin from "@fullcalendar/timegrid";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";
import { useAbility } from "@casl/vue";
import useConditions from "../Composables/conditions";


export default {
    components: { Datepicker, FullCalendar },
    data: () => ({
        dialog: false,
        dialogApp: false,
        dialogAppointment: false,
        dialogInvoice: false,
        dialogImage: {},
    }),
    setup() {
        const dialogP = ref(false);
        const dialogUpcoming = ref(false);
        const dialog = ref(false);
        const {
            myAppointment,
            appointments,
            getAppointment,
            getAppointments,
            checkIfExists,
            checked,
            storeAppointments,
            deleteAppointment,
            updateAppointment,
            deleteNextDate,
        } = useAppointments();

        const { getPatients, patients, myPatient, getPatient } = usePatients();
        const { getSettings, settings, fixedRate } = useSettings();
        const { getConditions, conditions } = useConditions();
        const { storeUpcomingAppointment, deleteUpcomingAppointment } = useUpcoming();


        onMounted(() => {
            getSettings();
            getPatients();
            getConditions();
            getAppointments();
        });

        const selectedPatient = ref("");
        const calendar = ref();
        const appointment = reactive({
            patient_id: "",
            medical_treatment: "",
            act: "",
            note: "",
            next_examination_date: new Date(),
            total_amount: "",
            gallery: [],
            rate: fixedRate,
        });

        const upcomingAppointment = reactive({
            name: "",
            date: null,
        })





        const calendarOptions = reactive({
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: "dayGridMonth",
            headerToolbar: {
                prev: "chevron-left",
                next: "chevron-right",
                right: "prev,next",
            },

            eventSources: [
                function (fetchInfo, successCallback, failureCallback) {
                    axios.get("/api/appointments").then((response) => {
                        successCallback(response.data.data);
                    });
                },
                function (fetchInfo, successCallback, failureCallback) {
                    axios.get("/api/nextdates").then((response) => {
                        successCallback(response.data.data);
                    });

                },
                function (fetchInfo, successCallback, failureCallback) {
                    axios.get("/api/upcomings").then((response) => {
                        successCallback(response.data.data);
                    });
                },
            ],
            eventClick(info) {
                if (info.event.extendedProps.patient_id) {
                    getAppointment(info.event.id);
                    getPatient(info.event.extendedProps.patient_id);

                    dialogP.value = true;
                } else {

                    deleteUpcomingAppointment(info.event.id, calendar.value);
                }
            },
        });

        const submitForm = () => {
            dialog.value = false;
            let calendarApi = calendar.value.getApi();
            calendarApi.refetchEvents();
            appointment.value = "";
        };
        const { can } = useAbility();

        return {
            submitForm,
            calendar,
            conditions,
            getConditions,
            calendarOptions,
            appointment,
            appointments,
            patients,
            getPatients,
            selectedPatient,
            storeAppointments,
            updateAppointment,
            deleteAppointment,
            deleteNextDate,
            getAppointments,
            getAppointment,
            myAppointment,
            getPatient,
            myPatient,
            dialogP,
            dialogUpcoming,
            dialog,
            checkIfExists,
            checked,
            getSettings,
            settings,
            fixedRate,
            upcomingAppointment,
            storeUpcomingAppointment,
            can,
        };
    },
};
</script>
<style>
.fc .fc-button .fc-icon {
    vertical-align: bottom !important;
}

.fc .fc-button-group>.fc-button {
    background: #68b984;
    border: #68b984;
}

.swal2-container {
    z-index: 10000;
}</style>
