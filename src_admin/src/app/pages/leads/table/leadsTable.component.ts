import { Component, OnInit, Input, ViewChild } from '@angular/core';
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
    @Input() pageNumber: number;
    @Input() pageSize: number;
    @Input() sorting: string;
    @Input() filter: any;
    @ViewChild('leadDetailsInfoModal')
    protected modalInfo: ModalComponent;
    /// fields
    private _defaultPageNumber: number = 0;
    private _defaultPageSize: number = 100;
    private _defaultSorting: string = 'recievedUtc desc';
    private _defaultFilter: any = null;
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
            .getAll(self.getPageNumber(), self.getPageSize(), self.buildSorting(), self.buildFilter())
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
    /// helpers
    private getPageNumber(): number {
        return Variable.isNotNullOrUndefined(this.pageNumber) ? this.pageNumber : this._defaultPageNumber;
    }
    private getPageSize(): number {
        return Variable.isNotNullOrUndefined(this.pageSize) ? this.pageSize : this._defaultPageSize;
    }
    private buildSorting(): string {
        return Variable.isNotNullOrUndefined(this.sorting) ? this.sorting : this._defaultSorting;
    }
    private buildFilter(): any {
        return Variable.isNotNullOrUndefined(this.filter) ? this.filter : this._defaultFilter;
    }
}
