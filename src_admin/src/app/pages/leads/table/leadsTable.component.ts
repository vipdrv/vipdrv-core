import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, Extensions, PromiseService, ConsoleLogger, ILogger } from './../../../utils/index';
import { IAuthorizationService, AuthorizationService } from './../../../services/index';
import { ILeadApiService, LeadApiService, GetAllResponse } from './../../../services/index';
import { ILeadEntityPolicyService, LeadEntityPolicyService } from './../../../services/index';
import { ISiteApiService, SiteApiService } from './../../../services/index';
import { IExpertApiService, ExpertApiService } from './../../../services/index';
import { IRouteApiService, RouteApiService } from './../../../services/index';
import { IBeverageApiService, BeverageApiService } from './../../../services/index';
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
    // @Input() sorting: string;
    @Input() filter: any;
    /// modals
    @ViewChild('leadDetailsInfoModal')
    protected modalInfo: ModalComponent;
    /// settings
    private _defaultPageNumber: number = 1;
    private _defaultPageSize: number = 10;
    // private _defaultSorting: string = 'recievedUtc desc';
    private _defaultFilter: any = null;
    protected maxPaginationSize: number = 3;
    protected pageSizeValues: Array<number> = [5, 10, 25, 50, 100];
    /// data fields
    protected totalCount: number;
    protected items: Array<LeadEntity>;
    protected entity: LeadEntity;
    protected siteOptions: Array<any> = [
        {
            value: null,
            displayText: 'filters.all'
        },
    ];
    protected expertOptions: Array<any> = [
        {
            value: null,
            displayText: 'filters.all'
        },
    ];
    protected beverageOptions: Array<any> = [
        {
            value: null,
            displayText: 'filters.all'
        },
    ];
    protected routeOptions: Array<any> = [
        {
            value: null,
            displayText: 'filters.all'
        },
    ];
    /// injected dependencies
    protected logger: ILogger;
    protected authorizationManager: IAuthorizationService;
    protected leadApiService: ILeadApiService;
    protected leadEntityPolicy: ILeadEntityPolicyService;
    protected siteApiService: ISiteApiService;
    protected expertApiService: IExpertApiService;
    protected routeApiService: IRouteApiService;
    protected beverageApiService: IBeverageApiService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        authorizationManager: AuthorizationService,
        leadApiService: LeadApiService,
        leadEntityPolicy: LeadEntityPolicyService,
        siteApiService: SiteApiService,
        expertApiService: ExpertApiService,
        routeApiService: RouteApiService,
        beverageApiService: BeverageApiService) {
        this.logger = logger;
        this.authorizationManager = authorizationManager;
        this.leadApiService = leadApiService;
        this.leadEntityPolicy = leadEntityPolicy;
        this.siteApiService = siteApiService;
        this.expertApiService = expertApiService;
        this.routeApiService = routeApiService;
        this.beverageApiService = beverageApiService;
        this.logger.logDebug('LeadsTableComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        this.pageNumber = Variable.isNotNullOrUndefined(this.pageNumber) ? this.pageNumber : this._defaultPageNumber;
        this.pageSize = Variable.isNotNullOrUndefined(this.pageSize) ? this.pageSize : this._defaultPageSize;
        // this.sorting = Variable.isNotNullOrUndefined(this.sorting) ? this.sorting : this._defaultSorting;
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
        // now will not wait this (filters will be loaded in background without any reaction)
        self.fillFilters();
    }
    protected fillFilters(): Promise<any[]> {
        const fillFiltersPromises: [Promise<void>, Promise<void>] = [
            this.fillSitesFilter(),
            this.fillExpertsFilter(),
            this.fillBeveragesFilter(),
            this.fillRoutesFilter(),
        ];
        return Promise.all(fillFiltersPromises);
    }
    protected fillSitesFilter(): Promise<void> {
        const self = this;
        self.logger.logTrase('LeadsTableComponent: Get relations (all sites) called.');
        const filter = {
            userId: this.authorizationManager.currentUserId
        };
        return self.siteApiService
            .getAll(0, 50, 'name asc', filter)
            .then(function (response: GetAllResponse<any>): void {
                for (const site of response.items) {
                    self.siteOptions.push({
                        value: site.id,
                        displayText: site.name
                    });
                }
            })
            .then(
                () => {

                },
                () => {

                });
    }
    protected fillExpertsFilter(): Promise<void> {
        const self = this;
        self.logger.logTrase('LeadsTableComponent: Get relations (all experts) called.');
        const filter = {
            userId: this.authorizationManager.currentUserId
        };
        return self.expertApiService
            .getAll(0, 50, 'name asc', filter)
            .then(function (response: GetAllResponse<any>): void {
                for (const entity of response.items) {
                    self.expertOptions.push({
                        value: entity.id,
                        displayText: entity.name
                    });
                }
            })
            .then(
                () => {

                },
                () => {

                });
    }
    protected fillBeveragesFilter(): Promise<void> {
        const self = this;
        self.logger.logTrase('LeadsTableComponent: Get relations (all beverages) called.');
        const filter = {
            userId: this.authorizationManager.currentUserId
        };
        return self.beverageApiService
            .getAll(0, 50, 'name asc', filter)
            .then(function (response: GetAllResponse<any>): void {
                for (const entity of response.items) {
                    self.beverageOptions.push({
                        value: entity.id,
                        displayText: entity.name
                    });
                }
            })
            .then(
                () => {

                },
                () => {

                });
    }
    protected fillRoutesFilter(): Promise<void> {
        const self = this;
        self.logger.logTrase('LeadsTableComponent: Get relations (all routes) called.');
        const filter = {
            userId: this.authorizationManager.currentUserId
        };
        return self.routeApiService
            .getAll(0, 50, 'name asc', filter)
            .then(function (response: GetAllResponse<any>): void {
                for (const route of response.items) {
                    self.routeOptions.push({
                        value: route.id,
                        displayText: route.name
                    });
                }
            })
            .then(
                () => {

                },
                () => {

                });
    }
    protected getAllEntities(): Promise<void> {
        const self = this;
        self.logger.logTrase('LeadsTableComponent: Get all entities called.');
        const filter = Object.assign({}, self.filter);
        filter.userId = this.authorizationManager.currentUserId;
        self.extendFilter(filter);
        self.getAllPromise = self.leadApiService
            .getAll(self.pageNumber - 1, self.pageSize, self.buildSorting(), filter)
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
                    self.changeIsNew(entityToSelect);
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
            ///.exportToExcel(self.pageNumber - 1, self.pageSize, self.buildSorting(), filter)
            .exportToExcel(
                null,
                null,
                self.buildSorting(),
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
            this.tableFilters.siteId === this.oldTableFilters.siteId &&
            this.tableFilters.expertId === this.oldTableFilters.expertId &&
            this.tableFilters.routeId === this.oldTableFilters.routeId &&
            this.tableFilters.beverageId === this.oldTableFilters.beverageId &&
            this.tableFilters.fullName === this.oldTableFilters.fullName &&
            this.tableFilters.firstName === this.oldTableFilters.firstName &&
            this.tableFilters.secondName === this.oldTableFilters.secondName &&
            this.tableFilters.isReachedByManager === this.oldTableFilters.isReachedByManager &&
            this.tableFilters.site === this.oldTableFilters.site &&
            this.tableFilters.email === this.oldTableFilters.email &&
            this.tableFilters.phone === this.oldTableFilters.phone &&
            this.tableFilters.expert === this.oldTableFilters.expert &&
            this.tableFilters.route === this.oldTableFilters.route &&
            this.tableFilters.beverage === this.oldTableFilters.beverage;
        if (!filtersWereNotChanged) {
            this.oldTableFilters = {};
            this.oldTableFilters.recievedDateTime = this.tableFilters.recievedDateTime;
            this.oldTableFilters.fullName = this.tableFilters.fullName;
            this.oldTableFilters.firstName = this.tableFilters.firstName;
            this.oldTableFilters.secondName = this.tableFilters.secondName;
            this.oldTableFilters.isReachedByManager = this.tableFilters.isReachedByManager;
            this.oldTableFilters.siteId = this.tableFilters.siteId;
            this.oldTableFilters.expertId = this.tableFilters.expertId;
            this.oldTableFilters.routeId = this.tableFilters.routeId;
            this.oldTableFilters.beverageId = this.tableFilters.beverageId;
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
            'leads-table-body-row-processing': false,
            'leads-table-body-row-with-new-entity': false
        };
        if (this.isGetProcessing(entity)) {
            result['leads-table-body-row'] = false;
            result['leads-table-body-row-processing'] = true;
        } else if (this.isGetDisabled(entity)) {
            result['leads-table-body-row'] = false;
            result['leads-table-body-row-disabled'] = true;
        }
        if (entity.isNew) {
            result['leads-table-body-row-with-new-entity'] = true;
        }
        return result;
    }
    protected changeIsReachedByManager(entity: LeadEntity): Promise<void> {
        let actionPromise: Promise<void>;
        if (this.isReachedByManagerDisabled(entity)) {
            actionPromise = Promise.resolve();
        } else {
            const self = this;
            const oldValue: boolean = entity.isReachedByManager;
            self.logger.logTrase(`LeadsTableComponent: Patch isReachedByManager (entityId = ${entity.id}, value = ${!oldValue}) called.`);
            entity.isReachedByManager = !oldValue;
            self.reachedByManagerIdsPatchInProgress.push(entity.id);
            actionPromise = self.leadApiService
                .patchIsReachedByManager(entity.id, !oldValue)
                .then(
                    () => {
                        const index = self.reachedByManagerIdsPatchInProgress.indexOf(entity.id);
                        if (index > -1) {
                            self.reachedByManagerIdsPatchInProgress.splice(index, 1);
                        }
                    },
                    () => {
                        entity.isReachedByManager = oldValue;
                        const index = self.reachedByManagerIdsPatchInProgress.indexOf(entity.id);
                        if (index > -1) {
                            self.reachedByManagerIdsPatchInProgress.splice(index, 1);
                        }
                    }
                );
        }
        return actionPromise;
    }
    protected changeIsNew(entity: LeadEntity): Promise<void> {
        let actionPromise: Promise<void>;
        if (this.isChangeIsNewDisabled(entity)) {
            actionPromise = Promise.resolve();
        } else {
            const self = this;
            const oldValue: boolean = entity.isNew;
            self.logger.logTrase(`LeadsTableComponent: Patch isNew (entityId = ${entity.id}, value = ${!oldValue}) called.`);
            entity.isNew = !oldValue;
            self.isNewPatchInProgress.push(entity.id);
            actionPromise = self.leadApiService
                .patchIsNew(entity.id, !oldValue)
                .then(
                    () => {
                        const index = self.isNewPatchInProgress.indexOf(entity.id);
                        if (index > -1) {
                            self.isNewPatchInProgress.splice(index, 1);
                        }
                    },
                    () => {
                        entity.isNew = oldValue;
                        const index = self.isNewPatchInProgress.indexOf(entity.id);
                        if (index > -1) {
                            self.isNewPatchInProgress.splice(index, 1);
                        }

                    }
                );
        }
        return actionPromise;
    }
    /// predicates
    protected isSelectedEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
    }
    protected isReachedByManagerDisabled(entity: LeadEntity): boolean {
        return this.reachedByManagerIdsPatchInProgress.indexOf(entity.id) > -1;
    }
    protected isChangeIsNewDisabled(entity: LeadEntity): boolean {
        return !entity.isNew || this.isNewPatchInProgress.indexOf(entity.id) > -1;
    }
    /// helpers
    protected loadDetalizedEntity(id: number): Promise<LeadEntity> {
        const self = this;
        self.logger.logTrase(`LeadsTableComponent: Get detalized entity (id = ${id}) called.`);
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
        fullName: null,
        firstName: null,
        secondName: null,
        isReachedByManager: null,
        siteId: null,
        expertId: null,
        routeId: null,
        beverageId: null,
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
        if (Variable.isNotNullOrUndefined(
            this.tableFilters.recievedDateTime) &&
            this.tableFilters.recievedDateTime !== '') {
            filter.recievedDateTime = this.tableFilters.recievedDateTime;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.fullName) && this.tableFilters.fullName !== '') {
            filter.fullName = this.tableFilters.fullName;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.firstName) && this.tableFilters.firstName !== '') {
            filter.firstName = this.tableFilters.firstName;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.secondName) && this.tableFilters.secondName !== '') {
            filter.secondName = this.tableFilters.secondName;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.siteId)) {
            filter.siteId = this.tableFilters.siteId;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.expertId)) {
            filter.expertId = this.tableFilters.expertId;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.routeId)) {
            filter.routeId = this.tableFilters.routeId;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.beverageId)) {
            filter.beverageId = this.tableFilters.beverageId;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.isReachedByManager)) {
            filter.isReachedByManager = this.tableFilters.isReachedByManager;
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
        fullName: null,
        firstName: null,
        secondName: null,
        isReachedByManager: null,
        siteId: null,
        expertId: null,
        routeId: null,
        beverageId: null,
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
    /// select controls service data
    protected dateFilterOptions: Array<any> = [
        {
            value: null,
            displayText: 'filters.date.allTime'
        },
        {
            value: Extensions.todayValue(),
            displayText: 'filters.date.today'
        },
        {
            value: Extensions.lastWeekValue(),
            displayText: 'filters.date.lastWeek'
        },
        {
            value: Extensions.thisMonthValue(),
            displayText: 'filters.date.thisMonth'
        },
        {
            value: Extensions.lastMonthValue(),
            displayText: 'filters.date.lastMonth'
        },
    ];
    protected isReachedByManagerFilterOptions: Array<any> = [
        {
            value: null,
            displayText: 'filters.isReachedByManager.all'
        },
        {
            value: true,
            displayText: 'filters.isReachedByManager.reached'
        },
        {
            value: false,
            displayText: 'filters.isReachedByManager.notReached'
        },
    ];
    /// sorting
    protected sortingRules: Array<any> = [
        {
          field: 'recievedUtc',
          isAsc: false,
        },
    ];
    protected isSortingAsc(targetField: string): boolean {
        const rule = this.getSortingRule(targetField);
        return Variable.isNotNullOrUndefined(rule) && rule.isAsc;
    }
    protected isSortingDesc(targetField: string): boolean {
        const rule = this.getSortingRule(targetField);
        return Variable.isNotNullOrUndefined(rule) && !rule.isAsc;
    }
    protected getSortingRule(targetField: string): any {
        const elems = this.sortingRules.filter(r => r.field === targetField);
        return elems.length === 1 ? elems[0] : null;
    }
    protected getSortingIndex(targetField: string): number {
        let result: number;
        if (this.sortingRules.length > 1) {
            const index = this.sortingRules.findIndex(r => r.field === targetField);
            if (index > -1) {
                result = index + 1;
            }
        } else {
            result = null;
        }
        return result;
    }
    protected changeSorting(targetField: string): Promise<void> {
        let actionPromise: Promise<void>;
        if (this.isChangeSortingDisabled()) {
            actionPromise = Promise.resolve();
        } else {
            const rule = this.getSortingRule(targetField);
            if (Variable.isNotNullOrUndefined(rule)) {
                if (rule.isAsc) {
                    rule.isAsc = false;
                } else {
                    const index = this.sortingRules.findIndex(r => r.field === targetField);
                    if (index > -1) {
                        this.sortingRules.splice(index, 1);
                    }
                }
            } else {
                this.sortingRules.push({
                    field: targetField,
                    isAsc: true,
                });
            }
            actionPromise = this.getAllEntities();
        }
        return actionPromise;
    }
    protected isChangeSortingDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise) ||
            Variable.isNotNullOrUndefined(this.getEntityPromise);
    }
    protected buildSorting(): string {
        let sorting: string = null;
        if (this.sortingRules.length > 0) {
            sorting = '';
            for (let i = 0; i < this.sortingRules.length; i++) {
                sorting += `${this.sortingRules[i].field} ${this.sortingRules[i].isAsc ? 'asc' : 'desc'}`;
                if (i < this.sortingRules.length - 1) {
                    sorting += ', '
                }
            }
        }
        return sorting;
    }
    /// promise manager
    protected firstLoadingPromise: Promise<void>;
    protected getAllPromise: Promise<void>;
    protected getEntityId: number;
    protected getEntityPromise: Promise<LeadEntity>;
    protected exportToExcelPromise: Promise<void>;
    protected reachedByManagerIdsPatchInProgress: Array<number> = new Array<number>();
    protected isNewPatchInProgress: Array<number> = new Array<number>();
}
