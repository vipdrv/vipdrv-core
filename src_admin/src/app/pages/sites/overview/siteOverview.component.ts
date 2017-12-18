import { Component, Input, OnInit, OnDestroy } from '@angular/core';
import { Variable, ILogger, ConsoleLogger, PromiseService } from '../../../utils/index';
import { SiteEntity } from './../../../entities/index';
@Component({
    selector: 'site-overview',
    styleUrls: ['./siteOverview.scss'],
    templateUrl: './siteOverview.html',
})
export class SiteOverviewComponent implements OnInit, OnDestroy {
    @Input() entity: SiteEntity;
    /// injected dependencies
    protected logger: ILogger;
    protected promiseService: PromiseService;
    /// ctor
    constructor(logger: ConsoleLogger, promiseService: PromiseService) {
        this.logger = logger;
        this.promiseService = promiseService;
    }
    /// methods
    ngOnInit(): void {
        this.initWidget();
    }
    ngOnDestroy(): void { }
    /// predicates
    protected isEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
    }
    protected initWidget(): void {
        (<any>window).TestDrive.init({ SiteId: this.entity.id });
        this.logger.logTrase(`WidgetPreviewComponent: Widget has been initialized for the site (siteId = ${this.entity.id}).`);
    }
    // experts
    protected showNoExperts(): boolean {
        return Variable.isNullOrUndefined(this.entity.expertsAmount) ||
            Variable.isNullOrUndefined(this.entity.activeExpertsAmount) ||
            this.entity.expertsAmount <= 0;
    }
    protected showActiveExpertsAmount(): boolean {
        return !this.showNoExperts() &&
            Variable.isNotNullOrUndefined(this.entity.activeExpertsAmount) &&
            this.entity.activeExpertsAmount > 0;
    }
    protected showNoActiveExpertsAmount(): boolean {
        return !this.showNoExperts() &&
            Variable.isNotNullOrUndefined(this.entity.activeExpertsAmount) &&
            this.entity.activeExpertsAmount <= 0;
    }
    protected showNotActiveExpertsAmount(): boolean {
        return !this.showNoExperts() &&
            Variable.isNotNullOrUndefined(this.entity.activeExpertsAmount) &&
            this.entity.expertsAmount !== this.entity.activeExpertsAmount;
    }
    // beverages
    protected showNoBeverages(): boolean {
        return Variable.isNullOrUndefined(this.entity.beveragesAmount) ||
            Variable.isNullOrUndefined(this.entity.activeBeveragesAmount) ||
            this.entity.beveragesAmount <= 0;
    }
    protected showActiveBeveragesAmount(): boolean {
        return !this.showNoBeverages() &&
            Variable.isNotNullOrUndefined(this.entity.activeBeveragesAmount) &&
            this.entity.activeBeveragesAmount > 0;
    }
    protected showNoActiveBeveragesAmount(): boolean {
        return !this.showNoBeverages() &&
            Variable.isNotNullOrUndefined(this.entity.activeBeveragesAmount) &&
            this.entity.activeBeveragesAmount <= 0;
    }
    protected showNotActiveBeveragesAmount(): boolean {
        return !this.showNoBeverages() &&
            Variable.isNotNullOrUndefined(this.entity.activeBeveragesAmount) &&
            this.entity.beveragesAmount !== this.entity.activeBeveragesAmount;
    }
    // routes
    protected showNoRoutes(): boolean {
        return Variable.isNullOrUndefined(this.entity.routesAmount) ||
            Variable.isNullOrUndefined(this.entity.activeRoutesAmount) ||
            this.entity.routesAmount <= 0;
    }
    protected showActiveRoutesAmount(): boolean {
        return !this.showNoRoutes() &&
            Variable.isNotNullOrUndefined(this.entity.activeRoutesAmount) &&
            this.entity.activeRoutesAmount > 0;
    }
    protected showNoActiveRoutesAmount(): boolean {
        return !this.showNoRoutes() &&
            Variable.isNotNullOrUndefined(this.entity.activeRoutesAmount) &&
            this.entity.activeRoutesAmount <= 0;
    }
    protected showNotActiveRoutesAmount(): boolean {
        return !this.showNoRoutes() &&
            Variable.isNotNullOrUndefined(this.entity.activeRoutesAmount) &&
            this.entity.routesAmount !== this.entity.activeRoutesAmount;
    }
}