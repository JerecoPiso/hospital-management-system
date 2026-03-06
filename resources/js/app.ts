import './bootstrap';
import { createApp } from 'vue';
import { createPinia } from 'pinia'
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
import DatePicker from 'primevue/datepicker';
import Dialog from 'primevue/dialog';
import FloatLabel from 'primevue/floatlabel';
import InputText from 'primevue/inputtext';
import InputNumber from 'primevue/inputnumber';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';
import Toast from 'primevue/toast';

const app = createApp(App);
const pinia = createPinia()
app.use(PrimeVue, {
    theme: {
        preset: Aura
    }
});
app.use(pinia);
app.use(ConfirmationService);
app.use(DialogService);
app.use(ToastService);

app.component('Button', Button)
app.component('Column', Column)
app.component('DataTable', DataTable)
app.component("DatePicker", DatePicker)
app.component('Dialog', Dialog)
app.component('FloatLabel', FloatLabel)
app.component('InputText', InputText)
app.component('InputNumber', InputNumber)
app.component('Select', Select)
app.component('Textarea', Textarea)
app.component('Toast', Toast)

app.use(router);
app.mount("#app");
