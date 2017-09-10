import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable } from './../../../utils/index';
import { ExpertEntity } from './../../../entities/index';
import { IExpertApiService, ExpertApiService, GetAllResponse } from './../../../services/serverApi/index';
@Component({
    selector: 'experts-table',
    styleUrls: ['./expertsTable.scss'],
    templateUrl: './expertsTable.html',
})
export class ExpertsTableComponent implements OnInit {
    @Input() pageNumber: number;
    @Input() pageSize: number;
    @Input() sorting: string;
    @Input() filter: any;
    @Input() siteId: number;
    @ViewChild('expertDetailsModal')
    protected modal: ModalComponent;
    /// fields
    private _defaultPageNumber: number = 0;
    private _defaultPageSize: number = 100;
    private _defaultSorting: string = null;
    private _defaultFilter: any = null;
    private _isInitialized: boolean;
    protected totalCount: number;
    protected items: Array<ExpertEntity>;
    protected selectedEntity: ExpertEntity;
    /// injected dependencies
    protected expertApiService: IExpertApiService;
    /// ctor
    constructor(siteApiService: ExpertApiService) {
        this.expertApiService = siteApiService;
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
        let operationPromise = self.expertApiService
            .getAll(self.getPageNumber(), self.getPageSize(), self.buildSorting(), self.buildFilter())
            .then(function (response: GetAllResponse<ExpertEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected changeEntityActivity(item: ExpertEntity): Promise<void> {
        let actionPromise: Promise<void>;
        if (Variable.isNotNullOrUndefined(item)) {
            /// add server api (for change activity) and use it here
            actionPromise = Promise.resolve();
        } else {
            actionPromise = Promise.resolve();
        }
        return actionPromise;
    }
    protected changeEntityOrder(item: ExpertEntity): Promise<void> {
        let actionPromise: Promise<void>;
        if (Variable.isNotNullOrUndefined(item)) {
            /// add server api (for change order) and use it here
            actionPromise = Promise.resolve();
        } else {
            actionPromise = Promise.resolve();
        }
        return actionPromise;
    }
    protected deleteEntity(id: number): Promise<void> {
        let self = this;
        let operationPromise = self.expertApiService
            .delete(id)
            .then(function (): Promise<void> {
                let elementIndex = self.items.findIndex((item: ExpertEntity) => item.id === id);
                self.items.splice(elementIndex, 1);
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalOpenCreate(): Promise<void> {
        let self = this;
        self.selectedEntity = new ExpertEntity();
        self.selectedEntity.siteId = this.siteId;
        self.selectedEntity.isActive = true;
        self.selectedEntity.order = 0;
        self.modal.open();
        return Promise.resolve();
    }
    protected modalOpenEdit(id: number): Promise<void> {
        let self = this;
        self.selectedEntity = self.items.find((item: ExpertEntity) => item.id === id);
        self.modal.open();
        let operationPromise = self.expertApiService
            .get(id)
            .then(function (response: ExpertEntity): Promise<void> {
                self.selectedEntity = response;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalApply() {
        let self = this;
        let operationPromise: Promise<ExpertEntity> = self.selectedEntity.id ?
            self.expertApiService.update(self.selectedEntity) :
            self.expertApiService.create(self.selectedEntity);
        return operationPromise
            .then(function (entity: ExpertEntity): Promise<void> {
                let elementIndex = self.items.findIndex((item: ExpertEntity) => item.id === entity.id);
                if (elementIndex !== -1) {
                    self.items.splice(elementIndex, 1, entity);
                } else {
                    self.items.push(entity);
                }
                self.selectedEntity = null;
                return self.modal.close();
            });
    }
    protected modalDismiss(): Promise<void> {
        this.selectedEntity = null;
        return this.modal.dismiss();
    }
    protected getEntityRowClass(item: ExpertEntity): string {
        let classValue: string;
        if (Variable.isNotNullOrUndefined(item) && item.isActive) {
            classValue = null; //'table-info';
        } else if (Variable.isNotNullOrUndefined(item) && !item.isActive) {
            classValue = 'table-danger';
        } else {
            classValue = null;
        }
        return classValue;
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
