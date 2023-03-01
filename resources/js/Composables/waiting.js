import axios from "axios";
import { ref } from "vue";

export default function useWaiting() {
    const waiting = ref(0);
  
const increment = () => {
    axios.post('api/increment/waiting').then(response => {
        waiting.value = response.data;
    })
}
const decrement = () => {
    axios.post('api/decrement/waiting').then(response => {
        waiting.value = response.data;
    })
}
    const show = () => {
        axios.get('/api/show/waiting').then(response => {
        waiting.value = response.data;
    });
    }
    const reset = () => {
        axios.post('/api/reset/waiting').then(response => {
        waiting.value = response.data;
    });
    }
    return {
        waiting,
        reset,
        increment,
        decrement,
        show,

    }
}