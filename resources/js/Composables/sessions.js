import axios from "axios";
import { ref, inject } from "vue";

export default function useSessions() {
    const sessions = ref({});
    const mySession = ref({});
    const swal = inject("$swal");
    const current_page = ref(1);
    const last_page = ref();
    const getSessions = async (page = 1) => {
        axios.get("/api/sessions?page=" + page).then((response) => {
            sessions.value = response.data;
            current_page.value = page;
            last_page.value = response.data.meta.last_page;
        });
    };
    const getSession = async (session) => {
        axios.get("/api/appointments/" + session).then((res) => {
            mySession.value = res.data.data;
        });
    };
    const searchSessions = async (item, page = 1) => {

        console.log("hello from search");
        console.log(item.id);
        axios
            .get("/api/search/sessions/" + item.id + "?page=" + page )
            .then((response) => {
                sessions.value = response.data;

                current_page.value = page;
            last_page.value = response.data.meta.last_page;
            })
            .catch((error) => console.log(error));
    };

    const updateSession = (session) => {
        axios.put("/api/sessions/" + session.id, session)
            .then((response) => {
                swal({
                    icon: "success",
                    title: localStorage.getItem('userLanguage') == 'en' ? "Session has been edited successfully" : "La session a été modifiée avec succès",
                });

                getSessions();
       
            })
           
    };
    const deleteSession = async (session) => {
        swal({
            title: localStorage.getItem('userLanguage') == 'en' ? "Are you sure you wanna delete this appointment?" : "Voulez-vous vraiment supprimer ce rendez-vous ?",
            text: localStorage.getItem('userLanguage') == 'en' ? "Upcoming appointments which associated with this appointment will be deleted too !" : "les prochains rendez-vous associés à ce rendez-vous seront également supprimés !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#499167",
            cancelButtonColor: "#E97777",
            confirmButtonText: localStorage.getItem('userLanguage') == 'en' ? "Yes, delete it" : "oui, supprimez",
        }).then((result) => {
            if (result.isConfirmed) {
                axios
                    .delete("/api/sessions/" + session)
                    .then((response) => {
                        swal.fire(
                            localStorage.getItem('userLanguage') == 'en' ? "Deleted!": "Supprimé",
                            localStorage.getItem('userLanguage') == 'en' ? "Appointment has been deleted." : "Le rendez-vous a été supprimé.",
                            "success"
                        );
                    });

                getSessions();
            }
        });
    };
    const storePayment = async(payment) => {
        console.log(payment);
        axios.post('/api/payments/', payment).then(response => {
            if(response.data == 205) {
                swal({
                    icon: "error",
                    title: localStorage.getItem('userLanguage') == 'en' ? "The price cannot be greater than remaining amount" : "Le prix ne peut pas être supérieur au montant restant",
                });

            if(response.data == 206) {
                swal({
                    icon: "error",
                    title: localStorage.getItem('userLanguage') == 'en' ? "the client has completed his payment: Remaining Amount: 0 MAD" : "le patient a terminé son paiement, Montant restant : 0 MAD",
                });
            }
            }else {
            swal({
                icon: "success",
                title: localStorage.getItem('userLanguage') == 'en' ? "Saved ! " : "Enregistré !",
            });
            getSessions();
        }
        });
    }

    return {
        storePayment,
        getSessions,
        current_page,
        last_page,
        deleteSession,
        updateSession,
        sessions,
        getSession,
        mySession,
        searchSessions,
    };
}
