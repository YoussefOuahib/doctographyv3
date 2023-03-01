import axios from "axios";
import { ref, inject } from "vue";


export default function useConditions() {
    const conditions = ref([]);
    const validationErrors = ref({});
    const myCondition = ref({})
    const patients = ref({})
    const swal = inject("$swal");
    const getConditions = async () => {
        axios.get("/api/conditions").then((res) => {
            
            conditions.value = res.data;
        });
    };

    const updateCondition = async (condition) => {
        axios
            .put("/api/conditions/" + condition.id, condition)
            .then((response) => {
                swal({
                    icon: "success",
                    title: localStorage.getItem('userLanguage') == 'en' ? "Act has been edited successfully" : "L'acte a été modifiée avec succès",
                });
                getConditions();


            })
            .catch((error) => {
                if (error.response?.data) {
                    validationErrors.value = error.response?.data.errors;
                }
            });
    };

    const getCondition = async(condition) => {
        axios.get("/api/conditions/" + condition)
        .then(res => {
            myCondition.value = res.data.data;
        });
    };



    const storeConditions = async (condition) => {
        axios
            .post("/api/conditions", condition)
            .then((response) => {
                swal({
                    icon: "success",
                    title: localStorage.getItem('userLanguage') == 'en' ? "Act has been saved successfully" : "L'acte a été enregistrée avec succès",
                });
                getConditions();
            })
            .catch((error) => {
                if (error.response?.data) {
                    validationErrors.value = error.response?.data.errors;
                }
            });
    };

    const searchConditions = async (item) => {
        axios
            .get("/api/search/conditions?searching=" + item)
            .then((response) => {
                conditions.value = response.data;
            })
            .catch((error) => console.log(error));
    };
    const deleteCondition = async (condition) => {

        swal({
            title: localStorage.getItem('userLanguage') == 'en' ? "Are you sure you wanna delete this act ?" : "Voulez-vous vraiment supprimer cet acte?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#499167",
            cancelButtonColor: "#E97777",
            confirmButtonText: localStorage.getItem('userLanguage') == 'en' ? "Yes, delete it!" : "Oui, Supprimez !",
        }).then((result) => {
            if (result.isConfirmed) {
                axios
                    .delete("/api/conditions/" + condition)
                    .then((response) => {
                   
                        if(response.data == 201) {
                            getConditions();

                        swal.fire(
                            localStorage.getItem('userLanguage') == 'en' ? "Deleted!" : "Supprimé",
                            localStorage.getItem('userLanguage') == 'en' ? "Act has been deleted." : "Acte a été supprimée.",
                            "success"
                        );
                        }
                        else {
                            swal.fire(
                                localStorage.getItem('userLanguage') == 'en' ? "Not Deleted!" : "non supprimé",
                                localStorage.getItem('userLanguage') == 'en' ? "Act has not been deleted because it's associated with patients." : "L'acte n'a pas été supprimé car il est associé à des patients.",
                                "error"
                            );  
                        }
                    });
               
            }
        });
    };

    return {
        getConditions,
        conditions,
        patients,
        myCondition,
        getCondition,
        updateCondition,
        storeConditions,
        searchConditions,
        deleteCondition,
    };
}
