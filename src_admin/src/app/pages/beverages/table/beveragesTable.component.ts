import { Component, OnInit, Input, Output, ViewChild, EventEmitter } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, Extensions } from './../../../utils/index';
import { BeverageEntity } from './../../../entities/index';
import { IContentApiService, ContentApiService } from './../../../services/serverApi/index';
import { IBeverageApiService, BeverageApiService } from './../../../services/serverApi/index';
import { GetAllResponse } from './../../../services/serverApi/index';
import { CropperSettings, ImageCropperComponent } from 'ng2-img-cropper';
import { ApplicationConstants } from './../../../app.constants';
import { BeveragesConstants } from './../beverages.constants';
@Component({
    selector: 'beverages-table',
    styleUrls: ['./beveragesTable.scss'],
    templateUrl: './beveragesTable.html'
})
export class BeveragesTableComponent implements OnInit {
    @Input() pageNumber: number;
    @Input() pageSize: number;
    @Input() sorting: string;
    @Input() filter: any;
    @Input() siteId: number;
    @Output() onBeveragesChange: EventEmitter<any> = new EventEmitter<any>();
    /// modals
    @ViewChild('confirmationDeleteModal')
    protected confirmationDeleteModal: ModalComponent;
    @ViewChild('beverageDetailsModal')
    protected modal: ModalComponent;
    @ViewChild('cropper', undefined)
    protected cropper: ImageCropperComponent;
    /// fields
    private _defaultPageNumber: number = 0;
    private _defaultPageSize: number = 100;
    private _defaultSorting: string = 'order asc';
    private _defaultFilter: any = null;
    private _isInitialized: boolean = false;
    protected switcherSettings = ApplicationConstants.switcherSettings;
    protected totalCount: number;
    protected items: Array<BeverageEntity>;
    protected entity: BeverageEntity;
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
    protected beverageApiService: IBeverageApiService;
    protected contentApiService: IContentApiService;
    /// ctor
    constructor(siteApiService: BeverageApiService, contentApiService: ContentApiService) {
        this.beverageApiService = siteApiService;
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
    protected notifyOnChanges(entityActivated: boolean = false, entityDeactivated: boolean = false): void {
        if (Variable.isNotNullOrUndefined(this.onBeveragesChange)) {
            this.onBeveragesChange
                .emit({
                    totalCount: this.totalCount,
                    entityWasActivated: entityActivated,
                    entityWasDeactivated: entityDeactivated
                });
        }
    }
    protected getEntityRowClass(item: BeverageEntity): string {
        let classValue: string;
        if (Variable.isNotNullOrUndefined(item) && item.isActive) {
            classValue = 'beverages-table-body-row-active';
        } else if (Variable.isNotNullOrUndefined(item) && !item.isActive) {
            classValue = 'beverages-table-body-row-not-active';
        } else {
            classValue = null;
        }
        return classValue;
    }
    protected getAllEntities(): Promise<void> {
        let self = this;
        let operationPromise = self.beverageApiService
            .getAll(self.getPageNumber(), self.getPageSize(), self.buildSorting(), self.buildFilter())
            .then(function (response: GetAllResponse<BeverageEntity>): Promise<void> {
                self.totalCount = response.totalCount;
                self.items = response.items;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected deleteEntity(id: number): Promise<void> {
        let self = this;
        let operationPromise = self.beverageApiService
            .delete(id)
            .then(function (): Promise<void> {
                self.totalCount--;
                let elementIndex = self.items.findIndex((item: BeverageEntity) => item.id === id);
                if (elementIndex > -1) {
                    self.notifyOnChanges(false, self.items[elementIndex].isActive);
                    self.items.splice(elementIndex, 1);
                } else {
                    self.notifyOnChanges();
                }
                return Promise.resolve();
            });
        return operationPromise;
    }
    // activity
    protected onChangeEntityActivity(entity: BeverageEntity): void {
        if (Variable.isNotNullOrUndefined(entity)) {
            entity.isActive = !entity.isActive;
            // TODO: after adding spinners should disable updating activity for this entity until promise ends
            this.commitChangeEntityActivity(entity);
        }
    }
    protected commitChangeEntityActivity(entity: BeverageEntity): Promise<void> {
        let actionPromise: Promise<void>;
        if (Variable.isNotNullOrUndefined(entity)) {
            let self = this;
            let newActivityValue: boolean = entity.isActive;
            actionPromise = this.beverageApiService
                .patchActivity(entity.id, entity.isActive)
                .then(function(): void {
                    self.notifyOnChanges(newActivityValue, !newActivityValue);
                });
        } else {
            actionPromise = Promise.resolve();
        }
        return actionPromise;
    }
    // order
    protected canIncrementOrder(entity: BeverageEntity): boolean {
        return this.items.findIndex((item) => item.id === entity.id) < (this.items.length - 1);
    }
    protected canDecrementOrder(entity: BeverageEntity): boolean {
        return this.items.findIndex((item) => item.id === entity.id) > 0;
    }
    protected incrementOrder(entity: BeverageEntity): void {
        if (Variable.isNotNullOrUndefined(entity)) {
            let entityIndex: number = this.items.findIndex((item) => item.id === entity.id);
            if (entityIndex > -1 && entityIndex < this.items.length - 1) {
                let newOrderValue: number = this.items[entityIndex + 1].order;
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
    protected decrementOrder(entity: BeverageEntity): void {
        if (Variable.isNotNullOrUndefined(entity)) {
            let entityIndex: number = this.items.findIndex((item) => item.id === entity.id);
            if (entityIndex > 0 && this.items.length > 1) {
                let newOrderValue: number = this.items[entityIndex - 1].order;
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
    protected commitChangeEntityOrder(entity: BeverageEntity): Promise<void> {
        let actionPromise: Promise<void>;
        if (Variable.isNotNullOrUndefined(entity)) {
            actionPromise = this.beverageApiService
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
        self.entity = self.items.find((item: BeverageEntity) => item.id === id);
        self.isOperationModeInfo = true;
        self.modal.open();
        let operationPromise = self.beverageApiService
            .get(id)
            .then(function (response: BeverageEntity): Promise<void> {
                self.entity = response;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalOpenCreate(): Promise<void> {
        let self = this;
        self.entity = new BeverageEntity();
        self.entity.siteId = this.siteId;
        self.entity.photoUrl = BeveragesConstants.BeverageImageDefault;
        self.entity.isActive = true;
        self.entity.order = this.getNewEntityOrder();
        self.isOperationModeAddOrUpdate = true;
        self.modal.open();
        return Promise.resolve();
    }
    protected modalOpenEdit(id: number): Promise<void> {
        let self = this;
        self.entity = self.items.find((item: BeverageEntity) => item.id === id);
        self.isOperationModeAddOrUpdate = true;
        self.modal.open();
        let operationPromise = self.beverageApiService
            .get(id)
            .then(function (response: BeverageEntity): Promise<void> {
                self.entity = response;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalApply() {
        let self = this;
        let operationPromise: Promise<BeverageEntity> = self.entity.id ?
            self.beverageApiService.update(self.entity) :
            self.beverageApiService.create(self.entity);
        return operationPromise
            .then(function (entity: BeverageEntity): Promise<void> {
                let elementIndex = self.items.findIndex((item: BeverageEntity) => item.id === entity.id);
                if (elementIndex !== -1) {
                    self.items.splice(elementIndex, 1, entity);
                    self.notifyOnChanges();
                } else {
                    self.items.push(entity);
                    self.totalCount++;
                    self.notifyOnChanges(entity.isActive, !entity.isActive);
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
    /// confirmation delete modal
    protected deleteCandidateId: number;
    protected getDeleteCandidateDisplayText(): string {
        let result;
        if (Variable.isNotNullOrUndefined(this.deleteCandidateId)) {
            const elementIndex = this.items
                .findIndex((item: BeverageEntity) => item.id === this.deleteCandidateId);
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
