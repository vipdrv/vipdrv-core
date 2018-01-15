import { Variable, WorkingInterval } from './../../../utils/index';
import { Entity } from './../../index';
export class SiteEntity extends Entity {
    /// entity properties
    userId: number;
    beautyId: string;
    name: string;
    url: string;
    contacts: any;
    imageUrl: string;
    /// dto properties
    leadsAmount: number;
    newLeadsAmount: number;
    expertsAmount: number;
    activeExpertsAmount: number;
    beveragesAmount: number;
    activeBeveragesAmount: number;
    routesAmount: number;
    activeRoutesAmount: number;
    dealerName: string;
    ownerName: string;
    dealerAddress: string;
    dealerPhone: string;
    workingHours: Array<WorkingInterval>;

    /// wizard steps
    useExpertStep: boolean;
    useBeverageStep: boolean;
    useRouteStep: boolean;

    /// ctor
    constructor() {
        super();
    }
    /// methods
    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        const mock: SiteEntity = <SiteEntity>dto;
        super.initializeFromDto(dto);
        this.userId = mock.userId;
        this.beautyId = mock.beautyId;
        this.name = mock.name;
        this.ownerName = mock.ownerName;
        this.url = mock.url;
        this.contacts = mock.contacts;
        this.leadsAmount = mock.leadsAmount;
        this.newLeadsAmount = mock.newLeadsAmount;
        this.expertsAmount = mock.expertsAmount;
        this.activeExpertsAmount = mock.activeExpertsAmount;
        this.beveragesAmount = mock.beveragesAmount;
        this.activeBeveragesAmount = mock.activeBeveragesAmount;
        this.routesAmount = mock.routesAmount;
        this.activeRoutesAmount = mock.activeRoutesAmount;
        this.imageUrl = mock.imageUrl;
        this.dealerName = mock.dealerName;
        this.dealerAddress = mock.dealerAddress;
        this.dealerPhone = mock.dealerPhone;
        this.workingHours = WorkingInterval.initializeManyFromDto(dto.workingHours);

        this.useExpertStep = mock.useExpertStep;
        this.useBeverageStep = mock.useBeverageStep;
        this.useRouteStep = mock.useRouteStep;
    }
}