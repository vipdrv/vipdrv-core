import { Variable } from "./../../../utils/index";
import { Entity } from "./../../index";

export class LeadEntity extends Entity {
    siteId: number;
    expertId: number;
    beverageId: number;
    routeId: number;
    recieved: string
    userName: number;
    userPhone: string;
    userEmail: string;

    constructor() {
        super();
    }

    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        let mock: LeadEntity = <LeadEntity>dto;
        super.initializeFromDto(dto);
        this.siteId = mock.siteId;
        this.expertId = mock.expertId;
        this.beverageId = mock.beverageId;
        this.routeId = mock.routeId;
        this.recieved = mock.recieved;
        this.userName = mock.userName;
        this.userPhone = mock.userPhone;
        this.userEmail = mock.userEmail;
    }
}