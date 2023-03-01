import axios from "axios";
import { ref, inject } from "vue";

export default function useAppointments() {
    const appointments = ref({});
    const dates = ref({});
    const checked = ref(true);
    const myAppointment = ref({});
    const validationErrors = ref({});
    const swal = inject("$swal");
    const getAppointments = async () => {
        axios.get("/api/appointments").then((response) => {
            appointments.value = response.data;
        });
    };
    const getAppointment = async (appointment) => {
   
        axios.get("/api/appointments/" + appointment).then((res) => {
            myAppointment.value = res.data.data;
        });
    };


    const getNextDates = async () => {
        axios.get("/api/nextdates").then((response) => {
            dates.value = response.data;
        });
    };

    const storeAppointments = async (appointment, calendar) => {
        let serializedAppointment = new FormData()
        
        for (let item in appointment) {
            if (appointment.hasOwnProperty(item)) {
                serializedAppointment.append(item, appointment[item])
                
            }
        }
        if (appointment.gallery.length > 0) {
            for (let i = 0; i < appointment.gallery.length; i++) {
              let file = appointment.gallery[i];
              serializedAppointment.append('gallery[' + i + ']', file);
            }
          }
         

        axios
            .post("/api/appointments", serializedAppointment)
            .then((response) => {
                if(response.data == 205) {
                    swal({
                        icon: "error",
                        title: localStorage.getItem('userLanguage') == 'en' ? "The price cannot be greater than remaining amount" : "Le prix ne peut pas être supérieur au montant restant",
                    });
                }else {
                swal({
                    icon: "success",
                    title: localStorage.getItem('userLanguage') == 'en' ? "Saved ! " : "Enregistré !",
                });
            }
                let calendarApi = calendar.getApi();
                calendarApi.refetchEvents();
            })
            .catch((error) => {
                if (error.response?.data) {
                    validationErrors.value = error.response?.data.errors;
                }
            });
    };
    const updateAppointment = async (appointment, calendar) => {
            axios.put("/api/appointments/" + appointment.id, appointment)
            .then((response) => {
                swal({
                    icon: "success",
                    title: localStorage.getItem('userLanguage') == 'en' ? "Session has been edited successfully" : "La session a été modifiée avec succès",
                });

                getSessions();
                let calendarApi = calendar.getApi();
                calendarApi.refetchEvents();
       
            }) .catch((error) => {
                if (error.response?.data) {
                    validationErrors.value = error.response?.data.errors;
                }
            });
        
    };

    const deleteAppointment = async (appointment, calendar) => {
        swal({
            title: localStorage.getItem('userLanguage') == 'en' ? "Are you sure you wanna delete this appointment ?" :"Voulez-vous vraiment supprimer ce rendez-vous ?",
            title: localStorage.getItem('userLanguage') == 'en' ? "the future appointments which associated with this appointment will be deleted too !" : "les prochains rendez-vous associés à ce rendez-vous seront également supprimés !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#499167",
            cancelButtonColor: "#E97777",
            confirmButtonText: localStorage.getItem('userLanguage') == 'en' ? "Yes, delete it!" : "Oui, supprimez !",
        }).then((result) => {
            if (result.isConfirmed) {
                axios
                    .delete("/api/appointments/" + appointment)
                    .then((response) => {
                        swal.fire(
                            localStorage.getItem('userLanguage') == 'en' ? "Deleted!" : "Supprimé",
                            localStorage.getItem('userLanguage') == 'en' ? "Appointment has been deleted." : "Le rendez-vous a été supprimé.",
                            "success"
                        );
                    });
                let calendarApi = calendar.getApi();
                calendarApi.refetchEvents();
            }
        });
    };
    const checkIfExists = async(patient_id, condition) => {
        console.log('hello guys');
        axios.get('/api/check/' + patient_id +'/' + condition).then(response => {
            checked.value = response.data.checked;
          
        })
    };
   
    const deleteNextDate = async (appointment, calendar) => {

        swal({
            title:  localStorage.getItem('userLanguage') == 'en' ? "Are you sure you wanna delete this date?" : "Voulez-vous vraiment supprimer cette date ?" ,
            text:  localStorage.getItem('userLanguage') == 'en' ? "you won't be able to restore this information" : "vous ne pourrez pas restaurer ces informations",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#499167",
            cancelButtonColor: "#E97777",
            confirmButtonText:  localStorage.getItem('userLanguage') == 'en' ? "Yes, delete it!" : 'Suprrimez !' ,
        }).then((result) => {
            if (result.isConfirmed) {
                axios
                    .post("/api/nextdates/" + appointment)
                    .then((response) => {
                        let calendarApi = calendar.getApi();
                        calendarApi.refetchEvents();
                        swal.fire(
                            "Deleted!",
                            "Date has been deleted.",
                            "success"
                        );
                    });
               
            }
        });
    };

   

    return {
        getAppointments,
        appointments,
        storeAppointments,
        getNextDates,
        getAppointment,
        myAppointment,
        dates,
        deleteAppointment,
        deleteNextDate,
        updateAppointment,
        checkIfExists,
        checked,
    };
}
