import './bootstrap';
import { createApp } from 'vue';
import router from "./router";
import App from './App.vue'
// PRIMEVUE
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import ConfirmationService from 'primevue/confirmationservice';
import ToastService from 'primevue/toastservice';

// PRIMEVUE COMPONENTS
import InputText from 'primevue/inputtext';
import Toast from 'primevue/toast';

const app = createApp(App);
app.use(PrimeVue, {
    theme: {
        preset: Aura
    }
});
app.use(ConfirmationService);
app.use(ToastService);
app.component('InputText', InputText)
app.component('Toast', Toast)
app.use(router);
app.mount("#app");
