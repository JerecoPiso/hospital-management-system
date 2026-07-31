import { defineStore } from "pinia";
import { ref } from "vue";
import { Room } from "@/interface/Interfaces";
import axios from "axios";
export const useRoomStore = defineStore("room", () => {
    const baseUrl = import.meta.env.VITE_APP_API_URL;
    const rooms = ref<Room[]>([])
    const room = ref<Room>({
        ward_pid: '',
        room_number: '',
        room_type: ''
    })
    const create = async (data: Room) => {
        await axios.post(`${baseUrl}api/rooms`, data);
        read();
    }
    const read = async (ward_pid?: string) => {
        const response = await axios.get(`${baseUrl}api/rooms`, {
            params: ward_pid ? { ward_pid } : {}
        });
        rooms.value = response.data.data;
    }
    const view = async (pid: string) => {
        const response = await axios.get(`${baseUrl}api/rooms/${pid}`);
        room.value = response.data.data;
    }
    const update = async (data: Room) => {
        await axios.put(`${baseUrl}api/rooms/${data.pid}`, data);
        read();
    }
    const archive = async (pid: string) => {
        await axios.delete(`${baseUrl}api/rooms/${pid}`);
        read();
    }
    return {
        archive,
        create,
        read,
        view,
        update,
        rooms,
        room
    }
})
