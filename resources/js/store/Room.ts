import { defineStore } from "pinia";
import { ref } from "vue";
import { Room } from "@/interface/Interfaces";
import { emptyMeta, type ApiTableMeta } from "@/composables/apiTable";
import axios from "axios";
export const useRoomStore = defineStore("room", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const rooms = ref<Room[]>([])
    const room = ref<Room>({
        ward_pid: '',
        room_number: '',
        room_type: ''
    })
    const meta = ref<ApiTableMeta>(emptyMeta())
    const create = async (data: Room) => {
        await axios.post(`${baseUrl}api/rooms`, data);
    }
    const read = async (params: Record<string, any> = {}) => {
        const response = await axios.get(`${baseUrl}api/rooms`, { params });
        rooms.value = response.data.data;
        if (response.data.meta) meta.value = response.data.meta;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/rooms/${pid}`);
        room.value = response.data.data;
    }
    const update = async (data: Room) => {
        await axios.put(`${baseUrl}api/rooms/${data.pid}`, data);
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/rooms/${pid}`);
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        rooms,
        room,
        meta
    }
})
