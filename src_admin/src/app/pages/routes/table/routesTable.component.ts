import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable } from './../../../utils/index';
import { RouteEntity } from './../../../entities/index';
import { IContentApiService, ContentApiService, IRouteApiService, RouteApiService, GetAllResponse } from './../../../services/serverApi/index';
import { CropperSettings, ImageCropperComponent } from 'ng2-img-cropper';
const DefaultRouteImg: string = 'data:image/jpeg;base64,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';
@Component({
    selector: 'routes-table',
    styleUrls: ['./routesTable.scss'],
    templateUrl: './routesTable.html'
})
export class RoutesTableComponent implements OnInit {
    @Input() pageNumber: number;
    @Input() pageSize: number;
    @Input() sorting: string;
    @Input() filter: any;
    @Input() siteId: number;
    @ViewChild('routeDetailsModal')
    protected modal: ModalComponent;
    @ViewChild('cropper', undefined)
    protected cropper: ImageCropperComponent;
    /// fields
    private _defaultPageNumber: number = 0;
    private _defaultPageSize: number = 100;
    private _defaultSorting: string = 'order asc';
    private _defaultFilter: any = null;
    private _isInitialized: boolean = false;
    protected totalCount: number;
    protected items: Array<RouteEntity>;
    protected entity: RouteEntity;
    protected avatarWidth: number;
    protected avatarHeight: number;
    protected avatarCropperData: any;
    protected avatarCropperSettings: CropperSettings;
    protected stubAvatarUrl: string;
    protected isOperationModeInfo: boolean;
    protected isOperationModeAddOrUpdate: boolean;
    /// properties
    private _showAvatarButtons: boolean = true;
    protected get showAvatarButtons(): boolean {
        return this._showAvatarButtons;
    }
    protected set showAvatarButtons(value: boolean) {
        this._showAvatarButtons = value;
    }
    private _showAvatarBrowse: boolean = false;
    protected get showAvatarBrowse(): boolean {
        return this._showAvatarBrowse;
    }
    protected set showAvatarBrowse(value: boolean) {
        this._showAvatarBrowse = value;
    }
    private _showAvatarChangeUrl: boolean = false;
    protected get showAvatarChangeUrl(): boolean {
        return this._showAvatarChangeUrl;
    }
    protected set showAvatarChangeUrl(value: boolean) {
        this._showAvatarChangeUrl = value;
    }
    /// injected dependencies
    protected routeApiService: IRouteApiService;
    protected contentApiService: IContentApiService;
    /// ctor
    constructor(siteApiService: RouteApiService, contentApiService: ContentApiService) {
        this.routeApiService = siteApiService;
        this.contentApiService = contentApiService;
        this.avatarWidth = 150;
        this.avatarHeight = 150;
    }
    /// methods
    ngOnInit(): void {
        let self = this;
        self.initializeAvatarCropper();
        self.getAllEntities()
            .then(() => self._isInitialized = true);
    }
    protected getEntityRowClass(item: RouteEntity): string {
        let classValue: string;
        if (Variable.isNotNullOrUndefined(item) && item.isActive) {
            classValue = null;
        } else if (Variable.isNotNullOrUndefined(item) && !item.isActive) {
            classValue = 'table-danger';
        } else {
            classValue = null;
        }
        return classValue;
    }
    protected getAllEntities(): Promise<void> {
        let self = this;
        let operationPromise = self.routeApiService
            .getAll(self.getPageNumber(), self.getPageSize(), self.buildSorting(), self.buildFilter())
            .then(function (response: GetAllResponse<RouteEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected deleteEntity(id: number): Promise<void> {
        let self = this;
        let operationPromise = self.routeApiService
            .delete(id)
            .then(function (): Promise<void> {
                let elementIndex = self.items.findIndex((item: RouteEntity) => item.id === id);
                self.items.splice(elementIndex, 1);
                return Promise.resolve();
            });
        return operationPromise;
    }
    // activity
    protected onChangeEntityActivity(entity: RouteEntity): void {
        if (Variable.isNotNullOrUndefined(entity)) {
            entity.isActive = !entity.isActive;
            // TODO: after adding spinners should disable updating activity for this entity until promise ends
            this.commitChangeEntityActivity(entity);
        }
    }
    protected commitChangeEntityActivity(entity: RouteEntity): Promise<void> {
        let actionPromise: Promise<void>;
        if (Variable.isNotNullOrUndefined(entity)) {
            actionPromise = this.routeApiService
                .patchActivity(entity.id, entity.isActive)
                .then(function(): void { });
        } else {
            actionPromise = Promise.resolve();
        }
        return actionPromise;
    }
    // order
    protected canIncrementOrder(entity: RouteEntity): boolean {
        return this.items.findIndex((item) => item.id === entity.id) < (this.items.length - 1);
    }
    protected canDecrementOrder(entity: RouteEntity): boolean {
        return this.items.findIndex((item) => item.id === entity.id) > 0;
    }
    protected incrementOrder(entity: RouteEntity): void {
        if (Variable.isNotNullOrUndefined(entity)) {
            let entityIndex: number = this.items.findIndex((item) => item.id === entity.id);
            if (entityIndex > -1 && entityIndex < this.items.length - 1) {
                let newOrderValue: number = this.items[entityIndex].order + 1;
                this.items[entityIndex + 1].order = this.items[entityIndex].order;
                this.items[entityIndex].order = newOrderValue;
                let stub = this.items[entityIndex];
                this.items[entityIndex] = this.items[entityIndex + 1];
                this.items[entityIndex + 1] = stub;
                // TODO: after adding spinners should disable updating order for this entity until promise ends
                this.commitChangeEntityOrder(this.items[entityIndex]);
                this.commitChangeEntityOrder(this.items[entityIndex + 1]);
            }
        }
    }
    protected decrementOrder(entity: RouteEntity): void {
        if (Variable.isNotNullOrUndefined(entity)) {
            let entityIndex: number = this.items.findIndex((item) => item.id === entity.id);
            if (entityIndex > 0 && this.items.length > 1) {
                let newOrderValue: number = this.items[entityIndex].order - 1;
                this.items[entityIndex - 1].order = this.items[entityIndex].order;
                this.items[entityIndex].order = newOrderValue;
                let stub = this.items[entityIndex];
                this.items[entityIndex] = this.items[entityIndex - 1];
                this.items[entityIndex - 1] = stub;
                // TODO: after adding spinners should disable updating order for this entity until promise ends
                this.commitChangeEntityOrder(this.items[entityIndex - 1]);
                this.commitChangeEntityOrder(this.items[entityIndex]);
            }
        }
    }
    protected commitChangeEntityOrder(entity: RouteEntity): Promise<void> {
        let actionPromise: Promise<void>;
        if (Variable.isNotNullOrUndefined(entity)) {
            actionPromise = this.routeApiService
                .patchOrder(entity.id, entity.order)
                .then(function(): void { });
        } else {
            actionPromise = Promise.resolve();
        }
        return actionPromise;
    }
    // modal
    protected modalOpenInfo(id: number): Promise<void> {
        let self = this;
        self.entity = self.items.find((item: RouteEntity) => item.id === id);
        self.isOperationModeInfo = true;
        self.modal.open();
        let operationPromise = self.routeApiService
            .get(id)
            .then(function (response: RouteEntity): Promise<void> {
                self.entity = response;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalOpenCreate(): Promise<void> {
        let self = this;
        self.entity = new RouteEntity();
        self.entity.siteId = this.siteId;
        self.entity.photoUrl = DefaultRouteImg;
        self.entity.isActive = true;
        self.entity.order = this.getNewEntityOrder();
        self.isOperationModeAddOrUpdate = true;
        self.modal.open();
        return Promise.resolve();
    }
    protected modalOpenEdit(id: number): Promise<void> {
        let self = this;
        self.entity = self.items.find((item: RouteEntity) => item.id === id);
        self.isOperationModeAddOrUpdate = true;
        self.modal.open();
        let operationPromise = self.routeApiService
            .get(id)
            .then(function (response: RouteEntity): Promise<void> {
                self.entity = response;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalApply() {
        let self = this;
        let operationPromise: Promise<RouteEntity> = self.entity.id ?
            self.routeApiService.update(self.entity) :
            self.routeApiService.create(self.entity);
        return operationPromise
            .then(function (entity: RouteEntity): Promise<void> {
                let elementIndex = self.items.findIndex((item: RouteEntity) => item.id === entity.id);
                if (elementIndex !== -1) {
                    self.items.splice(elementIndex, 1, entity);
                } else {
                    self.items.push(entity);
                }
                self.entity = null;
                self.isOperationModeAddOrUpdate = false;
                self.isOperationModeInfo = false;
                return self.modal.close();
            });
    }
    protected modalDismiss(): Promise<void> {
        this.entity = null;
        this.isOperationModeAddOrUpdate = false;
        this.isOperationModeInfo = false;
        return this.modal.dismiss();
    }
    // avatar
    protected getAvatar(): any {
        let avatar;
        if (this.showAvatarBrowse && this.avatarCropperData && this.avatarCropperData.image) {
            avatar = this.avatarCropperData.image;
        } else if (this.showAvatarChangeUrl) {
            avatar = this.stubAvatarUrl;
        } else {
            avatar = this.entity.photoUrl;
        }
        return avatar;
    }
    protected browseAvatar(): void {
        this.avatarCropperData = {};
        this.showAvatarButtons = false;
        this.showAvatarBrowse = true;
    }
    protected showAvatarBrowseAccept(): void {
        let self = this;
        self.contentApiService
            .postImage(self.avatarCropperData.image)
            .then(function (imageUrl: string) {
                // TODO: remove this stub result after implementing #27 - content controller
                self.entity.photoUrl = self.avatarCropperData.image; // imageUrl;
                self.showAvatarBrowseCancel();
            });
    }
    protected showAvatarBrowseCancel(): void {
        this.avatarCropperData = {};
        this.showAvatarBrowse = false;
        this.showAvatarButtons = true;
    }
    protected avatarBrowseFileChangeListener($event) {
        let image: any = new Image();
        let file: File = $event.target.files[0];
        let fileReader: FileReader = new FileReader();
        let self = this;
        fileReader.onloadend = function (loadEvent: any): void {
            image.src = loadEvent.target.result;
            self.cropper.setImage(image);

        };
        fileReader.readAsDataURL(file);
    }
    protected changeAvatarUrl(): void {
        this.showAvatarButtons = false;
        this.showAvatarChangeUrl = true;
    }
    protected changeAvatarUrlAccept(): void {
        this.entity.photoUrl = this.stubAvatarUrl;
        this.changeAvatarUrlCancel();
    }
    protected changeAvatarUrlCancel(): void {
        this.stubAvatarUrl = null;
        this.showAvatarChangeUrl = false;
        this.showAvatarButtons = true;
    }
    /// predicates
    protected isInitialized(): boolean {
        return this._isInitialized;
    }
    protected isSelectedEntityDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.entity);
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
    private getNewEntityOrder(): number {
        let maxOrder: number = this.items.length > 0 ? this.items[0].order : 0;
        for (let i: number = 1; i < this.items.length; i++) {
            maxOrder = this.items[i].order > maxOrder ? this.items[i].order : maxOrder;
        }
        return maxOrder === 0 ? 0 : maxOrder + 1;
    }
    private initializeAvatarCropper(): void {
        this.avatarCropperSettings = new CropperSettings();
        this.avatarCropperSettings.rounded = true;
        this.avatarCropperSettings.noFileInput = true;
        this.avatarCropperSettings.minWithRelativeToResolution = true;
        this.avatarCropperSettings.minWidth = this.avatarWidth;
        this.avatarCropperSettings.minHeight = this.avatarHeight;
        this.avatarCropperSettings.width = this.avatarWidth;
        this.avatarCropperSettings.height = this.avatarHeight;
        this.avatarCropperSettings.croppedWidth = this.avatarWidth;
        this.avatarCropperSettings.croppedHeight = this.avatarHeight;
        this.avatarCropperSettings.canvasWidth = this.avatarWidth * 2;
        this.avatarCropperSettings.canvasHeight = this.avatarHeight * 2;
        this.avatarCropperData = {};
    }
}
