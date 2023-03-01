import axios from "axios"
import { ref, inject } from 'vue'

export default function useUpcoming() {
    const swal = inject("$swal");

    const storeUpcomingAppointment = async (appointment, calendar) => {

        axios
            .post("/api/upcomings", appointment)
            .then((response) => {
                swal({
                    icon: "success",
                    title: "Appointment has been saved successfully",
                });
                let calendarApi = calendar.getApi();
                calendarApi.refetchEvents();
            });
    };
    const deleteUpcomingAppointment = async (appointment, calendar) => {
       
        swal({
            title: localStorage.getItem('userLanguage') == 'en' ? "Are you sure you wanna delete this date?" : "Voulez-vous vraiment supprimer cette date ?",
            text: localStorage.getItem('userLanguage') == 'en' ? "you won't be able to restore this information" : "Vous ne pourrez pas restaurer ces informations",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#499167",
            cancelButtonColor: "#E97777",
            confirmButtonText: localStorage.getItem('userLanguage') == 'en' ? "Yes, delete it!" : 'Suprrimez !',
        }).then((result) => {
            if (result.isConfirmed) {
                axios.delete("/api/upcomings/" + appointment)
                    .then((response) => {

                        swal.fire(
                            localStorage.getItem('userLanguage') == 'en' ? "Deleted!" : "Supprimé",
                            localStorage.getItem('userLanguage') == 'en' ? "Date has been deleted." : "la date a été supprimée",
                            "success"
                        );
                    });
                let calendarApi = calendar.getApi();
                calendarApi.refetchEvents();

            }
        });
    }
    return {
        deleteUpcomingAppointment,
        storeUpcomingAppointment
    }
}