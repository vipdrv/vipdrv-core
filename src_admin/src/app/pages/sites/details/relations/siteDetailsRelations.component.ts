import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Variable, Extensions, ILogger, ConsoleLogger, PromiseService } from '../../../../utils/index';
import { SiteEntity } from '../../../../entities/index';
import { ISiteApiService, SiteApiService } from '../../../../services/serverApi/index';
@Component({
    selector: 'site-details-relations',
    styleUrls: ['./siteDetailsRelations.scss'],
    templateUrl: './siteDetailsRelations.html',
})
export class SiteDetailsRelationsComponent implements OnInit, OnDestroy {
    /// fields
    private _entityId: number;
    private _parameterSubscription: any; // type should be Subscription;
    protected entity: SiteEntity;
    /// injected dependencies
    protected logger: ILogger;
    protected siteApiService: ISiteApiService;
    protected promiseService: PromiseService;
    protected route: ActivatedRoute;
    /// ctor
    constructor(logger: ConsoleLogger,
                siteApiService: SiteApiService,
                promiseService: PromiseService,
                route: ActivatedRoute) {
        this.logger = logger;
        this.siteApiService = siteApiService;
        this.promiseService = promiseService;
        this.route = route;
    }
    /// methods
    ngOnInit(): void {
        let self = this;
        self._parameterSubscription = self.route.params
            .subscribe(params => {
                self._entityId = +params['entityId'];
                self.getEntity();
            });
    }
    ngOnDestroy() {
        if (this._parameterSubscription &&
            this._parameterSubscription.unsubscribe) {
            this._parameterSubscription.unsubscribe();
        }
    }
    protected getExpertsFilter(): any {
        let filter = null;
        let filterOptionSiteId: number = Variable.isNotNullOrUndefined(this.entity) &&
        Variable.isNotNullOrUndefined(this.entity.id) &&
        this.entity.id !== 0 ?
            this.entity.id : null;
        let anyFilterOptionIsDefined: boolean = Variable.isNotNullOrUndefined(filterOptionSiteId);
        if (anyFilterOptionIsDefined) {
            filter = {};
            if (Variable.isNotNullOrUndefined(filterOptionSiteId)) {
                filter.siteId = filterOptionSiteId;
            }
        }
        return filter;
    }
    protected getBeveragesFilter(): any {
        let filter = null;
        let filterOptionSiteId: number = Variable.isNotNullOrUndefined(this.entity) &&
        Variable.isNotNullOrUndefined(this.entity.id) &&
        this.entity.id !== 0 ?
            this.entity.id : null;
        let anyFilterOptionIsDefined: boolean = Variable.isNotNullOrUndefined(filterOptionSiteId);
        if (anyFilterOptionIsDefined) {
            filter = {};
            if (Variable.isNotNullOrUndefined(filterOptionSiteId)) {
                filter.siteId = filterOptionSiteId;
            }
        }
        return filter;
    }
    protected getRoutesFilter(): any {
        let filter = null;
        let filterOptionSiteId: number = Variable.isNotNullOrUndefined(this.entity) &&
        Variable.isNotNullOrUndefined(this.entity.id) &&
        this.entity.id !== 0 ?
            this.entity.id : null;
        let anyFilterOptionIsDefined: boolean = Variable.isNotNullOrUndefined(filterOptionSiteId);
        if (anyFilterOptionIsDefined) {
            filter = {};
            if (Variable.isNotNullOrUndefined(filterOptionSiteId)) {
                filter.siteId = filterOptionSiteId;
            }
        }
        return filter;
    }
    protected getEntity(): Promise<void> {
        let self = this;
        self.promiseService.applicationPromises.sites.get.entityId = self._entityId;
        self.promiseService.applicationPromises.sites.get.promise = self.siteApiService
            .get(self._entityId)
            .then(function (response: SiteEntity): Promise<void> {
                self.entity = response;
                return Promise.resolve();
            })
            .then(
                () => {
                    self.promiseService.applicationPromises.sites.get.promise = null;
                    self.promiseService.applicationPromises.sites.get.entityId = null;
                },
                () => {
                    self.promiseService.applicationPromises.sites.get.promise = null;
                    self.promiseService.applicationPromises.sites.get.entityId = null;
                });
        return self.promiseService.applicationPromises.sites.get.promise;
    }
    /// notifications
    protected patchSiteContacts(newContacts: string) {
        let self = this;
        self.promiseService.applicationPromises.sites.patch.contactsPromise = self.siteApiService
            .patchContacts(self.entity.id, newContacts)
            .then(
                function(): void {
                    self.entity.contacts = newContacts;
                    self.promiseService.applicationPromises.sites.patch.contactsPromise = null;
                },
                function(): void {
                    self.promiseService.applicationPromises.sites.patch.contactsPromise = null;
                });
    }
}
