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
    protected patchOrderPromise: Promise<void>;
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
    showActiveEntitiesAmount(step): boolean {
        if (this.isBeverageStep(step)) {
            return this.showActiveBeveragesAmount();
        }

        if (this.isExpertStep(step)) {
            return this.showActiveExpertsAmount();
        }

        if (this.isRouteStep(step)) {
            return this.showActiveRoutesAmount();
        }
    }
    showNoActiveEntities(step): boolean {
        if (this.isBeverageStep(step)) {
            return this.showNoActiveBeverages();
        }

        if (this.isExpertStep(step)) {
            return this.showNoActiveExperts();
        }

        if (this.isRouteStep(step)) {
            return this.showNoActiveRoutes();
        }
    }
    isChangeOrderStepAllowed(step): boolean {
        return this.siteEntityPolicy.canUseWizard(this.entity);
    }
    isUpOrderStepAllowed(step): boolean {
        return this.entity.steps[this.entity.steps.length - 1] !== step;
    }
    isDownOrderStepAllowed(step): boolean {
        return this.entity.steps[0] !== step;
    }
    downOrderStep(step): Promise<void> {
        const stepIndex = this.entity.steps.indexOf(step);
        if (stepIndex !== -1 && this.isDownOrderStepAllowed(step)) {
            return this.swapStepsOrder(step, this.entity.steps[stepIndex - 1]);
        } else {
            return Promise.resolve();
        }
    }
    upOrderStep(step): Promise<void> {
        const stepIndex = this.entity.steps.indexOf(step);
        if (stepIndex !== -1 && this.isUpOrderStepAllowed(step)) {
            return this.swapStepsOrder(step, this.entity.steps[stepIndex + 1]);
        } else {
            return Promise.resolve();
        }
    }
    swapStepsOrder(step1, step2): Promise<void> {
        if (this.isBeverageStep(step1) && this.isExpertStep(step2) ||
            this.isBeverageStep(step2) && this.isExpertStep(step1)) {
            const self = this;
            const step1Value = step1.order;
            const step2Value = step2.order;
            step1.order = step2Value;
            step2.order = step1Value;
            if (this.isBeverageStep(step1)) {
                self.entity.beverageStepOrder = step2Value;
                self.entity.expertStepOrder = step1Value;
            } else {
                self.entity.beverageStepOrder = step1Value;
                self.entity.expertStepOrder = step2Value;
            }
            self.entity.orderSteps();
            self.patchOrderPromise = self.siteApiService
                .swapBeverageExpertStepOrder(self.entity.id)
                .then(
                    () => {
                        self.patchOrderPromise = null;
                    },
                    () => {
                        step1.order = step1Value;
                        step2.order = step2Value;
                        if (this.isBeverageStep(step1)) {
                            self.entity.beverageStepOrder = step1Value;
                            self.entity.expertStepOrder = step2Value;
                        } else {
                            self.entity.beverageStepOrder = step2Value;
                            self.entity.expertStepOrder = step1Value;
                        }
                        self.entity.orderSteps();
                        self.patchOrderPromise = null;
                    });
        }


        if (this.isBeverageStep(step1) && this.isRouteStep(step2) ||
            this.isBeverageStep(step2) && this.isRouteStep(step1)) {
            const self = this;
            const step1Value = step1.order;
            const step2Value = step2.order;
            step1.order = step2Value;
            step2.order = step1Value;
            if (this.isBeverageStep(step1)) {
                self.entity.beverageStepOrder = step2Value;
                self.entity.routeStepOrder = step1Value;
            } else {
                self.entity.beverageStepOrder = step1Value;
                self.entity.routeStepOrder = step2Value;
            }
            self.entity.orderSteps();
            self.patchOrderPromise = self.siteApiService
                .swapBeverageRouteStepOrder(self.entity.id)
                .then(
                    () => {
                        self.patchOrderPromise = null;
                    },
                    () => {
                        step1.order = step1Value;
                        step2.order = step2Value;
                        if (this.isBeverageStep(step1)) {
                            self.entity.beverageStepOrder = step1Value;
                            self.entity.routeStepOrder = step2Value;
                        } else {
                            self.entity.beverageStepOrder = step2Value;
                            self.entity.routeStepOrder = step1Value;
                        }
                        self.entity.orderSteps();
                        self.patchOrderPromise = null;
                    });
        }

        if (this.isExpertStep(step1) && this.isRouteStep(step2) ||
            this.isExpertStep(step2) && this.isRouteStep(step1)) {
            const self = this;
            const step1Value = step1.order;
            const step2Value = step2.order;
            step1.order = step2Value;
            step2.order = step1Value;
            if (this.isExpertStep(step1)) {
                self.entity.expertStepOrder = step2Value;
                self.entity.routeStepOrder = step1Value;
            } else {
                self.entity.expertStepOrder = step1Value;
                self.entity.routeStepOrder = step2Value;
            }
            self.entity.orderSteps();
            self.patchOrderPromise = self.siteApiService
                .swapExpertRouteStepOrder(self.entity.id)
                .then(
                    () => {
                        self.patchOrderPromise = null;
                    },
                    () => {
                        step1.order = step1Value;
                        step2.order = step2Value;
                        if (this.isExpertStep(step1)) {
                            self.entity.expertStepOrder = step1Value;
                            self.entity.routeStepOrder = step2Value;
                        } else {
                            self.entity.expertStepOrder = step2Value;
                            self.entity.routeStepOrder = step1Value;
                        }
                        self.entity.orderSteps();
                        self.patchOrderPromise = null;
                    });
        }

        return this.patchOrderPromise;
    }
    isPatchOrderProcessing(step): boolean {
        return Variable.isNotNullOrUndefined(this.patchOrderPromise);
    }
    isChangeIsActiveStepAllowed(step): boolean {
        if (this.isBeverageStep(step)) {
            return this.isChangeUseBeverageStepAllowed();
        }

        if (this.isExpertStep(step)) {
            return this.isChangeUseExpertStepAllowed();
        }

        if (this.isRouteStep(step)) {
            return this.isChangeUseRouteStepAllowed();
        }
    }
    isChangeIsActiveStepDisabled(step): boolean {
        if (this.isBeverageStep(step)) {
            return this.isChangeUseBeverageStepDisabled();
        }

        if (this.isExpertStep(step)) {
            return this.isChangeUseExpertStepDisabled();
        }

        if (this.isRouteStep(step)) {
            return this.isChangeUseRouteStepDisabled();
        }
    }
    onChangeIsActiveStep(step): Promise<void> {
        if (this.isBeverageStep(step)) {
            return this.onChangeUseBeverageStep();
        }

        if (this.isExpertStep(step)) {
            return this.onChangeUseExpertStep();
        }

        if (this.isRouteStep(step)) {
            return this.onChangeUseRouteStep();
        }
    }
    onChangeUseExpertStep(): Promise<void> {
        const self = this;
        const oldValue = self.entity.useExpertStep;
        self.entity.useExpertStep = !oldValue;
        self.getExpertStep().isActive = self.entity.useExpertStep;
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
                    self.getExpertStep().isActive = self.entity.useExpertStep;
                    self.patchExpertStepPromise = null;
                });
        return self.patchExpertStepPromise;
    }
    onChangeUseBeverageStep(): Promise<void> {
        const self = this;
        const oldValue = self.entity.useBeverageStep;
        self.entity.useBeverageStep = !oldValue;
        self.getBeverageStep().isActive = self.entity.useBeverageStep;
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
                    self.getBeverageStep().isActive = self.entity.useBeverageStep;
                    self.patchBeverageStepPromise = null;
                });
        return self.patchBeverageStepPromise;
    }
    onChangeUseRouteStep(): Promise<void> {
        const self = this;
        const oldValue = self.entity.useRouteStep;
        self.entity.useRouteStep = !oldValue;
        self.getRouteStep().isActive = self.entity.useRouteStep;
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
                    self.getRouteStep().isActive = self.entity.useRouteStep;
                    self.patchRouteStepPromise = null;
                });
        return self.patchRouteStepPromise;
    }
    protected getBeverageStep() {
        const steps = this.entity.steps.filter((step) => this.isBeverageStep(step));
        return steps.length === 1 ? steps[0] : null;
    }
    protected getExpertStep() {
        const steps = this.entity.steps.filter((step) => this.isExpertStep(step));
        return steps.length === 1 ? steps[0] : null;
    }
    protected getRouteStep() {
        const steps = this.entity.steps.filter((step) => this.isRouteStep(step));
        return steps.length === 1 ? steps[0] : null;
    }
    /// predicates
    protected isBeverageStep(step): boolean {
        return step.name === 'BeverageStep';
    }
    protected isExpertStep(step): boolean {
        return step.name === 'ExpertStep';
    }
    protected isRouteStep(step): boolean {
        return step.name === 'RouteStep';
    }
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