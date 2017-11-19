import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, Extensions, PromiseService, ConsoleLogger, ILogger } from './../../../utils/index';
import { IAuthorizationService, AuthorizationService } from './../../../services/index';
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
    private _defaultPageSize: number = 10;
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
    protected authorizationManager: IAuthorizationService;
    protected leadApiService: ILeadApiService;
    protected promiseService: PromiseService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        authorizationManager: AuthorizationService,
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
        let filter = Object.assign({}, self.filter);
        filter.userId = Variable.isNullOrUndefined(this.authorizationManager.lastUser) ?
            null : this.authorizationManager.lastUser.userId;
        self.extendFilter(filter);
        self.promiseService.applicationPromises.leads.getAll = self.leadApiService
            .getAll(self.pageNumber - 1, self.pageSize, self.sorting, filter)
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
        let filter = Object.assign({}, self.filter)
        self.extendFilter(filter);
        let userId = Variable.isNullOrUndefined(this.authorizationManager.lastUser) ?
            null : this.authorizationManager.lastUser.userId;
        self.promiseService.applicationPromises.leads.exportToExcel = self.leadApiService
            /// #40 - download All entities from Lead table (ofc its stupid move - uncomment after see this)
            ///.exportToExcel(self.pageNumber - 1, self.pageSize, self.sorting, filter)
            .exportToExcel(null, null, self.sorting, { 'userId': userId })
            .then(function (response: string): Promise<void> {
                window.open(response, '_self', '');
                return Promise.resolve();
            })
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
    /// table filters
    protected tableFilters: any = {
        recievedDateTime: null,
        firstName: null,
        secondName: null,
        site: null,
        email: null,
        phone: null,
        expert: null,
        route: null,
        beverage: null
    };
    /// timeout to apply filters
    protected msTimeout: number = 3000;
    protected filterSyncKey: string = null;
    tableFilterValueChanged(newValue: string, key: string): void {
        if (this.tableFilters[key] !== newValue) {
            this.tableFilters[key] = newValue;
            let currentSyncKey: string = Extensions.generateGuid();
            this.filterSyncKey = currentSyncKey;
            setTimeout(
                () => {
                    if (this.filterSyncKey === currentSyncKey) {
                        this.filterSyncKey = null;
                        this.getAllEntities();
                    }
                },
                this.msTimeout);
        }
    }
    protected extendFilter(filter: any): void {
        if (Variable.isNullOrUndefined(filter)) {
            throw new Error('Argument exception! (extendFilter requires defined argument filter)');
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.firstName) && this.tableFilters.firstName !== '') {
            filter.firstName = this.tableFilters.firstName;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.secondName) && this.tableFilters.secondName !== '') {
            filter.secondName = this.tableFilters.secondName;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.site) && this.tableFilters.site !== '') {
            filter.site = this.tableFilters.site;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.email) && this.tableFilters.email !== '') {
            filter.email = this.tableFilters.email;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.phone) && this.tableFilters.phone !== '') {
            filter.phone = this.tableFilters.phone;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.expert) && this.tableFilters.expert !== '') {
            filter.expert = this.tableFilters.expert;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.route) && this.tableFilters.route !== '') {
            filter.route = this.tableFilters.route;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.beverage) && this.tableFilters.beverage !== '') {
            filter.beverage = this.tableFilters.beverage;
        }
    }
    /// onblur filters
    protected oldTableFilters: any = {
        recievedDateTime: null,
        firstName: null,
        secondName: null,
        site: null,
        email: null,
        phone: null,
        expert: null,
        route: null,
        beverage: null
    };
    applyFilters(): Promise<void> {
        let filtersWereNotChanged: boolean =
            this.tableFilters.recievedDateTime === this.oldTableFilters.recievedDateTime &&
            this.tableFilters.firstName === this.oldTableFilters.firstName &&
            this.tableFilters.secondName === this.oldTableFilters.secondName &&
            this.tableFilters.site === this.oldTableFilters.site &&
            this.tableFilters.email === this.oldTableFilters.email &&
            this.tableFilters.phone === this.oldTableFilters.phone &&
            this.tableFilters.expert === this.oldTableFilters.expert &&
            this.tableFilters.route === this.oldTableFilters.route &&
            this.tableFilters.beverage === this.oldTableFilters.beverage;
        if (!filtersWereNotChanged) {
            this.oldTableFilters = {};
            this.oldTableFilters.recievedDateTime = this.tableFilters.recievedDateTime;
            this.oldTableFilters.firstName = this.tableFilters.firstName;
            this.oldTableFilters.secondName = this.tableFilters.secondName;
            this.oldTableFilters.site = this.tableFilters.site;
            this.oldTableFilters.email = this.tableFilters.email;
            this.oldTableFilters.phone = this.tableFilters.phone;
            this.oldTableFilters.expert = this.tableFilters.expert;
            this.oldTableFilters.route = this.tableFilters.route;
            this.oldTableFilters.beverage = this.tableFilters.beverage;
            return this.getAllEntities();
        } else {
            return Promise.resolve();
        }
    }
}
