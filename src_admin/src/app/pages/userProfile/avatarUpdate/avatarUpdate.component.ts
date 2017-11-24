import { Component, ViewChild, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { Variable, ConsoleLogger, ILogger } from './../../../utils/index';
import { CropperSettings, ImageCropperComponent } from 'ng2-img-cropper';
import { IUserApiService, UserApiService } from './../../../services/index';
const enum ComponentMode {
    View = 1,
    EditViaCropper = 2,
    EditViaUrlInput = 3,
}
@Component({
    selector: 'user-avatar-update',
    styleUrls: ['./avatarUpdate.scss'],
    templateUrl: './avatarUpdate.html',
})
export class AvatarUpdateComponent implements OnInit {
    /// inputs
    @Input() userId: number;
    @Input() avatarUrl: string;
    /// outputs
    @Output() avatarUrlPatched: EventEmitter<string> = new EventEmitter<string>();
    /// view children
    @ViewChild('avatarCropper', undefined)
    protected avatarCropper: ImageCropperComponent;
    /// service fields
    protected componentMode: ComponentMode = ComponentMode.View;
    protected patchAvatarPromise: Promise<void>;
    protected avatarCropperSettings: CropperSettings;
    protected avatarCropperData: any;
    protected avatarWidth: number;
    protected avatarHeight: number;
    /// fields
    protected newAvatarUrl: string;
    protected tempAvatarUrl: string;
    /// injected dependencies
    protected logger: ILogger;
    protected userApiService: IUserApiService;
    /// ctor
    constructor(logger: ConsoleLogger, userApiService: UserApiService) {
        this.logger = logger;
        this.userApiService = userApiService;
        this.logger.logDebug('AvatarUpdateComponent: Component has been constructed.');
    }
    /// methods
    ngOnInit(): void {
        this.newAvatarUrl = this.avatarUrl;
        this.avatarWidth = 300;
        this.avatarHeight = 300;
        this.initializeAvatarCropper();
        this.componentMode = 1;
    }
    getAvatarUrl(): string {
        let avatarUrl: string;
        if (this.isModeView()) {
            avatarUrl = this.newAvatarUrl;
        } else if (this.isModeEditViaCropper()) {
            avatarUrl = this.avatarCropperData.image;
        } else {
            avatarUrl = this.tempAvatarUrl;
        }
        if (Variable.isNullOrUndefined(avatarUrl)) {
            avatarUrl = this.avatarUrl;
        }
        return avatarUrl;
    }
    patchAvatar(): Promise<void> {
        const self = this;
        self.patchAvatarPromise = self.userApiService
            .patchAvatar(self.userId, self.newAvatarUrl)
            .then(function(): void {
                self.avatarUrlPatched.emit(self.newAvatarUrl);
            })
            .then(
                () => {
                    self.patchAvatarPromise = null;
                },
                () => {
                    self.patchAvatarPromise = null;
                },
            );
        return self.patchAvatarPromise;
    }
    resetAvatar(): void {
        this.newAvatarUrl = this.avatarUrl;
    }
    startEditAvatarViaCropper(): void {
        this.avatarCropperData = {};
        this.componentMode = ComponentMode.EditViaCropper;
    }
    commitEditAvatarViaCropper(): void {
        this.newAvatarUrl = this.avatarCropperData.image;
        this.cancelEditAvatarViaCropper();
    }
    cancelEditAvatarViaCropper(): void {
        this.avatarCropperData = null;
        this.componentMode = ComponentMode.View;
    }
    startEditAvatarViaUrlInput(): void {
        this.tempAvatarUrl = null;
        this.componentMode = ComponentMode.EditViaUrlInput;
    }
    commitEditAvatarViaUrlInput(): void {
        this.newAvatarUrl = this.tempAvatarUrl;
        this.cancelEditAvatarViaUrlInput();
    }
    cancelEditAvatarViaUrlInput(): void {
        this.tempAvatarUrl = null;
        this.componentMode = ComponentMode.View;
    }
    /// predicates
    isUserIdDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.userId);
    }
    isAvatarUrlDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.avatarUrl);
    }
    isNewAvatarUrlDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.newAvatarUrl);
    }
    isCropperImageDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.avatarCropperData) &&
            Variable.isNotNullOrUndefined(this.avatarCropperData.image);
    }
    isTempAvatarUrlDefined(): boolean {
        return Variable.isNotNullOrUndefined(this.tempAvatarUrl);
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
    isPatchAvatarAllowed(): boolean {
        return this.isModeView() && Variable.isNotNullOrUndefined(this.userId);
    }
    isPatchAvatarDisabled(): boolean {
        return this.isPatchAvatarProcessing() || !this.isNewAvatarUrlDefined();
    }
    isPatchAvatarProcessing(): boolean {
        return Variable.isNotNullOrUndefined(this.patchAvatarPromise);
    }
    isResetAvatarAllowed(): boolean {
        return this.isModeView();
    }
    isResetAvatarDisabled(): boolean {
        return this.isPatchAvatarProcessing();
    }
    /// helpers
    protected initializeAvatarCropper(): void {
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
        this.avatarCropperSettings.canvasWidth = this.avatarWidth;
        this.avatarCropperSettings.canvasHeight = this.avatarHeight;
        this.avatarCropperData = {};
        this.logger.logTrase('AvatarUpdateComponent: Avatar cropper has been initialized.');
    }
    protected browseFileChangeListenerInAvatarCropper($event) {
        const self = this;
        const image: any = new Image();
        const file: File = $event.target.files[0];
        const fileReader: FileReader = new FileReader();
        fileReader.onloadend = function (loadEvent: any): void {
            image.src = loadEvent.target.result;
            self.avatarCropper.setImage(image);
        };
        fileReader.readAsDataURL(file);
    }
}