import { Component, OnInit, Input, Output, ViewChild, EventEmitter } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { ApplicationConstants } from './../../../app.constants';
import { RoutesConstants } from './../routes.constants';
import { Variable, ILogger, ConsoleLogger } from './../../../utils/index';
import { IRouteApiService, RouteApiService, GetAllResponse } from './../../../services/serverApi/index';
import { IRouteEntityPolicyService, RouteEntityPolicyService } from './../../../services/index';
import { IRouteValidationService, RouteValidationService } from './../../../services/index';
import { RouteEntity } from './../../../entities/index';
@Component({
    selector: 'routes-table',
    styleUrls: ['./routesTable.scss'],
    templateUrl: './routesTable.html'
})
export class RoutesTableComponent implements OnInit {
    /// inputs
    @Input() siteId: number;
    @Input() filter: any;
    /// outputs
    @Output() onEntityChanged: EventEmitter<any> = new EventEmitter<any>();
    /// children
    @ViewChild('confirmationDeleteModal')
    protected confirmationDeleteModal: ModalComponent;
    @ViewChild('editModal')
    protected editModal: ModalComponent;
    @ViewChild('infoModal')
    protected infoModal: ModalComponent;
    /// service fields
    private _defaultPageNumber: number = 0;
    private _defaultPageSize: number = 100;
    private _defaultSorting: string = 'order asc';
    private _defaultFilter: any = null;
    private _useValidation: boolean = false;
    protected imageInTableWidth: number = 50;
    protected imageInTableHeight: number = 50;
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
    protected updateActivityEntityIds: Array<number> = new Array<number>();
    protected updateOrderEntityIds: Array<number> = new Array<number>();
    /// fields
    protected switcherSettings = ApplicationConstants.switcherSettings;
    protected totalCount: number;
    protected items: Array<RouteEntity>;
    protected selectedEntity: RouteEntity;
    /// injected dependencies
    protected logger: ILogger;
    protected entityApiService: IRouteApiService;
    protected entityPolicyService: IRouteEntityPolicyService;
    protected entityValidationService: IRouteValidationService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        entityApiService: RouteApiService,
        entityPolicyService: RouteEntityPolicyService,
        entityValidationService: RouteValidationService) {
        this.logger = logger;
        this.entityApiService = entityApiService;
        this.entityPolicyService = entityPolicyService;
        this.entityValidationService = entityValidationService;
        this.logger.logDebug('RoutesTableComponent: Component has been constructed.');
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
    protected notifyOnChanges(entityActivated: boolean = false, entityDeactivated: boolean = false): void {
        if (Variable.isNotNullOrUndefined(this.onEntityChanged)) {
            this.onEntityChanged
                .emit({
                    totalCount: this.totalCount,
                    entityWasActivated: entityActivated,
                    entityWasDeactivated: entityDeactivated
                });
        }
    }
    protected getClassesForTableRow(entity: RouteEntity): any {
        const result = {
            'routes-table-body-row': true,
            'routes-table-body-row-disabled': false,
            'routes-table-body-row-processing': false,
            'routes-table-body-row-deactivated': false
        };
        if (this.isOperationGetProcessing(entity)) {
            result['routes-table-body-row'] = false;
            result['routes-table-body-row-processing'] = true;
        } else if (this.isOperationGetManyProcessing() ||
            this.isOperationChangeActivityProcessing(entity) ||
            this.isOperationChangeOrderProcessing(entity) ||
            this.isOperationEditProcessing(entity) ||
            this.isOperationDeleteProcessing(entity)) {
            result['routes-table-body-row'] = false;
            result['routes-table-body-row-disabled'] = true;
        }
        if (!entity.isActive) {
            result['routes-table-body-row-deactivated'] = true;
        }
        return result;
    }
    protected getAllEntities(): Promise<void> {
        const self = this;
        self.getAllPromise = self.entityApiService
            .getAll(self.getPageNumber(), self.getPageSize(), self.buildSorting(), self.buildFilter())
            .then(function (response: GetAllResponse<RouteEntity>): Promise<void> {
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
                const elementIndex = self.items.findIndex((item: RouteEntity) => item.id === id);
                if (elementIndex > -1) {
                    self.notifyOnChanges(false, self.items[elementIndex].isActive);
                    self.items.splice(elementIndex, 1);
                } else {
                    self.notifyOnChanges();
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
    // activity
    protected onChangeEntityActivity(entity: RouteEntity): Promise<void> {
        if (Variable.isNotNullOrUndefined(entity) && !this.isOperationChangeActivityDisabled(entity)) {
            entity.isActive = !entity.isActive;
            return this.commitChangeEntityActivity(entity);
        } else {
            return Promise.resolve();
        }
    }
    private commitChangeEntityActivity(entity: RouteEntity): Promise<void> {
        const self = this;
        const newActivityValue: boolean = entity.isActive;
        const entityId = entity.id;
        self.updateActivityEntityIds.push(entityId);
        const updateActivityPromise = this.entityApiService
            .patchActivity(entity.id, newActivityValue)
            .then(
                () => {
                    self.notifyOnChanges(newActivityValue, !newActivityValue);
                    const index: number = self.updateActivityEntityIds.findIndex((item) => item === entityId);
                    if (index > -1) {
                        self.updateActivityEntityIds.splice(index, 1);
                    }
                },
                () => {
                    entity.isActive = !newActivityValue;
                    const index: number = self.updateActivityEntityIds.findIndex((item) => item === entityId);
                    if (index > -1) {
                        self.updateActivityEntityIds.splice(index, 1);
                    }
                }
            );
        return updateActivityPromise;
    }
    // order
    protected isIncrementOrderDisabled(entity: RouteEntity): boolean {
        let isDisabled: boolean = this.isOperationChangeOrderDisabled(entity);
        if (!isDisabled) {
            const entityIndex: number = this.items.findIndex((item) => item.id === entity.id);
            if (entityIndex > -1 ) {
                if (entityIndex < (this.items.length - 1)) {
                    isDisabled = this.isOperationChangeOrderDisabled(this.items[entityIndex + 1]);
                } else {
                    isDisabled = true;
                }
            } else {
                isDisabled = true;
            }
        }
        return isDisabled;
    }
    protected isDecrementOrderDisabled(entity: RouteEntity): boolean {
        let isDisabled: boolean = this.isOperationChangeOrderDisabled(entity);
        if (!isDisabled) {
            const entityIndex: number = this.items.findIndex((item) => item.id === entity.id);
            if (entityIndex > 0 ) {
                isDisabled = this.isOperationChangeOrderDisabled(this.items[entityIndex - 1]);
            } else {
                isDisabled = true;
            }
        }
        return isDisabled;
    }
    protected incrementOrder(entity: RouteEntity): Promise<void> {
        if (Variable.isNotNullOrUndefined(entity)) {
            const entityIndex: number = this.items.findIndex((item) => item.id === entity.id);
            if (entityIndex > -1 && entityIndex < this.items.length - 1) {
                return this.commitSwapOrders(this.items[entityIndex], this.items[entityIndex + 1]);
            }
        }
        return Promise.resolve();
    }
    protected decrementOrder(entity: RouteEntity): Promise<void> {
        if (Variable.isNotNullOrUndefined(entity)) {
            const entityIndex: number = this.items.findIndex((item) => item.id === entity.id);
            if (entityIndex > 0 && this.items.length > 1) {
                return this.commitSwapOrders(this.items[entityIndex - 1], this.items[entityIndex]);
            }
        }
        return Promise.resolve();
    }
    private commitSwapOrders(entity1: RouteEntity, entity2: RouteEntity): Promise<void> {
        const self = this;
        self.updateOrderEntityIds.push(entity1.id);
        self.updateOrderEntityIds.push(entity2.id);
        return self.entityApiService
            .swapOrders(entity1.id, entity2.id)
            .then(
                () => {
                    self.swapEntityOrdersInItems(entity1, entity2);
                    if (Variable.isNotNullOrUndefined(entity1)) {
                        const index1: number = self.updateOrderEntityIds.findIndex((item) => item === entity1.id);
                        if (index1 > -1) {
                            self.updateOrderEntityIds.splice(index1, 1);
                        }
                    }
                    if (Variable.isNotNullOrUndefined(entity2)) {
                        const index2: number = self.updateOrderEntityIds.findIndex((item) => item === entity2.id);
                        if (index2 > -1) {
                            self.updateOrderEntityIds.splice(index2, 1);
                        }
                    }
                },
                () => {
                    if (Variable.isNotNullOrUndefined(entity1)) {
                        const index1: number = self.updateOrderEntityIds.findIndex((item) => item === entity1.id);
                        if (index1 > -1) {
                            self.updateOrderEntityIds.splice(index1, 1);
                        }
                    }
                    if (Variable.isNotNullOrUndefined(entity2)) {
                        const index2: number = self.updateOrderEntityIds.findIndex((item) => item === entity2.id);
                        if (index2 > -1) {
                            self.updateOrderEntityIds.splice(index2, 1);
                        }
                    }
                },
            );
    }
    private swapEntityOrdersInItems(entity1: RouteEntity, entity2: RouteEntity): void {
        const index1 = this.items.findIndex((item) => item.id === entity1.id);
        const index2 = this.items.findIndex((item) => item.id === entity2.id);
        if (index1 > -1 && index2 > -1) {
            const stubOrder: number = this.items[index1].order;
            this.items[index1].order = this.items[index2].order;
            this.items[index2].order = stubOrder;
            const stub = this.items[index1];
            this.items[index1] = this.items[index2];
            this.items[index2] = stub;
        }
    }
    // modal
    protected tryInfoModalOpen(entity: RouteEntity): Promise<void> {
        if (Variable.isNotNullOrUndefined(entity) && !this.isAnyOperationWithEntityProcessing()) {
            return this.openModalWithDetalizedEntity(this.infoModal, entity.id);
        } else {
            return Promise.resolve();
        }
    }
    protected createModalOpen(): Promise<void> {
        const self = this;
        self._useValidation = false;
        self.selectedEntity = new RouteEntity();
        self.selectedEntity.siteId = self.siteId;
        self.selectedEntity.photoUrl = RoutesConstants.routeImageDefault;
        self.selectedEntity.isActive = true;
        self.selectedEntity.order = self.getNewEntityOrder();
        return self.editModal.open();
    }
    protected editModalOpen(id: number): Promise<void> {
        const self = this;
        self._useValidation = false;
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
    protected modalApply(): Promise<void> {
        if (this.entityValidationService.isValid(this.selectedEntity)) {
            const self = this;
            self._useValidation = false;
            const operationPromise: Promise<RouteEntity> = self.selectedEntity.id ?
                self.entityApiService.update(self.selectedEntity) :
                self.entityApiService.create(self.selectedEntity);
            self.saveEntityId = self.selectedEntity.id;
            self.savePromise = operationPromise
                .then(function (entity: RouteEntity): Promise<void> {
                    const elementIndex = self.items.findIndex((item: RouteEntity) => item.id === entity.id);
                    if (elementIndex !== -1) {
                        self.items.splice(elementIndex, 1, entity);
                        self.notifyOnChanges();
                    } else {
                        self.items.push(entity);
                        self.totalCount++;
                        self.notifyOnChanges(entity.isActive, !entity.isActive);
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
            return self.savePromise;
        } else {
            this._useValidation = true;
            return Promise.resolve();
        }
    }
    protected editModalDismiss(): Promise<void> {
        this.selectedEntity = null;
        this._useValidation = false;
        return this.editModal.dismiss();
    }
    protected infoModalDismiss(): Promise<void> {
        this.selectedEntity = null;
        return this.infoModal.dismiss();
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
            Variable.isNotNullOrUndefined(this.deletePromise) ||
            this.updateActivityEntityIds.length > 0 ||
            this.updateOrderEntityIds.length > 0;
    }
    protected isOperationCreateProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.savePromise) && !(this.saveEntityId > 0);
    }
    protected isOperationEditProcessing(entity: RouteEntity): boolean {
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
    protected isOperationDeleteProcessing(entity: RouteEntity): boolean {
        return Variable.isNotNullOrUndefined(this.deletePromise) &&
            Variable.isNotNullOrUndefined(entity) &&
            this.deleteEntityId > 0 &&
            this.deleteEntityId === entity.id;
    }
    protected isOperationSaveProcessing(entity: RouteEntity): boolean {
        return this.isOperationCreateProcessing() || this.isOperationEditProcessing(entity);
    }
    protected isOperationGetProcessing(entity: RouteEntity): boolean {
        return Variable.isNotNullOrUndefined(this.getPromise) &&
            Variable.isNotNullOrUndefined(entity) &&
            this.getEntityId > 0 &&
            this.getEntityId === entity.id;
    }
    protected isOperationChangeActivityProcessing(entity: RouteEntity) {
        return this.updateActivityEntityIds.length > 0 &&
            Variable.isNotNullOrUndefined(entity) &&
            this.updateActivityEntityIds.indexOf(entity.id) > -1;
    }
    protected isOperationChangeActivityDisabled(entity: RouteEntity) {
        return !this.entityPolicyService.canUpdateActivityForEntity(entity) ||
            this.isOperationChangeActivityProcessing(entity) ||
            this.isOperationGetManyProcessing() ||
            this.isOperationEditProcessing(entity) ||
            this.isOperationDeleteProcessing(entity) ||
            this.isOperationGetProcessing(entity);
    }
    protected isOperationChangeOrderProcessing(entity: RouteEntity) {
        return this.updateOrderEntityIds.length > 0 &&
            Variable.isNotNullOrUndefined(entity) &&
            this.updateOrderEntityIds.indexOf(entity.id) > -1;
    }
    protected isOperationChangeOrderDisabled(entity: RouteEntity) {
        return this.isOperationChangeOrderProcessing(entity) ||
            this.isOperationGetManyProcessing() ||
            this.isOperationEditProcessing(entity) ||
            this.isOperationDeleteProcessing(entity) ||
            this.isOperationGetProcessing(entity);
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
            .then(function (response: RouteEntity): Promise<void> {
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
    private getNewEntityOrder(): number {
        let maxOrder: number = this.items.length > 0 ? this.items[0].order : 0;
        for (let i: number = 1; i < this.items.length; i++) {
            maxOrder = this.items[i].order > maxOrder ? this.items[i].order : maxOrder;
        }
        return maxOrder === 1 ? 1 : maxOrder + 1;
    }
    /// confirmation delete modal
    protected deleteCandidateId: number;
    protected getDeleteCandidateDisplayText(): string {
        let result;
        if (Variable.isNotNullOrUndefined(this.deleteCandidateId)) {
            const elementIndex = this.items
                .findIndex((item: RouteEntity) => item.id === this.deleteCandidateId);
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
