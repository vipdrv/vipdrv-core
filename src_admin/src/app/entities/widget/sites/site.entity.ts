import * as _ from 'lodash';
import { Variable, WorkingInterval } from './../../../utils/index';
import { Entity } from './../../index';
import { StepInfo } from './step-info';

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
    steps: Array<StepInfo>;
    beverageStepOrder: number;
    expertStepOrder: number;
    routeStepOrder: number;

    widgetAsSeparatePageUrl: string;

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

        this.useExpertStep = mock.useExpertStep;
        this.useBeverageStep = mock.useBeverageStep;
        this.useRouteStep = mock.useRouteStep;
        this.beverageStepOrder = mock.beverageStepOrder;
        this.expertStepOrder = mock.expertStepOrder;
        this.routeStepOrder = mock.routeStepOrder;
        this.steps = this.getStepInfos();
        this.orderSteps();
    }
    getStepInfos(): Array<StepInfo> {
        const steps: Array<StepInfo> = [];

        const beverageStep = new StepInfo();
        beverageStep.name = 'BeverageStep';
        beverageStep.localizationKey = 'sites.wizard.beverageStep';
        beverageStep.isActive = this.useBeverageStep;
        beverageStep.order = this.beverageStepOrder;
        beverageStep.entitiesCount = this.beveragesAmount;
        beverageStep.activeEntitiesCount = this.activeBeveragesAmount;
        beverageStep.activeEntitiesLocalizationKey = 'sites.wizard.activeBeverages_amount';
        beverageStep.noActiveEntitiesLocalizationKey = 'sites.wizard.noActiveBeverages';
        beverageStep.notActiveEntitiesLocalizationKey = 'sites.wizard.notActiveBeverages_amount';
        steps.push(beverageStep);

        const expertStep = new StepInfo();
        expertStep.name = 'ExpertStep';
        expertStep.localizationKey = 'sites.wizard.expertStep';
        expertStep.isActive = this.useExpertStep;
        expertStep.order = this.expertStepOrder;
        expertStep.entitiesCount = this.expertsAmount;
        expertStep.activeEntitiesCount = this.activeExpertsAmount;
        expertStep.activeEntitiesLocalizationKey = 'sites.wizard.activeExperts_amount';
        expertStep.noActiveEntitiesLocalizationKey = 'sites.wizard.noActiveExperts';
        expertStep.notActiveEntitiesLocalizationKey = 'sites.wizard.notActiveExperts_amount';
        steps.push(expertStep);

        const routeStep = new StepInfo();
        routeStep.name = 'RouteStep';
        routeStep.localizationKey = 'sites.wizard.routeStep';
        routeStep.isActive = this.useRouteStep;
        routeStep.order = this.routeStepOrder;
        routeStep.entitiesCount = this.routesAmount;
        routeStep.activeEntitiesCount = this.activeRoutesAmount;
        routeStep.activeEntitiesLocalizationKey = 'sites.wizard.activeRoutes_amount';
        routeStep.noActiveEntitiesLocalizationKey = 'sites.wizard.noActiveRoutes';
        routeStep.notActiveEntitiesLocalizationKey = 'sites.wizard.notActiveRoutes_amount';
        steps.push(routeStep);

        return steps;
    }
    orderSteps(): void {
        this.steps = _(this.steps).sortBy((r) => r.order).value();
    }
}