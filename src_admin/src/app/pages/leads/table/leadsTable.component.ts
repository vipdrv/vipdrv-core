import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, Extensions, PromiseService, ConsoleLogger, ILogger } from './../../../utils/index';
import { IAuthorizationService, AuthorizationService } from './../../../services/index';
import { ILeadApiService, LeadApiService, GetAllResponse } from './../../../services/index';
import { ILeadEntityPolicyService, LeadEntityPolicyService } from './../../../services/index';
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
    protected leadEntityPolicy: ILeadEntityPolicyService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        authorizationManager: AuthorizationService,
        leadApiService: LeadApiService,
        leadEntityPolicy: LeadEntityPolicyService) {
        this.logger = logger;
        this.authorizationManager = authorizationManager;
        this.leadApiService = leadApiService;
        this.leadEntityPolicy = leadEntityPolicy;
        this.logger.logDebug('LeadsTableComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        this.pageNumber = Variable.isNotNullOrUndefined(this.pageNumber) ? this.pageNumber : this._defaultPageNumber;
        this.pageSize = Variable.isNotNullOrUndefined(this.pageSize) ? this.pageSize : this._defaultPageSize;
        this.sorting = Variable.isNotNullOrUndefined(this.sorting) ? this.sorting : this._defaultSorting;
        this.filter = Variable.isNotNullOrUndefined(this.filter) ? this.filter : this._defaultFilter;
        const self = this;
        self.firstLoadingPromise = self
            .getAllEntities()
            .then(
                () => {
                    self.firstLoadingPromise = null;
                },
                () => {
                    self.firstLoadingPromise = null;
                }
            );
    }
    protected getAllEntities(): Promise<void> {
        const self = this;
        self.logger.logTrase('LeadsTableComponent: Get all entities called.');
        const filter = Object.assign({}, self.filter);
        filter.userId = this.authorizationManager.currentUserId;
        self.extendFilter(filter);
        self.getAllPromise = self.leadApiService
            .getAll(self.pageNumber - 1, self.pageSize, self.sorting, filter)
            .then(function (response: GetAllResponse<LeadEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            })
            .then(
                () => self.getAllPromise = null,
                () => self.getAllPromise = null);
        return self.getAllPromise;
    }
    protected tryModalOpenInfo(id: number): Promise<void> {
        const self = this;
        let actionPromise: Promise<void>;
        const entityToSelect = self.items.find((item: LeadEntity) => item.id === id);
        if (Variable.isNotNullOrUndefined(entityToSelect) &&
            this.isGetAllowed(entityToSelect) &&
            !this.isGetDisabled(entityToSelect)) {
            actionPromise = self.loadDetalizedEntity(id)
                .then(function (entity: LeadEntity): Promise<void> {
                    self.entity = entity;
                    return self.modalInfo.open();
                });
        } else {
            actionPromise = Promise.resolve();
        }
        return actionPromise;
    }
    protected modalDismiss(): Promise<void> {
        this.entity = null;
        return this.modalInfo.dismiss();
    }
    protected exportDataToExcel(): Promise<void> {
        const self = this;
        self.logger.logTrase(`LeadsTableComponent: Export data to excel called.`);
        const filter = Object.assign({}, self.filter)
        self.extendFilter(filter);
        self.exportToExcelPromise = self.leadApiService
            /// #40 - download All entities from Lead table (ofc its stupid move - uncomment after see this)
            ///.exportToExcel(self.pageNumber - 1, self.pageSize, self.sorting, filter)
            .exportToExcel(
                null,
                null,
                self.sorting,
                { 'userId': this.authorizationManager.currentUserId })
            .then(function (response: string): Promise<void> {
                window.open(response, '_self', '');
                return Promise.resolve();
            })
            .then(
                () => self.exportToExcelPromise = null,
                () => self.exportToExcelPromise = null);
        return self.exportToExcelPromise;
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
    protected applyFilters(): Promise<void> {
        const filtersWereNotChanged: boolean =
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
            if (this.pageNumber === this._defaultPageNumber) {
                return this.getAllEntities();
            } else {
                // js hack to start this operation after binding finished; also auto initiate change page event
                setTimeout(() => this.pageNumber = this._defaultPageNumber, 0);
                return Promise.resolve();
            }
        } else {
            return Promise.resolve();
        }
    }
    protected getClassesForLeadTableRow(entity: LeadEntity): any {
        const result = {
            'leads-table-body-row': true,
            'leads-table-body-row-disabled': false,
            'leads-table-body-row-processing': false
        };
        if (this.isGetProcessing(entity)) {
            result['leads-table-body-row'] = false;
            result['leads-table-body-row-processing'] = true;
        } else if (this.isGetDisabled(entity)) {
            result['leads-table-body-row'] = false;
            result['leads-table-body-row-disabled'] = true;
        }
        return result;
    }
    /// predicates
    protected isSelectedEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
    }
    /// helpers
    protected loadDetalizedEntity(id: number): Promise<LeadEntity> {
        const self = this;
        self.logger.logTrase(`LeadsTableComponent: Get entity (id = ${id}) called.`);
        self.getEntityId = id;
        self.getEntityPromise = self.leadApiService
            .get(id)
            .then(
                (entity: LeadEntity): LeadEntity => {
                    self.getEntityId = null;
                    self.getEntityPromise = null;
                    return entity;
                },
                () => {
                    self.getEntityId = null;
                    self.getEntityPromise = null;
                });
        return self.getEntityPromise;
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
            const currentSyncKey: string = Extensions.generateGuid();
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
    /// operation helpers
    // refresh button
    isRefreshAllowed(): boolean {
        return this.leadEntityPolicy.canGet();
    }
    isRefreshDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise) ||
            Variable.isNotNullOrUndefined(this.getEntityPromise);
    }
    isRefreshProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise);
    }
    // get entity
    isGetAllowed(entity: LeadEntity): boolean {
        return this.leadEntityPolicy.canGetEntity(entity);
    }
    isGetDisabled(entity: LeadEntity): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise) ||
            Variable.isNotNullOrUndefined(this.getEntityPromise);
    }
    isGetProcessing(entity: LeadEntity): boolean {
        return Variable.isNotNullOrUndefined(this.getEntityPromise) &&
            Variable.isNotNullOrUndefined(entity) &&
            entity.id === this.getEntityId;
    }
    // export to excel
    isExportToExcelAllowed(): boolean {
        return this.leadEntityPolicy.canExportDataToExcel();
    }
    isExportToExcelDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.exportToExcelPromise);
    }
    isExportToExcelProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.exportToExcelPromise);
    }
    // pagination
    isPageSizeChangeAllowed(): boolean {
        return true;
    }
    isPageSizeChangeDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise) ||
            Variable.isNotNullOrUndefined(this.getEntityPromise);
    }
    isPaginationAllowed(): boolean {
        return this.pageSize < this.totalCount;
    }
    isPaginationDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise) ||
            Variable.isNotNullOrUndefined(this.getEntityPromise);
    }
    // filters
    isFilteringAllowed(): boolean {
        return true;
    }
    isFilteringDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise) ||
            Variable.isNotNullOrUndefined(this.getEntityPromise);
    }
    /// promise manager
    protected firstLoadingPromise: Promise<void>;
    protected getAllPromise: Promise<void>;
    protected getEntityId: number;
    protected getEntityPromise: Promise<LeadEntity>;
    protected exportToExcelPromise: Promise<void>;
}
