import { Component, OnInit, Input, Output, ViewChild, EventEmitter } from '@angular/core';
import { Router } from '@angular/router';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { ApplicationConstants } from './../../../app.constants';
import { SitesConstants } from './../sites.constants';
import { Variable, ILogger, ConsoleLogger } from './../../../utils/index';
import { IAuthorizationService, AuthorizationService } from './../../../services/index';
import { ISiteApiService, SiteApiService, GetAllResponse } from './../../../services/serverApi/index';
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
    private _defaultPageNumber: number = 0;
    private _defaultPageSize: number = 10;
    private _defaultSorting: string = 'name asc';
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
    /// ctor
    constructor(
        logger: ConsoleLogger,
        router: Router,
        authorizationManager: AuthorizationService,
        entityApiService: SiteApiService,
        entityPolicyService: SiteEntityPolicyService,
        entityValidationService: SiteValidationService) {
        this.logger = logger;
        this.router = router;
        this.authorizationManager = authorizationManager;
        this.entityApiService = entityApiService;
        this.entityPolicyService = entityPolicyService;
        this.entityValidationService = entityValidationService;
        this.logger.logDebug('SitesTableComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        const self = this;
        self.firstLoadingPromise = self
            .getAllEntities()
            .then(
                () => self.firstLoadingPromise = null,
                () => self.firstLoadingPromise = null);
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
            .getAll(self.getPageNumber(), self.getPageSize(), self.buildSorting(), self.buildFilter())
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
    private getPageNumber(): number {
        return this._defaultPageNumber;
    }
    private getPageSize(): number {
        return this._defaultPageSize;
    }
    private buildSorting(): string {
        return this._defaultSorting;
    }
    private buildFilter(): any {
        return Variable.isNotNullOrUndefined(this.filter) ? this.filter : this._defaultFilter;
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
