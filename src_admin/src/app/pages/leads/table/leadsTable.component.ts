import { Component, OnInit, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, IAuthorizationManager, AuthorizationManager } from './../../../utils/index';
import { LeadEntity } from './../../../entities/index';
import { ILeadApiService, LeadApiService, GetAllResponse } from './../../../services/serverApi/index';
@Component({
    selector: 'leads-table',
    styleUrls: ['./leadsTable.scss'],
    templateUrl: './leadsTable.html',
})
export class LeadsTableComponent implements OnInit {
    @ViewChild('leadDetailsInfoModal')
    protected modalInfo: ModalComponent;
    /// fields
    private _isInitialized: boolean;
    protected totalCount: number;
    protected items: Array<LeadEntity>;
    protected selectedEntity: LeadEntity;
    /// injected dependencies
    protected authorizationManager: IAuthorizationManager;
    protected leadApiService: ILeadApiService;
    /// ctor
    constructor(authorizationManager: AuthorizationManager, leadApiService: LeadApiService) {
        this.authorizationManager = authorizationManager;
        this.leadApiService = leadApiService;
        this._isInitialized = false;
    }
    /// methods
    ngOnInit(): void {
        let self = this;
        self.getAllEntities()
            .then(() => self._isInitialized = true);
    }
    protected getAllEntities(): Promise<void> {
        let self = this;
        let operationPromise = self.leadApiService
            .getAll(0, 100, 'recievedUtc desc', null)
            .then(function (response: GetAllResponse<LeadEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected deleteEntity(id: number): Promise<void> {
        let self = this;
        let operationPromise = self.leadApiService
            .delete(id)
            .then(function (): Promise<void> {
                let elementIndex = self.items.findIndex((item: LeadEntity) => item.id === id);
                self.items.splice(elementIndex, 1);
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalOpenInfo(id: number): Promise<void> {
        let self = this;
        self.selectedEntity = self.items.find((item: LeadEntity) => item.id === id);
        self.modalInfo.open();
        let operationPromise = self.leadApiService
            .get(id)
            .then(function (response: LeadEntity): Promise<void> {
                self.selectedEntity = response;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalDismiss(): Promise<void> {
        this.selectedEntity = null;
        return this.modalInfo.dismiss();
    }
    protected exportDataToExcel(): Promise<void> {
        return Promise.resolve();
    }
    /// predicates
    protected isInitialized(): boolean {
        return this._isInitialized;
    }
    protected isSelectedEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.selectedEntity);
    }
}
