import { defineStore } from "pinia";
import phil from 'phil-reg-prov-mun-brgy';
import { ref } from "vue";
export const useLocationStore = defineStore("location", () => {
    const regions = ref(phil.regions);
    const provinces = ref(phil.provinces);
    const municipalities = ref(phil.city_mun);
    const all_municipalities = ref(phil.city_mun);
    const barangays = ref(phil.barangays);
    const getProvincesByRegion = (reg_code: string) => {
        provinces.value = phil.getProvincesByRegion(reg_code);
    }
    const getCityMunByProvince = (prov_code: string) => {
        municipalities.value = phil.getCityMunByProvince(prov_code);
    }
    const getBarangayByMun = (mun_code: string) => {
        barangays.value = phil.getBarangayByMun(mun_code);
    }
    return { all_municipalities, barangays, municipalities, provinces, regions, getBarangayByMun, getCityMunByProvince, getProvincesByRegion }
});