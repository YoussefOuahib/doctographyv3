import axios from "axios";
import {ref, inject, reactive} from "vue"
export default function useHome() {
    const generalData = ref([])
    const paid = ref([])

    const getGeneralData = async() => {
        axios.get('api/generalData').then(response => {
            generalData.value = response.data.generalData;
            paid.value = response.data.generalData.paid;
            console.log(generalData.value);
        
          
        });
    }
    return {generalData, getGeneralData, paid}
}