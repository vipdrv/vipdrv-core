import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable } from './../../../utils/index';
import { ExpertEntity } from './../../../entities/index';
import { IExpertApiService, ExpertApiService, GetAllResponse } from './../../../services/serverApi/index';
import { CropperSettings, ImageCropperComponent } from 'ng2-img-cropper';
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
    private _defaultSorting: string = null;
    private _defaultFilter: any = null;
    private _isInitialized: boolean = false;
    protected totalCount: number;
    protected items: Array<ExpertEntity>;
    protected selectedEntity: ExpertEntity;
    protected avatarWidth: number;
    protected avatarHeight: number;
    protected avatarCropperData: any;
    protected avatarCropperSettings: CropperSettings;
    protected stubAvatarUrl: string;
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
    /// ctor
    constructor(siteApiService: ExpertApiService) {
        this.expertApiService = siteApiService;
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
            classValue = null; //'table-info';
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
    // modal
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
    // avatar
    protected getAvatar(): any {
        let avatar;
        if (this.showAvatarBrowse && this.avatarCropperData && this.avatarCropperData.image) {
            avatar = this.avatarCropperData.image;
        } else if (this.showAvatarChangeUrl) {
            avatar = this.stubAvatarUrl;
        } else {
            avatar = this.selectedEntity.photoUrl;
        }
        return avatar;
    }
    protected browseAvatar(): void {
        this.avatarCropperData = {};
        this.showAvatarButtons = false;
        this.showAvatarBrowse = true;
    }
    protected showAvatarBrowseAccept(): void {
        // TODO: accept (save image data to server store and get url)
        this.showAvatarBrowseCancel();
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
        this.stubAvatarUrl = this.selectedEntity.photoUrl;
        this.showAvatarButtons = false;
        this.showAvatarChangeUrl = true;
    }
    protected changeAvatarUrlAccept(): void {
        this.selectedEntity.photoUrl = this.stubAvatarUrl;
        this.changeAvatarUrlCancel();
    }
    protected changeAvatarUrlCancel(): void {
        this.stubAvatarUrl = null;
        this.showAvatarChangeUrl = false;
        this.showAvatarButtons = true;
    }
    // working hours
    protected onWorkingHoursChanged(value: string): void {
        this.selectedEntity.workingHours = value;
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
