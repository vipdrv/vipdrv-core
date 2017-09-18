import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, Extensions, PromiseService, ConsoleLogger, ILogger } from './../../../utils/index';
import { IAuthorizationManager, AuthorizationManager } from './../../../utils/index';
import { ILeadApiService, LeadApiService, GetAllResponse } from './../../../services/serverApi/index';
import { LeadEntity } from './../../../entities/index';
@Component({
    selector: 'leads-table',
    styleUrls: ['./leadsTable.scss'],
    templateUrl: './leadsTable.html'
})
export class LeadsTableComponent implements OnInit {
    /// inputs
    @Input() pageNumber: number;
    @Input() pageSize: number;
    @Input() sorting: string;
    @Input() filter: any;
    /// modals
    @ViewChild('leadDetailsInfoModal')
    protected modalInfo: ModalComponent;
    /// settings
    private _defaultPageNumber: number = 1;
    private _defaultPageSize: number = 5;
    private _defaultSorting: string = 'recievedUtc desc';
    private _defaultFilter: any = null;
    protected maxPaginationSize: number = 3;
    protected pageSizeValues: Array<number> = [5, 10, 25, 50, 100];
    /// data fields
    protected totalCount: number;
    protected items: Array<LeadEntity>;
    protected entity: LeadEntity;
    /// injected dependencies
    protected logger: ILogger;
    protected authorizationManager: IAuthorizationManager;
    protected leadApiService: ILeadApiService;
    protected promiseService: PromiseService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        authorizationManager: AuthorizationManager,
        leadApiService: LeadApiService,
        promiseService: PromiseService) {
        this.logger = logger;
        this.authorizationManager = authorizationManager;
        this.leadApiService = leadApiService;
        this.promiseService = promiseService;
    }
    /// methods
    ngOnInit(): void {
        this.pageNumber = Variable.isNotNullOrUndefined(this.pageNumber) ? this.pageNumber : this._defaultPageNumber;
        this.pageSize = Variable.isNotNullOrUndefined(this.pageSize) ? this.pageSize : this._defaultPageSize;
        this.sorting = Variable.isNotNullOrUndefined(this.sorting) ? this.sorting : this._defaultSorting;
        this.filter = Variable.isNotNullOrUndefined(this.filter) ? this.filter : this._defaultFilter;
        this.getAllEntities();
    }
    protected getAllEntities(): Promise<void> {
        let self = this;
        self.promiseService.applicationPromises.leads.getAll = self.leadApiService
            .getAll(self.pageNumber - 1, self.pageSize, self.sorting, self.filter)
            .then(function (response: GetAllResponse<LeadEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            })
            .then(
                () => self.promiseService.applicationPromises.leads.getAll = null,
                () => self.promiseService.applicationPromises.leads.getAll = null);
        return self.promiseService.applicationPromises.leads.getAll;
    }
    protected deleteEntity(id: number): Promise<void> {
        let self = this;
        self.promiseService.applicationPromises.leads.delete = self.leadApiService
            .delete(id)
            .then(function (): Promise<void> {
                let elementIndex = self.items.findIndex((item: LeadEntity) => item.id === id);
                self.items.splice(elementIndex, 1);
                return Promise.resolve();
            })
            .then(
                () => self.promiseService.applicationPromises.leads.delete = null,
                () => self.promiseService.applicationPromises.leads.delete = null);
        return self.promiseService.applicationPromises.leads.delete;
    }
    protected modalOpenInfo(id: number): Promise<void> {
        let self = this;
        self.entity = self.items.find((item: LeadEntity) => item.id === id);
        self.modalInfo.open();
        self.promiseService.applicationPromises.leads.get = self.leadApiService
            .get(id)
            .then(function (response: LeadEntity): Promise<void> {
                self.entity = response;
                return Promise.resolve();
            })
            .then(
                () => self.promiseService.applicationPromises.leads.get = null,
                () => self.promiseService.applicationPromises.leads.get = null);
        return self.promiseService.applicationPromises.leads.get;
    }
    protected modalDismiss(): Promise<void> {
        this.entity = null;
        return this.modalInfo.dismiss();
    }
    protected exportDataToExcel(): Promise<void> {
        let self = this;
        /// TODO: remove this stub implementation and use export to excel method from api service here
        self.logger.logWarning('Stub implementation of exportDataToExcel was called.');
        self.promiseService.applicationPromises.leads.exportToExcel = Extensions
            .delay(5000)
            .then(
                () => self.promiseService.applicationPromises.leads.exportToExcel = null,
                () => self.promiseService.applicationPromises.leads.exportToExcel = null);
        return self.promiseService.applicationPromises.leads.exportToExcel;
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
    /// predicates
    protected usePagination(): boolean {
        return this.pageSize < this.totalCount;
    }
    protected isSelectedEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
    }
}
