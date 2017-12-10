import { Variable, Extensions } from './../../../utils/index';
import { Entity } from './../../index';
export class LeadEntity extends Entity {
    siteId: number;
    expertId: number;
    beverageId: number;
    routeId: number;
    recievedDateTime: string;
    firstName: number;
    secondName: number;
    userPhone: string;
    userEmail: string;
    siteName: string;
    expertName: string;
    routeName: string;
    beverageName: string;
    isNew: boolean;
    isReachedByManager: boolean;
    bookingDateTimeUtc: string;
    carTitle: string;
    carVin: string;
    carImageUrl: string;
    /// ctor
    constructor() {
        super();
    }
    /// methods
    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        const mock: LeadEntity = <LeadEntity>dto;
        super.initializeFromDto(dto);
        this.siteId = mock.siteId;
        this.expertId = mock.expertId;
        this.beverageId = mock.beverageId;
        this.routeId = mock.routeId;
        this.recievedDateTime = Extensions.formatUtcDateTimeToLocalTimezone(dto.recievedUtc);
        this.firstName = mock.firstName;
        this.secondName = mock.secondName;
        this.userPhone = mock.userPhone;
        this.userEmail = mock.userEmail;
        this.isNew = mock.isNew;
        this.isReachedByManager = mock.isReachedByManager;
        this.siteName = mock.siteName;
        this.expertName = mock.expertName;
        this.routeName = mock.routeName;
        this.beverageName = mock.beverageName;
        this.bookingDateTimeUtc = Extensions.formatUtcDateTimeToLocalTimezone(dto.bookingDateTimeOutputUtc);;
        this.carTitle = mock.carTitle;
        this.carVin = mock.carVin;
        this.carImageUrl = mock.carImageUrl;
    }
}