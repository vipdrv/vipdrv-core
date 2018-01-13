import { Component, Input } from '@angular/core';
import { Variable, ILogger, ConsoleLogger } from '../../../utils/index';
import { SiteEntity } from './../../../entities/index';
import { ApplicationConstants } from './../../../app.constants';
import { ISiteApiService, SiteApiService } from './../../../services/index';
import { ISiteEntityPolicyService, SiteEntityPolicyService } from './../../../services/index';
@Component({
    selector: 'site-wizard',
    templateUrl: './site-wizard.html',
    styleUrls: ['./site-wizard.scss'],
})
export class SiteWizardComponent {
    /// inputs
    @Input() entity: SiteEntity;
    /// service fields
    protected switcherSettings = ApplicationConstants.switcherSettings;
    protected patchExpertStepPromise: Promise<void>;
    protected patchBeverageStepPromise: Promise<void>;
    protected patchRouteStepPromise: Promise<void>;
    /// injected dependencies
    protected logger: ILogger;
    protected siteApiService: ISiteApiService;
    protected siteEntityPolicy: ISiteEntityPolicyService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        siteApiService: SiteApiService,
        siteEntityPolicy: SiteEntityPolicyService) {
        this.logger = logger;
        this.siteApiService = siteApiService;
        this.siteEntityPolicy = siteEntityPolicy;
        logger.logDebug('SiteWizardComponent: Component has been constructed.');
    }
    /// methods
    onChangeUseExpertStep(): Promise<void> {
        const self = this;
        const oldValue = self.entity.useExpertStep;
        self.entity.useExpertStep = !oldValue;
        self.patchExpertStepPromise = self.siteApiService
            .patchUseExpertStep(self.entity.id, self.entity.useExpertStep)
            .then(
                () => {
                    self.logger.logTrase(`SiteWizardComponent: Property useExpertStep has been patched successfully (for site with id = ${self.entity.id} and name = ${self.entity.name}) to value ${self.entity.useExpertStep}.`);
                    self.patchExpertStepPromise = null;
                },
                () => {
                    self.logger.logError(`SiteWizardComponent: Property useExpertStep has not been patched (for site with id = ${self.entity.id} and name = ${self.entity.name}) to value ${self.entity.useExpertStep}.`);
                    self.entity.useExpertStep = oldValue;
                    self.patchExpertStepPromise = null;
                });
        return self.patchExpertStepPromise;
    }
    onChangeUseBeverageStep(): Promise<void> {
        const self = this;
        const oldValue = self.entity.useBeverageStep;
        self.entity.useBeverageStep = !oldValue;
        self.patchBeverageStepPromise = self.siteApiService
            .patchUseBeverageStep(self.entity.id, self.entity.useBeverageStep)
            .then(
                () => {
                    self.logger.logTrase(`SiteWizardComponent: Property useBeverageStep has been patched successfully (for site with id = ${self.entity.id} and name = ${self.entity.name}) to value ${self.entity.useBeverageStep}.`);
                    self.patchBeverageStepPromise = null;
                },
                () => {
                    self.logger.logError(`SiteWizardComponent: Property useBeverageStep has not been patched (for site with id = ${self.entity.id} and name = ${self.entity.name}) to value ${self.entity.useBeverageStep}.`);
                    self.entity.useBeverageStep = oldValue;
                    self.patchBeverageStepPromise = null;
                });
        return self.patchBeverageStepPromise;
    }
    onChangeUseRouteStep(): Promise<void> {
        const self = this;
        const oldValue = self.entity.useRouteStep;
        self.entity.useRouteStep = !oldValue;
        self.patchRouteStepPromise = self.siteApiService
            .patchUseRouteStep(self.entity.id, self.entity.useRouteStep)
            .then(
                () => {
                    self.logger.logTrase(`SiteWizardComponent: Property useRouteStep has been patched successfully (for site with id = ${self.entity.id} and name = ${self.entity.name}) to value ${self.entity.useRouteStep}.`);
                    self.patchRouteStepPromise = null;
                },
                () => {
                    self.logger.logError(`SiteWizardComponent: Property useRouteStep has not been patched (for site with id = ${self.entity.id} and name = ${self.entity.name}) to value ${self.entity.useRouteStep}.`);
                    self.entity.useRouteStep = oldValue;
                    self.patchRouteStepPromise = null;
                });
        return self.patchRouteStepPromise;
    }
    /// predicates
    protected isEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
    }
    // expert step
    protected showNoActiveExperts(): boolean {
        return this.entity.activeExpertsAmount <= 0;
    }
    protected showActiveExpertsAmount(): boolean {
        return !this.showNoActiveExperts();
    }
    protected isChangeUseExpertStepDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.patchExpertStepPromise);
    }
    protected isChangeUseExpertStepAllowed(): boolean {
        return this.siteEntityPolicy.canUseWizard(this.entity);
    }
    protected showExpertStepWarning(): boolean {
        return this.entity.useExpertStep && this.entity.activeExpertsAmount <= 0;
    }
    // beverage step
    protected showNoActiveBeverages(): boolean {
        return this.entity.activeBeveragesAmount <= 0;
    }
    protected showActiveBeveragesAmount(): boolean {
        return !this.showNoActiveBeverages();
    }
    protected isChangeUseBeverageStepDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.patchBeverageStepPromise);
    }
    protected isChangeUseBeverageStepAllowed(): boolean {
        return this.siteEntityPolicy.canUseWizard(this.entity);
    }
    protected showBeverageStepWarning(): boolean {
        return this.entity.useBeverageStep && this.entity.activeBeveragesAmount <= 0;
    }
    // route step
    protected showNoActiveRoutes(): boolean {
        return this.entity.activeRoutesAmount <= 0;
    }
    protected showActiveRoutesAmount(): boolean {
        return !this.showNoActiveRoutes();
    }
    protected isChangeUseRouteStepDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.patchRouteStepPromise);
    }
    protected isChangeUseRouteStepAllowed(): boolean {
        return this.siteEntityPolicy.canUseWizard(this.entity);
    }
    protected showRouteStepWarning(): boolean {
        return this.entity.useRouteStep && this.entity.activeRoutesAmount <= 0;
    }
}