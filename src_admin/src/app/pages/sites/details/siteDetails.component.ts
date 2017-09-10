import { Component, OnInit, OnDestroy } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Variable } from './../../../utils/index';
import { SiteEntity } from './../../../entities/index';
import { ISiteApiService, SiteApiService } from './../../../services/serverApi/index';
@Component({
    selector: 'site-details',
    styleUrls: ['./siteDetails.scss'],
    templateUrl: './siteDetails.html',
})
export class SiteDetailsComponent implements OnInit, OnDestroy {
    /// fields
    private _entityId: number;
    private _parameterSubscription: any; // type should be Subscription;
    private _isInitialized: boolean;
    protected entity: SiteEntity;
    /// injected dependencies
    protected siteApiService: ISiteApiService;
    /// ctor
    constructor(siteApiService: SiteApiService, private route: ActivatedRoute) {
        this.siteApiService = siteApiService;
        this._isInitialized = false;
    }
    /// methods
    ngOnInit(): void {
        let self = this;
        self._parameterSubscription = self.route.params
            .subscribe(params => {
                self._isInitialized = false;
                self._entityId = +params['entityId'];
                self.getEntity()
                    .then(() => self._isInitialized = true);
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
    protected getEntity(): Promise<void> {
        let self = this;
        let operationPromise = self.siteApiService
            .get(self._entityId)
            .then(function (response: SiteEntity): Promise<void> {
                self.entity = response;
                return Promise.resolve();
            });
        return operationPromise;
    }
    /// predicates
    protected isInitialized(): boolean {
        return this._isInitialized;
    }
}
