import { Component, Input, OnInit, Output, ViewChild, EventEmitter } from '@angular/core';
import { ExpertValidationService } from '../../../services/validation/concrete/entity/expert/expert.validation-service';
import { ExpertEntityPolicyService } from '../../../services/policy/concrete/widget/expert/expertEntity.policy-service';
import { ILogger, ConsoleLogger, Variable } from '../../../utils/index';
import { IExpertEntityPolicyService } from '../../../services/policy/concrete/widget/expert/i-expertEntity.policy-service';
import { IExpertValidationService } from '../../../services/validation/concrete/entity/expert/i-expert.validation-service';
import { UserApiService, GetAllResponse, IUserApiService } from '../../../services/serverApi/index';
import { UserEntity } from '../../../entities/main/users/user.entity';
import { promise } from 'selenium-webdriver';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { UserEntityPolicyService } from '../../../services/policy/concrete/main/user/userEntity.policy-service';
import { IUserEntityPolicyService } from '../../../services/policy/concrete/main/user/i-userEntity.policy-service';
import { IUserValidationService } from '../../../services/validation/concrete/entity/user/i-user.validation-service';
import { UserValidationService } from '../../../services/validation/concrete/entity/user/user.validation-service';

@Component({
    selector: 'users-table',
    templateUrl: './usersTable.html',
})
export class UsersTableComponent implements OnInit {
    /// inputs
    @Input() filter: any;
    /// outputs
    @Output() onEntityChanged: EventEmitter<any> = new EventEmitter<any>();
    /// children
    @ViewChild('confirmationDeleteModal') protected confirmationDeleteModal: ModalComponent;
    @ViewChild('editModal') protected editModal: ModalComponent;
    @ViewChild('infoModal') protected infoModal: ModalComponent;
    /// service fields
    private _defaultPageNumber: number = 0;
    private _defaultPageSize: number = 100;
    private _defaultSorting: string = 'id asc';
    private _defaultFilter: any = null;
    private _useValidation: boolean = false;

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
    protected totalCount: number;
    protected items: UserEntity[];
    protected selectedEntity: UserEntity;
    protected passwordRepeat: string;
    protected isModalInEditMode: boolean;

    /// injected dependencies
    protected logger: ILogger;
    protected entityApiService: IUserApiService;
    protected entityPolicyService: IUserEntityPolicyService;
    protected entityValidationService: IUserValidationService;

    /// ctor
    constructor(logger: ConsoleLogger,
                entityApiService: UserApiService,
                entityPolicyService: UserEntityPolicyService,
                entityValidationService: UserValidationService) {
        this.logger = logger;
        this.entityApiService = entityApiService;
        this.entityPolicyService = entityPolicyService;
        this.entityValidationService = entityValidationService;
        this.logger.logDebug('UsersTableComponent: Component has been constructed.');
    }

    /// methods

    ngOnInit(): void {
        const self = this;
        self.firstLoadingPromise = self.getAllEntities()
            .then(
                () => self.firstLoadingPromise = null,
                () => self.firstLoadingPromise = null);
    }

    protected getAllEntities(): Promise<void> {
        const self = this;
        self.getAllPromise = self.entityApiService
            .getAll(this.getPageNumber(), this.getPageSize(), this.buildSorting(), this.buildFilter())
            .then((response: GetAllResponse<UserEntity>) => {
                self.items = response.items;
                self.totalCount = response.totalCount;
                return Promise.resolve();
            }).then(
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
                const elementIndex = self.items.findIndex((item: UserEntity) => item.id === id);
                if (elementIndex > -1) {
                    // self.notifyOnChanges(false, self.items[elementIndex].isActive);
                    self.items.splice(elementIndex, 1);
                } else {
                    // self.notifyOnChanges();
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
                },
            );
        return self.deletePromise;
    }

    /// activity
    /// order
    /// modal
    protected tryInfoModalOpen(entity: UserEntity): Promise<void> {
        if (Variable.isNotNullOrUndefined(entity) && !this.isAnyOperationWithEntityProcessing()) {
            return this.openModalWithDetalizedEntity(this.infoModal, entity.id);
        } else {
            return Promise.resolve();
        }
    }

    protected createModalOpen(): Promise<void> {
        const self = this;
        self._useValidation = false;
        self.selectedEntity = new UserEntity();
        return self.editModal.open();
    }

    protected modalApply(): void {
        if (this.entityValidationService.isValid(this.selectedEntity)) {
            const self = this;
            self._useValidation = false;
            // self.forceAcceptImage = true;
            setTimeout(
                function () {
                    const operationPromise: Promise<UserEntity> = self.selectedEntity.id ?
                        self.entityApiService.update(self.selectedEntity) :
                        self.entityApiService.create(self.selectedEntity);

                    self.saveEntityId = self.selectedEntity.id;
                    self.savePromise = operationPromise
                        .then(function (entity: UserEntity): Promise<void> {
                            const elementIndex = self.items.findIndex((item: UserEntity) => item.id === entity.id);
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

    protected editModalOpen(id: number): Promise<void> {
        const self = this;
        self._useValidation = false;
        self.isModalInEditMode = true;

        self.isGetPromiseForEdit = true;
        return self
            .openModalWithDetalizedEntity(self.editModal, id)
            .then(
                () => {
                    self.isGetPromiseForEdit = false;
                },
                () => {
                    self.isGetPromiseForEdit = false;
                }
            );
    }

    protected editModalDismiss(): Promise<void> {
        this.selectedEntity = null;
        this._useValidation = false;
        this.isModalInEditMode = false;
        return this.editModal.dismiss();
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

    protected isOperationEditProcessing(entity: UserEntity): boolean {
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

    protected isOperationDeleteProcessing(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(this.deletePromise) &&
            Variable.isNotNullOrUndefined(entity) &&
            this.deleteEntityId > 0 &&
            this.deleteEntityId === entity.id;
    }

    protected isOperationSaveProcessing(entity: UserEntity): boolean {
        return this.isOperationCreateProcessing() || this.isOperationEditProcessing(entity);
    }

    protected isOperationGetProcessing(entity: UserEntity): boolean {
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
            .then(function (response: UserEntity): Promise<void> {
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
                .findIndex((item: UserEntity) => item.id === this.deleteCandidateId);
            if (elementIndex > -1) {
                result = this.items[elementIndex].username;
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
        // Promise
    }
}
