import { IMenuPolicyService } from "app/services/policy/concrete/main/menu/i-menu.policy-service";
import { Injectable } from "@angular/core";
import { permissionNames } from "../../../../../constants/permissions.consts";

@Injectable()
export class MenuPolicyService
    implements IMenuPolicyService {

    canGetHome(): boolean {
        return true;
    }

    canGetSites(): boolean {
        return true;
    }

    canGetLeads(): boolean {
        return true;
    }

    canGetSettings(): boolean {
        return false;
    }

    canGet(): boolean {
        return false;
    }

    canCreate(): boolean {
        return false;
    }

    canUpdate(): boolean {
        return false;
    }

    canDelete(): boolean {
        return false;
    }
}