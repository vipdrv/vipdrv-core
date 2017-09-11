import { Component, OnInit, Input, ViewChild } from '@angular/core';
import { ModalComponent } from 'ng2-bs3-modal/ng2-bs3-modal';
import { Variable } from './../../../utils/index';
import { BeverageEntity } from './../../../entities/index';
import { IContentApiService, ContentApiService, IBeverageApiService, BeverageApiService, GetAllResponse } from './../../../services/serverApi/index';
import { CropperSettings, ImageCropperComponent } from 'ng2-img-cropper';
const DefaultBeverageImg: string = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADICAMAAACahl6sAAAAe1BMVEX///8AAACDg4M7OzuTk5NXV1e/v78rKytra2vT09Pd3d329vaZmZn6+vr5+flCQkLp6ekZGRkLCwtSUlK0tLTLy8stLS0cHBx0dHSvr6+3t7enp6eNjY1mZmbs7OwmJiZ5eXk1NTVeXl5AQEBKSkrExMQQEBB/f3+fn59KDg5wAAAEnklEQVR4nO2d21LbQBBEJd8AG/DdBoyvGMP/f2EqDEiVCtSG3Z5ui+x5zkN3pUrIZzWzRZHJ/B8MZqu+OgOC3rgsy7U6RTqr8o2VOkcqLetRHtRB0rhZlz+iyOD40aO8VmdJYbSoejzdqsMkcLWsenQH6jAJPEyqHnfqLCm8VjXKjjpLCi9Vjcm9OksCt4eqx+lZHSaBzbDqsW3ya1b/ouqxH6nDJDAfVz2OTX7szurH7vpGHSaB6/qx21JnSWFX92jym/ttt37s9tRhEti0qx7LK3WYBPrbqsfihzx2hxt1mATu68fuocm/Pjr142qnzpLCXd2jyb9qB9P6rX2mDpPAqH7sjufqMCnU/x+NfmsvRlWPdpMfu0XR++gxbfJbe1EXuWzyW/tv3ots1TmSeS9yoc6RTC5ybuQi50Yucm7kIudGLoKk307n/czzFPyHj45Frkoil7lILpKL5CK5SC6Si/gWGbWIPDgWyWQymUYy77jCO4/rhP+apcA76L33LcI76Z37FuF9YTMKh0mBd0Z649pjQutRFMtwnHiYp4vtcJx42sQi03CceLrEIpeeRZjTCo+eRZjjCivPIsx5hWfPIsyBhb5nEea3TwPPItSPbU5+PcbMHsUiHCiWPbXIMRwolim1yDocKBZPwfg3rXCgWLhzJK/hQLG8Uos8+BXhTpI4annut8AbvyLkj2gn4URxnLg9im04UhwLcpFhOFIcR3KRQzhSHOxVIrtwpDheyEXc9C97wsdN/7JHfNz0L3u00k3/0odjnHowxa/hpH/5YyVO+veJXqQbDhUDU/wad+FQMfDX1DjpX/6eGif9y19U46R/+TOvTvqXv2vg1qeIYMpyHE71fbji19h7FOGKX8NF/3LFr+Gif7ni13DRv57DL1/hon8VG4Rc9K9ihZCL/lUsgXA5EJVsT3DQv2zxazjoX7b4NRz0L1v8Gg76V7ND2kH/ssWvcR0O9l00q50c9K9m9sVB/2p26jnoX9FWJHgPvvg14PpXtU8Irn/54teA61/V3nu4/t2JisD1r2pBOVz/qjaUw/WvatkhXP+qlszC9a9svR5Y/y5VPYo9tohC/Bpg/asQvwZ4HkYhfg2w/lWIXwOsf3Wr43vhcN9BtzserH9123/B+le4Nhc6D6MRv8ZFON6/oxG/BlT/asSvAdW/yssDofpXI34NqP7lTsD8yQxZRLn0CKp/lZepQPWvdB0+8EBUJX4NoP7VLpIH6t+htAhQ/2ovPAXq3520CFD/am+mBOpf7dWUQP2rveUGqH+1t4sB1yGJ71WB6V+d+DX2qCLM1UefAVuHpBO/Bkz/6sSvAdO/OvFrwPSv+s5QmP5VXxoK+4uovvYNpn/l96WB9K9S/Bog/asUvwboQFQpfg2Q/lWKX+MFU0R/hTZI/yrFrwHSv/pt96ADUf0t2iD9ewb3oEL0r1b8GpB5mHO4QRSif7Xi14DoX634NSD6d6duUYD0r1b8GpB5GK34NSD69xyuN4f82NWKXwOif9Ul3gDoX7X4NfbpRdTi1wDoX/7qo88A6F/+6qPPAOhftfg1et1k1L40k/maX+3qUbhJwgoUAAAAAElFTkSuQmCC';
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
    protected totalCount: number;
    protected items: Array<BeverageEntity>;
    protected selectedEntity: BeverageEntity;
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
    protected getEntityRowClass(item: BeverageEntity): string {
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
                let elementIndex = self.items.findIndex((item: BeverageEntity) => item.id === id);
                self.items.splice(elementIndex, 1);
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
            actionPromise = this.beverageApiService
                .patchActivity(entity.id, entity.isActive)
                .then(function(): void { });
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
    protected decrementOrder(entity: BeverageEntity): void {
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
    protected modalOpenCreate(): Promise<void> {
        let self = this;
        self.selectedEntity = new BeverageEntity();
        self.selectedEntity.siteId = this.siteId;
        self.selectedEntity.photoUrl = DefaultBeverageImg;
        self.selectedEntity.isActive = true;
        self.selectedEntity.order = this.getNewEntityOrder();
        self.modal.open();
        return Promise.resolve();
    }
    protected modalOpenEdit(id: number): Promise<void> {
        let self = this;
        self.selectedEntity = self.items.find((item: BeverageEntity) => item.id === id);
        self.modal.open();
        let operationPromise = self.beverageApiService
            .get(id)
            .then(function (response: BeverageEntity): Promise<void> {
                self.selectedEntity = response;
                return Promise.resolve();
            });
        return operationPromise;
    }
    protected modalApply() {
        let self = this;
        let operationPromise: Promise<BeverageEntity> = self.selectedEntity.id ?
            self.beverageApiService.update(self.selectedEntity) :
            self.beverageApiService.create(self.selectedEntity);
        return operationPromise
            .then(function (entity: BeverageEntity): Promise<void> {
                let elementIndex = self.items.findIndex((item: BeverageEntity) => item.id === entity.id);
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
        let self = this;
        self.contentApiService
            .postImage(self.avatarCropperData.image)
            .then(function (imageUrl: string) {
                // TODO: remove this stub result after implementing #27 - content controller
                self.selectedEntity.photoUrl = self.avatarCropperData.image; // imageUrl;
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
        this.selectedEntity.photoUrl = this.stubAvatarUrl;
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
