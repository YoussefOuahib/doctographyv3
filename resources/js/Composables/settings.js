import axios from "axios";
import { ref, inject } from "vue";

export default function useSettings() {
    const settings = ref({})
    const fixedRate = ref('')
    const doctorName = ref('');
    const doctorAddress = ref('')
    const speciality = ref('')
    const swal = inject("$swal")


    const getSettings = async () => {
        axios.get("/api/settings").then((res) => {
            settings.value = res.data;
            fixedRate.value = res.data.rate
            doctorName.value = res.data.Name
            doctorAddress.value = res.data.address
            speciality.value = res.data.speciality

        });
        
    };
    const updateSettings = async (settings) => {
        axios.post('/api/settings', settings).then((response) => {
            swal({
                icon: "success",
                title: localStorage.getItem('userLanguage') == 'en' ? "Settings has been saved successfully" : "Les paramètres ont été enregistrés avec succès",

            });
            getSettings();
        })
    }

    return {getSettings, settings, updateSettings, fixedRate, doctorName, doctorAddress, speciality}


}