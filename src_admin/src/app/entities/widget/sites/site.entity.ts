import { Variable } from "./../../../utils/index";
import { Entity } from "./../../index";

export class SiteEntity extends Entity {
    userId: number;
    beautyId: string;
    name: string;
    url: string;
    contacts: any;
    leadsAmount: number;

    constructor() {
        super();
    }

    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        let mock: SiteEntity = <SiteEntity>dto;
        super.initializeFromDto(dto);
        this.userId = mock.userId;
        this.beautyId = mock.beautyId;
        this.name = mock.name;
        this.url = mock.url;
        this.contacts = mock.contacts;
        this.leadsAmount = mock.leadsAmount;
    }
}