import { Component, Input, OnInit, ViewChild } from '@angular/core';
import { ExpertValidationService } from '../../../services/validation/concrete/entity/expert/expert.validation-service';
import { ExpertEntityPolicyService } from '../../../services/policy/concrete/widget/expert/expertEntity.policy-service';
import { ILogger, ConsoleLogger, Variable } from '../../../utils/index';
import { IExpertEntityPolicyService } from '../../../services/policy/concrete/widget/expert/i-expertEntity.policy-service';
import { IExpertValidationService } from '../../../services/validation/concrete/entity/expert/i-expert.validation-service';
import { UserApiService, GetAllResponse, IUserApiService } from '../../../services/serverApi/index';
import { UserEntity } from '../../../entities/main/users/user.entity';
import { promise } from 'selenium-webdriver';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';

@Component({
    selector: 'users-table',
    styleUrls: ['./usersTable.scss'],
    templateUrl: './usersTable.html'
})
export class UsersTableComponent implements OnInit {
    /// inputs
    @Input() filter: any;
    /// outputs

    /// children
    @ViewChild('editModal')
    protected editModal: ModalComponent;

    /// service fields
    private _defaultPageNumber: number = 0;
    private _defaultPageSize: number = 100;
    private _defaultSorting: string = '';
    private _defaultFilter: any = null;

    /// promise fields
    protected firstLoadingPromise: Promise<void>;
    protected getAllPromise: Promise<void>;

    /// fields
    protected totalCount: number;
    protected items: UserEntity[];
    protected selectedEntity: UserEntity;

    /// injected dependencies
    protected logger: ILogger;
    protected entityApiService: IUserApiService;
    protected entityPolicyService: IExpertEntityPolicyService;
    protected entityValidationService: IExpertValidationService;

    /// ctor
    constructor(logger: ConsoleLogger,
                entityApiService: UserApiService,
                entityPolicyService: ExpertEntityPolicyService,
                entityValidationService: ExpertValidationService) {
        this.logger = logger;
        this.entityApiService = entityApiService;
        this.entityPolicyService = entityPolicyService;
        this.entityValidationService = entityValidationService;
        this.logger.logDebug('UsersTableComponent: Component has been constructed.');
    }

    /// methods
    ngOnInit(): void {
        const self = this;
        self.firstLoadingPromise = self.getAllEntities();
        this.logger.logDebug('Init');
    }

    protected getAllEntities(): Promise<void> {
        const self = this;
        self.getAllPromise = self.entityApiService.getAll(this.getPageNumber(), this.getPageSize(), this.buildSorting(), this.buildFilter())
            .then((response: GetAllResponse<UserEntity>) => {
                self.items = response.items;

                console.log(self.items);
                self.totalCount = response.totalCount;
                return Promise.resolve();
            });

        return self.getAllPromise;
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
    /// modal

    protected createModalOpen(): Promise<void> {
        const self = this;
        self.selectedEntity = new UserEntity();
        self.selectedEntity.avatarUrl  = '';
        self.selectedEntity.firstName = 'toto';

        return self.editModal.open();
    }




}
