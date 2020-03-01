import { Component, OnInit, Input, Output, ViewChild, EventEmitter } from '@angular/core';
import { Router } from '@angular/router';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { ApplicationConstants } from './../../../app.constants';
import { SitesConstants } from './../sites.constants';
import { Variable, ILogger, ConsoleLogger } from './../../../utils/index';
import { IAuthorizationService, AuthorizationService } from './../../../services/index';
import { ISiteApiService, SiteApiService, GetAllResponse } from './../../../services/serverApi/index'
import { IUserApiService, UserApiService } from './../../../services/serverApi/index';
import { ISiteEntityPolicyService, SiteEntityPolicyService } from './../../../services/index';
import { ISiteValidationService, SiteValidationService } from './../../../services/index';
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
    @Input() filter: any;
    /// outputs
    @Output() onEntityChanged: EventEmitter<any> = new EventEmitter<any>();
    @Output() resetForceAcceptImage: EventEmitter<void> = new EventEmitter<void>();
    /// children
    @ViewChild('confirmationDeleteModal')
    protected confirmationDeleteModal: ModalComponent;
    @ViewChild('editModal')
    protected editModal: ModalComponent;
    /// service fields
    protected maxPaginationSize: number = 3;
    protected pageSizeValues: Array<number> = [5, 10, 25, 50, 100];
    /// data fields
    protected ownerOptions: Array<any> = [
        {
            value: null,
            displayText: 'filters.all'
        },
    ];
    private _defaultPageNumber: number = 1;
    private _defaultPageSize: number = 50;
    private _defaultFilter: any = null;
    private _useValidation: boolean = false;
    protected forceAcceptImage: boolean = false;
    protected siteImageAlt: string = SitesConstants.siteImageAlt;
    /// promise fields
    protected firstLoadingPromise: Promise<void>;
    protected getAllPromise: Promise<void>;
    protected getPromise: Promise<void>;
    protected getEntityId: number;
    protected isGetPromiseForEdit: boolean;
    protected savePromise: Promise<void>;
    protected saveEntityId: number;
    protected deletePromise: Promise<void>;
    protected deleteEntityId: number;
    /// fields
    protected switcherSettings = ApplicationConstants.switcherSettings;
    protected totalCount: number;
    protected items: Array<SiteEntity>;
    protected selectedEntity: SiteEntity;
    // protected defaultSiteSchedule: Array<WorkingHours>;
    protected isWeekScheduleOpenedByDefault: boolean = false;
    /// injected dependencies
    protected logger: ILogger;
    protected router: Router;
    protected authorizationManager: IAuthorizationService;
    protected entityApiService: ISiteApiService;
    protected entityPolicyService: ISiteEntityPolicyService;
    protected entityValidationService: ISiteValidationService;
    protected userApiService: IUserApiService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        router: Router,
        authorizationManager: AuthorizationService,
        entityApiService: SiteApiService,
        entityPolicyService: SiteEntityPolicyService,
        entityValidationService: SiteValidationService,
        userApiService: UserApiService) {
        this.logger = logger;
        this.router = router;
        this.authorizationManager = authorizationManager;
        this.entityApiService = entityApiService;
        this.entityPolicyService = entityPolicyService;
        this.entityValidationService = entityValidationService;
        this.userApiService = userApiService;
        this.logger.logDebug('SitesTableComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        const self = this;
        this.pageNumber = Variable.isNotNullOrUndefined(this.pageNumber) ? this.pageNumber : this._defaultPageNumber;
        this.pageSize = Variable.isNotNullOrUndefined(this.pageSize) ? this.pageSize : this._defaultPageSize;
        this.filter = Variable.isNotNullOrUndefined(this.filter) ? this.filter : this._defaultFilter;
        self.firstLoadingPromise = self
            .getAllEntities()
            .then(
                () => self.firstLoadingPromise = null,
                () => self.firstLoadingPromise = null);
        self.fillFilters();
        this.logger.logDebug('SitesTableComponent: Component has been initialized.');
    }
    protected fillFilters(): Promise<any> {
        return this.fillOwnersFilter();
    }
    protected fillOwnersFilter(): Promise<void> {
        const self = this;
        self.logger.logTrase('SitesTableComponent: Get relations (all owners) called.');
        return self.userApiService
            .getAll(0, 50, 'username asc', null)
            .then(function (response: GetAllResponse<any>): void {
                for (const item of response.items) {
                    self.ownerOptions.push({
                        value: item.id,
                        displayText: `${item.username} (${item.firstName} ${item.secondName})`,
                    });
                }
            })
            .then(
                () => {

                },
                () => {

                });
    }
    protected onResetForceAcceptImage(): void {
        this.forceAcceptImage = false;
    }
    protected getClassesForTableRow(entity: SiteEntity): any {
        const result = {
            'sites-table-body-row': true,
            'sites-table-body-row-disabled': false,
            'sites-table-body-row-processing': false,
        };
        if (this.isOperationGetProcessing(entity)) {
            result['sites-table-body-row'] = false;
            result['sites-table-body-row-processing'] = true;
        } else if (this.isOperationGetManyProcessing() ||
            this.isOperationEditProcessing(entity) ||
            this.isOperationDeleteProcessing(entity)) {
            result['sites-table-body-row'] = false;
            result['sites-table-body-row-disabled'] = true;
        }
        return result;
    }
    protected getAllEntities(): Promise<void> {
        const self = this;
        self.getAllPromise = self.entityApiService
            .getAll(self.pageNumber - 1, self.pageSize, self.buildSorting(), self.buildFilter())
            .then(function (response: GetAllResponse<SiteEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            })
            .then(
                () => self.getAllPromise = null,
                () => self.getAllPromise = null);
        return self.getAllPromise;
    }
    protected deleteEntity(id: number): Promise<void> {
        const self = this;
        self.deleteEntityId = id;
        self.deletePromise = self.entityApiService
            .delete(id)
            .then(function (): Promise<void> {
                self.totalCount--;
                const elementIndex = self.items.findIndex((item: SiteEntity) => item.id === id);
                if (elementIndex > -1) {
                    self.items.splice(elementIndex, 1);
                }
                return Promise.resolve();
            })
            .then(
                () => {
                    self.deleteEntityId = null;
                    self.deletePromise = null;
                },
                () => {
                    self.deleteEntityId = null;
                    self.deletePromise = null;
                }
            );
        return self.deletePromise;
    }
    // modal
    protected createModalOpen(): Promise<void> {
        const self = this;
        self._useValidation = false;
        self.selectedEntity = new SiteEntity();
        self.selectedEntity.userId = self.authorizationManager.currentUserId;
        self.selectedEntity.imageUrl = SitesConstants.siteImageDefault;
        this.isWeekScheduleOpenedByDefault = true;
        return self.editModal.open();
    }
    protected editModalOpen(id: number): Promise<void> {
        const self = this;
        self._useValidation = false;
        self.isWeekScheduleOpenedByDefault = false;
        self.isGetPromiseForEdit = true;
        return self
            .openModalWithDetalizedEntity(self.editModal, id)
            .then(
                () => {
                    self.isGetPromiseForEdit = false;
                },
                () => {
                    self.isGetPromiseForEdit = false
                }
            );
    }
    protected modalApply(): void {
        if (this.entityValidationService.isValid(this.selectedEntity)) {
            const self = this;
            self._useValidation = false;
            self.forceAcceptImage = true;
            setTimeout(
                function () {
                    const operationPromise: Promise<SiteEntity> = self.selectedEntity.id ?
                        self.entityApiService.update(self.selectedEntity) :
                        self.entityApiService.create(self.selectedEntity);
                    self.saveEntityId = self.selectedEntity.id;
                    self.savePromise = operationPromise
                        .then(function (entity: SiteEntity): Promise<void> {
                            const elementIndex = self.items.findIndex((item: SiteEntity) => item.id === entity.id);
                            if (elementIndex !== -1) {
                                self.items.splice(elementIndex, 1, entity);
                            } else {
                                self.items.push(entity);
                                self.totalCount++;
                            }
                            return self.editModalDismiss();
                        })
                        .then(
                            () => {
                                self.saveEntityId = null;
                                self.savePromise = null;
                            },
                            () => {
                                self.saveEntityId = null;
                                self.savePromise = null;
                            }
                        );
                },
                0);
        } else {
            this._useValidation = true;
        }
    }
    protected editModalDismiss(): Promise<void> {
        this.selectedEntity = null;
        this._useValidation = false;
        this.isWeekScheduleOpenedByDefault = false;
        return this.editModal.dismiss();
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
    /// predicates
    protected isSelectedEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.selectedEntity);
    }
    protected isEditModalReadOnly(): boolean {
        return Variable.isNotNullOrUndefined(this.savePromise);
    }
    protected isValidationActivated(): boolean {
        return this._useValidation;
    }
    protected isAnyOperationWithEntityProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise) ||
            Variable.isNotNullOrUndefined(this.getPromise) ||
            Variable.isNotNullOrUndefined(this.savePromise) ||
            Variable.isNotNullOrUndefined(this.deletePromise);
    }
    protected isOperationCreateProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.savePromise) && !(this.saveEntityId > 0);
    }
    protected isOperationEditProcessing(entity: SiteEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            (
                Variable.isNotNullOrUndefined(this.savePromise) &&
                this.saveEntityId > 0 &&
                this.saveEntityId === entity.id
                ||
                Variable.isNotNullOrUndefined(this.getPromise) &&
                this.getEntityId === entity.id &&
                this.isGetPromiseForEdit
            );
    }
    protected isOperationDeleteProcessing(entity: SiteEntity): boolean {
        return Variable.isNotNullOrUndefined(this.deletePromise) &&
            Variable.isNotNullOrUndefined(entity) &&
            this.deleteEntityId > 0 &&
            this.deleteEntityId === entity.id;
    }
    protected isOperationSaveProcessing(entity: SiteEntity): boolean {
        return this.isOperationCreateProcessing() || this.isOperationEditProcessing(entity);
    }
    protected isOperationGetProcessing(entity: SiteEntity): boolean {
        return Variable.isNotNullOrUndefined(this.getPromise) &&
            Variable.isNotNullOrUndefined(entity) &&
            this.getEntityId > 0 &&
            this.getEntityId === entity.id;
    }
    protected isOperationGetManyProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise);
    }
    /// helpers
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
    private openModalWithDetalizedEntity(modal: ModalComponent, entityId: number): Promise<void> {
        const self = this;
        self.getEntityId = entityId;
        self.getPromise = self.entityApiService
            .get(entityId)
            .then(function (response: SiteEntity): Promise<void> {
                self.selectedEntity = response;
                return modal.open();
            })
            .then(
                () => {
                    self.getEntityId = null;
                    self.getPromise = null;
                },
                () => {
                    self.getEntityId = null;
                    self.getPromise = null;
                }
            );
        return self.getPromise;
    }
    // pagination
    isPageSizeChangeAllowed(): boolean {
        return true;
    }
    isPageSizeChangeDisabled(): boolean {
        return this.isAnyOperationWithEntityProcessing();
    }
    isPaginationAllowed(): boolean {
        return this.pageSize < this.totalCount;
    }
    isPaginationDisabled(): boolean {
        return this.isAnyOperationWithEntityProcessing();
    }
    // filters
    protected applyFilters(): Promise<void> {
        const filtersWereNotChanged: boolean =
            this.tableFilters.id === this.oldTableFilters.id &&
            this.tableFilters.name === this.oldTableFilters.name &&
            this.tableFilters.dealer === this.oldTableFilters.dealer &&
            this.tableFilters.userId === this.oldTableFilters.userId;
        if (!filtersWereNotChanged) {
            this.oldTableFilters = {};
            this.oldTableFilters.id = this.tableFilters.id;
            this.oldTableFilters.name = this.tableFilters.name;
            this.oldTableFilters.dealer = this.tableFilters.dealer;
            this.oldTableFilters.userId = this.tableFilters.userId;
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
    isFilteringAllowed(): boolean {
        return true;
    }
    isFilteringDisabled(): boolean {
        return this.isAnyOperationWithEntityProcessing();
    }
    /// table filters
    protected tableFilters: any = {
        id: null,
        name: null,
        userId: null,
        dealer: null,
    }
    protected oldTableFilters: any = {
        id: null,
        name: null,
        userId: null,
        dealer: null,
    }
    protected buildFilter(): any {
        const filter = Object.assign({}, this.filter);
        this.extendFilter(filter);
        return filter;
    }
    protected extendFilter(filter: any): void {
        if (Variable.isNullOrUndefined(filter)) {
            throw new Error('Argument exception! (extendFilter requires defined argument filter)');
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.id) && this.tableFilters.id !== '') {
            filter.id = this.tableFilters.id;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.name) && this.tableFilters.name !== '') {
            filter.name = this.tableFilters.name;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.dealer) && this.tableFilters.dealer !== '') {
            filter.dealer = this.tableFilters.dealer;
        }
        if (Variable.isNotNullOrUndefined(this.tableFilters.userId)) {
            filter.userId = this.tableFilters.userId;
        }
    }
    /// sorting
    protected sortingRules: Array<any> = [
        {
            field: 'id',
            isAsc: true,
        }
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
        return this.isAnyOperationWithEntityProcessing();
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
    /// confirmation delete modal
    protected deleteCandidateId: number;
    protected getDeleteCandidateDisplayText(): string {
        let result;
        if (Variable.isNotNullOrUndefined(this.deleteCandidateId)) {
            const elementIndex = this.items
                .findIndex((item: SiteEntity) => item.id === this.deleteCandidateId);
            if (elementIndex > -1) {
                result = this.items[elementIndex].name;
            }
        }
        return Variable.isNotNullOrUndefined(result) ? result : '';
    }
    protected openConfirmationDeleteModal(candidateId: number): Promise<void> {
        this.deleteCandidateId = candidateId;
        return this.confirmationDeleteModal.open();
    }
    protected acceptConfirmationDeleteModal(): Promise<void> {
        const self = this;
        return self.confirmationDeleteModal
            .close()
            .then(() => {
                self.deleteEntity(self.deleteCandidateId);
                self.deleteCandidateId = null;
            });
    }
    protected closeConfirmationDeleteModal(): Promise<void> {
        const self = this;
        return self.confirmationDeleteModal
            .close()
            .then(() => self.deleteCandidateId = null);
    }
}
