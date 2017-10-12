import { Component, OnInit, Input, Output, ViewChild, EventEmitter } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable, WorkingInterval } from './../../../utils/index';
import { ExpertEntity } from './../../../entities/index';
import { IContentApiService, ContentApiService } from './../../../services/serverApi/index';
import { IExpertApiService, ExpertApiService } from './../../../services/serverApi/index';
import { GetAllResponse } from './../../../services/serverApi/index';
import { CropperSettings, ImageCropperComponent } from 'ng2-img-cropper';
import { ApplicationConstants } from './../../../app.constants';
import { ExpertsConstants } from './../experts.constants';
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
    @Output() onExpertsChange: EventEmitter<any> = new EventEmitter<any>();
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
    protected switcherSettings = ApplicationConstants.switcherSettings;
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
    protected notifyOnChanges(entityActivated: boolean = false, entityDeactivated: boolean = false): void {
        if (Variable.isNotNullOrUndefined(this.onExpertsChange)) {
            this.onExpertsChange
                .emit({
                    totalCount: this.totalCount,
                    entityWasActivated: entityActivated,
                    entityWasDeactivated: entityDeactivated
                });
        }
    }
    protected getEntityRowClass(item: ExpertEntity): string {
        let classValue: string;
        if (Variable.isNotNullOrUndefined(item) && item.isActive) {
            classValue = 'experts-table-body-row-active';
        } else if (Variable.isNotNullOrUndefined(item) && !item.isActive) {
            classValue = 'experts-table-body-row-not-active';
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
                self.totalCount--;
                let elementIndex = self.items.findIndex((item: ExpertEntity) => item.id === id);
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
            let self = this;
            let newActivityValue: boolean = entity.isActive;
            actionPromise = this.expertApiService
                .patchActivity(entity.id, newActivityValue)
                .then(function(): void {
                    self.notifyOnChanges(newActivityValue, !newActivityValue);
                });
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
    protected decrementOrder(entity: ExpertEntity): void {
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
        self.entity.photoUrl = ExpertsConstants.ExpertAvatarDefault;
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
                    self.notifyOnChanges();
                } else {
                    self.items.push(entity);
                    self.totalCount++;
                    self.notifyOnChanges(entity.isActive, !entity.isActive);
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
    protected actualizeWorkingHours(actualWorkingHours: Array<WorkingInterval>): void {
        this.entity.workingHours = actualWorkingHours;
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
