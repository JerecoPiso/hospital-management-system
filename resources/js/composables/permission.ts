import { useAuthStore } from "@/store/patientchart/AuthStore";
import { PermissionAbilities } from "@/interface/Interfaces";

export function usePermission() {
    const auth = useAuthStore();

    const can = (module: string, ability: keyof PermissionAbilities = "view") => auth.can(module, ability);

    return { can };
}
