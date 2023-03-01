import axios from "axios";
import { inject, ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { AbilityBuilder, createMongoAbility } from '@casl/ability';
import { ABILITY_TOKEN } from '@casl/vue';

const user = reactive({
    name: "",
    email: "",
});
export default function useAuth() {
    const swal = inject("$swal");
    const router = useRouter();
    // const validationErrors = ref({})
    const ability = inject(ABILITY_TOKEN)

    const loginForm = reactive({
        email: "",
        password: "",
        remember: false,
    });

    // const swal = inject("$swal");

    const submitLogin = async () => {
        axios.get('/sanctum/csrf-cookie').then(response => {
        axios.post("/login", loginForm).then(async (response) => {
            loginUser(response);
        });
    });


    };

    const loginUser = async (response) => {
        user.name = response.data.name;
        user.email = response.data.email;
        localStorage.setItem("loggedIn", JSON.stringify(true));

        await getAbilities()
        await router.push({ name: "patients" });
    };

    const getUser = () => {
        axios.get("/api/user")
            .then((response) => {
                loginUser(response)
            }).catch((error) => console.log(error));
    };
    const logout = async () => {
        
        axios.post("logout").then(response => {
            localStorage.setItem('loggedIn', JSON.stringify(false))
            router.push({ name: "login" })


        });
    };
    const getAbilities = async() => {
        axios.get('/api/abilities')
            .then(response => {
                const permissions = response.data
                const { can, rules } = new AbilityBuilder(createMongoAbility)

                can(permissions)

                ability.update(rules)
            })
    }
    const updatePassword = (password) => {
        axios.post('/api/update/password', password).then(response => {
            console.log(response.status);
            swal({
                icon: "success",
                title: localStorage.getItem('userLanguage') == 'en' ? "Password has been successfully edited" : "Le mot de passe a été modifié avec succès",

                
            });
            
        }).catch(error => {
            if (error.response?.data) {
                const errors = error.response.data.errors;
                console.log(errors);
                let errorMessage = '';
                for (const field in errors) {
                  errorMessage += `${errors[field][0]}\n`;
                }
                swal({
                  icon: 'error',
                  title: localStorage.getItem('userLanguage') == 'en' ? "Validation Error" : "Erreur de validation",
                  text: errorMessage +'\n',
                });
          
            }
        });
    }
    const updateRecPassword = (password) => {
        axios.post('/api/update/receptionist/password', password).then(response => {
            console.log(response.status);
            swal({
                icon: "success",
                title: localStorage.getItem('userLanguage') == 'en' ? "Password has been successfully edited" : "Le mot de passe a été modifié avec succès",
            });
        
                
        }).catch(error => {
            if (error.response?.data) {
                const errors = error.response.data.errors;
                console.log(errors);
                let errorMessage = '';
                for (const field in errors) {
                  errorMessage += `${errors[field][0]}\n`;
                }
                swal({
                  icon: 'error',
                  title: localStorage.getItem('userLanguage') == 'en' ? "Validation Error" : "Erreur de validation",
                  text: errorMessage +'\n',
                });
          
            }
        });
    }

    return {
        getAbilities,
        getUser,
        user,
        loginForm,
        submitLogin,
        updatePassword,
        updateRecPassword,
        logout,
        
    };
}
