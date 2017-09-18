import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { Router} from '@angular/router';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, PromiseService, ConsoleLogger, ILogger } from './../../../utils/index';
import { IAuthorizationManager, AuthorizationManager } from './../../../utils/index';
import { ISiteApiService, SiteApiService, GetAllResponse } from './../../../services/serverApi/index';
import { SiteEntity } from './../../../entities/index';
@Component({
    selector: 'sites-table',
    styleUrls: ['./sitesTable.scss'],
    templateUrl: './sitesTable.html'
})
export class SitesTableComponent implements OnInit {
    /// inputs
    @Input() pageNumber: number;
    @Input() pageSize: number;
    @Input() sorting: string;
    @Input() filter: any;
    /// modals
    @ViewChild('siteDetailsModal')
    protected modal: ModalComponent;
    /// settings
    private _defaultPageNumber: number = 1;
    private _defaultPageSize: number = 10;
    private _defaultSorting: string = 'name asc';
    private _defaultFilter: any = null;
    protected maxPaginationSize: number = 3;
    protected pageSizeValues: Array<number> = [5, 10, 25, 50, 100];
    /// data fields
    protected totalCount: number;
    protected items: Array<SiteEntity>;
    protected entity: SiteEntity;
    /// injected dependencies
    protected logger: ILogger;
    protected authorizationManager: IAuthorizationManager;
    protected siteApiService: ISiteApiService;
    protected promiseService: PromiseService;
    protected router: Router;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        authorizationManager: AuthorizationManager,
        siteApiService: SiteApiService,
        promiseService: PromiseService,
        router: Router) {
        this.logger = logger;
        this.authorizationManager = authorizationManager;
        this.siteApiService = siteApiService;
        this.promiseService = promiseService;
        this.router = router;
    }
    /// methods
    ngOnInit(): void {
        this.pageNumber = Variable.isNotNullOrUndefined(this.pageNumber) ? this.pageNumber : this._defaultPageNumber;
        this.pageSize = Variable.isNotNullOrUndefined(this.pageSize) ? this.pageSize : this._defaultPageSize;
        this.sorting = Variable.isNotNullOrUndefined(this.sorting) ? this.sorting : this._defaultSorting;
        this.filter = Variable.isNotNullOrUndefined(this.filter) ? this.filter : this._defaultFilter;
        this.getAllEntities();
    }
    protected redirectToEntityDetails(entity: SiteEntity): Promise<boolean> {
        let actionPromise: Promise<boolean>;
        if (Variable.isNotNullOrUndefined(entity)) {
            actionPromise = this.router.navigate([`./pages/sites/${String(entity.id)}`]);
        } else {
            actionPromise = Promise.resolve(false);
        }
        return actionPromise;
    }
    protected getAllEntities(): Promise<void> {
        let self = this;
        self.promiseService.applicationPromises.sites.getAll.promise = self.siteApiService
            .getAll(self.pageNumber - 1, self.pageSize, self.sorting, self.filter)
            .then(function (response: GetAllResponse<SiteEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            })
            .then(
                () => self.promiseService.applicationPromises.sites.getAll.promise = null,
                () => self.promiseService.applicationPromises.sites.getAll.promise = null);
        return self.promiseService.applicationPromises.sites.getAll.promise;
    }
    protected deleteEntity(id: number): Promise<void> {
        let self = this;
        self.promiseService.applicationPromises.sites.delete.entityId = id;
        self.promiseService.applicationPromises.sites.delete.promise = self.siteApiService
            .delete(id)
            .then(function (): Promise<void> {
                let elementIndex = self.items.findIndex((item: SiteEntity) => item.id === id);
                self.items.splice(elementIndex, 1);
                return Promise.resolve();
            })
            .then(
                () => {
                    self.promiseService.applicationPromises.sites.delete.promise = null;
                    self.promiseService.applicationPromises.sites.delete.entityId = null;
                },
                () => {
                    self.promiseService.applicationPromises.sites.delete.promise = null;
                    self.promiseService.applicationPromises.sites.delete.entityId = null;
                });
        return self.promiseService.applicationPromises.sites.delete.promise;
    }
    protected modalOpenCreate(): Promise<void> {
        let self = this;
        self.entity = new SiteEntity();
        self.entity.userId = this.authorizationManager.lastUser.id;
        self.modal.open();
        return Promise.resolve();
    }
    protected modalOpenEdit(id: number): Promise<void> {
        let self = this;
        self.entity = self.items.find((item: SiteEntity) => item.id === id);
        self.modal.open();
        self.promiseService.applicationPromises.sites.get.entityId = id;
        self.promiseService.applicationPromises.sites.get.promise = self.siteApiService
            .get(id)
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
    protected modalApply() {
        let self = this;
        self.promiseService.applicationPromises.sites.addOrUpdate.entityId =
            self.entity.id ? self.entity.id : null;
        self.promiseService.applicationPromises.sites.addOrUpdate.promise = (self.entity.id ?
            self.siteApiService.update(self.entity) : self.siteApiService.create(self.entity))
            .then(function (entity: SiteEntity): Promise<void> {
                let elementIndex = self.items.findIndex((item: SiteEntity) => item.id === entity.id);
                if (elementIndex !== -1) {
                    self.items.splice(elementIndex, 1, entity);
                } else {
                    self.items.push(entity);
                }
                self.entity = null;
                return self.modal.close();
            })
            .then(
                () => {
                    self.promiseService.applicationPromises.sites.addOrUpdate.promise = null;
                    self.promiseService.applicationPromises.sites.addOrUpdate.entityId = null;
                },
                () => {
                    self.promiseService.applicationPromises.sites.addOrUpdate.promise = null;
                    self.promiseService.applicationPromises.sites.addOrUpdate.entityId = null;
                });
        return self.promiseService.applicationPromises.sites.addOrUpdate.promise;
    }
    protected modalDismiss(): Promise<void> {
        this.entity = null;
        return this.modal.dismiss();
    }
    protected onPageNumberChanged(): void {
        // js hack to start this operation after binding finished
        setTimeout(() => this.getAllEntities(), 0);
    }
    protected onPageSizeChanged(): void {
        if (this.pageNumber === this._defaultPageNumber) {
            this.onPageNumberChanged();
        } else {
            // js hack to start this operation after binding finished; also auto initiate change page event
            setTimeout(() => this.pageNumber = this._defaultPageNumber, 0);
        }
    }
    protected getModalBodyBusyPromises() {
        let array: Array<Promise<void>> = [];
        if (this.promiseService.applicationPromises.sites.getAll.promise) {
            array.push(this.promiseService.applicationPromises.sites.getAll.promise);
        }
        if (this.promiseService.applicationPromises.sites.get.promise) {
            array.push(this.promiseService.applicationPromises.sites.get.promise);
        }
        if (this.promiseService.applicationPromises.sites.addOrUpdate.promise) {
            array.push(this.promiseService.applicationPromises.sites.addOrUpdate.promise);
        }
        return array;
    }
    /// predicates
    protected isBtnModalApplyBusy(): boolean {
        return !!this.promiseService.applicationPromises.sites.addOrUpdate.promise ||
            !!this.promiseService.applicationPromises.sites.delete.promise ||
            !!this.promiseService.applicationPromises.sites.get.promise;
    }
    protected isBtnCreateBusy(): boolean {
        return !!this.promiseService.applicationPromises.sites.addOrUpdate.promise ||
            !!this.promiseService.applicationPromises.sites.get.promise;
    }
    protected isBtnEditBusy(entityId: number): boolean {
        return !!this.promiseService.applicationPromises.sites.addOrUpdate.promise &&
            this.promiseService.applicationPromises.sites.addOrUpdate.entityId === entityId ||
            !!this.promiseService.applicationPromises.sites.delete.promise &&
            this.promiseService.applicationPromises.sites.delete.entityId === entityId ||
            !!this.promiseService.applicationPromises.sites.get.promise &&
            this.promiseService.applicationPromises.sites.get.entityId === entityId;
    }
    protected isBtnDeleteBusy(entityId: number): boolean {
        return !!this.promiseService.applicationPromises.sites.addOrUpdate.promise &&
            this.promiseService.applicationPromises.sites.addOrUpdate.entityId === entityId ||
            !!this.promiseService.applicationPromises.sites.delete.promise &&
            this.promiseService.applicationPromises.sites.delete.entityId === entityId ||
            !!this.promiseService.applicationPromises.sites.get.promise &&
            this.promiseService.applicationPromises.sites.get.entityId === entityId;
    }
    protected usePagination(): boolean {
        return this.pageSize < this.totalCount;
    }
    protected isSelectedEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
    }
}
