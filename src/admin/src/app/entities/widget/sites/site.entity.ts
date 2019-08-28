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
    widgetAsSeparatePageUrl: string;
    zipCode: string;
    availableTestDriveFromHome: boolean;
    maxVehicleDeliveryDistance: number;
    importRelativeFtpPath: string;
    shuffleExperts: boolean;

    ftpLogin: string;
    ftpPassword: string;
    sendEmailNotificationsToCustomer: boolean;
    injectButtonToSrp: boolean;
    injectButtonToVdp: boolean;
    injectButtonToSidebar: boolean;
    injectWidgetToSaw: boolean;

    //#region readonly fields for api

    usedVehiclesCount: number;
    newVehiclesCount: number;

    //#endregion

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
        this.widgetAsSeparatePageUrl = mock.widgetAsSeparatePageUrl;
        this.workingHours = WorkingInterval.initializeManyFromDto(dto.workingHours);
        this.zipCode = mock.zipCode;
        this.availableTestDriveFromHome = mock.availableTestDriveFromHome;
        this.maxVehicleDeliveryDistance = mock.maxVehicleDeliveryDistance;
        this.importRelativeFtpPath = mock.importRelativeFtpPath;
        this.shuffleExperts = mock.shuffleExperts;

        this.ftpLogin = mock.ftpLogin;
        this.ftpPassword = mock.ftpPassword;
        this.sendEmailNotificationsToCustomer = mock.sendEmailNotificationsToCustomer;
        this.injectButtonToSrp = mock.injectButtonToSrp;
        this.injectButtonToVdp = mock.injectButtonToVdp;
        this.injectButtonToSidebar = mock.injectButtonToSidebar;
        this.injectWidgetToSaw = mock.injectWidgetToSaw;

        this.usedVehiclesCount = mock.usedVehiclesCount;
        this.newVehiclesCount = mock.newVehiclesCount;
    }
}