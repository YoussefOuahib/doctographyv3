<template>
    <div>
        <div class="mx-4 mt-4">
            <v-row justify="space-around">
                <v-col cols="12" md="4">
                    <v-sheet class="pa-8 text-center" color="grey-backg">
                        <v-icon
                            color="primary"
                            size="x-large"
                            style="font-size: 50px"
                            icon="mdi-account-injury"
                        >
                        </v-icon>

                        <br />
                        <v-chip
                            color="darkPrimary"
                            size="large"
                            class="font-weight-bold mt-2"
                        >
                            {{ generalData.patients }} Patients
                        </v-chip>
                        <br />

                        <v-chip
                            size="small"
                            prepend-icon="mdi-trending-up"
                            class="mt-2 font-weight-medium"
                            color="secondary-darken-1"
                        >
                            +{{ generalData.patientsAddedLastMonth }} Patients {{ $t('message.thisMonth')}}
                        </v-chip>
                    </v-sheet>
                </v-col>

                <v-col cols="12" md="4">
                    <v-sheet class="pa-8 text-center" color="grey-backg">
                        <v-icon
                            color="primary"
                            size="x-large"
                            style="font-size: 50px"
                            icon="mdi-calendar-account"
                        >
                        </v-icon>
                        <br />
                        <v-chip
                            color="darkPrimary"
                            size="large"
                            class="font-weight-bold mt-2"
                        >
                            {{ generalData.appointments }} {{$t('message.appointments')}}
                        </v-chip>
                        <br />
                
                        <v-chip
                            prepend-icon="mdi-calendar"
                            size="small"
                            class="mt-2 font-weight-medium"
                            color="darkPrimary"
                        >
                            {{ generalData.sessions }} {{$t('message.sessions')}}
                        </v-chip>
                    </v-sheet>
                </v-col>
                <v-col cols="12" md="4">
                    <v-sheet class="pa-8 text-center" color="grey-backg">
                        <v-icon
                            color="primary"
                            size="large"
                            style="font-size: 50px"
                            icon="mdi-cash-check"
                        >
                        </v-icon>
                        <br />
                        <v-chip
                            color="darkPrimary"
                            size="large"
                            class="font-weight-bold mt-2"
                        >
                            {{ generalData.earnings }} MAD
                        </v-chip>
                        <br />
                        <v-chip
                            size="small"
                            prepend-icon="mdi-trending-up"
                            class="mt-2 font-weight-medium"
                            color="secondary-darken-1"
                        >
                            +{{ generalData.earningsLastWeek }} MAD {{ $t('message.lastWeek') }}
                        </v-chip>
                    </v-sheet>
                </v-col>
            </v-row>
            <v-divider></v-divider>
            <v-spacer></v-spacer>
            <v-container>
                        <v-table>
            <thead>
                <tr>
                    <th>{{$t('message.month')}}</th>
                    <th>{{$t('message.totalIncome')}}</th>
                    <th>{{$t('message.appointments')}}</th>
                   
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in paid">
                    <td>
                        <v-chip
                        size="small"
                            color="primary"
                            class="font-weight-bold mt-2"
                        >
                        {{ item.month_name }}
                        </v-chip>
                       </td>
                    <td>
                        <v-chip
                        size="small"
                            color="darkPrimary"
                            class="font-weight-bold mt-2"
                        >
                        {{ item.total }} MAD
                        </v-chip>
                    </td>
                    <td>
                        <v-chip
                        size="small"
                            color="darkPrimary"
                            class="font-weight-bold mt-2"
                        >
                        {{ item.apps }}
                    </v-chip>
                    </td>
                </tr>
            </tbody>
            </v-table>
            
            </v-container>
            <!-- <v-select
                class="mt-4"
                label="Filter By:"
                v-model="filter"
                :items="items"
            ></v-select>  -->
            <!-- <div class="text-center">
                <canvas id="myChart"></canvas>
            </div> -->
        </div>
    </div>
</template>

<script>
import useHome from "../Composables/home";
import {
    reactive,
    onMounted,
    ref,
    watch,
    unref,
    watchEffect,
    onBeforeMount,
} from "vue";

export default {

    setup() {
        const { getGeneralData, generalData, paid } = useHome();

        onMounted(() => {
            getGeneralData();
        });

        // watchEffect(async () => {
        //     const ctx = document.getElementById("myChart");
        //     new Chart(ctx, {
        //         responsive: true,
        //         type: "line",
        //         data: {
        //             labels: Object.values(paid.value),
        //             datasets: [
        //                 {
        //                     label: "Paid Appointments",
        //                     borderColor: "#68B984",
        //                     backgroundColor: "rgba(14, 157, 19, 0.29)",
        //                     data: paid.value.total,
        //                     borderWidth: 4,
        //                     fill: true,
        //                 },
        //             ],
        //         },
        //         options: {
        //             scales: {
        //                 y: {
        //                     beginAtZero: true,
        //                 },
        //             },
        //         },
        //     });
        // });

        return { getGeneralData, generalData, paid };
    },
};
</script>
