import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, ILogger, ConsoleLogger } from './../../../utils/index';
import { IAuthorizationService, AuthorizationService } from './../../../services/index';
import { IUserApiService, UserApiService, GetAllResponse } from './../../../services/index';
import { IRoleApiService, RoleApiService } from './../../../services/index';
import { IInvitationEntityPolicyService, InvitationEntityPolicyService } from './../../../services/index';
import { IInvitationValidationService, InvitationValidationService } from './../../../services/index';
import { InvitationEntity, RoleEntity } from './../../../entities/index';
@Component({
    selector: 'invitations-table',
    styleUrls: ['./invitationsTable.scss'],
    templateUrl: './invitationsTable.html'
})
export class InvitationsTableComponent implements OnInit {
    /// inputs
    @Input() pageNumber: number;
    @Input() pageSize: number;
    @Input() sorting: string;
    @Input() filter: any;
    /// modals
    @ViewChild('confirmationDeleteModal')
    protected confirmationDeleteModal: ModalComponent;
    @ViewChild('invitationDetailsModal')
    protected modalDetails: ModalComponent;
    /// settings
    private _defaultPageNumber: number = 1;
    private _defaultPageSize: number = 10;
    private _defaultSorting: string = 'used asc, createdTimeUtc desc';
    private _defaultFilter: any = null;
    protected maxPaginationSize: number = 3;
    protected pageSizeValues: Array<number> = [5, 10, 25, 50, 100];
    protected isOperationModeInfo: boolean = false;
    protected isOperationModeCreate: boolean = false;
    protected useValidationForSelectedEntity: boolean = false;
    /// data fields
    protected totalCount: number;
    protected items: Array<InvitationEntity>;
    protected entity: InvitationEntity;
    protected rolesCanBeUsedForInvitation: Array<RoleEntity> = [];
    /// injected dependencies
    protected logger: ILogger;
    protected authorizationManager: IAuthorizationService;
    protected userApiService: IUserApiService;
    protected policyService: IInvitationEntityPolicyService;
    protected validationService: IInvitationValidationService;
    protected roleApiService: IRoleApiService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        authorizationManager: AuthorizationService,
        userApiService: UserApiService,
        policyService: InvitationEntityPolicyService,
        validationService: InvitationValidationService,
        roleApiService: RoleApiService) {
        this.logger = logger;
        this.authorizationManager = authorizationManager;
        this.userApiService = userApiService;
        this.policyService = policyService;
        this.validationService = validationService;
        this.roleApiService = roleApiService;
        this.logger.logDebug('InvitationsTableComponent: Component has been constructed.');
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
                () => self.firstLoadingPromise = null,
                () => self.firstLoadingPromise = null);
        self.loadRelations();
    }
    protected loadRelations(): Promise<void> {
        return this.loadRolesCanBeUsedForInvitation();
    }
    protected loadRolesCanBeUsedForInvitation(): Promise<void> {
        const self = this;
        let actionPromise = self.roleApiService
            .getAllCanBeUsedForInvitation()
            .then(function (response: GetAllResponse<RoleEntity>): void {
                self.rolesCanBeUsedForInvitation = response.items;
            })
            .then(
                () => actionPromise = null,
                () => actionPromise = null
            );
        return actionPromise;
    }
    protected getAllEntities(): Promise<void> {
        const self = this;
        self.getAllPromise = self.userApiService
            .getInvitations(
                this.authorizationManager.currentUserId,
                self.pageNumber - 1,
                self.pageSize,
                self.sorting)
            .then(function (response: GetAllResponse<InvitationEntity>): Promise<void> {
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
        self.deleteEntityPromise = self.userApiService
            .deleteInvitation(id)
            .then(function (): Promise<void> {
                const elementIndex = self.items
                    .findIndex((item: InvitationEntity) => item.id === id);
                if (elementIndex > -1) {
                    self.items.splice(elementIndex, 1);
                }
                return Promise.resolve();
            })
            .then(
                () => {
                    self.deleteEntityPromise = null,
                    self.deleteEntityId = null
                },
                () => {
                    self.deleteEntityPromise = null,
                    self.deleteEntityId = null
                });
        return self.deleteEntityPromise;
    }
    protected tryModalOpenInfo(id: number): Promise<void> {
        let actionPromise: Promise<void>;
        const entityToSelect = this.items.find((item: InvitationEntity) => item.id === id);
        if (Variable.isNotNullOrUndefined(entityToSelect) &&
            this.isGetAvailable(entityToSelect) &&
            !this.isGetProcessing(entityToSelect)) {
            this.entity = entityToSelect;
            this.isOperationModeInfo = true;
            return this.modalDetails.open();
        } else {
            actionPromise = Promise.resolve();
        }
        return actionPromise;
    }
    protected modalDismiss(): Promise<void> {
        this.entity = null;
        this.isOperationModeInfo = false;
        this.isOperationModeCreate = false;
        this.useValidationForSelectedEntity = false;
        return this.modalDetails.dismiss();
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
    protected getClassesForTableRow(entity: InvitationEntity): any {
        const result = {
            'invitations-table-body-row': true,
            'invitations-table-body-row-disabled': false,
            'invitations-table-body-row-processing': false,
            'invitations-table-body-row-with-pending-entity': false
        };
        if (this.isGetProcessing(entity)) {
            result['invitations-table-body-row'] = false;
            result['invitations-table-body-row-processing'] = true;
        } else if (this.isGetProcessing(entity)) {
            result['invitations-table-body-row'] = false;
            result['invitations-table-body-row-disabled'] = true;
        }
        if (Variable.isNotNullOrUndefined(entity) && !entity.used) {
            result['invitations-table-body-row-with-pending-entity'] = true;
        }
        return result;
    }
    /// predicates
    protected isRefreshAllowed(): boolean {
        return this.policyService.canGet();
    }
    protected isRefreshDisabled(): boolean {
        return this.isAnyOperationWithEntityProcessing();
    }
    protected isRefreshProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise);
    }
    protected isOperationCreateAllowed(): boolean {
        return this.policyService.canCreate();
    }
    protected isOperationCreateProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.sendInvitationPromise);
    }
    protected isDeleteAvailable(entity: InvitationEntity): boolean {
        return Variable.isNotNullOrUndefined(entity)  &&
            !entity.used &&
            this.policyService.canDeleteEntity(entity);
    }
    protected isDeleteProcessing(entity: InvitationEntity): boolean {
        return Variable.isNotNullOrUndefined(this.deleteEntityPromise) &&
            Variable.isNotNullOrUndefined(this.deleteEntityId) &&
            Variable.isNotNullOrUndefined(entity) &&
            this.deleteEntityId === entity.id;
    }
    protected isGetAvailable(entity: InvitationEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            this.policyService.canGetEntity(entity);
    }
    protected isGetProcessing(entity: InvitationEntity): boolean {
        return Variable.isNotNullOrUndefined(this.getEntityPromise) &&
            Variable.isNotNullOrUndefined(this.getEntityId) &&
            Variable.isNotNullOrUndefined(entity) &&
            this.getEntityId === entity.id;
    }
    protected isAnyOperationWithEntityProcessing(): boolean {
        return this.isRefreshProcessing() ||
            this.isOperationCreateProcessing() ||
            Variable.isNotNullOrUndefined(this.getEntityPromise) ||
            Variable.isNotNullOrUndefined(this.deleteEntityPromise);
    }
    protected isSelectedEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
    }
    protected isValidationUsedForSelectedEntity(): boolean {
        return this.useValidationForSelectedEntity;
    }
    protected isPageSizeChangeAllowed(): boolean {
        return true;
    }
    protected isPageSizeChangeDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise) ||
            Variable.isNotNullOrUndefined(this.getEntityPromise);
    }
    protected isPaginationAllowed(): boolean {
        return this.pageSize < this.totalCount;
    }
    protected isPaginationDisabled(): boolean {
        return Variable.isNotNullOrUndefined(this.getAllPromise) ||
            Variable.isNotNullOrUndefined(this.getEntityPromise);
    }
    /// send invite
    protected modalOpenCreate(): Promise<void> {
        this.initializeEntityToCreate();
        this.isOperationModeCreate = true;
        return this.modalDetails.open();
    }
    protected sendInvitation(): Promise<void> {
        if (this.validationService.isValid(this.entity)) {
            const self = this;
            self.useValidationForSelectedEntity = false;
            self.sendInvitationPromise = self.userApiService
                .createInvitation(this.entity)
                .then(function (response: InvitationEntity): void {
                    self.items.splice(0, 0, response);
                    self.modalDismiss();
                    self.sendInvitationPromise = null;
                }).catch(function (reason) {
                    self.sendInvitationPromise = null;
                });
            return self.sendInvitationPromise;
        } else {
            this.useValidationForSelectedEntity = true;
            return Promise.resolve();
        }
    }
    protected onEntityChange(newValue: InvitationEntity): void {
        this.entity = newValue;
    }
    protected isSendInvitationDisabled(): boolean {
        return this.isSendInvitationInProgress();
    }
    protected isSendInvitationInProgress(): boolean {
        return Variable.isNotNullOrUndefined(this.sendInvitationPromise);
    }
    private initializeEntityToCreate(): void {
        this.entity = new InvitationEntity();
        this.entity.email = null;
        this.entity.phoneNumber = null;
        this.entity.availableSitesCount = 1;
        if (this.rolesCanBeUsedForInvitation.length > 0) {
            this.entity.roleId = this.rolesCanBeUsedForInvitation[0].id;
        }
    }
    /// promise manager
    protected firstLoadingPromise: Promise<void>;
    protected getAllPromise: Promise<void>;
    protected getEntityPromise: Promise<InvitationEntity>;
    protected getEntityId: number;
    protected sendInvitationPromise: Promise<void>;
    protected deleteEntityPromise: Promise<void>;
    protected deleteEntityId: number;
    /// confirmation delete modal
    protected deleteCandidateId: number;
    protected getDeleteCandidateDisplayText(): string {
        let result;
        if (Variable.isNotNullOrUndefined(this.deleteCandidateId)) {
            const elementIndex = this.items
                .findIndex((item: InvitationEntity) => item.id === this.deleteCandidateId);
            if (elementIndex > -1) {
                result = this.items[elementIndex].email;
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
