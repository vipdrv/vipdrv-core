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
                self.initializeNotificationEntities();
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
    protected patchContactsPromise: Promise<void> = null;
    protected initializeNotificationEntities() {
        this.initializeEmailEntities();
        this.initializeSMSEntities();
    }
    protected saveSiteContacts(): Promise<void> {
        let self = this;
        self.entity.contacts =
            self.emailEntities.map((item) => item.value).join(',') + ';' +
            self.smsEntities.map((item) => item.value).join(',');
        self.patchContactsPromise = self.siteApiService
            .patchContacts(self.entity.id, self.entity.contacts)
            .then(
                () => self.patchContactsPromise = null,
                () => self.patchContactsPromise = null);
        return self.patchContactsPromise;
    }
    protected resetSiteContacts(): void {
        this.initializeNotificationEntities();
    }
    protected emailEntities: Array<any>;
    protected newEmailEntity: any;
    protected initializeEmailEntities(): void {
        this.emailEntities = this.parseToValueObj(this.entity.contacts, ';', ',', 0);
        this.newEmailEntity = { value: '' };
    }
    protected deleteEmailFromContacts(emailEntity: any): void {
        let index = this.emailEntities.findIndex((r) => r === emailEntity);
        if (index > -1) {
            this.emailEntities.splice(index, 1);
        }
    }
    protected addNewEmailEntity(): void {
        this.emailEntities.push(this.newEmailEntity);
        this.newEmailEntity = { value: '' };
    }
    protected isNewEmailValid(): boolean {
        return Extensions.emailRegExp.test(this.newEmailEntity.value);
    }
    protected smsEntities: Array<any>;
    protected newSMSEntity: any;
    protected initializeSMSEntities(): void {
        this.smsEntities = this.parseToValueObj(this.entity.contacts, ';', ',', 1);
        this.newSMSEntity = { value: '' };
    }
    protected deleteSMSFromContacts(smsEntity: any): void {
        let index = this.smsEntities.findIndex((r) => r === smsEntity);
        if (index > -1) {
            this.smsEntities.splice(index, 1);
        }
    }
    protected addNewSMSEntity(): void {
        this.smsEntities.push(this.newSMSEntity);
        this.newSMSEntity = { value: '' };
    }
    protected isNewSMSValid(): boolean {
        return /^\d\d\d\d\d\d\d\d\d\d$/.test(this.newSMSEntity.value);
    }
    private parseToValueObj(str: string, globalSeparator: string, localSeparator: string, globalPosition: number): Array<any> {
        let result = [];
        if (str && str.indexOf(globalSeparator) > -1) {
            let arr = str.split(globalSeparator);
            if (arr.length > globalPosition && arr[globalPosition] !== '') {
                let values: Array<string> = arr[globalPosition].split(localSeparator);
                for (let item of values) {
                    result.push({ value: item });
                }
            }
        }
        return result
    }
}
