import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable } from './../../../utils/index';
import { ExpertEntity } from './../../../entities/index';
import { IContentApiService, ContentApiService, IExpertApiService, ExpertApiService, GetAllResponse } from './../../../services/serverApi/index';
import { CropperSettings, ImageCropperComponent } from 'ng2-img-cropper';
const DefaultExpertAvatar: string = 'data:image/png;base64,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';
@Component({
    selector: 'experts-table',
    styleUrls: ['./expertsTable.scss'],
    templateUrl: './expertsTable.html'
})
export class ExpertsTableComponent implements OnInit {
    @Input() pageNumber: number;
    @Input() pageSize: number;
    @Input() sorting: string;
    @Input() filter: any;
    @Input() siteId: number;
    @ViewChild('expertDetailsModal')
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
    protected items: Array<ExpertEntity>;
    protected entity: ExpertEntity;
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
    protected expertApiService: IExpertApiService;
    protected contentApiService: IContentApiService;
    /// ctor
    constructor(siteApiService: ExpertApiService, contentApiService: ContentApiService) {
        this.expertApiService = siteApiService;
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
    protected getEntityRowClass(item: ExpertEntity): string {
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
        let operationPromise = self.expertApiService
            .getAll(self.getPageNumber(), self.getPageSize(), self.buildSorting(), self.buildFilter())
            .then(function (response: GetAllResponse<ExpertEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            });
        return operationPromise;
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
    // activity
    protected onChangeEntityActivity(entity: ExpertEntity): void {
        if (Variable.isNotNullOrUndefined(entity)) {
            entity.isActive = !entity.isActive;
            // TODO: after adding spinners should disable updating activity for this entity until promise ends
            this.commitChangeEntityActivity(entity);
        }
    }
    protected commitChangeEntityActivity(entity: ExpertEntity): Promise<void> {
        let actionPromise: Promise<void>;
        if (Variable.isNotNullOrUndefined(entity)) {
            actionPromise = this.expertApiService
                .patchActivity(entity.id, entity.isActive)
                .then(function(): void { });
        } else {
            actionPromise = Promise.resolve();
        }
        return actionPromise;
    }
    // order
    protected canIncrementOrder(entity: ExpertEntity): boolean {
        return this.items.findIndex((item) => item.id === entity.id) < (this.items.length - 1);
    }
    protected canDecrementOrder(entity: ExpertEntity): boolean {
        return this.items.findIndex((item) => item.id === entity.id) > 0;
    }
    protected incrementOrder(entity: ExpertEntity): void {
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
    protected decrementOrder(entity: ExpertEntity): void {
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
    protected commitChangeEntityOrder(entity: ExpertEntity): Promise<void> {
        let actionPromise: Promise<void>;
        if (Variable.isNotNullOrUndefined(entity)) {
            actionPromise = this.expertApiService
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
        self.entity = self.items.find((item: ExpertEntity) => item.id === id);
        self.isOperationModeInfo = true;
        self.modal.open();
        let operationPromise = self.expertApiService
            .get(id)
            .then(function (response: ExpertEntity): Promise<void> {
                self.entity = response;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalOpenCreate(): Promise<void> {
        let self = this;
        self.entity = new ExpertEntity();
        self.entity.siteId = this.siteId;
        self.entity.photoUrl = DefaultExpertAvatar;
        self.entity.isActive = true;
        self.entity.order = this.getNewEntityOrder();
        self.isOperationModeAddOrUpdate = true;
        self.modal.open();
        return Promise.resolve();
    }
    protected modalOpenEdit(id: number): Promise<void> {
        let self = this;
        self.entity = self.items.find((item: ExpertEntity) => item.id === id);
        self.isOperationModeAddOrUpdate = true;
        self.modal.open();
        let operationPromise = self.expertApiService
            .get(id)
            .then(function (response: ExpertEntity): Promise<void> {
                self.entity = response;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalApply() {
        let self = this;
        let operationPromise: Promise<ExpertEntity> = self.entity.id ?
            self.expertApiService.update(self.entity) :
            self.expertApiService.create(self.entity);
        return operationPromise
            .then(function (entity: ExpertEntity): Promise<void> {
                let elementIndex = self.items.findIndex((item: ExpertEntity) => item.id === entity.id);
                if (elementIndex !== -1) {
                    self.items.splice(elementIndex, 1, entity);
                } else {
                    self.items.push(entity);
                }
                self.entity = null;
                self.isOperationModeInfo = false;
                self.isOperationModeAddOrUpdate = false;
                return self.modal.close();
            });
    }
    protected modalDismiss(): Promise<void> {
        this.entity = null;
        this.isOperationModeInfo = false;
        this.isOperationModeAddOrUpdate = false;
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
    // working hours
    protected onWorkingHoursChanged(value: string): void {
        this.entity.workingHours = value;
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
