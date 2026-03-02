import './bootstrap';
import { createApp } from 'vue';
import router from "./router";
import App from './App.vue'
// PRIMEVUE
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import ConfirmationService from 'primevue/confirmationservice';
import DialogService from 'primevue/dialogservice'
import ToastService from 'primevue/toastservice';

// PRIMEVUE COMPONENTS
import Button from 'primevue/button';
import Column from 'primevue/column';
import DataTable from 'primevue/datatable';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import Toast from 'primevue/toast';


const app = createApp(App);
app.use(PrimeVue, {
    theme: {
        preset: Aura
    }
});
app.use(ConfirmationService);
app.use(DialogService);
app.use(ToastService);

app.component('Button', Button)
app.component('Column', Column)
app.component('DataTable', DataTable)
app.component('Dialog', Dialog)
app.component('InputText', InputText)
app.component('Textarea', Textarea)
app.component('Toast', Toast)

app.use(router);
app.mount("#app");
