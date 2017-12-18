import { Component, ViewChild, Input, Output, EventEmitter } from '@angular/core';
import { OnInit, OnChanges, SimpleChanges, SimpleChange } from '@angular/core';
import { CropperSettings, ImageCropperComponent } from 'ng2-img-cropper';
import { Variable } from './../../../utils/index';
const enum ComponentMode {
    View = 1,
    EditViaCropper = 2,
    EditViaUrlInput = 3,
}
@Component({
    selector: 'image-select',
    styleUrls: ['./imageSelect.scss'],
    templateUrl: './imageSelect.html',
})
export class ImageSelectComponent implements OnInit, OnChanges {
    /// inputs
    @Input() isReadOnly: boolean = false;
    @Input() imageUrl: string;
    @Input() defaultImageUrl: string;
    @Input() imageWidth: number = 300;
    @Input() imageHeight: number = 300;
    @Input() isRounded: boolean = false;
    @Input() imageAlt: string = 'image';
    @Input() columnRules: string = 'col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6';
    @Input() forceAcceptImage: boolean = false;
    /// outputs
    @Output() imageUrlSelected: EventEmitter<string> = new EventEmitter<string>();
    @Output() resetForceAcceptImage: EventEmitter<void> = new EventEmitter<void>();
    /// view children
    @ViewChild('imageCropper', undefined)
    protected imageCropper: ImageCropperComponent;
    /// service fields
    protected componentMode: ComponentMode = ComponentMode.View;
    protected imageCropperSettings: CropperSettings;
    protected imageCropperData: any;
    /// fields
    protected newImageUrl: string;
    protected tempImageUrl: string;
    /// ctor
    constructor() { }
    /// methods
    ngOnInit(): void {
        if (Variable.isNullOrUndefined(this.imageUrl)) {
            this.imageUrl = this.defaultImageUrl;
        }
        this.newImageUrl = this.imageUrl;
        this.componentMode = 1;
        this.initializeImageCropper();
    }
    ngOnChanges(changes: SimpleChanges) {
        const imageChanged: SimpleChange = changes['imageUrl'];
        if (Variable.isNotNullOrUndefined(imageChanged) &&
            this.newImageUrl !== this.imageUrl) {
            this.newImageUrl = this.imageUrl;
        }
        const imageAccepted: SimpleChange = changes['forceAcceptImage'];
        if (Variable.isNotNullOrUndefined(imageAccepted)
            && imageAccepted.currentValue
            && imageAccepted.currentValue !== imageAccepted.previousValue) {
            if (this.componentMode === ComponentMode.EditViaCropper) {
                this.commitEditImageViaCropper();
            } else if (this.componentMode === ComponentMode.EditViaUrlInput) {
                this.commitEditImageViaUrlInput();
            }
            this.resetForceAcceptImage.emit();
        }
    }
    getColumnRules(): string{
        return this.columnRules;
    }
    getImageAlt(): string {
        return this.imageAlt;
    }
    getImageClass(): any {
        return {
            'rounded-circle': this.isRounded
        };
    }
    getImageUrl(): string {
        let imageUrl: string;
        if (this.isModeView()) {
            imageUrl = this.newImageUrl;
        } else if (this.isModeEditViaCropper()) {
            imageUrl = this.imageCropperData.image;
        } else {
            imageUrl = this.tempImageUrl;
        }
        if (Variable.isNullOrUndefined(imageUrl)) {
            imageUrl = this.imageUrl;
        }
        return imageUrl;
    }
    commitImageSelect(): void {
        this.imageUrlSelected.emit(this.newImageUrl);
    }
    startEditImageViaCropper(): void {
        this.imageCropperData = {};
        this.componentMode = ComponentMode.EditViaCropper;
    }
    commitEditImageViaCropper(): void {
        this.newImageUrl = this.imageCropperData.image;
        this.commitImageSelect();
        this.cancelEditImageViaCropper();
    }
    cancelEditImageViaCropper(): void {
        this.imageCropperData = null;
        this.componentMode = ComponentMode.View;
    }
    startEditImageViaUrlInput(): void {
        this.tempImageUrl = null;
        this.componentMode = ComponentMode.EditViaUrlInput;
    }
    commitEditImageViaUrlInput(): void {
        this.newImageUrl = this.tempImageUrl;
        this.commitImageSelect();
        this.cancelEditImageViaUrlInput();
    }
    cancelEditImageViaUrlInput(): void {
        this.tempImageUrl = null;
        this.componentMode = ComponentMode.View;
    }
    /// predicates
    isComponentReadOnly(): boolean {
        return this.isReadOnly;
    }
    isImageUrlDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.imageUrl);
    }
    isCropperImageDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.imageCropperData) &&
            Variable.isNotNullOrUndefined(this.imageCropperData.image);
    }
    isTempImageUrlDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.tempImageUrl);
    }
    isModeView(): boolean {
        return this.componentMode === ComponentMode.View;
    }
    isModeEditViaCropper(): boolean {
        return this.componentMode === ComponentMode.EditViaCropper;
    }
    isModeEditViaUrlInput(): boolean {
        return this.componentMode === ComponentMode.EditViaUrlInput;
    }
    /// helpers
    protected initializeImageCropper(): void {
        this.imageCropperSettings = new CropperSettings();
        this.imageCropperSettings.rounded = this.isRounded;
        this.imageCropperSettings.noFileInput = true;
        this.imageCropperSettings.minWithRelativeToResolution = true;
        this.imageCropperSettings.minWidth = this.imageWidth;
        this.imageCropperSettings.minHeight = this.imageHeight;
        this.imageCropperSettings.width = this.imageWidth;
        this.imageCropperSettings.height = this.imageHeight;
        this.imageCropperSettings.croppedWidth = this.imageWidth;
        this.imageCropperSettings.croppedHeight = this.imageHeight;
        this.imageCropperSettings.canvasWidth = this.imageWidth;
        this.imageCropperSettings.canvasHeight = this.imageHeight;
        this.imageCropperData = {};
    }
    protected browseFileChangeListenerInImageCropper($event) {
        const self = this;
        const image: any = new Image();
        const file: File = $event.target.files[0];
        const fileReader: FileReader = new FileReader();
        fileReader.onloadend = function (loadEvent: any): void {
            image.src = loadEvent.target.result;
            self.imageCropper.setImage(image);
        };
        fileReader.readAsDataURL(file);
    }
}