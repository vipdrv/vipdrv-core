import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, Extensions, PromiseService, ConsoleLogger, ILogger } from './../../../utils/index';
import { IAuthorizationService, AuthorizationService } from './../../../services/index';
import { IUserApiService, UserApiService, GetAllResponse } from './../../../services/serverApi/index';
import { InvitationEntity } from './../../../entities/index';
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
    /// data fields
    protected totalCount: number;
    protected items: Array<InvitationEntity>;
    protected entity: InvitationEntity;
    /// injected dependencies
    protected logger: ILogger;
    protected authorizationManager: IAuthorizationService;
    protected userApiService: IUserApiService;
    protected promiseService: PromiseService;
    /// ctor
    constructor(
        logger: ConsoleLogger,
        authorizationManager: AuthorizationService,
        userApiService: UserApiService,
        promiseService: PromiseService) {
        this.logger = logger;
        this.authorizationManager = authorizationManager;
        this.userApiService = userApiService;
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
        const self = this;
        const userId: number = Variable.isNullOrUndefined(this.authorizationManager.lastUser) ?
            null : this.authorizationManager.lastUser.userId;
        self.promiseService.applicationPromises.leads.getAll = self.userApiService
            .getInvitations(userId, self.pageNumber - 1, self.pageSize, self.sorting)
            .then(function (response: GetAllResponse<InvitationEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            })
            .then(
                () => self.promiseService.applicationPromises.leads.getAll = null,
                () => self.promiseService.applicationPromises.leads.getAll = null);
        return self.promiseService.applicationPromises.leads.getAll;
    }
    protected isDeleteAvailable(entity: InvitationEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) && !entity.used;
    }
    protected deleteEntity(id: number): Promise<void> {
        const self = this;
        // self.promiseService.applicationPromises.leads.delete = self.userApiService
        //     .delete(id)
        //     .then(function (): Promise<void> {
        //         const elementIndex = self.items.findIndex((item: InvitationEntity) => item.id === id);
        //         self.items.splice(elementIndex, 1);
        //         return Promise.resolve();
        //     })
        //     .then(
        //         () => self.promiseService.applicationPromises.leads.delete = null,
        //         () => self.promiseService.applicationPromises.leads.delete = null);
        return self.promiseService.applicationPromises.leads.delete;
    }
    protected modalOpenInfo(id: number): Promise<void> {
        this.entity = this.items.find((item: InvitationEntity) => item.id === id);
        this.isOperationModeInfo = true;
        return this.modalDetails.open();
    }
    protected modalDismiss(): Promise<void> {
        this.entity = null;
        this.isOperationModeInfo = false;
        this.isOperationModeCreate = false;
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
    /// predicates
    protected usePagination(): boolean {
        return this.pageSize < this.totalCount;
    }
    protected isSelectedEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
    }

    /// send invite
    private _sendInvitationPromise = null;
    protected modalOpenCreate(id: number): Promise<void> {
        this.initializeEntityToCreate();
        this.isOperationModeCreate = true;
        return this.modalDetails.open();
    }
    sendInvitation(): void {
        const self = this;
        self._sendInvitationPromise = self.userApiService
            .createInvitation(self.authorizationManager.lastUser.userId, this.entity)
            .then(function(response: InvitationEntity): void {
                self.items.splice(0, 0, response);
                self.modalDismiss();
                self._sendInvitationPromise = null;
            }).catch(function (reason) {
                self._sendInvitationPromise = null;
            });
    }
    protected onEntityChange(newValue: InvitationEntity): void {
        this.entity = newValue;
    }
    protected isSendInvitationDisabled(): boolean {
        return this.isSendInvitationInProgress() ||
            Variable.isNullOrUndefined(this.entity) || !this.isValidEmail(this.entity.email);
    }
    protected isSendInvitationInProgress(): boolean {
        return Variable.isNotNullOrUndefined(this._sendInvitationPromise);
    }
    protected isValidEmail(value: string): boolean {
        return Extensions.regExp.email.test(value)
    }
    private initializeEntityToCreate(): void {
        this.entity = new InvitationEntity();
        this.entity.email = null;
        this.entity.phoneNumber = null;
        this.entity.availableSitesCount = 0;
        this.entity.roleId = 1;
    }
}
